<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'SOZO POS')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:ital,wght@0,300;0,400;0,500;1,400&display=swap" rel="stylesheet">
    <style>
        *,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
        :root{
            --bg:#0b0d11;--surf:#13161d;--surf2:#1a1e29;--surf3:#222740;
            --border:#252b3b;--accent:#f0c040;--accent2:#3b82f6;
            --success:#22c55e;--danger:#ef4444;--warn:#f59e0b;
            --text:#eef0f6;--muted:#6b7280;--radius:12px;
            --font-head:'Syne',sans-serif;--font-body:'DM Sans',sans-serif;
            --sidebar:240px;
        }
        body{background:var(--bg);color:var(--text);font-family:var(--font-body);min-height:100vh;display:flex}

        /* ── SIDEBAR ── */
        .sidebar{
            width:var(--sidebar);min-height:100vh;background:var(--surf);
            border-right:1px solid var(--border);display:flex;flex-direction:column;
            position:fixed;top:0;left:0;bottom:0;z-index:200;
        }
        .sb-brand{
            padding:1.4rem 1.2rem 1rem;
            font-family:var(--font-head);font-weight:800;font-size:1.25rem;
            color:var(--accent);letter-spacing:-.5px;text-decoration:none;
            display:block;border-bottom:1px solid var(--border);
        }
        .sb-brand span{color:var(--text)}
        .sb-section{padding:.7rem .8rem .2rem;font-size:.65rem;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.1em}
        .sb-nav{flex:1;padding:.5rem .6rem;overflow-y:auto}
        .sb-link{
            display:flex;align-items:center;gap:.7rem;
            padding:.6rem .8rem;border-radius:9px;
            color:var(--muted);text-decoration:none;font-size:.88rem;font-weight:500;
            transition:all .15s;margin-bottom:2px;
        }
        .sb-link:hover{background:var(--surf2);color:var(--text)}
        .sb-link.active{background:rgba(240,192,64,.12);color:var(--accent);border:1px solid rgba(240,192,64,.2)}
        .sb-link svg{flex-shrink:0;opacity:.7}
        .sb-link.active svg{opacity:1}
        .sb-badge{
            margin-left:auto;background:var(--danger);color:#fff;
            font-size:.65rem;font-weight:700;padding:1px 6px;border-radius:20px;min-width:18px;text-align:center;
        }
        .sb-user{
            padding:1rem;border-top:1px solid var(--border);
            display:flex;align-items:center;gap:.7rem;
        }
        .sb-avatar{
            width:34px;height:34px;border-radius:50%;
            background:rgba(240,192,64,.15);border:1px solid rgba(240,192,64,.3);
            display:flex;align-items:center;justify-content:center;
            font-family:var(--font-head);font-weight:700;font-size:.8rem;color:var(--accent);
            flex-shrink:0;
        }
        .sb-uname{font-size:.82rem;font-weight:600;line-height:1.2}
        .sb-urole{font-size:.72rem;color:var(--muted);text-transform:capitalize}
        .sb-logout{
            margin-left:auto;color:var(--muted);background:none;border:none;cursor:pointer;
            transition:color .15s;padding:4px;
        }
        .sb-logout:hover{color:var(--danger)}

        /* ── MAIN ── */
        .main-wrap{margin-left:var(--sidebar);flex:1;min-height:100vh;display:flex;flex-direction:column}
        .topbar{
            height:58px;background:var(--surf);border-bottom:1px solid var(--border);
            padding:0 1.8rem;display:flex;align-items:center;justify-content:space-between;
            position:sticky;top:0;z-index:100;
        }
        .topbar-title{font-family:var(--font-head);font-weight:700;font-size:1.1rem}
        .topbar-right{display:flex;align-items:center;gap:.8rem}
        .topbar-btn{
            display:inline-flex;align-items:center;gap:.4rem;
            padding:.45rem .9rem;border-radius:8px;font-size:.83rem;font-weight:500;
            font-family:var(--font-body);cursor:pointer;text-decoration:none;transition:all .15s;border:none;
        }
        .tb-primary{background:var(--accent);color:#0b0d11;font-weight:700}
        .tb-primary:hover{background:#ffd55e}
        .tb-outline{background:transparent;color:var(--text);border:1px solid var(--border)}
        .tb-outline:hover{border-color:var(--accent);color:var(--accent)}

        .page-body{padding:1.8rem;flex:1}

        /* ── SHARED COMPONENTS ── */
        .btn{display:inline-flex;align-items:center;gap:.4rem;padding:.55rem 1.1rem;border-radius:8px;border:none;font-family:var(--font-body);font-weight:500;font-size:.88rem;cursor:pointer;text-decoration:none;transition:all .15s}
        .btn-primary{background:var(--accent);color:#0b0d11;font-weight:700}
        .btn-primary:hover{background:#ffd55e}
        .btn-outline{background:transparent;color:var(--text);border:1px solid var(--border)}
        .btn-outline:hover{border-color:var(--accent);color:var(--accent)}
        .btn-danger{background:var(--danger);color:#fff}
        .btn-danger:hover{background:#dc2626}
        .btn-sm{padding:.35rem .75rem;font-size:.8rem}

        .card{background:var(--surf);border:1px solid var(--border);border-radius:var(--radius);padding:1.4rem}
        .card-sm{padding:.9rem 1.1rem}

        /* form controls */
        input[type=text],input[type=email],input[type=number],input[type=password],input[type=date],textarea,select{
            width:100%;background:var(--bg);border:1px solid var(--border);color:var(--text);
            border-radius:8px;padding:.65rem .9rem;font-family:var(--font-body);font-size:.9rem;
            outline:none;transition:border-color .18s,box-shadow .18s;appearance:none;
        }
        input:focus,textarea:focus,select:focus{border-color:var(--accent);box-shadow:0 0 0 3px rgba(240,192,64,.1)}
        select{background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' fill='none' stroke='%236b7280' stroke-width='2' viewBox='0 0 24 24'%3E%3Cpath d='M6 9l6 6 6-6'/%3E%3C/svg%3E");background-repeat:no-repeat;background-position:right .8rem center;padding-right:2.2rem}
        label{display:block;font-size:.78rem;font-weight:600;color:var(--muted);margin-bottom:.4rem;text-transform:uppercase;letter-spacing:.05em}
        .form-group{margin-bottom:1rem}
        .form-row{display:grid;grid-template-columns:1fr 1fr;gap:.9rem}
        .form-row-3{display:grid;grid-template-columns:1fr 1fr 1fr;gap:.9rem}
        .input-prefix{position:relative}
        .input-prefix .pfx{position:absolute;left:.9rem;top:50%;transform:translateY(-50%);color:var(--muted);font-size:.85rem;pointer-events:none}
        .input-prefix input{padding-left:2.8rem}
        .error-msg{color:#fca5a5;font-size:.78rem;margin-top:.3rem}

        /* flash */
        .flash{padding:.8rem 1.1rem;border-radius:var(--radius);margin-bottom:1.4rem;font-size:.88rem;display:flex;align-items:center;gap:.5rem}
        .flash-success{background:rgba(34,197,94,.1);border:1px solid rgba(34,197,94,.3);color:#86efac}
        .flash-error{background:rgba(239,68,68,.1);border:1px solid rgba(239,68,68,.3);color:#fca5a5}

        /* badges */
        .badge{display:inline-flex;align-items:center;padding:.2rem .6rem;border-radius:20px;font-size:.72rem;font-weight:700;letter-spacing:.03em}
        .badge-yellow{background:rgba(240,192,64,.12);color:var(--accent);border:1px solid rgba(240,192,64,.2)}
        .badge-blue{background:rgba(59,130,246,.12);color:#93c5fd;border:1px solid rgba(59,130,246,.2)}
        .badge-green{background:rgba(34,197,94,.12);color:#86efac;border:1px solid rgba(34,197,94,.2)}
        .badge-red{background:rgba(239,68,68,.12);color:#fca5a5;border:1px solid rgba(239,68,68,.2)}
        .badge-warn{background:rgba(245,158,11,.12);color:#fcd34d;border:1px solid rgba(245,158,11,.2)}

        /* table */
        .data-table{width:100%;border-collapse:collapse}
        .data-table th{text-align:left;font-size:.72rem;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:var(--muted);padding:.7rem 1rem;border-bottom:1px solid var(--border)}
        .data-table td{padding:.8rem 1rem;font-size:.88rem;border-bottom:1px solid rgba(255,255,255,.04)}
        .data-table tr:hover td{background:rgba(255,255,255,.02)}
        .data-table tr:last-child td{border-bottom:none}

        /* pagination */
        .pagination-wrap{margin-top:1.5rem;display:flex;justify-content:center}
        .pagination-wrap nav span,.pagination-wrap nav a{display:inline-flex;align-items:center;padding:.45rem .8rem;margin:0 .15rem;border-radius:7px;border:1px solid var(--border);color:var(--muted);font-size:.85rem;text-decoration:none;background:var(--surf);transition:all .15s}
        .pagination-wrap nav a:hover{border-color:var(--accent);color:var(--accent)}
        .pagination-wrap nav span[aria-current="page"]{background:var(--accent);color:#0b0d11;border-color:var(--accent);font-weight:700}

        @media(max-width:900px){
            .sidebar{transform:translateX(-100%)}
            .main-wrap{margin-left:0}
        }
    </style>
    @stack('styles')
</head>
<body>

{{-- ── SIDEBAR ── --}}
<aside class="sidebar">
    <a class="sb-brand" href="{{ route('reports.dashboard') }}">SOZO<span>POS</span></a>
    <nav class="sb-nav">

        <div class="sb-section">Main</div>
        <a href="{{ route('reports.dashboard') }}" class="sb-link {{ request()->routeIs('reports.dashboard') ? 'active' : '' }}">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
            Dashboard
        </a>
        <a href="{{ route('pos.terminal') }}" class="sb-link {{ request()->routeIs('pos.*') ? 'active' : '' }}">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="2" y="3" width="20" height="14" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
            POS Terminal
        </a>

        <div class="sb-section" style="margin-top:.5rem">Stock</div>
        <a href="{{ route('inventory.index') }}" class="sb-link {{ request()->routeIs('inventory.*') ? 'active' : '' }}">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/></svg>
            Inventory
            @php $alerts = \App\Models\InventoryItem::whereColumn('quantity','<=','low_stock_threshold')->count(); @endphp
            @if($alerts > 0)<span class="sb-badge">{{ $alerts }}</span>@endif
        </a>

        <div class="sb-section" style="margin-top:.5rem">People</div>
        <a href="{{ route('customers.index') }}" class="sb-link {{ request()->routeIs('customers.*') ? 'active' : '' }}">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            Customers
        </a>
        @if(auth()->user()?->isAdmin())
        <a href="{{ route('users.index') }}" class="sb-link {{ request()->routeIs('users.*') ? 'active' : '' }}">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="8" r="4"/><path d="M20 21a8 8 0 1 0-16 0"/></svg>
            Staff / Users
        </a>
        @endif

        <div class="sb-section" style="margin-top:.5rem">Reports</div>
        <a href="{{ route('reports.sales') }}" class="sb-link {{ request()->routeIs('reports.sales') ? 'active' : '' }}">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="12" y1="20" x2="12" y2="10"/><line x1="18" y1="20" x2="18" y2="4"/><line x1="6" y1="20" x2="6" y2="16"/></svg>
            Sales Report
        </a>
        <a href="{{ route('reports.inventory') }}" class="sb-link {{ request()->routeIs('reports.inventory') ? 'active' : '' }}">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
            Stock Report
        </a>
    </nav>

    <div class="sb-user">
        <div class="sb-avatar">{{ substr(auth()->user()?->name ?? 'U', 0, 2) }}</div>
        <div>
            <div class="sb-uname">{{ auth()->user()?->name ?? 'User' }}</div>
            <div class="sb-urole">{{ auth()->user()?->role ?? '' }}</div>
        </div>
        <form action="{{ route('logout') }}" method="POST" style="margin-left:auto">
            @csrf
            <button type="submit" class="sb-logout" title="Logout">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
            </button>
        </form>
    </div>
</aside>

{{-- ── MAIN ── --}}
<div class="main-wrap">
    <div class="topbar">
        <span class="topbar-title">@yield('page-title', 'Dashboard')</span>
        <div class="topbar-right">@yield('topbar-actions')</div>
    </div>

    <div class="page-body">
        @if(session('success'))
            <div class="flash flash-success">
                <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="flash flash-error">
                <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                {{ session('error') }}
            </div>
        @endif
        @yield('content')
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@stack('scripts')
</body>
</html>
