@extends('layouts.shop')
@section('title', 'SOZO Store — Premium Shopping in Kampala')

@push('styles')
<style>
/* ── HERO ── */
.hero {
    background: var(--charcoal);
    position: relative;
    overflow: hidden;
    min-height: 580px;
    display: flex;
    align-items: center;
}
.hero::before {
    content: '';
    position: absolute; inset: 0;
    background:
        radial-gradient(ellipse 80% 60% at 65% 50%, rgba(201,151,58,.18) 0%, transparent 70%),
        radial-gradient(ellipse 40% 40% at 20% 80%, rgba(201,151,58,.08) 0%, transparent 60%);
}
/* animated particles */
.hero-particles { position: absolute; inset: 0; overflow: hidden; }
.hero-particles span {
    position: absolute; width: 1px; height: 1px;
    background: rgba(201,151,58,.6); border-radius: 50%;
    animation: float var(--dur, 8s) ease-in-out infinite;
    animation-delay: var(--delay, 0s);
}
@keyframes float {
    0%,100% { transform: translateY(0) scale(1); opacity: .3; }
    50%      { transform: translateY(-40px) scale(2); opacity: .8; }
}
.hero-content {
    position: relative; z-index: 2;
    max-width: 1300px; margin: 0 auto;
    padding: 5rem 2rem;
    display: grid; grid-template-columns: 1fr 1fr;
    gap: 3rem; align-items: center;
}
.hero-text .hero-eyebrow {
    font-size: .78rem; font-weight: 600;
    text-transform: uppercase; letter-spacing: .12em;
    color: var(--gold-lt); margin-bottom: .8rem;
    display: flex; align-items: center; gap: .5rem;
}
.hero-eyebrow::before { content: ''; display: inline-block; width: 24px; height: 1px; background: var(--gold-lt); }
.hero-text h1 {
    font-family: var(--font-serif);
    font-size: clamp(2.2rem, 4vw, 3.2rem);
    font-weight: 700;
    color: var(--white);
    line-height: 1.15;
    margin-bottom: 1.2rem;
}
.hero-text h1 em { color: var(--gold-lt); font-style: italic; }
.hero-text p {
    color: rgba(255,255,255,.6);
    font-size: 1rem; line-height: 1.7;
    margin-bottom: 2rem; max-width: 460px;
}
.hero-cta { display: flex; gap: .8rem; flex-wrap: wrap; }
.hero-stats {
    display: flex; gap: 2rem; margin-top: 2.5rem;
    padding-top: 1.5rem; border-top: 1px solid rgba(255,255,255,.08);
}
.hero-stat .num {
    font-family: var(--font-serif);
    font-size: 1.5rem; font-weight: 700;
    color: var(--gold-lt);
}
.hero-stat .lbl { font-size: .75rem; color: rgba(255,255,255,.45); text-transform: uppercase; letter-spacing: .06em; }

.hero-visual {
    position: relative;
    display: grid; grid-template-columns: 1fr 1fr; gap: .8rem;
}
.hero-card {
    background: rgba(255,255,255,.06);
    border: 1px solid rgba(255,255,255,.1);
    border-radius: 14px;
    overflow: hidden;
    transition: transform .3s, box-shadow .3s;
}
.hero-card:hover { transform: translateY(-4px); box-shadow: 0 20px 40px rgba(0,0,0,.3); }
.hero-card:first-child { grid-column: span 2; }
.hero-card img { width: 100%; aspect-ratio: 4/3; object-fit: cover; display: block; }
.hero-card-body { padding: .7rem .9rem; }
.hero-card-name { font-size: .85rem; font-weight: 500; color: rgba(255,255,255,.85); }
.hero-card-price { font-family: var(--font-serif); font-size: .95rem; font-weight: 700; color: var(--gold-lt); }

/* ── CATEGORIES ── */
.cats-section { padding: 3rem 0; }
.section-header { display: flex; align-items: baseline; justify-content: space-between; margin-bottom: 1.8rem; }
.section-header h2 { font-family: var(--font-serif); font-size: 1.8rem; font-weight: 700; }
.section-header a { font-size: .85rem; color: var(--gold); text-decoration: none; font-weight: 600; }
.section-header a:hover { text-decoration: underline; }

