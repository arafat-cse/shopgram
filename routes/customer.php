<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Customer\DashboardController;
use App\Http\Controllers\Customer\OrderController;
use App\Http\Controllers\Customer\ProfileController;
use App\Http\Controllers\Customer\AddressController;
use App\Http\Controllers\Customer\WishlistController;
use App\Http\Controllers\Customer\ReviewController;
use App\Http\Controllers\Customer\TicketController;
use App\Http\Controllers\Customer\ReturnController;

Route::middleware(['auth'])->prefix('customer')->name('customer.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('/password', [ProfileController::class, 'editPassword'])->name('password.edit');
    Route::put('/password', [ProfileController::class, 'updatePassword'])->name('password.update');

    // Addresses
    Route::resource('addresses', AddressController::class);
    Route::post('addresses/{address}/default', [AddressController::class, 'setDefault'])->name('addresses.default');

    // Orders
    Route::resource('orders', OrderController::class)->only(['index', 'show']);
    Route::get('/order-tracking', [OrderController::class, 'tracking'])->name('order.tracking');
    Route::post('/order-tracking', [OrderController::class, 'trackByNumber'])->name('order.track');

    // Wishlist
    Route::resource('wishlist', WishlistController::class)->only(['index', 'store', 'destroy']);

    // Reviews
    Route::resource('reviews', ReviewController::class)->only(['store', 'index']);

    // Support Tickets
    Route::resource('tickets', TicketController::class);
    Route::post('/tickets/{ticket}/reply', [TicketController::class, 'reply'])->name('tickets.reply');

    // Returns
    Route::resource('returns', ReturnController::class)->only(['index', 'store', 'show']);
});
