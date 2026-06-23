<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ProductVariantController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\BrandController;
use App\Http\Controllers\Admin\InventoryController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\PaymentController;
use App\Http\Controllers\Admin\CouponController;
use App\Http\Controllers\Admin\ShippingZoneController;
use App\Http\Controllers\Admin\CourierController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\ReviewController;
use App\Http\Controllers\Admin\TicketController;
use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\PageController;
use App\Http\Controllers\Admin\NewsletterController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\AdminUserController;

Route::middleware(['auth', 'admin.access'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Products
    Route::middleware('permission:product.view')->group(function () {
        Route::resource('products', ProductController::class);
        Route::post('products/{product}/images', [ProductController::class, 'uploadImages'])->name('products.images.upload');
        Route::delete('products/images/{image}', [ProductController::class, 'deleteImage'])->name('products.images.delete');
        Route::resource('products.variants', ProductVariantController::class);
    });

    // Categories
    Route::middleware('permission:category.manage')->group(function () {
        Route::resource('categories', CategoryController::class);
    });

    // Brands
    Route::middleware('permission:brand.manage')->group(function () {
        Route::resource('brands', BrandController::class);
    });

    // Inventory
    Route::middleware('permission:inventory.manage')->group(function () {
        Route::get('inventory', [InventoryController::class, 'index'])->name('inventory.index');
        Route::post('inventory/stock-in', [InventoryController::class, 'stockIn'])->name('inventory.stock-in');
        Route::post('inventory/stock-out', [InventoryController::class, 'stockOut'])->name('inventory.stock-out');
        Route::post('inventory/adjust', [InventoryController::class, 'adjust'])->name('inventory.adjust');
        Route::get('inventory/history/{product}', [InventoryController::class, 'history'])->name('inventory.history');
    });

    // Orders
    Route::middleware('permission:order.view')->group(function () {
        Route::resource('orders', OrderController::class)->only(['index', 'show', 'update']);
        Route::post('orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.status.update');
        Route::post('orders/{order}/assign-courier', [OrderController::class, 'assignCourier'])->name('orders.courier.assign');
        Route::get('orders/{order}/invoice', [OrderController::class, 'invoice'])->name('orders.invoice');
        Route::get('orders/{order}/invoice/pdf', [OrderController::class, 'invoicePdf'])->name('orders.invoice.pdf');
    });

    // Payments
    Route::get('payments', [PaymentController::class, 'index'])->name('payments.index');
    Route::post('payments/{payment}/status', [PaymentController::class, 'updateStatus'])->name('payments.status.update');

    // Coupons
    Route::middleware('permission:coupon.manage')->group(function () {
        Route::resource('coupons', CouponController::class);
    });

    // Shipping
    Route::resource('shipping-zones', ShippingZoneController::class);
    Route::resource('couriers', CourierController::class);

    // Customers
    Route::middleware('permission:customer.view')->group(function () {
        Route::resource('customers', CustomerController::class)->only(['index', 'show', 'update']);
        Route::post('customers/{customer}/toggle-status', [CustomerController::class, 'toggleStatus'])->name('customers.toggle-status');
    });

    // Reviews
    Route::middleware('permission:review.manage')->group(function () {
        Route::resource('reviews', ReviewController::class)->only(['index', 'update', 'destroy']);
    });

    // Tickets
    Route::middleware('permission:ticket.view')->group(function () {
        Route::resource('tickets', TicketController::class)->only(['index', 'show']);
        Route::post('tickets/{ticket}/reply', [TicketController::class, 'reply'])->name('tickets.reply');
        Route::post('tickets/{ticket}/status', [TicketController::class, 'updateStatus'])->name('tickets.status');
    });

    // Banners
    Route::middleware('permission:banner.manage')->group(function () {
        Route::resource('banners', BannerController::class);
    });

    // Pages
    Route::middleware('permission:page.manage')->group(function () {
        Route::resource('pages', PageController::class);
    });

    // Newsletter
    Route::middleware('permission:newsletter.manage')->group(function () {
        Route::get('newsletter', [NewsletterController::class, 'index'])->name('newsletter.index');
        Route::get('newsletter/export', [NewsletterController::class, 'export'])->name('newsletter.export');
        Route::delete('newsletter/{subscriber}', [NewsletterController::class, 'destroy'])->name('newsletter.destroy');
    });

    // Reports
    Route::middleware('permission:report.view')->group(function () {
        Route::get('reports/sales', [ReportController::class, 'sales'])->name('reports.sales');
        Route::get('reports/orders', [ReportController::class, 'orders'])->name('reports.orders');
        Route::get('reports/products', [ReportController::class, 'products'])->name('reports.products');
        Route::get('reports/customers', [ReportController::class, 'customers'])->name('reports.customers');
        Route::get('reports/stock', [ReportController::class, 'stock'])->name('reports.stock');
        Route::get('reports/payments', [ReportController::class, 'payments'])->name('reports.payments');
        Route::get('reports/coupons', [ReportController::class, 'coupons'])->name('reports.coupons');
        Route::get('reports/{type}/export', [ReportController::class, 'export'])->name('reports.export');
    });

    // Settings
    Route::middleware('permission:setting.manage')->group(function () {
        Route::get('settings', [SettingController::class, 'index'])->name('settings.index');
        Route::post('settings', [SettingController::class, 'update'])->name('settings.update');
    });

    // Roles
    Route::middleware('permission:role.manage')->group(function () {
        Route::resource('roles', RoleController::class);
        Route::post('roles/{role}/permissions', [RoleController::class, 'updatePermissions'])->name('roles.permissions.update');
        Route::resource('admin-users', AdminUserController::class);
        Route::post('admin-users/{user}/role', [AdminUserController::class, 'assignRole'])->name('admin-users.role.assign');
    });
});
