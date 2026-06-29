<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') - {{ $siteName ?? 'ShopGram' }} Admin</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        :root { --admin-primary: #2c3e50; --admin-accent: #e91e63; }
        body { background: #f4f6f9; }
        .sidebar { width: 250px; height: 100vh; background: var(--admin-primary); position: fixed; top: 0; left: 0; z-index: 100; transition: transform .3s, width .3s; display: flex; flex-direction: column; overflow: hidden; }
        .sidebar-brand { flex: 0 0 auto; padding: 1rem 1.2rem; border-bottom: 1px solid rgba(255,255,255,.1); color: #fff; font-weight: 700; font-size: 1.2rem; text-decoration: none; display: block; white-space: nowrap; }
        .sidebar-brand span { color: var(--admin-accent); }
        .sidebar-nav { flex: 1 1 auto; overflow-y: auto; overflow-x: hidden; padding-bottom: 1rem; scrollbar-width: thin; scrollbar-color: rgba(255,255,255,.35) transparent; }
        .sidebar-nav::-webkit-scrollbar { width: 6px; }
        .sidebar-nav::-webkit-scrollbar-thumb { background: rgba(255,255,255,.28); border-radius: 999px; }
        .sidebar-nav::-webkit-scrollbar-track { background: transparent; }
        .sidebar .nav-link { color: rgba(255,255,255,.75); padding: .5rem 1.2rem; font-size: .9rem; border-radius: 0; display: flex; align-items: center; gap: .6rem; }
        .sidebar .nav-link i { flex: 0 0 1rem; text-align: center; }
        .sidebar .nav-link span { white-space: nowrap; }
        .sidebar .nav-link:hover, .sidebar .nav-link.active { color: #fff; background: rgba(255,255,255,.1); }
        .sidebar .nav-section { color: rgba(255,255,255,.4); font-size: .7rem; text-transform: uppercase; letter-spacing: .1em; padding: .8rem 1.2rem .3rem; }
        .main-content { margin-left: 250px; min-height: 100vh; transition: margin-left .3s; }
        .top-bar { background: #fff; border-bottom: 1px solid #eee; padding: .7rem 1.5rem; display: flex; justify-content: space-between; align-items: center; position: sticky; top: 0; z-index: 99; }
        .page-content { padding: 1.5rem; }
        .stat-card { border: none; border-radius: 12px; }
        .stat-card .icon { width: 50px; height: 50px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 1.4rem; }
        body.sidebar-collapsed .sidebar { width: 74px; }
        body.sidebar-collapsed .main-content { margin-left: 74px; }
        body.sidebar-collapsed .sidebar-brand { padding-left: .8rem; padding-right: .8rem; font-size: 0; text-align: center; }
        body.sidebar-collapsed .sidebar-brand::before { content: 'SG'; font-size: 1.1rem; color: #fff; }
        body.sidebar-collapsed .sidebar .nav-link { justify-content: center; padding-left: .75rem; padding-right: .75rem; }
        body.sidebar-collapsed .sidebar .nav-link span,
        body.sidebar-collapsed .sidebar .nav-section { display: none; }
        @media (max-width: 991px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.show { transform: translateX(0); }
            .main-content { margin-left: 0; }
            body.sidebar-collapsed .sidebar { width: 250px; }
            body.sidebar-collapsed .main-content { margin-left: 0; }
            body.sidebar-collapsed .sidebar-brand { padding: 1rem 1.2rem; font-size: 1.2rem; text-align: left; }
            body.sidebar-collapsed .sidebar-brand::before { content: none; }
            body.sidebar-collapsed .sidebar .nav-link { justify-content: flex-start; padding: .5rem 1.2rem; }
            body.sidebar-collapsed .sidebar .nav-link span,
            body.sidebar-collapsed .sidebar .nav-section { display: block; }
        }
        .sidebar-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,.5); z-index: 99; }
        .sidebar-overlay.show { display: block; }
    </style>
    @stack('styles')
</head>
<body>

<div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>

{{-- Sidebar --}}
<aside class="sidebar" id="sidebar">
    <a class="sidebar-brand" href="{{ route('admin.dashboard') }}">Shop<span>Gram</span> Admin</a>
    <nav class="sidebar-nav py-2">
        <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <i class="bi bi-speedometer2"></i> <span>Dashboard</span>
        </a>
        @can('analytics.view')
            <a href="{{ route('admin.analytics.index') }}" class="nav-link {{ request()->routeIs('admin.analytics.*') ? 'active' : '' }}">
                <i class="bi bi-graph-up-arrow"></i> <span>Analytics</span>
            </a>
        @endcan

        <div class="nav-section">Catalog</div>
        <a href="{{ route('admin.products.index') }}" class="nav-link {{ request()->routeIs('admin.products.*') ? 'active' : '' }}">
            <i class="bi bi-box-seam"></i> <span>Products</span>
        </a>
        <a href="{{ route('admin.categories.index') }}" class="nav-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
            <i class="bi bi-diagram-3"></i> <span>Categories</span>
        </a>
        <a href="{{ route('admin.brands.index') }}" class="nav-link {{ request()->routeIs('admin.brands.*') ? 'active' : '' }}">
            <i class="bi bi-award"></i> <span>Brands</span>
        </a>
        <a href="{{ route('admin.inventory.index') }}" class="nav-link {{ request()->routeIs('admin.inventory.*') ? 'active' : '' }}">
            <i class="bi bi-archive"></i> <span>Inventory</span>
        </a>

        <div class="nav-section">Sales</div>
        <a href="{{ route('admin.orders.index') }}" class="nav-link {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">
            <i class="bi bi-cart3"></i> <span>Orders</span>
        </a>
        <a href="{{ route('admin.payments.index') }}" class="nav-link {{ request()->routeIs('admin.payments.*') ? 'active' : '' }}">
            <i class="bi bi-credit-card"></i> <span>Payments</span>
        </a>
        <a href="{{ route('admin.coupons.index') }}" class="nav-link {{ request()->routeIs('admin.coupons.*') ? 'active' : '' }}">
            <i class="bi bi-ticket-perforated"></i> <span>Coupons</span>
        </a>

        <div class="nav-section">Delivery</div>
        <a href="{{ route('admin.shipping-zones.index') }}" class="nav-link {{ request()->routeIs('admin.shipping-zones.*') ? 'active' : '' }}">
            <i class="bi bi-geo-alt"></i> <span>Shipping Zones</span>
        </a>
        <a href="{{ route('admin.couriers.index') }}" class="nav-link {{ request()->routeIs('admin.couriers.*') ? 'active' : '' }}">
            <i class="bi bi-truck"></i> <span>Couriers</span>
        </a>

        <div class="nav-section">Users</div>
        <a href="{{ route('admin.customers.index') }}" class="nav-link {{ request()->routeIs('admin.customers.*') ? 'active' : '' }}">
            <i class="bi bi-people"></i> <span>Customers</span>
        </a>
        <a href="{{ route('admin.admin-users.index') }}" class="nav-link {{ request()->routeIs('admin.admin-users.*') ? 'active' : '' }}">
            <i class="bi bi-person-badge"></i> <span>Admin Users</span>
        </a>
        <a href="{{ route('admin.roles.index') }}" class="nav-link {{ request()->routeIs('admin.roles.*') ? 'active' : '' }}">
            <i class="bi bi-shield-check"></i> <span>Role & Permission</span>
        </a>

        <div class="nav-section">Content</div>
        <a href="{{ route('admin.banners.index') }}" class="nav-link {{ request()->routeIs('admin.banners.*') ? 'active' : '' }}">
            <i class="bi bi-image"></i> <span>Banners</span>
        </a>
        <a href="{{ route('admin.promoted.index') }}" class="nav-link {{ request()->routeIs('admin.promoted.*') ? 'active' : '' }}">
            <i class="bi bi-megaphone"></i> <span>Promoted Products</span>
        </a>
        <a href="{{ route('admin.pages.index') }}" class="nav-link {{ request()->routeIs('admin.pages.*') ? 'active' : '' }}">
            <i class="bi bi-file-text"></i> <span>Footer Pages</span>
        </a>
        <a href="{{ route('admin.newsletter.index') }}" class="nav-link {{ request()->routeIs('admin.newsletter.*') ? 'active' : '' }}">
            <i class="bi bi-envelope"></i> <span>Newsletter</span>
        </a>

        <div class="nav-section">Support</div>
        @can('order.chat')
        <a href="{{ route('admin.live-chat.index') }}" class="nav-link {{ request()->routeIs('admin.live-chat.*') ? 'active' : '' }}" id="admin-lc-nav-link">
            <i class="bi bi-chat-dots"></i>
            <span>Customer Support</span>
            <span id="admin-lc-nav-badge" class="badge bg-danger ms-auto" style="display:none;font-size:.65rem;padding:2px 5px"></span>
        </a>
        @endcan
        <a href="{{ route('admin.reviews.index') }}" class="nav-link {{ request()->routeIs('admin.reviews.*') ? 'active' : '' }}">
            <i class="bi bi-star"></i> <span>Reviews</span>
        </a>
        <a href="{{ route('admin.tickets.index') }}" class="nav-link {{ request()->routeIs('admin.tickets.*') ? 'active' : '' }}">
            <i class="bi bi-headset"></i> <span>Tickets</span>
        </a>
        <a href="{{ route('admin.contact-messages.index') }}" class="nav-link {{ request()->routeIs('admin.contact-messages.*') ? 'active' : '' }}">
            <i class="bi bi-chat-left-text"></i> <span>Contact Messages</span>
        </a>

        <div class="nav-section">Reports</div>
        <a href="{{ route('admin.reports.sales') }}" class="nav-link {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
            <i class="bi bi-bar-chart"></i> <span>Reports</span>
        </a>

        <div class="nav-section">System</div>
        <a href="{{ route('admin.settings.index') }}" class="nav-link {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
            <i class="bi bi-gear"></i> <span>Settings</span>
        </a>
        <a href="{{ route('admin.activity-logs.index') }}" class="nav-link {{ request()->routeIs('admin.activity-logs.*') ? 'active' : '' }}">
            <i class="bi bi-clock-history"></i> <span>Activity Log</span>
        </a>
        <a href="{{ route('home') }}" class="nav-link" target="_blank">
            <i class="bi bi-box-arrow-up-right"></i> <span>View Site</span>
        </a>
    </nav>
</aside>

<div class="main-content">
    {{-- Top Bar --}}
    <div class="top-bar">
        <div class="d-flex align-items-center gap-3">
            <button class="btn btn-sm btn-light" type="button" onclick="toggleSidebar()" aria-label="Toggle sidebar">
                <i class="bi bi-list fs-5"></i>
            </button>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 small">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                    @yield('breadcrumb')
                </ol>
            </nav>
        </div>
        <div class="d-flex align-items-center gap-2">

            {{-- Chat / Messages Icon --}}
            <div class="dropdown">
                <button class="btn btn-light btn-sm position-relative px-2" id="chatDropdown"
                    data-bs-toggle="dropdown" aria-expanded="false" title="Messages">
                    <i class="bi bi-chat-dots fs-5"></i>
                    <span id="msgBadge" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger d-none" style="font-size:11px;min-width:18px;padding:3px 5px;line-height:1"></span>
                </button>
                <div class="dropdown-menu dropdown-menu-end shadow p-0" style="width:320px;max-height:420px;overflow-y:auto" id="chatDropdownMenu">
                    <div class="px-3 py-2 border-bottom d-flex justify-content-between align-items-center">
                        <span class="fw-semibold small">Unread Messages</span>
                        <a href="{{ route('admin.contact-messages.index') }}" class="small text-primary">View all</a>
                    </div>
                    <div id="chatItems"><div class="text-center text-muted py-4 small">Loading...</div></div>
                </div>
            </div>

            {{-- Web Push Subscribe Button --}}
            <button class="btn btn-light btn-sm px-2" id="pushToggleBtn" title="Enable desktop notifications" style="display:none">
                <i class="bi bi-bell-slash fs-5" id="pushBtnIcon"></i>
            </button>

            {{-- Notification Bell --}}
            <div class="dropdown">
                <button class="btn btn-light btn-sm position-relative px-2" id="bellDropdown"
                    data-bs-toggle="dropdown" aria-expanded="false" title="Notifications">
                    <i class="bi bi-bell fs-5"></i>
                    <span id="bellBadge" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger d-none" style="font-size:11px;min-width:18px;padding:3px 5px;line-height:1"></span>
                </button>
                <div class="dropdown-menu dropdown-menu-end shadow p-0" style="width:340px;max-height:460px;overflow-y:auto" id="bellDropdownMenu">
                    <div class="px-3 py-2 border-bottom d-flex justify-content-between align-items-center">
                        <span class="fw-semibold small">Notifications</span>
                        <span id="bellTotal" class="badge bg-primary rounded-pill small">0</span>
                    </div>
                    <div id="bellItems"><div class="text-center text-muted py-4 small">Loading...</div></div>
                    <div class="border-top text-center py-2">
                        <a href="{{ route('admin.orders.index') }}" class="small text-muted">View all orders</a>
                    </div>
                </div>
            </div>

            {{-- Admin Avatar --}}
            <div class="dropdown">
                <a href="#" class="dropdown-toggle text-dark text-decoration-none d-flex align-items-center gap-2" data-bs-toggle="dropdown">
                    <i class="bi bi-person-circle fs-5"></i>
                    <span class="d-none d-md-inline small">{{ auth()->user()->name }}</span>
                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item small" href="{{ route('home') }}" target="_blank">View Site</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button class="dropdown-item small text-danger">Logout</button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    {{-- Flash Messages --}}
    <div class="px-4 pt-3">
        <x-alert />
        <x-toast />
    </div>

    <div class="page-content">
        @yield('content')
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<x-confirm-delete />
<script>
const sidebar = document.getElementById('sidebar');
const sidebarOverlay = document.getElementById('sidebarOverlay');

function isMobileSidebar() {
    return window.matchMedia('(max-width: 991px)').matches;
}

function toggleSidebar() {
    if (isMobileSidebar()) {
        sidebar.classList.toggle('show');
        sidebarOverlay.classList.toggle('show');
        return;
    }

    document.body.classList.toggle('sidebar-collapsed');
    localStorage.setItem('adminSidebarCollapsed', document.body.classList.contains('sidebar-collapsed') ? '1' : '0');
}

function closeMobileSidebar() {
    sidebar.classList.remove('show');
    sidebarOverlay.classList.remove('show');
}

if (localStorage.getItem('adminSidebarCollapsed') === '1' && !isMobileSidebar()) {
    document.body.classList.add('sidebar-collapsed');
}

window.addEventListener('resize', function () {
    if (!isMobileSidebar()) {
        closeMobileSidebar();
    }
});
</script>
<script>
// ── Notification Bell + Chat Polling ──────────────────────────
const NOTIF_URL     = '{{ route("admin.notifications.counts") }}';
const RECENT_URL    = '{{ route("admin.notifications.recent") }}';
const MESSAGES_URL  = '{{ route("admin.notifications.messages") }}';
const MARK_READ_URL = '{{ route("admin.notifications.mark-read") }}';
const CSRF          = document.querySelector('meta[name="csrf-token"]').content;

function updateBadge(el, count) {
    if (count > 0) {
        el.textContent = count > 99 ? '99+' : count;
        el.classList.remove('d-none');
    } else {
        el.classList.add('d-none');
    }
}

function applyCountsToUI(data) {
    updateBadge(document.getElementById('bellBadge'), data.total);
    updateBadge(document.getElementById('msgBadge'),  data.messages);
    const totalEl = document.getElementById('bellTotal');
    if (totalEl) totalEl.textContent = data.total;
}

async function fetchCounts() {
    try {
        const res  = await fetch(NOTIF_URL, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
        const data = await res.json();
        applyCountsToUI(data);
    } catch {}
}

async function markRead(type, modelId, url) {
    try {
        const res  = await fetch(MARK_READ_URL, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': CSRF,
            },
            body: JSON.stringify({ type, model_id: modelId }),
        });
        const data = await res.json();
        applyCountsToUI(data);
    } catch {}
    window.location.href = url;
}

async function fetchBellItems() {
    const el = document.getElementById('bellItems');
    try {
        const res   = await fetch(RECENT_URL, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
        const items = await res.json();
        if (!items.length) {
            el.innerHTML = '<div class="text-center text-muted py-4 small">No new notifications.</div>';
            return;
        }
        el.innerHTML = items.map(n => `
            <a href="#" onclick="event.preventDefault();markRead('${n.type}',${n.model_id},'${n.url}')"
               class="d-flex align-items-start gap-2 px-3 py-2 text-decoration-none text-dark border-bottom notif-item"
               style="cursor:pointer;transition:background .15s" onmouseover="this.style.background='#f8f9fa'" onmouseout="this.style.background=''">
                <div class="rounded-circle bg-${n.color} bg-opacity-10 text-${n.color} d-flex align-items-center justify-content-center flex-shrink-0 mt-1" style="width:32px;height:32px;font-size:14px">
                    <i class="bi ${n.icon}"></i>
                </div>
                <div class="flex-grow-1 overflow-hidden">
                    <div class="small fw-semibold text-truncate">${n.text}</div>
                    <div class="d-flex justify-content-between mt-1">
                        <span class="text-muted" style="font-size:11px">${n.sub}</span>
                        <span class="text-muted" style="font-size:11px">${n.time}</span>
                    </div>
                </div>
            </a>`).join('');
    } catch { el.innerHTML = '<div class="text-center text-muted py-3 small">Failed to load.</div>'; }
}

async function fetchMessages() {
    const el = document.getElementById('chatItems');
    try {
        const res   = await fetch(MESSAGES_URL, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
        const items = await res.json();
        if (!items.length) {
            el.innerHTML = '<div class="text-center text-muted py-4 small">No unread messages.</div>';
            return;
        }
        el.innerHTML = items.map(m => `
            <a href="${m.url}" class="d-flex align-items-start gap-2 px-3 py-2 text-decoration-none text-dark border-bottom"
               style="transition:background .15s" onmouseover="this.style.background='#f8f9fa'" onmouseout="this.style.background=''">
                <div class="rounded-circle bg-info bg-opacity-10 text-info d-flex align-items-center justify-content-center flex-shrink-0 mt-1" style="width:32px;height:32px;font-size:14px">
                    <i class="bi bi-person"></i>
                </div>
                <div class="flex-grow-1 overflow-hidden">
                    <div class="d-flex justify-content-between">
                        <span class="small fw-semibold">${m.name}</span>
                        <span class="text-muted" style="font-size:11px">${m.time}</span>
                    </div>
                    <div class="small text-muted text-truncate">${m.subject}</div>
                </div>
            </a>`).join('');
    } catch { el.innerHTML = '<div class="text-center text-muted py-3 small">Failed to load.</div>'; }
}

// Reload bell list after returning to page (back button)
document.getElementById('bellDropdown').addEventListener('show.bs.dropdown', fetchBellItems);
document.getElementById('chatDropdown').addEventListener('show.bs.dropdown', fetchMessages);

// Initial + poll every 60s
fetchCounts();
setInterval(fetchCounts, 60000);

// ── Web Push ──────────────────────────────────────────────
(function () {
    if (!('serviceWorker' in navigator) || !('PushManager' in window)) return;

    const VAPID_PUBLIC = '{{ config("webpush.vapid.public_key") }}';
    const SUBSCRIBE_URL   = '{{ route("admin.push.subscribe") }}';
    const UNSUBSCRIBE_URL = '{{ route("admin.push.unsubscribe") }}';
    const CSRF            = document.querySelector('meta[name="csrf-token"]')?.content ?? '';

    const btn     = document.getElementById('pushToggleBtn');
    const btnIcon = document.getElementById('pushBtnIcon');

    function urlBase64ToUint8Array(base64String) {
        const padding = '='.repeat((4 - base64String.length % 4) % 4);
        const base64  = (base64String + padding).replace(/-/g, '+').replace(/_/g, '/');
        const raw     = atob(base64);
        return Uint8Array.from([...raw].map(c => c.charCodeAt(0)));
    }

    function updateBtn(sub) {
        if (sub) {
            btnIcon.className = 'bi bi-bell-fill fs-5 text-warning';
            btn.title = 'Desktop notifications ON — click to disable';
        } else {
            btnIcon.className = 'bi bi-bell-slash fs-5';
            btn.title = 'Enable desktop notifications';
        }
    }

    async function sendToServer(url, body) {
        await fetch(url, {
            method:  'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'X-Requested-With': 'XMLHttpRequest' },
            body: JSON.stringify(body),
        });
    }

    navigator.serviceWorker.register('/sw.js').then(async reg => {
        btn.style.display = '';

        let sub = await reg.pushManager.getSubscription();
        updateBtn(sub);

        btn.addEventListener('click', async () => {
            if (sub) {
                await sendToServer(UNSUBSCRIBE_URL, { endpoint: sub.endpoint });
                await sub.unsubscribe();
                sub = null;
            } else {
                if (Notification.permission === 'denied') {
                    showToast('Browser blocked notifications. Allow them in site settings, then click again.', 'warning');
                    return;
                }
                sub = await reg.pushManager.subscribe({
                    userVisibleOnly:      true,
                    applicationServerKey: urlBase64ToUint8Array(VAPID_PUBLIC),
                });
                await sendToServer(SUBSCRIBE_URL, {
                    endpoint: sub.endpoint,
                    keys: {
                        p256dh: btoa(String.fromCharCode(...new Uint8Array(sub.getKey('p256dh')))),
                        auth:   btoa(String.fromCharCode(...new Uint8Array(sub.getKey('auth')))),
                    },
                });
            }
            updateBtn(sub);
        });
    });
})();
</script>
@stack('scripts')
@can('order.chat')
<script>
(function () {
    function pollLcUnread() {
        fetch('/admin/live-chat/unread', { headers: { Accept: 'application/json' } })
            .then(r => r.json())
            .then(d => {
                const b = document.getElementById('admin-lc-nav-badge');
                if (!b) return;
                if (d.count > 0) { b.textContent = d.count > 99 ? '99+' : d.count; b.style.display = 'inline-flex'; }
                else b.style.display = 'none';
            }).catch(() => {});
        setTimeout(pollLcUnread, 30000);
    }
    pollLcUnread();
})();
</script>
@endcan
<script>
window.showToast = function(message, type = 'danger') {
    let container = document.querySelector('.toast-container');
    if (!container) {
        container = document.createElement('div');
        container.className = 'toast-container position-fixed top-0 end-0 p-3';
        container.style.zIndex = '1080';
        document.body.appendChild(container);
    }
    
    const iconMap = {
        success: 'bi-check-circle-fill',
        danger: 'bi-x-circle-fill',
        warning: 'bi-exclamation-triangle-fill',
        info: 'bi-info-circle-fill'
    };
    
    const icon = iconMap[type] || 'bi-info-circle-fill';
    const bgClass = type === 'error' ? 'danger' : type;
    
    const toastEl = document.createElement('div');
    toastEl.className = 'toast border-0 shadow-sm mb-2';
    toastEl.setAttribute('role', 'alert');
    toastEl.setAttribute('data-bs-autohide', 'true');
    toastEl.setAttribute('data-bs-delay', '4200');
    
    toastEl.innerHTML = `
        <div class="toast-header text-bg-${bgClass} border-0">
            <i class="bi ${icon} me-2"></i>
            <strong class="me-auto">${type.charAt(0).toUpperCase() + type.slice(1)}</strong>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
        <div class="toast-body bg-white text-dark">
            ${message}
        </div>
    `;
    
    container.appendChild(toastEl);
    const toastInstance = new bootstrap.Toast(toastEl);
    toastInstance.show();
    
    toastEl.addEventListener('hidden.bs.toast', () => {
        toastEl.remove();
    });
};
</script>
</body>
</html>
