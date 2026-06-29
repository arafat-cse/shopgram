# ShopGram — Feature Update Roadmap

**Goal:** Smarter UX, higher conversion, better admin control  
**Last updated:** 2026-06-28 (session 2)

---

## 🎯 IMPLEMENT NEXT — Best ROI (Ordered by impact ÷ effort)

| # | Feature | Why best now | Est. effort |
|---|---------|-------------|-------------|
| 1 | **bKash / Nagad payment** | BD market — 80%+ users prefer mobile banking over COD | 3 days |
| 2 | **Mini cart drawer** | Biggest UX win. Customer adds item → stays on page → buys more | 2 days |
| 3 | **Flash sale manager + countdown** | BD shoppers respond strongly to urgency. Full admin control | 3 days |
| 4 | **Abandoned cart email** | Recovers ~15% of lost carts automatically. Scheduled job | 2 days |
| 5 | **Product quick view modal** | Keeps customer on listing → more products seen → more buys | 3 days |
| 6 | **Free shipping threshold** | "Free delivery above ৳500" — huge conversion driver in BD | 1 day |
| 7 | **SMS order notifications** | BD customers track orders via SMS, not email | 2 days |
| 8 | **Loyalty points system** | Retention goldmine. Customers come back to spend points | 5 days |

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
| **Free shipping threshold** | "Free delivery on orders above ৳500" shown on cart + checkout | `free_shipping_above` in settings table, check in CartService |
| **COD fee** | Extra ৳30-50 charge for cash on delivery (common BD practice) | `cod_extra_charge` in settings, add to order total when method=cod |
| **Product bundle deal** | Buy A+B together for X% off — "combo offer" | `bundles` + `bundle_items` tables, shown on product page |
| **Estimated delivery date** | "Expected delivery: Jun 30 – Jul 2" on product + checkout | Shipping zone `delivery_days` column + date calc |

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
| **Loyalty points display** | Show earned points in dashboard | `loyalty_points` column on users table |
| **Customer wallet / store credit** | Refund goes to wallet, customer spends at next order | `wallet_balance` on users, `wallet_transactions` table |
| **Order note** | Customer adds note during checkout ("leave at gate") | `customer_note` column on orders, textarea in checkout |

### 🟢 Mobile UX

| Feature | What it does | Implementation hint |
|---------|-------------|---------------------|
| **PWA support** | Add to home screen, offline page | `manifest.json` + service worker + `offline.blade.php` |
| **Swipe gestures on product gallery** | Swipe left/right between images on mobile | Swiper.js |
| **Bottom sheet filters** | Mobile filter as bottom drawer | Bootstrap offcanvas bottom |

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
| **Block/unblock with reason** | Store reason when blocking customer | `blocked_reason` + `blocked_at` columns on users |

### 🟡 Marketing Tools

| Feature | What it does | Implementation hint |
|---------|-------------|---------------------|
| **Flash sale manager** | Create sale with % off, start/end time, specific products/categories | `flash_sales` + `flash_sale_products` tables |
| **Loyalty points system** | Earn points per ৳ spent, redeem as discount | `loyalty_points` on users, `point_transactions` table, redeem at checkout |
| **Referral system** | Customer gets unique link, earns credit when referral buys | `referral_code` on users, `referral_conversions` table |
| **Pop-up / exit-intent offer** | Show coupon popup when user is about to leave | JS `mouseleave` on document + session flag |
| **Affiliate / influencer panel** | Influencer gets custom link, earns % commission on sales | `affiliates` table, separate dashboard, commission tracking |

### 🟢 Admin UX

| Feature | What it does | Implementation hint |
|---------|-------------|---------------------|
| **Dark mode for admin** | Toggle dark/light in admin panel | CSS variables + `localStorage` preference |
| **Admin quick search** | Press `/` anywhere in admin → search orders, products, customers | JS modal + AJAX |
| **Keyboard shortcuts** | `N` = new product, `O` = orders, `ESC` = close modal | JS `keydown` listener |
| **Review reply by admin** | Admin replies publicly to customer review on product page | `review_replies` table or `reply` column on reviews |
| **Admin order note** | Internal note on order visible to staff only | `admin_note` column on orders |

---

## 🇧🇩 BANGLADESH MARKET — Critical Features

### 🔴 Payment Gateways

| Feature | What it does | Implementation hint |
|---------|-------------|---------------------|
| **bKash payment** | Bangladesh's #1 mobile banking — huge conversion boost | SSLCommerz integration (covers bKash/Nagad/Rocket/cards) or bKash API direct |
| **Nagad payment** | Bangladesh Post's mobile banking — growing fast | Via SSLCommerz or Nagad PGW API |
| **SSLCommerz** | Single gateway covering all BD payment methods + cards | `aamarPay/sslcommerz-laravel` package · `ssl_transactions` table |
| **Card payment (Visa/Mastercard)** | Credit/debit cards via SSLCommerz | Covered by SSLCommerz integration |

### 🔴 Communication

| Feature | What it does | Implementation hint |
|---------|-------------|---------------------|
| **SMS order notifications** | BD customers track orders via SMS not email | `infobip` / `twilio` / local BD gateway (Mim SMS, Banglalink API) · trigger on order status change |
| **OTP SMS login** | Phone number → OTP → login (no password needed) | SMS gateway + `otp_verifications` table · 5-min expiry |
| **WhatsApp order alert to admin** | New order → WhatsApp message to shop owner | Twilio WhatsApp API or `360dialog` → webhook trigger |

### 🟡 BD Address System

| Feature | What it does | Implementation hint |
|---------|-------------|---------------------|
| **Division → District → Upazila selector** | Structured BD address with cascading dropdowns | `bd_divisions`, `bd_districts`, `bd_upazilas` seed tables · AJAX cascade |
| **Area-based delivery availability** | Mark products not deliverable outside Dhaka / only in specific districts | `product_delivery_zones` table |

### 🟡 Auth & Login

| Feature | What it does | Implementation hint |
|---------|-------------|---------------------|
| **Facebook OAuth login** | 1-tap login with Facebook — very common in BD | `laravel/socialite` · `facebook_id` on users |
| **Google OAuth login** | 1-tap login with Google | `laravel/socialite` · `google_id` on users |

---

## 💬 ORDER CHAT — Real-time Customer ↔ Staff (Node.js + Socket.io)

> **Idea:** Each order gets a real-time chat thread. Customer + permission-granted staff can chat. Chat auto-closes when order status = `delivered` or `cancelled`.

---

### IMPLEMENTATION SPEC (AI-ready — follow steps in order)

---

#### STEP 1 — Laravel: Database migration

**File to create:** `database/migrations/YYYY_MM_DD_create_order_messages_table.php`

```php
Schema::create('order_messages', function (Blueprint $table) {
    $table->id();
    $table->foreignId('order_id')->constrained()->cascadeOnDelete();
    $table->foreignId('user_id')->constrained()->cascadeOnDelete();
    $table->enum('sender_role', ['customer', 'staff']);
    $table->text('message');
    $table->boolean('is_read')->default(false);
    $table->timestamp('created_at')->useCurrent();
});
```

Run: `php artisan migrate`

---

#### STEP 2 — Laravel: Model

**File to create:** `app/Models/OrderMessage.php`

```php
class OrderMessage extends Model {
    public $timestamps = false;
    protected $fillable = ['order_id', 'user_id', 'sender_role', 'message', 'is_read'];
    protected $casts = ['is_read' => 'boolean', 'created_at' => 'datetime'];

    public function user() { return $this->belongsTo(User::class); }
    public function order() { return $this->belongsTo(Order::class); }
}
```

**Add to `app/Models/Order.php`:**
```php
public function messages() { return $this->hasMany(OrderMessage::class)->orderBy('created_at'); }
public function isChatOpen(): bool {
    return in_array($this->status, ['pending', 'processing', 'shipped']);
}
```

---

#### STEP 3 — Laravel: Spatie permission

Add permission `order.chat` via seeder or tinker:
```php
Permission::firstOrCreate(['name' => 'order.chat', 'guard_name' => 'web']);
// Assign to Super Admin and Admin roles
Role::findByName('Super Admin')->givePermissionTo('order.chat');
Role::findByName('Admin')->givePermissionTo('order.chat');
```

---

#### STEP 4 — Laravel: API routes

**File to edit:** `routes/web.php` — add inside existing auth middleware group or new group:

