<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Post;
use App\Models\Category;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use DOMDocument;
use DOMXPath;

class CrawlRecentArticles extends Command
{
    protected $signature = 'enews:crawl-recent {--limit=50 : Số lượng bài báo tối đa để quét} {--cookie= : Cookie sl-session từ trình duyệt sau khi pass WAF}';
    protected $description = 'Crawl các bài viết mới từ enews.agu.edu.vn từ 11/03/2026 đến hiện tại';

    private $baseUrl = 'https://enews.agu.edu.vn';
    
    public function handle()
    {
        $this->info("Bắt đầu crawl dữ liệu từ trang enews.agu.edu.vn...");

        // Danh sách chuyên mục để quét (ID Joomla)
        // Đây là một vài ID chuyên mục chính của enews Joomla
        $categoryUrls = [
            '/', // Trang chủ
            '/index.php?option=com_content&view=category&layout=blog&id=10&Itemid=114', // Bản tin AGU
            '/index.php?option=com_content&view=category&layout=blog&id=11&Itemid=115', // Phóng sự Ảnh
            '/index.php?option=com_content&view=category&layout=blog&id=13&Itemid=117', // Khoa học với AGU
            '/index.php?option=com_content&view=category&layout=blog&id=16&Itemid=120', // Câu chuyện AGU
            '/index.php?option=com_content&view=category&layout=blog&id=14&Itemid=118', // Góc nhìn
            '/index.php?option=com_content&view=category&layout=blog&id=18&Itemid=122', // Tản mạn
            '/index.php?option=com_content&view=category&layout=blog&id=17&Itemid=121', // Gương mặt AGU
            '/index.php?option=com_content&view=category&layout=blog&id=21&Itemid=107', // SV với Câu lạc bộ
            '/index.php?option=com_content&view=category&layout=blog&id=20&Itemid=124', // eNews và Bạn đọc
            '/index.php?option=com_content&view=category&layout=blog&id=32&Itemid=108', // Lướt web cùng SV
        ];

        // Lấy danh sách category hiện có trong DB để mapping
        $localCategories = Category::all()->keyBy('name')->toArray();
        $defaultCategoryId = Category::first()->id ?? 1;
        $defaultUserId = User::first()->id ?? 1;

        $limit = $this->option('limit');
        $wafCookie = $this->option('cookie');
        $crawledCount = 0;
        $addedCount = 0;

        foreach ($categoryUrls as $catUrl) {
            if ($crawledCount >= $limit) break;

            $this->info("Đang quét chuyên mục: " . $catUrl);
            $html = $this->fetchHtml($this->baseUrl . $catUrl, $wafCookie);
            
            if (!$html) {
                $this->error("Không thể tải trang: " . $catUrl);
                continue;
            }
            
            $this->info("   -> Đã tải xong HTML (" . strlen($html) . " bytes)");

            // Sử dụng Regex để tìm chính xác các link bài viết (bất chấp HTML bị lỗi)
            preg_match_all('/href=[\'"]([^\'"]+view=article[^\'"]+id=[^\'"]+)[\'"]/i', $html, $matches);
            $links = $matches[1] ?? [];

            $articleUrls = [];
            foreach ($links as $href) {
                // Giải mã các entity như &amp; thành &
                $href = html_entity_decode($href);

                // Xử lý link tương đối và tuyệt đối
                if (str_starts_with($href, 'http')) {
                    if (!str_contains($href, 'enews.agu.edu.vn')) continue;
                    $parsed = parse_url($href);
                    $href = ($parsed['path'] ?? '/') . (isset($parsed['query']) ? '?' . $parsed['query'] : '');
                } else if (!str_starts_with($href, '/')) {
                    $href = '/' . $href;
                }

                if (!in_array($href, $articleUrls)) {
                    $articleUrls[] = $href;
                }
            }

            foreach ($articleUrls as $articleUrl) {
                if ($crawledCount >= $limit) break;
                
                $fullUrl = rtrim($this->baseUrl, '/') . '/' . ltrim($articleUrl, '/');
                $this->line("-> Tìm thấy bài viết: " . $fullUrl);
                
                // Lấy ID bài viết từ URL để tránh trùng
                preg_match('/id=(\d+)/', $articleUrl, $matches);
                $joomlaId = $matches[1] ?? null;

                $crawledCount++;

                // Lấy chi tiết bài viết
                $articleHtml = $this->fetchHtml($fullUrl, $wafCookie);
                if (!$articleHtml) continue;

                $articleDom = new DOMDocument();
                @$articleDom->loadHTML('<?xml encoding="UTF-8">' . $articleHtml);
                $articleXpath = new DOMXPath($articleDom);

                // Lấy Tiêu đề
                $titleNode = $articleXpath->query('//h2[contains(@class, "article-title")] | //h1 | //div[contains(@class,"page-header")]/h2')->item(0);
                $title = $titleNode ? trim($titleNode->nodeValue) : '';
                
                if (empty($title)) {
                    $this->warn("   Không tìm thấy tiêu đề, bỏ qua.");
                    continue;
                }

                // Cắt ngắn title nếu quá dài để tránh lỗi DB
                $title = Str::limit($title, 250, '');
                $slug = Str::slug($title);
                $slug = Str::limit($slug, 250, '');

                // Lấy ngày đăng (Published Date) TRƯỚC KHI check tồn tại
                $dateNode = $articleXpath->query('//dd[contains(@class, "published")] | //span[contains(@class, "created")] | //time[@itemprop="datePublished"]')->item(0);
                $dateStr = $dateNode ? trim($dateNode->nodeValue) : '';
                
                $publishedAt = now(); // Mặc định nếu không tìm thấy
                
                if ($dateStr) {
                    // Xử lý chuỗi "Ngày đăng: 24 Tháng 12 2019"
                    preg_match('/(\d{1,2})\s+Tháng\s+(\d{1,2})\s+(\d{4})/u', $dateStr, $dateMatches);
                    
                    if (count($dateMatches) == 4) {
                        $day = $dateMatches[1];
                        $month = $dateMatches[2];
                        $year = $dateMatches[3];
                        try {
                            $publishedAt = \Carbon\Carbon::createFromDate($year, $month, $day);
                        } catch (\Exception $e) {}
                    } else {
                        try {
                            $publishedAt = \Carbon\Carbon::parse(strip_tags($dateStr));
                        } catch (\Exception $e) {}
                    }
                }

                // Lấy Nội dung
                $contentNode = $articleXpath->query('//div[contains(@class, "item-page")] | //article | //div[@itemprop="articleBody"]')->item(0);
                if (!$contentNode) {
                    $this->warn("   Không tìm thấy nội dung, bỏ qua.");
                    continue;
                }

                // DỌN RÁC TRIỆT ĐỂ: Loại bỏ Komento và các thẻ rác
                $trashQueries = [
                    './/*[@id="section-kmt"]',
                    './/*[contains(@class, "kmt-")]',
                    './/*[contains(@id, "kmt-")]',
                    './/script',
                    './/style',
                    './/*[contains(@class, "item-separator")]',
                    './/*[contains(@class, "post-share")]',
                    './/*[contains(@class, "article-info")]',
                    './/*[contains(@class, "icons")]',
                    './/*[contains(@class, "btn-group")]',
                    './/*[contains(@class, "dropdown-menu")]',
                    './/*[contains(@class, "pagenavcounter")]',
                    './/*[contains(@class, "pagenav")]'
                ];
                
                foreach ($trashQueries as $query) {
                    $trashNodes = $articleXpath->query($query, $contentNode);
                    foreach ($trashNodes as $node) {
                        if ($node->parentNode) {
                            $node->parentNode->removeChild($node);
                        }
                    }
                }


                // Tự tải ảnh về và cập nhật img src trong bài viết
                $thumbnail = null;
                $imgNodes = $articleXpath->query('.//img', $contentNode);
                foreach ($imgNodes as $img) {
                    $imgSrc = $img->getAttribute('src');
                    
                    // Bỏ qua các ảnh linh tinh như icon, index.php
                    if (empty($imgSrc) || str_contains($imgSrc, 'index.php') || str_contains($imgSrc, 'stackideas') || str_contains($imgSrc, 'komento')) {
                        continue;
                    }
                    
                    // Lấy URL gốc
                    if (!str_starts_with($imgSrc, 'http')) {
                        $imgSrc = ltrim($imgSrc, '/');
                        $imgSrc = $this->baseUrl . '/' . $imgSrc;
                    }

                    // Gọi hàm tải ảnh
                    $localImage = $this->downloadImage($imgSrc, $wafCookie);
                    
                    if ($localImage) {
                        // Cập nhật thẻ img src thành đường dẫn local
                        $img->setAttribute('src', '/storage/' . $localImage);
                        
                        // Dùng ảnh đầu tiên tải thành công làm thumbnail
                        if (!$thumbnail) {
                            $thumbnail = $localImage;
                        }
                    }
                }

                // Chuyển node sang HTML string SAU KHI ĐÃ SỬA src của img
                $content = $articleDom->saveHTML($contentNode);

                // Ép chuẩn UTF-8 để tránh lỗi "Incorrect string value" của DB
                $content = mb_convert_encoding($content, 'UTF-8', 'UTF-8');
                $title = mb_convert_encoding($title, 'UTF-8', 'UTF-8');

                // Xóa các thẻ không cần thiết như tiêu đề bị lặp trong content
                $content = preg_replace('/<h2[^>]*>.*?<\/h2>/is', '', $content, 1);

                // Xóa các cụm phân trang rác của Joomla (pagenav, pagenavcounter)
                $content = preg_replace('/<div[^>]*class="[^"]*pagenavcounter[^"]*"[^>]*>.*?<\/div>/is', '', $content);
                $content = preg_replace('/<div[^>]*class="[^"]*pagenav[^"]*"[^>]*>.*?<\/div>/is', '', $content);
                $content = preg_replace('/<ul[^>]*class="[^"]*pagenav[^"]*"[^>]*>.*?<\/ul>/is', '', $content);
                
                // Đôi khi có span phân trang
                $content = preg_replace('/<span[^>]*class="[^"]*pagenav[^"]*"[^>]*>.*?<\/span>/is', '', $content);

                // Kiểm tra xem bài đã tồn tại chưa (dựa theo slug)
                $post = Post::where('slug', $slug)->first();
                if ($post) {
                    $post->published_at = $publishedAt;
                    $post->content = $content;
                    if ($thumbnail) {
                        $post->thumbnail = $thumbnail;
                    }
                    $post->save();
                    $this->warn("   Đã cập nhật: ngày đăng, nội dung (sửa ảnh) và thumbnail.");
                    continue;
                }

                // Lấy tác giả từ dòng cuối cùng của bài viết hoặc thẻ span
                $authorNode = $articleXpath->query('//span[@itemprop="author"] | //div[contains(@class, "createdby")]')->item(0);
                $sourceAuthor = $authorNode ? trim(strip_tags($authorNode->nodeValue)) : null;
                if ($sourceAuthor) {
                    $sourceAuthor = mb_convert_encoding($sourceAuthor, 'UTF-8', 'UTF-8');
                    $sourceAuthor = Str::limit($sourceAuthor, 250, '');
                }

                // Lấy chuyên mục từ breadcrumb hoặc thẻ category
                $catNode = $articleXpath->query('//dd[contains(@class, "category-name")]/a | //span[@itemprop="genre"]')->item(0);
                $catName = $catNode ? trim($catNode->nodeValue) : 'Tin tức';
                
                // Mapping category
                $categoryId = $defaultCategoryId;
                foreach ($localCategories as $name => $cat) {
                    if (str_contains(mb_strtolower($catName), mb_strtolower($name))) {
                        $categoryId = $cat['id'];
                        break;
                    }
                }

                // Lưu vào database
                try {
                $post = new Post();
                $post->title = $title;
                $post->slug = $slug;
                $post->content = $content;
                $post->thumbnail = $thumbnail;
                
                // Đảm bảo lấy đúng User ID hiện có trong DB
                $user = User::first();
                if (!$user) {
                    // Nếu chưa có user nào, tạo tạm một ông Admin
                    $user = User::create([
                        'name' => 'Admin',
                        'email' => 'admin@agu.edu.vn',
                        'password' => bcrypt('12345678'),
                    ]);
                }
                $post->author_id = $user->id;

                $post->category_id = $categoryId;
                $post->status = 'published';
                $post->published_at = $publishedAt;
                $post->source_author = $sourceAuthor;
                $post->view_count = 0;
                $post->save();
                    $this->info("   [OK] Đã lưu bài: " . $title);
                    $addedCount++;
                } catch (\Exception $e) {
                    // Cắt lấy dòng đầu tiên của Exception để tránh in cả câu query HTML siêu dài ra console
                    $errMsg = explode("\n", $e->getMessage())[0];
                    $this->error("   [Lỗi DB] " . $errMsg);
                }
                
                // Sleep lâu hơn một chút (1s) để server Joomla không bị sập (Time out)
                usleep(1000000); 
            }
        }

        $this->info("Hoàn tất! Đã quét $crawledCount link, thêm mới $addedCount bài viết.");
    }

