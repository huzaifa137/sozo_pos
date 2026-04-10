@extends('layouts.shop')
@section('title', 'Your Cart — SOZO Store')

@push('styles')
<style>
.cart-page { padding: 2.5rem 0 4rem; }
.cart-page h1 { font-family: var(--font-serif); font-size: 2rem; font-weight: 700; margin-bottom: 2rem; }
.cart-layout { display: grid; grid-template-columns: 1fr 380px; gap: 2rem; align-items: start; }
/* items */
.cart-items-card { background: var(--white); border: 1px solid var(--border); border-radius: 14px; overflow: hidden; }
.cart-item { display: flex; gap: 1.2rem; padding: 1.2rem 1.4rem; border-bottom: 1px solid var(--border); align-items: center; }
.cart-item:last-child { border-bottom: none; }
.ci-img { width: 80px; height: 80px; border-radius: 10px; overflow: hidden; background: var(--warm); flex-shrink: 0; }
.ci-img img { width: 100%; height: 100%; object-fit: cover; }
.ci-info { flex: 1; }
.ci-cat { font-size: .7rem; color: var(--light); text-transform: uppercase; letter-spacing: .06em; }
.ci-name { font-family: var(--font-serif); font-size: 1rem; font-weight: 500; margin-bottom: .3rem; }
.ci-price { font-size: .85rem; color: var(--mid); }
.ci-right { display: flex; flex-direction: column; align-items: flex-end; gap: .6rem; }
.ci-total { font-family: var(--font-serif); font-size: 1.1rem; font-weight: 700; color: var(--gold); }
.ci-qty-ctrl { display: flex; align-items: center; border: 1px solid var(--border); border-radius: 7px; overflow: hidden; }
.ci-qty-ctrl button { width: 30px; height: 32px; border: none; background: var(--warm); cursor: pointer; font-size: .95rem; font-weight: 700; transition: background .13s; }
.ci-qty-ctrl button:hover { background: var(--border); }
.ci-qty-ctrl span { width: 36px; text-align: center; font-weight: 600; font-size: .88rem; }
.ci-remove { background: none; border: none; color: var(--light); cursor: pointer; font-size: .78rem; display: flex; align-items: center; gap: .2rem; transition: color .13s; }
.ci-remove:hover { color: var(--danger); }

/* summary */
.cart-summary { background: var(--white); border: 1px solid var(--border); border-radius: 14px; padding: 1.5rem; position: sticky; top: 90px; }
.cart-summary h3 { font-family: var(--font-serif); font-size: 1.2rem; font-weight: 700; margin-bottom: 1.2rem; padding-bottom: .8rem; border-bottom: 1px solid var(--border); }
.sum-row { display: flex; justify-content: space-between; font-size: .9rem; margin-bottom: .6rem; }
.sum-row .lbl { color: var(--mid); }
.sum-row.grand { font-weight: 700; font-size: 1.05rem; border-top: 1px solid var(--border); padding-top: .8rem; margin-top: .4rem; }
.sum-row.grand .val { font-family: var(--font-serif); color: var(--gold); font-size: 1.2rem; }
.checkout-btn { width: 100%; margin-top: 1.2rem; padding: .9rem; border-radius: 10px; background: var(--charcoal); color: var(--white); border: none; font-family: var(--font-sans); font-size: .95rem; font-weight: 700; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: .5rem; transition: background .18s; text-decoration: none; }
.checkout-btn:hover { background: var(--gold); color: var(--white); }

.empty-cart { text-align: center; padding: 5rem 2rem; }
.empty-cart h3 { font-family: var(--font-serif); font-size: 1.5rem; margin: 1rem 0 .5rem; }
.empty-cart p { color: var(--mid); margin-bottom: 1.5rem; }

@media (max-width: 860px) { .cart-layout { grid-template-columns: 1fr; } .cart-summary { position: static; } }
</style>
@endpush

@section('content')
<div class="container cart-page">
    <h1>Shopping Cart
        @if($items->isNotEmpty())
        <span style="font-family:var(--font-sans);font-size:1rem;font-weight:400;color:var(--mid)">
            ({{ $items->count() }} item{{ $items->count() !== 1 ? 's' : '' }})
        </span>
        @endif
    </h1>

    @if($items->isEmpty())
    <div class="empty-cart">
        <svg width="64" height="64" fill="none" stroke="var(--border)" stroke-width="1.2" viewBox="0 0 24 24" style="display:block;margin:0 auto"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
        <h3>Your cart is empty</h3>
        <p>Discover our amazing products and add something you love.</p>
        <a href="{{ route('shop.catalog') }}" class="btn btn-gold btn-lg">Browse Products</a>
    </div>
    @else
    <div class="cart-layout">
        <div class="cart-items-card">
            @foreach($items as $item)
            <div class="cart-item">
                <div class="ci-img">
                    @if($item['image_path'])
                        <img src="{{ asset($item['image_path']) }}" alt="{{ $item['name'] }}">
                    @else
                        <div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;color:var(--border)">
                            <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                        </div>
                    @endif
                </div>
                <div class="ci-info">
                    <div class="ci-name">{{ $item['name'] }}</div>
                    <div class="ci-price">UGX {{ number_format($item['price'], 0) }} each</div>
                </div>
                <div class="ci-right">
                    <div class="ci-total">UGX {{ number_format($item['line_total'], 0) }}</div>
                    <form action="{{ route('shop.cart.update') }}" method="POST" style="display:flex;align-items:center;gap:.4rem">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $item['id'] }}">
                        <div class="ci-qty-ctrl">
                            <button type="submit" name="qty" value="{{ max(0, $item['qty'] - 1) }}">−</button>
                            <span>{{ $item['qty'] }}</span>
                            <button type="submit" name="qty" value="{{ min($item['stock'], $item['qty'] + 1) }}">+</button>
                        </div>
                    </form>
                    <form action="{{ route('shop.cart.remove') }}" method="POST">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $item['id'] }}">
                        <button type="submit" class="ci-remove">
                            <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/></svg>
                            Remove
                        </button>
                    </form>
                </div>
            </div>
            @endforeach
        </div>

        <div class="cart-summary">
            <h3>Order Summary</h3>
            @php
                $subtotal = $items->sum('line_total');
                $tax = $items->sum('line_tax');
            @endphp
            <div class="sum-row"><span class="lbl">Subtotal</span><span class="val">UGX {{ number_format($subtotal, 0) }}</span></div>
            @if($tax > 0)
            <div class="sum-row"><span class="lbl">Tax</span><span class="val">UGX {{ number_format($tax, 0) }}</span></div>
            @endif
            <div class="sum-row"><span class="lbl">Delivery</span><span class="val" style="color:var(--success)">Calculated at checkout</span></div>
            <div class="sum-row grand">
                <span class="lbl">Estimated Total</span>
                <span class="val">UGX {{ number_format($subtotal + $tax, 0) }}</span>
            </div>
            <a href="{{ route('shop.checkout') }}" class="checkout-btn">
                Proceed to Checkout
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
            </a>
            <div style="text-align:center;margin-top:.8rem">
                <a href="{{ route('shop.catalog') }}" style="font-size:.82rem;color:var(--light);text-decoration:none">← Continue Shopping</a>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection