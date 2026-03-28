<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class AIWriterService
{
    protected $providers = [];

    public function __construct()
    {
        // Khởi tạo danh sách provider, chỉ thêm những provider nào có API Key
        // Ưu tiên: groq (vì rất nhanh, free tier khá ổn llama3) -> gemini -> deepseek

        $groqKey = config('services.groq.api_key');
        if ($groqKey) {
            $this->providers['groq'] = [
                'name' => 'Groq (Llama-3.3)',
                'key' => $groqKey,
                'url' => 'https://api.groq.com/openai/v1/chat/completions',
                'model' => 'llama-3.3-70b-versatile',
                'type' => 'openai',
            ];
        }

        $geminiKey = config('services.gemini.api_key');
        if ($geminiKey) {
            $this->providers['gemini'] = [
                'name' => 'Google Gemini (1.5 Flash)',
                'key' => $geminiKey,
                'url' => 'https://generativelanguage.googleapis.com/v1/models/gemini-1.5-flash:generateContent',
                'model' => 'gemini-1.5-flash',
                'type' => 'gemini',
            ];
        }

        $deepseekKey = config('services.deepseek.api_key');
        if ($deepseekKey) {
            $this->providers['deepseek'] = [
                'name' => 'DeepSeek Chat',
                'key' => $deepseekKey,
                'url' => 'https://api.deepseek.com/chat/completions',
                'model' => 'deepseek-chat',
                'type' => 'openai',
            ];
        }
    }

    public function writeArticle(string $topic)
    {
        if (empty($this->providers)) {
            throw new Exception('Chưa cấu hình bất kỳ AI Provider nào (Thiếu API Key). Vui lòng liên hệ Quản trị viên.');
        }

        $systemPrompt = "Bạn là một nhà báo và biên tập viên chuyên nghiệp của trang tin điện tử E-News trực thuộc Trường Đại học An Giang (VNUHCM). "
            . "Nhiệm vụ của bạn là viết một bài báo truyền thông sắc bén, hấp dẫn, mạch lạc bằng Tiếng Việt dựa trên chủ đề yêu cầu. "
            . "BẮT BUỘC trả về kết quả dưới dạng JSON hợp lệ. BẮT BUỘC escape các ký tự đặc biệt như ngoặc kép (\"), xuống dòng (thay vì xuống dòng thực tế, hãy dùng \\n) để tránh lỗi parse JSON. Cấu trúc JSON bắt buộc: "
            . '{ "title": "[Tiêu đề bài viết khoảng 10-15 chữ]", "content": "[Nội dung bài viết dùng thẻ <h2>, <p>, <strong>... ĐẶC BIỆT: Hãy chèn ít nhất 1 thẻ <img src=\"https://image.pollinations.ai/prompt/[từ khóa tiếng anh mô tả ảnh sinh động]?width=800&height=450&nologo=true\" alt=\"Mô tả ảnh\"> vào đầu hoặc giữa bài viết để làm ảnh minh họa. KHÔNG dùng <html><body>]", "cover_image_prompt": "[1-2 từ khóa Tiếng Anh ngắn gọn mô tả ảnh bìa bài viết, vd: university campus]" }';

        $userPrompt = "Chủ đề: {$topic}";

        $lastError = '';

        foreach ($this->providers as $id => $provider) {
            try {
                if ($provider['type'] === 'gemini') {
                    $content = $this->callGemini($provider, $systemPrompt, $userPrompt);
                } else {
                    $content = $this->callOpenAICompatible($provider, $systemPrompt, $userPrompt);
                }

                $jsonStr = trim($content);
                $start = strpos($jsonStr, '{');
                $end = strrpos($jsonStr, '}');
                
                if ($start !== false && $end !== false) {
                    $jsonStr = substr($jsonStr, $start, $end - $start + 1);
                }

                $data = json_decode($jsonStr, true);

                if (!$data || !isset($data['content'])) {
                    throw new Exception("Provider {$provider['name']} trả về định dạng không đúng. (Str: " . substr($jsonStr, 0, 50) . "...)");
                }

                return [
                    'title' => $data['title'] ?? '',
                    'content' => $data['content'],
                    'cover_image_prompt' => $data['cover_image_prompt'] ?? '',
                    'provider' => $provider['name']
                ];

            } catch (Exception $e) {
                $msg = strtolower($e->getMessage());
                // Fallback nếu gặp lỗi về Rate limit, Quota, Overloaded
                if (
                    str_contains($msg, 'quota') || 
                    str_contains($msg, 'rate limit') || 
                    str_contains($msg, '429') ||
                    str_contains($msg, 'limit exhausted') ||
                    str_contains($msg, 'exceeded') ||
                    str_contains($msg, 'overloaded') ||
                    str_contains($msg, '503')
                ) {
                    Log::warning("[AI Writer Fallback] Provider {$id} failed due to quota/rate limit.", ['error' => $e->getMessage()]);
                    $lastError = "{$provider['name']} ({$e->getMessage()})";
                    continue; // Chuyển sang provider tiếp theo
                }
                
                // Cũng fallback nếu có lỗi kết nối/timeout (bắt buộc hệ thống phải ổn định)
                Log::error("[AI Writer Error] Provider {$id} throw error.", ['error' => $e->getMessage()]);
                $lastError = "{$provider['name']} ({$e->getMessage()})";
                continue;
            }
        }

        throw new Exception("Tất cả AI model đều đang quá tải hoặc gặp sự cố. Vui lòng thử lại sau. (Chi tiết: {$lastError})");
    }

    private function callGemini($provider, $system, $user)
    {
        $response = Http::timeout(60)->post(
            "{$provider['url']}?key={$provider['key']}",
            [
                'contents' => [
                    [
                        'role' => 'user',
                        'parts' => [
                            ['text' => $system . "\n\n--- Chủ đề yêu cầu ---\n" . $user]
                        ]
                    ]
                ],
                'generationConfig' => [
                    'temperature' => 0.8,
                    'maxOutputTokens' => 4096,
                    'topP' => 0.95,
                ]
            ]
        );

        if ($response->status() === 429) {
            throw new Exception('429 Rate Limit/Quota Exceeded');
        }

        if ($response->failed()) {
            throw new Exception("HTTP Error: " . $response->status() . " " . $response->body());
        }

        $data = $response->json();
        return $data['candidates'][0]['content']['parts'][0]['text'] ?? null;
    }

    private function callOpenAICompatible($provider, $system, $user)
    {
        $payload = [
            'model' => $provider['model'],
            'messages' => [
                ['role' => 'system', 'content' => $system],
                ['role' => 'user', 'content' => $user],
            ],
            'temperature' => 0.8,
            'max_tokens' => 4096,
        ];
        
        if (str_contains(strtolower($provider['name']), 'groq')) {
            $payload['response_format'] = ['type' => 'json_object'];
        }

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $provider['key'],
        ])->timeout(60)->post($provider['url'], $payload);

        if ($response->status() === 429) {
            throw new Exception('429 Rate Limit/Quota Exceeded');
        }

        if ($response->failed()) {
            throw new Exception("HTTP Error: " . $response->status() . " " . $response->body());
        }

        $data = $response->json();
        return $data['choices'][0]['message']['content'] ?? null;
    }
}