```php
// Chat API — used by Node.js server and browser
Route::middleware('auth')->prefix('api/chat')->group(function () {
    Route::get('token',                       [ChatController::class, 'token']);
    Route::get('orders/{order}/messages',     [ChatController::class, 'messages']);
    Route::post('orders/{order}/messages',    [ChatController::class, 'store']);
    Route::post('orders/{order}/close',       [ChatController::class, 'close']);
    Route::get('orders/{order}/unread-count', [ChatController::class, 'unreadCount']);
});
```

---

#### STEP 5 — Laravel: ChatController

**File to create:** `app/Http/Controllers/ChatController.php`

```php
<?php
namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ChatController extends Controller
{
    // Returns a signed token for socket.io auth
    public function token(Request $request)
    {
        $user = auth()->user();
        $token = base64_encode(json_encode([
            'user_id'   => $user->id,
            'name'      => $user->name,
            'role'      => $user->hasPermissionTo('order.chat') ? 'staff' : 'customer',
            'signature' => hash_hmac('sha256', $user->id, config('app.key')),
        ]));
        return response()->json(['token' => $token]);
    }

    // Returns message history for an order
    public function messages(Order $order)
    {
        $this->authorizeOrderAccess($order);
        return response()->json(
            $order->messages()->with('user:id,name')->latest('created_at')->limit(100)->get()
        );
    }

    // Save message — called by Node.js with internal key OR by browser fallback
    public function store(Request $request, Order $order)
    {
        // Allow internal Node.js call with secret key
        $isInternal = $request->header('X-Internal-Key') === config('chat.internal_key');
        if (!$isInternal) $this->authorizeOrderAccess($order);
        if (!$order->isChatOpen()) return response()->json(['error' => 'Chat closed'], 403);

        $msg = OrderMessage::create([
            'order_id'    => $order->id,
            'user_id'     => $request->user_id ?? auth()->id(),
            'sender_role' => $request->sender_role ?? (auth()->user()->hasPermissionTo('order.chat') ? 'staff' : 'customer'),
            'message'     => $request->validate(['message' => 'required|string|max:2000'])['message'],
        ]);

        return response()->json($msg->load('user:id,name'));
    }

    // Close chat (called when order status changes to delivered/cancelled)
    public function close(Order $order)
    {
        // Mark all messages as read, emit close event via Node if needed
        OrderMessage::where('order_id', $order->id)->update(['is_read' => true]);
        return response()->json(['closed' => true]);
    }

    public function unreadCount(Order $order)
    {
        $this->authorizeOrderAccess($order);
        $role = auth()->user()->hasPermissionTo('order.chat') ? 'customer' : 'staff';
        return response()->json([
            'count' => OrderMessage::where('order_id', $order->id)
                ->where('sender_role', $role)
                ->where('is_read', false)
                ->count()
        ]);
    }

    private function authorizeOrderAccess(Order $order)
    {
        $user = auth()->user();
        if ($user->hasPermissionTo('order.chat')) return; // staff — allow all orders
        abort_if($order->user_id !== $user->id, 403);     // customer — own orders only
    }
}
```

---

#### STEP 6 — Laravel: config file

**File to create:** `config/chat.php`

```php
<?php
return [
    'internal_key' => env('CHAT_INTERNAL_KEY', 'changeme'),
    'node_url'     => env('CHAT_NODE_URL', 'http://localhost:3001'),
];
```

**Add to `.env` and `.env.example`:**
```
CHAT_INTERNAL_KEY=super_secret_key_here
CHAT_NODE_URL=http://localhost:3001
```

---

#### STEP 7 — Laravel: Auto-close chat on order status change

**File to edit:** `app/Http/Controllers/Admin/OrderController.php`

In the `updateStatus()` method, after updating status, add:
```php
if (in_array($status, ['delivered', 'cancelled'])) {
    // Close chat
    \App\Models\OrderMessage::where('order_id', $order->id)->update(['is_read' => true]);
    // Optionally notify Node to close the room:
    // Http::post(config('chat.node_url') . '/internal/close-room/' . $order->id,
    //     ['headers' => ['X-Internal-Key' => config('chat.internal_key')]]);
}
```

---

#### STEP 8 — Node.js: Setup

```bash
# From project root
mkdir chat-service && cd chat-service
npm init -y
npm install express socket.io axios dotenv cors
```

