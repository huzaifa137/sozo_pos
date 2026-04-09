<?php

use App\Http\Controllers\PosController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ResetPasswordController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\ForgotPasswordController;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

// Auth routes
Auth::routes();

// Redirect root → dashboard
Route::get('/', fn() => redirect()->route('reports.dashboard'));

Route::middleware(['auth'])->group(function () {

    // ── POS Terminal ──
    Route::get('/pos',                      [PosController::class, 'index'])->name('pos.terminal');
    Route::post('/pos/products/search',     [PosController::class, 'searchProducts'])->name('pos.products.search');
    Route::post('/pos/sale',                [PosController::class, 'store'])->name('pos.sale.store');
    Route::get('/pos/receipt/{sale}',       [PosController::class, 'receipt'])->name('pos.receipt');
    Route::post('/pos/sale/{sale}/void',    [PosController::class, 'void'])->name('pos.sale.void');

    // ── Inventory ──
    Route::resource('inventory', InventoryController::class);

    // ── Customers ──
    Route::resource('customers', CustomerController::class);

    // ── Users (admin only) ──
    Route::resource('users', UserController::class);
    Route::patch('/users/{user}/toggle-active', [UserController::class, 'toggleActive'])->name('users.toggle-active');

    // ── Reports ──
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/dashboard',  [ReportController::class, 'dashboard'])->name('dashboard');
        Route::get('/sales',      [ReportController::class, 'sales'])->name('sales');
        Route::get('/inventory',  [ReportController::class, 'inventory'])->name('inventory');
    });
});
