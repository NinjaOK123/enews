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

        // 1. Serper.dev (Google Search API)
        try {
            $apiKey = env('SERPER_API_KEY');
            if (!empty($apiKey)) {
                $response = Http::withHeaders([
                    'X-API-KEY' => $apiKey,
                    'Content-Type' => 'application/json'
                ])
                ->timeout(5)
                ->post('https://google.serper.dev/search', [
                    'q' => Str::limit($query, 100, ''), // Google might not allow queries too long
                    'gl' => 'vn', // Lấy kết quả tiếng Việt
                    'hl' => 'vi'
                ]);

                if ($response->successful()) {
                    $data = $response->json();
                    if (isset($data['organic']) && is_array($data['organic'])) {
                        foreach ($data['organic'] as $result) {
                            if (isset($result['link'])) {
                                $urls[] = $result['link'];
                            }
                        }
                    }
                }
            } else {
                Log::warning("Serper API Key chưa được cài đặt trong .env");
            }
        } catch (\Exception $e) {
            Log::warning("Serper Search Error: " . $e->getMessage());
        }

        // Lấy top 8 từ Serper
        $urls = array_slice($urls, 0, 8);

        // 2. CrossRef API (for academic/articles)
        try {
            $crossRefUrl = "https://api.crossref.org/works?query={$queryEncoded}&rows=5";
            $response = Http::timeout(5)->get($crossRefUrl);
            
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
        } catch (\Exception $e) {
            Log::warning("CrossRef Search Error: " . $e->getMessage());
        }

        // Loại bỏ trùng lặp và lấy max 10 links
        return array_slice(array_unique($urls), 0, 10);
    }

    /**
     * Tải nội dung text của trang web
     */
    public function fetchPageContent(string $url): string
    {
        try {
            $response = Http::withHeaders([
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

            return mb_substr($text, 0, 5000); // Lấy max 5000 chars

        } catch (\Exception $e) {
            return '';
        }
    }

    /**
     * Cosine Similarity
     */
    public function calculateSimilarity(string $text1, string $text2): float
    {
        $words1 = preg_split('/\s+/u', mb_strtolower($text1));
        $words2 = preg_split('/\s+/u', mb_strtolower($text2));

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
            // Remove punctuation and get words
            $text = preg_replace('/[^\p{L}\p{N}\s]/u', '', mb_strtolower($text));
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

            $cosineSim = $this->calculateSimilarity($sentence, $strippedContent);
            $ngramSim = $this->nGramSimilarity($sentence, $strippedContent, 5);
            $similarity = max($cosineSim, $ngramSim);

            if ($similarity > 0.25) { // Trùng > 25% nội bộ cũng xử lý
                $urls[] = [
                    'url' => route('post.show', $post->slug) . '?internal=true', // Đánh dấu URL nội bộ
                    'title' => $post->title,
                    'similarity' => round($similarity * 100),
                    'is_internal' => true
                ];
            }
        }

        return $urls;
    }
}
