<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\PlagiarismService;
use Illuminate\Http\Request;

class PlagiarismController extends Controller
{
    protected $plagiarismService;

    public function __construct(PlagiarismService $plagiarismService)
    {
        $this->plagiarismService = $plagiarismService;
    }

    /**
     * API kiểm tra độ giống nhau cho 1 câu text
     * Request method POST, data: { sentence: "Một đoạn văn bản dài trên 20 chữ..." }
     */
    public function checkSentence(Request $request)
    {
        $request->validate([
            'sentence' => 'required|string|min:20',
        ]);

        $sentence = $request->input('sentence');
        
        $maxSimilarity = 0;
        $matchedSources = [];

        // 1. Quét nội bộ (E-News DB)
        $internalResults = $this->plagiarismService->searchInternal($sentence);
        foreach ($internalResults as $result) {
            $matchedSources[] = $result;
            if ($result['similarity'] > $maxSimilarity) {
                $maxSimilarity = $result['similarity'];
            }
        }

        // 2. Quét mạng (Google Serper) - CHỈ KHI kết quả nội bộ không đủ cao (ví dụ: < 20%)
        if ($maxSimilarity < 20) {
            $urls = $this->plagiarismService->searchWeb($sentence);
            foreach ($urls as $url) {
                $content = $this->plagiarismService->fetchPageContent($url);
                
                if ($content && mb_strlen($content) > 100) {
                    $similarity = $this->plagiarismService->findBestChunkSimilarity($sentence, $content);

                    if ($similarity > 0.05) { 
                        $simPercent = ceil($similarity * 100);
                        $matchedSources[] = [
                            'url' => $url,
                            'title' => 'External Web Source',
                            'similarity' => $simPercent,
                            'is_internal' => false
                        ];
                        
                        if ($simPercent > $maxSimilarity) {
                            $maxSimilarity = $simPercent;
                        }
                    }
                }
            }
        }

        // Sắp xếp nguồn trùng khớp nhiều nhất lên đầu
        usort($matchedSources, fn($a, $b) => $b['similarity'] <=> $a['similarity']);

        return response()->json([
            'sentence' => $sentence,
            'similarity' => (int) $maxSimilarity,
            'sources' => array_slice($matchedSources, 0, 5), // Trả về top 5 nguồn lặp nhiều nhất
            'isPlagiarized' => $maxSimilarity >= 20 // Tầng 3: Báo động vi phạm thật sự
        ]);
    }
}
