# ShopGram — Project Structure

**Stack:** Laravel 12, Blade, MySQL, Bootstrap 5, jQuery  
**Last updated:** 2026-06-24 (notifications + activity log added)

---

## app/

### Http/Controllers/

#### Admin/
- `ActivityLogController.php`
- `AdminUserController.php`
- `BannerController.php`
- `BrandController.php`
- `CategoryController.php`
- `ContactMessageController.php`
- `CouponController.php`
- `CourierController.php`
- `CustomerController.php`
- `DashboardController.php`
- `InventoryController.php`
- `NewsletterController.php`
- `NotificationController.php`
- `OrderController.php`
- `PageController.php`
- `PaymentController.php`
- `ProductController.php`
- `ProductVariantController.php`
- `ReportController.php`
- `ReviewController.php`
- `RoleController.php`
- `SettingController.php`
- `ShippingZoneController.php`
- `TicketController.php`

#### Auth/
- `AuthController.php`
- `PasswordController.php`

#### Customer/
- `AddressController.php`
- `DashboardController.php`
- `OrderController.php`
- `ProfileController.php`
- `ReturnController.php`
- `ReviewController.php`
- `TicketController.php`
- `WishlistController.php`

#### Frontend/
- `BrandController.php`
- `CategoryController.php`
- `ContactController.php`
- `HomeController.php`
- `NewsletterController.php`
- `PageController.php`
- `ProductController.php`
- `SearchController.php`

#### Root
- `CartController.php`
- `CheckoutController.php`
- `Controller.php`

### Http/Middleware/
- `AdminAccessMiddleware.php`
- `MaintenanceModeMiddleware.php`

### Http/Requests/
- `CheckoutRequest.php`
- `ProductRequest.php`
- `ProfileUpdateRequest.php`
- `CouponApplyRequest.php`

### Models/
- `Address.php`
- `AdminActivityLog.php`
- `Banner.php`
- `Brand.php`
- `CartItem.php`
- `Category.php`
- `ContactMessage.php`
- `Coupon.php`
- `CouponUsage.php`
- `Courier.php`
- `Newsletter.php`
- `Order.php`
- `OrderItem.php`
- `OrderStatusHistory.php`
- `Page.php`
- `Payment.php`
- `Product.php`
- `ProductImage.php`
- `ProductVariant.php`
- `ReturnRequest.php`
- `Review.php`
- `Setting.php`
- `ShippingZone.php`
- `StockHistory.php`
- `SupportTicket.php`
- `TicketReply.php`
- `User.php`
- `Wishlist.php`

### Notifications/
- `OrderPlacedNotification.php`
- `OrderStatusUpdatedNotification.php`
- `LowStockAlertNotification.php`

### Policies/
- `OrderPolicy.php`
- `TicketPolicy.php`

### Providers/
- `AppServiceProvider.php`

### Services/
- `ActivityLogService.php`
- `CartService.php`
- `CouponService.php`
- `InventoryService.php`
- `OrderService.php`
- `PendingActionService.php`
- `WishlistService.php`
- `ReportService.php`

---

## database/

### migrations/
- `0001_01_01_000000_create_users_table.php`
- `0001_01_01_000001_create_cache_table.php`
- `0001_01_01_000002_create_jobs_table.php`
- `2026_06_23_..._create_permission_tables.php`
- `2026_06_23_..._create_brands_table.php`
- `2026_06_23_..._create_categories_table.php`
- `2026_06_23_..._create_coupons_table.php`
- `2026_06_23_..._create_couriers_table.php`
- `2026_06_23_..._create_products_table.php`
- `2026_06_23_..._create_shipping_zones_table.php`
- `2026_06_23_..._create_addresses_table.php`
- `2026_06_23_..._create_product_images_table.php`
- `2026_06_23_..._create_product_variants_table.php`
- `2026_06_23_..._create_cart_items_table.php`
- `2026_06_23_..._create_orders_table.php`
- `2026_06_23_..._create_wishlists_table.php`
- `2026_06_23_..._create_banners_table.php`
- `2026_06_23_..._create_coupon_usages_table.php`
- `2026_06_23_..._create_order_items_table.php`
- `2026_06_23_..._create_order_status_histories_table.php`
- `2026_06_23_..._create_payments_table.php`
- `2026_06_23_..._create_reviews_table.php`
- `2026_06_23_..._create_newsletters_table.php`
- `2026_06_23_..._create_pages_table.php`
- `2026_06_23_..._create_settings_table.php`
- `2026_06_23_..._create_stock_histories_table.php`
- `2026_06_23_..._create_support_tickets_table.php`
- `2026_06_23_..._create_ticket_replies_table.php`
- `2026_06_23_..._create_return_requests_table.php`
- `2026_06_24_..._create_contact_messages_table.php`
- `2026_06_24_100001_create_admin_activity_logs_table.php`

