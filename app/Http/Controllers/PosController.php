<?php

namespace App\Http\Controllers;

use App\Models\InventoryItem;
use App\Models\Customer;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Category;
use App\Models\StockBatch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PosController extends Controller
{
    /**
     * POS terminal — load customers + filter options.
     */
    public function index()
    {
        $customers  = Customer::orderBy('name')
                              ->get(['id','name','phone','loyalty_points','loyalty_tier']);

        $categories = Category::where('is_active', true)->orderBy('display_name')->get();
        $batches    = StockBatch::where('is_active', true)->orderBy('batch_number')->get();

        return view('pos.terminal', compact('customers', 'categories', 'batches'));
    }

    /**
     * Load default product grid (no search term — called on page load).
     * Returns up to $limit products, ordered by newest, in stock first.
     */
    public function loadProducts(Request $request)
    {
        $limit = min((int) $request->input('limit', 60), 120);

        $items = InventoryItem::select([
                        'id','name','category_code','batch_code','image_path',
                        'selling_price','buying_price','quantity',
                        'low_stock_threshold','tax_rate',
                    ])
                    ->orderByRaw('quantity > 0 DESC')   // in-stock first
                    ->orderByDesc('id')                 // newest first
                    ->limit($limit)
                    ->get();

        return response()->json($items);
    }

    /**
     * Search products by name / barcode / SKU.
     */
    public function searchProducts(Request $request)
    {
        $term  = trim($request->input('q', ''));
        $limit = 40;

        $items = InventoryItem::select([
                        'id','name','category_code','batch_code','image_path',
                        'selling_price','buying_price','quantity',
                        'low_stock_threshold','tax_rate',
                    ])
                    ->where(function ($q) use ($term) {
                        $q->where('name',    'like', "%{$term}%")
                          ->orWhere('barcode', $term)
                          ->orWhere('sku',     $term);
                    })
                    ->orderByRaw('quantity > 0 DESC')
                    ->limit($limit)
                    ->get();

        return response()->json($items);
    }

    /**
     * Process and record a completed sale.
     */
    public function store(Request $request)
    {
        $request->validate([
            'items'            => 'required|array|min:1',
            'items.*.id'       => 'required|exists:inventory_items,id',
            'items.*.qty'      => 'required|integer|min:1',
            'items.*.price'    => 'required|numeric|min:0',
            'payment_method'   => 'required|in:cash,card,mobile_money,split',
            'amount_paid'      => 'required|numeric|min:0',
            'discount_amount'  => 'nullable|numeric|min:0',
            'customer_id'      => 'nullable|exists:customers,id',
        ]);

        DB::beginTransaction();
        try {
            $subtotal  = 0;
            $taxTotal  = 0;
            $lineItems = [];

            foreach ($request->items as $line) {
                $item = InventoryItem::lockForUpdate()->findOrFail($line['id']);

                if ($item->quantity < $line['qty']) {
                    DB::rollBack();
                    return response()->json([
                        'error' => "Insufficient stock for \"{$item->name}\". Available: {$item->quantity}"
                    ], 422);
                }

                $lineDiscount = (float) ($line['discount'] ?? 0);
                $linePrice    = ($line['price'] * $line['qty']) - $lineDiscount;
                $lineTax      = $linePrice * ($item->tax_rate / 100);
                $lineTotal    = $linePrice + $lineTax;

                $subtotal  += $linePrice;
                $taxTotal  += $lineTax;

                $lineItems[] = [
                    'item'       => $item,
                    'qty'        => $line['qty'],
                    'unit_price' => $line['price'],
                    'tax_rate'   => $item->tax_rate,
                    'discount'   => $lineDiscount,
                    'line_total' => $lineTotal,
                ];
            }

            $discountAmount = (float) ($request->discount_amount ?? 0);
            $total          = max(0, $subtotal + $taxTotal - $discountAmount);
            $change         = max(0, $request->amount_paid - $total);

            $sale = Sale::create([
                'receipt_number'    => Sale::generateReceiptNumber(),
                'user_id'           => auth()->id(),
                'customer_id'       => $request->customer_id,
                'subtotal'          => $subtotal,
                'tax_total'         => $taxTotal,
                'discount_amount'   => $discountAmount,
                'total'             => $total,
                'amount_paid'       => $request->amount_paid,
                'change_given'      => $change,
                'payment_method'    => $request->payment_method,
                'payment_reference' => $request->payment_reference,
                'status'            => 'completed',
            ]);

            foreach ($lineItems as $line) {
                SaleItem::create([
                    'sale_id'           => $sale->id,
                    'inventory_item_id' => $line['item']->id,
                    'item_name'         => $line['item']->name,
                    'unit_price'        => $line['unit_price'],
                    'quantity'          => $line['qty'],
                    'tax_rate'          => $line['tax_rate'],
                    'discount'          => $line['discount'],
                    'line_total'        => $line['line_total'],
                ]);

                $line['item']->decrement('quantity', $line['qty']);
            }

            // Update customer loyalty
            if ($request->customer_id) {
                $customer = Customer::find($request->customer_id);
                if ($customer) {
                    $customer->increment('loyalty_points', (int) ($total / 1000));
                    $customer->increment('total_spent', $total);
                    $customer->updateTier();
                }
            }

            DB::commit();

            return response()->json([
                'success'        => true,
                'receipt_number' => $sale->receipt_number,
                'sale_id'        => $sale->id,
                'change'         => $change,
                'total'          => $total,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Transaction failed: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Printable receipt page.
     */
    public function receipt(Sale $sale)
    {
        $sale->load('items.inventoryItem', 'customer', 'user');
        return view('pos.receipt', compact('sale'));
    }

    /**
     * Void a sale and restore stock.
     */
    public function void(Sale $sale)
    {
        if ($sale->status !== 'completed') {
            return back()->with('error', 'Only completed sales can be voided.');
        }

        DB::transaction(function () use ($sale) {
            foreach ($sale->items as $item) {
                $item->inventoryItem?->increment('quantity', $item->quantity);
            }
            $sale->update(['status' => 'voided']);
        });

        return back()->with('success', 'Sale voided and stock restored.');
    }
}