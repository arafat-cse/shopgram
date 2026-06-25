function parseSocketToken(rawToken) {
    if (!rawToken) {
        throw new Error('No token');
    }

    const payload = JSON.parse(Buffer.from(rawToken, 'base64').toString());

    if (!payload.user_id || !payload.name || !payload.role) {
        throw new Error('Invalid token payload');
    }

    return {
        id: payload.user_id,
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
