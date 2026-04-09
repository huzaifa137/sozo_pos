@extends('layouts.app')
@section('page-title', 'Dashboard')

@push('styles')
<style>
.kpi-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:1rem;margin-bottom:1.8rem}
.kpi{background:var(--surf);border:1px solid var(--border);border-radius:var(--radius);padding:1.2rem 1.4rem;position:relative;overflow:hidden}
.kpi::before{content:'';position:absolute;top:0;left:0;right:0;height:3px}
.kpi.yellow::before{background:var(--accent)}
.kpi.blue::before{background:var(--accent2)}
.kpi.green::before{background:var(--success)}
.kpi.red::before{background:var(--danger)}
.kpi .klabel{font-size:.72rem;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--muted);margin-bottom:.4rem}
.kpi .kval{font-family:var(--font-head);font-size:1.8rem;font-weight:800;line-height:1;margin-bottom:.25rem}
.kpi .ksub{font-size:.78rem;color:var(--muted)}
.kpi.yellow .kval{color:var(--accent)}
.kpi.blue   .kval{color:var(--accent2)}
.kpi.green  .kval{color:var(--success)}
.kpi.red    .kval{color:var(--danger)}

.dash-grid{display:grid;grid-template-columns:2fr 1fr;gap:1.2rem;margin-bottom:1.2rem}
.dash-grid-3{display:grid;grid-template-columns:1fr 1fr 1fr;gap:1.2rem}
.section-title{font-family:var(--font-head);font-weight:700;font-size:.95rem;margin-bottom:1rem;display:flex;align-items:center;gap:.5rem;justify-content:space-between}
.section-title a{font-family:var(--font-body);font-size:.78rem;font-weight:500;color:var(--muted);text-decoration:none;transition:color .15s}
.section-title a:hover{color:var(--accent)}

/* chart */
.chart-wrap{position:relative;height:200px}

/* top items */
.top-item{display:flex;align-items:center;gap:.8rem;padding:.55rem 0;border-bottom:1px solid rgba(255,255,255,.04)}
.top-item:last-child{border-bottom:none}
.ti-rank{font-family:var(--font-head);font-weight:800;font-size:.9rem;color:var(--muted);min-width:20px}
.ti-name{flex:1;font-size:.85rem;font-weight:500}
.ti-qty{font-size:.78rem;color:var(--muted);text-align:right}
.ti-rev{font-family:var(--font-head);font-weight:700;font-size:.88rem;color:var(--accent);min-width:90px;text-align:right}

/* recent sales */
.sale-row{display:flex;align-items:center;gap:.8rem;padding:.6rem 0;border-bottom:1px solid rgba(255,255,255,.04)}
.sale-row:last-child{border-bottom:none}
.sr-rcpt{font-family:var(--font-head);font-weight:700;font-size:.82rem;color:var(--accent2)}
.sr-info{flex:1}
.sr-who{font-size:.85rem;font-weight:500}
.sr-when{font-size:.72rem;color:var(--muted)}
.sr-total{font-family:var(--font-head);font-weight:700;font-size:.9rem}
.sr-method{font-size:.72rem;text-transform:capitalize}

/* cashier perf */
.cashier-row{display:flex;align-items:center;gap:.7rem;padding:.55rem 0;border-bottom:1px solid rgba(255,255,255,.04)}
.cashier-row:last-child{border-bottom:none}
.ca-avatar{width:30px;height:30px;border-radius:50%;background:rgba(240,192,64,.1);border:1px solid rgba(240,192,64,.2);display:flex;align-items:center;justify-content:center;font-family:var(--font-head);font-weight:700;font-size:.7rem;color:var(--accent);flex-shrink:0}
.ca-name{flex:1;font-size:.85rem;font-weight:500}
.ca-sales{font-size:.75rem;color:var(--muted)}
.ca-rev{font-family:var(--font-head);font-weight:700;font-size:.88rem;color:var(--success)}

