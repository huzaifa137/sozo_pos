@extends('layouts.app')
@section('page-title', 'POS Terminal')

@push('styles')
<style>
.pos-wrap{display:grid;grid-template-columns:1fr 400px;gap:1.2rem;height:calc(100vh - 96px)}

/* ─ LEFT PANEL ─ */
.pos-left{display:flex;flex-direction:column;gap:1rem;overflow:hidden}

.search-bar{display:flex;gap:.6rem;align-items:center}
.search-bar input{flex:1;background:var(--surf);border:1px solid var(--border);padding:.75rem 1rem .75rem 2.8rem;font-size:.95rem}
.search-wrap{position:relative;flex:1}
.search-icon{position:absolute;left:.9rem;top:50%;transform:translateY(-50%);color:var(--muted);pointer-events:none}
.barcode-btn{flex-shrink:0;background:var(--surf2);border:1px solid var(--border);color:var(--text);padding:.7rem .9rem;border-radius:8px;cursor:pointer;transition:all .15s}
.barcode-btn:hover{border-color:var(--accent);color:var(--accent)}

/* products grid */
.products-scroll{flex:1;overflow-y:auto;padding-right:4px}
.products-scroll::-webkit-scrollbar{width:4px}
.products-scroll::-webkit-scrollbar-thumb{background:var(--border);border-radius:4px}
.products-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(150px,1fr));gap:.8rem}
.prod-card{
    background:var(--surf);border:1px solid var(--border);border-radius:10px;
    cursor:pointer;transition:all .18s;overflow:hidden;
}
.prod-card:hover{border-color:var(--accent);transform:translateY(-2px)}
.prod-card.out-of-stock{opacity:.45;cursor:not-allowed}
.prod-img{width:100%;aspect-ratio:4/3;object-fit:cover;background:var(--surf2);display:flex;align-items:center;justify-content:center}
.prod-img img{width:100%;height:100%;object-fit:cover}
.prod-body{padding:.6rem .7rem}
.prod-name{font-size:.82rem;font-weight:600;line-height:1.25;margin-bottom:.3rem}
.prod-price{font-family:var(--font-head);font-size:.95rem;font-weight:700;color:var(--accent)}
.prod-qty{font-size:.7rem;color:var(--muted);margin-top:2px}
.prod-qty.low{color:var(--warn)}

/* ─ RIGHT PANEL: CART ─ */
.pos-right{display:flex;flex-direction:column;background:var(--surf);border:1px solid var(--border);border-radius:var(--radius);overflow:hidden}
.cart-header{padding:1rem 1.2rem;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between}
.cart-header h3{font-family:var(--font-head);font-weight:700;font-size:1.05rem}
.cart-clear{background:none;border:none;color:var(--muted);cursor:pointer;font-size:.8rem;display:flex;align-items:center;gap:.3rem;transition:color .15s}
.cart-clear:hover{color:var(--danger)}

/* customer selector */
.cart-customer{padding:.8rem 1rem;border-bottom:1px solid var(--border)}
.cart-customer select{font-size:.85rem;padding:.5rem .8rem}

/* cart items */
.cart-items{flex:1;overflow-y:auto;padding:.5rem}
.cart-empty{display:flex;flex-direction:column;align-items:center;justify-content:center;height:100%;gap:.5rem;color:var(--muted);font-size:.85rem;padding:2rem}
.cart-item{display:flex;align-items:center;gap:.6rem;padding:.6rem;border-radius:8px;transition:background .15s}
.cart-item:hover{background:var(--surf2)}
.ci-name{flex:1;font-size:.85rem;font-weight:600;line-height:1.2}
.ci-price{font-size:.78rem;color:var(--muted)}
.ci-qty{display:flex;align-items:center;gap:.3rem}
.ci-qty button{width:22px;height:22px;border-radius:5px;border:1px solid var(--border);background:var(--surf2);color:var(--text);cursor:pointer;font-size:.9rem;display:flex;align-items:center;justify-content:center;transition:all .15s}
.ci-qty button:hover{border-color:var(--accent);color:var(--accent)}
.ci-qty span{font-family:var(--font-head);font-size:.9rem;font-weight:700;min-width:24px;text-align:center}
.ci-total{font-family:var(--font-head);font-weight:700;font-size:.9rem;min-width:70px;text-align:right}
.ci-remove{color:var(--muted);cursor:pointer;background:none;border:none;padding:2px;transition:color .15s;flex-shrink:0}
.ci-remove:hover{color:var(--danger)}

