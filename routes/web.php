<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Frontend\HomeController;
use App\Http\Controllers\Frontend\ProductController as FrontProductController;
use App\Http\Controllers\Frontend\CategoryController as FrontCategoryController;
use App\Http\Controllers\Frontend\BrandController as FrontBrandController;
use App\Http\Controllers\Frontend\SearchController;
use App\Http\Controllers\Frontend\PageController as FrontPageController;
use App\Http\Controllers\Frontend\ContactController;
use App\Http\Controllers\Frontend\NewsletterController;
use App\Http\Controllers\Customer\OrderController as CustomerOrderController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;

// Public routes
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/products', [FrontProductController::class, 'index'])->name('products.index');
Route::get('/products/{slug}', [FrontProductController::class, 'show'])->name('products.show');
Route::get('/category/{slug}', [FrontCategoryController::class, 'show'])->name('category.show');
Route::get('/brand/{slug}', [FrontBrandController::class, 'show'])->name('brand.show');
Route::get('/search', [SearchController::class, 'index'])->name('search.index');
Route::get('/search/suggestions', [SearchController::class, 'suggestions'])->name('search.suggestions');
Route::get('/contact', [ContactController::class, 'index'])->name('contact.index');
Route::post('/contact', [ContactController::class, 'send'])->name('contact.send');
Route::post('/newsletter/subscribe', [NewsletterController::class, 'subscribe'])->name('newsletter.subscribe');
Route::get('/page/{slug}', [FrontPageController::class, 'show'])->name('page.show');
Route::get('/order-tracking', [CustomerOrderController::class, 'tracking'])->name('order.tracking');
Route::post('/order-tracking', [CustomerOrderController::class, 'trackByNumber'])->name('order.track');

// Auth routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.post');
    Route::get('/forgot-password', [PasswordController::class, 'showForgot'])->name('password.request');
    Route::post('/forgot-password', [PasswordController::class, 'sendReset'])->name('password.email');
    Route::get('/reset-password/{token}', [PasswordController::class, 'showReset'])->name('password.reset');
    Route::post('/reset-password', [PasswordController::class, 'resetPassword'])->name('password.update');
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Cart routes (auth required)
Route::middleware('auth')->group(function () {
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
    Route::post('/cart/update', [CartController::class, 'update'])->name('cart.update');
    Route::delete('/cart/remove/{id}', [CartController::class, 'remove'])->name('cart.remove');
    Route::post('/cart/coupon/apply', [CartController::class, 'applyCoupon'])->name('cart.coupon.apply');
    Route::post('/cart/coupon/remove', [CartController::class, 'removeCoupon'])->name('cart.coupon.remove');

    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout/place-order', [CheckoutController::class, 'placeOrder'])->name('checkout.place-order');
    Route::get('/checkout/success/{order}', [CheckoutController::class, 'success'])->name('checkout.success');
});

// Customer dashboard routes
require __DIR__.'/customer.php';

// Admin routes
require __DIR__.'/admin.php';