/* alert cards */
.alert-card{background:rgba(239,68,68,.08);border:1px solid rgba(239,68,68,.2);border-radius:var(--radius);padding:1rem 1.2rem;display:flex;align-items:center;gap:.8rem}
.alert-card.warn{background:rgba(245,158,11,.08);border-color:rgba(245,158,11,.2)}
.ac-icon{color:var(--danger);flex-shrink:0}
.alert-card.warn .ac-icon{color:var(--warn)}
.ac-text .ac-title{font-weight:700;font-size:.88rem;margin-bottom:.15rem}
.ac-text .ac-sub{font-size:.78rem;color:var(--muted)}

@media(max-width:1100px){.kpi-grid{grid-template-columns:repeat(2,1fr)}.dash-grid{grid-template-columns:1fr}.dash-grid-3{grid-template-columns:1fr 1fr}}
@media(max-width:700px){.dash-grid-3{grid-template-columns:1fr}}
</style>
@endpush

@section('topbar-actions')
    <a href="{{ route('pos.terminal') }}" class="topbar-btn tb-primary">
        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><rect x="2" y="3" width="20" height="14" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
        Open POS
    </a>
@endsection

@section('content')

{{-- ── KPIs ── --}}
<div class="kpi-grid">
    <div class="kpi yellow">
        <div class="klabel">Today's Revenue</div>
        <div class="kval">{{ number_format($todayRevenue, 0) }}</div>
        <div class="ksub">UGX · {{ $todayCount }} sale{{ $todayCount !== 1 ? 's' : '' }}</div>
    </div>
    <div class="kpi green">
        <div class="klabel">Today's Profit</div>
        <div class="kval">{{ number_format($todayProfit, 0) }}</div>
        <div class="ksub">UGX estimated</div>
    </div>
    <div class="kpi blue">
        <div class="klabel">Month Revenue</div>
        <div class="kval">{{ number_format($monthRevenue, 0) }}</div>
        <div class="ksub">UGX · {{ $monthCount }} sales</div>
    </div>
    @if($lowStock + $outOfStock > 0)
    <div class="kpi red">
        <div class="klabel">Stock Alerts</div>
        <div class="kval">{{ $lowStock + $outOfStock }}</div>
        <div class="ksub">{{ $outOfStock }} out · {{ $lowStock }} low</div>
    </div>
    @else
    <div class="kpi green">
        <div class="klabel">Stock Health</div>
        <div class="kval" style="font-size:1.4rem">All Good</div>
        <div class="ksub">No stock alerts</div>
    </div>
    @endif
</div>

{{-- ── ALERTS ── --}}
@if($outOfStock > 0)
<div class="alert-card" style="margin-bottom:1rem">
    <svg class="ac-icon" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
    <div class="ac-text">
        <div class="ac-title">{{ $outOfStock }} item{{ $outOfStock > 1 ? 's are' : ' is' }} out of stock</div>
        <div class="ac-sub"><a href="{{ route('reports.inventory') }}?filter=out" style="color:var(--danger)">View items →</a></div>
    </div>
</div>
@endif
@if($lowStock > 0)
<div class="alert-card warn" style="margin-bottom:1.2rem">
    <svg class="ac-icon" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
    <div class="ac-text">
        <div class="ac-title">{{ $lowStock }} item{{ $lowStock > 1 ? 's are' : ' is' }} running low</div>
        <div class="ac-sub"><a href="{{ route('reports.inventory') }}?filter=low" style="color:var(--warn)">View items →</a></div>
    </div>
</div>
@endif

{{-- ── CHARTS + TOP ITEMS ── --}}
<div class="dash-grid">
    <div class="card">
        <div class="section-title">Revenue — Last 7 Days <a href="{{ route('reports.sales') }}">View all →</a></div>
        <div class="chart-wrap"><canvas id="salesChart"></canvas></div>
    </div>
    <div class="card">
        <div class="section-title">Top Products This Month</div>
        @forelse($topItems as $i => $item)
        <div class="top-item">
            <span class="ti-rank">#{{ $i+1 }}</span>
            <span class="ti-name">{{ $item->item_name }}</span>
            <span class="ti-qty">{{ $item->total_qty }} sold</span>
            <span class="ti-rev">{{ number_format($item->total_revenue, 0) }}</span>
        </div>
        @empty
        <p style="color:var(--muted);font-size:.85rem">No sales data yet.</p>
        @endforelse
    </div>
