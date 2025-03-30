<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Chạy các seeders theo thứ tự phù hợp để đảm bảo các quan hệ giữa các bảng
        $this->call([
            UserSeeder::class,         // Tạo người dùng
            BrandSeeder::class,        // Tạo thương hiệu
            CategorySeeder::class,     // Tạo danh mục
            ProductSeeder::class,      // Tạo sản phẩm
            ProductVariantSeeder::class, // Tạo biến thể sản phẩm
            UpdateProductImagesWithColorSeeder::class,  // Cập nhật hình ảnh sản phẩm với đường dẫn theo màu sắc
            ReviewSeeder::class,       // Tạo đánh giá sản phẩm
            CouponSeeder::class,       // Tạo mã giảm giá
        ]);
    }
}
