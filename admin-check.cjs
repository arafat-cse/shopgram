const { chromium } = require('playwright-core');
const exe = 'C:/Users/Arafat/AppData/Local/ms-playwright/chromium-1223/chrome-win64/chrome.exe';
(async () => {
  const browser = await chromium.launch({ executablePath: exe, headless: true });
  const page = await browser.newPage();
  const errors = [];
  page.on('pageerror', e => errors.push('pageerror: ' + e.message));
  page.on('console', m => { if (m.type() === 'error') errors.push('console: ' + m.text()); });

  await page.goto('http://127.0.0.1:8000/login', { waitUntil: 'networkidle' });
  await page.fill('input[name="email"]', 'admin@tech.mysatra.com');
  await page.fill('input[name="password"]', 'tech@1234');
  await page.click('button[type="submit"]');
  await page.waitForLoadState('networkidle');
  console.log('after login url:', page.url());

  await page.goto('http://127.0.0.1:8000/admin/orders', { waitUntil: 'networkidle' });
  console.log('orders page title:', await page.title());

  await page.goto('http://127.0.0.1:8000/admin/settings', { waitUntil: 'networkidle' });
  const waVal = await page.inputValue('input[name="whatsapp"]').catch(() => 'FIELD_NOT_FOUND');
  console.log('whatsapp field value:', waVal);

  console.log('JS ERRORS:', errors.length ? JSON.stringify(errors) : 'none');
  await browser.close();
})().catch(e => { console.error('FAIL:', e.message); process.exit(1); });
