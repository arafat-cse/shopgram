const axios = require('axios');
const config = require('./config');

const client = axios.create({
    baseURL: config.laravelUrl,
    headers: {
        Accept: 'application/json',
        'X-Internal-Key': config.internalKey,
    },
});

async function saveOrderMessage(orderId, message, user, attachment = null) {
    const payload = { user_id: user.id, sender_role: user.role };
    if (message && message.trim()) payload.message = message.trim();
    if (attachment) {
        payload.attachment      = attachment.url;
        payload.attachment_type = attachment.type;
        payload.attachment_name = attachment.name;
        payload.attachment_size = attachment.size;
    }
    const response = await client.post(`/api/chat/orders/${orderId}/messages`, payload);
    return response.data;
}

async function saveLiveChatMessage(chatId, data) {
    const response = await client.post(`/api/livechat/${chatId}/messages`, data);
    return response.data;
}

async function closeOrderChat(orderId) {
    const response = await client.post(`/api/chat/orders/${orderId}/close`, {});
    return response.data;
}

module.exports = { saveOrderMessage, saveLiveChatMessage, closeOrderChat };
