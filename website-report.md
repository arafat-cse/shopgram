# Shopgram — Production Readiness Report

**Date:** 2026-09-22 · **Scope:** full-stack review (security, deployment, performance, code quality)
**Verdict:** The core architecture is solid (routes protected, order totals computed server-side, coin values from DB, no mass-assignment holes). But there are **6 issues that will cause real problems if deployed as-is**, plus a performance profile that will not survive real traffic, and a hardening/feature roadmap for going live properly.

---

## 1. CRITICAL — will break or expose the site if deployed now

### 1.1 Debug mode + local environment config
- `.env` has `APP_ENV=local`, `APP_DEBUG=true`.
- On production, every error page will show full stack traces, environment variables, and file paths to visitors. This is the #1 Laravel leak.
- **Fix:** production `.env` → `APP_ENV=production`, `APP_DEBUG=false`, `LOG_LEVEL=warning`, `APP_URL=https://yourdomain.com`, `SESSION_SECURE_COOKIE=true`. Run `php artisan config:cache route:cache view:cache` at deploy.

### 1.2 Uploaded files keep client-supplied extension into the web root
- `Admin/ProductController.php:133-134,216-217,277-278`, `Admin/CategoryController.php:41-42`, `Admin/BrandController.php:32-33` all do `uniqid().'.'.$file->getClientOriginalExtension()` then `$file->move(public_path('images/...'))`.
- MIME validation checks content, not the name. A file with JPEG content named `shell.php` is stored as an executable `.php` inside the web root → full server takeover via the admin product form.
- **Fix:** whitelist extensions (`jpg,jpeg,png,webp,gif`), or use `$file->hashName()` / store on the `public` disk via Laravel's API; add an nginx/Apache rule denying PHP execution in upload dirs.

### 1.3 Chat internal key can stay at its default
- `config/chat.php:13` / `chat-service/service/config.js:8` default to `changeme`; `.env.example:73` ships that default.
- CSRF is disabled for `api/chat/*` and `api/livechat/*` (`bootstrap/app.php:25-28`), and `ChatController::store/close`, `LiveChatController::store` authenticate **only** by `X-Internal-Key`. With the default key, anyone on the internet can post messages as staff into any order chat and close conversations.
- **Fix:** fail hard at boot if the key is `changeme`; compare with `hash_equals()`; long random key per environment.

### 1.4 Seeder creates Super Admins with password `password`
- `database/seeders/AdminUserSeeder.php:17,28` — two Super Admin accounts (including a personal Gmail) with a trivial password. If the seeder ever runs on the live DB, the site is compromised immediately.
- **Fix:** generate a random password or read from env, and force reset on first login. Never seed known credentials to production.

