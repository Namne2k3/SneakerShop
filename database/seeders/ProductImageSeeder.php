<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\ProductImage;

class ProductImageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = Product::all();
        
        foreach ($products as $product) {
            // Tạo đường dẫn ảnh dựa theo tên sản phẩm (giả lập)
            $slug = \Illuminate\Support\Str::slug($product->name);
            $brandSlug = \Illuminate\Support\Str::slug($product->brand->name);
            
            // Mỗi sản phẩm có 3-5 ảnh
            $numImages = rand(3, 5);
            
            for ($i = 1; $i <= $numImages; $i++) {
                $isPrimary = ($i === 1); // Ảnh đầu tiên là ảnh chính
                
                // Tạo đường dẫn ảnh mẫu
                $imagePath = "products/{$brandSlug}/{$slug}-{$i}.jpg";
                
                ProductImage::create([
                    'product_id' => $product->id,
                    'image_path' => $imagePath,
                    'is_primary' => $isPrimary,
                    'sort_order' => $i
                ]);
            }
        }
    }
}