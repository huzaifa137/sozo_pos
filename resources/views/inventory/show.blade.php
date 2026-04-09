@extends('layouts.app')
@section('page-title', $customer->name)

@section('topbar-actions')
    <a href="{{ route('customers.edit', $customer) }}" class="topbar-btn tb-outline">Edit</a>
    <a href="{{ route('customers.index') }}" class="topbar-btn tb-outline">← Customers</a>
@endsection

@section('content')
<div style="display:grid;grid-template-columns:300px 1fr;gap:1.2rem;align-items:start">

    {{-- Profile card --}}
    <div class="card">
        <div style="text-align:center;margin-bottom:1.2rem">
            <div style="width:60px;height:60px;border-radius:50%;background:rgba(240,192,64,.1);border:2px solid rgba(240,192,64,.3);display:flex;align-items:center;justify-content:center;font-family:var(--font-head);font-weight:800;font-size:1.3rem;color:var(--accent);margin:0 auto .8rem">
                {{ substr($customer->name,0,2) }}
            </div>
            <h3 style="font-family:var(--font-head);font-weight:700;font-size:1.1rem">{{ $customer->name }}</h3>
            <span class="badge {{ 'tier-'.$customer->loyalty_tier }}" style="margin-top:.3rem">{{ ucfirst($customer->loyalty_tier) }} Member</span>
        </div>

        <div style="display:flex;flex-direction:column;gap:.6rem;font-size:.85rem">
            @if($customer->phone)
            <div style="display:flex;justify-content:space-between">
                <span style="color:var(--muted)">Phone</span>
                <span>{{ $customer->phone }}</span>
            </div>
            @endif
            @if($customer->email)
            <div style="display:flex;justify-content:space-between">
                <span style="color:var(--muted)">Email</span>
                <span>{{ $customer->email }}</span>
            </div>
            @endif
            @if($customer->address)
            <div>
                <span style="color:var(--muted);display:block;margin-bottom:.2rem">Address</span>
                <span>{{ $customer->address }}</span>
            </div>
            @endif
        </div>

        <div style="margin-top:1rem;padding-top:1rem;border-top:1px solid var(--border);display:grid;grid-template-columns:1fr 1fr;gap:.6rem;text-align:center">
            <div style="background:var(--surf2);border-radius:8px;padding:.7rem">
                <div style="font-family:var(--font-head);font-weight:800;font-size:1.2rem;color:var(--accent)">{{ number_format($customer->loyalty_points) }}</div>
                <div style="font-size:.7rem;color:var(--muted)">Loyalty Pts</div>
            </div>
            <div style="background:var(--surf2);border-radius:8px;padding:.7rem">
                <div style="font-family:var(--font-head);font-weight:800;font-size:.9rem;color:var(--success)">{{ number_format($customer->total_spent, 0) }}</div>
                <div style="font-size:.7rem;color:var(--muted)">Total Spent</div>
            </div>
        </div>

        <div style="margin-top:.8rem;text-align:center;font-size:.75rem;color:var(--muted)">
            Customer since {{ $customer->created_at->format('d M Y') }}
        </div>
    </div>

    {{-- Purchase history --}}
    <div class="card" style="padding:0;overflow:hidden">
        <div style="padding:1rem 1.2rem;border-bottom:1px solid var(--border);font-family:var(--font-head);font-weight:700">
            Purchase History
        </div>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Receipt</th>
                    <th>Date</th>
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
                    <td style="font-family:var(--font-head);font-weight:700;color:var(--accent2);font-size:.82rem">{{ $sale->receipt_number }}</td>
                    <td>
                        <div style="font-size:.85rem">{{ $sale->created_at->format('d M Y') }}</div>
                        <div style="font-size:.73rem;color:var(--muted)">{{ $sale->created_at->format('H:i') }}</div>
                    </td>
                    <td style="font-size:.82rem;color:var(--muted)">{{ $sale->items->count() }} item(s)</td>
                    <td>
                        <span class="badge {{ $sale->payment_method === 'cash' ? 'badge-yellow' : 'badge-blue' }}">{{ str_replace('_',' ',$sale->payment_method) }}</span>
                    </td>
                    <td style="text-align:right;font-family:var(--font-head);font-weight:700">{{ number_format($sale->total, 0) }}</td>
                    <td><span class="badge {{ $sale->status === 'completed' ? 'badge-green' : 'badge-red' }}">{{ $sale->status }}</span></td>
                    <td><a href="{{ route('pos.receipt', $sale) }}" class="btn btn-outline btn-sm">Receipt</a></td>
                </tr>
                @empty
                <tr><td colspan="7" style="text-align:center;padding:2rem;color:var(--muted)">No purchase history.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div style="padding:1rem">{{ $sales->links() }}</div>
    </div>
</div>
@endsection

@push('styles')
<style>
.tier-gold   {background:rgba(240,192,64,.15);color:var(--accent);border:1px solid rgba(240,192,64,.3)}
.tier-silver {background:rgba(148,163,184,.12);color:#94a3b8;border:1px solid rgba(148,163,184,.25)}
.tier-bronze {background:rgba(180,120,60,.12);color:#c8864a;border:1px solid rgba(180,120,60,.25)}
</style>
@endpush
