<?php
require dirname(__DIR__) . '/vendor/autoload.php';
$app = require_once dirname(__DIR__) . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

header('Content-Type: text/plain; charset=utf-8');

$slug = ltrim(str_replace('http://enews.com/bai-viet/', '', 'http://enews.com/bai-viet/ha-p-qua-hoa-gia-y-23228'), '/');
// Tách lấy ID ở cuối slug (nếu có - format cũ là tiêu-đề-id)
preg_match('/-(\d+)$/', $slug, $matches);
$id = $matches[1] ?? null;

$post = \DB::table('posts')->where('id', $id)->orWhere('slug', $slug)->first();

if (!$post) {
    echo "❌ Không tìm thấy bài viết với ID $id hay slug $slug\n";
    exit;
}

echo "=== THÔNG TIN BÀI VIẾT ===\n";
echo "ID: {$post->id}\n";
echo "Tiêu đề: {$post->title}\n";
echo "Thumbnail (nguyên gốc DB): " . ($post->thumbnail ?? 'NULL') . "\n";

// Phân tích content để xem ảnh nằm ở đâu
$content = $post->content ?? '';
echo "\n=== MÃ HTML CỦA THẺ <img> TRONG NỘI DUNG ===\n";
preg_match_all('/<img[^>]+>/i', $content, $imgTags);

if (empty($imgTags[0])) {
     echo "❌ Không tìm thấy thẻ <img> nào trong nội dung.\n";
} else {
     foreach ($imgTags[0] as $tag) {
         echo "$tag\n";
         // Lấy src
         if (preg_match('/src=["\']([^"\']+)["\']/i', $tag, $srcMatch)) {
             $src = $srcMatch[1];
             echo "  -> src gốc: $src\n";
             
             // Giả lập Accessor đang chạy
             $srcReplaced = str_replace(['http://enews.agu.edu.vn/', 'https://enews.agu.edu.vn/'], '/', $src);
             $srcClean = ltrim($srcReplaced, '/');
             echo "  -> url sau khi Accessor xử lý: $srcClean\n";
             
             $fullPath = dirname(__DIR__) . '/public/' . $srcClean;
             echo "  -> File tại: $fullPath\n";
             echo "  -> TỒN TẠI KHÔNG: " . (file_exists($fullPath) ? '✅ CÓ' : '❌ KHÔNG') . "\n";
         }
     }
}
