/**
 * download-images.js
 * Dùng Puppeteer (browser thật) để bypass WAF SafeLine và download ảnh Joomla
 * Usage: node scripts/download-images.js
 */

const puppeteer = require('puppeteer');
const fs = require('fs');
const path = require('path');
const mysql = require('mysql2/promise');

// Đọc .env
function readEnv() {
  const env = {};
  const lines = fs.readFileSync(path.join(__dirname, '..', '.env'), 'utf8').split('\n');
  for (const line of lines) {
    const trimmed = line.trim();
    if (trimmed && !trimmed.startsWith('#') && trimmed.includes('=')) {
      const [k, ...vs] = trimmed.split('=');
      env[k.trim()] = vs.join('=').trim().replace(/^["']|["']$/g, '');
    }
  }
  return env;
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
  console.log('DB connected:', env.DB_DATABASE);

  // Lấy danh sách ảnh cần tải
  const [rows] = await db.query(
    "SELECT id, thumbnail FROM posts WHERE thumbnail IS NOT NULL AND thumbnail != '' AND thumbnail NOT LIKE 'http%' AND thumbnail LIKE 'images/%'"
  );
  console.log(`Total images: ${rows.length}`);

  // Mở browser thật (bypass WAF)
  const browser = await puppeteer.launch({
    headless: true,
    args: ['--no-sandbox', '--disable-setuid-sandbox'],
  });
  const page = await browser.newPage();

  // Truy cập trang chủ Joomla trước để lấy session cookie (pass WAF)
  console.log('Accessing Joomla to get WAF session...');
  await page.goto('https://enews.agu.edu.vn', { waitUntil: 'networkidle2', timeout: 30000 });
  console.log('WAF passed! Starting downloads...');

  let success = 0, fail = 0, skip = 0;

  for (let i = 0; i < rows.length; i++) {
    const { id, thumbnail } = rows[i];
    const remotePath = thumbnail.replace(/^\//, '');
    const remoteUrl  = `https://enews.agu.edu.vn/${remotePath}`;
    const localName  = path.basename(remotePath);
    const localPath  = path.join(storageDir, localName);
    const dbPath     = `joomla-images/${localName}`;

    process.stdout.write(`\r[${i+1}/${rows.length}] ${Math.round((i+1)/rows.length*100)}% - ${localName.slice(0,40)}`);

    // Skip nếu đã có file
    if (fs.existsSync(localPath) && fs.statSync(localPath).size > 100) {
      await db.query('UPDATE posts SET thumbnail=? WHERE id=?', [dbPath, id]);
      skip++;
      continue;
    }

    try {
      // Download qua Puppeteer (có WAF cookies)
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

  await browser.close();
  await db.end();

  console.log(`\n\nSuccess : ${success}`);
  console.log(`Skipped : ${skip}`);
  console.log(`Failed  : ${fail}`);
}

main().catch(err => { console.error(err); process.exit(1); });
