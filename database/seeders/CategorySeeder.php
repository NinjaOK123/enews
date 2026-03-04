<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Bản tin AGU',         'slug' => 'ban-tin-agu',         'order' => 1],
            ['name' => 'Phóng sự Ảnh',        'slug' => 'phong-su-anh',        'order' => 2],
            ['name' => 'Khoa học với AGU',     'slug' => 'khoa-hoc-voi-agu',    'order' => 3],
            ['name' => 'Câu chuyện AGU',       'slug' => 'cau-chuyen-agu',      'order' => 4],
            ['name' => 'Góc nhìn',             'slug' => 'goc-nhin',            'order' => 5],
            ['name' => 'Tản mạn',              'slug' => 'tan-man',             'order' => 6],
            ['name' => 'Gương mặt AGU',        'slug' => 'guong-mat-agu',       'order' => 7],
            ['name' => 'SV với Câu lạc bộ',   'slug' => 'sv-clb',              'order' => 8],
            ['name' => 'eNews và Bạn đọc',     'slug' => 'enews-ban-doc',       'order' => 9],
            ['name' => 'Lướt web cùng SV',     'slug' => 'luot-web-cung-sv',    'order' => 10],
        ];

        foreach ($categories as $cat) {
            Category::firstOrCreate(
                ['slug' => $cat['slug']],
                array_merge($cat, ['is_active' => true])
            );
        }
    }
}
