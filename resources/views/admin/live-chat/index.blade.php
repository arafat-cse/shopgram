@extends('layouts.admin')

@section('title', 'Customer Support — Live Chat')

@push('styles')
<style>
.lca-wrap{display:flex;height:calc(100vh - 64px);overflow:hidden}
.lca-sidebar{width:320px;flex-shrink:0;border-right:1px solid #e2e8f0;display:flex;flex-direction:column;background:#fff}
.lca-sidebar-head{padding:16px;border-bottom:1px solid #e2e8f0;flex-shrink:0}
.lca-sidebar-head h6{margin:0 0 10px;font-weight:700;color:#1e293b}
.lca-search{position:relative}
.lca-search input{width:100%;padding:8px 12px 8px 34px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:.83rem;outline:none;background:#f8fafc}
.lca-search input:focus{border-color:#0d6efd;background:#fff}
.lca-search svg{position:absolute;left:10px;top:50%;transform:translateY(-50%);color:#94a3b8}
.lca-filter-tabs{display:flex;gap:6px;padding:10px 12px;border-bottom:1px solid #e2e8f0;flex-shrink:0;overflow-x:auto}
.lca-filter-tab{padding:5px 12px;border-radius:20px;font-size:.75rem;font-weight:600;cursor:pointer;border:1.5px solid #e2e8f0;background:#fff;white-space:nowrap;transition:all .15s}
.lca-filter-tab.active{background:#0d6efd;border-color:#0d6efd;color:#fff}
.lca-list{flex:1;overflow-y:auto}
.lca-item{padding:13px 14px;border-bottom:1px solid #f1f5f9;cursor:pointer;transition:background .12s;position:relative}
.lca-item:hover{background:#f8fafc}
.lca-item.active{background:#eff6ff;border-left:3px solid #0d6efd}
.lca-item-name{font-weight:600;font-size:.88rem;color:#1e293b;margin-bottom:2px}
.lca-item-phone{font-size:.75rem;color:#64748b;margin-bottom:4px}
.lca-item-preview{font-size:.78rem;color:#94a3b8;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:260px}
.lca-item-meta{display:flex;justify-content:space-between;align-items:center;margin-top:4px}
.lca-item-time{font-size:.68rem;color:#94a3b8}
.lca-item-badge{background:#dc3545;color:#fff;font-size:.65rem;padding:2px 6px;border-radius:10px;font-weight:700}
.lca-status-dot{display:inline-block;width:8px;height:8px;border-radius:50%;margin-right:4px}
.lca-status-dot.waiting{background:#f59e0b}
.lca-status-dot.active{background:#22c55e}
.lca-status-dot.closed{background:#94a3b8}
/* Chat window */
.lca-main{flex:1;display:flex;flex-direction:column;background:#f8fafc}
.lca-empty{flex:1;display:flex;flex-direction:column;align-items:center;justify-content:center;color:#94a3b8}
.lca-empty svg{width:56px;height:56px;margin-bottom:12px;opacity:.4}
.lca-chat-head{padding:14px 20px;background:#fff;border-bottom:1px solid #e2e8f0;flex-shrink:0;display:flex;align-items:center;justify-content:space-between}
.lca-chat-head-info h6{margin:0;font-weight:700;font-size:.95rem}
.lca-chat-head-info small{font-size:.75rem;color:#64748b}
.lca-messages{flex:1;overflow-y:auto;padding:18px 20px;display:flex;flex-direction:column;gap:10px;scroll-behavior:smooth}
.lca-msg{display:flex;flex-direction:column;max-width:72%}
.lca-msg.guest{align-self:flex-start}
.lca-msg.staff{align-self:flex-end;align-items:flex-end}
.lca-msg-name{font-size:.7rem;color:#94a3b8;margin-bottom:2px;font-weight:500}
.lca-msg-bubble{padding:10px 14px;border-radius:14px;font-size:.88rem;line-height:1.5;word-break:break-word}
.lca-msg.guest .lca-msg-bubble{background:#fff;color:#1e293b;border-bottom-left-radius:4px;box-shadow:0 1px 3px rgba(0,0,0,.06)}
.lca-msg.staff .lca-msg-bubble{background:#0d6efd;color:#fff;border-bottom-right-radius:4px}
.lca-msg-time{font-size:.67rem;color:#94a3b8;margin-top:2px}
.lca-msg-img{max-width:280px;max-height:220px;border-radius:10px;cursor:pointer;object-fit:cover}
.lca-msg-file{display:inline-flex;align-items:center;gap:6px;padding:8px 12px;border-radius:10px;text-decoration:none;font-size:.8rem;font-weight:500}
.lca-msg.guest .lca-msg-file{background:#f1f5f9;color:#1e293b}
.lca-msg.staff .lca-msg-file{background:rgba(255,255,255,.15);color:#fff}
#lca-typing{padding:4px 20px;font-size:.75rem;color:#94a3b8;min-height:20px}
.lca-reply-area{padding:12px 16px;background:#fff;border-top:1px solid #e2e8f0;flex-shrink:0}
.lca-reply-row{display:flex;align-items:flex-end;gap:10px}
#lca-reply-input{flex:1;border:1.5px solid #e2e8f0;border-radius:10px;padding:10px 13px;font-size:.88rem;resize:none;max-height:120px;outline:none;transition:border-color .15s;background:#f8fafc}
#lca-reply-input:focus{border-color:#0d6efd;background:#fff}
.lca-reply-send{background:#0d6efd;color:#fff;border:none;border-radius:9px;padding:10px 18px;font-weight:600;font-size:.85rem;cursor:pointer;transition:background .15s;flex-shrink:0;height:42px}
.lca-reply-send:hover{background:#0b5ed7}
.lca-reply-attach{background:none;border:1.5px solid #e2e8f0;border-radius:9px;padding:10px;cursor:pointer;color:#64748b;height:42px;width:42px;display:flex;align-items:center;justify-content:center;flex-shrink:0;transition:all .15s}
.lca-reply-attach:hover{border-color:#0d6efd;color:#0d6efd}
.lca-close-btn{background:#dc3545;color:#fff;border:none;border-radius:8px;padding:7px 14px;font-size:.82rem;font-weight:600;cursor:pointer;transition:background .15s}
.lca-close-btn:hover{background:#b91c1c}
.lca-assign-select{font-size:.8rem;border:1.5px solid #e2e8f0;border-radius:8px;padding:6px 10px;outline:none}
.lca-file-preview{padding:8px 14px;background:#eff6ff;border-top:1px solid #dbeafe;display:none;align-items:center;gap:8px;font-size:.8rem;color:#1e293b}
.lca-file-preview.show{display:flex}
</style>
@endpush

@section('content')
<script>console.log('[lc-admin] section content loaded');</script>
<div class="lca-wrap">

    {{-- ── Chat list sidebar ──────────────────────────────────────────────── --}}
    <div class="lca-sidebar">
        <div class="lca-sidebar-head">
            <h6>Customer Support <span id="lca-total-badge" class="badge bg-danger" style="font-size:.65rem;display:none"></span></h6>
            <div class="lca-search">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
                <input type="text" id="lca-search" placeholder="Search by name or phone…">
            </div>
        </div>
        <div class="lca-filter-tabs">
            <button class="lca-filter-tab active" data-status="all">All</button>
            <button class="lca-filter-tab" data-status="waiting">Waiting</button>
            <button class="lca-filter-tab" data-status="active">Active</button>
            <button class="lca-filter-tab" data-status="closed">Closed</button>
        </div>
        <div class="lca-list" id="lca-list">
            <div style="padding:40px 20px;text-align:center;color:#94a3b8;font-size:.85rem">Loading chats…</div>
        </div>
    </div>

    {{-- ── Chat window ─────────────────────────────────────────────────────── --}}
    <div class="lca-main" id="lca-main">
        <div class="lca-empty" id="lca-empty">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M20 2H4a2 2 0 00-2 2v18l4-4h14a2 2 0 002-2V4a2 2 0 00-2-2z"/></svg>
            <div style="font-size:1rem;font-weight:600;margin-bottom:4px">Select a conversation</div>
            <div style="font-size:.82rem">Choose a chat from the left panel to reply</div>
        </div>

        <div id="lca-chat-window" style="display:none;flex:1;flex-direction:column;overflow:hidden">
            <div class="lca-chat-head">
                <div class="lca-chat-head-info">
                    <h6 id="lca-cw-name">—</h6>
                    <small id="lca-cw-sub">—</small>
                </div>
                <div style="display:flex;align-items:center;gap:10px">
                    <select class="lca-assign-select" id="lca-assign-select" onchange="lcaAssign(this.value)" style="display:none">
                        <option value="">Assign to…</option>
                        @foreach($agents as $agent)
                            <option value="{{ $agent->id }}">{{ $agent->name }}</option>
                        @endforeach
                    </select>
                    <button class="lca-close-btn" id="lca-cw-close-btn" onclick="lcaCloseChat()" style="display:none">Close Chat</button>
                </div>
            </div>

            <div id="lca-messages" class="lca-messages"></div>
            <div id="lca-admin-typing"></div>

            <div class="lca-file-preview" id="lca-admin-fp">
                <span id="lca-admin-fp-name" style="flex:1;overflow:hidden;text-overflow:ellipsis;white-space:nowrap"></span>
                <button onclick="lcaRemoveFile()" style="background:none;border:none;color:#dc3545;cursor:pointer">✕</button>
            </div>
            <div class="lca-reply-area" id="lca-reply-area" style="display:none">
                <div class="lca-reply-row">
                    <label for="lca-attach-input" class="lca-reply-attach" title="Attach file">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M21.44 11.05l-9.19 9.19a6 6 0 01-8.49-8.49l9.19-9.19a4 4 0 015.66 5.66l-9.2 9.19a2 2 0 01-2.83-2.83l8.49-8.48"/></svg>
                    </label>
                    <input type="file" id="lca-attach-input" style="display:none" accept="image/*,.pdf,.doc,.docx">
                    <textarea id="lca-reply-input" rows="1" placeholder="Type a reply…"></textarea>
                    <button class="lca-reply-send" onclick="lcaSend()">Send</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const NODE_URL   = '{{ config("chat.node_url") }}';
const CSRF       = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

let currentChatId = null;
let currentStatus = null;
let staffSocket   = null;
let adminFile     = null;
let filterStatus  = 'all';
let searchQ       = '';

// ── Fetch chat list ───────────────────────────────────────────────────────────
async function lcaLoadList() {
    const params = new URLSearchParams();
    if (filterStatus !== 'all') params.set('status', filterStatus);
    if (searchQ) params.set('q', searchQ);

    try {
        const res = await fetch(`/admin/live-chat/chats?${params}`, {
            credentials: 'same-origin',
            headers: { Accept: 'application/json' },
        });

        if (!res.ok) {
            const el = document.getElementById('lca-list');
            el.innerHTML = `<div style="padding:20px;color:#dc3545;font-size:.82rem">Error ${res.status}: Failed to load chats</div>`;
            return;
        }

        const data  = await res.json();
        const chats = data.data ?? data.chats ?? (Array.isArray(data) ? data : []);
        renderList(chats);

        const total = chats.filter(c => c.unread_count > 0).reduce((s, c) => s + (c.unread_count || 0), 0);
        const b = document.getElementById('lca-total-badge');
        if (total > 0) { b.textContent = total; b.style.display = 'inline-flex'; }
        else b.style.display = 'none';

    } catch (err) {
        console.error('[lca] loadList error:', err);
        const el = document.getElementById('lca-list');
        if (el) el.innerHTML = `<div style="padding:20px;color:#dc3545;font-size:.82rem">Load failed: ${err.message}</div>`;
    }
}

function renderList(chats) {
    const el = document.getElementById('lca-list');
    if (!chats.length) {
        el.innerHTML = '<div style="padding:40px 20px;text-align:center;color:#94a3b8;font-size:.85rem">No chats found</div>';
        return;
    }
    el.innerHTML = chats.map(c => `
        <div class="lca-item ${c.id == currentChatId ? 'active' : ''}" onclick="lcaOpenChat(${c.id})" id="lca-item-${c.id}">
            <div style="display:flex;justify-content:space-between;align-items:flex-start">
                <div class="lca-item-name"><span class="lca-status-dot ${escH(c.status)}"></span>${escH(c.guest_name)}</div>
                <div class="lca-item-time">${timeAgo(c.last_message_at)}</div>
            </div>
            <div class="lca-item-phone">${escH(c.guest_phone || '—')}</div>
            <div style="display:flex;justify-content:space-between;align-items:center">
                <div class="lca-item-preview">${escH(c.latest_message?.message || (c.latest_message?.attachment ? '📎 Attachment' : 'No messages yet'))}</div>
                ${c.unread_count > 0 ? `<span class="lca-item-badge">${c.unread_count}</span>` : ''}
            </div>
        </div>`).join('');
}

// ── Open chat ─────────────────────────────────────────────────────────────────
async function lcaOpenChat(chatId) {
    currentChatId = chatId;
    document.getElementById('lca-empty').style.display = 'none';
    const cw = document.getElementById('lca-chat-window');
    cw.style.display = 'flex';

    document.querySelectorAll('.lca-item').forEach(el => el.classList.remove('active'));
    const item = document.getElementById(`lca-item-${chatId}`);
    if (item) item.classList.add('active');

    // Load messages
    document.getElementById('lca-messages').innerHTML = '<div style="text-align:center;color:#94a3b8;font-size:.82rem;padding:30px">Loading…</div>';

    const res   = await fetch(`/admin/live-chat/${chatId}/messages`, { headers: { Accept: 'application/json' } });
    const data  = await res.json();
    const msgs  = data.messages ?? data;
    const chat  = data.chat ?? {};

    currentStatus = chat.status ?? 'active';

    document.getElementById('lca-cw-name').textContent = chat.guest_name ?? 'Guest';
    document.getElementById('lca-cw-sub').textContent  = (chat.guest_phone ?? '') + (chat.assigned_to_name ? ` • Assigned: ${chat.assigned_to_name}` : '');

    const box = document.getElementById('lca-messages');
    box.innerHTML = '';
    if (!msgs.length) {
        box.innerHTML = '<div style="text-align:center;color:#94a3b8;font-size:.82rem;padding:30px">No messages yet</div>';
    } else {
        msgs.forEach(m => renderMsg(m));
    }
    box.scrollTop = box.scrollHeight;

    const replyArea = document.getElementById('lca-reply-area');
    const closeBtn  = document.getElementById('lca-cw-close-btn');
    const assignSel = document.getElementById('lca-assign-select');
    if (currentStatus === 'closed') {
        replyArea.style.display = 'none';
        closeBtn.style.display  = 'none';
    } else {
        replyArea.style.display = 'block';
        closeBtn.style.display  = 'inline-block';
    }
    assignSel.style.display = currentStatus !== 'closed' ? 'block' : 'none';
    if (chat.assigned_to) assignSel.value = chat.assigned_to;

    // Connect socket for this chat
    lcaJoinSocket(chatId);

    // Refresh list to clear unread badge
    setTimeout(lcaLoadList, 500);
}

function renderMsg(msg) {
    const isGuest = msg.sender_type === 'guest';
    const box     = document.getElementById('lca-messages');
    let content   = '';
    if (msg.message) content += `<div>${escH(msg.message)}</div>`;
    if (msg.attachment) {
        const fn = escH(msg.attachment_name || 'Attachment');
        if (msg.attachment_type === 'image' || /\.(jpe?g|png|gif|webp)$/i.test(msg.attachment)) {
            content += `<a href="${msg.attachment}" target="_blank"><img class="lca-msg-img" src="${msg.attachment}" alt="${fn}"></a>`;
        } else {
            content += `<a href="${msg.attachment}" target="_blank" class="lca-msg-file">📎 ${fn}</a>`;
        }
    }
    box.insertAdjacentHTML('beforeend', `
        <div class="lca-msg ${isGuest ? 'guest' : 'staff'}">
            <div class="lca-msg-name">${escH(msg.sender_name || (isGuest ? 'Guest' : 'Support'))}</div>
            <div class="lca-msg-bubble">${content}</div>
            <div class="lca-msg-time">${timeStr(msg.created_at)}</div>
        </div>`);
}

// ── Admin socket ──────────────────────────────────────────────────────────────
async function lcaConnectStaffSocket() {
    if (typeof io === 'undefined') return;
    const tokenRes = await fetch('/admin/live-chat/staff-token', { headers: { Accept: 'application/json' } });
    const { token } = await tokenRes.json();

    staffSocket = io(NODE_URL, {
        auth: { token },
        reconnection: true, reconnectionDelay: 2000, reconnectionAttempts: 10,
    });

    staffSocket.on('connect', () => console.log('[admin-lc] Socket connected'));
    staffSocket.on('new_livechat_chat', (d) => {
        lcaLoadList();
        showToast(`New chat from ${d.guest_name || 'Guest'}`);
    });
    staffSocket.on('new_livechat_message', (d) => {
        lcaLoadList();
        if (d.chat_id == currentChatId) {
            const box = document.getElementById('lca-messages');
            const loading = box.querySelector('[style*="text-align:center"]');
            if (loading) loading.remove();
            renderMsg(d.message);
            box.scrollTop = box.scrollHeight;
        } else {
            showToast('New message from a customer');
        }
    });
    staffSocket.on('guest_typing', (d) => {
        if (d.chat_id == currentChatId) {
            document.getElementById('lca-admin-typing').innerHTML = `<div style="padding:4px 20px;font-size:.73rem;color:#94a3b8">${escH(d.name)} is typing…</div>`;
            setTimeout(() => document.getElementById('lca-admin-typing').innerHTML = '', 2500);
        }
    });
    staffSocket.on('livechat_status_changed', (d) => {
        if (d.chat_id == currentChatId && d.status === 'closed') {
            document.getElementById('lca-reply-area').style.display = 'none';
            document.getElementById('lca-cw-close-btn').style.display = 'none';
            currentStatus = 'closed';
        }
        lcaLoadList();
    });
}

function lcaJoinSocket(chatId) {
    if (!staffSocket?.connected) return;
    staffSocket.emit('join_room', { chat_id: chatId });
}

// ── Send reply ────────────────────────────────────────────────────────────────
async function lcaSend() {
    const msg = document.getElementById('lca-reply-input').value.trim();
    if (!msg && !adminFile) return;
    if (!currentChatId) return;

    const btn = document.querySelector('.lca-reply-send');
    btn.disabled = true; btn.textContent = '…';

    try {
        const fd = new FormData();
        if (msg) fd.append('message', msg);
        if (adminFile) {
            fd.append('attachment', adminFile.url);
            fd.append('attachment_type', adminFile.type);
            fd.append('attachment_name', adminFile.name);
            fd.append('attachment_size', adminFile.size);
        }

        const res = await fetch(`/admin/live-chat/${currentChatId}/messages`, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': CSRF, Accept: 'application/json' },
            body: fd,
        });
        const data = await res.json();
        if (!res.ok) throw new Error(data.message || 'Send failed');

        document.getElementById('lca-reply-input').value = '';
        lcaRemoveFile();
        renderMsg(data);
        document.getElementById('lca-messages').scrollTop = 999999;
        lcaLoadList();
    } catch (e) {
        alert(e.message || 'Failed to send');
    } finally {
        btn.disabled = false; btn.textContent = 'Send';
    }
}

// ── Close chat ────────────────────────────────────────────────────────────────
async function lcaCloseChat() {
    if (!currentChatId) return;
    if (!confirm('Close this chat? The customer will see a closed message.')) return;

    const res = await fetch(`/admin/live-chat/${currentChatId}/close`, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': CSRF, Accept: 'application/json' },
    });
    if (res.ok) {
        document.getElementById('lca-reply-area').style.display = 'none';
        document.getElementById('lca-cw-close-btn').style.display = 'none';
        currentStatus = 'closed';
        lcaLoadList();
    }
}

// ── Assign ────────────────────────────────────────────────────────────────────
async function lcaAssign(agentId) {
    if (!currentChatId) return;
    await fetch(`/admin/live-chat/${currentChatId}/assign`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, Accept: 'application/json' },
        body: JSON.stringify({ agent_id: agentId }),
    });
}

// ── File upload ───────────────────────────────────────────────────────────────
document.getElementById('lca-attach-input')?.addEventListener('change', async (e) => {
    const file = e.target.files[0];
    if (!file || !currentChatId) return;
    const fp   = document.getElementById('lca-admin-fp');
    const fn   = document.getElementById('lca-admin-fp-name');
    fn.textContent = file.name + ' — Uploading…';
    fp.classList.add('show');

    const fd = new FormData();
    fd.append('file', file);
    try {
        const res  = await fetch(`/admin/live-chat/${currentChatId}/upload`, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': CSRF },
            body: fd,
        });
        const data = await res.json();
        if (!res.ok) throw new Error(data.message || 'Upload failed');
        adminFile = data;
        fn.textContent = file.name + ' — Ready';
    } catch (err) {
        fn.textContent = 'Upload failed';
        setTimeout(lcaRemoveFile, 2000);
    }
});

function lcaRemoveFile() {
    adminFile = null;
    document.getElementById('lca-admin-fp').classList.remove('show');
    document.getElementById('lca-attach-input').value = '';
}

// ── Filters ───────────────────────────────────────────────────────────────────
document.querySelectorAll('.lca-filter-tab').forEach(btn => {
    btn.addEventListener('click', () => {
        document.querySelectorAll('.lca-filter-tab').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        filterStatus = btn.dataset.status;
        lcaLoadList();
    });
});

let searchTimer;
document.getElementById('lca-search')?.addEventListener('input', (e) => {
    clearTimeout(searchTimer);
    searchQ = e.target.value.trim();
    searchTimer = setTimeout(lcaLoadList, 350);
});

// ── Reply textarea Enter ──────────────────────────────────────────────────────
document.getElementById('lca-reply-input')?.addEventListener('keydown', (e) => {
    if (e.key === 'Enter' && !e.shiftKey) { e.preventDefault(); lcaSend(); }
});
document.getElementById('lca-reply-input')?.addEventListener('input', () => {
    const el = document.getElementById('lca-reply-input');
    el.style.height = 'auto';
    el.style.height = Math.min(el.scrollHeight, 120) + 'px';
});

// ── Toast ─────────────────────────────────────────────────────────────────────
function showToast(msg) {
    const t = document.createElement('div');
    t.textContent = msg;
    Object.assign(t.style, {position:'fixed',bottom:'80px',right:'20px',background:'#1e293b',color:'#fff',padding:'10px 16px',borderRadius:'10px',fontSize:'.85rem',zIndex:'9999',opacity:'0',transition:'opacity .3s'});
    document.body.appendChild(t);
    setTimeout(() => t.style.opacity = '1', 50);
    setTimeout(() => { t.style.opacity = '0'; setTimeout(() => t.remove(), 400); }, 4000);
}

// ── Helpers ───────────────────────────────────────────────────────────────────
function escH(s) { return String(s ?? '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;'); }
function timeStr(iso) { if (!iso) return ''; return new Date(iso).toLocaleTimeString([], { hour:'2-digit', minute:'2-digit' }); }
function timeAgo(iso) {
    if (!iso) return '';
    const diff = (Date.now() - new Date(iso)) / 1000;
    if (diff < 60) return 'now';
    if (diff < 3600) return Math.floor(diff/60) + 'm';
    if (diff < 86400) return Math.floor(diff/3600) + 'h';
    return Math.floor(diff/86400) + 'd';
}

// ── Boot ──────────────────────────────────────────────────────────────────────
(function () {
    // Debug: show fetch result directly in the list
    const debugEl = document.getElementById('lca-list');
    if (debugEl) {
        debugEl.innerHTML = '<div style="padding:10px;font-size:.75rem;color:#666">JS running, fetching...</div>';
        fetch('/admin/live-chat/chats', { credentials: 'same-origin', headers: { Accept: 'application/json' } })
            .then(r => r.text())
            .then(t => { debugEl.innerHTML = '<div style="padding:10px;font-size:.72rem;overflow:auto;max-height:300px"><pre>' + t.substring(0,500) + '</pre></div>'; })
            .catch(e => { debugEl.innerHTML = '<div style="padding:10px;color:red">' + e.message + '</div>'; });
    }

    lcaLoadList();
    setInterval(lcaLoadList, 20000);

    const s = document.createElement('script');
    s.src = NODE_URL + '/socket.io/socket.io.js';
    s.onload = () => lcaConnectStaffSocket();
    s.onerror = () => console.warn('[admin-lc] socket.io not loaded');
    document.head.appendChild(s);
})();
</script>
@endpush
