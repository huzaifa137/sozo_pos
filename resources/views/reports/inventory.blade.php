@extends('layouts.app')
@section('page-title', 'Stock Report')

@push('styles')
<style>
.inv-filter-bar{display:flex;gap:.6rem;flex-wrap:wrap;margin-bottom:1.5rem;align-items:center}
.filter-chip{
    padding:.45rem 1rem;border-radius:20px;border:1px solid var(--border);
    background:var(--surf);color:var(--muted);font-size:.82rem;font-weight:600;
    text-decoration:none;transition:all .15s;
}
.filter-chip:hover{border-color:var(--accent);color:var(--accent)}
.filter-chip.active{background:rgba(240,192,64,.1);border-color:var(--accent);color:var(--accent)}
.filter-chip.red.active{background:rgba(239,68,68,.1);border-color:rgba(239,68,68,.5);color:#fca5a5}
.filter-chip.warn.active{background:rgba(245,158,11,.1);border-color:rgba(245,158,11,.5);color:#fcd34d}

.summary-bar{display:grid;grid-template-columns:repeat(4,1fr);gap:1rem;margin-bottom:1.5rem}
.sum-card{background:var(--surf);border:1px solid var(--border);border-radius:var(--radius);padding:.9rem 1.1rem}
.sum-card .sl{font-size:.7rem;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:var(--muted);margin-bottom:.3rem}
.sum-card .sv{font-family:var(--font-head);font-size:1.4rem;font-weight:800}
.sum-card.yellow .sv{color:var(--accent)}
.sum-card.red    .sv{color:var(--danger)}
.sum-card.warn   .sv{color:var(--warn)}
.sum-card.blue   .sv{color:var(--accent2)}

.stock-bar-wrap{width:80px;height:6px;background:var(--surf2);border-radius:3px;overflow:hidden;display:inline-block;vertical-align:middle}
.stock-bar{height:100%;border-radius:3px;transition:width .3s}
</style>
@endpush

@section('topbar-actions')
    <a href="{{ route('inventory.create') }}" class="topbar-btn tb-primary">+ Add Item</a>
@endsection

@section('content')

<div class="summary-bar">
    <div class="sum-card yellow">
        <div class="sl">Total Products</div>
        <div class="sv">{{ $summary['total'] }}</div>
    </div>
    <div class="sum-card warn">
        <div class="sl">Low Stock</div>
        <div class="sv">{{ $summary['low'] }}</div>
    </div>
    <div class="sum-card red">
        <div class="sl">Out of Stock</div>
        <div class="sv">{{ $summary['out'] }}</div>
    </div>
    <div class="sum-card blue">
        <div class="sl">Expiring Soon</div>
        <div class="sv">{{ $summary['expiring'] }}</div>
    </div>
</div>

<div class="inv-filter-bar">
    <a href="{{ route('reports.inventory') }}" class="filter-chip {{ !request('filter') ? 'active' : '' }}">All Items</a>
    <a href="{{ route('reports.inventory') }}?filter=low"  class="filter-chip warn {{ request('filter')==='low' ? 'active warn' : '' }}">Low Stock ({{ $summary['low'] }})</a>
    <a href="{{ route('reports.inventory') }}?filter=out"  class="filter-chip red {{ request('filter')==='out' ? 'active red' : '' }}">Out of Stock ({{ $summary['out'] }})</a>
    <a href="{{ route('reports.inventory') }}?filter=expiring" class="filter-chip {{ request('filter')==='expiring' ? 'active' : '' }}">Expiring Soon ({{ $summary['expiring'] }})</a>
</div>

<div class="card" style="padding:0;overflow:hidden">
    <table class="data-table">
        <thead>
            <tr>
                <th>Item</th>
                <th>SKU / Barcode</th>
                <th>Category</th>
                <th>Stock Qty</th>
                <th>Stock Level</th>
                <th>Buying</th>
                <th>Selling</th>
                <th>Margin</th>
                <th>Expiry</th>
                <th>Batch</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse($items as $item)
            @php
                $pct = $item->low_stock_threshold > 0
                    ? min(100, ($item->quantity / max($item->low_stock_threshold * 2, 1)) * 100)
                    : 100;
                $barColor = $item->quantity === 0 ? '#ef4444' : ($item->isLowStock() ? '#f59e0b' : '#22c55e');
            @endphp
            <tr>
                <td>
                    <div style="font-weight:600;font-size:.88rem">{{ $item->name }}</div>
                    @if($item->size || $item->color || $item->model)
                    <div style="font-size:.72rem;color:var(--muted)">
                        {{ implode(' · ', array_filter([$item->size, $item->color, $item->model])) }}
                    </div>
                    @endif
                </td>
                <td style="font-size:.78rem;color:var(--muted)">
                    @if($item->sku)<div>SKU: {{ $item->sku }}</div>@endif
                    @if($item->barcode)<div>BC: {{ $item->barcode }}</div>@endif
                    @if(!$item->sku && !$item->barcode)—@endif
                </td>
                <td style="font-size:.82rem;color:var(--muted)">{{ $item->category }}</td>
                <td>
                    <span style="font-family:var(--font-head);font-weight:700;color:{{ $item->quantity === 0 ? 'var(--danger)' : ($item->isLowStock() ? 'var(--warn)' : 'var(--text)') }}">
                        {{ $item->quantity }}
                    </span>
                    <span style="font-size:.72rem;color:var(--muted)"> / {{ $item->low_stock_threshold }} min</span>
                </td>
                <td>
                    <div class="stock-bar-wrap">
                        <div class="stock-bar" style="width:{{ $pct }}%;background:{{ $barColor }}"></div>
                    </div>
                </td>
                <td style="font-size:.85rem">{{ number_format($item->buying_price, 0) }}</td>
                <td style="font-family:var(--font-head);font-weight:700;color:var(--accent)">{{ number_format($item->selling_price, 0) }}</td>
                <td>
                    <span class="badge {{ $item->profit_margin >= 20 ? 'badge-green' : ($item->profit_margin >= 10 ? 'badge-warn' : 'badge-red') }}">
                        {{ $item->profit_margin }}%
                    </span>
                </td>
                <td style="font-size:.8rem">
                    @if($item->expiry_date)
                        <span style="color:{{ $item->isExpiringSoon() ? 'var(--warn)' : 'var(--muted)' }}">
                            {{ $item->expiry_date->format('d M Y') }}
                        </span>
                    @else
                        <span style="color:var(--muted)">—</span>
                    @endif
                </td>
                <td style="font-size:.78rem;color:var(--muted)">{{ $item->batch_number ?? '—' }}</td>
                <td>
                    <a href="{{ route('inventory.edit', $item) }}" class="btn btn-outline btn-sm">Edit</a>
                </td>
            </tr>
            @empty
            <tr><td colspan="11" style="text-align:center;padding:3rem;color:var(--muted)">No items found.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="pagination-wrap">{{ $items->links() }}</div>
@endsection
