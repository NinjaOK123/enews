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
            return array_slice(array_unique($urls), 0, 5); // Giới hạn 5 URL — đủ chính xác, đỡ nhanh
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
            ->timeout(4) // timeout 4s: đủ để tải trang nhanh, không chờ trang chậm vô ích
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
     * Tìm kiếm nội bộ siêu nhanh bằng Inverted Index + Winnowing Fingerprints
     */
    public function searchInternal(string $sentence): array
    {
        $urls = [];
        $winnowingService = new \App\Services\Plagiarism\WinnowingService();
        
        // 1. Tạo fingerprints cho câu truy vấn
        $queryFingerprints = $winnowingService->getFingerprints($sentence);
        
        if (empty($queryFingerprints)) {
            return [];
        }

        // Lấy danh sách các hash values (đảm bảo unique để tính coverage chính xác)
        $queryHashes = array_unique(array_values($queryFingerprints));
        $querySize = count($queryHashes);

        if ($querySize === 0) {
            return [];
        }

        // 2. Tra cứu Inverted Index: Lấy ra các tài liệu chứa nhiều mã băm trùng nhất
        // Dùng COUNT(DISTINCT f.hash_value) để tránh bị đếm lặp nếu document có nhiều câu trùng hash
        $candidates = \Illuminate\Support\Facades\DB::table('plagiarism_fingerprints as f')
            ->join('plagiarism_documents as d', 'f.document_id', '=', 'd.id')
            ->whereIn('f.hash_value', $queryHashes)
            ->select('d.id', 'd.post_id', 'd.title', 'd.external_url', 'd.total_fingerprints', \Illuminate\Support\Facades\DB::raw('COUNT(DISTINCT f.hash_value) as match_count'))
            ->groupBy('d.id', 'd.post_id', 'd.title', 'd.external_url', 'd.total_fingerprints')
            ->orderByDesc('match_count')
            ->limit(5)
            ->get();

        foreach ($candidates as $doc) {
            // Tính % trùng lặp dựa trên lượng vân tay trùng khớp chia cho tổng vân tay của câu truy vấn
            // Dùng min(1.0, ...) để khóa cứng tối đa 100%
            $coverage = min(1.0, $doc->match_count / $querySize);

            if ($coverage > 0.05) { // Chỉ lấy những bài có khả năng copy trên 5%
                $url = $doc->post_id 
                        ? route('post.show', \App\Models\Post::find($doc->post_id)?->slug ?? '') . '?internal=true'
                        : $doc->external_url;

                $urls[] = [
                    'url' => $url,
                    'title' => $doc->title,
                    'similarity' => (int) ceil($coverage * 100),
                    'is_internal' => !empty($doc->post_id)
                ];
            }
        }

        return $urls;
    }

    /**
     * Băm và lưu Fingerprint của một bài Post vào cơ sở dữ liệu
     */
    public function indexDocument(int $postId, string $title, string $content)
    {
        $winnowingService = new \App\Services\Plagiarism\WinnowingService();
        $fingerprints = $winnowingService->getFingerprints($content);
        
        if (empty($fingerprints)) return;

        \Illuminate\Support\Facades\DB::beginTransaction();
        try {
            // Delete old index if exists
            $oldDoc = \Illuminate\Support\Facades\DB::table('plagiarism_documents')->where('post_id', $postId)->first();
            if ($oldDoc) {
                // Cascading will delete related fingerprints
                \Illuminate\Support\Facades\DB::table('plagiarism_documents')->where('id', $oldDoc->id)->delete();
            }

            // Insert new document
            $docId = \Illuminate\Support\Facades\DB::table('plagiarism_documents')->insertGetId([
                'post_id' => $postId,
                'title' => \Illuminate\Support\Str::limit($title, 250),
                'total_fingerprints' => count($fingerprints),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Prepare bulk insert for fingerprints
            $insertData = [];
            foreach ($fingerprints as $position => $hash) {
                $insertData[] = [
                    'document_id' => $docId,
                    'hash_value' => $hash,
                    'position' => $position
                ];
            }

            // Insert chunk by chunk (1000 items each) to prevent SQL string length errors
            foreach (array_chunk($insertData, 1000) as $chunk) {
                \Illuminate\Support\Facades\DB::table('plagiarism_fingerprints')->insert($chunk);
            }

            \Illuminate\Support\Facades\DB::commit();
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            Log::error("Failed to index document {$postId}: " . $e->getMessage());
        }
    }
}
