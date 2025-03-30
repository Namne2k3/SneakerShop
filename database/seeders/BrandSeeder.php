<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Brand;
use Illuminate\Support\Str;

class BrandSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $brands = [
            [
                'name' => 'Nike',
                'description' => 'Nike là thương hiệu giày thể thao đến từ Mỹ, nổi tiếng với các dòng giày thể thao, quần áo và dụng cụ thể thao chất lượng cao.',
                'logo' => 'brands/nike-logo.png',
            ],
            [
                'name' => 'Adidas',
                'description' => 'Adidas là thương hiệu thể thao đến từ Đức, cung cấp các sản phẩm giày, quần áo và phụ kiện thể thao đa dạng.',
                'logo' => 'brands/adidas-logo.png',
            ],
            [
                'name' => 'Puma',
                'description' => 'Puma là nhà sản xuất dụng cụ thể thao đến từ Đức, chuyên thiết kế và sản xuất giày dép thể thao, quần áo và phụ kiện.',
                'logo' => 'brands/puma-logo.png',
            ],
            [
                'name' => 'New Balance',
                'description' => 'New Balance là thương hiệu giày thể thao đến từ Mỹ, nổi tiếng với các dòng sản phẩm giày chạy bộ chất lượng cao.',
                'logo' => 'brands/new-balance-logo.png',
            ],
            [
                'name' => 'Converse',
                'description' => 'Converse là thương hiệu giày dép đến từ Mỹ, nổi tiếng với dòng giày Chuck Taylor All Star mang tính biểu tượng.',
                'logo' => 'brands/converse-logo.png',
            ],
            [
                'name' => 'Vans',
                'description' => 'Vans là thương hiệu giày trượt ván và thời trang đường phố đến từ Mỹ, được giới trẻ yêu thích.',
                'logo' => 'brands/vans-logo.png',
            ],
            [
                'name' => 'Reebok',
                'description' => 'Reebok là thương hiệu giày thể thao đến từ Mỹ, chuyên sản xuất giày dép và quần áo thể dục thể thao.',
                'logo' => 'brands/reebok-logo.png',
            ],
            [
                'name' => 'Under Armour',
                'description' => 'Under Armour là thương hiệu thể thao đến từ Mỹ, chuyên sản xuất quần áo, giày dép và phụ kiện thể thao.',
                'logo' => 'brands/under-armour-logo.png',
            ]
        ];

        foreach ($brands as $brandData) {
            Brand::create([
                'name' => $brandData['name'],
                'slug' => Str::slug($brandData['name']),
                'description' => $brandData['description'],
                'logo' => $brandData['logo'],
            ]);
        }
    }
}