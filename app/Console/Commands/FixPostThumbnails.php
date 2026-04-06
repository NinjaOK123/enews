<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class FixPostThumbnails extends Command
{
    protected $signature = 'posts:fix-thumbnails
                            {--dry-run : Chạy thử, không lưu vào DB}
                            {--limit=0 : Giới hạn số bài xử lý (0 = tất cả)}
                            {--force : Cập nhật kể cả bài đã có thumbnail nhưng file không tồn tại}';

    protected $description = 'Quét và vá ảnh đại diện cho từng bài viết từ kho local public/images';

    // Thư mục gốc ảnh Joomla đã copy về
    private string $publicPath;

    // Thống kê
    private int $ok       = 0; // Ảnh hiện tại hợp lệ
    private int $fixed    = 0; // Đã vá từ content
    private int $missing  = 0; // Không tìm được ảnh nào
    private int $skipped  = 0; // Bỏ qua

    public function handle(): int
    {
        $this->publicPath = public_path();
        $isDryRun         = $this->option('dry-run');
        $limit            = (int) $this->option('limit');
        $force            = $this->option('force');

        $this->info('🔍 Bắt đầu kiểm tra ảnh đại diện bài viết...');
        $isDryRun && $this->warn('⚠️  DRY-RUN — chỉ xem, không lưu.');

        $query = DB::table('posts')->select('id', 'title', 'thumbnail', 'content');

        if ($limit > 0) {
            $query->limit($limit);
            $this->info("⚙️  Giới hạn: {$limit} bài");
        }

        $total = $query->count();
        $this->info("📊 Tổng số bài: {$total}");

        $bar = $this->output->createProgressBar($total);
        $bar->start();

        // Xử lý từng batch 200 bài để tránh hết RAM
        $query->orderBy('id')->chunk(200, function ($posts) use ($isDryRun, $force, $bar) {
            foreach ($posts as $post) {
                $this->processPost($post, $isDryRun, $force);
                $bar->advance();
            }
        });

        $bar->finish();
        $this->newLine(2);

        // Kết quả
        $this->info("✅ Ảnh hợp lệ    : {$this->ok}");
        $this->info("🔧 Đã vá từ bài  : {$this->fixed}");
        $this->warn("❓ Không có ảnh  : {$this->missing}");
        $this->line("⏭️  Bỏ qua        : {$this->skipped}");

        return 0;
    }

    private function processPost(object $post, bool $isDryRun, bool $force): void
    {
        $currentThumb = $post->thumbnail ?? '';

        // ── Bước 1: Kiểm tra thumbnail hiện tại có hợp lệ không ──────────────
        if (!empty($currentThumb) && !$force) {
            if ($this->fileExists($currentThumb)) {
                // Thumbnail OK, không cần làm gì
                $this->ok++;
                return;
            }
            // Thumbnail có nhưng file không tồn tại → cần tìm lại
        }

        // ── Bước 2: Đào ảnh đầu tiên từ content ─────────────────────────────
        $newThumb = $this->extractFirstImageFromContent($post->content ?? '');

        if (empty($newThumb)) {
            // Không tìm được ảnh nào trong bài
            $this->missing++;
            return;
        }

        // ── Bước 3: Chuẩn hóa đường dẫn ─────────────────────────────────────
        $normalizedThumb = $this->normalizePath($newThumb);

        // Kiểm tra file có thật sự tồn tại trong public/ không
        if (!$this->fileExists($normalizedThumb)) {
            $this->missing++;
            return;
        }

        // ── Bước 4: Lưu vào DB ───────────────────────────────────────────────
        if (!$isDryRun) {
            DB::table('posts')->where('id', $post->id)->update([
                'thumbnail' => $normalizedThumb,
            ]);
        } else {
            // Dry-run: in ra để xem
            $this->newLine();
            $this->line("  ID #{$post->id}: {$currentThumb} → {$normalizedThumb}");
        }

        $this->fixed++;
    }

    /**
     * Lấy src ảnh đầu tiên trong HTML content
     */
    private function extractFirstImageFromContent(string $content): string
    {
        if (empty($content)) return '';

        // Thay thế URL Joomla cũ
        $content = str_replace(
            ['http://enews.agu.edu.vn/', 'https://enews.agu.edu.vn/'],
            '/',
            $content
        );

        // Tìm thẻ <img src="...">
        if (preg_match('/<img[^>]+src=["\']([^"\']+)["\']/i', $content, $matches)) {
            return $matches[1];
        }

        return '';
    }

    /**
     * Chuẩn hóa path: bỏ domain, bỏ dấu / đầu, chỉ giữ path local
     */
    private function normalizePath(string $path): string
    {
        // Nếu là URL đầy đủ, lấy phần path
        if (str_starts_with($path, 'http')) {
            $parsed = parse_url($path, PHP_URL_PATH);
            $path   = ltrim($parsed ?? '', '/');
        } else {
            $path = ltrim($path, '/');
        }

        return $path;
    }

    /**
     * Kiểm tra file có tồn tại dưới public/ không
     */
    private function fileExists(string $path): bool
    {
        // URL đầy đủ → bỏ qua kiểm tra local
        if (str_starts_with($path, 'http')) {
            return true;
        }

        // Ảnh trong storage/
        if (str_starts_with($path, 'joomla-images/') || str_starts_with($path, 'posts/')) {
            return \Storage::disk('public')->exists($path);
        }

        // Ảnh trong public/images/
        $fullPath = $this->publicPath . DIRECTORY_SEPARATOR . ltrim($path, '/\\');
        return file_exists($fullPath);
    }
}
