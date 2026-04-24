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

    public function writeArticle(string $topic, array $examples = [])
    {
        if (empty($this->providers)) {
            throw new Exception('Chưa cấu hình bất kỳ AI Provider nào (Thiếu API Key). Vui lòng liên hệ Quản trị viên.');
        }

        $exampleContext = "";
        if (!empty($examples)) {
            $exampleContext = "\n--- DƯỚI ĐÂY LÀ CÁC BÀI VIẾT MẪU CỦA TOÀ SOẠN ĐỂ BẠN HỌC TẬP VĂN PHONG ---\n";
            foreach ($examples as $index => $ex) {
                $exampleContext .= "MẪU " . ($index + 1) . ":\nTiêu đề: " . ($ex['title'] ?? '') . "\nNội dung: " . mb_substr(strip_tags($ex['content'] ?? ''), 0, 500) . "...\n\n";
            }
        }

        // Tự động nhận diện Task
        $isOutline = mb_stripos($topic, 'sườn bài') !== false || mb_stripos($topic, 'outline') !== false;
        $isSummary = mb_stripos($topic, 'tóm tắt') !== false || mb_stripos($topic, 'sapo') !== false;
        $isTitleOnly = mb_stripos($topic, 'tiêu đề') !== false && mb_stripos($topic, 'gợi ý') !== false;

        $taskInstruction = "Viết bài viết đầy đủ.";
        if ($isOutline) $taskInstruction = "Chỉ tập trung lên sườn bài (các thẻ h2, h3) kèm mô tả ngắn cho mỗi mục.";
        if ($isSummary) $taskInstruction = "Chỉ viết 1 đoạn văn tóm tắt cực kỳ hấp dẫn.";
        if ($isTitleOnly) $taskInstruction = "Chỉ gợi ý tiêu đề sắc bén nhất.";

        $systemPrompt = "Bạn là một nhà biên tập viên chuyên nghiệp của E-News (Đại học An Giang). Nhiệm vụ: {$taskInstruction}. "
            . "HÃY HỌC TẬP VĂN PHONG, CÁCH ĐẶT TIÊU ĐỀ VÀ CÁCH DÙNG TỪ TỪ CÁC BÀI MẪU DƯỚI ĐÂY (NẾU CÓ):"
            . $exampleContext
            . "\n\nBẮT BUỘC trả về kết quả dưới dạng JSON hợp lệ. Cấu trúc JSON: "
            . '{ "title": "[Gợi ý tiêu đề cho bài viết]", "content": "[Nội dung chính hoặc sườn bài/tóm tắt bằng HTML. Nếu là bài viết đầy đủ, hãy chèn ít nhất 1 ảnh minh họa từ Pollinations AI vào bài]", "cover_image_prompt": "[1-2 keywords mô tả ảnh bìa]" }';

        $userPrompt = "Yêu cầu chi tiết: {$topic}";

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
        $response = Http::withOptions(['verify' => false])->timeout(60)->post(
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

        $response = Http::withOptions(['verify' => false])->withHeaders([
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
