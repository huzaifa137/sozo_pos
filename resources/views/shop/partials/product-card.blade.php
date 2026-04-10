<div class="prod-card">
    <div class="prod-img-wrap">
        @if($product->image_path)
            <img src="{{ asset($product->image_path) }}" alt="{{ $product->name }}" loading="lazy">
        @else
            <div class="prod-img-placeholder">
                <svg width="38" height="38" fill="none" stroke="currentColor" stroke-width="1.3" viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
            </div>
        @endif
        @if($product->featured)<span class="prod-tag">Featured</span>@endif
        @if($product->quantity <= 3 && $product->quantity > 0)<span class="prod-tag" style="background:var(--danger)">Last {{ $product->quantity }}</span>@endif
        <button class="prod-wishlist" onclick="event.preventDefault()">
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
        </button>
    </div>
    <div class="prod-body">
        <div class="prod-cat">{{ \App\Http\Controllers\InventoryController::CATEGORIES[$product->category] ?? $product->category }}</div>
        <a href="{{ $product->slug ? route('shop.product', $product->slug) : '#' }}" class="prod-name">
            {{ $product->name }}
        </a>
        <div class="prod-footer">
            <div>
                <div class="prod-price">UGX {{ number_format($product->selling_price, 0) }}</div>
                @if($product->isLowStock())<div class="prod-stock-low">Only {{ $product->quantity }} left</div>@endif
            </div>
            <form action="{{ route('shop.cart.add') }}" method="POST">
                @csrf
                <input type="hidden" name="product_id" value="{{ $product->id }}">
                <input type="hidden" name="qty" value="1">
                <button type="submit" class="prod-add-btn" title="Add to cart">
                    <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></svg>
                </button>
            </form>
        </div>
    </div>
</div>