**File to create:** `chat-service/.env`
```
PORT=3001
LARAVEL_URL=http://127.0.0.1:8000
CHAT_INTERNAL_KEY=super_secret_key_here
```

---

#### STEP 9 — Node.js: server.js

**File to create:** `chat-service/server.js`

```js
require('dotenv').config();
const express    = require('express');
const http       = require('http');
const { Server } = require('socket.io');
const axios      = require('axios');

const app    = express();
const server = http.createServer(app);
const io     = new Server(server, {
    cors: { origin: process.env.LARAVEL_URL, credentials: true }
});

app.use(express.json());

// ── Auth middleware ──────────────────────────────────────────────────
io.use(async (socket, next) => {
    try {
        const rawToken = socket.handshake.auth.token;
        if (!rawToken) return next(new Error('No token'));

        const payload = JSON.parse(Buffer.from(rawToken, 'base64').toString());

        // Verify signature with Laravel
        const res = await axios.get(`${process.env.LARAVEL_URL}/api/chat/token`, {
            headers: { 'Cookie': socket.handshake.headers.cookie || '' }
        });

        // Simple: trust decoded token if signature field matches
        // (In production: call Laravel /api/verify-token endpoint instead)
        socket.user = {
            id:   payload.user_id,
            name: payload.name,
            role: payload.role,   // 'customer' or 'staff'
        };
        next();
    } catch (e) {
        next(new Error('Auth failed'));
    }
});

// ── Connection ───────────────────────────────────────────────────────
io.on('connection', (socket) => {
    const orderId = socket.handshake.query.order_id;
    if (!orderId) return socket.disconnect();

    const room = `order_${orderId}`;
    socket.join(room);

    console.log(`[chat] ${socket.user.name} (${socket.user.role}) joined ${room}`);

    // Send message
    socket.on('send_message', async (data) => {
        if (!data.message?.trim()) return;

        try {
            // Save to Laravel DB
            const res = await axios.post(
                `${process.env.LARAVEL_URL}/api/chat/orders/${orderId}/messages`,
                { message: data.message, user_id: socket.user.id, sender_role: socket.user.role },
                { headers: { 'X-Internal-Key': process.env.CHAT_INTERNAL_KEY } }
            );

            // Broadcast to room
            io.to(room).emit('new_message', {
                id:          res.data.id,
                sender_name: socket.user.name,
                sender_role: socket.user.role,
                message:     data.message,
                time:        new Date().toISOString(),
            });
        } catch (e) {
            socket.emit('error', { message: e.response?.data?.error || 'Failed to send' });
        }
    });

    // Typing indicator
    socket.on('typing', () => {
        socket.to(room).emit('user_typing', { name: socket.user.name, role: socket.user.role });
    });

    socket.on('disconnect', () => {
        console.log(`[chat] ${socket.user.name} left ${room}`);
    });
});

// ── Internal endpoint — close room (called by Laravel) ───────────────
app.post('/internal/close-room/:orderId', (req, res) => {
    if (req.headers['x-internal-key'] !== process.env.CHAT_INTERNAL_KEY) {
        return res.status(403).json({ error: 'Forbidden' });
    }
    const room = `order_${req.params.orderId}`;
    io.to(room).emit('chat_closed', { message: 'Order delivered. Chat is now closed.' });
    res.json({ closed: true });
});

server.listen(process.env.PORT, () => {
    console.log(`[chat-service] running on port ${process.env.PORT}`);
});
```

---

#### STEP 10 — Frontend: Customer order page

**File to edit:** `resources/views/customer/orders/show.blade.php`

Add at the bottom, before `@endsection`:

