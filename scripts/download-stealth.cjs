/**
 * download-stealth.cjs  — dùng puppeteer-extra + stealth plugin để bypass WAF
 * Usage: node scripts/download-stealth.cjs
 */

const puppeteer = require('puppeteer-extra');
const StealthPlugin = require('puppeteer-extra-plugin-stealth');
const fs = require('fs');
const path = require('path');
const mysql = require('mysql2/promise');

puppeteer.use(StealthPlugin());

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

async function main() {
  const env = readEnv();
  const storageDir = path.join(__dirname, '..', 'storage', 'app', 'public', 'joomla-images');
  fs.mkdirSync(storageDir, { recursive: true });

  const db = await mysql.createConnection({
    host: env.DB_HOST || '127.0.0.1',
    port: parseInt(env.DB_PORT || '3306'),
    database: env.DB_DATABASE,
    user: env.DB_USERNAME,
    password: env.DB_PASSWORD,
  });
  console.log('DB:', env.DB_DATABASE);

  const [rows] = await db.query(
    "SELECT id, thumbnail FROM posts WHERE thumbnail IS NOT NULL AND thumbnail != '' AND thumbnail NOT LIKE 'http%' AND thumbnail LIKE 'images/%'"
  );
  console.log(`Images to download: ${rows.length}`);

  const browser = await puppeteer.launch({
    headless: true,
    executablePath: require('puppeteer').executablePath(),
    args: [
      '--no-sandbox', '--disable-setuid-sandbox',
      '--disable-blink-features=AutomationControlled',
      '--disable-dev-shm-usage',
    ],
  });

  const page = await browser.newPage();
  await page.setDefaultTimeout(20000);
  await page.setUserAgent('Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36');

  console.log('Visiting Joomla to get WAF cookies...');
  try {
    await page.goto('https://enews.agu.edu.vn', { waitUntil: 'networkidle2', timeout: 30000 });
    // Chờ JS challenge pass
    await new Promise(r => setTimeout(r, 3000));
    const title = await page.title();
    console.log('Page title:', title);
  } catch (e) {
    console.log('Homepage timeout, continuing...');
  }

  let success = 0, fail = 0, skip = 0;

  for (let i = 0; i < rows.length; i++) {
    const { id, thumbnail } = rows[i];
    const remotePath = thumbnail.replace(/^\//, '');
    const remoteUrl  = `https://enews.agu.edu.vn/${remotePath}`;
    const localName  = path.basename(remotePath);
    const localPath  = path.join(storageDir, localName);
    const dbPath     = `joomla-images/${localName}`;

    process.stdout.write(`\r[${i+1}/${rows.length}] ${Math.round((i+1)/rows.length*100)}% | OK:${success} FAIL:${fail} SKIP:${skip} | ${localName.slice(0, 35)}`);

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

  await browser.close();
  await db.end();

  console.log(`\n\n=== DONE ===`);
  console.log(`Success : ${success}`);
  console.log(`Skipped : ${skip}`);
  console.log(`Failed  : ${fail}`);
}

main().catch(e => { console.error(e); process.exit(1); });
