<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with('items')->latest();
        if ($request->filled('status'))  { $query->where('status', $request->status); }
        if ($request->filled('channel')) { $query->where('channel', $request->channel); }
        if ($request->filled('search'))  {
            $s = $request->search;
            $query->where(fn($q) => $q->where('order_number','like',"%$s%")->orWhere('guest_name','like',"%$s%")->orWhere('guest_email','like',"%$s%"));
        }
        $orders  = $query->paginate(20)->withQueryString();
        $summary = [
            'pending'   => Order::where('status','pending')->count(),
            'confirmed' => Order::where('status','confirmed')->count(),
            'processing'=> Order::where('status','processing')->count(),
            'ready'     => Order::where('status','ready')->count(),
            'today'     => Order::whereDate('created_at', today())->where('channel','online')->count(),
        ];
        return view('orders.index', compact('orders','summary'));
    }

    public function show(Order $order)
    {
        $order->load('items.inventoryItem','customer');
        return view('orders.show', compact('order'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $request->validate(['status' => 'required|in:pending,confirmed,processing,ready,delivered,cancelled']);
        $order->update(['status' => $request->status]);

        // If cancelled, restore stock
        if ($request->status === 'cancelled') {
            foreach ($order->items as $item) {
                $item->inventoryItem?->increment('quantity', $item->quantity);
            }
        }

        return back()->with('success', 'Order status updated to ' . ucfirst($request->status));
    }
}