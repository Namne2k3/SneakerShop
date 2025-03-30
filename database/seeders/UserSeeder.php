<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Admin user
        User::create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('admin123'),
            'phone_number' => '0987654321',
            'address' => 'Quận 1, TP. Hồ Chí Minh',
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        // Regular customers
        $customers = [
            [
                'name' => 'Nguyễn Văn An',
                'email' => 'nguyenvanan@example.com',
                'password' => Hash::make('password123'),
                'phone_number' => '0123456789',
                'address' => 'Quận 7, TP. Hồ Chí Minh',
            ],
            [
                'name' => 'Trần Thị Bình',
                'email' => 'tranthibinh@example.com',
                'password' => Hash::make('password123'),
                'phone_number' => '0123456788',
                'address' => 'Quận Cầu Giấy, Hà Nội',
            ],
            [
                'name' => 'Lê Văn Cường',
                'email' => 'levancuong@example.com',
                'password' => Hash::make('password123'),
                'phone_number' => '0123456787',
                'address' => 'TP. Đà Nẵng',
            ],
        ];

        foreach ($customers as $customer) {
            User::create(array_merge($customer, [
                'role' => 'customer',
                'email_verified_at' => now(),
            ]));
        }

        // Create additional random users
        \App\Models\User::factory(10)->create();
    }
}