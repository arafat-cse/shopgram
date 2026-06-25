# ShopGram — Laravel eCommerce

Full-featured eCommerce platform built with Laravel 12, Bootstrap 5, and MySQL.

---

## Tech Stack

| Layer | Technology |
|-------|-----------|
| Backend | PHP 8.2+, Laravel 12 |
| Frontend | Blade, Bootstrap 5, Bootstrap Icons |
| Database | MySQL (SQLite for local dev) |
| Auth & Roles | Spatie Laravel Permission |
| PDF | barryvdh/laravel-dompdf |
| Excel/CSV | maatwebsite/excel |
| Image processing | intervention/image |

---

## Quick Setup

```bash
# 1. Install dependencies
composer install
npm install

# 2. Environment
cp .env.example .env
php artisan key:generate

# 3. Database
php artisan migrate
php artisan db:seed

# 4. Storage link
php artisan storage:link

# 5. Build assets
npm run build
```

Or use the composer script (does steps 1–4+build in one go):

```bash
composer run setup
```

---

## Development Server

```bash
composer run dev
```

Starts Laravel + Vite + Queue worker + Pail log viewer concurrently.

---

## Default Credentials

| Role | Email | Password |
|------|-------|----------|
| Super Admin | admin@shopgram.com | password |

---

## URL Structure

| Area | URL |
|------|-----|
| Storefront | `/` |
| Admin panel | `/admin/dashboard` |
| Customer dashboard | `/customer/dashboard` |
| Cart | `/cart` |
| Checkout | `/checkout` |

---

## Roles & Permissions

Managed via Spatie Laravel Permission. Roles seeded by `RolePermissionSeeder`:

- **Super Admin** — full access
- **Admin** — product/order/customer management
- (more roles configurable from `/admin/roles`)

---

## Key Features

### Storefront
- Product listing with category/brand/price filters
- Product detail — image gallery with zoom, variant selector, reviews
- Social proof — "X viewing now" + "Y sold in 24h" badges
- Sticky add-to-cart bar (IntersectionObserver)
- First-visit promotional popup (promoted products from admin)
- Animated hero banner with countdown timer + urgency design
- Cart, checkout, order tracking
- Wishlist, product reviews
- Customer dashboard — orders, addresses, returns, support tickets
- Reorder button — re-adds past order items to cart
- Customer invoice PDF download
- Recently viewed products

### Admin Panel
- Dashboard with stats
- Product management — CRUD, variants, gallery, duplication
- Promoted products flag (shown in popup)
- Order management — status updates, invoice PDF
- Customer management
- Inventory management — stock in/out, low-stock alerts (auto-email to admins)
- Coupon, shipping zone, courier management
- Banner management
- Analytics — charts + Excel/PDF export
- Reports — sales, orders, products, stock, coupons, payments
- Activity log
- Notification bell (AJAX polling)
- Role & permission management
- Settings, pages, newsletter

---

## Seeders

| Seeder | Purpose |
|--------|---------|
| `AdminUserSeeder` | Super Admin user |
| `RolePermissionSeeder` | All roles + permissions |
| `CategorySeeder` | Sample categories |
| `BrandSeeder` | Sample brands |
| `ProductSeeder` | Sample products |
| `PromotedProductSeeder` | Marks products 1–3 as promoted (popup) |
| `ShippingZoneSeeder` | Default shipping zones |
| `SettingSeeder` | Default site settings |

Run individual seeder:

```bash
php artisan db:seed --class=PromotedProductSeeder
```

---

## Important Artisan Commands

```bash
# Run migrations fresh with seed
php artisan migrate:fresh --seed

# Clear all caches
php artisan optimize:clear

# Process queued jobs (notifications, etc.)
php artisan queue:work

# Run tests
composer run test
```

---

## Environment Notes

- Set `APP_TIMEZONE=Asia/Dhaka` (already in `.env.example`)
- Queue driver: set `QUEUE_CONNECTION=database` for production notifications
- Mail: configure `MAIL_*` vars for low-stock alerts and order emails
- Storage: run `php artisan storage:link` for product images to be served

---

## Project Docs

- [`project-structure.md`](project-structure.md) — file map, all controllers/models/views
- [`feature-update-structure.md`](feature-update-structure.md) — implemented features + next roadmap
