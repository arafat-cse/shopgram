const laravel = require('./laravel-client');

function registerSocketHandlers(io) {
    io.on('connection', (socket) => {
        const orderId = socket.handshake.query.order_id;

        if (!orderId) {
            console.log('[chat] Connection rejected: no order_id');
            socket.disconnect();
            return;
        }

        const room = `order_${orderId}`;
        socket.join(room);

        console.log(`[chat] ${socket.user.name} (${socket.user.role}) joined ${room}`);

        socket.on('send_message', async (data) => {
            const message = data.message?.trim();
            if (!message) return;

            try {
                const savedMessage = await laravel.saveOrderMessage(orderId, message, socket.user);

                io.to(room).emit('new_message', {
                    id: savedMessage.id,
                    sender_name: socket.user.name,
                    sender_role: socket.user.role,
                    message,
                    time: new Date().toISOString(),
                });

                console.log(`[chat] Message sent in ${room} by ${socket.user.name}`);
            } catch (error) {
                console.error('[chat] Failed to send message:', error.response?.data || error.message);
                socket.emit('error', {
                    message: error.response?.data?.error || 'Failed to send message',
                });
            }
        });

        socket.on('typing', () => {
            socket.to(room).emit('user_typing', {
                name: socket.user.name,
                role: socket.user.role,
            });
        });

        socket.on('mark_read', async () => {
            try {
                await laravel.closeOrderChat(orderId);
            } catch (error) {
                console.error('[chat] Failed to mark messages as read:', error.message);
            }
        });

        socket.on('disconnect', () => {
            console.log(`[chat] ${socket.user.name} left ${room}`);
        });
    });
}

module.exports = {
    registerSocketHandlers,
};