### seeders/
- `DatabaseSeeder.php`
- `AdminUserSeeder.php`
- `CategorySeeder.php`
- `ProductSeeder.php`
- `RolePermissionSeeder.php`
- `SettingSeeder.php`
- `ShippingZoneSeeder.php`
- `BrandSeeder.php`

### factories/
- `UserFactory.php`

---

## resources/views/

### layouts/
- `admin.blade.php`
- `app.blade.php`
- `customer.blade.php`

### components/
- `alert.blade.php`
- `breadcrumb.blade.php`
- `confirm-delete.blade.php`
- `confirm-modal.blade.php`
- `confirm-toast.blade.php`
- `delete-button.blade.php`
- `order-status-badge.blade.php`
- `product-card.blade.php`
- `star-rating.blade.php`
- `toast.blade.php`

### auth/
- `forgot-password.blade.php`
- `login.blade.php`
- `register.blade.php`
- `reset-password.blade.php`

### frontend/
- `cart/index.blade.php`
- `checkout/index.blade.php`
- `checkout/success.blade.php`
- `home/index.blade.php`
- `pages/contact.blade.php`
- `pages/show.blade.php`
- `products/index.blade.php`
- `products/show.blade.php`
- `search/index.blade.php`

### customer/
- `dashboard.blade.php`
- `addresses/create.blade.php`
- `addresses/edit.blade.php`
- `addresses/index.blade.php`
- `orders/index.blade.php`
- `orders/show.blade.php`
- `orders/tracking.blade.php`
- `profile/edit.blade.php`
- `profile/password.blade.php`
- `returns/create.blade.php`
- `returns/index.blade.php`
- `reviews/index.blade.php`
- `tickets/create.blade.php`
- `tickets/index.blade.php`
- `tickets/show.blade.php`
- `wishlist/index.blade.php`

### admin/
- `admin-users/create.blade.php`
- `admin-users/edit.blade.php`
- `admin-users/index.blade.php`
- `banners/create.blade.php`
- `banners/edit.blade.php`
- `banners/index.blade.php`
- `brands/create.blade.php`
- `brands/edit.blade.php`
- `brands/index.blade.php`
- `categories/create.blade.php`
- `categories/edit.blade.php`
- `categories/index.blade.php`
- `contact-messages/index.blade.php`
- `contact-messages/show.blade.php`
- `coupons/create.blade.php`
- `coupons/edit.blade.php`
- `coupons/index.blade.php`
- `couriers/create.blade.php`
- `couriers/edit.blade.php`
- `couriers/index.blade.php`
- `customers/index.blade.php`
- `customers/show.blade.php`
- `dashboard/index.blade.php`
- `inventory/history.blade.php`
- `inventory/index.blade.php`
- `newsletter/index.blade.php`
- `orders/index.blade.php`
- `orders/invoice.blade.php`
- `orders/show.blade.php`
- `pages/create.blade.php`
- `pages/edit.blade.php`
- `pages/index.blade.php`
- `payments/index.blade.php`
- `products/create.blade.php`
- `products/edit.blade.php`
- `products/index.blade.php`
- `reports/index.blade.php`
- `reports/sales.blade.php`
- `reports/orders.blade.php`
- `reports/products.blade.php`
- `reports/customers.blade.php`
- `reports/stock.blade.php`
- `reports/payments.blade.php`
- `reports/coupons.blade.php`
- `reviews/index.blade.php`
- `roles/create.blade.php`
- `roles/edit.blade.php`
- `roles/index.blade.php`
- `settings/index.blade.php`
- `shipping-zones/create.blade.php`
- `shipping-zones/edit.blade.php`
- `shipping-zones/index.blade.php`
- `activity-logs/index.blade.php`
- `tickets/index.blade.php`
- `tickets/show.blade.php`

### Root views
- `maintenance.blade.php`
- `welcome.blade.php`

---

## routes/
- `admin.php`
- `console.php`
- `customer.php`
- `web.php`

---

## Status

All spec files implemented. No known gaps.

## Recent Additions (2026-06-24)

| Item | Type | Purpose |
|------|------|---------|
| `ActivityLogController.php` | Controller | Admin activity log page |
| `NotificationController.php` | Controller | Bell + chat AJAX endpoints |
| `AdminActivityLog.php` | Model | Activity log entries |
| `ActivityLogService.php` | Service | Static log helpers, wired into Order/Product/Customer controllers |
| `admin_activity_logs` migration | Migration | DB table (migrated) |
| `admin/activity-logs/index.blade.php` | View | Filterable log table |
| Admin layout bell + chat icons | Layout | AJAX polling notification bell + unread messages icon in top bar |
| Activity Log sidebar link | Layout | System section → Activity Log |
