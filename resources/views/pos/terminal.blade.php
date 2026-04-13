@extends('layouts.app')
@section('page-title', 'POS Terminal')

@push('styles')
<style>
.pos-wrap{display:grid;grid-template-columns:1fr 420px;gap:1rem;height:calc(100vh - 96px);min-height:0}
.pos-left{display:flex;flex-direction:column;gap:.7rem;min-height:0;overflow:hidden}
.search-row{display:flex;gap:.6rem;align-items:center;flex-shrink:0}
.search-wrap{position:relative;flex:1}
.search-wrap input{width:100%;background:var(--surf);border:1px solid var(--border);color:var(--text);border-radius:8px;padding:.7rem 1rem .7rem 2.7rem;font-family:var(--font-body);font-size:.92rem;outline:none;transition:border-color .18s}
.search-wrap input:focus{border-color:var(--accent)}
.search-icon{position:absolute;left:.85rem;top:50%;transform:translateY(-50%);color:var(--muted);pointer-events:none}
.barcode-btn{flex-shrink:0;background:var(--surf2);border:1px solid var(--border);color:var(--muted);padding:.65rem .85rem;border-radius:8px;cursor:pointer;transition:all .15s}
.barcode-btn:hover{border-color:var(--accent);color:var(--accent)}
.filter-wrapper{flex-shrink:0}
.filter-toggle{background:var(--surf);border:1px solid var(--border);border-radius:10px;padding:.7rem .9rem;cursor:pointer;display:flex;align-items:center;justify-content:space-between;transition:all .15s;user-select:none}
.filter-toggle:hover{border-color:var(--accent);background:var(--surf2)}
.filter-toggle-left{display:flex;align-items:center;gap:.6rem}
.filter-toggle-left span{font-size:.85rem;font-weight:600;color:var(--text)}
.filter-badge{background:var(--accent);color:#0b0d11;border-radius:12px;padding:.15rem .6rem;font-size:.7rem;font-weight:700;display:none}
.filter-toggle-icon{transition:transform .2s}
.filter-toggle-icon.rotated{transform:rotate(180deg)}
.filter-bar{background:var(--surf);border:1px solid var(--border);border-radius:10px;padding:.8rem .9rem;margin-top:.5rem;display:flex;gap:.7rem;align-items:center;flex-wrap:wrap}
.filter-bar.collapsed{display:none}
.filter-bar label{font-size:.7rem;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--muted);margin-bottom:0;white-space:nowrap}
.filter-select{background:var(--bg);border:1px solid var(--border);color:var(--text);border-radius:7px;padding:.42rem .7rem;font-size:.82rem;font-family:var(--font-body);outline:none;cursor:pointer;appearance:none;background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='10' fill='none' stroke='%236b7280' stroke-width='2' viewBox='0 0 24 24'%3E%3Cpath d='M6 9l6 6 6-6'/%3E%3C/svg%3E");background-repeat:no-repeat;background-position:right .6rem center;padding-right:1.8rem;transition:border-color .15s}
.filter-select:focus{border-color:var(--accent)}
.filter-input{background:var(--bg);border:1px solid var(--border);color:var(--text);border-radius:7px;padding:.42rem .65rem;font-size:.82rem;font-family:var(--font-body);outline:none;width:90px;transition:border-color .15s}
.filter-input:focus{border-color:var(--accent)}
.filter-input::placeholder{color:var(--muted)}
.filter-divider{width:1px;height:20px;background:var(--border);flex-shrink:0}
.filter-clear-btn{background:none;border:none;color:var(--muted);cursor:pointer;font-size:.75rem;font-family:var(--font-body);display:flex;align-items:center;gap:.25rem;padding:.3rem .5rem;border-radius:5px;transition:color .13s;white-space:nowrap;flex-shrink:0}
.filter-clear-btn:hover{color:var(--danger)}
.filter-count{margin-left:auto;font-size:.75rem;color:var(--muted);white-space:nowrap;flex-shrink:0}
.subcat-group{display:none;align-items:center;gap:.5rem}
.subcat-group.visible{display:flex}
.products-scroll{flex:1;overflow-y:auto;min-height:0;padding-right:2px}
.products-scroll::-webkit-scrollbar{width:4px}
.products-scroll::-webkit-scrollbar-thumb{background:var(--border);border-radius:4px}
.products-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:.8rem;padding-bottom:.5rem}
@media(max-width:1100px){.products-grid{grid-template-columns:repeat(2,1fr)}}
.prod-card{background:var(--surf);border:1px solid var(--border);border-radius:10px;cursor:pointer;transition:all .18s;overflow:hidden;user-select:none}
.prod-card:hover{border-color:var(--accent);transform:translateY(-2px);box-shadow:0 4px 16px rgba(0,0,0,.25)}
.prod-card:active{transform:translateY(0);box-shadow:none}
.prod-card.out-of-stock{opacity:.4;cursor:not-allowed;pointer-events:none}
.prod-card.in-cart{border-color:rgba(240,192,64,.5);background:rgba(240,192,64,.04)}
.prod-img{width:100%;aspect-ratio:4/3;background:var(--surf2);display:flex;align-items:center;justify-content:center;overflow:hidden;position:relative}
.prod-img img{width:100%;height:100%;object-fit:cover;display:block;transition:transform .3s}
.prod-card:hover .prod-img img{transform:scale(1.05)}
.prod-img svg{color:var(--border);opacity:.5}
.prod-ribbon{position:absolute;top:.4rem;left:.4rem;background:var(--danger);color:#fff;font-size:.62rem;font-weight:700;padding:.15rem .5rem;border-radius:4px;text-transform:uppercase;letter-spacing:.04em}
.prod-ribbon.low{background:var(--warn);color:#0b0d11}
.prod-cart-badge{position:absolute;top:.4rem;right:.4rem;background:var(--accent);color:#0b0d11;font-size:.68rem;font-weight:800;width:20px;height:20px;border-radius:50%;display:none;align-items:center;justify-content:center}
.prod-card.in-cart .prod-cart-badge{display:flex}
.prod-body{padding:.55rem .7rem .65rem}
.prod-name{font-size:.8rem;font-weight:600;line-height:1.25;margin-bottom:.25rem;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.prod-price{font-family:var(--font-head);font-size:.92rem;font-weight:700;color:var(--accent)}
.prod-meta{display:flex;align-items:center;justify-content:space-between;margin-top:.2rem}
.prod-qty{font-size:.68rem;color:var(--muted)}
.prod-qty.low{color:var(--warn)}
.prod-qty.out{color:var(--danger)}
.prod-category{font-size:.65rem;color:var(--muted);background:var(--surf2);border-radius:3px;padding:.08rem .35rem}
.grid-empty{grid-column:1/-1;text-align:center;padding:3rem 2rem;color:var(--muted)}
.grid-empty svg{display:block;margin:0 auto .8rem;opacity:.3}
.pos-right{display:flex;flex-direction:column;background:var(--surf);border:1px solid var(--border);border-radius:var(--radius);overflow:hidden;min-height:0}
.cart-header{padding:.8rem 1rem;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;flex-shrink:0}
.cart-header h3{font-family:var(--font-head);font-weight:700;font-size:1rem}
.cart-clear{background:none;border:none;color:var(--muted);cursor:pointer;font-size:.78rem;display:flex;align-items:center;gap:.3rem;transition:color .13s}
.cart-clear:hover{color:var(--danger)}
.cart-customer{padding:.6rem .9rem;border-bottom:1px solid var(--border);flex-shrink:0}
.cart-customer select{width:100%;background:var(--bg);border:1px solid var(--border);color:var(--text);border-radius:7px;padding:.5rem .8rem;font-family:var(--font-body);font-size:.84rem;outline:none;appearance:none;background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='10' fill='none' stroke='%236b7280' stroke-width='2' viewBox='0 0 24 24'%3E%3Cpath d='M6 9l6 6 6-6'/%3E%3C/svg%3E");background-repeat:no-repeat;background-position:right .7rem center;padding-right:2rem;transition:border-color .15s}
.cart-customer select:focus{outline:none;border-color:var(--accent)}
.loyalty-badge{margin-top:.35rem;font-size:.72rem;color:var(--muted);display:none;align-items:center;gap:.35rem}
.loyalty-badge .pts{color:var(--accent);font-weight:700}
.loyalty-badge .tier{text-transform:capitalize;background:var(--surf2);border-radius:10px;padding:.1rem .45rem;font-size:.68rem}
.cart-items{flex:1;overflow-y:auto;min-height:0;padding:.4rem .5rem}
.cart-items::-webkit-scrollbar{width:4px}
.cart-items::-webkit-scrollbar-thumb{background:var(--border);border-radius:4px}
.cart-empty{display:flex;flex-direction:column;align-items:center;justify-content:center;height:100%;gap:.5rem;color:var(--muted);font-size:.85rem;min-height:120px}
.cart-item{display:flex;align-items:center;gap:.5rem;padding:.55rem .5rem;border-radius:8px;transition:background .13s;border-bottom:1px solid rgba(255,255,255,.04)}
.cart-item:last-child{border-bottom:none}
.cart-item:hover{background:var(--surf2)}
.ci-thumb{width:34px;height:34px;border-radius:6px;background:var(--surf2);overflow:hidden;flex-shrink:0;display:flex;align-items:center;justify-content:center}
.ci-thumb img{width:100%;height:100%;object-fit:cover;display:block}
.ci-info{flex:1;min-width:0}
.ci-name{font-size:.82rem;font-weight:600;line-height:1.2;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.ci-price{font-size:.72rem;color:var(--muted);margin-top:.1rem}
.ci-qty{display:flex;align-items:center;gap:.25rem;flex-shrink:0}
.ci-qty button{width:22px;height:22px;border-radius:5px;border:1px solid var(--border);background:var(--surf2);color:var(--text);cursor:pointer;font-size:.88rem;display:flex;align-items:center;justify-content:center;transition:all .13s}
.ci-qty button:hover{border-color:var(--accent);color:var(--accent)}
.ci-qty span{font-family:var(--font-head);font-size:.88rem;font-weight:700;min-width:22px;text-align:center}
.ci-total{font-family:var(--font-head);font-weight:700;font-size:.85rem;min-width:68px;text-align:right;flex-shrink:0}
.ci-remove{color:var(--muted);cursor:pointer;background:none;border:none;padding:4px;transition:color .13s;flex-shrink:0;border-radius:4px}
.ci-remove:hover{color:var(--danger);background:rgba(239,68,68,.1)}
.cart-footer{border-top:1px solid var(--border);padding:.8rem;flex-shrink:0}
.totals-grid{display:grid;grid-template-columns:1fr auto;gap:.2rem .4rem;font-size:.82rem;margin-bottom:.65rem}
.totals-grid .lbl{color:var(--muted)}
.totals-grid .val{text-align:right;font-weight:500}
.totals-grid .grand-lbl{font-family:var(--font-head);font-weight:700;font-size:.95rem;border-top:1px solid var(--border);padding-top:.35rem;margin-top:.15rem}
.totals-grid .grand-val{font-family:var(--font-head);font-weight:800;font-size:1.15rem;color:var(--accent);border-top:1px solid var(--border);padding-top:.35rem;margin-top:.15rem;text-align:right}
.discount-row{display:flex;gap:.5rem;align-items:center;margin-bottom:.6rem}
.discount-row label{margin-bottom:0;font-size:.74rem;white-space:nowrap;color:var(--muted);text-transform:none;letter-spacing:0}
.discount-row input{flex:1;max-width:110px;background:var(--bg);border:1px solid var(--border);color:var(--text);border-radius:7px;padding:.45rem .7rem;font-size:.85rem;font-family:var(--font-body);outline:none}
.discount-row input:focus{border-color:var(--accent)}
.pay-methods{display:grid;grid-template-columns:repeat(4,1fr);gap:.35rem;margin-bottom:.65rem}
.pay-btn{padding:.45rem .2rem;border-radius:7px;border:1px solid var(--border);background:var(--surf2);color:var(--muted);font-size:.7rem;font-weight:600;cursor:pointer;transition:all .13s;text-align:center;display:flex;flex-direction:column;align-items:center;gap:.2rem}
.pay-btn.active{background:rgba(240,192,64,.12);border-color:var(--accent);color:var(--accent)}
.pay-btn svg{opacity:.6}.pay-btn.active svg{opacity:1}
.paid-row{display:flex;gap:.5rem;align-items:center;margin-bottom:.5rem}
.paid-row label{margin-bottom:0;white-space:nowrap;font-size:.74rem;text-transform:none;letter-spacing:0;color:var(--muted)}
.paid-row input{flex:1;background:var(--bg);border:1px solid var(--border);color:var(--text);border-radius:7px;padding:.45rem .7rem;font-family:var(--font-head);font-size:.9rem;font-weight:700;outline:none}
.paid-row input:focus{border-color:var(--accent)}
.change-display{background:var(--surf2);border:1px solid var(--border);border-radius:7px;padding:.5rem .8rem;display:flex;justify-content:space-between;align-items:center;margin-bottom:.6rem;font-size:.82rem}
.change-display .cv{font-family:var(--font-head);font-weight:800;font-size:1rem;color:var(--success)}
#refInput{margin-bottom:.5rem}
#refInput input{width:100%;background:var(--bg);border:1px solid var(--border);color:var(--text);border-radius:7px;padding:.45rem .7rem;font-size:.82rem;font-family:var(--font-body);outline:none}
.charge-btn{width:100%;background:var(--accent);color:#0b0d11;border:none;border-radius:9px;padding:.8rem;font-family:var(--font-head);font-size:.95rem;font-weight:800;cursor:pointer;transition:background .15s;display:flex;align-items:center;justify-content:center;gap:.45rem}
.charge-btn:hover{background:#ffd55e}
.charge-btn:disabled{opacity:.4;cursor:not-allowed}
.modal-backdrop{position:fixed;inset:0;background:rgba(0,0,0,.7);z-index:500;display:none;align-items:center;justify-content:center}
.modal-backdrop.open{display:flex}
.receipt-modal{background:var(--surf);border:1px solid var(--border);border-radius:14px;width:420px;max-height:90vh;overflow-y:auto;padding:1.4rem}
.receipt-header{text-align:center;margin-bottom:1rem;padding-bottom:.9rem;border-bottom:1px solid var(--border)}
.receipt-header h2{font-family:var(--font-head);font-weight:800;font-size:1.3rem;color:var(--accent)}
.receipt-header p{color:var(--muted);font-size:.83rem;margin-top:.2rem}
.receipt-line{display:flex;justify-content:space-between;padding:.28rem 0;font-size:.84rem;border-bottom:1px dashed var(--border)}
.receipt-line:last-child{border-bottom:none}
.receipt-line.total{font-weight:700;font-size:.92rem;color:var(--accent);border-top:1px solid var(--border);padding-top:.45rem;margin-top:.2rem}
.receipt-line.change-line{font-weight:700;color:var(--success)}
.receipt-actions{display:flex;gap:.6rem;margin-top:1.1rem}
.receipt-actions button,.receipt-actions a{flex:1;text-align:center}
</style>
@endpush

@section('content')
<div class="pos-wrap">
  <div class="pos-left">
    <div class="search-row">
      <div class="search-wrap">
        <svg class="search-icon" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
        <input type="text" id="productSearch" placeholder="Search product, scan barcode or SKU…" autocomplete="off">
      </div>
      <button class="barcode-btn" id="barcodeFocus" title="Click then scan barcode">
        <svg width="17" height="17" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path d="M3 5h2M7 5h2M3 5v4M21 5h-2M17 5h-2M21 5v4M3 19h2M7 19h2M3 19v-4M21 19h-2M17 19h-2M21 19v-4"/><line x1="7" y1="8" x2="7" y2="16"/><line x1="10" y1="8" x2="10" y2="16"/><line x1="13" y1="8" x2="13" y2="16"/><line x1="16" y1="8" x2="16" y2="12"/></svg>
      </button>
    </div>

    <div class="filter-wrapper">
      <div class="filter-toggle" onclick="toggleFilters()">
        <div class="filter-toggle-left">
          <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polygon points="22 3 2 3 10 13 10 21 14 18 14 13 22 3"/></svg>
          <span>Filters</span>
          <span class="filter-badge" id="activeFilterCount">0</span>
        </div>
        <svg class="filter-toggle-icon" id="filterToggleIcon" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="6 9 12 15 18 9"/></svg>
      </div>

      <div class="filter-bar" id="filterBar">
        <label>Category</label>
        <select class="filter-select" id="filterCategory" onchange="onCategoryChange()">
          <option value="">All</option>
          @foreach($categories as $cat)
          <option value="{{ $cat->code }}">{{ $cat->display_name }}</option>
          @endforeach
        </select>

        <div class="subcat-group" id="subcatGroup">
          <label>Sub</label>
          <select class="filter-select" id="filterSubcategory" onchange="applyFilters()">
            <option value="">All</option>
          </select>
        </div>

        <div class="filter-divider"></div>

        <label>Batch</label>
        <select class="filter-select" id="filterBatch" onchange="applyFilters()">
          <option value="">All</option>
          @foreach($batches as $batch)
          <option value="{{ $batch->code }}">{{ $batch->batch_number }}</option>
          @endforeach
        </select>

        <div class="filter-divider"></div>

        <label>Price</label>
        <input type="number" class="filter-input" id="filterMinPrice" placeholder="Min" min="0" oninput="applyFilters()">
        <span style="color:var(--muted);font-size:.8rem;flex-shrink:0">–</span>
        <input type="number" class="filter-input" id="filterMaxPrice" placeholder="Max" min="0" oninput="applyFilters()">

        <div class="filter-divider"></div>

        <select class="filter-select" id="filterStock" onchange="applyFilters()" style="min-width:90px">
          <option value="">In Stock</option>
          <option value="low">Low Stock</option>
          <option value="all">Show All</option>
        </select>

        <button class="filter-clear-btn" onclick="clearFilters()">
          <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
          Clear
        </button>
        <span class="filter-count" id="filterCount"></span>
      </div>
    </div>

    <div class="products-scroll">
      <div class="products-grid" id="productsGrid">
        @for($i=0;$i<9;$i++)
        <div style="background:var(--surf);border:1px solid var(--border);border-radius:10px;overflow:hidden;opacity:.5">
          <div style="width:100%;aspect-ratio:4/3;background:var(--surf2)"></div>
          <div style="padding:.6rem .7rem">
            <div style="height:11px;background:var(--surf2);border-radius:4px;margin-bottom:.4rem"></div>
            <div style="height:14px;background:var(--surf2);border-radius:4px;width:60%"></div>
          </div>
        </div>
        @endfor
      </div>
    </div>
  </div>

  <div class="pos-right">
    <div class="cart-header">
      <h3>Cart <span id="cartCount" style="color:var(--muted);font-size:.82rem;font-weight:400;margin-left:.25rem"></span></h3>
      <button class="cart-clear" onclick="clearCart()">
        <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/></svg>
        Clear All
      </button>
    </div>

    <div class="cart-customer">
      <select id="customerSelect" onchange="updateCustomer(this.value)">
        <option value="">— Walk-in customer —</option>
        @foreach($customers as $c)
        <option value="{{ $c->id }}" data-pts="{{ $c->loyalty_points }}" data-tier="{{ $c->loyalty_tier }}">
          {{ $c->name }}{{ $c->phone ? ' ('.$c->phone.')' : '' }}
        </option>
        @endforeach
      </select>
      <div class="loyalty-badge" id="loyaltyInfo">
        <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
        <span class="pts" id="loyaltyPts"></span> pts
        <span class="tier" id="loyaltyTier"></span>
      </div>
    </div>

    <div id="cartItems" class="cart-items">
      <div class="cart-empty" id="cartEmpty">
        <svg width="32" height="32" fill="none" stroke="currentColor" stroke-width="1.3" viewBox="0 0 24 24"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
        <span>Cart is empty</span>
      </div>
    </div>

    <div class="cart-footer">
      <div class="discount-row">
        <label>Discount (UGX)</label>
        <input type="number" id="discountInput" placeholder="0" min="0" oninput="recalc()">
      </div>
      <div class="totals-grid">
        <span class="lbl">Subtotal</span><span class="val" id="subtotalVal">UGX 0</span>
        <span class="lbl">Tax</span><span class="val" id="taxVal">UGX 0</span>
        <span class="lbl">Discount</span><span class="val" id="discountVal">UGX 0</span>
        <span class="grand-lbl">TOTAL</span><span class="grand-val" id="totalVal">UGX 0</span>
      </div>
      <div class="pay-methods">
        <button class="pay-btn active" data-method="cash" onclick="setPayMethod(this)"><svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>Cash</button>
        <button class="pay-btn" data-method="card" onclick="setPayMethod(this)"><svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="2" y="5" width="20" height="14" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/></svg>Card</button>
        <button class="pay-btn" data-method="mobile_money" onclick="setPayMethod(this)"><svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="5" y="2" width="14" height="20" rx="2"/><line x1="12" y1="18" x2="12.01" y2="18"/></svg>Mobile</button>
        <button class="pay-btn" data-method="split" onclick="setPayMethod(this)"><svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M16 3h5v5M8 21H3v-5M21 3l-7 7M3 21l7-7"/></svg>Split</button>
      </div>
      <div class="paid-row">
        <label>Amount Paid</label>
        <input type="number" id="amountPaid" placeholder="0" min="0" oninput="calcChange()">
      </div>
      <div class="change-display">
        <span style="color:var(--muted);font-size:.8rem">Change</span>
        <span class="cv" id="changeDisplay">UGX 0</span>
      </div>
      <div id="refInput" style="display:none">
        <input type="text" id="paymentRef" placeholder="Reference / Transaction ID…">
      </div>
      <button class="charge-btn" id="chargeBtn" onclick="processPayment()" disabled>
        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
        Charge — <span id="chargeTotal">UGX 0</span>
      </button>
    </div>
  </div>
</div>

<div class="modal-backdrop" id="receiptModal">
  <div class="receipt-modal">
    <div class="receipt-header"><h2>SOZO POS</h2><p id="rcpNum"></p><p id="rcpDate" style="font-size:.76rem"></p></div>
    <div id="rcpItems"></div>
    <div id="rcpTotals" style="margin-top:.7rem"></div>
    <div class="receipt-actions">
      <button class="btn btn-outline" onclick="window.print()"><svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>Print</button>
      <button class="btn btn-primary" onclick="closeReceipt()">New Sale</button>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
const cart = [];
let payMethod = 'cash', lastSaleId = null, allProducts = [];
const subcategoryMap = @json($subcategoryMap ?? []);
let filtersCollapsed = localStorage.getItem('posFiltersCollapsed') === 'true';

/* ── FILTER COLLAPSE ── */
function toggleFilters() {
    filtersCollapsed = !filtersCollapsed;
    localStorage.setItem('posFiltersCollapsed', filtersCollapsed);
    document.getElementById('filterBar').classList.toggle('collapsed', filtersCollapsed);
    document.getElementById('filterToggleIcon').classList.toggle('rotated', filtersCollapsed);
}
function initFilterCollapse() {
    document.getElementById('filterBar').classList.toggle('collapsed', filtersCollapsed);
    document.getElementById('filterToggleIcon').classList.toggle('rotated', filtersCollapsed);
}
function updateFilterBadge() {
    let n = ['filterCategory','filterSubcategory','filterBatch','filterMinPrice','filterMaxPrice','filterStock']
        .filter(id => document.getElementById(id).value).length;
    const b = document.getElementById('activeFilterCount');
    b.textContent = n; b.style.display = n > 0 ? 'inline-block' : 'none';
}

/* ── SUBCATEGORY CASCADE ── */
function onCategoryChange() {
    const catCode = document.getElementById('filterCategory').value;
    const sel = document.getElementById('filterSubcategory');
    const grp = document.getElementById('subcatGroup');
    sel.innerHTML = '<option value="">All</option>';
    sel.value = '';
    const subs = subcategoryMap[catCode] || [];
    if (catCode && subs.length) {
        subs.forEach(s => { const o = document.createElement('option'); o.value = s.code; o.textContent = s.display_name; sel.appendChild(o); });
        grp.classList.add('visible');
    } else {
        grp.classList.remove('visible');
    }
    applyFilters();
}

/* ── BOOT ── */
document.addEventListener('DOMContentLoaded', () => {
    initFilterCollapse();
    loadDefaultProducts();
    document.getElementById('barcodeFocus').addEventListener('click', () => document.getElementById('productSearch').focus());

    /* ★ FIX 1: Event delegation — survives innerHTML rewrites */
    document.getElementById('cartItems').addEventListener('click', function(e) {
        const r = e.target.closest('[data-action="remove"]');
        const p = e.target.closest('[data-action="plus"]');
        const m = e.target.closest('[data-action="minus"]');
        if (r) removeFromCart(+r.dataset.id);
        if (p) updateQty(+p.dataset.id,  1);
        if (m) updateQty(+m.dataset.id, -1);
    });
});

/* ── LOAD DEFAULT PRODUCTS ── */
async function loadDefaultProducts() {
    try {
        const r = await fetch('{{ route("pos.products.load") }}', {
            method:'POST', headers:{'Content-Type':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}'},
            body: JSON.stringify({ limit:60 })
        });
        allProducts = await r.json();
        applyFilters();
    } catch(e) { document.getElementById('productsGrid').innerHTML = gridEmpty('Failed to load products'); }
}

/* ── SEARCH ── */
const searchInput = document.getElementById('productSearch');
let searchTimer;
searchInput.addEventListener('input', () => {
    clearTimeout(searchTimer);
    const q = searchInput.value.trim();
    if (!q) { applyFilters(); return; }
    searchTimer = setTimeout(() => fetchSearch(q), 220);
});
async function fetchSearch(q) {
    try {
        const r = await fetch('{{ route("pos.products.search") }}', {
            method:'POST', headers:{'Content-Type':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}'},
            body: JSON.stringify({ q })
        });
        renderProducts(await r.json());
    } catch(e) { console.error(e); }
}

/* ── FILTERS ── */
function applyFilters() {
    const q = searchInput.value.trim();
    if (q) { fetchSearch(q); return; }
    renderProducts(filterProducts());
    updateFilterBadge();
    const filtered = filterProducts();
    document.getElementById('filterCount').textContent = filtered.length + ' item' + (filtered.length !== 1 ? 's' : '');
}
function filterProducts() {
    const cat      = document.getElementById('filterCategory').value;
    const subcat   = document.getElementById('filterSubcategory').value;
    const batch    = document.getElementById('filterBatch').value;
    const minPrice = parseFloat(document.getElementById('filterMinPrice').value) || 0;
    const maxPrice = parseFloat(document.getElementById('filterMaxPrice').value) || Infinity;
    const stockF   = document.getElementById('filterStock').value;
    return allProducts.filter(p => {
        if (cat    && p.category_code    !== cat)    return false;
        if (subcat && p.subcategory_code !== subcat)  return false;
        if (batch  && p.batch_code       !== batch)   return false;
        if (p.selling_price < minPrice)               return false;
        if (p.selling_price > maxPrice)               return false;
        if (stockF === '')    return p.quantity > 0;
        if (stockF === 'low') return p.quantity > 0 && p.quantity <= (p.low_stock_threshold || 5);
        return true;
    });
}
function clearFilters() {
    ['filterCategory','filterSubcategory','filterBatch','filterMinPrice','filterMaxPrice','filterStock']
        .forEach(id => document.getElementById(id).value = '');
    document.getElementById('subcatGroup').classList.remove('visible');
    searchInput.value = '';
    applyFilters();
}

/* ── RENDER PRODUCTS ── */
function renderProducts(items) {
    const grid = document.getElementById('productsGrid');
    if (!items.length) { grid.innerHTML = gridEmpty('No products match your filters'); return; }
    const cartMap = new Map(cart.map(c => [c.id, c.qty]));
    grid.innerHTML = items.map(p => {
        const inCart = cartMap.has(p.id);
        const oos    = p.quantity === 0;
        const low    = p.quantity > 0 && p.quantity <= (p.low_stock_threshold || 5);
        const qty    = cartMap.get(p.id) || 0;
        const ribbon = oos ? '<span class="prod-ribbon">Out of Stock</span>' : (low ? `<span class="prod-ribbon low">Low: ${p.quantity}</span>` : '');
        const img    = p.image_path
            ? `<img src="/${p.image_path}" alt="${escHtml(p.name)}" loading="lazy">`
            : `<svg width="26" height="26" fill="none" stroke="currentColor" stroke-width="1.3" viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>`;
        return `<div class="prod-card${oos?' out-of-stock':''}${inCart?' in-cart':''}" onclick="addToCartById(${p.id})">
            <div class="prod-img">${img}${ribbon}<div class="prod-cart-badge">${qty||''}</div></div>
            <div class="prod-body">
                <div class="prod-name">${escHtml(p.name)}</div>
                <div class="prod-price">${fmtMoney(p.selling_price)}</div>
                <div class="prod-meta">
                    <span class="prod-qty${oos?' out':low?' low':''}">${oos?'Out of stock':p.quantity+' left'}</span>
                    ${p.category_code?`<span class="prod-category">${escHtml(p.category_code)}</span>`:''}
                </div>
            </div>
        </div>`;
    }).join('');
}
function gridEmpty(msg) {
    return `<div class="grid-empty"><svg width="38" height="38" fill="none" stroke="currentColor" stroke-width="1.3" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg><p>${msg}</p></div>`;
}

/* ── CART LOGIC ── */

/* ★ FIX 2: look up product from allProducts — no fragile attribute strings */
function addToCartById(id) {
    const p = allProducts.find(x => x.id === id);
    if (!p || p.quantity === 0) return;
    const existing = cart.find(i => i.id === id);
    if (existing) {
        if (existing.qty >= p.quantity) {
            Swal.fire({toast:true,position:'top-end',icon:'warning',title:'Max stock reached',showConfirmButton:false,timer:1800,background:'var(--surf)',color:'var(--text)'});
            return;
        }
        existing.qty++;
    } else {
        /* ★ FIX 3: imagePath stored directly from allProducts object — no HTML encoding */
        cart.push({ id:p.id, name:p.name, price:+p.selling_price, stock:p.quantity,
                    taxRate:+p.tax_rate||0, qty:1, discount:0,
                    imagePath: p.image_path||'', catCode:p.category_code||'' });
    }
    renderCart();
    renderProducts(filterProducts());
}

function removeFromCart(id) {
    const idx = cart.findIndex(c => c.id === id);
    if (idx > -1) cart.splice(idx, 1);
    renderCart();
    renderProducts(filterProducts());
}
function updateQty(id, delta) {
    const item = cart.find(c => c.id === id);
    if (!item) return;
    const n = item.qty + delta;
    if (n < 1) { removeFromCart(id); return; }
    if (n > item.stock) return;
    item.qty = n;
    renderCart();
    renderProducts(filterProducts());
}
function clearCart() {
    cart.length = 0;
    document.getElementById('customerSelect').value = '';
    document.getElementById('loyaltyInfo').style.display = 'none';
    document.getElementById('discountInput').value = '';
    document.getElementById('amountPaid').value = '';
    renderCart();
    renderProducts(filterProducts());
}

/* ★ FIX 4: data-action + data-id attrs — no inline onclick in generated HTML */
function renderCart() {
    const container = document.getElementById('cartItems');
    const empty     = document.getElementById('cartEmpty');
    if (!cart.length) {
        container.innerHTML = '';
        container.appendChild(empty);
        empty.style.display = 'flex';
        recalc(); return;
    }
    empty.style.display = 'none';
    container.innerHTML = cart.map(item => {
        const thumb = item.imagePath
            ? `<div class="ci-thumb"><img src="/${item.imagePath}" alt="${escHtml(item.name)}"></div>`
            : `<div class="ci-thumb"><svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2"/></svg></div>`;
        return `<div class="cart-item">
            ${thumb}
            <div class="ci-info">
                <div class="ci-name">${escHtml(item.name)}</div>
                <div class="ci-price">${fmtMoney(item.price)} × ${item.qty}</div>
            </div>
            <div class="ci-qty">
                <button data-action="minus" data-id="${item.id}">−</button>
                <span>${item.qty}</span>
                <button data-action="plus"  data-id="${item.id}">+</button>
            </div>
            <div class="ci-total">${fmtMoney(item.price * item.qty)}</div>
            <button class="ci-remove" data-action="remove" data-id="${item.id}" title="Remove">
                <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
        </div>`;
    }).join('');
    document.getElementById('cartCount').textContent = '(' + cart.reduce((s,i)=>s+i.qty,0) + ' items)';
    recalc();
}

/* ── TOTALS ── */
function recalc() {
    const sub  = cart.reduce((s,i) => s + i.price*i.qty, 0);
    const tax  = cart.reduce((s,i) => s + (i.price*i.qty*i.taxRate/100), 0);
    const disc = parseFloat(document.getElementById('discountInput').value)||0;
    const tot  = Math.max(0, sub+tax-disc);
    document.getElementById('subtotalVal').textContent = fmtMoney(sub);
    document.getElementById('taxVal').textContent      = fmtMoney(tax);
    document.getElementById('discountVal').textContent = fmtMoney(disc);
    document.getElementById('totalVal').textContent    = fmtMoney(tot);
    document.getElementById('chargeTotal').textContent = 'UGX '+Math.round(tot).toLocaleString();
    calcChange();
    document.getElementById('chargeBtn').disabled = !cart.length;
}
function calcChange() {
    const tot  = parseFloat(document.getElementById('totalVal').textContent.replace(/[^0-9.]/g,''))||0;
    const paid = parseFloat(document.getElementById('amountPaid').value)||0;
    document.getElementById('changeDisplay').textContent = fmtMoney(Math.max(0,paid-tot));
}

/* ── PAYMENT ── */
function setPayMethod(btn) {
    document.querySelectorAll('.pay-btn').forEach(b=>b.classList.remove('active'));
    btn.classList.add('active');
    payMethod = btn.dataset.method;
    document.getElementById('refInput').style.display = ['card','mobile_money','split'].includes(payMethod)?'block':'none';
}
function updateCustomer(val) {
    const sel=document.getElementById('customerSelect'), opt=sel.options[sel.selectedIndex], info=document.getElementById('loyaltyInfo');
    if(val){document.getElementById('loyaltyPts').textContent=opt.dataset.pts;document.getElementById('loyaltyTier').textContent=opt.dataset.tier;info.style.display='flex';}
    else info.style.display='none';
}
async function processPayment() {
    if(!cart.length)return;
    const tot=parseFloat(document.getElementById('totalVal').textContent.replace(/[^0-9.]/g,''))||0;
    const paid=parseFloat(document.getElementById('amountPaid').value)||0;
    const disc=parseFloat(document.getElementById('discountInput').value)||0;
    if(payMethod==='cash'&&paid<tot){Swal.fire({icon:'warning',title:'Insufficient payment',text:`Total is ${fmtMoney(tot)}.`,background:'var(--surf)',color:'var(--text)',confirmButtonColor:'var(--accent)'});return;}
    const btn=document.getElementById('chargeBtn');
    btn.disabled=true;btn.textContent='Processing…';
    try{
        const r=await fetch('{{ route("pos.sale.store") }}',{method:'POST',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}'},
            body:JSON.stringify({items:cart.map(i=>({id:i.id,qty:i.qty,price:i.price,discount:i.discount||0})),payment_method:payMethod,amount_paid:paid||tot,discount_amount:disc,customer_id:document.getElementById('customerSelect').value||null,payment_reference:document.getElementById('paymentRef')?.value||null})});
        const data=await r.json();
        if(data.success)showReceipt(data);
        else Swal.fire({icon:'error',title:'Error',text:data.error||'Transaction failed',background:'var(--surf)',color:'var(--text)',confirmButtonColor:'var(--accent)'});
    }catch(e){Swal.fire({icon:'error',title:'Network Error',text:'Could not complete transaction.',background:'var(--surf)',color:'var(--text)',confirmButtonColor:'var(--accent)'});}
    finally{btn.disabled=false;btn.innerHTML=`<svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg> Charge — <span id="chargeTotal">UGX 0</span>`;recalc();}
}
function showReceipt(data){
    lastSaleId=data.sale_id;
    document.getElementById('rcpNum').textContent='Receipt: '+data.receipt_number;
    document.getElementById('rcpDate').textContent=new Date().toLocaleString();
    document.getElementById('rcpItems').innerHTML=cart.map(i=>`<div class="receipt-line"><span>${escHtml(i.name)} ×${i.qty}</span><span>${fmtMoney(i.price*i.qty)}</span></div>`).join('');
    document.getElementById('rcpTotals').innerHTML=`<div class="receipt-line total"><span>TOTAL</span><span>${fmtMoney(data.total)}</span></div><div class="receipt-line change-line"><span>Change</span><span>${fmtMoney(data.change)}</span></div>`;
    document.getElementById('receiptModal').classList.add('open');
}
function closeReceipt(){document.getElementById('receiptModal').classList.remove('open');clearCart();searchInput.value='';loadDefaultProducts();}

function fmtMoney(n){return 'UGX '+Math.round(parseFloat(n)).toLocaleString('en-UG');}
function escHtml(s){return String(s||'').replace(/[&<>"']/g,c=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));}
</script>
@endpush