require('dotenv').config();
const express    = require('express');
const http       = require('http');
const { Server } = require('socket.io');
const axios      = require('axios');

const app    = express();
const server = http.createServer(app);
const allowedOrigins = new Set([
    process.env.LARAVEL_URL,
    'http://127.0.0.1:8000',
    'http://localhost:8000',
]);
const io     = new Server(server, {
    cors: {
        origin: (origin, callback) => {
            if (!origin || allowedOrigins.has(origin)) {
                return callback(null, true);
            }

            return callback(new Error(`Origin ${origin} is not allowed by chat CORS`));
        },
        credentials: true
    }
});

app.use(express.json());

// ── Auth middleware ──────────────────────────────────────────────────
io.use(async (socket, next) => {
    try {
        const rawToken = socket.handshake.auth.token;
        if (!rawToken) return next(new Error('No token'));

        const payload = JSON.parse(Buffer.from(rawToken, 'base64').toString());

        // Simple validation - in production, verify signature with Laravel
        socket.user = {
            id:   payload.user_id,
            name: payload.name,
            role: payload.role,   // 'customer' or 'staff'
        };
        next();
    } catch (e) {
        console.error('[chat] Auth failed:', e.message);
        next(new Error('Auth failed'));
    }
});

// ── Connection ───────────────────────────────────────────────────────
io.on('connection', (socket) => {
    const orderId = socket.handshake.query.order_id;
    if (!orderId) {
        console.log('[chat] Connection rejected: no order_id');
        return socket.disconnect();
    }

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
                {
                    message: data.message,
                    user_id: socket.user.id,
                    sender_role: socket.user.role
                },
                {
                    headers: { 'X-Internal-Key': process.env.CHAT_INTERNAL_KEY }
                }
            );

            // Broadcast to room
            io.to(room).emit('new_message', {
                id:          res.data.id,
                sender_name: socket.user.name,
                sender_role: socket.user.role,
                message:     data.message,
                time:        new Date().toISOString(),
            });

            console.log(`[chat] Message sent in ${room} by ${socket.user.name}`);
        } catch (e) {
            console.error('[chat] Failed to send message:', e.response?.data || e.message);
            socket.emit('error', {
                message: e.response?.data?.error || 'Failed to send message'
            });
        }
    });

    // Typing indicator
    socket.on('typing', () => {
        socket.to(room).emit('user_typing', {
            name: socket.user.name,
            role: socket.user.role
        });
    });

    // Mark messages as read
    socket.on('mark_read', async () => {
        try {
            await axios.post(
                `${process.env.LARAVEL_URL}/api/chat/orders/${orderId}/close`,
                {},
                {
                    headers: { 'X-Internal-Key': process.env.CHAT_INTERNAL_KEY }
                }
            );
        } catch (e) {
            console.error('[chat] Failed to mark messages as read:', e.message);
        }
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
    io.to(room).emit('chat_closed', {
        message: 'Order delivered or cancelled. Chat is now closed.'
    });

    console.log(`[chat] Room ${room} closed`);
    res.json({ closed: true });
});

// ── Health check ─────────────────────────────────────────────────────
app.get('/health', (req, res) => {
    res.json({ status: 'ok', service: 'shopgram-chat-service' });
});

const PORT = process.env.PORT || 3001;
server.listen(PORT, () => {
    console.log(`[chat-service] Running on port ${PORT}`);
    console.log(`[chat-service] Laravel URL: ${process.env.LARAVEL_URL}`);
});
