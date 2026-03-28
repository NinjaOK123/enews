<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use App\Services\AIWriterService;

class AIController extends Controller
{
    /**
     * Sinh nội dung bài viết bằng AI (Server-side) xoay vòng nhiều Provider: Groq > Gemini > DeepSeek.
     * Quản lý rate limit mỗi user tối đa 5 lần/phút.
     */
    public function generatePost(Request $request, AIWriterService $service)
    {
        $request->validate([
            'prompt' => 'required|string|max:2000'
        ]);

        // Rate limit: mỗi user tối đa 5 lần/phút
        $userId = auth()->id() ?? 'guest';
        $key = 'ai-generate:' . $userId;

        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            return response()->json([
                'error' => "Bạn đã sử dụng quá nhiều lần. Vui lòng chờ {$seconds} giây rồi thử lại."
            ], 429);
        }

        RateLimiter::hit($key, 60);

        try {
            $topic = $request->input('prompt');
            $result = $service->writeArticle($topic);

            $content = $result['content'];
            $providerMatch = current(explode(' ', $result['provider'])); // VD: "Groq" từ "Groq (Llama-3.3)"

            // Nếu AI trả về markdown thay vì HTML, convert cơ bản
            if (!str_contains($content, '<p>') && !str_contains($content, '<h2>')) {
                $content = $this->markdownToHtml($content);
            }

            return response()->json([
                'title' => $result['title'] ?? '',
                'content' => $content,
                'cover_image_prompt' => $result['cover_image_prompt'] ?? '',
                'provider' => $providerMatch
            ]);

        } catch (\Exception $e) {
            Log::error('AI Generation Exception', [
                'message' => $e->getMessage()
            ]);

            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Convert markdown cơ bản sang HTML
     */
    private function markdownToHtml(string $text): string
    {
        // Headers
        $text = preg_replace('/^### (.+)$/m', '<h3>$1</h3>', $text);
        $text = preg_replace('/^## (.+)$/m', '<h2>$1</h2>', $text);
        $text = preg_replace('/^# (.+)$/m', '<h2>$1</h2>', $text);

        // Bold & Italic
        $text = preg_replace('/\*\*(.+?)\*\*/', '<strong>$1</strong>', $text);
        $text = preg_replace('/\*(.+?)\*/', '<em>$1</em>', $text);

        // Paragraphs: split by double newlines
        $paragraphs = preg_split('/\n\s*\n/', $text);
        $html = '';
        foreach ($paragraphs as $para) {
            $para = trim($para);
            if (empty($para)) continue;
            // Skip if already wrapped in a block tag
            if (preg_match('/^<(h[1-6]|ul|ol|li|blockquote|div|table)/', $para)) {
                $html .= $para;
            } else {
                $html .= '<p>' . nl2br($para) . '</p>';
            }
        }

        return $html;
    }
}
