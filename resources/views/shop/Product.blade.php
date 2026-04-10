@extends('layouts.shop')
@section('title', $product->name . ' — SOZO Store')

@push('styles')
<style>
.product-page { padding: 2.5rem 0 4rem; }
.product-breadcrumb { font-size: .82rem; color: var(--light); margin-bottom: 1.8rem; }
.product-breadcrumb a { color: var(--light); text-decoration: none; }
.product-breadcrumb a:hover { color: var(--gold); }
.product-breadcrumb span { margin: 0 .5rem; }

.product-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 3rem; margin-bottom: 4rem; }

/* ── IMAGE ── */
.product-img-main {
    border-radius: 16px; overflow: hidden;
    background: var(--warm);
    aspect-ratio: 1;
    border: 1px solid var(--border);
}
.product-img-main img { width: 100%; height: 100%; object-fit: cover; }
.product-img-placeholder { width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; color: var(--border); }

/* ── INFO ── */
.product-info {}
.product-category { font-size: .75rem; font-weight: 700; text-transform: uppercase; letter-spacing: .1em; color: var(--gold); margin-bottom: .6rem; }
.product-title { font-family: var(--font-serif); font-size: 1.8rem; font-weight: 700; line-height: 1.2; margin-bottom: 1rem; }
.product-price-wrap { display: flex; align-items: baseline; gap: 1rem; margin-bottom: 1.2rem; }
.product-price { font-family: var(--font-serif); font-size: 2rem; font-weight: 700; color: var(--gold); }
.product-profit-badge { font-size: .78rem; font-weight: 600; color: var(--success); background: rgba(45,122,79,.1); border-radius: 20px; padding: .2rem .7rem; }

.product-meta { display: flex; flex-direction: column; gap: .5rem; padding: 1.1rem; background: var(--warm); border-radius: 10px; margin-bottom: 1.5rem; font-size: .88rem; }
.meta-row { display: flex; align-items: center; gap: .6rem; }
.meta-row svg { color: var(--gold); flex-shrink: 0; }
.meta-row strong { color: var(--charcoal); }

.product-desc { font-size: .92rem; line-height: 1.75; color: var(--mid); margin-bottom: 1.5rem; }

.variant-chips { display: flex; flex-wrap: wrap; gap: .5rem; margin-bottom: 1.4rem; }
.variant-chip { padding: .35rem .8rem; border-radius: 7px; border: 1px solid var(--border); font-size: .83rem; font-weight: 500; color: var(--dark); cursor: pointer; transition: all .15s; }
.variant-chip.active, .variant-chip:hover { border-color: var(--gold); color: var(--gold); background: rgba(201,151,58,.06); }

/* qty + add */
.add-to-cart-row { display: flex; gap: .8rem; align-items: center; margin-bottom: 1rem; }
.qty-control { display: flex; align-items: center; border: 1.5px solid var(--border); border-radius: 9px; overflow: hidden; }
.qty-control button { width: 40px; height: 48px; border: none; background: var(--warm); cursor: pointer; font-size: 1.1rem; font-weight: 700; color: var(--dark); transition: background .15s; }
.qty-control button:hover { background: var(--border); }
.qty-control input { width: 50px; height: 48px; border: none; text-align: center; font-family: var(--font-sans); font-size: .95rem; font-weight: 600; color: var(--charcoal); background: var(--white); outline: none; }
.add-btn { flex: 1; padding: .85rem; display: flex; align-items: center; justify-content: center; gap: .5rem; border-radius: 9px; border: none; background: var(--charcoal); color: var(--white); font-family: var(--font-sans); font-size: .95rem; font-weight: 700; cursor: pointer; transition: background .18s; }
.add-btn:hover { background: var(--gold); }

.buy-now-btn { width: 100%; padding: .85rem; border-radius: 9px; border: 1.5px solid var(--gold); background: transparent; color: var(--gold); font-family: var(--font-sans); font-size: .95rem; font-weight: 700; cursor: pointer; transition: all .18s; }
.buy-now-btn:hover { background: var(--gold); color: var(--white); }

.out-of-stock-msg { background: rgba(192,57,43,.08); border: 1px solid rgba(192,57,43,.2); border-radius: 9px; padding: .9rem 1rem; color: var(--danger); font-weight: 600; font-size: .9rem; text-align: center; }

/* related */
.related-section h2 { font-family: var(--font-serif); font-size: 1.6rem; font-weight: 700; margin-bottom: 1.5rem; }

@media (max-width: 860px) { .product-grid { grid-template-columns: 1fr; } }
</style>
@endpush

