@extends('layouts.app')
@section('page-title', 'Online Orders')

@push('styles')
<style>
.order-summary-bar{display:grid;grid-template-columns:repeat(5,1fr);gap:.8rem;margin-bottom:1.5rem}
.os-card{background:var(--surf);border:1px solid var(--border);border-radius:var(--radius);padding:.9rem 1.1rem;cursor:pointer;transition:border-color .15s;text-decoration:none;display:block}
.os-card:hover{border-color:var(--accent)}
.os-card .osl{font-size:.68rem;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--muted);margin-bottom:.3rem}
.os-card .osv{font-family:var(--font-head);font-size:1.4rem;font-weight:800}
.os-card.pending .osv{color:var(--warn)}
.os-card.confirmed .osv{color:var(--accent2)}
.os-card.processing .osv{color:var(--accent2)}
.os-card.ready .osv{color:var(--accent)}
.os-card.online .osv{color:var(--success)}

.filters-bar{background:var(--surf);border:1px solid var(--border);border-radius:var(--radius);padding:.8rem 1rem;display:flex;gap:.6rem;align-items:center;flex-wrap:wrap;margin-bottom:1.3rem}
.filters-bar input,.filters-bar select{padding:.5rem .8rem;font-size:.85rem}

.status-select{padding:.35rem .6rem;border-radius:6px;border:1px solid var(--border);background:var(--surf2);color:var(--text);font-size:.78rem;font-family:var(--font-body);cursor:pointer;outline:none}
.status-select:focus{border-color:var(--accent)}
</style>
@endpush

@section('topbar-actions')
    <span style="font-size:.85rem;color:var(--muted)">Online orders from the store</span>
@endsection

@section('content')

<div class="order-summary-bar">
    <a href="{{ route('orders.index', ['status'=>'pending']) }}" class="os-card pending">
        <div class="osl">Pending</div><div class="osv">{{ $summary['pending'] }}</div>
    </a>
    <a href="{{ route('orders.index', ['status'=>'confirmed']) }}" class="os-card confirmed">
        <div class="osl">Confirmed</div><div class="osv">{{ $summary['confirmed'] }}</div>
    </a>
    <a href="{{ route('orders.index', ['status'=>'processing']) }}" class="os-card processing">
        <div class="osl">Processing</div><div class="osv">{{ $summary['processing'] }}</div>
    </a>
    <a href="{{ route('orders.index', ['status'=>'ready']) }}" class="os-card ready">
        <div class="osl">Ready</div><div class="osv">{{ $summary['ready'] }}</div>
    </a>
    <a href="{{ route('orders.index', ['channel'=>'online']) }}" class="os-card online">
        <div class="osl">Today Online</div><div class="osv">{{ $summary['today'] }}</div>
    </a>
</div>

<form method="GET" action="{{ route('orders.index') }}">
    <div class="filters-bar">
        <input type="text" name="search" placeholder="Search order # or customer…" value="{{ request('search') }}" style="min-width:220px">
        <select name="status">
            <option value="">All Statuses</option>
            @foreach(['pending','confirmed','processing','ready','delivered','cancelled'] as $s)
            <option value="{{ $s }}" {{ request('status') === $s ? 'selected':'' }}>{{ ucfirst($s) }}</option>
            @endforeach
        </select>
        <select name="channel">
            <option value="">All Channels</option>
            <option value="online" {{ request('channel') === 'online' ? 'selected':'' }}>Online</option>
            <option value="pos"    {{ request('channel') === 'pos'    ? 'selected':'' }}>POS</option>
        </select>
        <button type="submit" class="btn btn-primary btn-sm">Filter</button>
        @if(request()->hasAny(['search','status','channel']))
        <a href="{{ route('orders.index') }}" class="btn btn-outline btn-sm">Clear</a>
        @endif
    </div>
</form>

<div class="card" style="padding:0;overflow:hidden">
    <table class="data-table">
        <thead>
            <tr>
                <th>Order #</th>
                <th>Date</th>
                <th>Customer</th>
                <th>Items</th>
                <th>Delivery</th>
                <th>Payment</th>
                <th style="text-align:right">Total</th>
                <th>Channel</th>
                <th>Status</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse($orders as $order)
            <tr>
                <td style="font-family:var(--font-head);font-weight:700;color:var(--accent2);font-size:.82rem">
                    {{ $order->order_number }}
                </td>
                <td>
                    <div style="font-size:.85rem">{{ $order->created_at->format('d M Y') }}</div>
                    <div style="font-size:.73rem;color:var(--muted)">{{ $order->created_at->format('H:i') }}</div>
                </td>
                <td>
                    <div style="font-size:.88rem;font-weight:500">{{ $order->buyer_name }}</div>
                    <div style="font-size:.73rem;color:var(--muted)">{{ $order->buyer_email }}</div>
                </td>
                <td style="font-size:.82rem;color:var(--muted)">{{ $order->items->count() }} item(s)</td>
                <td>
                    <span class="badge {{ $order->delivery_method === 'pickup' ? 'badge-yellow' : 'badge-blue' }}">
                        {{ $order->delivery_method }}
                    </span>
                </td>
                <td style="font-size:.8rem;text-transform:capitalize;color:var(--muted)">
                    {{ str_replace('_',' ',$order->payment_method) }}
                </td>
                <td style="text-align:right;font-family:var(--font-head);font-weight:700">
                    {{ number_format($order->total, 0) }}
                </td>
                <td>
                    <span class="badge {{ $order->channel === 'online' ? 'badge-blue' : 'badge-yellow' }}">
                        {{ $order->channel }}
                    </span>
                </td>
                <td>
                    <form action="{{ route('orders.status', $order) }}" method="POST">
                        @csrf @method('PATCH')
                        <select name="status" class="status-select" onchange="this.form.submit()">
                            @foreach(['pending','confirmed','processing','ready','delivered','cancelled'] as $s)
                            <option value="{{ $s }}" {{ $order->status === $s ? 'selected':'' }}>{{ ucfirst($s) }}</option>
                            @endforeach
                        </select>
                    </form>
                </td>
                <td><a href="{{ route('orders.show', $order) }}" class="btn btn-outline btn-sm">View</a></td>
            </tr>
            @empty
            <tr><td colspan="10" style="text-align:center;padding:3rem;color:var(--muted)">No orders found.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="pagination-wrap">{{ $orders->links() }}</div>
@endsection