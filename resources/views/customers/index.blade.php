@extends('layouts.app')
@section('page-title', 'Customers')

@push('styles')
<style>
.tier-gold   {background:rgba(240,192,64,.15);color:var(--accent);border:1px solid rgba(240,192,64,.3)}
.tier-silver {background:rgba(148,163,184,.12);color:#94a3b8;border:1px solid rgba(148,163,184,.25)}
.tier-bronze {background:rgba(180,120,60,.12);color:#c8864a;border:1px solid rgba(180,120,60,.25)}
.customer-avatar{width:36px;height:36px;border-radius:50%;background:var(--surf2);border:1px solid var(--border);display:flex;align-items:center;justify-content:center;font-family:var(--font-head);font-weight:700;font-size:.8rem;flex-shrink:0}
.filters-bar{background:var(--surf);border:1px solid var(--border);border-radius:var(--radius);padding:.8rem 1rem;display:flex;gap:.7rem;align-items:center;flex-wrap:wrap;margin-bottom:1.4rem}
.filters-bar input,.filters-bar select{padding:.5rem .8rem;font-size:.85rem}
</style>
@endpush

@section('topbar-actions')
    <a href="{{ route('customers.create') }}" class="topbar-btn tb-primary">+ Add Customer</a>
@endsection

@section('content')

<form method="GET" action="{{ route('customers.index') }}">
    <div class="filters-bar">
        <input type="text" name="search" placeholder="Search name, phone, email…" value="{{ request('search') }}" style="min-width:220px">
        <select name="tier" style="max-width:160px">
            <option value="">All Tiers</option>
            <option value="gold"   {{ request('tier')==='gold'   ? 'selected':'' }}>Gold</option>
            <option value="silver" {{ request('tier')==='silver' ? 'selected':'' }}>Silver</option>
            <option value="bronze" {{ request('tier')==='bronze' ? 'selected':'' }}>Bronze</option>
        </select>
        <button type="submit" class="btn btn-primary btn-sm">Search</button>
        @if(request()->hasAny(['search','tier']))
        <a href="{{ route('customers.index') }}" class="btn btn-outline btn-sm">Clear</a>
        @endif
        <span style="margin-left:auto;font-size:.82rem;color:var(--muted)">{{ $customers->total() }} customers</span>
    </div>
</form>

<div class="card" style="padding:0;overflow:hidden">
    <table class="data-table">
        <thead>
            <tr>
                <th>Customer</th>
                <th>Contact</th>
                <th>Tier</th>
                <th>Loyalty Points</th>
                <th>Total Spent</th>
                <th>Purchases</th>
                <th>Since</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse($customers as $customer)
            <tr>
                <td>
                    <div style="display:flex;align-items:center;gap:.7rem">
                        <div class="customer-avatar">{{ substr($customer->name,0,2) }}</div>
                        <div>
                            <div style="font-weight:600;font-size:.9rem">{{ $customer->name }}</div>
                            @if($customer->address)
                            <div style="font-size:.73rem;color:var(--muted)">{{ Str::limit($customer->address,30) }}</div>
                            @endif
                        </div>
                    </div>
                </td>
                <td>
                    <div style="font-size:.85rem">{{ $customer->phone ?? '—' }}</div>
                    <div style="font-size:.75rem;color:var(--muted)">{{ $customer->email ?? '' }}</div>
                </td>
                <td>
                    <span class="badge tier-{{ $customer->loyalty_tier }}">
                        {{ ucfirst($customer->loyalty_tier) }}
                    </span>
                </td>
                <td style="font-family:var(--font-head);font-weight:700;color:var(--accent)">
                    {{ number_format($customer->loyalty_points) }} pts
                </td>
                <td style="font-family:var(--font-head);font-weight:700;font-size:.9rem">
                    {{ number_format($customer->total_spent, 0) }}
                </td>
                <td style="font-size:.85rem;color:var(--muted)">{{ $customer->sales_count }} orders</td>
                <td style="font-size:.78rem;color:var(--muted)">{{ $customer->created_at->format('d M Y') }}</td>
                <td>
                    <div style="display:flex;gap:.4rem">
                        <a href="{{ route('customers.show', $customer) }}" class="btn btn-outline btn-sm">View</a>
                        <a href="{{ route('customers.edit', $customer) }}" class="btn btn-outline btn-sm">Edit</a>
                        <form action="{{ route('customers.destroy', $customer) }}" method="POST" onsubmit="return confirm('Delete this customer?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm" style="background:rgba(239,68,68,.1);color:#fca5a5;border:1px solid rgba(239,68,68,.2)">Del</button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="8" style="text-align:center;padding:3rem;color:var(--muted)">No customers found.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="pagination-wrap">{{ $customers->links() }}</div>
@endsection
