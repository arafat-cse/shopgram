const axios = require('axios');
const config = require('./config');

const client = axios.create({
    baseURL: config.laravelUrl,
    headers: {
        Accept: 'application/json',
        'X-Internal-Key': config.internalKey,
    },
});

async function saveOrderMessage(orderId, message, user) {
    const response = await client.post(`/api/chat/orders/${orderId}/messages`, {
        message,
        user_id: user.id,
        sender_role: user.role,
    });

    return response.data;
}

async function closeOrderChat(orderId) {
    const response = await client.post(`/api/chat/orders/${orderId}/close`, {});
    return response.data;
}

module.exports = {
    saveOrderMessage,
    closeOrderChat,
};
