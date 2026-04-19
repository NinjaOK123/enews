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
        
        $urls = $this->plagiarismService->searchWeb($sentence);
        
        $maxSimilarity = 0;
        $matchedSources = [];

        foreach ($urls as $url) {
            $content = $this->plagiarismService->fetchPageContent($url);
            
            if ($content && mb_strlen($content) > 100) {
                $cosineSim = $this->plagiarismService->calculateSimilarity($sentence, $content);
                $ngramSim = $this->plagiarismService->nGramSimilarity($sentence, $content, 5);
                
                $similarity = max($cosineSim, $ngramSim);

                if ($similarity > $maxSimilarity) {
                    $maxSimilarity = $similarity;
                }

                if ($similarity > 0.25) { // Chỉ quan tâm nếu trùng > 25%
                    $matchedSources[] = [
                        'url' => $url,
                        'similarity' => round($similarity * 100)
                    ];
                }
            }
        }

        // Sắp xếp nguồn trùng khớp nhiều nhất lên đầu
        usort($matchedSources, fn($a, $b) => $b['similarity'] <=> $a['similarity']);

        return response()->json([
            'sentence' => $sentence,
            'similarity' => round($maxSimilarity * 100),
            'sources' => $matchedSources,
            'isPlagiarized' => $maxSimilarity > 0.25 // Cảnh báo Đạo văn nếu trùng > 25%
        ]);
    }
}
