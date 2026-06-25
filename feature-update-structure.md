# ShopGram — Feature Update Roadmap

**Goal:** Smarter UX, higher conversion, better admin control  
**Last updated:** 2026-06-25

---

## 🎯 IMPLEMENT NEXT — Best ROI (Ordered by impact ÷ effort)

| # | Feature | Why best now | Est. effort |
|---|---------|-------------|-------------|
| 1 | **Mini cart drawer** | Biggest UX win. Customer adds item → stays on page → buys more | 2 days |
| 4 | **Flash sale countdown timer** | Bangladeshi shoppers respond strongly to urgency. `sale_ends_at` column + JS | 2 days |
| 5 | **Abandoned cart email** | Recovers ~15% of lost carts automatically. Scheduled job + mail | 2 days |
| 6 | **Flash sale manager** | Admin-controlled deals = conversion engine. Full tables needed | 3 days |
| 7 | **Product quick view modal** | Keeps customer on listing page → more products seen → more buys | 3 days |
| 8 | **Loyalty points system** | Retention goldmine. Customers come back to spend points | 5 days |

> **Start with 1–2 today** — zero backend work needed.

---

## Priority Legend
- 🔴 High impact — implement first
- 🟡 Medium impact — implement next
- 🟢 Nice to have — when time allows

---

## FRONTEND / CUSTOMER UX

### 🔴 Conversion Boosters

| Feature | What it does | Implementation hint |
|---------|-------------|---------------------|
| **Mini cart drawer** | Slide-in cart from right when "Add to Cart" clicked — no page reload | JS + Blade partial + AJAX |
| **Product quick view modal** | Click product card → modal with image, price, variant, add-to-cart — stays on listing page | Bootstrap modal + AJAX route `/products/{slug}/quick-view` |
| **Flash sale countdown timer** | Countdown on sale products with end time | JS `setInterval` + `sale_ends_at` column in products |
| **"Notify me when in stock"** | Guest/customer subscribes to out-of-stock product — email when restocked | `stock_notifications` table → trigger in InventoryService |

### 🔴 Product Discovery

| Feature | What it does | Implementation hint |
|---------|-------------|---------------------|
| **"Customers also bought"** | Related products based on order co-occurrence | Query: other products in orders that contain current product |
| **Color/size swatch selector** | Visual buttons for variants instead of dropdown | Render variants as clickable swatches, update price/stock via JS |
| **Compare products** | Select up to 3 products, compare specs side by side | `compare_ids[]` session + `/compare` page |

### 🟡 Search & Filter

| Feature | What it does | Implementation hint |
|---------|-------------|---------------------|
| **Price range slider** | Drag slider to filter price — replaces min/max inputs | noUiSlider JS + query params |
| **AJAX filter (no page reload)** | Filter/sort products without full page reload | fetch() → replace product grid HTML partial |
| **Search history** | Show last searches in search dropdown | localStorage |
| **Voice search** | Mic icon → browser speech API → fill search | Web Speech API (browser native, no backend needed) |

### 🟡 Trust & Social Proof

| Feature | What it does | Implementation hint |
|---------|-------------|---------------------|
| ~~**"X people viewing this"**~~ | ✅ Done — seeded JS counter on product page | — |
| ~~**"Y sold in last 24h"**~~ | ✅ Done — real query from `order_items`, shown on product page | — |
| **Review photos** | Customers upload images with reviews | Add `review_images` table, image upload in ReviewController |
| **Verified purchase badge** | Badge on reviews from confirmed buyers | Check if reviewer has delivered order containing product |
| **Q&A section on product page** | Customer asks question, admin answers | `product_questions` + `product_answers` tables |

### 🟡 Customer Dashboard Enhancements

| Feature | What it does | Implementation hint |
|---------|-------------|---------------------|
| **Wishlist → share link** | Share wishlist URL with others | Generate token, public route `/wishlist/{token}` |
| **Wishlist → share link** | Share wishlist URL with others | Generate token, public route `/wishlist/{token}` |
| **Loyalty points display** | Show earned points in dashboard | `loyalty_points` column on users table |

### 🟢 Mobile UX

| Feature | What it does | Implementation hint |
|---------|-------------|---------------------|
| **PWA support** | Add to home screen, offline page | `manifest.json` + service worker + `offline.blade.php` |
| **Sticky add-to-cart bar** | Bar appears when product's ATC button scrolls out of view | IntersectionObserver JS |
| **Swipe gestures on product gallery** | Swipe left/right between images on mobile | Swiper.js (already likely in project) |
| **Bottom sheet filters** | Mobile filter as bottom drawer (spec mentions this) | Bootstrap offcanvas bottom |

