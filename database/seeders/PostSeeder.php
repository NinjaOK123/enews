<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PostSeeder extends Seeder
{
    public function run(): void
    {
        $admin       = User::where('role', 'admin')->first();
        $editor      = User::where('role', 'editor')->first();
        $contributor = User::where('role', 'contributor')->first();

        $cats = Category::pluck('id', 'slug');

        $posts = [
            // ── Bản tin AGU ───────────────────────────────────────────────────
            [
                'title'        => 'Trường Đại học An Giang tổ chức Lễ tốt nghiệp Đại học chính quy năm 2024',
                'slug'         => 'le-tot-nghiep-dai-hoc-2024',
                'excerpt'      => 'Sáng ngày 15/3/2024, Trường Đại học An Giang long trọng tổ chức Lễ tốt nghiệp cho 1.200 sinh viên đại học chính quy năm 2024.',
                'content'      => 'Sáng ngày 15/3/2024, Trường Đại học An Giang long trọng tổ chức Lễ tốt nghiệp cho hơn 1.200 sinh viên đại học chính quy. Buổi lễ có sự tham dự của Ban Giám hiệu, các Trưởng Khoa, cùng đông đảo phụ huynh và sinh viên. Dịp này, nhà trường đã trao bằng tốt nghiệp, học bổng khuyến khích học tập và tuyên dương những sinh viên xuất sắc. Hiệu trưởng PGS.TS Võ Văn Thắng chia sẻ: "Đây là cột mốc quan trọng trên hành trình các bạn bước ra xã hội, mang theo tri thức và giá trị đạo đức mà mái trường AGU đã trao truyền."',
                'thumbnail'    => 'https://images.unsplash.com/photo-1523050854058-8df90110c9f1?w=600&q=80',
                'author_id'    => $admin?->id ?? 1,
                'category_id'  => $cats['ban-tin-agu'],
                'status'       => 'published',
                'published_at' => now()->subDays(2),
                'view_count'   => 1520,
            ],
            [
                'title'        => 'Lễ ký kết hợp tác giữa Trường Đại học An Giang và Công ty FPT Software',
                'slug'         => 'ky-ket-hop-tac-agu-fpt-software',
                'excerpt'      => 'AGU và FPT Software vừa ký kết biên bản ghi nhớ hợp tác đào tạo nhân lực CNTT chất lượng cao, mở ra nhiều cơ hội thực tập và việc làm cho sinh viên.',
                'content'      => 'Chiều 10/3/2024, Trường Đại học An Giang đã tổ chức lễ ký kết biên bản ghi nhớ hợp tác toàn diện với Công ty FPT Software. Thỏa thuận bao gồm: hỗ trợ học bổng cho sinh viên xuất sắc ngành CNTT, tổ chức các khóa đào tạo kỹ năng thực chiến, nhận sinh viên thực tập có lương, và ưu tiên tuyển dụng sinh viên tốt nghiệp loại Giỏi. Đây là bước tiến quan trọng trong chiến lược kết nối nhà trường với doanh nghiệp của AGU.',
                'thumbnail'    => 'https://images.unsplash.com/photo-1560439514-4e9645039924?w=600&q=80',
                'author_id'    => $editor?->id ?? 1,
                'category_id'  => $cats['ban-tin-agu'],
                'status'       => 'published',
                'published_at' => now()->subDays(5),
                'view_count'   => 842,
            ],
            [
                'title'        => 'Sinh viên AGU tham gia hiến máu tình nguyện đợt 1 năm 2026',
                'slug'         => 'hien-mau-tinh-nguyen-dot-1-2026',
                'excerpt'      => 'Hơn 300 sinh viên AGU đã tham gia chương trình hiến máu tình nguyện "Giọt hồng yêu thương" lần thứ 14, góp phần bổ sung nguồn máu cho các bệnh viện tỉnh An Giang.',
                'content'      => 'Sáng 5/3/2026, Đoàn Trường Đại học An Giang phối hợp với Hội Chữ thập đỏ tỉnh An Giang tổ chức chương trình hiến máu tình nguyện "Giọt hồng yêu thương" lần thứ 14. Hơn 300 sinh viên đã tham gia và thu về được 285 đơn vị máu an toàn. Em Nguyễn Minh Tuấn (K22 Kế toán) chia sẻ: "Đây là lần thứ 3 mình tham gia hiến máu tại trường. Mỗi lần hiến máu là một lần mình cảm thấy mình đang làm điều gì đó thực sự có ý nghĩa."',
                'thumbnail'    => 'https://images.unsplash.com/photo-1609220136736-443140cfeaa8?w=600&q=80',
                'author_id'    => $contributor?->id ?? 1,
                'category_id'  => $cats['ban-tin-agu'],
                'status'       => 'published',
                'published_at' => now()->subDays(1),
                'view_count'   => 411,
            ],

            // ── Khoa học với AGU ──────────────────────────────────────────────
            [
                'title'        => 'Ứng dụng AI trong dự báo lũ lụt tại ĐBSCL — nghiên cứu của nhóm SV Khoa CNTT',
                'slug'         => 'ai-du-bao-lu-lut-dbscl-cntt-agu',
                'excerpt'      => 'Nhóm sinh viên Khoa CNTT đã phát triển thành công mô hình AI có thể dự báo mức độ ngập lụt trước 72 giờ với độ chính xác lên đến 87%, đoạt giải Nhất Hội nghị KHSV lần thứ 18.',
                'content'      => 'Nhóm nghiên cứu gồm 4 sinh viên năm 3 Khoa Công nghệ thông tin - Trường Đại học An Giang đã phát triển hệ thống AI dự báo lũ lụt ứng dụng mô hình học máy LSTM (Long Short-Term Memory). Dữ liệu được thu thập từ các trạm quan trắc thủy văn tại 5 tỉnh ĐBSCL trong vòng 20 năm. Kết quả thử nghiệm cho thấy mô hình có thể dự báo trước 72 giờ với độ chính xác 87,3%. Đề tài đã đoạt giải Nhất tại Hội nghị Khoa học Sinh viên lần thứ 18 của trường và hiện đang được Bộ Tài nguyên & Môi trường quan tâm triển khai thí điểm.',
                'thumbnail'    => 'https://images.unsplash.com/photo-1451187580459-43490279c0fa?w=600&q=80',
                'author_id'    => $editor?->id ?? 1,
                'category_id'  => $cats['khoa-hoc-voi-agu'],
                'status'       => 'published',
                'published_at' => now()->subDays(3),
                'view_count'   => 2103,
            ],
            [
                'title'        => 'Nghiên cứu giống lúa chịu mặn thích ứng biến đổi khí hậu vùng ĐBSCL',
                'slug'         => 'nghien-cuu-giong-lua-chiu-man-dbscl',
                'excerpt'      => 'Nhóm nghiên cứu Khoa Nông nghiệp - Tài nguyên thiên nhiên AGU đã lai tạo thành công 3 giống lúa mới chịu mặn đến 6‰, năng suất đạt 6,5 tấn/ha.',
                'content'      => 'Sau 4 năm nghiên cứu, nhóm cán bộ và sinh viên Khoa Nông nghiệp - Tài nguyên thiên nhiên vừa công bố kết quả lai tạo 3 giống lúa mới: AGU-M1, AGU-M2 và AGU-M3. Các giống này có khả năng chịu mặn đến 6‰ và cho năng suất bình quân 6,5 tấn/ha ngay cả trong điều kiện xâm nhập mặn. TS. Trần Thị Bích Loan, chủ nhiệm đề tài, cho biết: "Chúng tôi kỳ vọng bộ giống này sẽ giúp nông dân vùng ven biển ĐBSCL ứng phó hiệu quả với tình trạng xâm nhập mặn ngày càng gia tăng."',
                'thumbnail'    => 'https://images.unsplash.com/photo-1574943320219-553eb213f72d?w=600&q=80',
                'author_id'    => $admin?->id ?? 1,
                'category_id'  => $cats['khoa-hoc-voi-agu'],
                'status'       => 'published',
                'published_at' => now()->subDays(7),
                'view_count'   => 987,
            ],

            // ── Gương mặt AGU ─────────────────────────────────────────────────
            [
                'title'        => 'Nguyễn Thị Thanh Thảo — Nữ sinh viên vượt khó vươn lên nhận học bổng toàn phần',
                'slug'         => 'nguyen-thi-thanh-thao-hoc-bong-toan-phan',
                'excerpt'      => 'Từ một cô gái vùng quê nghèo Thoại Sơn, Thanh Thảo đã nỗ lực không ngừng để giành học bổng toàn phần và trở thành sinh viên xuất sắc nhất khóa K21 ngành Sư phạm Anh.',
                'content'      => 'Nguyễn Thị Thanh Thảo (sinh năm 2003, quê Thoại Sơn, An Giang) là con thứ ba trong gia đình 5 anh chị em, bố mẹ làm nông. Để có tiền học đại học, Thảo vừa học vừa làm thêm gia sư tiếng Anh. Với điểm GPA 3.85/4.0 và nhiều thành tích nghiên cứu khoa học, Thảo đã được nhận học bổng toàn phần từ Quỹ Phát triển Giáo dục AGU trị giá 36 triệu đồng/năm. "Mình muốn trở thành giáo viên tiếng Anh ở vùng nông thôn, để các em nhỏ ở quê mình cũng có cơ hội tiếp cận tiếng Anh chất lượng", Thảo chia sẻ.',
                'thumbnail'    => 'https://images.unsplash.com/photo-1580582932707-520aed937b7b?w=600&q=80',
                'author_id'    => $contributor?->id ?? 1,
                'category_id'  => $cats['guong-mat-agu'],
                'status'       => 'published',
                'published_at' => now()->subDays(4),
                'view_count'   => 3241,
            ],

            // ── Câu chuyện AGU ────────────────────────────────────────────────
            [
                'title'        => 'Câu chuyện về những người "giữ lửa" văn hóa dân tộc Khmer tại An Giang',
                'slug'         => 'giu-lua-van-hoa-khmer-an-giang',
                'excerpt'      => 'Những sinh viên Khmer tại AGU không chỉ học tập mà còn miệt mài gìn giữ ngôn ngữ, điệu múa, và những làn điệu dân ca truyền thống của dân tộc mình.',
                'content'      => 'Trong khuôn viên Trường Đại học An Giang, mỗi chiều thứ sáu, một nhóm sinh viên người Khmer lại tập hợp để luyện tập các điệu múa truyền thống Romvong và Lăm Vông. Họ là những "đại sứ văn hóa" trẻ, quyết tâm không để những giá trị cổ truyền của dân tộc bị mai một. Em Thạch Thị Sà Rết (K22 Văn hóa học) kể: "Mình học tiếng Khmer từ ông ngoại, học múa từ mẹ. Lên đại học, mình muốn chia sẻ điều này với nhiều người hơn." CLB Văn hóa Khmer AGU hiện có 45 thành viên, thường xuyên biểu diễn tại các sự kiện văn hóa cấp tỉnh.',
                'thumbnail'    => 'https://images.unsplash.com/photo-1544027993-37dbfe43562a?w=600&q=80',
                'author_id'    => $editor?->id ?? 1,
                'category_id'  => $cats['cau-chuyen-agu'],
                'status'       => 'published',
                'published_at' => now()->subDays(6),
                'view_count'   => 1876,
            ],

            // ── SV với Câu lạc bộ ─────────────────────────────────────────────
            [
                'title'        => 'CLB Võ thuật AGU đoạt Huy chương Vàng tại Giải vô địch Pencak Silat tỉnh An Giang 2024',
                'slug'         => 'clb-vo-thuat-agu-huy-chuong-vang-pencak-silat',
                'excerpt'      => 'Vận động viên Lê Văn Hào (K22 Giáo dục Thể chất) đã xuất sắc giành HCV nội dung đơn nam trên 60kg, mang vinh quang về cho Trường Đại học An Giang.',
                'content'      => 'Tại Giải vô địch Pencak Silat tỉnh An Giang năm 2024 diễn ra từ ngày 8-10/3, CLB Võ thuật Trường Đại học An Giang đã có màn trình diễn ấn tượng với tổng cộng 1 HCV, 2 HCB và 3 HCĐ. Nổi bật nhất là VĐV Lê Văn Hào (sinh viên K22 Giáo dục Thể chất), người đã trải qua 5 trận đấu căng thẳng để giành tấm HCV danh giá ở nội dung đơn nam hạng cân trên 60kg. "Em tập luyện 3 tiếng mỗi ngày suốt 2 năm qua. Tấm HCV này không chỉ là của em mà là của cả thầy huấn luyện và các bạn trong CLB", Hào xúc động chia sẻ.',
                'thumbnail'    => 'https://images.unsplash.com/photo-1517486808906-6ca8b3f04846?w=600&q=80',
                'author_id'    => $contributor?->id ?? 1,
                'category_id'  => $cats['sv-clb'],
                'status'       => 'published',
                'published_at' => now()->subDays(3),
                'view_count'   => 1102,
            ],

            // ── Góc nhìn ──────────────────────────────────────────────────────
            [
                'title'        => 'Giáo dục đại học trong kỷ nguyên trí tuệ nhân tạo — cơ hội và thách thức',
                'slug'         => 'giao-duc-dai-hoc-trong-ky-nguyen-ai',
                'excerpt'      => 'AI đang thay đổi cách chúng ta học, dạy và nghiên cứu. Bài viết phân tích những cơ hội và thách thức mà AI mang lại cho giáo dục đại học Việt Nam.',
                'content'      => 'Sự bùng nổ của các mô hình ngôn ngữ lớn như ChatGPT, Gemini, Claude đang tạo ra cuộc cách mạng thầm lặng trong giảng đường đại học. Sinh viên có thể tóm tắt tài liệu, viết code, dịch thuật chỉ trong vài giây. Điều này đặt ra câu hỏi: giáo dục đại học cần thay đổi như thế nào để không bị lạc hậu? Theo quan điểm của người viết, thay vì cấm đoán AI, các trường đại học nên tích hợp AI vào chương trình đào tạo, hướng sinh viên biết cách sử dụng AI có đạo đức và phê phán. Kỹ năng quan trọng nhất trong kỷ nguyên AI không phải là ghi nhớ kiến thức mà là tư duy phản biện, sáng tạo và khả năng đặt câu hỏi đúng.',
                'thumbnail'    => 'https://images.unsplash.com/photo-1677442135703-1787eea5ce01?w=600&q=80',
                'author_id'    => $editor?->id ?? 1,
                'category_id'  => $cats['goc-nhin'],
                'status'       => 'published',
                'published_at' => now()->subDays(8),
                'view_count'   => 4512,
            ],

            // ── Tản mạn ───────────────────────────────────────────────────────
            [
                'title'        => 'Tết ở ký túc xá — ký ức không thể quên của sinh viên xa nhà',
                'slug'         => 'tet-o-ky-tuc-xa-ky-uc-khong-the-quen',
                'excerpt'      => 'Những cái Tết không được về nhà ở ký túc xá AGU, tưởng buồn nhưng lại đầy ắp tình người — nơi những sinh viên xa quê tìm thấy một "gia đình" thứ hai.',
                'content'      => 'Tôi vẫn nhớ cái Tết đầu tiên xa nhà năm nhất đại học. Nhà tận Kiên Giang, vé tàu hết sạch, tôi đành ở lại ký túc xá AGU với mấy đứa bạn cùng cảnh ngộ. Chiều 30 Tết, chúng tôi góp gạo nấu nồi bánh tét, đứa thì rang dưa, đứa thì làm gỏi củ hủ dừa. Thầy quản lý ký túc xá còn mang sang một nồi thịt kho tàu thơm lừng. Không có bàn thờ ông bà, không có bánh mứt tuổi thơ, nhưng chúng tôi có nhau. Đó là cái Tết tôi nhớ nhất trong đời sinh viên của mình, không phải vì thiếu thốn, mà vì đầy tình người.',
                'thumbnail'    => 'https://images.unsplash.com/photo-1456513080510-7bf3a84b82f8?w=600&q=80',
                'author_id'    => $contributor?->id ?? 1,
                'category_id'  => $cats['tan-man'],
                'status'       => 'published',
                'published_at' => now()->subDays(10),
                'view_count'   => 2987,
            ],

            // ── eNews và Bạn đọc ──────────────────────────────────────────────
            [
                'title'        => 'Ký ức mùa thi — cảm xúc của một sinh viên năm cuối nhìn lại hành trình',
                'slug'         => 'ky-uc-mua-thi-sinh-vien-nam-cuoi',
                'excerpt'      => 'Bài viết của bạn Phạm Hoàng Minh (K20 Quản trị kinh doanh) gửi đến tòa soạn eNews, chia sẻ những cảm xúc lẫn lộn khi đứng trước kỳ thi tốt nghiệp cuối cùng.',
                'content'      => 'Hôm nay là ngày cuối cùng tôi ngồi trong giảng đường này để thi. Bốn năm đại học trôi qua nhanh đến mức tôi không kịp nhận ra. Nhìn lại, có những buổi sáng tôi đạp xe trong mưa để kịp giờ học, có những đêm thức đến 2 giờ sáng ngồi làm bài tập nhóm. Có cả những lần trốn học đi uống cà phê rồi hối hận vì bỏ lỡ bài quan trọng. Nhưng tôi không hối hận về bất cứ điều gì. Bởi chính những trải nghiệm đó — cả thành công lẫn thất bại — đã làm nên con người tôi hôm nay. Cảm ơn AGU, cảm ơn thầy cô, cảm ơn những người bạn đã đồng hành cùng tôi suốt hành trình này.',
                'thumbnail'    => 'https://images.unsplash.com/photo-1455390582262-044cdead277a?w=600&q=80',
                'author_id'    => $contributor?->id ?? 1,
                'category_id'  => $cats['enews-ban-doc'],
                'status'       => 'published',
                'published_at' => now()->subDays(2),
                'view_count'   => 1654,
            ],

            // ── Lướt web cùng SV ──────────────────────────────────────────────────
            [
                'title'        => '10 kỹ năng mềm sinh viên cần chuẩn bị trước khi ra trường',
                'slug'         => '10-ky-nang-mem-sinh-vien-can-chuan-bi',
                'excerpt'      => 'Giao tiếp, làm việc nhóm, tư duy phản biện, quản lý thời gian... Đây là những kỹ năng mà nhà tuyển dụng đánh giá cao hơn cả điểm GPA.',
                'content'      => 'Theo khảo sát của LinkedIn năm 2023, 89% nhà tuyển dụng cho biết họ từ chối ứng viên vì thiếu kỹ năng mềm, dù hồ sơ chuyên môn tốt. Vậy sinh viên cần chuẩn bị những gì trước khi ra trường? 1. Kỹ năng giao tiếp: Cả bằng lời nói và văn bản. 2. Làm việc nhóm: Biết lắng nghe, tôn trọng ý kiến khác. 3. Tư duy phản biện: Không chấp nhận thông tin một chiều. 4. Quản lý thời gian: Biết ưu tiên và đặt deadline cho bản thân. 5. Thích nghi với thay đổi: Đặc biệt quan trọng trong thời đại AI. 6. Lãnh đạo bản thân: Tự giác, chủ động trong công việc. 7. Giải quyết vấn đề sáng tạo. 8. Trí tuệ cảm xúc (EQ). 9. Kỹ năng thuyết trình. 10. Kỹ năng networking chuyên nghiệp.',
                'thumbnail'    => 'https://images.unsplash.com/photo-1531545514256-b1400bc00f31?w=600&q=80',
                'author_id'    => $editor?->id ?? 1,
                'category_id'  => $cats['luot-web-cung-sv'],
                'status'       => 'published',
                'published_at' => now()->subDays(9),
                'view_count'   => 5823,
            ],

            // ── Phóng sự Ảnh ──────────────────────────────────────────────────
            [
                'title'        => 'Góc ký túc xá AGU — Cuộc sống sinh viên qua ống kính',
                'slug'         => 'goc-ky-tuc-xa-agu-qua-ong-kinh',
                'excerpt'      => 'Bộ ảnh ghi lại những khoảnh khắc bình dị nhưng đầy ý nghĩa trong cuộc sống ký túc xá của sinh viên AGU: từ bữa cơm chia nhau, đêm ôn thi, đến những trận bóng đá sân nhỏ.',
                'content'      => 'Ký túc xá Trường Đại học An Giang là ngôi nhà thứ hai của hơn 800 sinh viên đến từ khắp các tỉnh ĐBSCL. Qua ống kính của nhiếp ảnh gia trẻ Trần Quốc Bảo (K21 Truyền thông đa phương tiện), cuộc sống nơi đây hiện lên sinh động và đầy cảm xúc. Từ những bữa cơm chung được chia nhau từng con cá kho, đến những đêm thức khuya ôn bài dưới ánh đèn hành lang; từ những trận bóng đá sân nhỏ chiều thứ bảy, đến những buổi sáng sớm xếp hàng tắm... Tất cả làm nên một góc ký túc xá bình dị, ấm áp và thân thương.',
                'thumbnail'    => 'https://images.unsplash.com/photo-1541339907198-e08756dedf3f?w=600&q=80',
                'author_id'    => $contributor?->id ?? 1,
                'category_id'  => $cats['phong-su-anh'],
                'status'       => 'published',
                'published_at' => now()->subDays(5),
                'view_count'   => 2234,
            ],
        ];

        foreach ($posts as $postData) {
            Post::firstOrCreate(
                ['slug' => $postData['slug']],
                $postData
            );
        }
    }
}
