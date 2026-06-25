require('dotenv').config();

const laravelUrl = process.env.LARAVEL_URL || 'http://127.0.0.1:8000';

module.exports = {
    port: process.env.PORT || 3001,
    laravelUrl,
    internalKey: process.env.CHAT_INTERNAL_KEY || 'changeme',
    allowedOrigins: [
        laravelUrl,
        'http://127.0.0.1:8000',
        'http://localhost:8000',
    ],
};
