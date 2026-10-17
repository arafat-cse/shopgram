# Tech — cPanel Deployment Guide

ডোমেইন ধরা হয়েছে: `https://tech.mysatra.com` (এটা `tech` সাবডোমেইন)। অন্য ডোমেইন হলে সব জায়গায় বদলে নাও।

---

## ০. আগে থেকে cPanel-এ যা লাগবে

- **Subdomain**: `tech.mysatra.com` (cPanel → Domains → Create A New Domain/Subdomain)
- **PHP 8.2+** (cPanel → MultiPHP Manager-এ এই subdomain-এর জন্য select করো)
- **MySQL Database** (cPanel → MySQL Databases)
- **Terminal** থাকলে ভালো (cPanel → Advanced → Terminal) — না থাকলে নিচে ছাড়াই করার উপায় দেওয়া আছে

---

## ১. Document Root ঠিক করা (সবচেয়ে গুরুত্বপূর্ণ ধাপ)

Laravel-এর entry point `public/` ফোল্ডারে, কিন্তু cPanel ডিফল্টভাবে subdomain-এর root পুরো ফোল্ডারটাই ধরে। তাই:

**cPanel → Domains → `tech.mysatra.com` এর পাশে "Manage" → Document Root বদলে দাও:**
```
/home/<cpanel_user>/shopgram/public
```

অর্থাৎ পুরো প্রজেক্ট আপলোড হবে `/home/<cpanel_user>/shopgram/` এ, কিন্তু ব্রাউজারে শুধু ওর ভেতরের `public/` ফোল্ডারটাই serve হবে। এটা না করলে `.env`, `app/`, `vendor/` সব সরাসরি ব্রাউজার থেকে অ্যাক্সেসযোগ্য হয়ে যাবে — বড় security হোল।

---

## ২. Zip আপলোড

cPanel → **File Manager** → `/home/<cpanel_user>/` এ গিয়ে:

1. Upload করো তোমার zip (যেমন `shopgram.zip`)
2. Select করে **Extract** এ ক্লিক করো → `shopgram` ফোল্ডার তৈরি হবে
3. zip ফাইলটা ডিলিট করে দাও (ডিস্ক স্পেস বাঁচাতে)

> **Tip:** zip করার আগে `vendor/`, `node_modules/`, `.git/` বাদ দিয়ে জিপ করলে আপলোড দ্রুত হবে — এগুলো নিচের ধাপে সার্ভারেই বানানো হবে। Terminal না থাকলে অবশ্য `vendor/` আগেই লোকাল থেকে বানিয়ে zip-এর সাথে রাখতে হবে (ধাপ ৪-এ বিস্তারিত)।

---

## ৩. Database তৈরি

cPanel → **MySQL Databases**:

1. নতুন database বানাও, যেমন `cpaneluser_tech`
2. নতুন database user বানাও, পাসওয়ার্ড দাও
3. User-কে database-এ **All Privileges** দিয়ে add করো

এই নাম তিনটা (`DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`) পরে `.env`-এ বসবে।

---

## ৪. Laravel সেটআপ

### Terminal থাকলে (সহজ পথ)

cPanel → **Terminal** খুলে:

```bash
cd ~/shopgram

composer install --no-dev --optimize-autoloader

cp .env.production .env
nano .env   # অথবা File Manager-এর Code Editor দিয়ে এডিট করো
```

`.env`-এ বদলাও:
```env
DB_DATABASE=cpaneluser_tech
DB_USERNAME=cpaneluser_dbuser
DB_PASSWORD=তোমার_db_password
```
(`APP_URL`, `GOOGLE_REDIRECT_URI` আগেই `tech.mysatra.com` দিয়ে সেট করা আছে)

```bash
php artisan key:generate --force
php artisan migrate --force
php artisan storage:link
chmod -R 775 storage bootstrap/cache

php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Terminal না থাকলে

- লোকাল মেশিনে `composer install --no-dev --optimize-autoloader` চালিয়ে `vendor/` ফোল্ডারসহ zip করে আপলোড করো
- `.env` লোকালি বানিয়ে zip-এ রাখো (এতে `php artisan key:generate` লোকালেই চালাতে হবে)
- `php artisan migrate`, `storage:link`, cache কমান্ডগুলো **cPanel → Cron Jobs**-এ একবারের জন্য একটা job বানিয়ে (যেমন `* * * * * php /home/user/shopgram/artisan migrate --force >> /home/user/migrate.log 2>&1`) এক মিনিট পর চালিয়ে সাথে সাথে cron job-টা ডিলিট করে দাও — শুধু একবার চালানোর ট্রিকস, স্থায়ী না

### Frontend assets

```bash
npm install
npm run build
```
(Terminal না থাকলে লোকালে বিল্ড করে `public/build/` ফোল্ডারটা zip-এ রেখে দাও)

---

## ৫. Cron Jobs (Laravel scheduler + queue worker)

cPanel-এ দীর্ঘ সময় চলা প্রসেস (`php artisan queue:work`) রাখা যায় না — shared hosting প্রসেস কিছুক্ষণ পর নিজেই kill করে দেয়। তাই cron দিয়ে বারবার ছোট ছোট ব্যাচে চালাতে হয়।

cPanel → **Cron Jobs** → নিচের দুইটা যোগ করো (Every Minute):

```bash
* * * * * cd /home/<cpanel_user>/shopgram && php artisan schedule:run >> /dev/null 2>&1

* * * * * cd /home/<cpanel_user>/shopgram && php artisan queue:work --stop-when-empty --tries=3 --max-time=50 >> /dev/null 2>&1
```

`--stop-when-empty --max-time=50` থাকায় প্রতি মিনিটে নতুন করে চালু হয়ে পুরনোটা নিজে থেকে বন্ধ হয়ে যায় — hosting-এর process-limit এ সমস্যা হয় না।

---

## ৬. Deploy-পরবর্তী চেকলিস্ট

- [ ] `https://tech.mysatra.com/` হোমপেজ লোড হচ্ছে
- [ ] ভুল URL দিয়ে 404 ট্রিগার করে দেখো — stack trace/`APP_DEBUG` leak দেখা যাবে না (`APP_DEBUG=false`)
- [ ] Admin লগইন (`admin@tech.mysatra.com` / `tech@1234`) — লগইনের পরপরই পাসওয়ার্ড বদলাও
- [ ] হোমপেজের WhatsApp বাটনে ক্লিক করলে সঠিক নম্বর দিয়ে WhatsApp চ্যাট খুলছে কিনা (অ্যাডমিন সেটিংসে নম্বর বসানো থাকতে হবে)
- [ ] Google login কাজ করছে (Google Cloud Console-এ `https://tech.mysatra.com/auth/google/callback` redirect URI যোগ করা আছে কিনা)
- [ ] SSL সক্রিয় (cPanel → SSL/TLS Status → AutoSSL চালু করো, `SESSION_SECURE_COOKIE=true` থাকায় HTTPS ছাড়া লগইন কাজ করবে না)
- [ ] cPanel → Cron Jobs-এ দুটো job-ই আছে এবং চলছে

---

## পরে কোড আপডেট করলে

```bash
cd ~/shopgram
composer install --no-dev --optimize-autoloader
php artisan migrate --force
npm run build
php artisan config:cache route:cache view:cache
```