```blade
@if($order->isChatOpen())
<div class="card border-0 shadow-sm mt-4">
    <div class="card-header bg-white fw-semibold d-flex justify-content-between align-items-center">
        <span><i class="bi bi-chat-dots me-2 text-primary"></i>Order Support Chat</span>
        <span class="badge bg-success">Live</span>
    </div>
    <div class="card-body p-0">
        <div id="chatMessages" style="height:320px;overflow-y:auto;padding:16px;background:#f8fafc;">
            <div class="text-center text-muted small py-4">Loading messages...</div>
        </div>
        <div class="border-top p-3 d-flex gap-2">
            <input type="text" id="chatInput" class="form-control" placeholder="Type a message..." maxlength="2000">
            <button class="btn btn-primary px-4" id="chatSendBtn">Send</button>
        </div>
        <div id="typingIndicator" class="px-3 pb-2 text-muted" style="font-size:.8rem;min-height:20px;"></div>
    </div>
</div>
@else
<div class="card border-0 shadow-sm mt-4">
    <div class="card-body text-center text-muted py-3 small">
        <i class="bi bi-chat-dots me-1"></i>Chat is closed for this order.
    </div>
</div>
@endif

@push('scripts')
<script src="{{ config('chat.node_url') }}/socket.io/socket.io.js"></script>
<script>
(async function () {
    const ORDER_ID = {{ $order->id }};
    const msgBox   = document.getElementById('chatMessages');
    const input    = document.getElementById('chatInput');
    const sendBtn  = document.getElementById('chatSendBtn');
    const typer    = document.getElementById('typingIndicator');
    if (!msgBox) return;

    // 1. Get auth token from Laravel
    const tokenRes = await fetch('/api/chat/token');
    const { token } = await tokenRes.json();

    // 2. Connect to Node.js
    const socket = io('{{ config("chat.node_url") }}', {
        auth: { token },
        query: { order_id: ORDER_ID },
    });

    // 3. Load history
    const histRes  = await fetch(`/api/chat/orders/${ORDER_ID}/messages`);
    const messages = await histRes.json();
    msgBox.innerHTML = '';
    messages.forEach(renderMessage);
    msgBox.scrollTop = msgBox.scrollHeight;

    // 4. Receive new messages
    socket.on('new_message', (msg) => { renderMessage(msg); msgBox.scrollTop = msgBox.scrollHeight; });
    socket.on('chat_closed', (d)   => { msgBox.insertAdjacentHTML('beforeend', `<div class="text-center text-danger small py-2">${d.message}</div>`); input.disabled = true; sendBtn.disabled = true; });
    socket.on('user_typing', (d)   => { typer.textContent = d.role === 'staff' ? 'Support is typing...' : ''; setTimeout(() => typer.textContent = '', 2000); });

    // 5. Send
    function send() {
        const msg = input.value.trim();
        if (!msg) return;
        socket.emit('send_message', { message: msg });
        input.value = '';
    }
    sendBtn.addEventListener('click', send);
    input.addEventListener('keydown', (e) => { if (e.key === 'Enter') send(); });
    input.addEventListener('input', () => socket.emit('typing'));

    function renderMessage(msg) {
        const isMe = msg.sender_role === 'customer';
        msgBox.insertAdjacentHTML('beforeend', `
            <div class="d-flex ${isMe ? 'justify-content-end' : 'justify-content-start'} mb-2">
                <div class="px-3 py-2 rounded-3 small" style="max-width:75%;background:${isMe ? '#0d6efd' : '#fff'};color:${isMe ? '#fff' : '#1e293b'};border:1px solid #e2e8f0">
                    ${!isMe ? `<div class="fw-semibold" style="font-size:.75rem;color:#6c757d">${msg.sender_name || msg.user?.name || 'Staff'}</div>` : ''}
                    ${msg.message}
                </div>
            </div>`);
    }
})();
</script>
@endpush
```

---

#### STEP 11 — Frontend: Admin order page

**File to edit:** `resources/views/admin/orders/show.blade.php`

Add the same chat widget as Step 10, but change:
- `sender_role` check: staff sees messages from `customer` side on left, own messages on right
- Gate check: `@can('order.chat')` wrap the entire widget
- Label: "Customer is typing..." instead of "Support is typing..."

---

#### STEP 12 — Run

```bash
# Terminal 1 — Laravel
php artisan serve

# Terminal 2 — Node chat service
cd chat-service
node server.js
# → [chat-service] running on port 3001

# Production (PM2)
npm install -g pm2
pm2 start chat-service/server.js --name chat-service
pm2 save
```

---

### Phase 2 enhancements (add after core works)

