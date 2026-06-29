@extends('layouts.admin')
@section('title', 'Order '.$order->order_number)
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Order #{{ $order->order_number }}</h4>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.orders.invoice', $order) }}" class="btn btn-outline-secondary btn-sm" target="_blank"><i class="bi bi-printer"></i> Invoice</a>
        <a href="{{ route('admin.orders.invoice.pdf', $order) }}" class="btn btn-outline-danger btn-sm"><i class="bi bi-file-pdf"></i> PDF</a>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        {{-- Items --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white fw-bold">Order Items</div>
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead class="table-light">
                        <tr><th>Product</th><th>Variant</th><th>Qty</th><th>Unit Price</th><th>Total</th></tr>
                    </thead>
                    <tbody>
                        @foreach($order->items as $item)
                        <tr>
                            <td class="small">{{ $item->product_name }}</td>
                            <td class="text-muted small">{{ $item->variant_info ? collect($item->variant_info)->filter()->implode(', ') : '-' }}</td>
                            <td>{{ $item->quantity }}</td>
                            <td>৳{{ number_format($item->unit_price, 0) }}</td>
                            <td>৳{{ number_format($item->total_price, 0) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Update Status --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white fw-bold">Update Status</div>
            <div class="card-body">
                <form action="{{ route('admin.orders.status.update', $order) }}" method="POST">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-5">
                            <select name="status" class="form-select">
                                @foreach(['pending','confirmed','processing','packed','shipped','out_for_delivery','delivered','cancelled','returned','refunded'] as $s)
                                <option value="{{ $s }}" {{ $order->status === $s ? 'selected' : '' }}>{{ ucwords(str_replace('_',' ',$s)) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-5">
                            <input type="text" name="note" class="form-control" placeholder="Optional note...">
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100">Update</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- Assign Courier --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white fw-bold">Assign Courier</div>
            <div class="card-body">
                <form action="{{ route('admin.orders.courier.assign', $order) }}" method="POST">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-5">
                            <select name="courier_id" class="form-select">
                                <option value="">Select Courier</option>
                                @foreach($couriers as $c)
                                <option value="{{ $c->id }}" {{ $order->courier_id === $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-5">
                            <input type="text" name="courier_tracking_number" class="form-control" placeholder="Tracking number" value="{{ $order->courier_tracking_number }}">
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-success w-100">Assign</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- Status History --}}
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white fw-bold">Status Timeline</div>
            <div class="card-body">
                @foreach($order->statusHistories as $h)
                <div class="d-flex gap-3 mb-3 pb-3 border-bottom">
                    <div class="text-muted small" style="min-width:140px">{{ $h->created_at->format('d M Y H:i') }}</div>
                    <div>
                        <x-order-status-badge :status="$h->status" />
                        @if($h->note)<p class="text-muted small mb-0 mt-1">{{ $h->note }}</p>@endif
                        @if($h->updatedBy)<small class="text-muted">by {{ $h->updatedBy->name }}</small>@endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-white fw-bold">Order Summary</div>
            <div class="card-body small">
                <div class="d-flex justify-content-between mb-1"><span>Subtotal</span><span>৳{{ number_format($order->subtotal,0) }}</span></div>
                @if($order->discount_amount > 0)<div class="d-flex justify-content-between mb-1 text-success"><span>Discount</span><span>-৳{{ number_format($order->discount_amount,0) }}</span></div>@endif
                <div class="d-flex justify-content-between mb-1"><span>Shipping</span><span>৳{{ number_format($order->shipping_charge,0) }}</span></div>
                <hr class="my-1">
                <div class="d-flex justify-content-between fw-bold"><span>Total</span><span>৳{{ number_format($order->total,0) }}</span></div>
                <div class="mt-2 pt-2 border-top">
                    <div class="mb-1"><strong>Payment Method:</strong> {{ strtoupper($order->payment_method) }}</div>
                    @can('order.payment.update')
                    <form method="POST" action="{{ route('admin.orders.payment.status.update', $order) }}" class="d-flex align-items-center gap-2 mt-1">
                        @csrf
                        <label class="small fw-semibold mb-0 flex-shrink-0">Payment Status:</label>
                        <select name="payment_status" class="form-select form-select-sm"
                                onchange="this.form.submit()"
                                style="max-width:130px">
                            @foreach(['unpaid' => 'Unpaid', 'paid' => 'Paid', 'failed' => 'Failed', 'refunded' => 'Refunded'] as $val => $label)
                                <option value="{{ $val }}" {{ $order->payment_status === $val ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </form>
                    @else
                    <div><strong>Payment Status:</strong>
                        @php
                            $payBadge = match($order->payment_status) {
                                'paid'     => 'success',
                                'failed'   => 'danger',
                                'refunded' => 'warning',
                                default    => 'secondary',
                            };
                        @endphp
                        <span class="badge bg-{{ $payBadge }}">{{ ucfirst($order->payment_status) }}</span>
                    </div>
                    @endcan
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-white fw-bold">Customer</div>
            <div class="card-body small">
                <strong>{{ $order->user->name }}</strong><br>
                {{ $order->user->email }}<br>{{ $order->user->phone }}
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white fw-bold">Shipping Address</div>
            <div class="card-body small">
                @php $addr = $order->shipping_address @endphp
                {{ $addr['name'] ?? '' }}<br>{{ $addr['phone'] ?? '' }}<br>{{ $addr['address_line'] ?? '' }}<br>{{ ($addr['city'] ?? '') . ', ' . ($addr['district'] ?? '') }}
            </div>
        </div>

        @can('order.chat')
        @if($order->isChatOpen())
        <div class="card border-0 shadow-sm mt-3" id="orderChat">
            <div class="card-header bg-white fw-semibold d-flex justify-content-between align-items-center">
                <span><i class="bi bi-chat-dots me-2 text-primary"></i>Customer Chat</span>
                <span class="badge bg-success">Live</span>
            </div>
            <div class="card-body p-0">
                <div id="chatMessages" style="height:320px;overflow-y:auto;padding:16px;background:#f8fafc;">
                    <div class="text-center text-muted small py-4">Loading messages...</div>
                </div>
                <div class="border-top p-3">
                    <div id="filePreview" class="mb-2 d-none">
                        <div class="d-flex align-items-start gap-2 p-2 bg-light rounded">
                            <img id="imagePreview" src="" class="rounded" style="max-width:80px;max-height:80px;object-fit:cover;display:none;">
                            <div id="fileInfo" class="flex-grow-1">
                                <div id="fileName" class="small fw-medium"></div>
                                <div id="fileSize" class="small text-muted"></div>
                                <div id="uploadStatus" class="small text-muted">Preparing...</div>
                            </div>
                            <button type="button" class="btn btn-sm btn-link text-danger p-0" id="removeFileBtn">
                                <i class="bi bi-x-lg"></i>
                            </button>
                        </div>
                    </div>
                    <div class="d-flex gap-2">
                        <label for="fileInput" class="btn btn-outline-secondary px-3 mb-0">
                            <i class="bi bi-paperclip"></i>
                        </label>
                        <input type="file" id="fileInput" class="d-none" accept="image/*,.pdf,.doc,.docx,.xls,.xlsx">
                        <input type="text" id="chatInput" class="form-control" placeholder="Type a message..." maxlength="2000">
                        <button class="btn btn-primary px-4" id="chatSendBtn">Send</button>
                    </div>
                </div>
                <div id="typingIndicator" class="px-3 pb-2 text-muted" style="font-size:.8rem;min-height:20px;"></div>
            </div>
        </div>
        @else
        <div class="card border-0 shadow-sm mt-3" id="orderChat">
            <div class="card-body text-center text-muted py-3 small">
                <i class="bi bi-chat-dots me-1"></i>Chat is closed for this order.
            </div>
        </div>
        @endif
        @endcan
    </div>
</div>

@can('order.chat')
@push('scripts')
<script src="{{ config('chat.node_url') }}/socket.io/socket.io.js"></script>
<script>
(function () {
    const ORDER_ID = {{ $order->id }};
    const msgBox   = document.getElementById('chatMessages');
    const input    = document.getElementById('chatInput');
    const sendBtn  = document.getElementById('chatSendBtn');
    const typer    = document.getElementById('typingIndicator');
    if (!msgBox) return;

    // Get CSRF token
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    // Check if socket.io is available
    if (typeof io === 'undefined') {
        console.error('[chat] socket.io library not loaded');
        if(msgBox) {
            msgBox.innerHTML = '<div class="text-center text-danger small py-4"><i class="bi bi-exclamation-circle d-block mb-2 fs-5"></i>Chat service unavailable<div class="small text-muted mt-2">Make sure the chat service is running on port 3001</div></div>';
        }
        return;
    }

    let socket = null;
    let currentFile = null;

    // Helper functions
    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }

    function isImage(url) {
        return /\.(jpg|jpeg|png|gif|webp|svg)$/i.test(url);
    }

    function escapeHtml(value) {
        return String(value)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function scrollToBottom() {
        msgBox.scrollTop = msgBox.scrollHeight;
    }

    function renderMessage(msg) {
        const isMe = msg.sender_role === 'staff';
        let content = '';

        if (msg.message) {
            content += `<div class="mb-1">${escapeHtml(msg.message)}</div>`;
        }

        if (msg.attachment) {
            const fileNameText = escapeHtml(msg.attachment_name || 'Attachment');
            const fileSizeText = msg.attachment_size ? formatFileSize(Number(msg.attachment_size)) : '';

            if (msg.attachment_type === 'image' || (!msg.attachment_type && isImage(msg.attachment))) {
                content += `<a href="${msg.attachment}" target="_blank" class="d-inline-block text-decoration-none">
                    <img src="${msg.attachment}" class="img-fluid rounded" alt="${fileNameText}" style="max-width:250px;max-height:250px;object-fit:cover;border:1px solid rgba(255,255,255,0.2);">
                    <div class="small ${isMe ? 'text-white-50' : 'text-muted'} mt-1">${fileNameText}${fileSizeText ? ` · ${fileSizeText}` : ''}</div>
                </a>`;
            } else {
                content += `<a href="${msg.attachment}" target="_blank" class="d-inline-flex align-items-center gap-2 px-3 py-2 rounded-2 text-decoration-none" style="background:${isMe ? 'rgba(255,255,255,0.15)' : '#f1f5f9'};color:${isMe ? '#fff' : '#0f172a'}">
                    <i class="bi bi-file-earmark fs-5"></i>
                    <span><span class="d-block">Open File</span><span class="small ${isMe ? 'text-white-50' : 'text-muted'}">${fileNameText}${fileSizeText ? ` · ${fileSizeText}` : ''}</span></span>
                </a>`;
            }
        }

        if (!msg.message && msg.attachment) {
            content = content.replace('class="mb-1"', '');
        }

        const messageTime = msg.created_at || msg.time;
        const timeLabel = messageTime
            ? new Date(messageTime).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})
            : new Date().toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});

        msgBox.insertAdjacentHTML('beforeend', `
            <div class="d-flex ${isMe ? 'justify-content-end' : 'justify-content-start'} mb-2">
                <div class="px-3 py-2 rounded-3" style="max-width:80%;background:${isMe ? '#0d6efd' : '#fff'};color:${isMe ? '#fff' : '#1e293b'};border:1px solid #e2e8f0">
                    ${!isMe ? `<div class="fw-semibold mb-1" style="font-size:.75rem;color:#6c757d">${msg.sender_name || msg.user?.name || 'Customer'}</div>` : ''}
                    ${content}
                    <div class="small ${isMe ? 'text-white-50' : 'text-muted'} mt-1" style="font-size:.65rem">${timeLabel}</div>
                </div>
            </div>`);
    }

    // Send message function
    function send() {
        const msg = input.value.trim();
        if (!msg && !currentFile) return;

        const messageData = { message: msg };
        if (currentFile) {
            messageData.attachment = currentFile;
        }

        if (socket && socket.connected) {
            socket.emit('send_message', messageData);
            input.value = '';

            // Reset file input
            if (currentFile) {
                const filePreview = document.getElementById('filePreview');
                const fileInput = document.getElementById('fileInput');
                const imagePreview = document.getElementById('imagePreview');
                if(filePreview) filePreview.classList.add('d-none');
                if(fileInput) fileInput.value = '';
                if(imagePreview) {
                    imagePreview.src = '';
                    imagePreview.style.display = 'none';
                }
                currentFile = null;
            }
        } else {
            console.error('[chat] Socket not connected');
            if(msgBox) {
                msgBox.insertAdjacentHTML('beforeend', `<div class="text-center text-danger small py-2">Not connected to chat service. Retrying...</div>`);
                scrollToBottom();
            }
        }
    }

    // Setup send button and input event listeners FIRST
    if(sendBtn) sendBtn.addEventListener('click', send);
    if(input) {
        input.addEventListener('keydown', (e) => { if (e.key === 'Enter') send(); });
        input.addEventListener('input', () => { if (socket && socket.connected) socket.emit('typing'); });
    }

    // File upload handling
    const fileInput = document.getElementById('fileInput');
    const filePreview = document.getElementById('filePreview');
    const imagePreview = document.getElementById('imagePreview');
    const fileName = document.getElementById('fileName');
    const fileSize = document.getElementById('fileSize');
    const uploadStatus = document.getElementById('uploadStatus');
    const removeFileBtn = document.getElementById('removeFileBtn');

    if(fileInput) {
        fileInput.addEventListener('change', async (e) => {
            const file = e.target.files[0];
            if (!file) return;

            // Reset previous state
            currentFile = null;

            // Show file info
            if(filePreview) filePreview.classList.remove('d-none');
            if(fileName) fileName.textContent = file.name;
            if(fileSize) fileSize.textContent = formatFileSize(file.size);
            if(uploadStatus) uploadStatus.textContent = 'Preparing...';

            // Show image preview if it's an image
            if (file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = (e) => {
                    if(imagePreview) {
                        imagePreview.src = e.target.result;
                        imagePreview.style.display = 'block';
                    }
                };
                reader.readAsDataURL(file);
            } else {
                if(imagePreview) imagePreview.style.display = 'none';
            }

            // Upload file to Laravel
            const formData = new FormData();
            formData.append('file', file);

            try {
                if(sendBtn) {
                    sendBtn.disabled = true;
                    sendBtn.textContent = 'Uploading...';
                }
                if(uploadStatus) {
                    uploadStatus.textContent = 'Uploading...';
                    uploadStatus.className = 'small text-warning';
                }

                const uploadRes = await fetch(`/api/chat/orders/${ORDER_ID}/upload`, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': csrfToken },
                    body: formData
                });

                if (!uploadRes.ok) throw new Error('Upload failed');

                const uploadData = await uploadRes.json();
                currentFile = uploadData;

                if(uploadStatus) {
                    uploadStatus.textContent = 'Ready to send';
                    uploadStatus.className = 'small text-success';
                }
                if(fileName) fileName.innerHTML = `<i class="bi bi-check-circle text-success"></i> ${file.name}`;
            } catch (err) {
                console.error('Upload error:', err);
                if(uploadStatus) {
                    uploadStatus.textContent = 'Upload failed';
                    uploadStatus.className = 'small text-danger';
                }
                setTimeout(() => {
                    if(filePreview) filePreview.classList.add('d-none');
                    if(fileInput) fileInput.value = '';
                    currentFile = null;
                }, 2000);
            } finally {
                if(sendBtn) {
                    sendBtn.disabled = false;
                    sendBtn.textContent = 'Send';
                }
            }
        });
    }

    if(removeFileBtn) {
        removeFileBtn.addEventListener('click', () => {
            if(filePreview) filePreview.classList.add('d-none');
            if(fileInput) fileInput.value = '';
            currentFile = null;
            if(imagePreview) {
                imagePreview.src = '';
                imagePreview.style.display = 'none';
            }
        });
    }

    // Now initialize the chat connection
    (async function initChat() {
        try {
            // 1. Get auth token from Laravel
            console.log('[chat] Fetching auth token...');
            const tokenRes = await Promise.race([
                fetch('/api/chat/token', {
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    }
                }),
                new Promise((_, reject) => setTimeout(() => reject(new Error('Token request timeout')), 10000))
            ]);

            if (!tokenRes.ok) {
                throw new Error(`Token request failed: ${tokenRes.status} ${tokenRes.statusText}`);
            }

            const { token } = await tokenRes.json();
            console.log('[chat] Auth token received');

            // 2. Connect to Node.js
            console.log('[chat] Connecting to socket...');
            socket = io('{{ config("chat.node_url") }}', {
                auth: { token },
                query: { order_id: ORDER_ID },
                reconnection: true,
                reconnectionDelay: 1000,
                reconnectionDelayMax: 5000,
                reconnectionAttempts: 5
            });

            socket.on('connect_error', (error) => {
                console.error('[chat] Socket connection error:', error);
                if (msgBox && msgBox.innerHTML.includes('Loading')) {
                    msgBox.innerHTML = '<div class="text-center text-danger small py-4">Connection error. Retrying...</div>';
                }
            });

            socket.on('connect', () => {
                console.log('[chat] Connected to socket server');
            });

            // 3. Load message history
            console.log('[chat] Loading message history...');
            const histRes = await Promise.race([
                fetch(`/api/chat/orders/${ORDER_ID}/messages`, {
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    }
                }),
                new Promise((_, reject) => setTimeout(() => reject(new Error('Messages request timeout')), 10000))
            ]);

            if (!histRes.ok) {
                throw new Error(`Messages request failed: ${histRes.status} ${histRes.statusText}`);
            }

            const messages = await histRes.json();
            console.log('[chat] Loaded', messages.length, 'messages');
            msgBox.innerHTML = '';
            if (messages.length === 0) {
                msgBox.innerHTML = '<div class="text-center text-muted small py-5"><i class="bi bi-chat-dots d-block mb-2 fs-4"></i>No messages yet.<br><span style="font-size:.8rem">Be the first to send a message.</span></div>';
            } else {
                messages.forEach(renderMessage);
                scrollToBottom();
            }

            // 4. Receive new messages via socket
            socket.on('new_message', (msg) => {
                if (msgBox.querySelector('.text-center.text-muted')) {
                    msgBox.innerHTML = '';
                }
                renderMessage(msg);
                scrollToBottom();
            });

            socket.on('chat_closed', (d) => {
                msgBox.insertAdjacentHTML('beforeend', `<div class="text-center text-danger small py-2">${d.message}</div>`);
                if (input) input.disabled = true;
                if (sendBtn) sendBtn.disabled = true;
                scrollToBottom();
            });

            socket.on('user_typing', (d) => {
                if (d.role === 'customer') {
                    typer.textContent = 'Customer is typing...';
                    setTimeout(() => typer.textContent = '', 2000);
                }
            });

        } catch (e) {
            console.error('[chat] Initialization error:', e);
            if (msgBox) {
                const errorMsg = e.message.includes('timeout')
                    ? 'Request timed out. Please check your connection.'
                    : e.message.includes('Failed to fetch')
                    ? 'Network error. Please check if the chat service is running.'
                    : 'Unable to connect to chat. Please try again later.';
                msgBox.innerHTML = `<div class="text-center text-danger small py-4"><i class="bi bi-exclamation-triangle d-block mb-2 fs-5"></i>${errorMsg}<div class="small text-muted mt-2">${e.message}</div></div>`;
            }
        }
    })();

})();
</script>
@endpush
@endcan
@endsection
