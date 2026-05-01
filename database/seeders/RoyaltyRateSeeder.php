<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\RoyaltyRate;

class RoyaltyRateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $rates = [
            // 1. Tin
            ['group_name' => '1. Tin', 'name' => 'Tin tự viết', 'unit' => 'đồng/tin bài', 'amount' => 30000],
            ['group_name' => '1. Tin', 'name' => 'Tin sưu tầm.', 'unit' => 'đồng/tin bài', 'amount' => 10000],

            // 2. Thông tin chuyên sâu
            ['group_name' => '2. Thông tin chuyên sâu', 'name' => 'Kỹ thuật', 'unit' => 'đồng/tin bài', 'amount' => 120000],
            ['group_name' => '2. Thông tin chuyên sâu', 'name' => 'Phỏng vấn, hỏi đáp', 'unit' => 'đồng/tin bài', 'amount' => 110000],
            ['group_name' => '2. Thông tin chuyên sâu', 'name' => 'Hoạt động ngành', 'unit' => 'đồng/tin bài', 'amount' => 90000],
            ['group_name' => '2. Thông tin chuyên sâu', 'name' => 'Mô hình sản xuất', 'unit' => 'đồng/tin bài', 'amount' => 60000],
            ['group_name' => '2. Thông tin chuyên sâu', 'name' => 'Văn học nghệ thuật thuộc thể loại ký, chính luận, tản văn, truyện ngắn, thơ', 'unit' => 'đồng/tin bài', 'amount' => 100000],
            ['group_name' => '2. Thông tin chuyên sâu', 'name' => 'Phóng sự', 'unit' => 'đồng/tin bài', 'amount' => 120000],
            ['group_name' => '2. Thông tin chuyên sâu', 'name' => 'Kết quả nghiên cứu khoa học', 'unit' => 'đồng/tin bài', 'amount' => 120000],
            ['group_name' => '2. Thông tin chuyên sâu', 'name' => 'Chiến lược, tổng quan, tổng luận', 'unit' => 'đồng/tin bài', 'amount' => 120000],

            // 3. Bài dịch
            ['group_name' => '3. Bài dịch', 'name' => 'Truyện, sách, báo nước ngoài, tin', 'unit' => 'đồng/tin bài', 'amount' => 100000],
            ['group_name' => '3. Bài dịch', 'name' => 'Tin', 'unit' => 'đồng/tin bài', 'amount' => 30000],

            // 4. Quay phim, nhiếp ảnh
            ['group_name' => '4. Quay phim, nhiếp ảnh', 'name' => 'Trang bìa (nguyên trang)', 'unit' => 'đồng/ảnh', 'amount' => 50000],
            ['group_name' => '4. Quay phim, nhiếp ảnh', 'name' => 'Trang ruột', 'unit' => 'đồng/ảnh', 'amount' => 10000],
            ['group_name' => '4. Quay phim, nhiếp ảnh', 'name' => 'Video clip', 'unit' => 'đồng/tác phẩm', 'amount' => 200000],

            // 5. Mỹ thuật
            ['group_name' => '5. Mỹ thuật', 'name' => 'Trang bìa tranh sáng tác, bản vẽ kỹ thuật, Infographic (hình thức đồ họa trực quan) nguyên trang', 'unit' => 'đồng/tác phẩm', 'amount' => 100000],
            ['group_name' => '5. Mỹ thuật', 'name' => 'Trang ruột tranh sáng tác, bản vẽ kỹ thuật.', 'unit' => 'đồng/tác phẩm', 'amount' => 60000],
        ];

        foreach ($rates as $rate) {
            RoyaltyRate::firstOrCreate(['name' => $rate['name']], $rate);
        }
    }
}
