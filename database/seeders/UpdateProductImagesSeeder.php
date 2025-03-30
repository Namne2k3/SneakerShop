<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Support\Facades\DB;

class UpdateProductImagesSeeder extends Seeder
{
    /**
     * Cập nhật đường dẫn hình ảnh thực tế cho sản phẩm.
     */
    public function run(): void
    {
        // Xóa các hình ảnh hiện có trong bảng product_images
        DB::table('product_images')->truncate();
        
        // Danh sách đường dẫn hình ảnh thực tế cho từng sản phẩm
        $productImagesMapping = [
            'Air Max 90' => [
                'products/nike/air-max-90-1.jpg', 
                'products/nike/air-max-90-2.jpg', 
                'products/nike/air-max-90-3.jpg',
                'products/nike/air-max-90-4.jpg',
                'products/nike/air-max-90-5.jpg'
            ],
            'Ultra Boost 21' => [
                'products/adidas/ultra-boost-21-1.jpg',
                'products/adidas/ultra-boost-21-2.jpg',
                'products/adidas/ultra-boost-21-3.jpg',
                'products/adidas/ultra-boost-21-4.jpg'
            ],
            'Suede Classic XXI' => [
                'products/puma/suede-classic-xxi-1.jpg',
                'products/puma/suede-classic-xxi-2.jpg',
                'products/puma/suede-classic-xxi-3.jpg'
            ],
            'LeBron 19' => [
                'products/nike/lebron-19-1.jpg',
                'products/nike/lebron-19-2.jpg',
                'products/nike/lebron-19-3.jpg'
            ],
            '574 Classic' => [
                'products/new-balance/574-classic-1.jpg',
                'products/new-balance/574-classic-2.jpg',
                'products/new-balance/574-classic-3.jpg'
            ],
            'React Infinity Run Flyknit' => [
                'products/nike/react-infinity-run-flyknit-1.jpg',
                'products/nike/react-infinity-run-flyknit-2.jpg',
                'products/nike/react-infinity-run-flyknit-3.jpg'
            ],
            'Chuck Taylor All Star' => [
                'products/converse/chuck-taylor-all-star-1.jpg',
                'products/converse/chuck-taylor-all-star-2.jpg',
                'products/converse/chuck-taylor-all-star-3.jpg'
            ],
            'Old Skool' => [
                'products/vans/old-skool-1.jpg',
                'products/vans/old-skool-2.jpg',
                'products/vans/old-skool-3.jpg'
            ],
            'Nano X2 Training' => [
                'products/reebok/nano-x2-training-1.jpg',
                'products/reebok/nano-x2-training-2.jpg',
                'products/reebok/nano-x2-training-3.jpg'
            ],
            'Air Force 1 Kids' => [
                'products/nike/air-force-1-kids-1.jpg',
                'products/nike/air-force-1-kids-2.jpg',
                'products/nike/air-force-1-kids-3.jpg'
            ],
            'Superstar Kids' => [
                'products/adidas/superstar-kids-1.jpg',
                'products/adidas/superstar-kids-2.jpg',
                'products/adidas/superstar-kids-3.jpg'
            ],
            'Tiny Slip-On' => [
                'products/vans/tiny-slip-on-1.jpg',
                'products/vans/tiny-slip-on-2.jpg',
                'products/vans/tiny-slip-on-3.jpg'
            ],
        ];

        // Cập nhật đường dẫn hình ảnh cho từng sản phẩm
        foreach ($productImagesMapping as $productName => $imagePaths) {
            // Tìm sản phẩm theo tên
            $product = Product::where('name', $productName)->first();
            
            if ($product) {
                // Thêm các hình ảnh cho sản phẩm
                foreach ($imagePaths as $index => $imagePath) {
                    ProductImage::create([
                        'product_id' => $product->id,
                        'image_path' => $imagePath,
                        'is_primary' => ($index === 0), // Hình đầu tiên là hình chính
                        'sort_order' => $index + 1
                    ]);
                }
                
                echo "Đã cập nhật hình ảnh cho sản phẩm: {$productName}\n";
            } else {
                echo "Không tìm thấy sản phẩm: {$productName}\n";
            }
        }
        
        echo "Hoàn thành cập nhật hình ảnh sản phẩm!\n";
    }
}