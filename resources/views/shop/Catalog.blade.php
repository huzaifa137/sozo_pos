@extends('layouts.shop')
@section('title', 'All Products — SOZO Store')

@push('styles')
<style>
.catalog-wrap { display: grid; grid-template-columns: 260px 1fr; gap: 2rem; padding: 2.5rem 0; align-items: start; }
/* ── SIDEBAR ── */
.sidebar { background: var(--white); border: 1px solid var(--border); border-radius: 14px; padding: 1.5rem; position: sticky; top: 82px; }
.sidebar-section { margin-bottom: 1.5rem; }
.sidebar-section:last-child { margin-bottom: 0; }
.sidebar-section h4 { font-size: .75rem; font-weight: 700; text-transform: uppercase; letter-spacing: .08em; color: var(--light); margin-bottom: .9rem; }
.filter-chip { display: flex; align-items: center; gap: .6rem; padding: .45rem .8rem; border-radius: 7px; font-size: .85rem; font-weight: 500; color: var(--dark); text-decoration: none; transition: all .15s; border: 1px solid transparent; }
.filter-chip:hover { background: var(--warm); }
.filter-chip.active { background: rgba(201,151,58,.1); color: var(--gold); border-color: rgba(201,151,58,.25); font-weight: 600; }
.filter-chip .chip-count { margin-left: auto; font-size: .72rem; color: var(--light); background: var(--warm); border-radius: 10px; padding: .1rem .5rem; }
.filter-chip.active .chip-count { background: rgba(201,151,58,.15); color: var(--gold); }
.price-inputs { display: flex; gap: .5rem; align-items: center; }
.price-inputs input { flex: 1; padding: .5rem .7rem; border: 1px solid var(--border); border-radius: 7px; font-family: var(--font-sans); font-size: .85rem; color: var(--charcoal); background: var(--warm); outline: none; }
.price-inputs input:focus { border-color: var(--gold); }
.apply-price { padding: .5rem .9rem; border-radius: 7px; background: var(--charcoal); color: var(--white); border: none; font-size: .82rem; font-weight: 600; cursor: pointer; transition: background .15s; white-space: nowrap; }
.apply-price:hover { background: var(--gold); }

/* ── CATALOG MAIN ── */
.catalog-toolbar { display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.3rem; flex-wrap: wrap; gap: .6rem; }
.catalog-info { font-size: .88rem; color: var(--mid); }
.catalog-info strong { color: var(--charcoal); }
.sort-select { padding: .5rem .8rem; border: 1px solid var(--border); border-radius: 8px; font-family: var(--font-sans); font-size: .88rem; color: var(--charcoal); background: var(--white); outline: none; cursor: pointer; }
.sort-select:focus { border-color: var(--gold); }

/* empty */
.catalog-empty { text-align: center; padding: 4rem 2rem; color: var(--light); }
.catalog-empty h3 { font-family: var(--font-serif); font-size: 1.3rem; color: var(--charcoal); margin-bottom: .5rem; }

/* pagination */
.pag-wrap { margin-top: 2rem; display: flex; justify-content: center; }
.pag-wrap nav span, .pag-wrap nav a { display: inline-flex; align-items: center; padding: .45rem .85rem; margin: 0 .15rem; border-radius: 7px; border: 1px solid var(--border); color: var(--mid); font-size: .88rem; text-decoration: none; background: var(--white); transition: all .15s; }
.pag-wrap nav a:hover { border-color: var(--gold); color: var(--gold); }
.pag-wrap nav span[aria-current="page"] { background: var(--gold); color: var(--white); border-color: var(--gold); font-weight: 700; }

@media (max-width: 900px) { .catalog-wrap { grid-template-columns: 1fr; } .sidebar { position: static; } }
</style>
@endpush

