@extends('layouts.shop')
@section('title', 'Order Confirmed — SOZO Store')

@section('content')
<div class="container" style="padding:4rem 2rem;max-width:640px;margin:0 auto;text-align:center">
    <div style="width:72px;height:72px;border-radius:50%;background:rgba(45,122,79,.1);border:2px solid rgba(45,122,79,.25);display:flex;align-items:center;justify-content:center;margin:0 auto 1.5rem">
        <svg width="32" height="32" fill="none" stroke="var(--success)" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
    </div>
    <h1 style="font-family:var(--font-serif);font-size:2.2rem;font-weight:700;margin-bottom:.8rem">Order Placed!</h1>
    <p style="color:var(--mid);font-size:1rem;margin-bottom:.4rem">
        Thank you <strong>{{ $order->buyer_name }}</strong>. Your order has been received.
    </p>
    <div style="font-family:'Playfair Display',serif;font-size:1.3rem;font-weight:700;color:var(--gold);margin:1rem 0">
        {{ $order->order_number }}
    </div>
    <p style="color:var(--light);font-size:.88rem;margin-bottom:2rem">
        We'll {{ $order->delivery_method === 'pickup' ? 'have your order ready for pickup' : 'contact you to arrange delivery' }}. You can track your order status below.
    </p>

    <div style="background:var(--white);border:1px solid var(--border);border-radius:14px;padding:1.5rem;text-align:left;margin-bottom:2rem">
        <h3 style="font-family:'Playfair Display',serif;font-size:1.1rem;font-weight:700;margin-bottom:1rem;padding-bottom:.8rem;border-bottom:1px solid var(--border)">Order Summary</h3>
        @foreach($order->items as $item)
        <div style="display:flex;justify-content:space-between;padding:.45rem 0;border-bottom:1px solid var(--warm);font-size:.88rem">
            <span>{{ $item->item_name }} <span style="color:var(--light)">×{{ $item->quantity }}</span></span>
            <span style="font-weight:600">UGX {{ number_format($item->line_total, 0) }}</span>
        </div>
        @endforeach
        <div style="display:flex;justify-content:space-between;padding-top:.8rem;font-family:'Playfair Display',serif;font-size:1.1rem;font-weight:700">
            <span>Total</span>
            <span style="color:var(--gold)">UGX {{ number_format($order->total, 0) }}</span>
        </div>
        <div style="margin-top:.8rem;padding-top:.8rem;border-top:1px solid var(--border);display:flex;gap:1.5rem;font-size:.82rem;color:var(--mid)">
            <span>📦 {{ ucfirst($order->delivery_method) }}</span>
            <span>💳 {{ str_replace('_',' ', $order->payment_method) }}</span>
            <span>⏳ {{ ucfirst($order->status) }}</span>
        </div>
    </div>

    <div style="display:flex;gap:.8rem;justify-content:center;flex-wrap:wrap">
        <a href="{{ route('shop.index') }}" class="btn btn-dark">Continue Shopping</a>
        <a href="{{ route('shop.catalog') }}" class="btn btn-outline">Browse Products</a>
    </div>
</div>
@endsection