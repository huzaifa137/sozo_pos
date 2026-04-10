<?php

namespace App\Http\Controllers;

use App\Models\InventoryItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ShopController extends Controller
{
    // ── STOREFRONT ──────────────────────────────────────────

    /** Landing / shop home */
    public function index(Request $request)
    {
        $featured = InventoryItem::where('published', true)
            ->where('featured', true)
            ->where('quantity', '>', 0)
            ->limit(8)->get();

        $categories = InventoryItem::where('published', true)
            ->where('quantity', '>', 0)
            ->select('category')
            ->distinct()
            ->pluck('category');

        $newArrivals = InventoryItem::where('published', true)
            ->where('quantity', '>', 0)
            ->latest()->limit(8)->get();

        $bestsellers = \App\Models\OrderItem::join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('inventory_items', 'order_items.inventory_item_id', '=', 'inventory_items.id')
            ->where('orders.status', '!=', 'cancelled')
            ->where('inventory_items.published', true)
            ->where('inventory_items.quantity', '>', 0)
            ->selectRaw('
                inventory_items.id,
                inventory_items.name,
                inventory_items.sku,
                inventory_items.selling_price,
                inventory_items.image_path,
                inventory_items.quantity,
                SUM(order_items.quantity) as sold_qty
            ')
            ->groupBy(
                'inventory_items.id',
                'inventory_items.name',
                'inventory_items.sku',
                'inventory_items.selling_price',
                'inventory_items.image_path',
                'inventory_items.quantity'
            )
            ->orderByDesc('sold_qty')
            ->limit(8)
            ->get();

        return view('shop.index', compact('featured', 'categories', 'newArrivals', 'bestsellers'));
    }

    /** Product catalog with filters */
    public function catalog(Request $request)
    {
        $query = InventoryItem::where('published', true)->where('quantity', '>', 0);

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }
        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(fn($x) => $x->where('name', 'like', "%$q%")->orWhere('description', 'like', "%$q%"));
        }
        if ($request->filled('min_price')) {
            $query->where('selling_price', '>=', $request->min_price);
        }
        if ($request->filled('max_price')) {
            $query->where('selling_price', '<=', $request->max_price);
        }
        if ($request->sort === 'price_asc') {
            $query->orderBy('selling_price');
        } elseif ($request->sort === 'price_desc') {
            $query->orderByDesc('selling_price');
        } elseif ($request->sort === 'newest') {
            $query->latest();
        } else {
            $query->latest();
        }

        $products = $query->paginate(16)->withQueryString();
        $categories = InventoryItem::where('published', true)->where('quantity', '>', 0)->select('category')->distinct()->pluck('category');

        return view('shop.catalog', compact('products', 'categories'));
    }

    /** Single product page */
    public function product(string $slug)
    {
        $product = InventoryItem::where('slug', $slug)->where('published', true)->firstOrFail();
        $product->increment('views');

        $related = InventoryItem::where('category', $product->category)
            ->where('id', '!=', $product->id)
            ->where('published', true)
            ->where('quantity', '>', 0)
            ->limit(4)->get();

        return view('shop.product', compact('product', 'related'));
    }

    // ── CART (session-based) ─────────────────────────────────

    public function cartView()
    {
        $cart = session('cart', []);
        $items = $this->hydrateCart($cart);
        return view('shop.cart', compact('items', 'cart'));
    }

    public function cartAdd(Request $request)
    {
        $request->validate(['product_id' => 'required|exists:inventory_items,id', 'qty' => 'required|integer|min:1']);
        $cart = session('cart', []);
        $id = $request->product_id;
        $cart[$id] = ($cart[$id] ?? 0) + $request->qty;
        session(['cart' => $cart]);
        return back()->with('cart_success', 'Item added to cart!');
    }

    public function cartUpdate(Request $request)
    {
        $request->validate(['product_id' => 'required', 'qty' => 'required|integer|min:0']);
        $cart = session('cart', []);
        if ($request->qty == 0) {
            unset($cart[$request->product_id]);
        } else {
            $cart[$request->product_id] = $request->qty;
        }
        session(['cart' => $cart]);
        return back();
    }

    public function cartRemove(Request $request)
    {
        $cart = session('cart', []);
        unset($cart[$request->product_id]);
        session(['cart' => $cart]);
        return back()->with('success', 'Item removed.');
    }

    public function cartCount()
    {
        $cart = session('cart', []);
        return response()->json(['count' => array_sum($cart)]);
    }

    // ── CHECKOUT ────────────────────────────────────────────

    public function checkoutView()
    {
        $cart = session('cart', []);
        if (empty($cart))
            return redirect()->route('shop.cart');
        $items = $this->hydrateCart($cart);
        return view('shop.checkout', compact('items', 'cart'));
    }

    public function checkoutProcess(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'phone' => 'required|string|max:20',
            'delivery_method' => 'required|in:pickup,delivery',
            'payment_method' => 'required|in:cash_on_delivery,mobile_money,card_on_delivery',
            'shipping_address' => 'required_if:delivery_method,delivery|nullable|string',
            'shipping_city' => 'required_if:delivery_method,delivery|nullable|string',
        ]);

        $cart = session('cart', []);
        if (empty($cart))
            return redirect()->route('shop.cart');

        DB::beginTransaction();
        try {
            $items = $this->hydrateCart($cart);
            $subtotal = $items->sum(fn($i) => $i['line_total']);
            $taxTotal = $items->sum(fn($i) => $i['line_tax']);
            $delivery = $request->delivery_method === 'delivery' ? 5000 : 0;
            $total = $subtotal + $taxTotal + $delivery;

            // Find or create customer
            $customer = Customer::where('email', $request->email)
                ->orWhere('phone', $request->phone)
                ->first();

            $order = Order::create([
                'order_number' => Order::generateOrderNumber(),
                'customer_id' => $customer?->id,
                'guest_name' => $request->name,
                'guest_email' => $request->email,
                'guest_phone' => $request->phone,
                'shipping_address' => $request->shipping_address,
                'shipping_city' => $request->shipping_city,
                'delivery_method' => $request->delivery_method,
                'delivery_fee' => $delivery,
                'subtotal' => $subtotal,
                'tax_total' => $taxTotal,
                'total' => $total,
                'payment_method' => $request->payment_method,
                'payment_reference' => $request->payment_reference,
                'notes' => $request->notes,
                'status' => 'pending',
                'channel' => 'online',
            ]);

            foreach ($items as $line) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'inventory_item_id' => $line['id'],
                    'item_name' => $line['name'],
                    'unit_price' => $line['price'],
                    'quantity' => $line['qty'],
                    'line_total' => $line['line_total'] + $line['line_tax'],
                ]);
                // Reserve stock
                InventoryItem::where('id', $line['id'])->decrement('quantity', $line['qty']);
            }

            // Update customer stats
            if ($customer) {
                $customer->increment('total_spent', $total);
                $customer->increment('loyalty_points', (int) ($total / 1000));
                $customer->updateTier();
            }

            DB::commit();
            session()->forget('cart');

            return redirect()->route('shop.order.confirmation', $order->order_number)
                ->with('order_success', $order->order_number);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Order failed: ' . $e->getMessage())->withInput();
        }
    }

    public function orderConfirmation(string $orderNumber)
    {
        $order = Order::where('order_number', $orderNumber)->with('items')->firstOrFail();
        return view('shop.order-confirmation', compact('order'));
    }

    // ── ACCOUNT (customer portal) ───────────────────────────

    public function accountOrders()
    {
        $customer = auth()->user()?->customer;
        if (!$customer) {
            // Guest: show order lookup
            return view('account.orders-guest');
        }
        $orders = $customer->orders()->with('items')->latest()->paginate(10);
        return view('account.orders', compact('orders'));
    }

    public function accountOrderDetail(Order $order)
    {
        $order->load('items.inventoryItem');
        return view('account.order-detail', compact('order'));
    }

    // ── HELPERS ─────────────────────────────────────────────

    private function hydrateCart(array $cart): \Illuminate\Support\Collection
    {
        if (empty($cart))
            return collect();
        $products = InventoryItem::whereIn('id', array_keys($cart))->get()->keyBy('id');
        return collect($cart)->map(function ($qty, $id) use ($products) {
            $p = $products[$id] ?? null;
            if (!$p)
                return null;
            $lineTotal = $p->selling_price * $qty;
            $lineTax = $lineTotal * ($p->tax_rate / 100);
            return [
                'id' => $p->id,
                'name' => $p->name,
                'image_path' => $p->image_path,
                'price' => $p->selling_price,
                'qty' => $qty,
                'stock' => $p->quantity,
                'line_total' => $lineTotal,
                'line_tax' => $lineTax,
                'slug' => $p->slug,
            ];
        })->filter();
    }
}