@section('content')
<div class="container">
    {{-- Breadcrumb --}}
    <nav style="padding:1.2rem 0 0;font-size:.82rem;color:var(--light)">
        <a href="{{ route('shop.index') }}" style="color:var(--light);text-decoration:none">Home</a>
        <span style="margin:0 .5rem">/</span>
        <span style="color:var(--charcoal);font-weight:500">
            {{ request('category') ? (\App\Http\Controllers\InventoryController::CATEGORIES[request('category')] ?? request('category')) : 'All Products' }}
        </span>
    </nav>

    <div class="catalog-wrap">
        {{-- ── SIDEBAR ── --}}
        <aside class="sidebar">
            <div class="sidebar-section">
                <h4>Categories</h4>
                <a href="{{ route('shop.catalog', request()->except('category')) }}" class="filter-chip {{ !request('category') ? 'active' : '' }}">
                    All Products
                    <span class="chip-count">{{ \App\Models\InventoryItem::where('published',true)->where('quantity','>',0)->count() }}</span>
                </a>
                @foreach(\App\Http\Controllers\InventoryController::CATEGORIES as $key => $label)
                @php $cnt = \App\Models\InventoryItem::where('category',$key)->where('published',true)->where('quantity','>',0)->count(); @endphp
                @if($cnt > 0)
                <a href="{{ route('shop.catalog', array_merge(request()->all(), ['category'=>$key])) }}"
                   class="filter-chip {{ request('category') === $key ? 'active' : '' }}">
                    {{ $label }}
                    <span class="chip-count">{{ $cnt }}</span>
                </a>
                @endif
                @endforeach
            </div>

            <div class="sidebar-section">
                <h4>Price Range (UGX)</h4>
                <form method="GET" action="{{ route('shop.catalog') }}">
                    @if(request('category'))<input type="hidden" name="category" value="{{ request('category') }}">@endif
                    @if(request('sort'))<input type="hidden" name="sort" value="{{ request('sort') }}">@endif
                    <div class="price-inputs">
                        <input type="number" name="min_price" placeholder="Min" value="{{ request('min_price') }}" min="0">
                        <span style="color:var(--light);flex-shrink:0">—</span>
                        <input type="number" name="max_price" placeholder="Max" value="{{ request('max_price') }}" min="0">
                        <button type="submit" class="apply-price">Go</button>
                    </div>
                </form>
            </div>

            @if(request()->hasAny(['category','min_price','max_price','q']))
            <a href="{{ route('shop.catalog') }}" style="display:block;text-align:center;font-size:.82rem;color:var(--gold);text-decoration:none;padding:.5rem;border:1px dashed rgba(201,151,58,.3);border-radius:7px">
                Clear all filters ×
            </a>
            @endif
        </aside>

        {{-- ── PRODUCTS ── --}}
        <div>
            <div class="catalog-toolbar">
                <p class="catalog-info">
                    Showing <strong>{{ $products->firstItem() ?? 0 }}–{{ $products->lastItem() ?? 0 }}</strong>
                    of <strong>{{ $products->total() }}</strong> products
                </p>
                <form method="GET" style="display:inline">
                    @foreach(request()->except('sort') as $k => $v)
                    <input type="hidden" name="{{ $k }}" value="{{ $v }}">
                    @endforeach
                    <select name="sort" class="sort-select" onchange="this.form.submit()">
                        <option value="newest"     {{ request('sort','newest') === 'newest'     ? 'selected':'' }}>Newest First</option>
                        <option value="price_asc"  {{ request('sort') === 'price_asc'  ? 'selected':'' }}>Price: Low → High</option>
                        <option value="price_desc" {{ request('sort') === 'price_desc' ? 'selected':'' }}>Price: High → Low</option>
                    </select>
                </form>
            </div>

            @if($products->isEmpty())
            <div class="catalog-empty">
                <svg width="52" height="52" fill="none" stroke="currentColor" stroke-width="1.2" viewBox="0 0 24 24" style="margin:0 auto 1rem;display:block"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                <h3>No products found</h3>
                <p>Try adjusting your filters or <a href="{{ route('shop.catalog') }}" style="color:var(--gold)">browse everything</a>.</p>
            </div>
            @else
            <div class="products-grid">
                @foreach($products as $product)
                @include('shop.partials.product-card', ['product' => $product])
                @endforeach
            </div>
            <div class="pag-wrap">{{ $products->links() }}</div>
            @endif
        </div>
    </div>
</div>
@endsection