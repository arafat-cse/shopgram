const crypto = require('crypto');
const config = require('./config');

function parseSocketToken(rawToken) {
    if (!rawToken) {
        throw new Error('No token');
    }

    const payload = JSON.parse(Buffer.from(rawToken, 'base64').toString());

    if (!payload.user_id || !payload.name || !payload.role || !payload.signature) {
        throw new Error('Invalid token payload');
    }

    // Verify HMAC — must match Laravel: hash_hmac('sha256', (string)$user->id, config('chat.internal_key'))
    const expected = crypto
        .createHmac('sha256', config.internalKey)
        .update(String(payload.user_id))
        .digest('hex');

    if (payload.signature !== expected) {
        throw new Error('Invalid token signature');
    }

    return {
        id:   payload.user_id,
        name: payload.name,
        role: payload.role,
    };
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

module.exports = {
    registerSocketAuth,
};
