@extends('layouts.customer')
@section('title', 'Order '.$order->order_number)
@section('customer_content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Order #{{ $order->order_number }}</h4>
    <div class="d-flex align-items-center gap-2">
        <x-order-status-badge :status="$order->status" />
        <a href="{{ route('customer.orders.invoice.pdf', $order) }}" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-download"></i> Invoice PDF
        </a>
        <form action="{{ route('customer.orders.reorder', $order) }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-sm btn-outline-success">
                <i class="bi bi-arrow-repeat"></i> Reorder
            </button>
        </form>
    </div>
</div>

<div class="row g-4">
    <div class="col-md-8">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white fw-bold">Order Items</div>
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead class="table-light">
                        <tr><th>Product</th><th>Variant</th><th>Qty</th><th>Price</th><th>Total</th></tr>
                    </thead>
                    <tbody>
                        @foreach($order->items as $item)
                        <tr>
                            <td>{{ $item->product_name }}</td>
                            <td class="text-muted small">{{ $item->variant_info ? implode(', ', array_filter($item->variant_info)) : '-' }}</td>
                            <td>{{ $item->quantity }}</td>
                            <td>৳{{ number_format($item->unit_price, 0) }}</td>
                            <td>৳{{ number_format($item->total_price, 0) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white fw-bold">Order Timeline</div>
            <div class="card-body">
                @foreach($order->statusHistories as $history)
                <div class="d-flex gap-3 mb-3">
                    <div class="text-muted small" style="min-width:130px">{{ $history->created_at->format('d M Y H:i') }}</div>
                    <div>
                        <x-order-status-badge :status="$history->status" />
                        @if($history->note)<div class="text-muted small mt-1">{{ $history->note }}</div>@endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-white fw-bold">Order Summary</div>
            <div class="card-body">
                <div class="d-flex justify-content-between small mb-1"><span>Subtotal</span><span>৳{{ number_format($order->subtotal, 0) }}</span></div>
                @if($order->discount_amount > 0)<div class="d-flex justify-content-between small mb-1 text-success"><span>Discount</span><span>-৳{{ number_format($order->discount_amount, 0) }}</span></div>@endif
                <div class="d-flex justify-content-between small mb-1"><span>Shipping</span><span>৳{{ number_format($order->shipping_charge, 0) }}</span></div>
                <hr class="my-2">
                <div class="d-flex justify-content-between fw-bold"><span>Total</span><span class="text-danger">৳{{ number_format($order->total, 0) }}</span></div>
                <div class="mt-2 small"><span class="text-muted">Payment:</span> <strong>{{ strtoupper($order->payment_method) }}</strong> — <x-order-status-badge :status="$order->payment_status" /></div>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white fw-bold">Shipping Address</div>
            <div class="card-body small">
                @php $addr = $order->shipping_address @endphp
                <p class="mb-0">{{ $addr['name'] ?? '' }}<br>{{ $addr['phone'] ?? '' }}<br>{{ $addr['address_line'] ?? '' }}<br>{{ $addr['city'] ?? '' }}, {{ $addr['district'] ?? '' }}</p>
            </div>
        </div>

        @if($order->courier && $order->courier_tracking_number)
        <div class="card border-0 shadow-sm mt-3">
            <div class="card-body small">
                <strong>Courier:</strong> {{ $order->courier->name }}<br>
                <strong>Tracking:</strong> {{ $order->courier_tracking_number }}
                @if($link = $order->courier->getTrackingLink($order->courier_tracking_number))
                <a href="{{ $link }}" target="_blank" class="btn btn-sm btn-outline-primary d-block mt-2">Track Package</a>
                @endif
            </div>
        </div>
        @endif

        @if($order->isChatOpen())
        <div class="card border-0 shadow-sm mt-3">
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
        <div class="card border-0 shadow-sm mt-3">
            <div class="card-body text-center text-muted py-3 small">
                <i class="bi bi-chat-dots me-1"></i>Chat is closed for this order.
            </div>
        </div>
        @endif
    </div>
</div>

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

    // Get CSRF token
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    try {
        // 1. Get auth token from Laravel
        const tokenRes = await fetch('/api/chat/token', {
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            }
        });
        const { token } = await tokenRes.json();

        // 2. Connect to Node.js
        const socket = io('{{ config("chat.node_url") }}', {
            auth: { token },
            query: { order_id: ORDER_ID },
        });

        // 3. Load history
        const histRes  = await fetch(`/api/chat/orders/${ORDER_ID}/messages`, {
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            }
        });
        const messages = await histRes.json();
        msgBox.innerHTML = '';
        messages.forEach(renderMessage);
        scrollToBottom();

        // 4. Receive new messages
        socket.on('new_message', (msg) => {
            renderMessage(msg);
            scrollToBottom();
        });

        socket.on('chat_closed', (d) => {
            msgBox.insertAdjacentHTML('beforeend', `<div class="text-center text-danger small py-2">${d.message}</div>`);
            if(input) input.disabled = true;
            if(sendBtn) sendBtn.disabled = true;
            scrollToBottom();
        });

        socket.on('user_typing', (d) => {
            if (d.role === 'staff') {
                typer.textContent = 'Support is typing...';
                setTimeout(() => typer.textContent = '', 2000);
            }
        });

        // 5. Send message
        function send() {
            const msg = input.value.trim();
            if (!msg) return;
            socket.emit('send_message', { message: msg });
            input.value = '';
        }

        if(sendBtn) sendBtn.addEventListener('click', send);
        if(input) {
            input.addEventListener('keydown', (e) => { if (e.key === 'Enter') send(); });
            input.addEventListener('input', () => socket.emit('typing'));
        }

        function scrollToBottom() {
            msgBox.scrollTop = msgBox.scrollHeight;
        }

        function renderMessage(msg) {
            const isMe = msg.sender_role === 'customer';
            msgBox.insertAdjacentHTML('beforeend', `
                <div class="d-flex ${isMe ? 'justify-content-end' : 'justify-content-start'} mb-2">
                    <div class="px-3 py-2 rounded-3 small" style="max-width:75%;background:${isMe ? '#0d6efd' : '#fff'};color:${isMe ? '#fff' : '#1e293b'};border:1px solid #e2e8f0">
                        ${!isMe ? `<div class="fw-semibold" style="font-size:.75rem;color:#6c757d">${msg.sender_name || msg.user?.name || 'Support'}</div>` : ''}
                        ${msg.message}
                    </div>
                </div>`);
        }
    } catch (e) {
        console.error('Chat error:', e);
        if(msgBox) msgBox.innerHTML = '<div class="text-center text-danger small py-4">Unable to connect to chat. Please try again later.</div>';
    }
})();
</script>
@endpush
@endsection
