const { chromium } = require('playwright-core');
const exe = 'C:/Users/Arafat/AppData/Local/ms-playwright/chromium-1223/chrome-win64/chrome.exe';
(async () => {
  const browser = await chromium.launch({ executablePath: exe, headless: true });
  const page = await browser.newPage();
  const logs = [];
  page.on('console', m => logs.push(m.type() + ': ' + m.text()));
  page.on('response', r => { if (r.url().includes('/api/livechat')) logs.push('API ' + r.status() + ' ' + r.url()); });

  await page.goto('http://127.0.0.1:8000/', { waitUntil: 'networkidle', timeout: 40000 });

  // wrap io() before the chat starts
  await page.evaluate(() => {
    window.__connEvents = [];
    const real = window.io;
    window.io = function (...args) {
      window.__ioArgs = JSON.stringify(args.map(a => typeof a === 'object' ? Object.keys(a) : a));
      const sock = real(...args);
      window.__lastSocket = sock;
      sock.on('connect', () => window.__connEvents.push('connected id=' + sock.id));
      sock.on('connect_error', e => window.__connEvents.push('connect_error: ' + (e.message || e)));
      sock.on('disconnect', r => window.__connEvents.push('disconnect: ' + r));
      return sock;
    };
  });

  await page.click('#lc-btn');
  await page.fill('#lc-name', 'Test Guest');
  await page.fill('#lc-phone', '01712345678');
  await page.click('.lc-start-btn');
  await page.waitForTimeout(4000);

  const state = await page.evaluate(() => ({
    ioArgs: window.__ioArgs,
    connEvents: window.__connEvents,
    connected: window.__lastSocket ? window.__lastSocket.connected : 'no socket',
    session: JSON.parse(localStorage.getItem('shopgram_lc_session') || 'null'),
  }));
  console.log('STATE:', JSON.stringify(state, null, 2));

  // manually hit the token endpoint with the session headers
  const tokenInfo = await page.evaluate(async () => {
    const s = JSON.parse(localStorage.getItem('shopgram_lc_session'));
    const res = await fetch('/api/livechat/token', {
      headers: { 'X-Session-Id': s.session_id, 'X-Chat-Id': String(s.chat_id), 'Accept': 'application/json' },
    });
    return { status: res.status, body: await res.text() };
  });
  console.log('TOKEN RESPONSE:', JSON.stringify(tokenInfo, null, 2));
  console.log('LOGS:', logs.length ? '\n' + logs.join('\n') : 'none');
  await browser.close();
})().catch(e => { console.error('FAIL:', e.message); process.exit(1); });
