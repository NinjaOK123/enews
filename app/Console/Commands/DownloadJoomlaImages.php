<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class DownloadJoomlaImages extends Command
{
    protected $signature   = 'joomla:download-images
                                {--limit=0 : Giới hạn số ảnh tải (0 = tất cả)}
                                {--dry-run : Chạy thử, không lưu thực sự}
                                {--cookie= : Cookie sl-session từ trình duyệt sau khi pass WAF}';
    protected $description = 'Tải ảnh từ enews.agu.edu.vn về storage local và cập nhật DB';

    private string $joomlaBase = 'https://enews.agu.edu.vn';
    private int $successCount  = 0;
    private int $failCount     = 0;
    private int $skipCount     = 0;
    private string $wafCookie  = '';

    public function handle(): int
    {
        $isDryRun        = $this->option('dry-run');
        $limit           = (int) $this->option('limit');
        $this->wafCookie = (string) $this->option('cookie');

        $this->info("🔄 Bắt đầu download ảnh từ Joomla...");
        $isDryRun && $this->warn("⚠️  Chế độ DRY-RUN — không lưu thực sự.");

        // Tạo thư mục lưu ảnh
        Storage::disk('public')->makeDirectory('joomla-images');

        // Lấy các bài có thumbnail là path Joomla (images/...)
        $query = DB::table('posts')
            ->whereNotNull('thumbnail')
            ->where('thumbnail', '!=', '')
            ->where('thumbnail', 'not like', 'http%')
            ->where('thumbnail', 'like', 'images/%');

        $total = $query->count();
        $this->info("📊 Tổng số bài cần tải: {$total}");

        if ($limit > 0) {
            $query = $query->limit($limit);
            $this->info("⚙️  Giới hạn: {$limit} ảnh");
        }

        $posts = $query->select('id', 'thumbnail')->get();
        $bar   = $this->output->createProgressBar($posts->count());
        $bar->start();

        foreach ($posts as $post) {
            $this->processPost($post, $isDryRun);
            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        $this->info("✅ Thành công : {$this->successCount}");
        $this->warn("⏭️  Đã tồn tại : {$this->skipCount}");
        $this->error("❌ Thất bại   : {$this->failCount}");

        return 0;
    }

    private function processPost(object $post, bool $isDryRun): void
    {
        $remotePath  = ltrim($post->thumbnail, '/');
        $remoteUrl   = $this->joomlaBase . '/' . $remotePath;

        // Lưu vào storage/app/public/joomla-images/ (giữ cấu trúc thư mục)
        $localPath   = 'joomla-images/' . basename($remotePath);

        // Skip nếu đã tồn tại
        if (Storage::disk('public')->exists($localPath)) {
            DB::table('posts')->where('id', $post->id)->update(['thumbnail' => $localPath]);
            $this->skipCount++;
            return;
        }

        if ($isDryRun) {
            $this->successCount++;
            return;
        }

        try {
            $ch = curl_init($remoteUrl);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_MAXREDIRS      => 5,
                CURLOPT_TIMEOUT        => 15,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => false,
                CURLOPT_HTTPHEADER     => [
                    'Accept: image/avif,image/webp,image/apng,image/svg+xml,image/*,*/*;q=0.8',
                    'Accept-Language: vi-VN,vi;q=0.9,en-US;q=0.8,en;q=0.7',
                    'Cache-Control: no-cache',
                    'Pragma: no-cache',
                    'Referer: https://enews.agu.edu.vn/',
                    'Sec-Fetch-Dest: image',
                    'Sec-Fetch-Mode: no-cors',
                    'Sec-Fetch-Site: same-origin',
                    ...($this->wafCookie ? ['Cookie: ' . $this->wafCookie] : []),
                ],
                CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36',
                CURLOPT_COOKIEFILE => '',   // enable cookie engine
                CURLOPT_ENCODING   => '',   // accept gzip
            ]);

            $content  = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($content === false || strlen($content) < 100 || $httpCode >= 400) {
                $this->failCount++;
                return;
            }

            // Lưu file
            Storage::disk('public')->put($localPath, $content);

            // Cập nhật DB
            DB::table('posts')->where('id', $post->id)->update(['thumbnail' => $localPath]);

            $this->successCount++;

        } catch (\Throwable $e) {
            $this->failCount++;
        }
    }
}
