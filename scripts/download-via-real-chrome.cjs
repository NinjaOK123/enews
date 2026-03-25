/**
 * download-via-real-chrome.cjs
 * Kết nối vào Chrome thật (đang chạy với --remote-debugging-port=9222)
 * để bypass WAF bằng session cookie thật của user
 * 
 * HƯỚNG DẪN:
 * 1. Đóng Chrome đang mở
 * 2. Mở Chrome với remote debugging:
 *    "C:\Program Files\Google\Chrome\Application\chrome.exe" --remote-debugging-port=9222
 * 3. Trong Chrome, vào https://enews.agu.edu.vn (pass WAF bình thường)
 * 4. Chạy: node scripts/download-via-real-chrome.cjs
 */

const puppeteer = require('puppeteer');
const fs = require('fs');
const path = require('path');
const mysql = require('mysql2/promise');
const http = require('http');

function readEnv() {
  const env = {};
  const lines = fs.readFileSync(path.join(__dirname, '..', '.env'), 'utf8').split('\n');
  for (const line of lines) {
    const t = line.trim();
    if (t && !t.startsWith('#') && t.includes('=')) {
      const [k, ...vs] = t.split('=');
      env[k.trim()] = vs.join('=').trim().replace(/^["']|["']$/g, '');
    }
  }
  return env;
}

// Lấy websocket URL từ Chrome remote debugging
function getChromeWSUrl() {
  return new Promise((resolve, reject) => {
    http.get('http://localhost:9222/json/version', (res) => {
      let data = '';
      res.on('data', chunk => data += chunk);
      res.on('end', () => {
        try {
          const json = JSON.parse(data);
          resolve(json.webSocketDebuggerUrl);
        } catch(e) {
          reject(new Error('Cannot parse Chrome debug info'));
        }
      });
    }).on('error', () => {
      reject(new Error('Chrome remote debugging not found on port 9222.\nHay mo Chrome voi: chrome.exe --remote-debugging-port=9222'));
    });
  });
}

async function main() {
  const env = readEnv();
  const storageDir = path.join(__dirname, '..', 'storage', 'app', 'public', 'joomla-images');
  fs.mkdirSync(storageDir, { recursive: true });

  // Kết nối DB
  const db = await mysql.createConnection({
    host: env.DB_HOST || '127.0.0.1',
    port: parseInt(env.DB_PORT || '3306'),
    database: env.DB_DATABASE,
    user: env.DB_USERNAME,
    password: env.DB_PASSWORD,
  });
  console.log('DB:', env.DB_DATABASE);

  const [rows] = await db.query(
    `SELECT p.id, p.thumbnail FROM posts p
     WHERE p.thumbnail IS NOT NULL AND p.thumbnail != ''
       AND p.thumbnail NOT LIKE 'http%'
       AND p.thumbnail LIKE 'images/%'
       AND p.published_at >= '2022-01-01'
     ORDER BY p.published_at DESC`
  );
  console.log(`Images to download: ${rows.length}`);

  // Kết nối vào Chrome thật đang chạy
  console.log('Connecting to real Chrome browser on port 9222...');
  let wsUrl;
  try {
    wsUrl = await getChromeWSUrl();
    console.log('Found Chrome! WS:', wsUrl.slice(0, 60) + '...');
  } catch(e) {
    console.error('ERROR:', e.message);
    process.exit(1);
  }

  const browser = await puppeteer.connect({
    browserWSEndpoint: wsUrl,
    defaultViewport: null,
  });
  console.log('Connected to real Chrome!');

  // Mở tab mới để download
  const page = await browser.newPage();
  await page.setDefaultTimeout(20000);

  // Kiểm tra WAF session bằng cách truy cập trang chủ
  console.log('Checking WAF session on Joomla...');
  try {
    await page.goto('https://enews.agu.edu.vn', { waitUntil: 'networkidle2', timeout: 15000 });
    const title = await page.title();
    console.log('Joomla page title:', title);
    if (title.toLowerCase().includes('confirm') || title.toLowerCase().includes('security')) {
      console.log('WAF challenge detected. Please pass it in Chrome first, then press ENTER');
      await new Promise(r => process.stdin.once('data', r));
    }
  } catch(e) {
    console.log('Continue anyway...');
  }

  console.log('Starting image downloads...\n');
  let success = 0, fail = 0, skip = 0;

  for (let i = 0; i < rows.length; i++) {
    const { id, thumbnail } = rows[i];
    const remotePath = thumbnail.replace(/^\//, '');
    const remoteUrl  = `https://enews.agu.edu.vn/${remotePath}`;
    const localName  = path.basename(remotePath);
    const localPath  = path.join(storageDir, localName);
    const dbPath     = `joomla-images/${localName}`;

    process.stdout.write(`\r[${i+1}/${rows.length}] ${Math.round((i+1)/rows.length*100)}% | OK:${success} FAIL:${fail} | ${localName.slice(0, 35)}`);

    if (fs.existsSync(localPath) && fs.statSync(localPath).size > 100) {
      await db.query('UPDATE posts SET thumbnail=? WHERE id=?', [dbPath, id]);
      skip++;
      continue;
    }

    try {
      const response = await page.goto(remoteUrl, { waitUntil: 'load', timeout: 15000 });
      if (!response || response.status() >= 400) { fail++; continue; }

      const buffer = await response.buffer();
      if (buffer.length < 100) { fail++; continue; }

      fs.writeFileSync(localPath, buffer);
      await db.query('UPDATE posts SET thumbnail=? WHERE id=?', [dbPath, id]);
      success++;
    } catch (e) {
      fail++;
    }
  }

  await page.close();
  // Không đóng browser vì là Chrome thật của user
  await browser.disconnect();
  await db.end();

  console.log(`\n\n=== DONE ===`);
  console.log(`Success : ${success}`);
  console.log(`Skipped : ${skip}`);
  console.log(`Failed  : ${fail}`);

  // Clear cache Laravel sau khi cập nhật thumbnails
  if (success > 0) {
    const { execSync } = require('child_process');
    try {
      execSync('php artisan cache:clear', { cwd: path.join(__dirname, '..') });
      console.log('Laravel cache cleared!');
    } catch(e) {}
  }
}

main().catch(e => { console.error(e.message); process.exit(1); });
