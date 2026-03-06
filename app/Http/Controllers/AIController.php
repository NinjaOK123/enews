<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AIController extends Controller
{
    public function generatePost(Request $request)
    {
        $request->validate([
            'prompt' => 'required|string|max:1000'
        ]);

        $prompt = $request->input('prompt');

        // Placeholder giả lập AI
        $content = "<h2>Nội dung sinh bởi AI</h2>";
        $content .= "<p>Bài viết nháp dành cho chủ đề: <strong>{$prompt}</strong>.</p>";
        $content .= "<p>Bạn có thể sử dụng nội dung cơ bản này để triển khai sâu hơn.</p>";

        return response()->json([
            'content' => $content
        ]);
    }
}