---

## BACKEND / ADMIN

### 🔴 Productivity

| Feature | What it does | Implementation hint |
|---------|-------------|---------------------|
| **Bulk order status update** | Check multiple orders → change status all at once | Checkbox datatable + `POST /admin/orders/bulk-status` |
| **CSV product import** | Upload CSV to create/update products in bulk | `maatwebsite/excel` already installed |
| **Scheduled product publish** | Set `published_at` date — product goes live automatically | `published_at` column + `where('published_at', '<=', now())` scope |

### 🔴 Smart Inventory

| Feature | What it does | Implementation hint |
|---------|-------------|---------------------|
| **Auto low-stock email to admin** | Send `LowStockAlertNotification` when stock hits threshold during order | Trigger in `InventoryService::deductStock()` — `LowStockAlertNotification` class exists, wire it up |
| **Inventory forecast** | Show "At current sell rate, stock runs out in X days" | Avg daily sales from `order_items` ÷ current stock |
| **Restock purchase order** | Admin creates purchase order (PO) to track incoming stock | `purchase_orders` + `purchase_order_items` tables |

### 🟡 Order Intelligence

| Feature | What it does | Implementation hint |
|---------|-------------|---------------------|
| **Abandoned cart list** | Admin sees carts not converted to order in 24h+ | Query `cart_items` grouped by user, older than 24h, no corresponding order |
| **Abandoned cart email** | Auto-email customer after 1h of abandonment | Laravel scheduled job + queued mail |
| **Order fraud flag** | Flag orders with multiple failed payments or rapid duplicate orders | Simple rule engine in OrderService |
| **Bulk invoice print** | Select orders → print all invoices at once | JS `window.print()` on multi-invoice page |

### 🟡 Customer Intelligence

| Feature | What it does | Implementation hint |
|---------|-------------|---------------------|
| **Customer segments** | Tag customers: VIP (spent > X), New, At Risk (no order in 60d) | Computed from order history, show in customer list |
| **Customer lifetime value** | Show total spent, avg order, first/last order date on customer detail | Aggregate from `orders` table |
| **Block/unblock with reason** | Store reason when blocking customer | `blocked_reason` + `blocked_at` columns on users |

### 🟡 Marketing Tools

| Feature | What it does | Implementation hint |
|---------|-------------|---------------------|
| **Flash sale manager** | Create sale with % off, start/end time, specific products/categories | `flash_sales` + `flash_sale_products` tables |
| **Loyalty points system** | Earn points per ৳ spent, redeem as discount | `loyalty_points` on users, `point_transactions` table, redeem at checkout |
| **Referral system** | Customer gets unique link, earns credit when referral buys | `referral_code` on users, `referral_conversions` table |
| **Newsletter campaigns** | Admin composes email, sends to all subscribers | Queue-based bulk mail using `Newsletter` model |
| **Pop-up / exit-intent offer** | Show coupon popup when user is about to leave | JS `mouseleave` on document + session flag |

### 🟢 Admin UX

| Feature | What it does | Implementation hint |
|---------|-------------|---------------------|
| **Dark mode for admin** | Toggle dark/light in admin panel | CSS variables + `localStorage` preference |
| **Admin quick search** | Press `/` anywhere in admin → search orders, products, customers | JS modal + AJAX |
| **Keyboard shortcuts** | `N` = new product, `O` = orders, `ESC` = close modal | JS `keydown` listener |

---

## SMART / AI FEATURES

| Feature | What it does | Notes |
|---------|-------------|-------|
| **Smart search (typo-tolerant)** | "samsubng" finds Samsung products | Laravel Scout + Meilisearch (self-hosted, free) |
| **Auto SEO generator** | Fill SEO title/description from product name + category automatically | Simple template: `"{name} — Buy Online in Bangladesh"` |
| **Smart product tagging** | Auto-suggest tags based on product name | Keyword extraction from name/description |
| **Related products (ML-style)** | "Bought together" based on real order co-occurrence | SQL: products co-purchased in same orders, weighted by frequency |

---

## PERFORMANCE & TECHNICAL

