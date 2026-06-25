const express = require('express');
const http = require('http');
const { Server } = require('socket.io');
const config = require('./config');
const { registerHttpRoutes } = require('./http-routes');
const { registerSocketAuth } = require('./socket-auth');
const { registerSocketHandlers } = require('./socket-handlers');

function createService() {
    const app = express();
    const server = http.createServer(app);
    const allowedOrigins = new Set(config.allowedOrigins);

    const io = new Server(server, {
        cors: {
            origin: (origin, callback) => {
                if (!origin || allowedOrigins.has(origin)) {
                    return callback(null, true);
                }

                return callback(new Error(`Origin ${origin} is not allowed by chat CORS`));
            },
            credentials: true,
        },
    });

    app.use(express.json());

    registerSocketAuth(io);
    registerSocketHandlers(io);
    registerHttpRoutes(app, io);

    return { app, server, io };
}

module.exports = {
    createService,
};
