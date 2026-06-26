const config = require('./service/config');
const { createService } = require('./service/app');

const { server } = createService();

server.listen(config.port, () => {
    console.log(`[chat-service] Running on port ${config.port}`);
    console.log(`[chat-service] Laravel URL: ${config.laravelUrl}`);
});