/* cart footer */
.cart-footer{border-top:1px solid var(--border);padding:1rem}
.totals-grid{display:grid;grid-template-columns:1fr auto;gap:.3rem .5rem;font-size:.85rem;margin-bottom:.8rem}
.totals-grid .lbl{color:var(--muted)}
.totals-grid .val{text-align:right;font-weight:500}
.totals-grid .grand-lbl{font-family:var(--font-head);font-weight:700;font-size:1rem;color:var(--text);border-top:1px solid var(--border);padding-top:.4rem;margin-top:.2rem}
.totals-grid .grand-val{font-family:var(--font-head);font-weight:800;font-size:1.2rem;color:var(--accent);border-top:1px solid var(--border);padding-top:.4rem;margin-top:.2rem;text-align:right}

.discount-row{display:flex;gap:.5rem;margin-bottom:.8rem;align-items:center}
.discount-row input{flex:1;padding:.55rem .8rem;font-size:.85rem}
.discount-row label{margin-bottom:0;font-size:.78rem;white-space:nowrap;color:var(--muted);text-transform:none;letter-spacing:0}

/* payment methods */
.pay-methods{display:grid;grid-template-columns:repeat(4,1fr);gap:.4rem;margin-bottom:.8rem}
.pay-btn{
    padding:.5rem .3rem;border-radius:7px;border:1px solid var(--border);
    background:var(--surf2);color:var(--muted);font-size:.72rem;font-weight:600;
    cursor:pointer;transition:all .15s;text-align:center;display:flex;flex-direction:column;
    align-items:center;gap:.25rem;
}
.pay-btn.active{background:rgba(240,192,64,.12);border-color:var(--accent);color:var(--accent)}
.pay-btn svg{opacity:.6}
.pay-btn.active svg{opacity:1}

.paid-row{display:flex;gap:.5rem;margin-bottom:.6rem;align-items:center}
.paid-row label{margin-bottom:0;white-space:nowrap;font-size:.78rem;text-transform:none;letter-spacing:0}
.paid-row input{flex:1;font-size:.9rem;font-weight:700}

.change-display{background:var(--surf2);border:1px solid var(--border);border-radius:8px;padding:.6rem .9rem;display:flex;justify-content:space-between;align-items:center;margin-bottom:.8rem;font-size:.85rem}
.change-display .cv{font-family:var(--font-head);font-weight:800;font-size:1.1rem;color:var(--success)}

