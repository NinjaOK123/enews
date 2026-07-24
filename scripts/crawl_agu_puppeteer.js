import puppeteer from 'puppeteer-extra';
import StealthPlugin from 'puppeteer-extra-plugin-stealth';
import mysql from 'mysql2/promise';
import fs from 'fs';
import path from 'path';
import crypto from 'crypto';

puppeteer.use(StealthPlugin());

// Read .env file for database credentials
let dbPassword = 'vertrigo';
try {
  const envContent = fs.readFileSync(path.resolve('.env'), 'utf8');
  const match = envContent.match(/DB_PASSWORD=(.*)/);
  if (match && match[1]) dbPassword = match[1].trim();
} catch (e) {}

// MySQL configuration matching Laravel .env
const dbConfig = {
  host: process.env.DB_HOST || '127.0.0.1',
  port: process.env.DB_PORT || 3306,
  user: process.env.DB_USERNAME || 'root',
  password: process.env.DB_PASSWORD || dbPassword,
  database: process.env.DB_DATABASE || 'enews'
};

function slugify(text) {
  return text.toString().toLowerCase()
    .normalize('NFD').replace(/[\u0300-\u036f]/g, '')
    .replace(/[đĐ]/g, 'd')
    .replace(/[^a-z0-9 -]/g, '')
    .replace(/\s+/g, '-')
    .replace(/-+/g, '-')
    .trim();
}

