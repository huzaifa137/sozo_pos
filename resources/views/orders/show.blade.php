@extends('layouts.app')
@section('page-title', $order->order_number)

@section('topbar-actions')
    <a href="{{ route('orders.index') }}" class="topbar-btn tb-outline">← Orders</a>
@endsection

@section('content')
<div style="display:grid;grid-template-columns:1fr 320px;gap:1.2rem;align-items:start">

    {{-- Items --}}
    <div>
        <div class="card" style="padding:0;overflow:hidden;margin-bottom:1.2rem">
            <div style="padding:1rem 1.2rem;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between">
                <span style="font-family:var(--font-head);font-weight:700">Order Items</span>
                <span class="badge badge-{{ $order->status_color }}">{{ ucfirst($order->status) }}</span>
            </div>
            <table class="data-table">
                <thead><tr><th>Product</th><th>Qty</th><th>Unit Price</th><th style="text-align:right">Total</th></tr></thead>
                <tbody>
                    @foreach($order->items as $item)
                    <tr>
                        <td>
                            <div style="font-weight:600;font-size:.88rem">{{ $item->item_name }}</div>
                            @if($item->inventoryItem)
                            <div style="font-size:.73rem;color:var(--muted)">SKU: {{ $item->inventoryItem->sku ?? '—' }}</div>
                            @endif
                        </td>
                        <td style="font-weight:700">{{ $item->quantity }}</td>
                        <td>{{ number_format($item->unit_price, 0) }}</td>
                        <td style="text-align:right;font-family:var(--font-head);font-weight:700">{{ number_format($item->line_total, 0) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div style="padding:1rem 1.2rem;border-top:1px solid var(--border);display:flex;flex-direction:column;gap:.4rem;align-items:flex-end">
                <div style="display:flex;gap:2rem;font-size:.88rem"><span style="color:var(--muted)">Subtotal</span><span>{{ number_format($order->subtotal, 0) }}</span></div>
                @if($order->tax_total > 0)<div style="display:flex;gap:2rem;font-size:.88rem"><span style="color:var(--muted)">Tax</span><span>{{ number_format($order->tax_total, 0) }}</span></div>@endif
                @if($order->delivery_fee > 0)<div style="display:flex;gap:2rem;font-size:.88rem"><span style="color:var(--muted)">Delivery</span><span>{{ number_format($order->delivery_fee, 0) }}</span></div>@endif
                <div style="display:flex;gap:2rem;font-family:var(--font-head);font-weight:800;font-size:1.1rem;border-top:1px solid var(--border);padding-top:.6rem;margin-top:.2rem"><span>Total</span><span style="color:var(--accent)">UGX {{ number_format($order->total, 0) }}</span></div>
            </div>
        </div>

        {{-- Update status --}}
        <div class="card">
            <div style="font-family:var(--font-head);font-weight:700;margin-bottom:1rem">Update Order Status</div>
            <form action="{{ route('orders.status', $order) }}" method="POST" style="display:flex;gap:.8rem;align-items:center">
                @csrf @method('PATCH')
                <select name="status" style="flex:1;padding:.65rem .9rem">
                    @foreach(['pending','confirmed','processing','ready','delivered','cancelled'] as $s)
                    <option value="{{ $s }}" {{ $order->status === $s ? 'selected':'' }}>{{ ucfirst($s) }}</option>
                    @endforeach
                </select>
                <button type="submit" class="btn btn-primary">Update</button>
            </form>
        </div>
    </div>

    {{-- Sidebar info --}}
    <div>
        <div class="card" style="margin-bottom:1rem">
            <div style="font-family:var(--font-head);font-weight:700;margin-bottom:1rem;font-size:.9rem">Customer</div>
            <div style="font-size:.88rem;display:flex;flex-direction:column;gap:.5rem">
                <div><strong>{{ $order->buyer_name }}</strong></div>
                @if($order->buyer_email)<div style="color:var(--muted)">{{ $order->buyer_email }}</div>@endif
                @if($order->guest_phone)<div style="color:var(--muted)">{{ $order->guest_phone }}</div>@endif
                @if($order->customer)<a href="{{ route('customers.show', $order->customer) }}" style="color:var(--accent2);font-size:.8rem">View customer profile →</a>@endif
            </div>
        </div>

        <div class="card" style="margin-bottom:1rem">
            <div style="font-family:var(--font-head);font-weight:700;margin-bottom:1rem;font-size:.9rem">Delivery</div>
            <div style="font-size:.85rem;display:flex;flex-direction:column;gap:.4rem">
                <div><span class="badge {{ $order->delivery_method === 'pickup' ? 'badge-yellow' : 'badge-blue' }}">{{ ucfirst($order->delivery_method) }}</span></div>
                @if($order->shipping_address)
                <div style="color:var(--muted)">{{ $order->shipping_address }}</div>
                <div style="color:var(--muted)">{{ $order->shipping_city }}</div>
                @endif
            </div>
        </div>

        <div class="card">
            <div style="font-family:var(--font-head);font-weight:700;margin-bottom:1rem;font-size:.9rem">Payment</div>
            <div style="font-size:.85rem;display:flex;flex-direction:column;gap:.4rem">
                <div>{{ str_replace('_',' ', $order->payment_method) }}</div>
                <div><span class="badge {{ $order->payment_status === 'paid' ? 'badge-green' : 'badge-warn' }}">{{ $order->payment_status }}</span></div>
                @if($order->payment_reference)<div style="color:var(--muted);font-size:.78rem">Ref: {{ $order->payment_reference }}</div>@endif
            </div>
        </div>
    </div>
</div>
@endsection