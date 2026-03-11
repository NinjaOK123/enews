<?php

namespace Database\Seeders;

use App\Models\Notification;
use Illuminate\Database\Seeder;

class NotificationSeeder extends Seeder
{
    public function run(): void
    {
        $notifications = [
            [
                'title'      => 'Chào mừng năm học mới 2025-2026!',
                'content'    => '<p>Kính gửi toàn thể cán bộ, giảng viên và sinh viên,</p><p>Ban Giám hiệu trân trọng thông báo và chúc mừng năm học mới 2025–2026 đến toàn thể cộng đồng trường. Chúc mọi người một năm học thành công, năng động và nhiều kết quả tốt đẹp!</p><p>Trân trọng,<br>Ban Biên tập eNews</p>',
                'recipients' => ['all'],
                'sent_at'    => now()->subDays(30),
            ],
            [
                'title'      => 'Hướng dẫn đăng bài lên cổng thông tin',
                'content'    => '<p>Kính chào các Cộng tác viên và Biên tập viên,</p><p>Để đảm bảo chất lượng bài viết đăng trên hệ thống, xin lưu ý các bước sau:</p><ol><li>Bài viết phải có tiêu đề rõ ràng, không vi phạm thuần phong mỹ tục.</li><li>Nội dung phải có ít nhất 300 từ, kèm ảnh minh họa chất lượng cao.</li><li>Sau khi submit, hãy chờ Biên tập viên phê duyệt trong vòng 24 giờ.</li></ol>',
                'recipients' => ['editor', 'contributor'],
                'sent_at'    => now()->subDays(20),
            ],
            [
                'title'      => 'Bảo trì hệ thống tối ngày 15/03/2026',
                'content'    => '<p>Thông báo bảo trì định kỳ hệ thống!</p><p>Hệ thống eNews sẽ được bảo trì từ <strong>22:00 – 02:00</strong> ngày 15/03/2026. Trong thời gian này, toàn bộ tính năng sẽ tạm thời không khả dụng.</p><p>Mong quý thành viên thông cảm và sắp xếp công việc phù hợp.</p>',
                'recipients' => ['all'],
                'sent_at'    => now()->subDays(10),
            ],
            [
                'title'      => 'Nhắc nhở quy trình kiểm duyệt bình luận',
                'content'    => '<p>Kính gửi đội ngũ Biên tập viên,</p><p>Xin nhắc nhở: tất cả bình luận từ người dùng cần được kiểm duyệt trước khi hiển thị. Các bình luận vi phạm cần bị từ chối ngay. Hệ thống có tính năng lọc từ ngữ tự động, tuy nhiên Biên tập viên vẫn cần rà soát thủ công.</p>',
                'recipients' => ['editor'],
                'sent_at'    => now()->subDays(7),
            ],
            [
                'title'      => 'Cập nhật chính sách đăng bài tháng 3/2026',
                'content'    => '<p>Từ ngày 01/03/2026, một số thay đổi trong chính sách đăng bài được áp dụng:</p><ul><li>Giới hạn 3 bài/ngày/cộng tác viên.</li><li>Bài viết cần được gắn ít nhất 1 chuyên mục và 3 từ khóa.</li><li>Ảnh bìa bắt buộc, kích thước tối thiểu 800x400px.</li></ul><p>Vui lòng tuân thủ để tránh bài bị từ chối tự động.</p>',
                'recipients' => ['contributor'],
                'sent_at'    => now()->subDays(5),
            ],
            [
                'title'      => 'Thông báo tuyển dụng Cộng tác viên mùa hè 2026',
                'content'    => '<p>eNews đang tuyển dụng thêm Cộng tác viên viết bài cho mùa hè 2026!</p><p>Yêu cầu: có kỹ năng viết lách, nhiệt tình, chịu khó. Ưu tiên sinh viên năm 2-3 ngành Báo chí, Ngữ văn, CNTT.</p><p>Đăng ký: liên hệ Ban biên tập qua email <a href="mailto:enews@agu.edu.vn">enews@agu.edu.vn</a></p>',
                'recipients' => ['reader', 'contributor'],
                'sent_at'    => now()->subDays(3),
            ],
            // Chưa gửi (nháp)
            [
                'title'      => '[NHÁP] Thông báo lịch họp Ban biên tập tháng 4',
                'content'    => '<p>Lịch họp Ban biên tập tháng 4/2026 dự kiến vào ngày 10/04/2026 lúc 14:00 tại phòng họp A204.</p><p>Nội dung: Đánh giá kết quả quý 1, kế hoạch quý 2, và ra mắt tính năng mới.</p>',
                'recipients' => ['admin', 'editor'],
                'sent_at'    => null, // Chưa gửi
            ],
            [
                'title'      => '[NHÁP] Chúc mừng 8/3 – Ngày Quốc tế Phụ nữ',
                'content'    => '<p>Nhân dịp kỷ niệm Ngày Quốc tế Phụ nữ 8/3, Ban biên tập eNews xin gửi lời chúc mừng tốt đẹp nhất đến toàn thể phụ nữ trong cộng đồng trường AGU!</p><p>Chúc các chị em luôn mạnh khỏe, hạnh phúc và thành công!</p>',
                'recipients' => ['all'],
                'sent_at'    => null, // Chưa gửi
            ],
        ];

        foreach ($notifications as $data) {
            Notification::create($data);
        }
    }
}
