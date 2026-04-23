<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Log;

class PlagiarismService
{
    /**
     * Tìm kiếm các links từ DuckDuckGo và CrossRef cho 1 đoạn câu
     */
    public function searchWeb(string $query): array
    {
        $urls = [];
        $queryEncoded = urlencode(Str::limit($query, 150, ''));

        // 1. Google Search via Serper.dev (Ưu tiên nhất cho tiếng Việt)
        if (env('SERPER_API_KEY')) {
            try {
                $response = Http::withOptions(['verify' => storage_path('cacert.pem')])
                    ->withHeaders([
                        'X-API-KEY' => env('SERPER_API_KEY'),
                        'Content-Type' => 'application/json'
                    ])
                    ->timeout(10)
                    ->post('https://google.serper.dev/search', [
                        'q' => Str::limit($query, 200, ''),
                        'gl' => 'vn',
                        'hl' => 'vi',
                        'num' => 10
                    ]);

                if ($response->successful()) {
                    $results = $response->json();
                    Log::info('Serper Response', [
                        'status' => $response->status(),
                        'organic_count' => count($results['organic'] ?? []),
                        'credits_remaining' => $response->header('X-RateLimit-Remaining'),
                    ]);
                    if (isset($results['organic'])) {
                        foreach ($results['organic'] as $organic) {
                            if (isset($organic['link']) && str_starts_with($organic['link'], 'http')) {
                                $urls[] = $organic['link'];
                            }
                        }
                    }
                } else {
                    Log::warning('Serper API returned non-200', [
                        'status' => $response->status(),
                        'body' => $response->body()
                    ]);
                }
            } catch (\Exception $e) {
                Log::error("Serper Search Error: " . $e->getMessage());
            }
        }

        // Nếu Serper tìm được URL, trả về luôn không cần các nguồn phụ
        if (count($urls) > 0) {
            return array_slice(array_unique($urls), 0, 10);
        }

        // 2. DuckDuckGo HTML Scraper (Fallback nếu không có SERPER KEY)
        try {
            $ddgQuery = urlencode(Str::limit($query, 200, ''));
            $ddgUrl = "https://html.duckduckgo.com/html/?q={$ddgQuery}";
            
            $response = Http::withOptions(['verify' => storage_path('cacert.pem')])->withHeaders([
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)'
            ])->timeout(8)->get($ddgUrl);

            if ($response->successful()) {
                $html = $response->body();
                if (preg_match_all('/uddg=([^"&]+)/i', $html, $matches)) {
                    foreach ($matches[1] as $encodedUrl) {
                        try {
                            $decodedUrl = urldecode(urldecode($encodedUrl)); // Có thể decode
                            if (str_starts_with($decodedUrl, 'http') && !str_contains($decodedUrl, 'duckduckgo.com')) {
                                $urls[] = $decodedUrl;
                            }
                        } catch (\Exception $e) { }
                    }
                }
            }
        } catch (\Exception $e) { }

        // 3. CrossRef API (for academic/articles)
        try {
            $crossRefUrl = "https://api.crossref.org/works?query={$queryEncoded}&rows=5";
            $response = Http::withOptions(['verify' => storage_path('cacert.pem')])->timeout(5)->get($crossRefUrl);
            
            if ($response->successful()) {
                $data = $response->json();
                if (isset($data['message']['items'])) {
                    foreach ($data['message']['items'] as $item) {
                        if (isset($item['URL'])) {
                            $urls[] = $item['URL'];
                        }
                    }
                }
            }
        } catch (\Exception $e) { }

