{{-- Live Chat Widget — shown on all frontend pages --}}
<style>
#lc-btn{position:fixed;bottom:24px;right:24px;z-index:9999;width:56px;height:56px;border-radius:50%;background:var(--bs-primary,#0d6efd);border:none;cursor:pointer;box-shadow:0 4px 16px rgba(13,110,253,.4);display:flex;align-items:center;justify-content:center;transition:transform .2s,box-shadow .2s}
#lc-btn:hover{transform:scale(1.08);box-shadow:0 6px 20px rgba(13,110,253,.5)}
#lc-btn svg{width:26px;height:26px;fill:#fff;transition:opacity .2s}
#lc-btn .lc-close-ico{display:none}
#lc-btn.open .lc-chat-ico{display:none}
#lc-btn.open .lc-close-ico{display:block}
#lc-badge{position:absolute;top:-4px;right:-4px;background:#dc3545;color:#fff;font-size:10px;font-weight:700;border-radius:50%;width:18px;height:18px;display:none;align-items:center;justify-content:center;line-height:1}
#lc-badge.show{display:flex}
#lc-panel{position:fixed;bottom:90px;right:24px;z-index:9998;width:360px;max-height:560px;background:#fff;border-radius:16px;box-shadow:0 8px 40px rgba(0,0,0,.18);display:flex;flex-direction:column;overflow:hidden;overscroll-behavior:contain;transform:translateY(20px) scale(.96);opacity:0;pointer-events:none;transition:transform .25s cubic-bezier(.34,1.56,.64,1),opacity .2s}
#lc-panel.open{transform:translateY(0) scale(1);opacity:1;pointer-events:all}
.lc-header{background:linear-gradient(135deg,#0d6efd,#0056d3);padding:18px 16px 16px;color:#fff;flex-shrink:0}
.lc-header-top{display:flex;align-items:center;gap:10px;margin-bottom:6px}
.lc-avatar{width:40px;height:40px;border-radius:50%;background:rgba(255,255,255,.2);display:flex;align-items:center;justify-content:center;font-size:1.2rem;font-weight:700;flex-shrink:0}
.lc-header h6{margin:0;font-size:1rem;font-weight:700}
.lc-header small{font-size:.75rem;opacity:.85}
.lc-online{display:inline-flex;align-items:center;gap:4px;font-size:.72rem;opacity:.9}
.lc-online span{width:7px;height:7px;border-radius:50%;background:#4ade80;animation:lc-pulse 1.8s infinite}
@keyframes lc-pulse{0%,100%{opacity:1}50%{opacity:.4}}
.lc-body{flex:1;overflow:hidden;display:flex;flex-direction:column}
/* Form state */
#lc-form{padding:24px 20px;display:flex;flex-direction:column;gap:14px}
#lc-form h6{margin:0 0 4px;font-size:.95rem;color:#1e293b;font-weight:600}
#lc-form p{margin:0;font-size:.83rem;color:#64748b}
.lc-field label{font-size:.8rem;font-weight:600;color:#374151;margin-bottom:4px;display:block}
.lc-field input{width:100%;padding:10px 12px;border:1.5px solid #e2e8f0;border-radius:10px;font-size:.9rem;outline:none;transition:border-color .15s}
.lc-field input:focus{border-color:#0d6efd}
.lc-start-btn{background:#0d6efd;color:#fff;border:none;border-radius:10px;padding:12px;font-weight:600;cursor:pointer;font-size:.9rem;transition:background .15s}
.lc-start-btn:hover{background:#0b5ed7}
/* Chat state */
#lc-chat{display:none;flex-direction:column;flex:1;min-height:0}
#lc-chat.visible{display:flex}
#lc-messages{flex:1;overflow-y:auto;padding:14px 14px 0;display:flex;flex-direction:column;gap:8px;scroll-behavior:smooth;min-height:0;overscroll-behavior:contain}
.lc-msg{display:flex;flex-direction:column;max-width:82%}
.lc-msg.guest{align-self:flex-end;align-items:flex-end}
.lc-msg.staff{align-self:flex-start;align-items:flex-start}
.lc-msg-name{font-size:.68rem;color:#94a3b8;margin-bottom:2px;font-weight:500}
.lc-msg-bubble{padding:9px 13px;border-radius:14px;font-size:.85rem;line-height:1.45;word-break:break-word}
.lc-msg.guest .lc-msg-bubble{background:#0d6efd;color:#fff;border-bottom-right-radius:4px}
.lc-msg.staff .lc-msg-bubble{background:#f1f5f9;color:#1e293b;border-bottom-left-radius:4px}
.lc-msg-time{font-size:.65rem;color:#94a3b8;margin-top:2px}
.lc-msg-img{max-width:200px;max-height:200px;border-radius:10px;cursor:pointer;object-fit:cover}
.lc-msg-file{display:inline-flex;align-items:center;gap:6px;padding:8px 12px;border-radius:10px;text-decoration:none;font-size:.8rem;font-weight:500}
.lc-msg.guest .lc-msg-file{background:rgba(255,255,255,.15);color:#fff}
.lc-msg.staff .lc-msg-file{background:#e2e8f0;color:#1e293b}
#lc-typing{padding:4px 14px 0;font-size:.73rem;color:#94a3b8;min-height:18px}
.lc-input-area{padding:10px 12px;border-top:1px solid #f1f5f9;display:flex;align-items:center;gap:8px;flex-shrink:0}
#lc-file-preview{padding:6px 12px;background:#f8fafc;border-top:1px solid #e2e8f0;display:none;align-items:center;gap:8px;font-size:.78rem}
#lc-file-preview.show{display:flex}
#lc-input{flex:1;border:none;outline:none;font-size:.88rem;resize:none;max-height:80px;background:transparent;line-height:1.4}
.lc-attach-btn{background:none;border:none;cursor:pointer;color:#94a3b8;padding:4px;border-radius:6px;line-height:1;transition:color .15s}
.lc-attach-btn:hover{color:#0d6efd}
.lc-send-btn{background:#0d6efd;color:#fff;border:none;border-radius:8px;width:34px;height:34px;cursor:pointer;display:flex;align-items:center;justify-content:center;transition:background .15s;flex-shrink:0}
.lc-send-btn:hover{background:#0b5ed7}
.lc-closed-bar{padding:10px 14px;background:#fef2f2;color:#dc2626;font-size:.8rem;text-align:center;flex-shrink:0;border-top:1px solid #fecaca}
#lc-status-bar{padding:6px 14px;background:#f0fdf4;color:#166534;font-size:.75rem;text-align:center;border-top:1px solid #dcfce7;display:none}
#lc-status-bar.show{display:block}
@media(max-width:400px){#lc-panel{right:8px;width:calc(100vw - 16px)}}
</style>

<div id="lc-btn" role="button" aria-label="Open chat" onclick="lcToggle()">
    <svg class="lc-chat-ico" viewBox="0 0 24 24"><path d="M20 2H4a2 2 0 00-2 2v18l4-4h14a2 2 0 002-2V4a2 2 0 00-2-2z"/></svg>
    <svg class="lc-close-ico" viewBox="0 0 24 24"><path d="M18 6L6 18M6 6l12 12" stroke="#fff" stroke-width="2.5" stroke-linecap="round" fill="none"/></svg>
    <div id="lc-badge"></div>
</div>

<div id="lc-panel">
    <div class="lc-header">
        <div class="lc-header-top">
            <div class="lc-avatar">SG</div>
            <div>
                <h6>ShopGram Support</h6>
                <div class="lc-online"><span></span> Online — we reply fast</div>
            </div>
        </div>
    </div>

    <div class="lc-body">
        {{-- Form state --}}
        <div id="lc-form">
            <div>
                <h6>Start a conversation</h6>
                <p>Tell us your name and number to connect with our support team.</p>
            </div>
            <div class="lc-field">
                <label>Full Name <span style="color:#dc3545">*</span></label>
                <input type="text" id="lc-name" placeholder="Enter your name" maxlength="100">
            </div>
            <div class="lc-field">
                <label>Phone Number <span style="color:#dc3545">*</span></label>
                <input type="tel" id="lc-phone" placeholder="01XXXXXXXXX" maxlength="20">
            </div>
            <div id="lc-form-err" style="font-size:.8rem;color:#dc3545;display:none"></div>
            <button class="lc-start-btn" onclick="lcStartChat()">Start Chat →</button>
        </div>

        {{-- Chat state --}}
        <div id="lc-chat">
            <div id="lc-status-bar"></div>
            <div id="lc-messages">
                <div style="text-align:center;color:#94a3b8;font-size:.8rem;padding:20px 0">Loading messages...</div>
            </div>
            <div id="lc-typing"></div>
            <div id="lc-file-preview">
                <span id="lc-fp-name" style="flex:1;overflow:hidden;text-overflow:ellipsis;white-space:nowrap"></span>
                <button onclick="lcRemoveFile()" style="background:none;border:none;color:#dc3545;cursor:pointer;font-size:1rem">✕</button>
            </div>
            <div class="lc-input-area">
                <label for="lc-file-input" class="lc-attach-btn" title="Attach file">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M21.44 11.05l-9.19 9.19a6 6 0 01-8.49-8.49l9.19-9.19a4 4 0 015.66 5.66l-9.2 9.19a2 2 0 01-2.83-2.83l8.49-8.48"/></svg>
                </label>
                <input type="file" id="lc-file-input" style="display:none" accept="image/*,.pdf,.doc,.docx">
                <textarea id="lc-input" rows="1" placeholder="Write a message..."></textarea>
                <button class="lc-send-btn" onclick="lcSend()" title="Send">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.5" stroke-linecap="round"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
                </button>
            </div>
        </div>
    </div>
</div>

<script>
(function () {
    const NODE_URL   = '{{ config("chat.node_url") }}';
    const CSRF       = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    @auth
    const AUTH_NAME  = '{{ addslashes(auth()->user()->name) }}';
    const AUTH_PHONE = '{{ addslashes(auth()->user()->phone ?? '') }}';
    @else
    const AUTH_NAME  = null;
    const AUTH_PHONE = null;
    @endauth

    const STORE_KEY = 'shopgram_lc_session';
    let socket      = null;
    let currentFile = null;
    let typingTimer = null;
    let panelOpen   = false;
    let initialized = false;

    // ── State helpers ────────────────────────────────────────────────────────
    function getSession()       { try { return JSON.parse(localStorage.getItem(STORE_KEY)); } catch { return null; } }
    function saveSession(data)  { localStorage.setItem(STORE_KEY, JSON.stringify(data)); }
    function clearSession()     { localStorage.removeItem(STORE_KEY); }

    function esc(s) {
        return String(s ?? '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
    }
    function timeStr(iso) {
        if (!iso) return '';
        return new Date(iso).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
    }
    function isImage(url) { return /\.(jpe?g|png|gif|webp)$/i.test(url || ''); }

    // ── Toggle ───────────────────────────────────────────────────────────────
    window.lcToggle = function () {
        panelOpen = !panelOpen;
        document.getElementById('lc-panel').classList.toggle('open', panelOpen);
        document.getElementById('lc-btn').classList.toggle('open', panelOpen);
        if (panelOpen && !initialized) lcInit();
    };

    function lcInit() {
        initialized = true;
        const session = getSession();

        // Pre-fill for logged-in users
        if (AUTH_NAME) {
            const ni = document.getElementById('lc-name');
            const pi = document.getElementById('lc-phone');
            if (ni) ni.value = AUTH_NAME;
            if (pi && AUTH_PHONE) pi.value = AUTH_PHONE;
        }

        if (session && session.chat_id && session.status !== 'closed') {
            showChatUI();
            loadHistory(session);
            connectSocket(session);
        } else {
            showFormUI();
        }

        pollUnread();
    }

    // ── UI states ────────────────────────────────────────────────────────────
    function showFormUI() {
        document.getElementById('lc-form').style.display = 'flex';
        document.getElementById('lc-chat').classList.remove('visible');
    }
    function showChatUI() {
        document.getElementById('lc-form').style.display = 'none';
        document.getElementById('lc-chat').classList.add('visible');
    }

    // ── Start chat ───────────────────────────────────────────────────────────
    window.lcStartChat = async function () {
        const name  = document.getElementById('lc-name').value.trim();
        const phone = document.getElementById('lc-phone').value.trim();
        const err   = document.getElementById('lc-form-err');

        if (!name || !phone) {
            err.textContent = 'Please enter your name and phone number.';
            err.style.display = 'block';
            return;
        }
        err.style.display = 'none';

        const btn = document.querySelector('.lc-start-btn');
        btn.textContent = 'Connecting...';
        btn.disabled = true;

        try {
            const existingSession = getSession();
            const payload = {
                name, phone,
                session_id: existingSession?.session_id ?? null,
            };

            const res  = await fetch('/api/livechat/start', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
                body: JSON.stringify(payload),
            });
            const data = await res.json();
            if (!res.ok) throw new Error(data.message || 'Failed to start chat');

            const session = { chat_id: data.chat_id, session_id: data.session_id, guest_name: data.guest_name, status: 'waiting' };
            saveSession(session);

            showChatUI();
            document.getElementById('lc-messages').innerHTML = '<div style="text-align:center;color:#94a3b8;font-size:.8rem;padding:20px 0">Chat started. Our support team will be with you shortly.</div>';
            connectSocket(session);

        } catch (e) {
            err.textContent = e.message || 'Connection failed. Please try again.';
            err.style.display = 'block';
        } finally {
            btn.textContent = 'Start Chat →';
            btn.disabled = false;
        }
    };

    // ── Socket connection ────────────────────────────────────────────────────
    async function connectSocket(session) {
        if (socket) { socket.disconnect(); socket = null; }
        if (typeof io === 'undefined') return;

        try {
            const tokenRes = await fetch('/api/livechat/token', {
                headers: { 'X-Session-Id': session.session_id, 'X-Chat-Id': String(session.chat_id), 'Accept': 'application/json' }
            });
            const { token } = await tokenRes.json();

            socket = io(NODE_URL, {
                auth: { token },
                query: { livechat_id: session.chat_id },
                reconnection: true,
                reconnectionDelay: 2000,
                reconnectionAttempts: 5,
            });

            socket.on('connect', () => {
                const bar = document.getElementById('lc-status-bar');
                bar.textContent = 'Connected to support';
                bar.classList.add('show');
                setTimeout(() => bar.classList.remove('show'), 3000);
            });

            socket.on('new_message', (msg) => {
                const box = document.getElementById('lc-messages');
                const empty = box.querySelector('[style*="text-align:center"]');
                if (empty) empty.remove();
                renderMessage(msg);
                box.scrollTop = box.scrollHeight;
                // Clear unread badge if panel is open
                if (panelOpen) document.getElementById('lc-badge').classList.remove('show');
            });

            socket.on('user_typing', (d) => {
                if (d.type === 'staff') {
                    document.getElementById('lc-typing').textContent = `${d.name} is typing…`;
                    clearTimeout(typingTimer);
                    typingTimer = setTimeout(() => document.getElementById('lc-typing').textContent = '', 2500);
                }
            });

            socket.on('livechat_closed', (d) => {
                document.getElementById('lc-input').disabled = true;
                document.querySelector('.lc-send-btn').disabled = true;
                document.getElementById('lc-typing').innerHTML =
                    `<div class="lc-closed-bar">${esc(d.message)}</div>`;
                const s = getSession();
                if (s) saveSession({ ...s, status: 'closed' });
            });

        } catch (e) {
            console.error('[livechat] Socket connect failed:', e);
        }
    }

    // ── Load history ─────────────────────────────────────────────────────────
    async function loadHistory(session) {
        try {
            const res  = await fetch(`/api/livechat/${session.chat_id}/messages`, {
                headers: { 'X-Session-Id': session.session_id, 'Accept': 'application/json' }
            });
            const msgs = await res.json();
            const box  = document.getElementById('lc-messages');
            box.innerHTML = '';
            if (msgs.length === 0) {
                box.innerHTML = '<div style="text-align:center;color:#94a3b8;font-size:.8rem;padding:20px 0">No messages yet. Say hello!</div>';
            } else {
                msgs.forEach(renderMessage);
            }
            box.scrollTop = box.scrollHeight;
        } catch (e) { console.error('[livechat] History load failed:', e); }
    }

    // ── Render message ───────────────────────────────────────────────────────
    function renderMessage(msg) {
        const isGuest = msg.sender_type === 'guest';
        const box     = document.getElementById('lc-messages');
        let content   = '';

        if (msg.message) content += `<div>${esc(msg.message)}</div>`;

        if (msg.attachment) {
            const fname = esc(msg.attachment_name || 'Attachment');
            if (msg.attachment_type === 'image' || isImage(msg.attachment)) {
                content += `<a href="${msg.attachment}" target="_blank"><img class="lc-msg-img" src="${msg.attachment}" alt="${fname}"></a>`;
            } else {
                content += `<a href="${msg.attachment}" target="_blank" class="lc-msg-file">📎 ${fname}</a>`;
            }
        }

        box.insertAdjacentHTML('beforeend', `
            <div class="lc-msg ${isGuest ? 'guest' : 'staff'}">
                ${!isGuest ? `<div class="lc-msg-name">${esc(msg.sender_name || 'Support')}</div>` : ''}
                <div class="lc-msg-bubble">${content}</div>
                <div class="lc-msg-time">${timeStr(msg.created_at)}</div>
            </div>`);
    }

    // ── Send ─────────────────────────────────────────────────────────────────
    window.lcSend = function () {
        const input = document.getElementById('lc-input');
        const msg   = input.value.trim();
        if (!msg && !currentFile) return;
        if (!socket || !socket.connected) {
            alert('Not connected. Please wait a moment and try again.');
            return;
        }

        const data = { message: msg };
        if (currentFile) data.attachment = currentFile;

        socket.emit('send_message', data);
        input.value = '';
        lcRemoveFile();
    };

    // ── File upload ──────────────────────────────────────────────────────────
    document.getElementById('lc-file-input')?.addEventListener('change', async (e) => {
        const file = e.target.files[0];
        if (!file) return;
        const session = getSession();
        if (!session) return;

        const preview  = document.getElementById('lc-file-preview');
        const nameEl   = document.getElementById('lc-fp-name');
        nameEl.textContent = file.name + ' — Uploading…';
        preview.classList.add('show');

        const fd = new FormData();
        fd.append('file', file);

        try {
            const res  = await fetch(`/api/livechat/${session.chat_id}/upload`, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': CSRF, 'X-Session-Id': session.session_id },
                body: fd,
            });
            const data = await res.json();
            if (!res.ok) throw new Error(data.message || 'Upload failed');
            currentFile = data;
            nameEl.textContent = file.name + ' — Ready';
        } catch (err) {
            nameEl.textContent = 'Upload failed. Try again.';
            setTimeout(lcRemoveFile, 2000);
        }
    });

    window.lcRemoveFile = function () {
        currentFile = null;
        document.getElementById('lc-file-preview').classList.remove('show');
        document.getElementById('lc-file-input').value = '';
    };

    // ── Typing + Enter ───────────────────────────────────────────────────────
    document.getElementById('lc-input')?.addEventListener('keydown', (e) => {
        if (e.key === 'Enter' && !e.shiftKey) { e.preventDefault(); lcSend(); }
    });
    document.getElementById('lc-input')?.addEventListener('input', () => {
        if (socket?.connected) socket.emit('typing');
        // Auto-grow
        const el = document.getElementById('lc-input');
        el.style.height = 'auto';
        el.style.height = Math.min(el.scrollHeight, 80) + 'px';
    });

    // ── Badge polling ────────────────────────────────────────────────────────
    function pollUnread() {
        const session = getSession();
        if (!session) return;
        fetch(`/api/livechat/unread?session_id=${session.session_id}`, { headers: { 'Accept': 'application/json' } })
            .then(r => r.json())
            .then(d => {
                const badge = document.getElementById('lc-badge');
                if (d.count > 0 && !panelOpen) {
                    badge.textContent = d.count > 9 ? '9+' : d.count;
                    badge.classList.add('show');
                } else {
                    badge.classList.remove('show');
                }
            }).catch(() => {});
        setTimeout(pollUnread, 30000);
    }

    // Load socket.io script dynamically
    if (!window.__lcSocketLoaded) {
        window.__lcSocketLoaded = true;
        const s = document.createElement('script');
        s.src = NODE_URL + '/socket.io/socket.io.js';
        s.onerror = () => console.warn('[livechat] socket.io not loaded — chat will work without real-time');
        document.head.appendChild(s);
    }
})();
</script>
