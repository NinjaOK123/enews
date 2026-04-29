<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Post;
use App\Services\PlagiarismService;

class BuildPlagiarismCorpus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'plagiarism:build-corpus {--limit=0 : Số bài giới hạn để build, 0 là build toàn bộ}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Quét toàn bộ bài viết đã đăng và băm thành dấu vân tay (Winnowing Fingerprints) nạp vào Inverted Index để phục vụ check đạo văn offline';

    /**
     * Execute the console command.
     */
    public function handle(PlagiarismService $plagiarismService)
    {
        $limit = (int) $this->option('limit');
        
        $query = Post::where('status', 'published')->orderBy('id', 'desc');
        
        if ($limit > 0) {
            $query->limit($limit);
            $this->info("Bắt đầu build corpus cho $limit bài viết mới nhất...");
        } else {
            $this->info("Bắt đầu build corpus cho TOÀN BỘ bài viết...");
        }

        $total = $query->count();
        $bar = $this->output->createProgressBar($total);
        $bar->start();

        // Xử lý từng lô 100 bài để tránh tràn RAM
        $query->chunk(100, function ($posts) use ($plagiarismService, $bar) {
            foreach ($posts as $post) {
                // Lọc bỏ tags HTML khỏi nội dung bài
                $plainContent = strip_tags($post->content);
                
                if (mb_strlen($plainContent) > 50) {
                    $plagiarismService->indexDocument($post->id, $post->title, $plainContent);
                }
                $bar->advance();
            }
        });

        $bar->finish();
        $this->newLine(2);
        $this->info('Đã hoàn tất xây dựng kho dữ liệu Fingerprint!');
    }
}
