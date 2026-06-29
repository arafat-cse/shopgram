const crypto = require('crypto');
const config = require('./config');

function parseSocketToken(rawToken) {
    if (!rawToken) throw new Error('No token');

    const payload = JSON.parse(Buffer.from(rawToken, 'base64').toString());
    const type = payload.type || 'order';

    if (type === 'guest') {
        if (!payload.chat_id || !payload.session_id || !payload.name || !payload.signature) {
            throw new Error('Invalid guest token');
        }
        const expected = crypto.createHmac('sha256', config.internalKey)
            .update(`${payload.chat_id}:${payload.session_id}`)
            .digest('hex');
        if (payload.signature !== expected) throw new Error('Invalid guest token signature');
        return { type: 'guest', chat_id: payload.chat_id, session_id: payload.session_id, name: payload.name };
    }

    if (type === 'staff_livechat') {
        if (!payload.user_id || !payload.name || !payload.signature) throw new Error('Invalid staff_livechat token');
        const expected = crypto.createHmac('sha256', config.internalKey)
            .update(String(payload.user_id))
            .digest('hex');
        if (payload.signature !== expected) throw new Error('Invalid staff_livechat token signature');
        return { type: 'staff_livechat', id: payload.user_id, name: payload.name };
    }

    // Existing order chat token (role: 'customer' | 'staff')
    if (!payload.user_id || !payload.name || !payload.role || !payload.signature) {
        throw new Error('Invalid token payload');
    }
    const expected = crypto.createHmac('sha256', config.internalKey)
        .update(String(payload.user_id))
        .digest('hex');
    if (payload.signature !== expected) throw new Error('Invalid token signature');
    return { type: 'order', id: payload.user_id, name: payload.name, role: payload.role };
}

function registerSocketAuth(io) {
    io.use((socket, next) => {
        try {
            socket.user = parseSocketToken(socket.handshake.auth.token);
            next();
        } catch (error) {
            console.error('[chat] Auth failed:', error.message);
            next(new Error('Auth failed'));
        }
    });
}

module.exports = { registerSocketAuth };