.cats-grid { display: grid; grid-template-columns: repeat(5, 1fr); gap: .8rem; }
.cat-card {
    background: var(--warm);
    border: 1px solid var(--border);
    border-radius: 12px;
    padding: 1.4rem 1rem;
    text-align: center;
    text-decoration: none; color: var(--charcoal);
    transition: all .2s;
}
.cat-card:hover { background: var(--charcoal); color: var(--white); border-color: var(--charcoal); transform: translateY(-2px); }
.cat-card:hover .cat-icon { background: rgba(201,151,58,.2); }
.cat-icon {
    width: 52px; height: 52px; border-radius: 12px;
    background: rgba(201,151,58,.1);
    display: flex; align-items: center; justify-content: center;
    margin: 0 auto .7rem;
    transition: background .2s;
}
.cat-card svg { color: var(--gold); }
.cat-name { font-size: .85rem; font-weight: 600; }
.cat-count { font-size: .72rem; color: var(--light); margin-top: .2rem; }
.cat-card:hover .cat-count { color: rgba(255,255,255,.5); }

/* ── PRODUCT GRID ── */
.products-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(230px, 1fr)); gap: 1.2rem; }
.prod-card {
    background: var(--white);
    border: 1px solid var(--border);
    border-radius: 14px;
    overflow: hidden;
    transition: box-shadow .22s, transform .22s;
    position: relative;
}
.prod-card:hover { box-shadow: 0 8px 30px rgba(0,0,0,.1); transform: translateY(-3px); }
.prod-img-wrap {
    position: relative;
    aspect-ratio: 4/3;
    overflow: hidden;
    background: var(--warm);
}
.prod-img-wrap img { width: 100%; height: 100%; object-fit: cover; transition: transform .4s; display: block; }
.prod-card:hover .prod-img-wrap img { transform: scale(1.05); }
.prod-img-placeholder { width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; color: var(--border); }
.prod-tag {
    position: absolute; top: .7rem; left: .7rem;
    background: var(--gold); color: var(--white);
    font-size: .68rem; font-weight: 700;
    padding: .2rem .6rem; border-radius: 20px;
    text-transform: uppercase; letter-spacing: .05em;
}
.prod-wishlist {
    position: absolute; top: .7rem; right: .7rem;
    background: rgba(255,255,255,.9); border: none;
    width: 32px; height: 32px; border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    cursor: pointer; color: var(--light); transition: all .15s;
    opacity: 0;
}
.prod-card:hover .prod-wishlist { opacity: 1; }
.prod-wishlist:hover { color: var(--danger); background: #fff; }
.prod-body { padding: .9rem 1rem; }
.prod-cat { font-size: .7rem; color: var(--light); text-transform: uppercase; letter-spacing: .06em; margin-bottom: .3rem; }
.prod-name {
    font-family: var(--font-serif);
    font-size: .98rem; font-weight: 500;
    line-height: 1.3; margin-bottom: .5rem;
    color: var(--charcoal); text-decoration: none; display: block;
}
.prod-name:hover { color: var(--gold); }
.prod-footer { display: flex; align-items: center; justify-content: space-between; }
.prod-price { font-family: var(--font-serif); font-size: 1.05rem; font-weight: 700; color: var(--gold); }
.prod-stock-low { font-size: .7rem; color: #c0392b; font-weight: 600; }
.prod-add-btn {
    width: 34px; height: 34px; border-radius: 8px;
    background: var(--charcoal); color: var(--white);
    border: none; cursor: pointer;
    display: flex; align-items: center; justify-content: center;
    transition: background .15s;
}
.prod-add-btn:hover { background: var(--gold); }

/* ── BANNER ── */
.promo-banner {
    background: linear-gradient(135deg, var(--charcoal) 0%, #2d2924 100%);
    border-radius: 16px;
    padding: 3rem 2.5rem;
    display: grid; grid-template-columns: 1fr auto;
    gap: 2rem; align-items: center;
    position: relative; overflow: hidden;
}
.promo-banner::before {
    content: '';
    position: absolute; right: -60px; top: -60px;
    width: 300px; height: 300px;
    border-radius: 50%;
    background: radial-gradient(circle, rgba(201,151,58,.2), transparent 70%);
}
.promo-eyebrow { font-size: .75rem; font-weight: 600; text-transform: uppercase; letter-spacing: .1em; color: var(--gold-lt); margin-bottom: .6rem; }
.promo-banner h3 { font-family: var(--font-serif); font-size: 2rem; color: var(--white); line-height: 1.2; margin-bottom: .7rem; }
.promo-banner p { color: rgba(255,255,255,.55); font-size: .9rem; }

/* ── TRUST BAR ── */
.trust-bar {
    background: var(--warm);
    border-top: 1px solid var(--border);
    border-bottom: 1px solid var(--border);
    padding: 1.5rem 0;
}
.trust-items { display: flex; justify-content: center; gap: 3rem; flex-wrap: wrap; }
.trust-item { display: flex; align-items: center; gap: .7rem; font-size: .85rem; font-weight: 500; color: var(--dark); }
.trust-item svg { color: var(--gold); flex-shrink: 0; }

@media (max-width:900px) { .hero-content { grid-template-columns: 1fr; } .hero-visual { display: none; } .cats-grid { grid-template-columns: repeat(3,1fr); } }
@media (max-width:600px) { .cats-grid { grid-template-columns: repeat(2,1fr); } .hero-stats { gap: 1rem; } }
</style>
@endpush

@section('content')

{{-- ── HERO ── --}}
<section class="hero">
    <div class="hero-particles">
        @for($i=0;$i<20;$i++)
        <span style="left:{{ rand(0,100) }}%;top:{{ rand(0,100) }}%;width:{{ rand(1,3) }}px;height:{{ rand(1,3) }}px;--dur:{{ rand(5,12) }}s;--delay:{{ rand(0,8) }}s"></span>
        @endfor
    </div>
    <div class="hero-content">
        <div class="hero-text">
            <div class="hero-eyebrow">Kampala's Premium Store</div>
            <h1>Shop <em>Smarter,</em><br>Live Better</h1>
            <p>Discover curated products across electronics, fashion, home, and more — all available for same-day pickup or delivery across Kampala.</p>
            <div class="hero-cta">
                <a href="{{ route('shop.catalog') }}" class="btn btn-gold btn-lg">
                    Shop All Products
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                </a>
                <a href="{{ route('shop.catalog', ['category'=>'electronics']) }}" class="btn btn-outline btn-lg" style="color:rgba(255,255,255,.7);border-color:rgba(255,255,255,.2)">Browse Electronics</a>
            </div>
            <div class="hero-stats">
                <div class="hero-stat"><div class="num">500+</div><div class="lbl">Products</div></div>
                <div class="hero-stat"><div class="num">1K+</div><div class="lbl">Happy Customers</div></div>
                <div class="hero-stat"><div class="num">5★</div><div class="lbl">Rated Service</div></div>
            </div>
        </div>
        <div class="hero-visual">
            @foreach($featured->take(3) as $i => $p)
            <div class="hero-card {{ $i === 0 ? '' : '' }}">
                <div style="aspect-ratio:4/3;overflow:hidden">
                    @if($p->image_path)
                        <img src="{{ asset($p->image_path) }}" alt="{{ $p->name }}" style="width:100%;height:100%;object-fit:cover">
                    @else
                        <div style="width:100%;height:100%;background:rgba(255,255,255,.06);display:flex;align-items:center;justify-content:center">
                            <svg width="36" height="36" fill="none" stroke="rgba(255,255,255,.2)" stroke-width="1.5" viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                        </div>
                    @endif
                </div>
                <div class="hero-card-body">
                    <div class="hero-card-name">{{ Str::limit($p->name, 30) }}</div>
                    <div class="hero-card-price">UGX {{ number_format($p->selling_price, 0) }}</div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ── TRUST BAR ── --}}
<div class="trust-bar">
    <div class="trust-items">
        <div class="trust-item"><svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M5 12h14M12 5l7 7-7 7"/></svg> Same-day delivery in Kampala</div>
        <div class="trust-item"><svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg> Quality Guaranteed</div>
        <div class="trust-item"><svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 .49-5"/></svg> Easy Returns</div>
        <div class="trust-item"><svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg> Secure Payments</div>
    </div>
</div>

{{-- ── CATEGORIES ── --}}
<section class="section">
    <div class="container">
        <div class="section-header">
            <h2>Shop by Category</h2>
            <a href="{{ route('shop.catalog') }}">View all →</a>
        </div>
        <div class="cats-grid">
            @php
            $catIcons = [
                'electronics'  => '<svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><rect x="2" y="3" width="20" height="14" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>',
                'clothing'     => '<svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path d="M20.38 3.46L16 2a4 4 0 0 1-8 0L3.62 3.46a2 2 0 0 0-1.34 2.23l.58 3.57a1 1 0 0 0 .99.84H6v10c0 1.1.9 2 2 2h8a2 2 0 0 0 2-2V10h2.15a1 1 0 0 0 .99-.84l.58-3.57a2 2 0 0 0-1.34-2.23z"/></svg>',
                'food_beverage'=> '<svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path d="M18 8h1a4 4 0 0 1 0 8h-1"/><path d="M2 8h16v9a4 4 0 0 1-4 4H6a4 4 0 0 1-4-4V8z"/><line x1="6" y1="1" x2="6" y2="4"/><line x1="10" y1="1" x2="10" y2="4"/><line x1="14" y1="1" x2="14" y2="4"/></svg>',
                'furniture'    => '<svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path d="M20 9V7a2 2 0 0 0-2-2H6a2 2 0 0 0-2 2v2"/><path d="M2 11v5a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-5a2 2 0 0 0-4 0v2H6v-2a2 2 0 0 0-4 0z"/><line x1="6" y1="19" x2="6" y2="21"/><line x1="18" y1="19" x2="18" y2="21"/></svg>',
                'stationery'   => '<svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>',
            ];
            @endphp
            @foreach(\App\Http\Controllers\InventoryController::CATEGORIES as $key => $label)
            @php $count = \App\Models\InventoryItem::where('category',$key)->where('published',true)->where('quantity','>',0)->count(); @endphp
            @if($count > 0)
            <a href="{{ route('shop.catalog', ['category'=>$key]) }}" class="cat-card">
                <div class="cat-icon">{!! $catIcons[$key] ?? '' !!}</div>
                <div class="cat-name">{{ $label }}</div>
                <div class="cat-count">{{ $count }} item{{ $count !== 1 ? 's' : '' }}</div>
            </a>
            @endif
            @endforeach
        </div>
    </div>
</section>

{{-- ── FEATURED ── --}}
@if($featured->isNotEmpty())
<section class="section" style="padding-top:0">
    <div class="container">
        <div class="section-header">
            <h2>Featured Products</h2>
            <a href="{{ route('shop.catalog') }}">See all →</a>
        </div>
        <div class="products-grid">
            @foreach($featured as $product)
            @include('shop.partials.product-card', ['product' => $product])
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- ── PROMO BANNER ── --}}
<section class="section" style="padding-top:0">
    <div class="container">
        <div class="promo-banner">
            <div>
                <div class="promo-eyebrow">Limited Time Offer</div>
                <h3>New Arrivals<br>Every Week</h3>
                <p>Fresh stock added regularly — electronics, clothing, home goods and more.</p>
            </div>
            <a href="{{ route('shop.catalog', ['sort'=>'newest']) }}" class="btn btn-gold btn-lg" style="flex-shrink:0">
                Shop New In
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
            </a>
        </div>
    </div>
</section>

{{-- ── NEW ARRIVALS ── --}}
@if($newArrivals->isNotEmpty())
<section class="section" style="padding-top:0">
    <div class="container">
        <div class="section-header">
            <h2>New Arrivals</h2>
            <a href="{{ route('shop.catalog', ['sort'=>'newest']) }}">See all →</a>
        </div>
        <div class="products-grid">
            @foreach($newArrivals as $product)
            @include('shop.partials.product-card', ['product' => $product])
            @endforeach
        </div>
    </div>
</section>
@endif

@endsection