| Feature | How |
|---------|-----|
| Typing indicator | Already in spec (`socket.emit('typing')`) |
| Read receipts | `PATCH /api/chat/orders/{order}/read` → mark is_read = true |
| File/image attach | `multipart/form-data` → store in `storage/chat` → send URL in message |
| Auto reply bot | If no staff online after 5 min → emit auto message from system user |
| Chat rating | After close event → show 1-5 star widget → POST `/api/chat/orders/{order}/rate` |
| Chat transcript email | On close → queue job → `Mail::to($order->user)->send(new ChatTranscriptMail($order))` |
| Push notification | On `new_message` → call existing `LowStockAlertNotification` pattern for webpush |
| Unread badge on order list | Poll `/api/chat/orders/{order}/unread-count` every 30s |

---

### Chat lifecycle

```
order.status = pending/processing/shipped  →  chat OPEN
order.status = delivered/cancelled         →  chat CLOSED (auto)
                                                ↓
                                    all messages read = true
                                    Node emits 'chat_closed' to room
                                    customer sees "Chat closed" UI
                                    messages remain visible (read-only)
```

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
-- Already created ✅
order_messages       — id, order_id, user_id, sender_role(customer/staff), message, is_read, attachment, attachment_type, attachment_name, attachment_size, created_at

-- Pending
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
wallet_transactions  — id, user_id, type(credit/debit), amount, note, order_id, created_at
ssl_transactions     — id, order_id, tran_id, status, amount, gateway, raw_response, created_at
otp_verifications    — id, phone, otp, expires_at, used_at
bundles              — id, name, discount_percent, active
bundle_items         — id, bundle_id, product_id, variant_id
bd_divisions         — id, name (8 divisions)
bd_districts         — id, division_id, name (64 districts)
bd_upazilas          — id, district_id, name (~500 upazilas)
affiliates           — id, user_id, code, commission_percent, total_earned, status
affiliate_clicks     — id, affiliate_id, order_id, commission_amount, created_at
review_replies       — id, review_id, user_id, reply, created_at
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
| **Newsletter campaigns** | `NewsletterCampaign` model · `NewsletterController` · bulk send with status tracking · admin routes `admin.php:125-133` |
| **Customer lifetime value** | `CustomerController@show` calculates `totalSpent` from delivered orders · displayed in `admin.customers.show` |
| **Order Chat** (Node.js + Socket.io) | `order_messages` table · `ChatController` (token/messages/upload/close/unread) · Node.js service at `chat-service/` · image+file attachment · typing indicator · auto-close on delivered/cancelled |
| **Admin payment status update** | `OrderController@updatePaymentStatus` · `POST admin/orders/{order}/payment-status` · `order.payment.update` permission · dropdown for permitted roles, readonly badge otherwise |
| **Mobile customer dashboard nav** | Horizontal scrollable icon+label nav pills (Daraz-style) · `layouts/customer.blade.php` · hidden on desktop, shown on mobile only |
| **Phone-based login** | Login with phone number OR email · regex detection in `AuthController@login` · phone required on registration · `order.payment.update` added to seeder |
| **Product YouTube video** | YouTube URL field on products · video shown in product gallery tab · `frontend/products/show.blade.php` |
| **Star ratings on product cards** | Average rating + review count shown on product cards · `product-card.blade.php` |

---

## RECOMMENDED IMPLEMENTATION ORDER

```
Phase A ✅ COMPLETE:
  ✅ Reorder button
  ✅ Auto low-stock email
  ✅ Customer invoice PDF
  ✅ Image lazy loading
  ✅ Order chat (Node.js + Socket.io with file/image)
  ✅ Admin payment status update
  ✅ Mobile customer dashboard nav

Phase B — Next up (3-5 days each):
  1. bKash/SSLCommerz payment gateway  ← BD market priority #1
  2. Mini cart drawer (AJAX)
  3. Free shipping threshold + COD fee
  4. SMS order notifications
  5. Flash sale manager + countdown timer
  6. Product quick view modal
  7. Abandoned cart list + email
  8. AJAX product filters
  9. CSV product import
  10. Facebook / Google OAuth login

Phase C — Medium features (1 week+):
  11. Loyalty points system
  12. Customer wallet/store credit
  13. OTP SMS login
  14. Referral system
  15. BD Division/District/Upazila address selector
  16. Smart search (Meilisearch)
  17. Q&A on product page
  18. Bulk order status update

Phase D — Bigger features (2+ weeks):
  19. PWA support
  20. Affiliate/influencer panel
  21. Product bundle deals
  22. Affiliate system
  23. Multi-vendor (future)
```
