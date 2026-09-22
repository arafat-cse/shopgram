require('dotenv').config();

const laravelUrl = process.env.LARAVEL_URL || 'http://127.0.0.1:8000';

// LAN IPs change with DHCP — accept any private-network origin instead of
// hardcoding a single machine IP.
const privateOrigin = /^(https?):\/\/(localhost|127\.0\.0\.1|10\.\d+\.\d+\.\d+|192\.168\.\d+\.\d+|172\.(1[6-9]|2\d|3[01])\.\d+\.\d+)(:\d+)?$/;

module.exports = {
    port: process.env.PORT || 3001,
    laravelUrl,
    internalKey: process.env.CHAT_INTERNAL_KEY || 'changeme',
    allowedOrigins: [laravelUrl, 'http://127.0.0.1:8000', 'http://localhost:8000'],
    isAllowedOrigin(origin) {
        return this.allowedOrigins.includes(origin) || privateOrigin.test(origin);
    },
};
