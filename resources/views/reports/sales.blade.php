@extends('layouts.app')
@section('page-title', 'Sales Report')

@push('styles')
<style>
.filters-bar{background:var(--surf);border:1px solid var(--border);border-radius:var(--radius);padding:1rem 1.2rem;display:flex;gap:.8rem;align-items:flex-end;flex-wrap:wrap;margin-bottom:1.5rem}
.filters-bar .form-group{margin-bottom:0}
.filters-bar label{font-size:.72rem}
.filters-bar input,.filters-bar select{padding:.55rem .8rem;font-size:.85rem}
.summary-bar{display:grid;grid-template-columns:repeat(4,1fr);gap:1rem;margin-bottom:1.5rem}
.sum-card{background:var(--surf);border:1px solid var(--border);border-radius:var(--radius);padding:.9rem 1.1rem}
.sum-card .sl{font-size:.7rem;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:var(--muted);margin-bottom:.3rem}
.sum-card .sv{font-family:var(--font-head);font-size:1.3rem;font-weight:800}
.sum-card.yellow .sv{color:var(--accent)}
.sum-card.green  .sv{color:var(--success)}
.sum-card.blue   .sv{color:var(--accent2)}
.method-icon{font-size:.75rem;text-transform:capitalize}
.void-form{display:inline}
</style>
@endpush

@section('topbar-actions')
    <a href="{{ route('reports.dashboard') }}" class="topbar-btn tb-outline">← Dashboard</a>
@endsection

@section('content')

{{-- ── FILTERS ── --}}
<form method="GET" action="{{ route('reports.sales') }}">
    <div class="filters-bar">
        <div class="form-group">
            <label>From</label>
            <input type="date" name="from" value="{{ request('from', $from->toDateString()) }}">
        </div>
        <div class="form-group">
            <label>To</label>
            <input type="date" name="to" value="{{ request('to', $to->toDateString()) }}">
        </div>
        <div class="form-group">
            <label>Status</label>
            <select name="status">
                <option value="">All</option>
                <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                <option value="voided"    {{ request('status') === 'voided'    ? 'selected' : '' }}>Voided</option>
                <option value="refunded"  {{ request('status') === 'refunded'  ? 'selected' : '' }}>Refunded</option>
            </select>
        </div>
        <div class="form-group">
            <label>Payment</label>
            <select name="payment_method">
                <option value="">All</option>
                <option value="cash"         {{ request('payment_method') === 'cash'         ? 'selected' : '' }}>Cash</option>
                <option value="card"         {{ request('payment_method') === 'card'         ? 'selected' : '' }}>Card</option>
                <option value="mobile_money" {{ request('payment_method') === 'mobile_money' ? 'selected' : '' }}>Mobile Money</option>
                <option value="split"        {{ request('payment_method') === 'split'        ? 'selected' : '' }}>Split</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary btn-sm">Apply Filter</button>
        <a href="{{ route('reports.sales') }}" class="btn btn-outline btn-sm">Clear</a>
    </div>
</form>

{{-- ── SUMMARY ── --}}
<div class="summary-bar">
    <div class="sum-card yellow">
        <div class="sl">Total Revenue</div>
        <div class="sv">{{ number_format($totals->revenue ?? 0, 0) }}</div>
    </div>
    <div class="sum-card blue">
        <div class="sl">Transactions</div>
        <div class="sv">{{ number_format($totals->count ?? 0) }}</div>
    </div>
    <div class="sum-card green">
        <div class="sl">Tax Collected</div>
        <div class="sv">{{ number_format($totals->tax ?? 0, 0) }}</div>
    </div>
    <div class="sum-card">
        <div class="sl">Discounts Given</div>
        <div class="sv">{{ number_format($totals->discounts ?? 0, 0) }}</div>
    </div>
</div>

{{-- ── TABLE ── --}}
<div class="card" style="padding:0;overflow:hidden">
    <table class="data-table">
        <thead>
            <tr>
                <th>Receipt</th>
                <th>Date & Time</th>
                <th>Cashier</th>
                <th>Customer</th>
                <th>Items</th>
                <th>Payment</th>
                <th style="text-align:right">Total (UGX)</th>
                <th>Status</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse($sales as $sale)
            <tr>
                <td style="font-family:var(--font-head);font-weight:700;color:var(--accent2);font-size:.82rem">
                    {{ $sale->receipt_number }}
                </td>
                <td>
                    <div style="font-size:.85rem">{{ $sale->created_at->format('d M Y') }}</div>
                    <div style="font-size:.75rem;color:var(--muted)">{{ $sale->created_at->format('H:i') }}</div>
                </td>
                <td style="font-size:.85rem">{{ $sale->user->name }}</td>
                <td style="font-size:.85rem;color:var(--muted)">{{ $sale->customer?->name ?? 'Walk-in' }}</td>
                <td style="font-size:.82rem;color:var(--muted)">{{ $sale->items->count() }} item(s)</td>
                <td>
                    <span class="badge {{ $sale->payment_method === 'cash' ? 'badge-yellow' : ($sale->payment_method === 'card' ? 'badge-blue' : 'badge-green') }}">
                        {{ str_replace('_',' ',$sale->payment_method) }}
                    </span>
                </td>
                <td style="text-align:right;font-family:var(--font-head);font-weight:700">
                    {{ number_format($sale->total, 0) }}
                </td>
                <td>
                    <span class="badge {{ $sale->status === 'completed' ? 'badge-green' : ($sale->status === 'voided' ? 'badge-red' : 'badge-warn') }}">
                        {{ $sale->status }}
                    </span>
                </td>
                <td style="display:flex;gap:.4rem;align-items:center">
                    <a href="{{ route('pos.receipt', $sale) }}" class="btn btn-outline btn-sm" title="View Receipt">
                        <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M1 6v16l7-4 8 4 7-4V2l-7 4-8-4-7 4z"/><line x1="8" y1="2" x2="8" y2="18"/><line x1="16" y1="6" x2="16" y2="22"/></svg>
                    </a>
                    @if($sale->status === 'completed' && auth()->user()->isManager())
                    <form class="void-form" action="{{ route('pos.sale.void', $sale) }}" method="POST" onsubmit="return confirm('Void this sale and restore stock?')">
                        @csrf
                        <button type="submit" class="btn btn-sm" style="background:rgba(239,68,68,.1);color:#fca5a5;border:1px solid rgba(239,68,68,.2)">Void</button>
                    </form>
                    @endif
                </td>
            </tr>
            @empty
            <tr><td colspan="9" style="text-align:center;padding:3rem;color:var(--muted)">No sales found for this period.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="pagination-wrap">{{ $sales->links() }}</div>
@endsection
