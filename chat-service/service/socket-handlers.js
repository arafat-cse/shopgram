const laravel = require('./laravel-client');

function registerSocketHandlers(io) {
    io.on('connection', (socket) => {
        const { order_id, livechat_id } = socket.handshake.query;

        if (livechat_id) {
            handleLiveChat(io, socket, livechat_id);
        } else if (order_id) {
            handleOrderChat(io, socket, order_id);
        } else if (socket.user.type === 'staff_livechat') {
            // Admin panel — join notification room only
            socket.join('livechat_admin');
            console.log(`[livechat] ${socket.user.name} joined admin room`);
            socket.on('disconnect', () => console.log(`[livechat] ${socket.user.name} left admin room`));
        } else {
            console.log('[chat] Connection rejected: no room identifier');
            socket.disconnect();
        }
    });
}

// ─── Live Chat ────────────────────────────────────────────────────────────────

function handleLiveChat(io, socket, chatId) {
    const room      = `livechat_${chatId}`;
    const isStaff   = socket.user.type === 'staff_livechat';
    const senderType = isStaff ? 'staff' : 'guest';
    const senderName = socket.user.name;

    socket.join(room);
    console.log(`[livechat] ${senderName} (${senderType}) joined ${room}`);

    // Notify admin room when guest connects
    if (!isStaff) {
        io.to('livechat_admin').emit('guest_joined', { chat_id: chatId, name: senderName });
    }

    socket.on('send_message', async (data) => {
        const message    = data.message?.trim() || '';
        const attachment = data.attachment || null;
        if (!message && !attachment) return;

        try {
            const saved = await laravel.saveLiveChatMessage(chatId, {
                sender_type:     senderType,
                sender_name:     senderName,
                user_id:         isStaff ? socket.user.id : null,
                message:         message || null,
                attachment:      attachment?.url || null,
                attachment_type: attachment?.type || null,
                attachment_name: attachment?.name || null,
                attachment_size: attachment?.size || null,
            });

            const out = {
                id:              saved.id,
                sender_type:     senderType,
                sender_name:     senderName,
                message:         saved.message || message || null,
                attachment:      saved.attachment || attachment?.url || null,
                attachment_type: saved.attachment_type || attachment?.type || null,
                attachment_name: saved.attachment_name || attachment?.name || null,
                attachment_size: saved.attachment_size || attachment?.size || null,
                created_at:      saved.created_at || new Date().toISOString(),
            };

            io.to(room).emit('new_message', out);

            // Notify admin panel of new guest message
            if (!isStaff) {
                io.to('livechat_admin').emit('new_livechat_message', { chat_id: chatId, message: out });
            }
        } catch (err) {
            console.error('[livechat] Failed to save message:', err.response?.data || err.message);
            socket.emit('error', { message: 'Failed to send message' });
        }
    });

    socket.on('typing', () => {
        socket.to(room).emit('user_typing', { name: senderName, type: senderType });
        if (!isStaff) {
            io.to('livechat_admin').emit('guest_typing', { chat_id: chatId, name: senderName });
        }
    });

    socket.on('disconnect', () => console.log(`[livechat] ${senderName} left ${room}`));
}

// ─── Order Chat (unchanged logic) ────────────────────────────────────────────

function handleOrderChat(io, socket, orderId) {
    const room = `order_${orderId}`;
    socket.join(room);
    console.log(`[chat] ${socket.user.name} (${socket.user.role}) joined ${room}`);

    socket.on('send_message', async (data) => {
        const message    = data.message?.trim() || '';
        const attachment = data.attachment || null;
        if (!message && !attachment) return;

        try {
            const savedMessage = await laravel.saveOrderMessage(orderId, message, socket.user, attachment);
            io.to(room).emit('new_message', {
                id:              savedMessage.id,
                sender_name:     socket.user.name,
                sender_role:     socket.user.role,
                message:         savedMessage.message || message,
                attachment:      savedMessage.attachment || attachment?.url || null,
                attachment_type: savedMessage.attachment_type || attachment?.type || null,
                attachment_name: savedMessage.attachment_name || attachment?.name || null,
                attachment_size: savedMessage.attachment_size || attachment?.size || null,
                created_at:      savedMessage.created_at || new Date().toISOString(),
            });
        } catch (error) {
            console.error('[chat] Failed to send message:', error.response?.data || error.message);
            socket.emit('error', { message: error.response?.data?.error || 'Failed to send message' });
        }
    });

    socket.on('typing', () => {
        socket.to(room).emit('user_typing', { name: socket.user.name, role: socket.user.role });
    });

    socket.on('mark_read', async () => {
        try { await laravel.closeOrderChat(orderId); } catch (e) {
            console.error('[chat] Failed to mark read:', e.message);
        }
    });

    socket.on('disconnect', () => console.log(`[chat] ${socket.user.name} left ${room}`));
}

module.exports = { registerSocketHandlers };
