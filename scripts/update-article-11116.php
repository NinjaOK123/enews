<?php
/**
 * update-article-11116.php
 * Đọc file article_11116.json (từ fetch-article-11116.cjs) 
 * và update content vào Laravel DB cho bài có Joomla article_id = 11116
 */

$jsonFile = __DIR__ . '/article_11116.json';
if (!file_exists($jsonFile)) {
    die("❌ Không tìm thấy file: $jsonFile\nChạy fetch-article-11116.cjs trước!\n");
}

$data = json_decode(file_get_contents($jsonFile), true);
if (!$data) {
    die("❌ File JSON không hợp lệ!\n");
}

echo "📄 Title: {$data['title']}\n";
echo "📝 Content length: " . strlen($data['content']) . " chars\n";

// Kết nối MySQL
$pdo = new PDO(
    'mysql:host=127.0.0.1;dbname=enews;charset=utf8mb4',
    'root',
    'vertrigo',
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
);

// Tìm bài theo joomla_article_id hoặc slug
$slug = 'tham-dinh-de-an-mo-nganh-vat-ly-ly-thuyet-va-vat-ly-toan-trinh-do-thac-si';

$stmt = $pdo->prepare("SELECT id, title FROM posts WHERE slug = ? LIMIT 1");
$stmt->execute([$slug]);
$post = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$post) {
    // Tìm gần đúng theo tên
    $stmt = $pdo->prepare("SELECT id, title FROM posts WHERE title LIKE '%Thẩm định Đề án mở ngành Vật lý%' LIMIT 3");
    $stmt->execute();
    $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if ($posts) {
        echo "\n🔎 Tìm thấy bài gần đúng:\n";
        foreach ($posts as $p) {
            echo "  ID={$p['id']} | {$p['title']}\n";
        }
        echo "\n❓ Nhập ID bài cần update: ";
        $id = trim(fgets(STDIN));
        $stmt2 = $pdo->prepare("SELECT id, title FROM posts WHERE id = ?");
        $stmt2->execute([$id]);
        $post = $stmt2->fetch(PDO::FETCH_ASSOC);
    } else {
        die("❌ Không tìm thấy bài viết trong DB!\n");
    }
}

echo "\n✅ Tìm thấy bài: ID={$post['id']} — {$post['title']}\n";

// Clean nội dung Joomla (bỏ các phần nav, sidebar)
$content = $data['content'];

// Update content trong DB
$update = $pdo->prepare("UPDATE posts SET content = ?, updated_at = NOW() WHERE id = ?");
$update->execute([$content, $post['id']]);

echo "💾 Đã update content cho bài ID={$post['id']}!\n";
echo "✅ Xong! Vào admin để kiểm tra lại.\n";
