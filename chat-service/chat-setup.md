# ShopGram Chat Service — Command Reference

PM2 দিয়ে manage হয়। App name: **shopgram-chat**

---

## PM2 Commands (daily use)

```bash
# Status দেখো
pm2 list

# Restart (code change করলে এটাই দাও)
pm2 restart shopgram-chat

# Stop
pm2 stop shopgram-chat

# Start (stop থাকলে)
pm2 start shopgram-chat

# Delete করো PM2 থেকে
pm2 delete shopgram-chat

# Live log দেখো
pm2 logs shopgram-chat

# Last 50 line log
pm2 logs shopgram-chat --lines 50

# Log clear করো
pm2 flush shopgram-chat

# CPU/Memory monitor
pm2 monit
```

---

## প্রথমবার Setup (fresh install)

```bash
cd /var/www/html/shopgram/chat-service

# 1. Dependencies install
npm install

# 2. .env তৈরি করো
cp .env.example .env   # নাহলে নিচে manually তৈরি করো
```

**.env file:**
```
PORT=3001
LARAVEL_URL=http://192.168.100.92:8000
CHAT_INTERNAL_KEY=8ee2a7d6ec21a433b994ba07e0ab95cc27ac5d722a1fa046
```

```bash
# 3. PM2 দিয়ে start করো
pm2 start server.js --name shopgram-chat

# 4. Server reboot হলে auto-start হবে
pm2 startup
pm2 save
```

---

## Development Mode (npm run dev)

Code লিখতে লিখতে test করতে চাইলে:

```bash
cd /var/www/html/shopgram/chat-service

npm run dev
```

এটা করে:
1. PM2 বন্ধ করে (conflict নাই)
2. `nodemon` দিয়ে চালায় — file save করলে auto-restart

**Dev শেষে production-এ ফেরো:**
```bash
npm run prod
# অথবা
pm2 restart shopgram-chat
```

> **মনে রেখো:** `npm run dev` চলার সময় PM2 বন্ধ থাকে। Dev শেষে `npm run prod` দিলে PM2 আবার চালু হবে।

---

## Production — Code Change করলে

```bash
pm2 restart shopgram-chat
```

শুধু এটুকুই। PM2 নিজে kill → restart করে।

---

## npm scripts summary

| Command | কাজ |
|---------|-----|
| `npm run dev` | PM2 বন্ধ করে nodemon দিয়ে চালায় (development) |
| `npm run prod` | PM2 restart করে (dev থেকে production-এ ফেরা) |
| `npm start` | শুধু `node server.js` (direct, PM2 ছাড়া) |

---

## Health Check

```bash
# Service চলছে কিনা
curl http://127.0.0.1:3001/health

# Response হবে:
# {"status":"ok","service":"shopgram-chat-service"}
```

---

## Port Conflict হলে (EADDRINUSE)

`npm run dev` না দিয়ে সরাসরি `node server.js` চালালে conflict হয়।

```bash
# সঠিক উপায় — npm run dev ব্যবহার করো
npm run dev

# যদি তবুও conflict হয়:
kill $(lsof -ti:3001)
pm2 restart shopgram-chat
```

**মনে রেখো:** সরাসরি `node server.js` চালাবে না। `npm run dev` দিলে PM2 auto-stop হয়।

---

## Environment Variables

| Variable            | কাজ                                      | Example                                 |
|---------------------|------------------------------------------|-----------------------------------------|
| `PORT`              | Node.js listen port                      | `3001`                                  |
| `LARAVEL_URL`       | Laravel server (browser-facing IP)       | `http://192.168.100.92:8000`            |
| `CHAT_INTERNAL_URL` | Laravel internal (server-to-server)      | `http://127.0.0.1:8000` (optional)     |
| `CHAT_INTERNAL_KEY` | HMAC key — Laravel config-এর সাথে match | `8ee2a7d6ec21a433b994ba07e0ab95cc27ac5d722a1fa046` |

> **Important:** `CHAT_INTERNAL_KEY` এবং Laravel-এর `CHAT_INTERNAL_KEY` (.env) একই হতে হবে।

---

## Laravel .env এ যা থাকবে

```env
CHAT_NODE_URL=http://192.168.100.92:3001      # Browser জন্য (LAN IP)
CHAT_NODE_INTERNAL_URL=http://127.0.0.1:3001  # Server-to-server
CHAT_INTERNAL_KEY=8ee2a7d6ec21a433b994ba07e0ab95cc27ac5d722a1fa046
```

---

## Troubleshooting

| সমস্যা | সমাধান |
|--------|--------|
| `EADDRINUSE` | `pm2 restart shopgram-chat` |
| Chat connect হচ্ছে না | `pm2 logs shopgram-chat` দেখো |
| Mobile থেকে connect হচ্ছে না | `LARAVEL_URL` LAN IP আছে কিনা চেক করো |
| Token invalid error | Laravel ও Node-এর `CHAT_INTERNAL_KEY` same কিনা চেক |
| `pm2: command not found` | `npm install -g pm2` |
