<?php

namespace App\Services;

use App\Models\Post;
use App\Models\Category;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class AguNewsCrawlerService
{
    protected string $targetDomain = 'enews.agu.edu.vn';
    protected string $targetIp = '171.244.43.212';
    protected string $cookieFile;

    public function __construct()
    {
        $this->cookieFile = storage_path('app/agu_cookies.txt');
    }

    /**
     * Tự động crawl các bài viết mới từ enews.agu.edu.vn
     */
    public function crawlLatestNews(int $limit = 10, bool $force = false): array
    {
        $importedCount = 0;
        $skippedCount = 0;
        $errors = [];

        try {
            $html = $this->fetchUrl("https://{$this.targetDomain}/");
            if (empty($html) || strpos($html, 'SafeLine') !== false) {
                // If cURL hits SafeLine WAF, try running Node Puppeteer scraper fallback
                $puppeteerResult = $this->runPuppeteerScraper($limit);
                if ($puppeteerResult['success']) {
                    return $puppeteerResult['summary'];
                }
                
                // Fallback attempt: Parse html if available
                if (empty($html)) {
                    return [
                        'status' => 'error',
                        'message' => 'Không thể kết nối đến trang nguồn enews.agu.edu.vn (SafeLine WAF).',
                        'imported' => 0,
                        'skipped' => 0
                    ];
                }
            }

            $articles = $this->extractArticleLinks($html);
            if (empty($articles)) {
                return [
                    'status' => 'warning',
                    'message' => 'Không tìm thấy liên kết bài viết nào trên trang nguồn.',
                    'imported' => 0,
                    'skipped' => 0
                ];
            }

            $defaultAuthor = User::where('role', 'admin')->first() ?? User::first();
            $defaultCategory = Category::firstOrCreate(
                ['slug' => 'tin-tuc-agu'],
                ['name' => 'Tin tức AGU', 'is_active' => true, 'show_in_menu' => true]
            );

            $count = 0;
            foreach ($articles as $articleData) {
                if ($count >= $limit) break;

                $url = $articleData['url'];
                $slug = Str::slug($articleData['title'] ?? '');
                
                if (empty($slug)) {
                    $slug = Str::slug(parse_url($url, PHP_URL_PATH));
                }

                // Check duplicate
                $existing = Post::where('slug', $slug)
                    ->orWhere('title', $articleData['title'] ?? '')
                    ->first();

                if ($existing && !$force) {
                    $skippedCount++;
                    continue;
                }

                // Fetch detail page
                $detailHtml = $this->fetchUrl($url);
                if (empty($detailHtml)) {
                    $errors[] = "Không tải được nội dung bài viết: {$url}";
                    continue;
                }

                $parsedPost = $this->parseArticleDetail($detailHtml, $url, $articleData);
                if (empty($parsedPost['content'])) {
                    $errors[] = "Không bóc tách được nội dung bài viết: {$url}";
                    continue;
                }

                // Download & localize main thumbnail
                $localThumbnail = null;
                if (!empty($parsedPost['thumbnail_url'])) {
                    $localThumbnail = $this->downloadAndSaveImage($parsedPost['thumbnail_url']);
                }

                // Localize all images inside content HTML
                $localizedContent = $this->localizeContentImages($parsedPost['content']);

                // Category resolution
                $category = $defaultCategory;
                if (!empty($parsedPost['category_name'])) {
                    $catSlug = Str::slug($parsedPost['category_name']);
                    $category = Category::firstOrCreate(
                        ['slug' => $catSlug],
                        ['name' => $parsedPost['category_name'], 'is_active' => true]
                    );
                }

                // Create or Update Post
                Post::updateOrCreate(
                    ['slug' => $slug],
                    [
                        'title' => $parsedPost['title'] ?? $articleData['title'],
                        'excerpt' => $parsedPost['excerpt'] ?? Str::limit(strip_tags($localizedContent), 250),
                        'content' => $localizedContent,
                        'thumbnail' => $localThumbnail ?? 'uploads/posts/default.jpg',
                        'author_id' => $defaultAuthor->id ?? 1,
                        'category_id' => $category->id,
                        'status' => 'published',
                        'published_at' => $parsedPost['published_at'] ?? now(),
                        'source_author' => $parsedPost['source_author'] ?? 'Enews AGU',
                        'photographer' => $parsedPost['photographer'] ?? null,
                    ]
                );

                $importedCount++;
                $count++;
            }

            return [
                'status' => 'success',
                'message' => "Đã đồng bộ thành công {$importedCount} bài viết mới (bỏ qua {$skippedCount} bài trùng).",
                'imported' => $importedCount,
                'skipped' => $skippedCount,
                'errors' => $errors
            ];

        } catch (\Throwable $e) {
            Log::error("AGU News Crawler Error: " . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return [
                'status' => 'error',
                'message' => 'Lỗi trong quá trình cào dữ liệu: ' . $e->getMessage(),
                'imported' => $importedCount,
                'skipped' => $skippedCount
            ];
        }
    }

    /**
     * Gửi request cURL kèm SNI/Host và Cookie Support
     */
    protected function fetchUrl(string $url): string
    {
        $parsed = parse_url($url);
        $path = ($parsed['path'] ?? '/') . (isset($parsed['query']) ? '?' . $parsed['query'] : '');
        $targetUrl = "https://{$this.targetIp}{$path}";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $targetUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_RESOLVE, ["{$this->targetDomain}:443:{$this.targetIp}"]);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Host: {$this->targetDomain}",
            "Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8",
            "Accept-Language: vi-VN,vi;q=0.9,en-US;q=0.8,en;q=0.7",
            "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0.0 Safari/537.36"
        ]);
        curl_setopt($ch, CURLOPT_COOKIEJAR, $this->cookieFile);
        curl_setopt($ch, CURLOPT_COOKIEFILE, $this->cookieFile);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);

        $html = curl_exec($ch);
        curl_close($ch);

        return $html ?: '';
    }

    /**
     * Bóc tách danh sách link bài viết từ trang chủ / chuyên mục
     */
    protected function extractArticleLinks(string $html): array
    {
        $articles = [];
        $dom = new \DOMDocument();
        @$dom->loadHTML('<?xml encoding="UTF-8">' . $html, LIBXML_NOERROR);

        $xpath = new \DOMXPath($dom);
        // Find links that look like news articles
        $nodes = $xpath->query("//a[contains(@href, '.html') or contains(@href, 'id=') or contains(@href, '/tin-tuc/')]");

        foreach ($nodes as $node) {
            $href = $node->getAttribute('href');
            $title = trim($node->textContent);

            if (strlen($title) < 15 || strpos($href, 'javascript:') !== false) {
                continue;
            }

            // Normalize URL
            if (strpos($href, 'http') !== 0) {
                $href = 'https://' . $this->targetDomain . '/' . ltrim($href, '/');
            }

            $articles[$href] = [
                'url' => $href,
                'title' => $title
            ];
        }

        return array_values($articles);
    }

    /**
     * Bóc tách chi tiết 1 bài viết
     */
    protected function parseArticleDetail(string $html, string $url, array $defaultData): array
    {
        $dom = new \DOMDocument();
        @$dom->loadHTML('<?xml encoding="UTF-8">' . $html, LIBXML_NOERROR);
        $xpath = new \DOMXPath($dom);

        // Title
        $titleNode = $xpath->query("//h1|//h2[contains(@class, 'title')]|//div[contains(@class, 'article-title')]")->item(0);
        $title = $titleNode ? trim($titleNode->textContent) : ($defaultData['title'] ?? '');

        // Category
        $categoryNode = $xpath->query("//div[contains(@class, 'breadcrumb')]//a[last()]|//span[contains(@class, 'category')]")->item(0);
        $categoryName = $categoryNode ? trim($categoryNode->textContent) : null;

        // Content
        $contentNode = $xpath->query("//div[contains(@class, 'content') or contains(@class, 'detail') or contains(@id, 'content') or contains(@class, 'article-body')]")->item(0);
        $content = '';
        if ($contentNode) {
            $content = $dom->saveHTML($contentNode);
        } else {
            // Fallback body query
            $bodyNodes = $xpath->query("//div[contains(@class, 'main')]");
            if ($bodyNodes->length > 0) {
                $content = $dom->saveHTML($bodyNodes->item(0));
            }
        }

        // Main thumbnail image
        $thumbNode = $xpath->query("//img[contains(@class, 'thumb') or contains(@class, 'featured')]|//div[contains(@class, 'content')]//img")->item(0);
        $thumbnailUrl = null;
        if ($thumbNode) {
            $src = $thumbNode->getAttribute('src');
            if ($src) {
                $thumbnailUrl = (strpos($src, 'http') === 0) ? $src : 'https://' . $this->targetDomain . '/' . ltrim($src, '/');
            }
        }

        // Author
        $authorNode = $xpath->query("//*[contains(@class, 'author') or contains(@class, 'tac-gia')]")->item(0);
        $sourceAuthor = $authorNode ? trim($authorNode->textContent) : 'Enews AGU';

        return [
            'title' => $title,
            'category_name' => $categoryName,
            'content' => $content,
            'thumbnail_url' => $thumbnailUrl,
            'source_author' => $sourceAuthor,
            'published_at' => now(),
        ];
    }

    /**
     * Tải hình ảnh về lưu ở local storage và trả về đường dẫn lưu trữ
     */
    public function downloadAndSaveImage(string $imageUrl): ?string
    {
        try {
            if (empty($imageUrl)) return null;

            // Make full absolute URL
            if (strpos($imageUrl, 'http') !== 0) {
                $imageUrl = 'https://' . $this->targetDomain . '/' . ltrim($imageUrl, '/');
            }

            $parsed = parse_url($imageUrl);
            $path = ($parsed['path'] ?? '/') . (isset($parsed['query']) ? '?' . $parsed['query'] : '');
            $targetUrl = "https://{$this.targetIp}{$path}";

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $targetUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch, CURLOPT_RESOLVE, ["{$this->targetDomain}:443:{$this.targetIp}"]);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                "Host: {$this->targetDomain}",
                "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64)"
            ]);
            curl_setopt($ch, CURLOPT_TIMEOUT, 15);
            $imgData = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($httpCode !== 200 || empty($imgData)) {
                return null;
            }

            // Determine extension
            $ext = pathinfo(parse_url($imageUrl, PHP_URL_PATH), PATHINFO_EXTENSION);
            if (!$ext || strlen($ext) > 4) $ext = 'jpg';

            $subDir = 'posts/' . date('Y/m');
            $fileName = Str::random(20) . '.' . strtolower($ext);
            $relativePath = "uploads/{$subDir}/{$fileName}";
            $fullDir = public_path("uploads/{$subDir}");

            if (!file_exists($fullDir)) {
                mkdir($fullDir, 0755, true);
            }

            file_put_contents("{$fullDir}/{$fileName}", $imgData);

            return $relativePath;

        } catch (\Throwable $e) {
            Log::warning("Failed to download image: {$imageUrl}. Error: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Tự động quét tất cả thẻ <img> trong bài viết, tải ảnh về local và sửa lại src
     */
    protected function localizeContentImages(string $content): string
    {
        if (empty($content)) return '';

        preg_match_all('/<img[^>]+src=[\'"]([^\'"]+)[\'"]/i', $content, $matches);

        if (!empty($matches[1])) {
            $urls = array_unique($matches[1]);
            foreach ($urls as $url) {
                $localPath = $this->downloadAndSaveImage($url);
                if ($localPath) {
                    $localUrl = asset($localPath);
                    $content = str_replace($url, $localUrl, $content);
                }
            }
        }

        return $content;
    }

    /**
     * Puppeteer Fallback Scraper execution
     */
    protected function runPuppeteerScraper(int $limit): array
    {
        $scriptPath = base_path('scripts/crawl_agu_puppeteer.js');
        if (!file_exists($scriptPath)) {
            return ['success' => false];
        }

        $cmd = sprintf(
            'node %s --limit=%d 2>&1',
            escapeshellarg($scriptPath),
            $limit
        );

        $output = shell_exec($cmd);
        $json = json_decode($output, true);

        if ($json && isset($json['success']) && $json['success']) {
            return [
                'success' => true,
                'summary' => [
                    'status' => 'success',
                    'message' => "Puppeteer: Đã đồng bộ thành công {$json['imported']} bài viết mới.",
                    'imported' => $json['imported'],
                    'skipped' => $json['skipped'] ?? 0
                ]
            ];
        }

        return ['success' => false];
    }
}
