const { chromium } = require('playwright-core');
const exe = 'C:/Users/Arafat/AppData/Local/ms-playwright/chromium-1223/chrome-win64/chrome.exe';
(async () => {
  const browser = await chromium.launch({ executablePath: exe, headless: true });
  const page = await browser.newPage();
  const logs = [];
  page.on('console', m => logs.push(m.type() + ': ' + m.text()));
  page.on('requestfailed', r => { if (r.url().includes('3001')) logs.push('REQFAILED: ' + r.url() + ' ' + r.failure()?.errorText); });

  await page.goto('http://127.0.0.1:8000/', { waitUntil: 'networkidle', timeout: 40000 });

  await page.click('#lc-btn');
  await page.waitForTimeout(400);
  await page.fill('#lc-name', 'Test Guest');
  await page.fill('#lc-phone', '01712345678');
  await page.click('.lc-start-btn');
  await page.waitForTimeout(2500);

  const state = await page.evaluate(() => ({
    ioType: typeof window.io,
    socketScriptLoaded: !!window.__lcSocketLoaded,
    chatVisible: document.getElementById('lc-chat')?.classList.contains('visible'),
    err: document.getElementById('lc-form-err')?.textContent,
    session: (() => { try { return JSON.parse(localStorage.getItem('lc_session') || sessionStorage.getItem('lc_session') || 'null'); } catch { return 'unreadable'; } })(),
    lsKeys: Object.keys(localStorage).filter(k => /lc|chat/i.test(k)),
  }));
  console.log('STATE:', JSON.stringify(state, null, 2));

  // try sending a message
  await page.fill('#lc-input', 'Hello, this is a test message');
  await page.click('.lc-send-btn');
  await page.waitForTimeout(1200);
  const dialog = await page.evaluate(() => !!document.querySelector('.modal.show, [role=alertdialog]'));
  console.log('dialog appeared:', dialog);
  page.once('dialog', d => { console.log('ALERT:', d.message()); d.dismiss(); });
  await page.waitForTimeout(500);

  console.log('PAGE LOGS:', logs.length ? '\n' + logs.join('\n') : 'none');
  await browser.close();
})().catch(e => { console.error('FAIL:', e.message); process.exit(1); });