| Feature | What it does | Notes |
|---------|-------------|-------|
| **Redis cache for product listing** | Cache product queries for 5 min — huge speed boost | `CACHE_DRIVER=redis`, wrap queries in `Cache::remember()` |
| **Image WebP conversion on upload** | Auto-convert uploaded images to WebP | `spatie/image` package |
| **Rate limiting on cart/checkout** | Prevent checkout spam/bots | Laravel `RateLimiter` in `RouteServiceProvider` |
| **2FA for admin login** | Extra security for admin panel | `pragmarx/google2fa-laravel` |
| **Sitemap auto-generation** | SEO: auto sitemap.xml for all products/categories | `spatie/laravel-sitemap` |
| **robots.txt management** | Control crawler access from admin settings | Setting key + served via route |

---

## DATABASE ADDITIONS NEEDED

For above features, new tables required:

```
flash_sales          — id, name, discount_percent, starts_at, ends_at, status
flash_sale_products  — flash_sale_id, product_id
stock_notifications  — id, user_id, product_id, notified_at (nullable)
product_questions    — id, product_id, user_id, question, status
product_answers      — id, question_id, user_id, answer
loyalty_points       — column on users table: points INT default 0
point_transactions   — id, user_id, type(earn/redeem), points, note, order_id, created_at
referral_codes       — column on users: referral_code (unique)
referral_conversions — id, referrer_id, referee_id, order_id, credit_amount
purchase_orders      — id, supplier_name, status, expected_at, note, created_by
purchase_order_items — id, po_id, product_id, variant_id, qty, unit_cost
```

---

## ✅ ALREADY IMPLEMENTED

| Feature | Where |
|---------|-------|
| **Admin activity log** | `admin_activity_logs` table · `ActivityLogController` · route `activity-logs.index` · logged in all admin controllers |
| **Dashboard notifications bell** | `NotificationController` · polling `/admin/notifications/counts` + `/recent` · bell icon + badge in `layouts/admin.blade.php` |
| **CSV/Excel export** (orders, customers, products, stock, analytics, newsletter) | `ReportController@export` · `AnalyticsController@exportExcel/Pdf` · `reports/{type}/export` routes |
| **Analytics module** | `AnalyticsController` · `AnalyticsService` · full analytics dashboard with export |
| **Recently viewed products** | `RecentlyViewedProductService` · shown on homepage |
| **Wishlist** (add/remove/view) | `WishlistController` · `WishlistService` · customer dashboard |
| **Product duplication** | Admin product create with `duplicate_product_id` → copies product + gallery + variants |
| **Image lazy loading** | `loading="lazy"` on `product-card.blade.php` |
| **Hero banner redesign + urgency** | 3 themed fallback slides (Flash Sale/New Arrivals/Deals) · real-time midnight countdown · date badges · `frontend/home/index.blade.php` |
| **First-visit promotional popup** | `is_promoted` on products · `/api/promoted-products` · Bootstrap modal + JS slider · `layouts/app.blade.php` |
| **Password visibility toggle** | Eye icon on login + register forms · `auth/login.blade.php` · `auth/register.blade.php` |
| **Auto low-stock email** | `InventoryService::checkLowStock()` → `LowStockAlertNotification` → admins notified on stock-out |
| **Reorder button** | `Customer/OrderController@reorder` · `POST customer/orders/{order}/reorder` · buttons on orders index + show |
| **Customer invoice PDF** | `Customer/OrderController@invoicePdf` · `GET customer/orders/{order}/invoice/pdf` · download button on orders show |
| **Admin order invoice PDF** | `OrderController@invoicePdf` · route `admin.orders.invoice.pdf` |
| **Social proof badges** | "X viewing now" (seeded JS) + "Y sold in 24h" (real query) · `frontend/products/show.blade.php` |
| **Sticky add-to-cart bar** | `IntersectionObserver` hides/shows bar when main ATC scrolls out · `frontend/products/show.blade.php` |

---

## RECOMMENDED IMPLEMENTATION ORDER

```
Phase A (Quick wins — 1-2 days each):
  1. Reorder button (customer)
  2. Auto low-stock email — wire LowStockAlertNotification into InventoryService::stockOut()
  3. Customer invoice download (customer-side route, class already exists)
  4. Image lazy loading — add loading="lazy" to product-card.blade.php

Phase B (Medium effort — 3-5 days each):
  5. Mini cart drawer (AJAX)
  6. Product quick view modal
  7. Bulk order status update
  8. Abandoned cart list + email
  9. Flash sale manager
  10. AJAX product filters
  11. CSV product import

Phase C (Bigger features — 1 week+):
  12. Loyalty points system
  13. Smart search (Meilisearch)
  14. Referral system
  15. Q&A on product page
  16. PWA support
```