.charge-btn{
    width:100%;background:var(--accent);color:#0b0d11;border:none;border-radius:9px;
    padding:.9rem;font-family:var(--font-head);font-size:1rem;font-weight:800;
    cursor:pointer;transition:background .15s;display:flex;align-items:center;justify-content:center;gap:.5rem;
}
.charge-btn:hover{background:#ffd55e}
.charge-btn:disabled{opacity:.4;cursor:not-allowed}

/* receipt modal */
.modal-backdrop{position:fixed;inset:0;background:rgba(0,0,0,.7);z-index:500;display:flex;align-items:center;justify-content:center;display:none}
.modal-backdrop.open{display:flex}
.receipt-modal{background:var(--surf);border:1px solid var(--border);border-radius:14px;width:420px;max-height:90vh;overflow-y:auto;padding:1.5rem}
.receipt-header{text-align:center;margin-bottom:1.2rem;padding-bottom:1rem;border-bottom:1px solid var(--border)}
.receipt-header h2{font-family:var(--font-head);font-weight:800;font-size:1.4rem;color:var(--accent)}
.receipt-header p{color:var(--muted);font-size:.85rem;margin-top:.2rem}
.receipt-line{display:flex;justify-content:space-between;padding:.3rem 0;font-size:.85rem;border-bottom:1px dashed var(--border)}
.receipt-line:last-child{border-bottom:none}
.receipt-line.total{font-weight:700;font-size:.95rem;color:var(--accent);border-top:1px solid var(--border);padding-top:.5rem;margin-top:.2rem}
.receipt-line.change-line{font-weight:700;color:var(--success)}
.receipt-actions{display:flex;gap:.6rem;margin-top:1.2rem}
.receipt-actions button,.receipt-actions a{flex:1;text-align:center}
</style>
@endpush

@section('content')
<div class="pos-wrap">

    {{-- ── LEFT: PRODUCTS ── --}}
    <div class="pos-left">
        <div class="search-bar">
            <div class="search-wrap">
                <svg class="search-icon" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                <input type="text" id="productSearch" placeholder="Search product, scan barcode or enter SKU…" autocomplete="off">
            </div>
            <button class="barcode-btn" id="barcodeFocus" title="Focus for barcode scan">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path d="M3 5h2M7 5h2M3 5v4M21 5h-2M17 5h-2M21 5v4M3 19h2M7 19h2M3 19v-4M21 19h-2M17 19h-2M21 19v-4"/>
                    <line x1="7" y1="8" x2="7" y2="16"/><line x1="10" y1="8" x2="10" y2="16"/>
                    <line x1="13" y1="8" x2="13" y2="16"/><line x1="16" y1="8" x2="16" y2="12"/>
                </svg>
            </button>
        </div>

        <div class="products-scroll">
            <div class="products-grid" id="productsGrid">
                <div style="grid-column:1/-1;text-align:center;padding:3rem;color:var(--muted)">
                    <svg width="40" height="40" fill="none" stroke="currentColor" stroke-width="1.3" viewBox="0 0 24 24" style="margin:0 auto 1rem;display:block"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                    Search or scan a product to begin
                </div>
            </div>
        </div>
    </div>

    {{-- ── RIGHT: CART ── --}}
    <div class="pos-right">
        <div class="cart-header">
            <h3>Cart <span id="cartCount" style="color:var(--muted);font-size:.85rem;font-weight:400"></span></h3>
            <button class="cart-clear" onclick="clearCart()">
                <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/></svg>
                Clear All
            </button>
        </div>

        {{-- Customer --}}
        <div class="cart-customer">
            <select id="customerSelect" onchange="updateCustomer(this.value)">
                <option value="">— Walk-in customer —</option>
                @foreach($customers as $c)
                    <option value="{{ $c->id }}" data-pts="{{ $c->loyalty_points }}" data-tier="{{ $c->loyalty_tier }}">
                        {{ $c->name }}{{ $c->phone ? ' ('.$c->phone.')' : '' }}
                    </option>
                @endforeach
            </select>
            <div id="loyaltyInfo" style="margin-top:.4rem;font-size:.75rem;color:var(--muted);display:none">
                Loyalty: <span id="loyaltyPts" style="color:var(--accent);font-weight:700"></span> pts · <span id="loyaltyTier" style="text-transform:capitalize"></span>
            </div>
        </div>

        {{-- Items --}}
        <div id="cartItems" class="cart-items">
            <div class="cart-empty" id="cartEmpty">
                <svg width="36" height="36" fill="none" stroke="currentColor" stroke-width="1.3" viewBox="0 0 24 24"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
                Cart is empty
            </div>
        </div>

        {{-- Footer totals --}}
        <div class="cart-footer">
            <div class="discount-row">
                <label>Discount (UGX)</label>
                <input type="number" id="discountInput" placeholder="0" min="0" oninput="recalc()" style="max-width:120px">
            </div>

            <div class="totals-grid">
                <span class="lbl">Subtotal</span>      <span class="val" id="subtotalVal">0</span>
                <span class="lbl">Tax</span>           <span class="val" id="taxVal">0</span>
                <span class="lbl">Discount</span>      <span class="val" id="discountVal">0</span>
                <span class="grand-lbl">TOTAL</span>   <span class="grand-val" id="totalVal">0</span>
            </div>

            <div class="pay-methods">
                <button class="pay-btn active" data-method="cash" onclick="setPayMethod(this)">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
                    Cash
                </button>
                <button class="pay-btn" data-method="card" onclick="setPayMethod(this)">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="2" y="5" width="20" height="14" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/></svg>
                    Card
                </button>
                <button class="pay-btn" data-method="mobile_money" onclick="setPayMethod(this)">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="5" y="2" width="14" height="20" rx="2"/><line x1="12" y1="18" x2="12.01" y2="18"/></svg>
                    Mobile
                </button>
                <button class="pay-btn" data-method="split" onclick="setPayMethod(this)">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M16 3h5v5M8 21H3v-5M21 3l-7 7M3 21l7-7"/></svg>
                    Split
                </button>
            </div>

            <div class="paid-row">
                <label>Amount Paid</label>
                <input type="number" id="amountPaid" placeholder="0" min="0" oninput="calcChange()" style="font-family:var(--font-head)">
            </div>

            <div class="change-display">
                <span style="color:var(--muted);font-size:.82rem">Change to give</span>
                <span class="cv" id="changeDisplay">0</span>
            </div>

            <div id="refInput" style="display:none;margin-bottom:.6rem">
                <input type="text" id="paymentRef" placeholder="Reference / Transaction ID…" style="font-size:.85rem">
            </div>

            <button class="charge-btn" id="chargeBtn" onclick="processPayment()" disabled>
                <svg width="17" height="17" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                Charge — <span id="chargeTotal">UGX 0</span>
            </button>
        </div>
    </div>
</div>

{{-- ── RECEIPT MODAL ── --}}
<div class="modal-backdrop" id="receiptModal">
    <div class="receipt-modal">
        <div class="receipt-header">
            <h2>SOZS POS</h2>
            <p id="rcpNum"></p>
            <p id="rcpDate" style="font-size:.78rem"></p>
        </div>
        <div id="rcpItems"></div>
        <div id="rcpTotals" style="margin-top:.8rem"></div>
        <div class="receipt-actions">
            <button class="btn btn-outline" onclick="window.print()">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
                Print
            </button>
            <button class="btn btn-primary" onclick="closeReceipt()">New Sale</button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const cart = [];
let payMethod = 'cash';
let lastSaleId = null;

// ─ PRODUCT SEARCH ─
const searchInput = document.getElementById('productSearch');
let searchTimer;

searchInput.addEventListener('input', () => {
    clearTimeout(searchTimer);
    const q = searchInput.value.trim();
    if (!q) { document.getElementById('productsGrid').innerHTML = '<div style="grid-column:1/-1;text-align:center;padding:3rem;color:var(--muted)">Search or scan a product to begin</div>'; return; }
    searchTimer = setTimeout(() => fetchProducts(q), 250);
});

// Barcode focus button
document.getElementById('barcodeFocus').addEventListener('click', () => searchInput.focus());

async function fetchProducts(q) {
    try {
        const r = await fetch('{{ route("pos.products.search") }}', {
            method:'POST',
            headers:{'Content-Type':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}'},
            body: JSON.stringify({q})
        });
        const items = await r.json();
        renderProducts(items);
    } catch(e) { console.error(e); }
}

function renderProducts(items) {
    const grid = document.getElementById('productsGrid');
    if (!items.length) { grid.innerHTML = '<div style="grid-column:1/-1;text-align:center;padding:2rem;color:var(--muted)">No products found</div>'; return; }
    grid.innerHTML = items.map(p => `
        <div class="prod-card ${p.quantity === 0 ? 'out-of-stock' : ''}" onclick="addToCart(${p.id}, '${escHtml(p.name)}', ${p.selling_price}, ${p.quantity}, ${p.tax_rate})">
            <div class="prod-img">
                ${p.image_path ? `<img src="/${p.image_path}" alt="${escHtml(p.name)}">` :
                `<svg width="28" height="28" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>`}
            </div>
            <div class="prod-body">
                <div class="prod-name">${escHtml(p.name)}</div>
                <div class="prod-price">${fmtMoney(p.selling_price)}</div>
                <div class="prod-qty ${p.quantity <= 5 ? 'low' : ''}">${p.quantity} in stock</div>
            </div>
        </div>
    `).join('');
}

// ─ CART LOGIC ─
function addToCart(id, name, price, stock, taxRate) {
    if (stock === 0) return;
    const existing = cart.find(i => i.id === id);
    if (existing) {
        if (existing.qty >= stock) { Swal.fire({toast:true,position:'top-end',icon:'warning',title:'Max stock reached',showConfirmButton:false,timer:1800,background:'#161920',color:'#eef0f6'}); return; }
        existing.qty++;
    } else {
        cart.push({id, name, price, stock, taxRate: parseFloat(taxRate)||0, qty:1, discount:0});
    }
    renderCart();
}

function removeFromCart(id) {
    const i = cart.findIndex(c => c.id === id);
    if (i > -1) cart.splice(i, 1);
    renderCart();
}

function updateQty(id, delta) {
    const item = cart.find(c => c.id === id);
    if (!item) return;
    item.qty = Math.max(1, Math.min(item.stock, item.qty + delta));
    renderCart();
}

function clearCart() {
    cart.length = 0;
    document.getElementById('customerSelect').value = '';
    document.getElementById('loyaltyInfo').style.display = 'none';
    document.getElementById('discountInput').value = '';
    document.getElementById('amountPaid').value = '';
    renderCart();
}

function renderCart() {
    const container = document.getElementById('cartItems');
    const empty     = document.getElementById('cartEmpty');

    if (!cart.length) {
        container.innerHTML = '';
        container.appendChild(empty);
        empty.style.display = 'flex';
        recalc();
        return;
    }
    empty.style.display = 'none';

    container.innerHTML = cart.map(item => `
        <div class="cart-item">
            <div style="flex:1;min-width:0">
                <div class="ci-name">${escHtml(item.name)}</div>
                <div class="ci-price">${fmtMoney(item.price)} × ${item.qty}</div>
            </div>
            <div class="ci-qty">
                <button onclick="updateQty(${item.id}, -1)">−</button>
                <span>${item.qty}</span>
                <button onclick="updateQty(${item.id}, 1)">+</button>
            </div>
            <div class="ci-total">${fmtMoney(item.price * item.qty)}</div>
            <button class="ci-remove" onclick="removeFromCart(${item.id})">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
        </div>
    `).join('');

    document.getElementById('cartCount').textContent = `(${cart.reduce((s,i)=>s+i.qty,0)} items)`;
    recalc();
}

function recalc() {
    const subtotal    = cart.reduce((s,i) => s + i.price * i.qty, 0);
    const tax         = cart.reduce((s,i) => s + (i.price * i.qty * i.taxRate / 100), 0);
    const discount    = parseFloat(document.getElementById('discountInput').value)||0;
    const total       = Math.max(0, subtotal + tax - discount);

    document.getElementById('subtotalVal').textContent = fmtMoney(subtotal);
    document.getElementById('taxVal').textContent      = fmtMoney(tax);
    document.getElementById('discountVal').textContent = fmtMoney(discount);
    document.getElementById('totalVal').textContent    = fmtMoney(total);
    document.getElementById('chargeTotal').textContent = 'UGX ' + total.toLocaleString();

    calcChange();
    document.getElementById('chargeBtn').disabled = !cart.length;
}

function calcChange() {
    const total = parseFloat(document.getElementById('totalVal').textContent.replace(/[^0-9.]/g,''))||0;
    const paid  = parseFloat(document.getElementById('amountPaid').value)||0;
    const change = Math.max(0, paid - total);
    document.getElementById('changeDisplay').textContent = 'UGX ' + change.toLocaleString();
}

function setPayMethod(btn) {
    document.querySelectorAll('.pay-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    payMethod = btn.dataset.method;
    const showRef = ['card','mobile_money','split'].includes(payMethod);
    document.getElementById('refInput').style.display = showRef ? 'block' : 'none';
}

function updateCustomer(val) {
    const sel = document.getElementById('customerSelect');
    const opt = sel.options[sel.selectedIndex];
    const info = document.getElementById('loyaltyInfo');
    if (val) {
        document.getElementById('loyaltyPts').textContent = opt.dataset.pts;
        document.getElementById('loyaltyTier').textContent = opt.dataset.tier;
        info.style.display = 'block';
    } else {
        info.style.display = 'none';
    }
}

// ─ PROCESS PAYMENT ─
async function processPayment() {
    if (!cart.length) return;

    const total    = parseFloat(document.getElementById('totalVal').textContent.replace(/[^0-9.]/g,''))||0;
    const paid     = parseFloat(document.getElementById('amountPaid').value)||0;
    const discount = parseFloat(document.getElementById('discountInput').value)||0;

    if (payMethod === 'cash' && paid < total) {
        Swal.fire({icon:'warning',title:'Insufficient payment',text:`Total is UGX ${total.toLocaleString()}. Amount paid is short.`,background:'#161920',color:'#eef0f6',confirmButtonColor:'#f0c040'});
        return;
    }

    const btn = document.getElementById('chargeBtn');
    btn.disabled = true;
    btn.innerHTML = '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4"/></svg> Processing…';

    try {
        const r = await fetch('{{ route("pos.sale.store") }}', {
            method:'POST',
            headers:{'Content-Type':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}'},
            body: JSON.stringify({
                items: cart.map(i => ({id:i.id, qty:i.qty, price:i.price, discount:i.discount})),
                payment_method: payMethod,
                amount_paid: paid || total,
                discount_amount: discount,
                customer_id: document.getElementById('customerSelect').value || null,
                payment_reference: document.getElementById('paymentRef')?.value || null,
            })
        });
        const data = await r.json();

        if (data.success) {
            showReceipt(data);
        } else {
            Swal.fire({icon:'error',title:'Error',text:data.error||'Transaction failed',background:'#161920',color:'#eef0f6',confirmButtonColor:'#f0c040'});
        }
    } catch(e) {
        Swal.fire({icon:'error',title:'Network Error',text:'Could not complete transaction.',background:'#161920',color:'#eef0f6',confirmButtonColor:'#f0c040'});
    } finally {
        btn.disabled = false;
        btn.innerHTML = '<svg width="17" height="17" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg> Charge — <span id="chargeTotal">UGX 0</span>';
        recalc();
    }
}

function showReceipt(data) {
    lastSaleId = data.sale_id;
    document.getElementById('rcpNum').textContent = 'Receipt: ' + data.receipt_number;
    document.getElementById('rcpDate').textContent = new Date().toLocaleString();
    document.getElementById('rcpItems').innerHTML = cart.map(i => `
        <div class="receipt-line">
            <span>${escHtml(i.name)} ×${i.qty}</span>
            <span>${fmtMoney(i.price * i.qty)}</span>
        </div>
    `).join('');
    document.getElementById('rcpTotals').innerHTML = `
        <div class="receipt-line total"><span>TOTAL</span><span>UGX ${data.total.toLocaleString()}</span></div>
        <div class="receipt-line change-line"><span>Change</span><span>UGX ${data.change.toLocaleString()}</span></div>
    `;
    document.getElementById('receiptModal').classList.add('open');
}

function closeReceipt() {
    document.getElementById('receiptModal').classList.remove('open');
    clearCart();
    searchInput.value = '';
    document.getElementById('productsGrid').innerHTML = '<div style="grid-column:1/-1;text-align:center;padding:3rem;color:var(--muted)">Search or scan a product to begin</div>';
}

function fmtMoney(n) { return 'UGX ' + parseFloat(n).toLocaleString('en-UG', {maximumFractionDigits:0}); }
function escHtml(s) { return String(s).replace(/[&<>"']/g,c=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c])); }
</script>
@endpush