@section('content')
<div class="container product-page">

    <nav class="product-breadcrumb">
        <a href="{{ route('shop.index') }}">Home</a>
        <span>/</span>
        <a href="{{ route('shop.catalog') }}">Products</a>
        <span>/</span>
        <a href="{{ route('shop.catalog', ['category'=>$product->category]) }}">{{ \App\Http\Controllers\InventoryController::CATEGORIES[$product->category] ?? $product->category }}</a>
        <span>/</span>
        <strong style="color:var(--charcoal)">{{ $product->name }}</strong>
    </nav>

    <div class="product-grid">
        {{-- IMAGE --}}
        <div class="product-img-main">
            @if($product->image_path)
                <img src="{{ asset($product->image_path) }}" alt="{{ $product->name }}">
            @else
                <div class="product-img-placeholder">
                    <svg width="80" height="80" fill="none" stroke="currentColor" stroke-width="1" viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                </div>
            @endif
        </div>

        {{-- INFO --}}
        <div class="product-info">
            <div class="product-category">{{ \App\Http\Controllers\InventoryController::CATEGORIES[$product->category] ?? $product->category }}</div>
            <h1 class="product-title">{{ $product->name }}</h1>

            <div class="product-price-wrap">
                <div class="product-price">UGX {{ number_format($product->selling_price, 0) }}</div>
            </div>

            <div class="product-meta">
                @if($product->quantity > 0)
                <div class="meta-row">
                    <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
                    <span><strong style="color:var(--success)">In Stock</strong> — {{ $product->quantity }} available</span>
                </div>
                @else
                <div class="meta-row">
                    <svg width="15" height="15" fill="none" stroke="var(--danger)" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
                    <span style="color:var(--danger);font-weight:600">Out of Stock</span>
                </div>
                @endif
                @if($product->sku)
                <div class="meta-row">
                    <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
                    <span>SKU: <strong>{{ $product->sku }}</strong></span>
                </div>
                @endif
                @if($product->expiry_date)
                <div class="meta-row">
                    <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                    <span>Best before: <strong>{{ $product->expiry_date->format('d M Y') }}</strong></span>
                </div>
                @endif
            </div>

            @if($product->description)
            <p class="product-desc">{{ $product->description }}</p>
            @endif

            {{-- Variants --}}
            @if($product->size || $product->color || $product->model)
            <div style="margin-bottom:1rem">
                @if($product->size)<div style="margin-bottom:.5rem;font-size:.82rem;color:var(--mid)">Size: <strong style="color:var(--charcoal)">{{ $product->size }}</strong></div>@endif
                @if($product->color)<div style="margin-bottom:.5rem;font-size:.82rem;color:var(--mid)">Color: <strong style="color:var(--charcoal)">{{ $product->color }}</strong></div>@endif
                @if($product->model)<div style="margin-bottom:.5rem;font-size:.82rem;color:var(--mid)">Model: <strong style="color:var(--charcoal)">{{ $product->model }}</strong></div>@endif
            </div>
            @endif

            @if($product->quantity > 0)
            <form action="{{ route('shop.cart.add') }}" method="POST">
                @csrf
                <input type="hidden" name="product_id" value="{{ $product->id }}">
                <div class="add-to-cart-row">
                    <div class="qty-control">
                        <button type="button" onclick="changeQty(-1)">−</button>
                        <input type="number" name="qty" id="qtyInput" value="1" min="1" max="{{ $product->quantity }}">
                        <button type="button" onclick="changeQty(1)">+</button>
                    </div>
                    <button type="submit" class="add-btn">
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
                        Add to Cart
                    </button>
                </div>
            </form>
            <form action="{{ route('shop.cart.add') }}" method="POST">
                @csrf
                <input type="hidden" name="product_id" value="{{ $product->id }}">
                <input type="hidden" name="qty" value="1">
                <button type="submit" class="buy-now-btn" formaction="{{ route('shop.checkout') }}" onclick="document.getElementById('buyNowForm').submit()">
                    Buy Now
                </button>
            </form>
            @else
            <div class="out-of-stock-msg">Currently out of stock — check back soon</div>
            @endif
        </div>
    </div>

    {{-- RELATED --}}
    @if($related->isNotEmpty())
    <div class="related-section">
        <h2>You might also like</h2>
        <div class="products-grid">
            @foreach($related as $product)
            @include('shop.partials.product-card', ['product' => $product])
            @endforeach
        </div>
    </div>
    @endif

</div>
@endsection

@push('scripts')
<script>
function changeQty(delta) {
    const input = document.getElementById('qtyInput');
    const newVal = Math.min(parseInt(input.max), Math.max(1, parseInt(input.value) + delta));
    input.value = newVal;
}
</script>
@endpush