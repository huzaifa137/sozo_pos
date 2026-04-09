<?php

namespace App\Http\Controllers;

use App\Models\InventoryItem;
use App\Models\Customer;
use App\Models\Sale;
use App\Models\SaleItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PosController extends Controller
{
    /** POS terminal view */
    public function index()
    {
        $customers = Customer::orderBy('name')->get(['id','name','phone','loyalty_points','loyalty_tier']);
        return view('pos.terminal', compact('customers'));
    }

    /** Search products for POS (barcode, SKU, name) */
    public function searchProducts(Request $request)
    {
        $term = $request->q;

        $items = InventoryItem::where('quantity', '>', 0)
            ->where(function ($q) use ($term) {
                $q->where('name', 'like', "%{$term}%")
                  ->orWhere('barcode', $term)
                  ->orWhere('sku', $term);
            })
            ->limit(10)
            ->get(['id','name','sku','barcode','selling_price','quantity','tax_rate','image_path']);

        return response()->json($items);
    }

    /** Process a sale */
    public function store(Request $request)
    {
        $request->validate([
            'items'           => 'required|array|min:1',
            'items.*.id'      => 'required|exists:inventory_items,id',
            'items.*.qty'     => 'required|integer|min:1',
            'items.*.price'   => 'required|numeric|min:0',
            'payment_method'  => 'required|in:cash,card,mobile_money,split',
            'amount_paid'     => 'required|numeric|min:0',
            'discount_amount' => 'nullable|numeric|min:0',
            'customer_id'     => 'nullable|exists:customers,id',
        ]);

        DB::beginTransaction();
        try {
            $subtotal   = 0;
            $taxTotal   = 0;
            $lineItems  = [];

            foreach ($request->items as $line) {
                $item = InventoryItem::findOrFail($line['id']);

                if ($item->quantity < $line['qty']) {
                    DB::rollBack();
                    return response()->json([
                        'error' => "Insufficient stock for {$item->name}. Available: {$item->quantity}"
                    ], 422);
                }

                $lineDiscount = $line['discount'] ?? 0;
                $linePrice    = ($line['price'] * $line['qty']) - $lineDiscount;
                $lineTax      = $linePrice * ($item->tax_rate / 100);
                $lineTotal    = $linePrice + $lineTax;

                $subtotal   += $linePrice;
                $taxTotal   += $lineTax;

                $lineItems[] = [
                    'item'       => $item,
                    'qty'        => $line['qty'],
                    'unit_price' => $line['price'],
                    'tax_rate'   => $item->tax_rate,
                    'discount'   => $lineDiscount,
                    'line_total' => $lineTotal,
                ];
            }

            $discountAmount = $request->discount_amount ?? 0;
            $total          = $subtotal + $taxTotal - $discountAmount;
            $change         = max(0, $request->amount_paid - $total);

            // Create sale record
            $sale = Sale::create([
                'receipt_number'   => Sale::generateReceiptNumber(),
                'user_id'          => auth()->id(),
                'customer_id'      => $request->customer_id,
                'subtotal'         => $subtotal,
                'tax_total'        => $taxTotal,
                'discount_amount'  => $discountAmount,
                'total'            => $total,
                'amount_paid'      => $request->amount_paid,
                'change_given'     => $change,
                'payment_method'   => $request->payment_method,
                'payment_reference'=> $request->payment_reference,
                'notes'            => $request->notes,
                'status'           => 'completed',
            ]);

            // Create sale items & deduct stock
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

            // Update customer loyalty points & total spent
            if ($request->customer_id) {
                $customer = Customer::find($request->customer_id);
                $customer->increment('loyalty_points', (int) ($total / 1000)); // 1 pt per 1000
                $customer->increment('total_spent', $total);
                $customer->updateTier();
            }

            DB::commit();

            $sale->load('items');

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

    /** Receipt view */
    public function receipt(Sale $sale)
    {
        $sale->load('items.inventoryItem', 'customer', 'user');
        return view('pos.receipt', compact('sale'));
    }

    /** Void a sale */
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