        // Loại bỏ trùng lặp và lấy max 10 links
        return array_slice(array_unique($urls), 0, 10);
    }

    /**
     * Tải nội dung text của trang web
     */
    public function fetchPageContent(string $url): string
    {
        try {
            $response = Http::withOptions(['verify' => storage_path('cacert.pem')])->withHeaders([
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
                'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
                'Accept-Language' => 'en-US,en;q=0.5',
            ])
            ->timeout(8) // timeout max 8s
            ->get($url);

            if (!$response->successful()) {
                return '';
            }

            $html = $response->body();

            // Loại bỏ các tags rác (script, style, nav, header, footer, aside)
            $html = preg_replace('/<(script|style|nav|header|footer|aside)[^>]*>.*?<\/\1>/is', '', $html);
            // Loại bỏ toàn bộ HTML tags để lấy plain text
            $text = strip_tags($html);
            // Decode html entities
            $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
            // Remove dư thừa khoảng trắng
            $text = preg_replace('/\s+/u', ' ', $text);
            $text = trim($text);

            return mb_substr($text, 0, 8000); // Lấy max 8000 chars để đủ ngữ cảnh

        } catch (\Exception $e) {
            return '';
        }
    }

    /**
     * Loại bỏ các Hư từ (Stopwords) tiếng Việt phổ biến để giảm False Positives
     */
    private function removeStopwords(string $text): string
    {
        $stopwords = [
            'và', 'của', 'là', 'có', 'trong', 'được', 'cho', 'những', 'một', 'các', 'với', 'để', 
            'không', 'như', 'khi', 'người', 'đến', 'này', 'đã', 'từ', 'vào', 'ra', 'đó', 'thì', 
            'mà', 'theo', 'trên', 'tại', 'sẽ', 'nhưng', 'lại', 'rất', 'cũng', 'làm', 'phải', 
            'về', 'những', 'điều', 'sự', 'bởi', 'do', 'nào', 'nữa'
        ];

        $words = preg_split('/\s+/u', mb_strtolower($text));
        $filtered = array_filter($words, function($w) use ($stopwords) {
            return !in_array($w, $stopwords) && mb_strlen($w) > 1; // Bỏ stopword và các từ đơn lẻ 1 ký tự
        });

        return implode(' ', $filtered);
    }

    /**
     * So sánh câu với TỪNG CHUNK nhỏ của nội dung trang (chunk-based matching).
     * Đây là kỹ thuật Turnitin sử dụng — tránh tình trạng vector bị pha loãng
     * khi so sánh 1 câu ngắn với toàn bộ 8000 ký tự của trang.
     */
    public function findBestChunkSimilarity(string $sentence, string $pageContent, int $chunkSize = 250, int $step = 80): float
    {
        if (mb_strlen($pageContent) < 20) return 0;

        $words = preg_split('/\s+/u', $pageContent, -1, PREG_SPLIT_NO_EMPTY);
        $totalWords = count($words);
        $chunkWordSize = 40; // ~250 chars ≈ 40 từ
        $stepSize = 12;      // bước trượt 12 từ

        $maxSim = 0;

        for ($i = 0; $i < $totalWords - $chunkWordSize; $i += $stepSize) {
            $chunk = implode(' ', array_slice($words, $i, $chunkWordSize));
            $cosineSim = $this->calculateSimilarity($sentence, $chunk);
            $ngramSim = $this->nGramSimilarity($sentence, $chunk, 3); // n=3 for short chunks
            $sim = max($cosineSim, $ngramSim);
            if ($sim > $maxSim) {
                $maxSim = $sim;
            }
            // Early exit nếu đã tìm được match cao
            if ($maxSim > 0.85) break;
        }

        return $maxSim;
    }

    /**
     * Cosine Similarity
     */
    public function calculateSimilarity(string $text1, string $text2): float
    {
        $clean1 = $this->removeStopwords($text1);
        $clean2 = $this->removeStopwords($text2);

        $words1 = preg_split('/\s+/u', $clean1, -1, PREG_SPLIT_NO_EMPTY);
        $words2 = preg_split('/\s+/u', $clean2, -1, PREG_SPLIT_NO_EMPTY);

        if (empty($words1) || empty($words2)) return 0;

        $allWords = array_unique(array_merge($words1, $words2));

        $vector1 = [];
        $vector2 = [];

        $counts1 = array_count_values($words1);
        $counts2 = array_count_values($words2);

        foreach ($allWords as $word) {
            $vector1[] = $counts1[$word] ?? 0;
            $vector2[] = $counts2[$word] ?? 0;
        }

        $dotProduct = 0;
        foreach ($vector1 as $i => $val) {
            $dotProduct += $val * $vector2[$i];
        }

        $magnitude1 = sqrt(array_reduce($vector1, fn($sum, $val) => $sum + ($val * $val), 0));
        $magnitude2 = sqrt(array_reduce($vector2, fn($sum, $val) => $sum + ($val * $val), 0));

        if ($magnitude1 == 0 || $magnitude2 == 0) {
            return 0;
        }

        return $dotProduct / ($magnitude1 * $magnitude2);
    }

    /**
     * N-Gram Similarity
     */
    public function nGramSimilarity(string $text1, string $text2, int $n = 5): float
    {
        $createNGrams = function ($text) use ($n) {
            // Loại bỏ dấu câu và stopwords
            $text = preg_replace('/[^\p{L}\p{N}\s]/u', '', mb_strtolower($text));
            $text = $this->removeStopwords($text);
            
            $words = preg_split('/\s+/u', $text, -1, PREG_SPLIT_NO_EMPTY);
            
            $ngrams = [];
            $count = count($words);
            for ($i = 0; $i <= $count - $n; $i++) {
                $ngrams[] = implode(' ', array_slice($words, $i, $n));
            }
            return array_unique($ngrams);
        };

        $ngrams1 = $createNGrams($text1);
        $ngrams2 = $createNGrams($text2);

        $size1 = count($ngrams1);
        $size2 = count($ngrams2);

        if ($size1 == 0 || $size2 == 0) return 0;

        $matches = count(array_intersect($ngrams1, $ngrams2));

        return $matches / max($size1, $size2);
    }

    /**
     * Tìm kiếm nội bộ trong DB các bài viết của ENews để tìm các câu văn trùng khớp.
     * Sử dụng LIKE với các từ khóa dài/hiếm.
     */
    public function searchInternal(string $sentence): array
    {
        $urls = [];
        
        // 1. Phân tách và lấy các từ có độ dài trên 4 ký tự (bỏ qua hư từ "và", "là", "thì", "mà", "của")
        $words = preg_split('/[\s,\.\!\?]+/', mb_strtolower($sentence));
        $longWords = array_filter($words, function($w) {
            return mb_strlen($w) >= 5;
        });

        // Nếu không có từ dài, lấy luôn những từ vừa
        if (empty($longWords)) {
            $longWords = array_filter($words, function($w) {
                return mb_strlen($w) >= 3;
            });
        }

        // Lấy 3 từ khoá dài nhất để search
        usort($longWords, function($a, $b) {
            return mb_strlen($b) - mb_strlen($a);
        });
        $keywords = array_slice($longWords, 0, 3);

        if (empty($keywords)) {
            return []; // Câu văn toàn số hoặc ký tự vô nghĩa
        }

        // 2. Query cơ sở dữ liệu `posts` xem có bài nào chứa các cụm này không
        $query = \App\Models\Post::where('status', 'published')
            ->select('slug', 'content', 'title', 'id');
            
        // Tìm kiếm kết hợp: Các bài viết phải chứa ít nhất 1-2 từ khoá khó
        $query->where(function($q) use ($keywords) {
            foreach ($keywords as $kw) {
                $q->orWhere('content', 'like', "%{$kw}%");
            }
        });

        // Giới hạn max 20 bài viết nội bộ có chứa các từ hiếm
        $posts = $query->limit(20)->get();

        foreach ($posts as $post) {
            $strippedContent = strip_tags($post->content);
            $strippedContent = html_entity_decode($strippedContent, ENT_QUOTES | ENT_HTML5, 'UTF-8');
            
            // Xoá khoảng trắng thừa
            $strippedContent = preg_replace('/\s+/u', ' ', $strippedContent);

            // Dùng chunk-based để tránh pha loãng vector khi so với toàn bài nội bộ
            $similarity = $this->findBestChunkSimilarity($sentence, $strippedContent);

            if ($similarity > 0) { // Ghi nhận mọi tỷ lệ trùng lặp
                $urls[] = [
                    'url' => route('post.show', $post->slug) . '?internal=true', // Đánh dấu URL nội bộ
                    'title' => $post->title,
                    'similarity' => ceil($similarity * 100),
                    'is_internal' => true
                ];
            }
        }

        return $urls;
    }
}
