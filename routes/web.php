<?php

use App\Http\Controllers\PosController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\StockBatchController;
use App\Http\Controllers\SubcategoryController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Public shop routes (landing page = store)
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('reports.dashboard')
        : redirect()->route('login');
});

// ── E-Commerce Storefront (public) ──
// Route::get('/', [ShopController::class, 'index'])->name('shop.index');
Route::get('/shop', [ShopController::class, 'catalog'])->name('shop.catalog');
Route::get('/shop/product/{slug}', [ShopController::class, 'product'])->name('shop.product');

// Cart (session, no auth required)
Route::get('/cart', [ShopController::class, 'cartView'])->name('shop.cart');
Route::post('/cart/add', [ShopController::class, 'cartAdd'])->name('shop.cart.add');
Route::post('/cart/update', [ShopController::class, 'cartUpdate'])->name('shop.cart.update');
Route::post('/cart/remove', [ShopController::class, 'cartRemove'])->name('shop.cart.remove');
Route::get('/cart/count', [ShopController::class, 'cartCount'])->name('shop.cart.count');

// Checkout
Route::get('/checkout', [ShopController::class, 'checkoutView'])->name('shop.checkout');
Route::post('/checkout', [ShopController::class, 'checkoutProcess'])->name('shop.checkout.process');
Route::get('/order/{orderNumber}/confirmation', [ShopController::class, 'orderConfirmation'])->name('shop.order.confirmation');

/*
|--------------------------------------------------------------------------
| Auth routes
|--------------------------------------------------------------------------
*/
Auth::routes();

/*
|--------------------------------------------------------------------------
| Authenticated routes (POS staff + customers)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {

    // ── Customer account portal ──
    Route::get('/account/orders', [ShopController::class, 'accountOrders'])->name('account.orders');
    Route::get('/account/orders/{order}', [ShopController::class, 'accountOrderDetail'])->name('account.order.detail');

    // ── POS staff routes ──
    Route::middleware([])->group(function () {

        // Dashboard
        Route::get('/dashboard', [ReportController::class, 'dashboard'])->name('reports.dashboard');

        // POS Terminal
        Route::get('/pos', [PosController::class, 'index'])->name('pos.terminal');
        Route::post('/pos/products/load', [PosController::class, 'loadProducts'])->name('pos.products.load');
        Route::post('/pos/products/search', [PosController::class, 'searchProducts'])->name('pos.products.search');
        Route::post('/pos/sale', [PosController::class, 'store'])->name('pos.sale.store');
        Route::get('/pos/receipt/{sale}', [PosController::class, 'receipt'])->name('pos.receipt');
        Route::post('/pos/sale/{sale}/void', [PosController::class, 'void'])->name('pos.sale.void');

        // Inventory
        Route::resource('inventory', InventoryController::class);

        // Online Orders
        Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
        Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
        Route::patch('/orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.status');

        // Customers
        Route::resource('customers', CustomerController::class);

        // Users (admin only handled inside controller)
        Route::resource('users', UserController::class);
        Route::patch('/users/{user}/toggle-active', [UserController::class, 'toggleActive'])->name('users.toggle-active');

        // ── Reports ──
        Route::prefix('reports')->name('reports.')->group(function () {
            Route::get('/dashboard', [ReportController::class, 'dashboard'])->name('dashboard');
            Route::get('/sales', [ReportController::class, 'sales'])->name('sales');
            Route::get('/inventory', [ReportController::class, 'inventory'])->name('inventory');
        });

        // ── Settings: Categories ──
        Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
        Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
        Route::put('/categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
        Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');

        // ── Settings: Stock Batches ──
        Route::get('/stock-batches', [StockBatchController::class, 'index'])->name('stock-batches.index');
        Route::post('/stock-batches', [StockBatchController::class, 'store'])->name('stock-batches.store');
        Route::put('/stock-batches/{stockBatch}', [StockBatchController::class, 'update'])->name('stock-batches.update');
        Route::delete('/stock-batches/{stockBatch}', [StockBatchController::class, 'destroy'])->name('stock-batches.destroy');

        // ── Settings: Subcategories ──
        Route::get('/subcategories', [SubcategoryController::class, 'index'])->name('subcategories.index');
        Route::post('/subcategories', [SubcategoryController::class, 'store'])->name('subcategories.store');
        Route::put('/subcategories/{subcategory}', [SubcategoryController::class, 'update'])->name('subcategories.update');
        Route::delete('/subcategories/{subcategory}', [SubcategoryController::class, 'destroy'])->name('subcategories.destroy');

    });
});