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
use App\Http\Controllers\Admin\ActivityLogController;
use App\Http\Controllers\Admin\ContactMessageController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\AnalyticsController;
use App\Http\Controllers\Admin\PromotedProductController;
use App\Http\Controllers\LiveChatController;

Route::middleware(['auth', 'admin.access'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::middleware('permission:analytics.view')->group(function () {
        Route::get('/analytics', [AnalyticsController::class, 'index'])->name('analytics.index');
        Route::get('/analytics/export/pdf', [AnalyticsController::class, 'exportPdf'])->name('analytics.export.pdf');
        Route::get('/analytics/export/excel', [AnalyticsController::class, 'exportExcel'])->name('analytics.export.excel');
    });

    // Products
    Route::middleware('permission:product.view')->group(function () {
        Route::post('products/{product}/duplicate', [ProductController::class, 'duplicate'])->name('products.duplicate');
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
        Route::post('orders/{order}/payment-status', [OrderController::class, 'updatePaymentStatus'])->name('orders.payment.status.update')->middleware('permission:order.payment.update');
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
        Route::post('reviews/{review}/approve', [ReviewController::class, 'approve'])->name('reviews.approve');
        Route::post('reviews/{review}/reject', [ReviewController::class, 'reject'])->name('reviews.reject');
        Route::resource('reviews', ReviewController::class)->only(['index', 'update', 'destroy']);
    });

    // Tickets
    Route::middleware('permission:ticket.view')->group(function () {
        Route::resource('tickets', TicketController::class)->only(['index', 'show']);
        Route::post('tickets/{ticket}/reply', [TicketController::class, 'reply'])->name('tickets.reply');
        Route::post('tickets/{ticket}/status', [TicketController::class, 'updateStatus'])->name('tickets.status');
        Route::resource('contact-messages', ContactMessageController::class)->only(['index', 'show', 'destroy']);
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
        Route::post('newsletter/campaigns', [NewsletterController::class, 'sendCampaign'])->name('newsletter.campaigns.send');
        Route::get('newsletter/export', [NewsletterController::class, 'export'])->name('newsletter.export');
        Route::patch('newsletter/{subscriber}/status', [NewsletterController::class, 'updateStatus'])->name('newsletter.status.update');
        Route::delete('newsletter/{subscriber}', [NewsletterController::class, 'destroy'])->name('newsletter.destroy');
        Route::delete('newsletter/campaigns/{campaign}', [NewsletterController::class, 'destroyCampaign'])->name('newsletter.campaigns.destroy');
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

    // Notifications (AJAX)
    Route::get('notifications/counts',   [NotificationController::class, 'counts'])->name('notifications.counts');
    Route::get('notifications/recent',   [NotificationController::class, 'recent'])->name('notifications.recent');
    Route::get('notifications/messages', [NotificationController::class, 'messages'])->name('notifications.messages');
    Route::post('notifications/mark-read',[NotificationController::class, 'markRead'])->name('notifications.mark-read');
    Route::post('push/subscribe',        [NotificationController::class, 'pushSubscribe'])->name('push.subscribe');
    Route::post('push/unsubscribe',      [NotificationController::class, 'pushUnsubscribe'])->name('push.unsubscribe');

    // Promoted Products
    Route::get('promoted-products',              [PromotedProductController::class, 'index'])->name('promoted.index');
    Route::post('promoted-products/{product}/toggle', [PromotedProductController::class, 'toggle'])->name('promoted.toggle');

    // Activity Log
    Route::get('activity-logs', [ActivityLogController::class, 'index'])->name('activity-logs.index');

    // Live chat admin panel
    Route::middleware('permission:order.chat')->group(function () {
        Route::get('live-chat',                         [LiveChatController::class, 'adminIndex'])->name('live-chat.index');
        Route::get('live-chat/chats',                   [LiveChatController::class, 'adminChats'])->name('live-chat.chats');
        Route::get('live-chat/unread',                  [LiveChatController::class, 'adminTotalUnread'])->name('live-chat.unread');
        Route::get('live-chat/staff-token',             [LiveChatController::class, 'adminStaffToken'])->name('live-chat.staff-token');
        Route::get('live-chat/{chat}/messages',         [LiveChatController::class, 'adminMessages'])->name('live-chat.messages');
        Route::post('live-chat/{chat}/messages',        [LiveChatController::class, 'adminStore'])->name('live-chat.store');
        Route::post('live-chat/{chat}/close',           [LiveChatController::class, 'adminClose'])->name('live-chat.close');
        Route::post('live-chat/{chat}/reopen',          [LiveChatController::class, 'adminReopen'])->name('live-chat.reopen');
        Route::post('live-chat/{chat}/assign',          [LiveChatController::class, 'adminAssign'])->name('live-chat.assign');
        Route::post('live-chat/{chat}/upload',          [LiveChatController::class, 'adminUpload'])->name('live-chat.upload');
    });
});
