<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'SOZO Store') — Premium Shopping</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,500;0,700;1,500&family=Outfit:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --cream:    #faf8f4;
            --warm:     #f3efe8;
            --border:   #e8e2d9;
            --gold:     #c9973a;
            --gold-lt:  #e8c56a;
            --charcoal: #1c1a17;
            --dark:     #2e2b26;
            --mid:      #6b6560;
            --light:    #9e9890;
            --white:    #ffffff;
            --danger:   #c0392b;
            --success:  #2d7a4f;
            --r:        10px;
            --font-serif: 'Playfair Display', Georgia, serif;
            --font-sans:  'Outfit', sans-serif;
        }

        body { background: var(--cream); color: var(--charcoal); font-family: var(--font-sans); line-height: 1.6; }

        /* ── ANNOUNCEMENT BAR ── */
        .ann-bar {
            background: var(--charcoal);
            color: rgba(255,255,255,.85);
            text-align: center;
            padding: .5rem 1rem;
            font-size: .78rem;
            letter-spacing: .05em;
            font-weight: 500;
        }
        .ann-bar strong { color: var(--gold-lt); }

        /* ── HEADER ── */
        header {
            background: var(--white);
            border-bottom: 1px solid var(--border);
            position: sticky; top: 0; z-index: 200;
        }
        .header-inner {
            max-width: 1300px; margin: 0 auto;
            padding: 0 2rem;
            display: flex; align-items: center;
            height: 68px; gap: 2rem;
        }
        .site-logo {
            font-family: var(--font-serif);
            font-weight: 700;
            font-size: 1.5rem;
            color: var(--charcoal);
            text-decoration: none;
            letter-spacing: -.3px;
            white-space: nowrap;
        }
        .site-logo span { color: var(--gold); }

        /* search */
        .header-search {
            flex: 1; max-width: 480px;
            position: relative;
        }
        .header-search input {
            width: 100%;
            background: var(--warm);
            border: 1px solid var(--border);
            border-radius: 8px;
            padding: .6rem 1rem .6rem 2.6rem;
            font-family: var(--font-sans);
            font-size: .9rem;
            color: var(--charcoal);
            outline: none;
            transition: border-color .18s, box-shadow .18s;
        }
        .header-search input:focus { border-color: var(--gold); box-shadow: 0 0 0 3px rgba(201,151,58,.12); }
        .header-search input::placeholder { color: var(--light); }
        .header-search-icon {
            position: absolute; left: .8rem; top: 50%; transform: translateY(-50%);
            color: var(--light); pointer-events: none;
        }

        /* header right */
        .header-right { margin-left: auto; display: flex; align-items: center; gap: .5rem; }
        .hdr-btn {
            display: flex; align-items: center; gap: .4rem;
            padding: .5rem .9rem; border-radius: 8px;
            background: none; border: 1px solid transparent;
            color: var(--dark); font-family: var(--font-sans);
            font-size: .85rem; font-weight: 500; cursor: pointer;
            text-decoration: none; transition: all .15s; white-space: nowrap;
        }
        .hdr-btn:hover { background: var(--warm); border-color: var(--border); }
        .hdr-btn.cart-btn {
            background: var(--charcoal); color: var(--white);
            border-color: var(--charcoal); font-weight: 600;
        }
        .hdr-btn.cart-btn:hover { background: var(--dark); border-color: var(--dark); }
        .cart-pill {
            background: var(--gold); color: var(--charcoal);
            font-size: .68rem; font-weight: 700;
            width: 18px; height: 18px; border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            line-height: 1;
        }

        /* ── NAV ── */
        .site-nav {
            background: var(--white);
            border-bottom: 1px solid var(--border);
        }
        .nav-inner {
            max-width: 1300px; margin: 0 auto;
            padding: 0 2rem;
            display: flex; gap: 0; align-items: center;
        }
        .nav-link {
            padding: .75rem 1.1rem;
            font-size: .88rem; font-weight: 500;
            color: var(--mid);
            text-decoration: none;
            border-bottom: 2px solid transparent;
            transition: all .15s; white-space: nowrap;
        }
        .nav-link:hover { color: var(--charcoal); border-bottom-color: var(--gold); }
        .nav-link.active { color: var(--charcoal); border-bottom-color: var(--gold); font-weight: 600; }

        /* ── PAGE BODY ── */
        .page-body { min-height: 60vh; }

        /* ── FOOTER ── */
        footer {
            background: var(--charcoal); color: rgba(255,255,255,.65);
            padding: 3rem 2rem 2rem; margin-top: 4rem;
        }
        .footer-inner { max-width: 1300px; margin: 0 auto; display: grid; grid-template-columns: 2fr 1fr 1fr 1fr; gap: 2rem; margin-bottom: 2rem; }
        .footer-brand .logo { font-family: var(--font-serif); font-size: 1.3rem; color: var(--white); margin-bottom: .6rem; }
        .footer-brand .logo span { color: var(--gold-lt); }
        .footer-brand p { font-size: .82rem; line-height: 1.7; }
        .footer-col h4 { font-size: .82rem; font-weight: 600; text-transform: uppercase; letter-spacing: .08em; color: rgba(255,255,255,.5); margin-bottom: .8rem; }
        .footer-col a { display: block; font-size: .85rem; color: rgba(255,255,255,.6); text-decoration: none; margin-bottom: .4rem; transition: color .15s; }
        .footer-col a:hover { color: var(--gold-lt); }
        .footer-bottom { border-top: 1px solid rgba(255,255,255,.08); padding-top: 1.5rem; display: flex; justify-content: space-between; align-items: center; font-size: .78rem; gap: 1rem; flex-wrap: wrap; }
        .footer-bottom a { color: rgba(255,255,255,.4); text-decoration: none; }
        .footer-bottom a:hover { color: var(--gold-lt); }

        /* ── SHARED UTILS ── */
        .btn { display: inline-flex; align-items: center; gap: .45rem; padding: .65rem 1.4rem; border-radius: 8px; font-family: var(--font-sans); font-weight: 600; font-size: .9rem; cursor: pointer; text-decoration: none; transition: all .18s; border: none; }
        .btn-gold { background: var(--gold); color: var(--white); }
        .btn-gold:hover { background: #b8882f; }
        .btn-dark { background: var(--charcoal); color: var(--white); }
        .btn-dark:hover { background: var(--dark); }
        .btn-outline { background: transparent; color: var(--charcoal); border: 1.5px solid var(--border); }
        .btn-outline:hover { border-color: var(--gold); color: var(--gold); }
        .btn-sm { padding: .45rem .9rem; font-size: .82rem; }
        .btn-lg { padding: .85rem 2rem; font-size: 1rem; }

        .badge { display: inline-flex; align-items: center; padding: .2rem .65rem; border-radius: 20px; font-size: .72rem; font-weight: 600; }
        .badge-gold { background: rgba(201,151,58,.12); color: var(--gold); border: 1px solid rgba(201,151,58,.25); }
        .badge-green { background: rgba(45,122,79,.1); color: var(--success); border: 1px solid rgba(45,122,79,.2); }
        .badge-red { background: rgba(192,57,43,.1); color: var(--danger); border: 1px solid rgba(192,57,43,.2); }

        .container { max-width: 1300px; margin: 0 auto; padding: 0 2rem; }
        .section { padding: 3.5rem 0; }

        /* flash */
        .flash { padding: .85rem 1.2rem; border-radius: 10px; margin-bottom: 1.5rem; font-size: .9rem; display: flex; align-items: center; gap: .5rem; }
        .flash-success { background: rgba(45,122,79,.08); border: 1px solid rgba(45,122,79,.2); color: var(--success); }
        .flash-error   { background: rgba(192,57,43,.08); border: 1px solid rgba(192,57,43,.2); color: var(--danger); }
        .flash-info    { background: rgba(201,151,58,.08); border: 1px solid rgba(201,151,58,.2); color: var(--gold); }

        @media (max-width: 900px) {
            .header-search { display: none; }
            .footer-inner { grid-template-columns: 1fr 1fr; }
        }
        @media (max-width: 600px) {
            .header-inner { padding: 0 1rem; }
            .footer-inner { grid-template-columns: 1fr; }
            .container { padding: 0 1rem; }
        }
    </style>
    @stack('styles')
</head>
<body>

<div class="ann-bar">
    Free delivery on orders over <strong>UGX 100,000</strong> · Kampala same-day pickup available
</div>

<header>
    <div class="header-inner">
        <a class="site-logo" href="{{ route('shop.index') }}">SOZO<span>.</span>store</a>

        <div class="header-search">
            <form action="{{ route('shop.catalog') }}" method="GET">
                <svg class="header-search-icon" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                <input type="text" name="q" placeholder="Search products…" value="{{ request('q') }}">
            </form>
        </div>

        <div class="header-right">
            @auth
                @if(auth()->user()->role ?? false)
                    {{-- POS staff --}}
                    <a href="{{ route('reports.dashboard') }}" class="hdr-btn">
                        <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
                        Dashboard
                    </a>
                @else
                    <a href="{{ route('account.orders') }}" class="hdr-btn">My Orders</a>
                @endif
                <form action="{{ route('logout') }}" method="POST" style="display:inline">
                    @csrf
                    <button class="hdr-btn">Sign Out</button>
                </form>
            @else
                <a href="{{ route('login') }}" class="hdr-btn">Sign In</a>
            @endauth

            <a href="{{ route('shop.cart') }}" class="hdr-btn cart-btn">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
                Cart
                <span class="cart-pill" id="cartCount">{{ array_sum(session('cart', [])) }}</span>
            </a>
        </div>
    </div>
</header>

<nav class="site-nav">
    <div class="nav-inner">
        <a href="{{ route('shop.index') }}" class="nav-link {{ request()->routeIs('shop.index') ? 'active' : '' }}">Home</a>
        <a href="{{ route('shop.catalog') }}" class="nav-link {{ request()->routeIs('shop.catalog') ? 'active' : '' }}">All Products</a>
        @php
            $cats = \App\Models\InventoryItem::where('published',true)->where('quantity','>',0)->select('category')->distinct()->pluck('category');
        @endphp
        @foreach($cats->take(5) as $cat)
        <a href="{{ route('shop.catalog', ['category' => $cat]) }}" class="nav-link {{ request('category') === $cat ? 'active' : '' }}">
            {{ \App\Http\Controllers\InventoryController::CATEGORIES[$cat] ?? ucfirst($cat) }}
        </a>
        @endforeach
    </div>
</nav>

<div class="page-body">
    @if(session('success') || session('cart_success'))
    <div class="container" style="padding-top:1.2rem">
        <div class="flash flash-success">
            <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
            {{ session('success') ?? session('cart_success') }}
        </div>
    </div>
    @endif
    @if(session('error'))
    <div class="container" style="padding-top:1.2rem">
        <div class="flash flash-error">{{ session('error') }}</div>
    </div>
    @endif

    @yield('content')
</div>

<footer>
    <div class="footer-inner">
        <div class="footer-brand">
            <div class="logo">SOZO<span>.</span>store</div>
            <p>Premium products delivered to your door in Kampala. Quality you can trust, prices you'll love.</p>
        </div>
        <div class="footer-col">
            <h4>Shop</h4>
            <a href="{{ route('shop.catalog') }}">All Products</a>
            @foreach(($cats ?? collect())->take(4) as $cat)
            <a href="{{ route('shop.catalog', ['category'=>$cat]) }}">{{ \App\Http\Controllers\InventoryController::CATEGORIES[$cat] ?? ucfirst($cat) }}</a>
            @endforeach
        </div>
        <div class="footer-col">
            <h4>Account</h4>
            <a href="{{ route('login') }}">Sign In</a>
            <a href="{{ route('register') }}">Create Account</a>
            <a href="{{ route('account.orders') }}">My Orders</a>
        </div>
        <div class="footer-col">
            <h4>Help</h4>
            <a href="#">Delivery Info</a>
            <a href="#">Returns Policy</a>
            <a href="#">Contact Us</a>
        </div>
    </div>
    <div class="container">
        <div class="footer-bottom">
            <span>© {{ date('Y') }} SOZO Store. All rights reserved.</span>
            <div style="display:flex;gap:1rem"><a href="#">Privacy</a><a href="#">Terms</a></div>
        </div>
    </div>
</footer>

@stack('scripts')
</body>
</html>