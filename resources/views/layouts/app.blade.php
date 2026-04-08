<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>@yield('title', 'SOZO POS') — Inventory</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">

    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --bg:        #0d0f14;
            --surface:   #161920;
            --surface2:  #1e2230;
            --border:    #2a2f42;
            --accent:    #f0c040;
            --accent2:   #3b82f6;
            --success:   #22c55e;
            --danger:    #ef4444;
            --text:      #eef0f6;
            --muted:     #6b7280;
            --radius:    12px;
            --font-head: 'Syne', sans-serif;
            --font-body: 'DM Sans', sans-serif;
        }

        body {
            background: var(--bg);
            color: var(--text);
            font-family: var(--font-body);
            min-height: 100vh;
        }

        /* ── NAV ── */
        nav {
            background: var(--surface);
            border-bottom: 1px solid var(--border);
            padding: 0 2rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            height: 64px;
            position: sticky;
            top: 0;
            z-index: 100;
        }
        .nav-brand {
            font-family: var(--font-head);
            font-weight: 800;
            font-size: 1.3rem;
            color: var(--accent);
            letter-spacing: -.5px;
            text-decoration: none;
        }
        .nav-brand span { color: var(--text); }
        .nav-links { display: flex; gap: 1.5rem; align-items: center; }
        .nav-links a {
            color: var(--muted);
            text-decoration: none;
            font-size: .9rem;
            font-weight: 500;
            transition: color .2s;
        }
        .nav-links a:hover, .nav-links a.active { color: var(--text); }
        .btn {
            display: inline-flex;
            align-items: center;
            gap: .45rem;
            padding: .55rem 1.2rem;
            border-radius: 8px;
            border: none;
            font-family: var(--font-body);
            font-weight: 500;
            font-size: .9rem;
            cursor: pointer;
            text-decoration: none;
            transition: all .18s;
        }
        .btn-primary  { background: var(--accent);  color: #0d0f14; }
        .btn-primary:hover  { background: #ffd55e; }
        .btn-outline  { background: transparent; color: var(--text); border: 1px solid var(--border); }
        .btn-outline:hover  { border-color: var(--accent); color: var(--accent); }
        .btn-danger   { background: var(--danger); color: #fff; }
        .btn-danger:hover   { background: #dc2626; }
        .btn-sm { padding: .35rem .8rem; font-size: .8rem; }

        /* ── LAYOUT ── */
        main { max-width: 1280px; margin: 0 auto; padding: 2rem; }

        /* ── FLASH ── */
        .flash {
            padding: .85rem 1.2rem;
            border-radius: var(--radius);
            margin-bottom: 1.5rem;
            font-size: .9rem;
            display: flex;
            align-items: center;
            gap: .6rem;
        }
        .flash-success { background: rgba(34,197,94,.12); border: 1px solid rgba(34,197,94,.3); color: #86efac; }
        .flash-error   { background: rgba(239,68,68,.12);  border: 1px solid rgba(239,68,68,.3);  color: #fca5a5; }
    </style>

    @stack('styles')
</head>
<body>

<nav>
    <a class="nav-brand" href="{{ route('inventory.index') }}">SOZO<span>POS</span></a>
    <div class="nav-links">
        <a href="{{ route('inventory.index') }}" class="{{ request()->routeIs('inventory.index') ? 'active' : '' }}"></a>
        {{-- <a href="{{ route('inventory.create') }}" class="{{ request()->routeIs('inventory.create') ? 'active' : '' }}">Add Item</a> --}}
        <a href="{{ route('inventory.create') }}" class="btn btn-primary btn-sm">
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></svg>
            New Item
        </a>
    </div>
</nav>

<main>
    @if(session('success'))
        <div class="flash flash-success">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="flash flash-error">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            {{ session('error') }}
        </div>
    @endif

    @yield('content')
</main>

@stack('scripts')
</body>
</html>
