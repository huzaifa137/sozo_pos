@extends('layouts.shop')
@section('title', 'Checkout — SOZO Store')

@push('styles')
<style>
.checkout-page { padding: 2.5rem 0 4rem; }
.checkout-page h1 { font-family: var(--font-serif); font-size: 2rem; font-weight: 700; margin-bottom: 2rem; }
.checkout-grid { display: grid; grid-template-columns: 1fr 380px; gap: 2rem; align-items: start; }
.checkout-card { background: var(--white); border: 1px solid var(--border); border-radius: 14px; padding: 1.8rem; margin-bottom: 1.2rem; }
.checkout-card h3 { font-family: var(--font-serif); font-size: 1.1rem; font-weight: 700; margin-bottom: 1.3rem; padding-bottom: .8rem; border-bottom: 1px solid var(--border); }
.form-group { margin-bottom: 1rem; }
.form-group label { display: block; font-size: .75rem; font-weight: 700; text-transform: uppercase; letter-spacing: .05em; color: var(--light); margin-bottom: .4rem; }
.form-group input, .form-group select, .form-group textarea { width: 100%; background: var(--warm); border: 1px solid var(--border); border-radius: 8px; padding: .7rem .9rem; font-family: var(--font-sans); font-size: .9rem; color: var(--charcoal); outline: none; transition: border-color .18s; appearance: none; }
.form-group input:focus, .form-group select:focus, .form-group textarea:focus { border-color: var(--gold); box-shadow: 0 0 0 3px rgba(201,151,58,.1); }
.form-row { display: grid; grid-template-columns: 1fr 1fr; gap: .8rem; }
.error-msg { color: var(--danger); font-size: .78rem; margin-top: .3rem; }

/* delivery / payment options */
.option-cards { display: grid; grid-template-columns: 1fr 1fr; gap: .7rem; }
.option-card { border: 2px solid var(--border); border-radius: 10px; padding: .9rem 1rem; cursor: pointer; transition: all .15s; }
.option-card:hover { border-color: var(--gold); }
.option-card input[type=radio] { display: none; }
.option-card.selected { border-color: var(--gold); background: rgba(201,151,58,.05); }
.option-card .oc-title { font-size: .9rem; font-weight: 600; display: flex; align-items: center; gap: .5rem; }
.option-card .oc-sub { font-size: .78rem; color: var(--mid); margin-top: .2rem; }
.oc-check { width: 18px; height: 18px; border-radius: 50%; border: 2px solid var(--border); display: flex; align-items: center; justify-content: center; flex-shrink: 0; transition: all .15s; }
.option-card.selected .oc-check { background: var(--gold); border-color: var(--gold); color: var(--white); }

