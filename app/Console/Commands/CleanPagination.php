<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Post;

class CleanPagination extends Command
{
    protected $signature = 'enews:clean-pagination';
    protected $description = 'Clean up Joomla pagination HTML from post content';

    public function handle()
    {
        $this->info("Bắt đầu dọn dẹp rác phân trang...");
        
        $updated = 0;
        
        foreach (Post::query()->cursor() as $post) {
            $originalContent = $post->getRawOriginal('content');
            
            if (empty(trim($originalContent))) continue;

            // Dùng DOMDocument để xóa an toàn hơn là regex
            $dom = new \DOMDocument();
            // Suppress warnings due to malformed HTML
            libxml_use_internal_errors(true);
            // Thêm thẻ meta để đảm bảo UTF-8
            $dom->loadHTML('<?xml encoding="utf-8" ?>' . $originalContent, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
            libxml_clear_errors();

            $xpath = new \DOMXPath($dom);

            // Các class rác cần xóa
            $classesToRemove = [
                'pagenavcounter',
                'pagenav',
                'print-icon',
                'email-icon',
                'btn-group',
                'icons', // Wrapper của các nút In/Email
                'dropdown-menu'
            ];

            $hasChanges = false;

            foreach ($classesToRemove as $cls) {
                // Tìm tất cả các node có chứa class rác
                $nodes = $xpath->query("//*[contains(concat(' ', normalize-space(@class), ' '), ' $cls ')]");
                foreach ($nodes as $node) {
                    if ($node->parentNode) {
                        $node->parentNode->removeChild($node);
                        $hasChanges = true;
                    }
                }
            }

            // Xóa nút dropdown-toggle (nút bánh răng)
            $toggleNodes = $xpath->query("//a[contains(@class, 'dropdown-toggle')]");
            foreach ($toggleNodes as $node) {
                if ($node->parentNode) {
                    $node->parentNode->removeChild($node);
                    $hasChanges = true;
                }
            }

            if ($hasChanges) {
                // Lấy lại HTML, bỏ thẻ xml ban đầu
                $content = $dom->saveHTML();
                $content = str_replace('<?xml encoding="utf-8" ?>', '', $content);
                
                $post->content = trim($content);
                $post->save();
                $updated++;
                $this->line(" - Đã dọn dẹp bài: " . $post->title);
            }
        }
        
        $this->info("Hoàn tất! Đã dọn dẹp $updated bài viết.");
    }
}