### 1.5 No rate limiting on login / password reset
- `routes/web.php:62,66` — `AuthController::login` and `PasswordController::sendReset` have no `throttle` middleware → credential stuffing and mail-bombing on day one.
- **Fix:** `->middleware('throttle:5,1')` on both POST routes (Laravel's default login limiter is not automatic for custom controllers).

### 1.6 No test coverage at all
- `tests/` contains only the Laravel example tests. ~380 routes, checkout, coins, variants — none verified. Every deploy is a guess; refactors (e.g., the pending performance fixes) will silently break checkout.
- **Fix (minimum):** feature tests for cart → checkout → order, coin earn/spend, admin auth boundaries, and coupon edge cases before touching anything else.

---

## 2. HIGH — fix in the first week after launch

| # | Issue | Where | Why it matters | Fix |
|---|-------|-------|----------------|-----|
| 2.1 | Admin write actions gated by **view-only** permissions or none | `routes/admin.php:86-87` (payment status), `:95-96` (shipping CRUD), `:174-184` (promoted toggle, **coin adjust**), `:78-80`, `:49` | Any low-role admin (e.g. Sales Executive) can mark orders paid/refunded or credit arbitrary coins | Add `*.manage` permissions; separate view vs write |
| 2.2 | Guest live-chat upload, no CSRF, trusts client-sent session id | `routes/web.php:89`, `LiveChatController.php:209-233` | 10 MB anonymous uploads to your disk; spoofable session id | Require the signed token instead of raw `X-Session-Id` |
| 2.3 | Wildcard `View::composer('*', ...)` runs 4+ queries on **every view render** | `app/Providers/AppServiceProvider.php:46-86` | The home page renders ~56 product cards → hundreds of duplicated cart/wishlist/nav queries per page view | Scope to `layouts.app`, cache nav/footer data 5–15 min |
| 2.4 | N+1 per product card (2 review queries per card) | `product-card.blade.php:33`, `Product.php:60-62` | ~112 extra queries per home page; collapses under load | `withCount('reviews')->withAvg('reviews','rating')` in all listing controllers |
| 2.5 | Home page builds 8 uncached product collections; loads **every product of every category** just to pick thumbnails | `HomeController.php:31-49` (`with(['products','children.products'])`) | Slow page, DB hammering; grows linearly with catalog size | Cache each section; select only `id, thumbnail` for category tiles |
| 2.6 | Search leaks inactive products | `SearchController.php:19-21` — `orWhere(sku)` escapes the `active()` scope | Draft/disabled products visible & buyable via search suggestions | Wrap in `where(fn($q) => $q->where(name)->orWhere(sku))` |
| 2.7 | Frontend assets: no Vite build, jQuery 3.7.1 loaded but never used, ~1000 lines of CSS inlined in blade | `layouts/app.blade.php:9-10,461-462`, `home/index.blade.php:5-776` | No cache-busting/minification, render-blocking CDN dependencies, bigger pages | Adopt Vite (`npm run build`), extract CSS, drop jQuery |
| 2.8 | No image pipeline — originals served as thumbnails | `Product.php:71-79`, intervention/image installed but unused | MB-size product images on card grids; slow mobile; storage bloat (re-uploads already duplicating files) | Generate resized webp variants on upload |

---

## 3. MEDIUM — plan for the first month

- **Email/phone verification absent** — `AuthController.php:84-94` logs users in instantly; fake accounts cost you coins (`MustVerifyEmail` unused).
- **Weak password policy** — `Password::defaults()` never configured; enforce length+numbers, consider bcrypt rounds >12 on production hardware.
- **Public order tracking leaks data** — `Customer/OrderController.php:59-71` exposes address history + courier by enumerable order number; require phone/postcode confirmation.
- **Chat attachment unvalidated string** — `ChatController.php:74` persists any URL → stored-XSS/phishing inside chat UIs; validate as internal `storage/` path.
- **Deployment hardening missing** — no `config/cors.php` (default `*` on `api/*`), no TrustProxies, no HTTPS enforcement, no security headers (confirmed live: no `X-Frame-Options`, `X-Content-Type-Options`, CSP, HSTS). CSRF disabled on whole wildcard prefixes rather than exact internal paths (`bootstrap/app.php:25`).
- **Silent frontend failures** — `layouts/app.blade.php:841` `.catch(() => {})`; because the promo "seen" flag only saves on modal close, a failing endpoint re-fetches **every page load forever**. Remove console.log spam from chat JS (`customer/orders/show.blade.php:381-449`, `admin/orders/show.blade.php:455-512`).
- **DB gaps** — no `SoftDeletes`; `order_items.product_id` cascades, so deleting a product erases order history. Add composite indexes `reviews(product_id,status)`, `orders(status,created_at)`.
- **Quickview re-injects and re-runs its full `<script>` on every open** (`layouts/app.blade.php:1052-1059`) — works, but fragile; return JSON + one reusable script when you next touch it.

---

## 4. What to develop next (roadmap, by value)

1. **Test suite for the money paths** — cart → checkout → order → coin earn/spend. Everything else gets safer after this.
2. **Asset pipeline** — Vite build, remove jQuery, extract inline CSS/JS. Fastest large perf win.
3. **Image optimization service** — webp thumbnails (intervention/image is already installed and unused).
4. **Query/cache layer** — fix 2.3/2.4/2.5, add a `settings()` and nav-categories cache. Home page should drop from ~100+ queries to <20.
5. **Admin permission split (view vs manage)** — required before adding staff accounts.
6. **Email verification + password policy** — protects the coin economy from throwaway accounts.
7. **Monitoring** — Sentry/Flare for errors, queue worker under supervisor (`QUEUE_CONNECTION=database` needs a running `php artisan queue:work`), DB backups, uptime check on both the web app **and** the Node chat service (it is a separate always-on process — use pm2/systemd; a dead chat service also breaks page load if `CHAT_NODE_URL` is wrong, as seen locally with the stale LAN IP that cost 21 s per page load).
8. **Feature ideas already hinted by the codebase** — cache the promoted-products endpoint, paginate search suggestions, soft deletes for products/orders, and an admin "chat service health" indicator.

---

## 5. Production deploy checklist

- [ ] `.env`: production values (see 1.1) + real SMTP + `CHAT_NODE_URL` pointing at the deployed chat host
- [ ] `php artisan config:cache route:cache view:cache`
- [ ] Seeder passwords rotated / never run seeder in prod
- [ ] Upload-dir PHP execution blocked at web-server level (see 1.2)
- [ ] Chat internal key randomized (see 1.3)
- [ ] `throttle` on auth routes (see 1.5)
- [ ] Queue worker running (supervisor) — or confirm nothing is queued yet
- [ ] HTTPS + security headers middleware + TrustProxies
- [ ] `npm run build` once Vite is adopted; CDN assets pinned
- [ ] Nightly DB backup + restore drill
- [ ] Error tracking (Sentry) + uptime monitor on `/` and chat `/health`

---

## 6. Already good (no action needed)

- Order/cart/coupon totals computed **server-side** (`OrderService.php:45-57`); coin earn/spend values never come from client input.
- Customer ownership checks on orders/cart/addresses; admin routes protected by auth + role middleware + spatie permissions.
- No `$guarded = []`, no `dd()` leftovers, no hardcoded secrets in code/JS.
- Newsletter unsubscribe uses signed URLs; product slugs unique; FKs constrained and indexed.
- Clean route grouping (`web/admin/customer`), readable controllers, consistent Blade components.