/* order summary sidebar */
.order-summary { background: var(--white); border: 1px solid var(--border); border-radius: 14px; padding: 1.5rem; position: sticky; top: 90px; }
.order-summary h3 { font-family: var(--font-serif); font-size: 1.1rem; font-weight: 700; margin-bottom: 1rem; padding-bottom: .8rem; border-bottom: 1px solid var(--border); }
.os-item { display: flex; gap: .8rem; padding: .6rem 0; border-bottom: 1px solid var(--warm); }
.os-item:last-of-type { border-bottom: none; }
.os-img { width: 48px; height: 48px; border-radius: 7px; background: var(--warm); overflow: hidden; flex-shrink: 0; }
.os-img img { width: 100%; height: 100%; object-fit: cover; }
.os-name { font-size: .85rem; font-weight: 500; line-height: 1.3; }
.os-qty-price { font-size: .78rem; color: var(--mid); }
.os-line-total { margin-left: auto; font-weight: 700; font-size: .88rem; white-space: nowrap; }
.sum-row { display: flex; justify-content: space-between; font-size: .88rem; margin-bottom: .5rem; }
.sum-row .lbl { color: var(--mid); }
.sum-row.grand { font-weight: 700; font-size: 1rem; border-top: 1px solid var(--border); padding-top: .7rem; margin-top: .3rem; }
.sum-row.grand .val { font-family: var(--font-serif); color: var(--gold); font-size: 1.15rem; }
.place-order-btn { width: 100%; margin-top: 1.2rem; padding: .95rem; border-radius: 10px; background: var(--gold); color: var(--white); border: none; font-family: var(--font-sans); font-size: 1rem; font-weight: 700; cursor: pointer; transition: background .18s; }
.place-order-btn:hover { background: #b8882f; }

@media (max-width: 860px) { .checkout-grid { grid-template-columns: 1fr; } .order-summary { position: static; } .option-cards { grid-template-columns: 1fr; } .form-row { grid-template-columns: 1fr; } }
</style>
@endpush

@section('content')
<div class="container checkout-page">
    <h1>Checkout</h1>

    @if($errors->any())
    <div class="flash flash-error">
        <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/></svg>
        <div><ul style="margin:0;padding-left:1rem">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
    </div>
    @endif

    <form action="{{ route('shop.checkout.process') }}" method="POST" id="checkoutForm">
    @csrf
    <div class="checkout-grid">
        <div>
            {{-- Contact --}}
            <div class="checkout-card">
                <h3>Contact Information</h3>
                <div class="form-row">
                    <div class="form-group">
                        <label>Full Name *</label>
                        <input type="text" name="name" value="{{ old('name', auth()->user()?->name) }}" required placeholder="e.g. Grace Nakamya">
                        @error('name')<p class="error-msg">{{ $message }}</p>@enderror
                    </div>
                    <div class="form-group">
                        <label>Phone *</label>
                        <input type="tel" name="phone" value="{{ old('phone') }}" required placeholder="+256 7XX XXX XXX">
                        @error('phone')<p class="error-msg">{{ $message }}</p>@enderror
                    </div>
                </div>
                <div class="form-group">
                    <label>Email *</label>
                    <input type="email" name="email" value="{{ old('email', auth()->user()?->email) }}" required placeholder="your@email.com">
                    @error('email')<p class="error-msg">{{ $message }}</p>@enderror
                </div>
            </div>

            {{-- Delivery --}}
            <div class="checkout-card">
                <h3>Delivery Method</h3>
                <div class="option-cards" id="deliveryOptions">
                    <label class="option-card selected" id="pickupCard">
                        <input type="radio" name="delivery_method" value="pickup" checked onchange="toggleDelivery('pickup')">
                        <div class="oc-title">
                            <div class="oc-check"><svg width="10" height="10" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg></div>
                            Store Pickup
                        </div>
                        <div class="oc-sub">Free · Ready within 1–2 hours</div>
                    </label>
                    <label class="option-card" id="deliveryCard">
                        <input type="radio" name="delivery_method" value="delivery" onchange="toggleDelivery('delivery')">
                        <div class="oc-title">
                            <div class="oc-check"><svg width="10" height="10" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg></div>
                            Home Delivery
                        </div>
                        <div class="oc-sub">UGX 5,000 · Same day in Kampala</div>
                    </label>
                </div>
                <div id="addressSection" style="display:none;margin-top:1rem">
                    <div class="form-group">
                        <label>Delivery Address</label>
                        <textarea name="shipping_address" rows="2" placeholder="Street, area, landmark…">{{ old('shipping_address') }}</textarea>
                        @error('shipping_address')<p class="error-msg">{{ $message }}</p>@enderror
                    </div>
                    <div class="form-group">
                        <label>City / District</label>
                        <input type="text" name="shipping_city" value="{{ old('shipping_city', 'Kampala') }}" placeholder="Kampala">
                    </div>
                </div>
            </div>

            {{-- Payment --}}
            <div class="checkout-card">
                <h3>Payment Method</h3>
                <div class="option-cards" style="grid-template-columns:1fr 1fr 1fr">
                    <label class="option-card selected" id="payCard_cod">
                        <input type="radio" name="payment_method" value="cash_on_delivery" checked onchange="selectPay(this)">
                        <div class="oc-title"><div class="oc-check"><svg width="10" height="10" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg></div> Cash on Delivery</div>
                        <div class="oc-sub">Pay when you receive</div>
                    </label>
                    <label class="option-card" id="payCard_mm">
                        <input type="radio" name="payment_method" value="mobile_money" onchange="selectPay(this)">
                        <div class="oc-title"><div class="oc-check"><svg width="10" height="10" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg></div> Mobile Money</div>
                        <div class="oc-sub">MTN / Airtel</div>
                    </label>
                    <label class="option-card" id="payCard_card">
                        <input type="radio" name="payment_method" value="card_on_delivery" onchange="selectPay(this)">
                        <div class="oc-title"><div class="oc-check"><svg width="10" height="10" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg></div> Card on Delivery</div>
                        <div class="oc-sub">Visa / Mastercard</div>
                    </label>
                </div>
                <div id="mmRef" style="display:none;margin-top:.8rem">
                    <div class="form-group" style="margin-bottom:0">
                        <label>Mobile Money Reference (optional)</label>
                        <input type="text" name="payment_reference" placeholder="Transaction ID if prepaid">
                    </div>
                </div>
            </div>

            <div class="checkout-card" style="padding:1rem 1.4rem">
                <div class="form-group" style="margin-bottom:0">
                    <label>Order Notes (optional)</label>
                    <textarea name="notes" rows="2" placeholder="Any special instructions…">{{ old('notes') }}</textarea>
                </div>
            </div>
        </div>

        {{-- SUMMARY --}}
        <div class="order-summary">
            <h3>Your Order</h3>
            @foreach($items as $item)
            <div class="os-item">
                <div class="os-img">
                    @if($item['image_path'])<img src="{{ asset($item['image_path']) }}" alt="">
                    @else<div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center"><svg width="16" height="16" fill="none" stroke="var(--border)" stroke-width="1.5" viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2"/></svg></div>
                    @endif
                </div>
                <div style="flex:1">
                    <div class="os-name">{{ $item['name'] }}</div>
                    <div class="os-qty-price">× {{ $item['qty'] }} · UGX {{ number_format($item['price'], 0) }}</div>
                </div>
                <div class="os-line-total">{{ number_format($item['line_total'], 0) }}</div>
            </div>
            @endforeach
            @php $subtotal = $items->sum('line_total'); $tax = $items->sum('line_tax'); @endphp
            <div style="margin-top:.8rem;padding-top:.8rem;border-top:1px solid var(--border)">
                <div class="sum-row"><span class="lbl">Subtotal</span><span>{{ number_format($subtotal, 0) }}</span></div>
                @if($tax > 0)<div class="sum-row"><span class="lbl">Tax</span><span>{{ number_format($tax, 0) }}</span></div>@endif
                <div class="sum-row"><span class="lbl">Delivery</span><span id="deliveryFeeDisplay" style="color:var(--success)">Free</span></div>
                <div class="sum-row grand"><span class="lbl">Total</span><span class="val" id="orderTotal">UGX {{ number_format($subtotal + $tax, 0) }}</span></div>
            </div>
            <button type="submit" class="place-order-btn">
                Place Order →
            </button>
            <p style="text-align:center;font-size:.75rem;color:var(--light);margin-top:.8rem">Secure &amp; encrypted checkout</p>
        </div>
    </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
const base = {{ $items->sum('line_total') + $items->sum('line_tax') }};

function toggleDelivery(method) {
    document.getElementById('pickupCard').classList.toggle('selected', method === 'pickup');
    document.getElementById('deliveryCard').classList.toggle('selected', method === 'delivery');
    document.getElementById('addressSection').style.display = method === 'delivery' ? 'block' : 'none';
    const fee = method === 'delivery' ? 5000 : 0;
    document.getElementById('deliveryFeeDisplay').textContent = fee > 0 ? 'UGX ' + fee.toLocaleString() : 'Free';
    document.getElementById('deliveryFeeDisplay').style.color = fee > 0 ? '' : 'var(--success)';
    document.getElementById('orderTotal').textContent = 'UGX ' + (base + fee).toLocaleString();
}

function selectPay(radio) {
    document.querySelectorAll('.option-card[id^="payCard"]').forEach(c => c.classList.remove('selected'));
    radio.closest('.option-card').classList.add('selected');
    document.getElementById('mmRef').style.display = radio.value === 'mobile_money' ? 'block' : 'none';
}
</script>
@endpush