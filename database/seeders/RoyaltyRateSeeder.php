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
            ['group_name' => '1. Tin', 'name' => 'Tin tự viết', 'unit' => 'đồng/tin bài', 'amount' => 30000],
            ['group_name' => '1. Tin', 'name' => 'Tin sưu tầm (có trích nguồn)', 'unit' => 'đồng/tin bài', 'amount' => 10000],
            ['group_name' => '2. Thông tin chuyên sâu', 'name' => 'Kỹ thuật', 'unit' => 'đồng/tin bài', 'amount' => 120000],
            ['group_name' => '2. Thông tin chuyên sâu', 'name' => 'Phỏng vấn', 'unit' => 'đồng/tin bài', 'amount' => 110000],
            ['group_name' => '2. Thông tin chuyên sâu', 'name' => 'Nghệ thuật, giải trí', 'unit' => 'đồng/tin bài', 'amount' => 110000],
            ['group_name' => '2. Thông tin chuyên sâu', 'name' => 'Xã hội', 'unit' => 'đồng/tin bài', 'amount' => 110000],
            ['group_name' => '2. Thông tin chuyên sâu', 'name' => 'Diễn đàn', 'unit' => 'đồng/tin bài', 'amount' => 110000],
            ['group_name' => '3. Bài dịch', 'name' => 'Dịch ngược (Việt - Anh)', 'unit' => 'đồng/tin bài', 'amount' => 80000],
            ['group_name' => '3. Bài dịch', 'name' => 'Dịch xuôi (Anh/Hàn/Hoa - Việt)', 'unit' => 'đồng/tin bài', 'amount' => 60000],
            ['group_name' => '4. Tranh ảnh, thiết kế mỹ thuật', 'name' => 'Video clip tự làm (có xử lý đồ hoạ)', 'unit' => 'đồng/clip', 'amount' => 120000],
            ['group_name' => '4. Tranh ảnh, thiết kế mỹ thuật', 'name' => 'Infographics', 'unit' => 'đồng/tác phẩm', 'amount' => 100000],
            ['group_name' => '4. Tranh ảnh, thiết kế mỹ thuật', 'name' => 'Thiết kế Video - Audio', 'unit' => 'đồng/clip', 'amount' => 80000],
            ['group_name' => '4. Tranh ảnh, thiết kế mỹ thuật', 'name' => 'Làm phụ đề Video', 'unit' => 'đồng/clip', 'amount' => 30000],
            ['group_name' => '4. Tranh ảnh, thiết kế mỹ thuật', 'name' => 'Ảnh', 'unit' => 'đồng/ảnh', 'amount' => 10000],
            ['group_name' => '5. Nhóm Câu lạc bộ', 'name' => 'Tản văn, Thơ, Truyện', 'unit' => 'đồng/tác phẩm', 'amount' => 10000],
            ['group_name' => '5. Nhóm Câu lạc bộ', 'name' => 'Thực hiện Radio eNews', 'unit' => 'đồng/kỳ', 'amount' => 50000],
        ];

        foreach ($rates as $rate) {
            RoyaltyRate::firstOrCreate(['name' => $rate['name']], $rate);
        }
    }
}
