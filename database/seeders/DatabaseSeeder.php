<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $users = [
            [
                'name'     => 'Quản Trị Viên',
                'email'    => 'admin@agu.edu.vn',
                'password' => Hash::make('password'),
                'role'     => User::ROLE_ADMIN,
            ],
            [
                'name'     => 'Biên Tập Viên',
                'email'    => 'editor@agu.edu.vn',
                'password' => Hash::make('password'),
                'role'     => User::ROLE_EDITOR,
            ],
            [
                'name'     => 'Cộng Tác Viên',
                'email'    => 'contributor@agu.edu.vn',
                'password' => Hash::make('password'),
                'role'     => User::ROLE_CONTRIBUTOR,
            ],
            [
                'name'     => 'Người Xem Nội Bộ',
                'email'    => 'viewer@agu.edu.vn',
                'password' => Hash::make('password'),
                'role'     => User::ROLE_VIEWER,
            ],
            [
                'name'     => 'Người Dùng AGU',
                'email'    => 'reader@agu.edu.vn',
                'password' => Hash::make('password'),
                'role'     => User::ROLE_READER,
            ],
        ];

        foreach ($users as $userData) {
            User::firstOrCreate(
                ['email' => $userData['email']],
                $userData
            );
        }
    }
}
