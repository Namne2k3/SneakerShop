<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Main categories
        $mainCategories = [
            [
                'name' => 'Nam',
                'description' => 'Tất cả các loại giày sneaker và giày dép dành cho nam',
            ],
            [
                'name' => 'Nữ',
                'description' => 'Tất cả các loại giày sneaker và giày dép dành cho nữ',
            ],
            [
                'name' => 'Trẻ em',
                'description' => 'Tất cả các loại giày sneaker và giày dép dành cho trẻ em',
            ]
        ];

        $createdMainCategories = [];
        foreach ($mainCategories as $categoryData) {
            $category = Category::create([
                'name' => $categoryData['name'],
                'slug' => Str::slug($categoryData['name']),
                'description' => $categoryData['description'],
            ]);
            $createdMainCategories[$categoryData['name']] = $category->id;
        }

        // Subcategories for Men
        $menSubcategories = [
            [
                'name' => 'Giày chạy bộ',
                'description' => 'Giày chạy bộ nam cho mọi loại địa hình',
                'parent_id' => $createdMainCategories['Nam'],
            ],
            [
                'name' => 'Giày bóng rổ',
                'description' => 'Giày bóng rổ nam dành cho hiệu suất thi đấu trên sân',
                'parent_id' => $createdMainCategories['Nam'],
            ],
            [
                'name' => 'Giày sneaker thời trang',
                'description' => 'Giày sneaker thời trang nam dành cho mặc hàng ngày',
                'parent_id' => $createdMainCategories['Nam'],
            ],
            [
                'name' => 'Giày tập luyện',
                'description' => 'Giày tập luyện và gym dành cho nam',
                'parent_id' => $createdMainCategories['Nam'],
            ]
        ];

        foreach ($menSubcategories as $categoryData) {
            Category::create([
                'name' => $categoryData['name'],
                'slug' => Str::slug($categoryData['name'] . '-nam'),
                'description' => $categoryData['description'],
                'parent_id' => $categoryData['parent_id'],
            ]);
        }

        // Subcategories for Women
        $womenSubcategories = [
            [
                'name' => 'Giày chạy bộ',
                'description' => 'Giày chạy bộ nữ cho mọi loại địa hình',
                'parent_id' => $createdMainCategories['Nữ'],
            ],
            [
                'name' => 'Giày sneaker thời trang',
                'description' => 'Giày sneaker thời trang dành cho nữ',
                'parent_id' => $createdMainCategories['Nữ'],
            ],
            [
                'name' => 'Giày tập luyện',
                'description' => 'Giày tập luyện và gym dành cho nữ',
                'parent_id' => $createdMainCategories['Nữ'],
            ],
            [
                'name' => 'Giày đi bộ',
                'description' => 'Giày đi bộ thoải mái dành cho nữ',
                'parent_id' => $createdMainCategories['Nữ'],
            ]
        ];

        foreach ($womenSubcategories as $categoryData) {
            Category::create([
                'name' => $categoryData['name'],
                'slug' => Str::slug($categoryData['name'] . '-nu'),
                'description' => $categoryData['description'],
                'parent_id' => $categoryData['parent_id'],
            ]);
        }

        // Subcategories for Kids
        $kidsSubcategories = [
            [
                'name' => 'Giày bé trai',
                'description' => 'Giày dành cho bé trai ở mọi lứa tuổi',
                'parent_id' => $createdMainCategories['Trẻ em'],
            ],
            [
                'name' => 'Giày bé gái',
                'description' => 'Giày dành cho bé gái ở mọi lứa tuổi',
                'parent_id' => $createdMainCategories['Trẻ em'],
            ],
            [
                'name' => 'Giày trẻ mới biết đi',
                'description' => 'Giày dành cho trẻ mới biết đi và trẻ nhỏ',
                'parent_id' => $createdMainCategories['Trẻ em'],
            ]
        ];

        foreach ($kidsSubcategories as $categoryData) {
            Category::create([
                'name' => $categoryData['name'],
                'slug' => Str::slug($categoryData['name']),
                'description' => $categoryData['description'],
                'parent_id' => $categoryData['parent_id'],
            ]);
        }
    }
}