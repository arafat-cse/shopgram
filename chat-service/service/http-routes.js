const config = require('./config');

function registerHttpRoutes(app, io) {
    app.get('/health', (req, res) => {
        res.json({ status: 'ok', service: 'shopgram-chat-service' });
    });

    // ─── Order chat ────────────────────────────────────────────────────────────
    app.post('/internal/close-room/:orderId', (req, res) => {
        if (req.headers['x-internal-key'] !== config.internalKey) return res.status(403).json({ error: 'Forbidden' });
        const room = `order_${req.params.orderId}`;
        io.to(room).emit('chat_closed', { message: 'Order delivered or cancelled. Chat is now closed.' });
        console.log(`[chat] Room ${room} closed`);
        return res.json({ closed: true });
    });

    // ─── Live chat ─────────────────────────────────────────────────────────────

    // Close a live chat room
    app.post('/internal/livechat/close/:chatId', (req, res) => {
        if (req.headers['x-internal-key'] !== config.internalKey) return res.status(403).json({ error: 'Forbidden' });
        const room = `livechat_${req.params.chatId}`;
        io.to(room).emit('livechat_closed', { message: 'This chat has been closed by support.' });
        io.to('livechat_admin').emit('livechat_status_changed', { chat_id: req.params.chatId, status: 'closed' });
        console.log(`[livechat] Room ${room} closed`);
        return res.json({ closed: true });
    });

    // Reopen a live chat room
    app.post('/internal/livechat/reopen/:chatId', (req, res) => {
        if (req.headers['x-internal-key'] !== config.internalKey) return res.status(403).json({ error: 'Forbidden' });
        const room = `livechat_${req.params.chatId}`;
        io.to(room).emit('livechat_reopened', { message: 'Support has reopened this chat.' });
        io.to('livechat_admin').emit('livechat_status_changed', { chat_id: req.params.chatId, status: 'active' });
        console.log(`[livechat] Room ${room} reopened`);
        return res.json({ reopened: true });
    });

    // Emit a message from admin to guest socket room
    app.post('/internal/livechat/emit/:chatId', (req, res) => {
        if (req.headers['x-internal-key'] !== config.internalKey) return res.status(403).json({ error: 'Forbidden' });
        const room = `livechat_${req.params.chatId}`;
        if (req.body.message) io.to(room).emit('new_message', req.body.message);
        return res.json({ emitted: true });
    });

    // Notify admin panel of a new chat
    app.post('/internal/livechat/notify-admin', (req, res) => {
        if (req.headers['x-internal-key'] !== config.internalKey) return res.status(403).json({ error: 'Forbidden' });
        io.to('livechat_admin').emit('new_livechat_chat', req.body);
        return res.json({ notified: true });
    });
}

module.exports = { registerHttpRoutes };
