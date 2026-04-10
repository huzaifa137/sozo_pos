@extends('layouts.app')

@section('title', 'Inventory')

@push('styles')
<style>
    /* ── PAGE HEADER ── */
    .page-top {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        margin-bottom: 1.8rem;
        gap: 1rem;
        flex-wrap: wrap;
    }
    .page-top h1 {
        font-family: var(--font-head);
        font-size: 2rem;
        font-weight: 800;
        letter-spacing: -.5px;
    }
    .page-top p { color: var(--muted); margin-top: .3rem; font-size: .9rem; }

    /* ── STATS BAR ── */
    .stats-bar {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 1rem;
        margin-bottom: 2rem;
    }
    .stat-card {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: var(--radius);
        padding: 1.1rem 1.3rem;
    }
    .stat-card .label {
        font-size: .75rem;
        font-weight: 600;
        color: var(--muted);
        text-transform: uppercase;
        letter-spacing: .05em;
        margin-bottom: .3rem;
    }
    .stat-card .value {
        font-family: var(--font-head);
        font-size: 1.6rem;
        font-weight: 800;
        line-height: 1;
    }
    .stat-card.accent .value { color: var(--accent); }
    .stat-card.blue   .value { color: var(--accent2); }
    .stat-card.green  .value { color: var(--success); }

    /* ── FILTERS ── */
    .filters {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: var(--radius);
        padding: 1rem 1.3rem;
        display: flex;
        gap: .8rem;
        align-items: center;
        flex-wrap: wrap;
        margin-bottom: 1.5rem;
    }
    .filters input,
    .filters select {
        background: var(--bg);
        border: 1px solid var(--border);
        color: var(--text);
        border-radius: 8px;
        padding: .55rem .85rem;
        font-family: var(--font-body);
        font-size: .88rem;
        outline: none;
        transition: border-color .18s;
        appearance: none;
    }
    .filters input { min-width: 200px; }
    .filters select { padding-right: 2rem; background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' fill='none' stroke='%236b7280' stroke-width='2' viewBox='0 0 24 24'%3E%3Cpath d='M6 9l6 6 6-6'/%3E%3C/svg%3E"); background-repeat:no-repeat; background-position: right .7rem center; }
    .filters input:focus, .filters select:focus { border-color: var(--accent); }

    /* ── GRID ── */
    .items-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(270px, 1fr));
        gap: 1.2rem;
    }

    /* ── ITEM CARD ── */
    .item-card {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: var(--radius);
        overflow: hidden;
        transition: transform .2s, border-color .2s;
    }
    .item-card:hover {
        transform: translateY(-3px);
        border-color: rgba(240,192,64,.3);
    }
    .item-img {
        width: 100%;
        aspect-ratio: 4/3;
        object-fit: cover;
        background: var(--bg);
        display: block;
    }
    .item-img-placeholder {
        width: 100%;
        aspect-ratio: 4/3;
        background: var(--surface2);
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--muted);
    }
    .item-body { padding: 1rem; }
    .item-category {
        font-size: .72rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .06em;
        color: var(--accent2);
        margin-bottom: .35rem;
    }
    .item-name {
        font-family: var(--font-head);
        font-weight: 700;
        font-size: 1.05rem;
        margin-bottom: .6rem;
        line-height: 1.2;
    }
    .item-prices {
        display: flex;
        gap: .8rem;
        margin-bottom: .8rem;
    }
    .price-block .plabel { font-size: .7rem; color: var(--muted); margin-bottom: .1rem; text-transform: uppercase; }
    .price-block .pvalue { font-family: var(--font-head); font-size: .95rem; font-weight: 700; }
    .price-block.sell .pvalue { color: var(--success); }
    .price-block.buy  .pvalue { color: var(--text); }

    .item-meta {
        display: flex;
        align-items: center;
        justify-content: space-between;
        font-size: .78rem;
        color: var(--muted);
        border-top: 1px solid var(--border);
        padding-top: .7rem;
        margin-top: .5rem;
    }
    .stock-tag {
        background: var(--surface2);
        border-radius: 5px;
        padding: .2rem .5rem;
        font-size: .72rem;
        font-weight: 600;
        color: var(--muted);
    }
    .qty-tag {
        display: flex;
        align-items: center;
        gap: .3rem;
    }
    .qty-tag .qty-num { font-weight: 700; color: var(--text); }

    .item-actions {
        display: flex;
        gap: .5rem;
        margin-top: .8rem;
    }
    .item-actions a, .item-actions button {
        flex: 1;
        text-align: center;
        padding: .45rem;
        border-radius: 7px;
        font-size: .8rem;
        font-weight: 500;
        font-family: var(--font-body);
        text-decoration: none;
        cursor: pointer;
        transition: all .15s;
        border: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: .3rem;
    }
    .btn-edit { background: var(--surface2); color: var(--text); border: 1px solid var(--border); }
    .btn-edit:hover { border-color: var(--accent2); color: var(--accent2); }
    .btn-del  { background: rgba(239,68,68,.1); color: #fca5a5; border: 1px solid rgba(239,68,68,.2); }
    .btn-del:hover  { background: rgba(239,68,68,.2); }

    /* ── EMPTY ── */
    .empty-state {
        text-align: center;
        padding: 5rem 2rem;
        color: var(--muted);
    }
    .empty-state svg { margin: 0 auto 1rem; color: var(--border); }
    .empty-state h3 { font-family: var(--font-head); font-size: 1.3rem; color: var(--text); margin-bottom: .5rem; }

    /* ── PAGINATION ── */
    .pagination-wrap {
        margin-top: 2rem;
        display: flex;
        justify-content: center;
    }
    .pagination-wrap nav span,
    .pagination-wrap nav a {
        display: inline-flex;
        align-items: center;
        padding: .5rem .85rem;
        margin: 0 .2rem;
        border-radius: 7px;
        border: 1px solid var(--border);
        color: var(--muted);
        font-size: .88rem;
        text-decoration: none;
        background: var(--surface);
        transition: all .15s;
    }
    .pagination-wrap nav a:hover { border-color: var(--accent); color: var(--accent); }
    .pagination-wrap nav span[aria-current="page"] { background: var(--accent); color: #0d0f14; border-color: var(--accent); font-weight: 700; }

    @media (max-width: 900px) {
        .stats-bar { grid-template-columns: repeat(2, 1fr); }
    }
    @media (max-width: 600px) {
        .stats-bar { grid-template-columns: 1fr 1fr; }
        .filters { flex-direction: column; }
        .filters input, .filters select { width: 100%; }
    }
</style>
@endpush

@section('content')

<div class="page-top">
    <div>
        <h1>Inventory</h1>
        <p>{{ $items->total() }} item{{ $items->total() !== 1 ? 's' : '' }} in stock</p>
    </div>
    <a href="{{ route('inventory.create') }}" class="btn btn-primary">
        <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></svg>
        Add Item
    </a>
</div>

{{-- ── STATS ── --}}
<div class="stats-bar">
    <div class="stat-card accent">
        <div class="label">Total Items</div>
        <div class="value">{{ $items->total() }}</div>
    </div>
    <div class="stat-card blue">
        <div class="label">Categories</div>
        <div class="value">{{ count($categories) }}</div>
    </div>
    <div class="stat-card green">
        <div class="label">Stock Batches</div>
        <div class="value">{{ count($stocks) }}</div>
    </div>
    <div class="stat-card">
        <div class="label">This Page</div>
        <div class="value">{{ $items->count() }}</div>
    </div>
</div>

{{-- ── FILTERS ── --}}
<form method="GET" action="{{ route('inventory.index') }}">
    <div class="filters">
        <input type="text" name="search" placeholder="Search items…" value="{{ request('search') }}">

        <select name="category">
            <option value="">All Categories</option>
            @foreach($categories as $key => $label)
                <option value="{{ $key }}" {{ request('category') == $key ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
        </select>

        <select name="stock_number">
            <option value="">All Stock Batches</option>
            @foreach($stocks as $key => $label)
                <option value="{{ $key }}" {{ request('stock_number') == $key ? 'selected' : '' }}>{{ $key }}</option>
            @endforeach
        </select>

        <button type="submit" class="btn btn-outline btn-sm">Filter</button>
        @if(request()->hasAny(['search','category','stock_number']))
            <a href="{{ route('inventory.index') }}" class="btn btn-outline btn-sm">Clear</a>
        @endif
    </div>
</form>

{{-- ── GRID ── --}}
@if($items->isEmpty())
    <div class="empty-state">
        <svg width="60" height="60" fill="none" stroke="currentColor" stroke-width="1.2" viewBox="0 0 24 24">
            <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/>
            <polyline points="3.27 6.96 12 12.01 20.73 6.96"/>
            <line x1="12" y1="22.08" x2="12" y2="12"/>
        </svg>
        <h3>No items found</h3>
        <p>Add your first inventory item to get started.</p>
        <br>
        <a href="{{ route('inventory.create') }}" class="btn btn-primary">Add First Item</a>
    </div>
@else
    <div class="items-grid">
        @foreach($items as $item)
        <div class="item-card">
            @if($item->image_path)
                <img src="{{ asset('storage/' . $item->image_path) }}" alt="{{ $item->name }}" class="item-img">
            @else
                <div class="item-img-placeholder">
                    <svg width="36" height="36" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                </div>
            @endif

            <div class="item-body">
                <div class="item-category">{{ $categories[$item->category] ?? $item->category }}</div>
                <div class="item-name">{{ $item->name }}</div>

                <div class="item-prices">
                    <div class="price-block sell">
                        <div class="plabel">Selling</div>
                        <div class="pvalue">{{ number_format($item->selling_price, 0) }}/=</div>
                    </div>
                    <div class="price-block buy">
                        <div class="plabel">Bought</div>
                        <div class="pvalue">{{ number_format($item->buying_price, 0) }}/=</div>
                    </div>
                </div>

                <div class="item-meta">
                    <span class="stock-tag">{{ $item->stock_number }}</span>
                    <span class="qty-tag">
                        <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8"/></svg>
                        <span class="qty-num">{{ $item->quantity }}</span> in stock
                    </span>
                </div>

                <div class="item-actions">
                    <a href="{{ route('inventory.edit', $item) }}" class="btn-edit">
                        <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4z"/></svg>
                        Edit
                    </a>
                    <form action="{{ route('inventory.destroy', $item) }}" method="POST" style="flex:1" onsubmit="return confirm('Remove this item?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn-del" style="width:100%">
                            <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6M14 11v6"/></svg>
                            Delete
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <div class="pagination-wrap">
        {{ $items->links() }}
    </div>
@endif

@endsection
