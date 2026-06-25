const config = require('./config');

function registerHttpRoutes(app, io) {
    app.get('/health', (req, res) => {
        res.json({ status: 'ok', service: 'shopgram-chat-service' });
    });

    app.post('/internal/close-room/:orderId', (req, res) => {
        if (req.headers['x-internal-key'] !== config.internalKey) {
            return res.status(403).json({ error: 'Forbidden' });
        }

        const room = `order_${req.params.orderId}`;
        io.to(room).emit('chat_closed', {
            message: 'Order delivered or cancelled. Chat is now closed.',
        });

        console.log(`[chat] Room ${room} closed`);
        return res.json({ closed: true });
    });
}

module.exports = {
    registerHttpRoutes,
};
