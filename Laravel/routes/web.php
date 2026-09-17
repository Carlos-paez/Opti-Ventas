<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PosController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SalesController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// Welcome / redirect to dashboard if logged in
Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }

    return redirect()->route('login');
});

// Require authenticated users for everything below
Route::middleware('auth')->group(function () {

    // Dashboard
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // POS (Punto de Venta)
    Route::get('pos', [PosController::class, 'index'])->name('pos.index');
    Route::post('pos/sale', [PosController::class, 'store'])->name('pos.store');

    // Categories
    Route::resource('categories', CategoryController::class)->except('show');

    // Products
    Route::resource('products', ProductController::class)->except('show');

    // Customers
    Route::resource('customers', CustomerController::class)->except('show');

    // Sales
    Route::get('sales', [SalesController::class, 'index'])->name('sales.index');
    Route::get('sales/{sale}', [SalesController::class, 'show'])->name('sales.show');
    Route::post('sales/{sale}/void', [SalesController::class, 'voidSale'])->name('sales.void');

    // Reports
    Route::get('reports', [ReportController::class, 'index'])->name('reports.index');

    // Settings (admin only)
    Route::middleware('admin')->group(function () {
        Route::get('settings', [SettingController::class, 'index'])->name('settings.index');
        Route::put('settings', [SettingController::class, 'update'])->name('settings.update');
        Route::resource('users', UserController::class)->except('show');
        Route::post('users/{user}/toggle-active', [UserController::class, 'toggleActive'])->name('users.toggle-active');
    });

    // Profile (from Breeze)
    Route::get('profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