    private function fetchHtml($url, $wafCookie = null)
    {
        try {
            $headers = [
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0.0 Safari/537.36'
            ];
            
            if ($wafCookie) {
                $headers['Cookie'] = $wafCookie;
            }

            $response = Http::withoutVerifying() // Bỏ qua kiểm tra SSL nếu local bị lỗi certificate
                ->withHeaders($headers)
                ->timeout(60)->get($url); // Tăng timeout lên 60s

            if ($response->successful()) {
                return $response->body();
            } else {
                $this->error("      [Lỗi HTTP] Code: " . $response->status() . " tại " . $url);
            }
        } catch (\Exception $e) {
            // Lấy dòng lỗi đầu tiên cho gọn
            $this->error("      [Lỗi Kết Nối] " . explode("\n", $e->getMessage())[0]);
        }
        return null;
    }

    private function downloadImage($remoteUrl, $wafCookie = null)
    {
        if (empty($remoteUrl)) return null;

        // Bỏ qua rác thêm lần nữa cho chắc
        if (str_contains($remoteUrl, 'index.php') || str_contains($remoteUrl, 'stackideas') || str_contains($remoteUrl, 'komento')) {
            return null;
        }

        $rawFilename = basename(parse_url($remoteUrl, PHP_URL_PATH));
        if (empty($rawFilename)) $rawFilename = uniqid() . '.jpg';

        // Làm sạch tên file gốc
        $rawFilename = preg_replace('/[^a-zA-Z0-9_.-]/', '', urldecode($rawFilename));

        // ✅ Thêm hash ngắn từ URL để đảm bảo 2 file khác nhau nhưng cùng tên không bị trùng nhau
        // Ví dụ: "anh1.jpg" từ bài A → "a1b2c3d4_anh1.jpg", từ bài B → "9f8e7d6c_anh1.jpg"
        $urlHash = substr(md5($remoteUrl), 0, 8);
        $filename = $urlHash . '_' . $rawFilename;

        $localPath = 'joomla-images/' . $filename;

        // Nếu ảnh đã tồn tại (đúng URL này) thì không tải lại, trả về luôn path
        if (Storage::disk('public')->exists($localPath)) {
            return $localPath;
        }

        try {
            $ch = curl_init($remoteUrl);
            
            $headers = [
                'Accept: image/avif,image/webp,image/apng,image/svg+xml,image/*,*/*;q=0.8',
                'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0.0 Safari/537.36',
            ];
            
            if ($wafCookie) {
                $headers[] = 'Cookie: ' . $wafCookie;
            }

            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_MAXREDIRS      => 5,
                CURLOPT_TIMEOUT        => 30, // Tăng timeout tải ảnh lên 30s
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => false,
                CURLOPT_HTTPHEADER     => $headers
            ]);

            $content = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($content !== false && $httpCode < 400 && strlen($content) > 100) {
                Storage::disk('public')->put($localPath, $content);
                return $localPath;
            }
        } catch (\Exception $e) {
            // Bỏ qua lỗi kết nối tải ảnh
        }

        return null;
    }
}
