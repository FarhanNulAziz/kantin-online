<?php

use App\Http\Controllers\Admin\CanteenStatusController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\MenuController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', [CatalogController::class, 'index'])->name('catalog');

Route::prefix('cart')->name('cart.')->group(function () {
    Route::get('/', [CartController::class, 'index'])->name('index');
    Route::post('/add/{menu}', [CartController::class, 'add'])->name('add');
    Route::patch('/order-type', [CartController::class, 'updateOrderType'])->name('order-type');
    Route::patch('/{menu}', [CartController::class, 'update'])->name('update');
    Route::delete('/{menu}', [CartController::class, 'remove'])->name('remove');
    Route::delete('/', [CartController::class, 'clear'])->name('clear');
});

Route::get('/dashboard', fn() => request()->user()->isAdmin()
    ? redirect()->route('admin.dashboard')
    : redirect()->route('catalog'))
    ->middleware('auth')
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::patch('canteen-status', [CanteenStatusController::class, 'update'])->name('canteen-status.update');

    Route::resource('menus', MenuController::class)->except('show');
    Route::patch('menus/{menu}/toggle', [MenuController::class, 'toggle'])->name('menus.toggle');
});
