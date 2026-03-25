/**
 * fetch-article-11116.cjs
 * Dùng Puppeteer để mở Chrome bình thường, lấy full content bài Joomla id=11116
 * rồi ghi ra file JSON để PHP script update vào Laravel DB
 */
const puppeteer = require('puppeteer');
const fs = require('fs');
const path = require('path');

const JOOMLA_URL = 'https://enews.agu.edu.vn/index.php?option=com_content&view=article&id=11116&Itemid=1';
const OUT_FILE = path.join(__dirname, 'article_11116.json');

(async () => {
    console.log('🚀 Khởi động Chrome...');
    const browser = await puppeteer.launch({
        headless: false,
        args: [
            '--no-sandbox',
            '--disable-blink-features=AutomationControlled',
            '--start-maximized'
        ],
        defaultViewport: null,
    });

    const page = await browser.newPage();

    // Giả lập trình duyệt thật
    await page.setUserAgent('Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36');
    await page.setExtraHTTPHeaders({ 'Accept-Language': 'vi-VN,vi;q=0.9,en;q=0.8' });

    console.log(`📄 Đang mở: ${JOOMLA_URL}`);
    try {
        await page.goto(JOOMLA_URL, { waitUntil: 'domcontentloaded', timeout: 30000 });
    } catch (e) {
        console.log('⚠️  goto timeout, tiếp tục...');
    }

    // Đợi trang ổn định sau redirect
    await new Promise(r => setTimeout(r, 5000));

    // Đợi nội dung bài load
    await page.waitForSelector('body', { timeout: 10000 }).catch(() => {});

    console.log('📋 Đang trích xuất nội dung...');
    const result = await page.evaluate(() => {

        // Thử nhiều selector khác nhau của Joomla template
        const selectors = [
            '.item-page',
            '.article-item',
            '#joomla-main-body',
            '.blog-item',
            'article',
            '.contentpane',
            '#content',
            '.page-content'
        ];

        let container = null;
        for (const sel of selectors) {
            container = document.querySelector(sel);
            if (container) break;
        }

        const titleEl = document.querySelector('h1, h2.article-title, .page-header h2');
        const title = titleEl ? titleEl.innerText.trim() : document.title;

        const content = container ? container.innerHTML : document.body.innerHTML;
        const text = container ? container.innerText.trim() : document.body.innerText.trim();

        return { title, content, text, url: location.href };
    });

    console.log(`✅ Title: ${result.title}`);
    console.log(`📝 Content length: ${result.content.length} chars`);

    fs.writeFileSync(OUT_FILE, JSON.stringify(result, null, 2), 'utf8');
    console.log(`💾 Đã lưu ra: ${OUT_FILE}`);

    await browser.close();
    console.log('🎉 Xong! Chạy PHP script tiếp theo để update DB.');
})();