(async () => {
  console.log('[AGU Puppeteer Crawler] Starting automated scraper...');
  let browser;
  let connection;

  try {
    connection = await mysql.createConnection(dbConfig);

    browser = await puppeteer.launch({
      headless: 'new',
      executablePath: 'C:\\Program Files\\Google\\Chrome\\Application\\chrome.exe',
      args: [
        '--no-sandbox',
        '--disable-setuid-sandbox',
        '--ignore-certificate-errors',
        '--host-rules=MAP enews.agu.edu.vn 171.244.43.212'
      ]
    });

    const page = await browser.newPage();
    await page.setViewport({ width: 1280, height: 800 });

    await page.evaluateOnNewDocument(() => {
      const OriginalWebSocket = window.WebSocket;
      window.WebSocket = function (url, protocols) {
        if (typeof url === 'string' && (url.includes('127.0.0.1') || url.includes('localhost'))) {
          return { addEventListener: () => {}, removeEventListener: () => {}, send: () => {}, close: () => {}, readyState: 3 };
        }
        return new OriginalWebSocket(url, protocols);
      };
      window.WebSocket.prototype = OriginalWebSocket.prototype;
      Object.defineProperty(navigator, 'webdriver', { get: () => false });
    });

    console.log('[AGU Puppeteer Crawler] Navigating to https://enews.agu.edu.vn/ ...');
    await page.goto('https://enews.agu.edu.vn/', { waitUntil: 'domcontentloaded' });
    
    // Wait for SafeLine dynamic decrypting to finish
    await page.waitForFunction(() => {
      const title = document.title;
      const body = document.body ? document.body.innerText : '';
      return !title.includes('WAF') && !body.includes('解密中') && !body.includes('Decrypting') && document.querySelectorAll('a').length > 5;
    }, { timeout: 30000 }).catch(e => console.log('WAF wait notice:', e.message));

    console.log('[AGU Puppeteer Crawler] SafeLine Challenge / Decryption passed!');
    console.log('[AGU Puppeteer Crawler] Title:', await page.title());

    console.log('[AGU Puppeteer Crawler] Scraping article links...');
    const articleLinks = await page.evaluate(() => {
      const links = [];
      document.querySelectorAll('a').forEach(a => {
        const href = a.href;
        const text = a.innerText.trim();
        if (text.length > 5 && href && !href.startsWith('javascript:') && !href.includes('#')) {
          links.push({ title: text, url: href });
        }
      });
      return links;
    });

    console.log(`[AGU Puppeteer Crawler] Found ${articleLinks.length} article links.`);

    let imported = 0;
    const limit = 15;

    // Get default admin user and default category
    const [users] = await connection.query('SELECT id FROM users LIMIT 1');
    const authorId = users[0] ? users[0].id : 1;

    const [cats] = await connection.query("SELECT id FROM categories WHERE slug = 'tin-tuc-agu' LIMIT 1");
    let categoryId;
    if (cats.length > 0) {
      categoryId = cats[0].id;
    } else {
      const [res] = await connection.query("INSERT INTO categories (name, slug, is_active, created_at, updated_at) VALUES ('Tin tức AGU', 'tin-tuc-agu', 1, NOW(), NOW())");
      categoryId = res.insertId;
    }

    for (const item of articleLinks) {
      if (imported >= limit) break;

      const slug = slugify(item.title).substring(0, 240);
      if (!slug) continue;

      const [existing] = await connection.query('SELECT id FROM posts WHERE slug = ? OR title = ? LIMIT 1', [slug, item.title]);
      if (existing.length > 0) {
        console.log(`[AGU Puppeteer Crawler] Skipping duplicate: ${item.title}`);
        continue;
      }

      console.log(`[AGU Puppeteer Crawler] Fetching article detail: ${item.url}`);
      await page.goto(item.url, { waitUntil: 'domcontentloaded' });
      await new Promise(r => setTimeout(r, 2000));

      const articleData = await page.evaluate(() => {
        const titleEl = document.querySelector('h1, h2.article-title, .page-header h2');
        const title = titleEl ? titleEl.innerText.trim() : '';

        const contentEl = document.querySelector('div.item-page, article, div[itemprop="articleBody"], .content');
        if (!contentEl) return null;

        // Clean trash
        ['#section-kmt', '.kmt-', '.post-share', '.article-info', '.pagenav'].forEach(sel => {
          contentEl.querySelectorAll(sel).forEach(el => el.remove());
        });

        const imgs = Array.from(contentEl.querySelectorAll('img')).map(img => img.src);

        return {
          title,
          contentHtml: contentEl.innerHTML,
          images: imgs
        };
      });

      if (!articleData || !articleData.contentHtml) {
        console.log(`[AGU Puppeteer Crawler] Could not extract content for: ${item.url}`);
        continue;
      }

      let finalHtml = articleData.contentHtml;
      let thumbnailPath = null;

      // Download content images
      for (const imgUrl of articleData.images) {
        if (!imgUrl || imgUrl.includes('komento') || imgUrl.includes('index.php')) continue;

        try {
          const imgResponse = await page.goto(imgUrl);
          const buffer = await imgResponse.buffer();

          if (buffer && buffer.length > 200) {
            const ext = path.extname(new URL(imgUrl).pathname) || '.jpg';
            const hashName = crypto.createHash('md5').update(imgUrl).digest('hex').substring(0, 8);
            const fileName = `${hashName}_${Date.now()}${ext.toLowerCase()}`;
            const subDir = path.join('storage', 'joomla-images');
            const fullDir = path.resolve('storage', 'app', 'public', 'joomla-images');

            if (!fs.existsSync(fullDir)) {
              fs.mkdirSync(fullDir, { recursive: true });
            }

            fs.writeFileSync(path.join(fullDir, fileName), buffer);

            const localUrl = `/storage/joomla-images/${fileName}`;
            finalHtml = finalHtml.replaceAll(imgUrl, localUrl);

            if (!thumbnailPath) {
              thumbnailPath = `joomla-images/${fileName}`;
            }
          }
        } catch (e) {
          console.log(`[AGU Puppeteer Crawler] Failed to download image ${imgUrl}: ${e.message}`);
        }
      }

      // Insert post into MySQL
      const postTitle = articleData.title || item.title;
      const excerpt = postTitle;

      await connection.query(
        `INSERT INTO posts (title, slug, excerpt, content, thumbnail, author_id, category_id, status, published_at, view_count, source_author, created_at, updated_at)
         VALUES (?, ?, ?, ?, ?, ?, ?, 'published', NOW(), 0, 'Enews AGU', NOW(), NOW())`,
        [postTitle, slug, excerpt, finalHtml, thumbnailPath || 'uploads/posts/default.jpg', authorId, categoryId]
      );

      console.log(`[AGU Puppeteer Crawler] [SUCCESS] Saved article: ${postTitle}`);
      imported++;
    }

    console.log(`[AGU Puppeteer Crawler] Finished! Imported ${imported} new articles.`);

  } catch (err) {
    console.error('[AGU Puppeteer Crawler Error]', err);
  } finally {
    if (browser) await browser.close();
    if (connection) await connection.end();
  }
})();