</div>

<div class="dash-grid-3">
    {{-- Recent Sales --}}
    <div class="card">
        <div class="section-title">Recent Sales <a href="{{ route('reports.sales') }}">All →</a></div>
        @forelse($recentSales as $sale)
        <div class="sale-row">
            <div class="sr-info">
                <div class="sr-rcpt">{{ $sale->receipt_number }}</div>
                <div class="sr-who">{{ $sale->customer?->name ?? 'Walk-in' }}</div>
                <div class="sr-when">{{ $sale->created_at->diffForHumans() }} · {{ $sale->user->name }}</div>
            </div>
            <div style="text-align:right">
                <div class="sr-total">{{ number_format($sale->total, 0) }}</div>
                <div class="sr-method badge badge-{{ $sale->payment_method === 'cash' ? 'yellow' : 'blue' }}">{{ $sale->payment_method }}</div>
            </div>
        </div>
        @empty
        <p style="color:var(--muted);font-size:.85rem">No sales yet today.</p>
        @endforelse
    </div>

    {{-- Cashier Performance --}}
    <div class="card">
        <div class="section-title">Cashier Performance Today</div>
        @forelse($cashierPerf as $c)
        <div class="cashier-row">
            <div class="ca-avatar">{{ substr($c->name,0,2) }}</div>
            <div>
                <div class="ca-name">{{ $c->name }}</div>
                <div class="ca-sales">{{ $c->sales_count }} sales</div>
            </div>
            <div class="ca-rev">{{ number_format($c->total_revenue, 0) }}</div>
        </div>
        @empty
        <p style="color:var(--muted);font-size:.85rem">No activity today.</p>
        @endforelse
    </div>

    {{-- Quick Actions --}}
    <div class="card">
        <div class="section-title">Quick Actions</div>
        <div style="display:flex;flex-direction:column;gap:.6rem">
            <a href="{{ route('pos.terminal') }}" class="btn btn-primary" style="justify-content:center">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="2" y="3" width="20" height="14" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/></svg>
                Open POS Terminal
            </a>
            <a href="{{ route('inventory.create') }}" class="btn btn-outline" style="justify-content:center">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></svg>
                Add Inventory Item
            </a>
            <a href="{{ route('customers.create') }}" class="btn btn-outline" style="justify-content:center">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="8.5" cy="7" r="4"/><line x1="20" y1="8" x2="20" y2="14"/><line x1="23" y1="11" x2="17" y2="11"/></svg>
                Add Customer
            </a>
            <a href="{{ route('reports.sales') }}" class="btn btn-outline" style="justify-content:center">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="12" y1="20" x2="12" y2="10"/><line x1="18" y1="20" x2="18" y2="4"/><line x1="6" y1="20" x2="6" y2="16"/></svg>
                Sales Report
            </a>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
<script>
const chartData = @json($salesChart);
const labels  = chartData.map(d => new Date(d.date).toLocaleDateString('en-UG',{weekday:'short',month:'short',day:'numeric'}));
const revenue = chartData.map(d => parseFloat(d.revenue));

new Chart(document.getElementById('salesChart'), {
    type:'bar',
    data:{
        labels,
        datasets:[{
            label:'Revenue (UGX)',
            data: revenue,
            backgroundColor:'rgba(240,192,64,.25)',
            borderColor:'rgba(240,192,64,.8)',
            borderWidth:1.5,
            borderRadius:5,
        }]
    },
    options:{
        responsive:true,maintainAspectRatio:false,
        plugins:{legend:{display:false},tooltip:{callbacks:{label:ctx=>'UGX '+ctx.raw.toLocaleString()}}},
        scales:{
            x:{grid:{color:'rgba(255,255,255,.04)'},ticks:{color:'#6b7280',font:{size:11}}},
            y:{grid:{color:'rgba(255,255,255,.05)'},ticks:{color:'#6b7280',font:{size:11},callback:v=>'UGX '+(v/1000).toFixed(0)+'k'}}
        }
    }
});
</script>
@endpush
