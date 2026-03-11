<?php

namespace Database\Seeders;

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Seeder;

class CommentSeeder extends Seeder
{
    public function run(): void
    {
        // Lấy danh sách bài viết đã published và người dùng
        $posts = Post::where('status', 'published')->pluck('id')->toArray();
        $users = User::pluck('id')->toArray();

        if (empty($posts) || empty($users)) {
            $this->command->warn('Không có bài viết published hoặc người dùng nào. Bỏ qua CommentSeeder.');
            return;
        }

        $comments = [
            // Nhóm 1: Bình luận tích cực (Đã duyệt)
            ['content' => 'Bài viết rất hay và bổ ích! Cảm ơn tác giả đã chia sẻ những thông tin quý giá này.', 'is_approved' => true],
            ['content' => 'Thông tin trong bài rất chi tiết và dễ hiểu. Mình đã học được nhiều điều mới từ bài này.', 'is_approved' => true],
            ['content' => 'Cảm ơn ban biên tập đã đăng tải bài viết này. Mong có thêm nhiều bài hay như vậy!', 'is_approved' => true],
            ['content' => 'Nội dung rất chất lượng, hình ảnh minh họa cũng rất đẹp. 5 sao cho bài viết này!', 'is_approved' => true],
            ['content' => 'Mình đã chia sẻ bài này cho cả nhóm học tập. Thực sự rất hữu ích!', 'is_approved' => true],

            // Nhóm 2: Hỏi đáp, trao đổi (Đã duyệt)
            ['content' => 'Cho mình hỏi thêm về phần cuối bài, có tài liệu tham khảo nào không ạ?', 'is_approved' => true],
            ['content' => 'Bài viết có đề cập đến sự kiện này sẽ được tổ chức khi nào? Mình muốn tham gia.', 'is_approved' => true],
            ['content' => 'Rất đồng ý với quan điểm của tác giả. Đây là vấn đề cần được quan tâm hơn nữa.', 'is_approved' => true],
            ['content' => 'Bài viết có một số điểm mình không đồng ý, nhưng nhìn chung vẫn rất đáng đọc.', 'is_approved' => true],

            // Nhóm 3: Phòng chờ duyệt (Pending -> is_approved = null)
            ['content' => 'Bài này có vẻ cập nhật thông tin hơi muộn so với bài báo gốc?', 'is_approved' => null],
            ['content' => 'Mình muốn xin bản pdf của bài này thì email cho ai ạ?', 'is_approved' => null],
            ['content' => 'Mong admin duyệt comment này sớm để mọi người cùng thảo luận.', 'is_approved' => null],
            ['content' => 'Có ai biết thời gian cụ thể diễn ra sự kiện này là bao giờ không?', 'is_approved' => null],

            // Nhóm 4: Bị từ chối (Rejected -> is_approved = false)
            ['content' => 'Bài này quảng cáo lộ liễu quá! Mọi người sang web abc.com xem bài review chân thực hơn.', 'is_approved' => false],
            ['content' => 'Mua acc game giá rẻ tại link này nhé: http://spam-link.com', 'is_approved' => false],
            ['content' => 'Tác giả viết bài này chả hiểu cái gì cả, toàn copy trên mạng về.', 'is_approved' => false],
            ['content' => 'Ai muốn kiếm tiền online ngày 500k thì inbox zalo mình nhé: 09xx.xxx.xxx', 'is_approved' => false],
            ['content' => 'Thông tin trong bài hoàn toàn bịa đặt, yêu cầu gỡ ngay!', 'is_approved' => false],

            // Nhóm 5: Ngắn gọn (Đã duyệt)
            ['content' => 'Tuyệt vời! Cảm ơn biên tập viên!', 'is_approved' => true],
            ['content' => 'Hay lắm, đọc mãi không chán 👍', 'is_approved' => true],
            ['content' => 'Bài viết bài viết rất ý nghĩa, đặc biệt với sinh viên năm nhất như mình.', 'is_approved' => true],
        ];

        foreach ($comments as $index => $commentData) {
            // Phân bổ bình luận đều trên các bài viết
            $postId = $posts[$index % count($posts)];
            $userId = $users[$index % count($users)];

            Comment::create([
                'post_id'     => $postId,
                'user_id'     => $userId,
                'content'     => $commentData['content'],
                'is_approved' => $commentData['is_approved'],
                // Tạo timestamps rải rác trong 30 ngày qua
                'created_at'  => now()->subDays(rand(1, 30))->subHours(rand(0, 23)),
                'updated_at'  => now()->subDays(rand(0, 5)),
            ]);
        }

        $this->command->info('✅ Đã tạo ' . count($comments) . ' bình luận mẫu.');
    }
}
