<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class UpdateProductImagesWithColorSeeder extends Seeder
{
    /**
     * Cập nhật đường dẫn hình ảnh thực tế cho sản phẩm và liên kết hình ảnh đến các màu sắc qua tên file.
     */
    public function run(): void
    {
        // Xóa các hình ảnh hiện có trong bảng product_images một cách an toàn
        try {
            // Tắt tạm thời khóa ngoại để tránh lỗi khi truncate
            DB::statement('SET FOREIGN_KEY_CHECKS=0');
            ProductImage::truncate();
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
        } catch (\Exception $e) {
            // Nếu không thể truncate, xóa từng bản ghi
            ProductImage::query()->delete();
        }
        
        // Danh sách màu sắc thực tế
        $colors = [
            'Đen' => ['color_code' => 'BLK', 'color_name' => 'den'],
            'Trắng' => ['color_code' => 'WHT', 'color_name' => 'trang'],
            'Đỏ' => ['color_code' => 'RED', 'color_name' => 'do'],
            'Xanh dương' => ['color_code' => 'BLU', 'color_name' => 'xanh-duong'],
            'Xám' => ['color_code' => 'GRY', 'color_name' => 'xam'],
            'Xanh lá' => ['color_code' => 'GRN', 'color_name' => 'xanh-la'],
            'Cam' => ['color_code' => 'ORG', 'color_name' => 'cam'],
            'Vàng' => ['color_code' => 'YLW', 'color_name' => 'vang'],
            'Tím' => ['color_code' => 'PRP', 'color_name' => 'tim'],
            'Hồng' => ['color_code' => 'PNK', 'color_name' => 'hong']
        ];
        
        echo "Bắt đầu tạo hình ảnh sản phẩm...\n";
        
        // Lấy tất cả sản phẩm
        $products = Product::all();
        foreach ($products as $product) {
            echo "Đang xử lý sản phẩm: {$product->name} (ID: {$product->id})...\n";
            
            // Lấy các biến thể màu sắc của sản phẩm này
            try {
                $productVariants = ProductVariant::where('product_id', $product->id)
                    ->select('color')
                    ->distinct()
                    ->get()
                    ->pluck('color')
                    ->toArray();
                    
                echo "  Tìm thấy " . count($productVariants) . " màu sắc cho sản phẩm.\n";
            } catch (\Exception $e) {
                echo "  Lỗi khi lấy biến thể màu sắc: " . $e->getMessage() . "\n";
                $productVariants = [];
            }
            
            $slug = Str::slug($product->name);
            $brandSlug = Str::slug($product->brand->name);
            
            // Nếu sản phẩm không có biến thể màu sắc, thêm hình ảnh mặc định
            if (empty($productVariants)) {
                echo "  Không tìm thấy biến thể màu sắc, sẽ tạo hình ảnh mặc định.\n";
                $this->createDefaultProductImages($product, $brandSlug, $slug);
                continue;
            }
            
            // Tạo hình ảnh cho từng màu sắc của sản phẩm
            $colorIndex = 1; // Đánh số thứ tự màu
            foreach ($productVariants as $colorName) {
                echo "  Đang xử lý màu: {$colorName}\n";
                
                if (isset($colors[$colorName])) {
                    $colorSlug = $colors[$colorName]['color_name'];
                    
                    // Mỗi màu có 2-3 hình ảnh
                    $numImagesPerColor = rand(2, 3);
                    for ($i = 1; $i <= $numImagesPerColor; $i++) {
                        // Đường dẫn hình ảnh bao gồm tên màu sắc để có thể nhận biết
                        $imagePath = "products/{$brandSlug}/{$slug}-{$colorSlug}-{$i}.jpg";
                        
                        // Hình đầu tiên của màu đầu tiên là hình chính
                        $isPrimary = ($colorIndex === 1 && $i === 1);
                        
                        try {
                            ProductImage::create([
                                'product_id' => $product->id,
                                'image_path' => $imagePath,
                                'is_primary' => $isPrimary,
                                'sort_order' => ($colorIndex - 1) * 5 + $i // Thứ tự sắp xếp theo màu
                            ]);
                            echo "    Đã tạo hình ảnh: {$imagePath}\n";
                        } catch (\Exception $e) {
                            echo "    Lỗi khi tạo hình ảnh: " . $e->getMessage() . "\n";
                        }
                    }
                    $colorIndex++;
                } else {
                    echo "    Không tìm thấy màu {$colorName} trong danh sách màu.\n";
                }
            }
            
            echo "  Đã cập nhật hình ảnh cho sản phẩm: {$product->name} với " . ($colorIndex - 1) . " màu sắc\n";
        }
        
        echo "Hoàn thành cập nhật hình ảnh sản phẩm!\n";
    }
    
    /**
     * Tạo hình ảnh mặc định cho sản phẩm không có biến thể màu sắc
     */
    private function createDefaultProductImages($product, $brandSlug, $slug) 
    {
        $numImages = rand(3, 5);
        for ($i = 1; $i <= $numImages; $i++) {
            $imagePath = "products/{$brandSlug}/{$slug}-{$i}.jpg";
            
            try {
                ProductImage::create([
                    'product_id' => $product->id,
                    'image_path' => $imagePath,
                    'is_primary' => ($i === 1), // Hình đầu tiên là hình chính
                    'sort_order' => $i
                ]);
                echo "    Đã tạo hình ảnh mặc định: {$imagePath}\n";
            } catch (\Exception $e) {
                echo "    Lỗi khi tạo hình ảnh mặc định: " . $e->getMessage() . "\n";
            }
        }
        
        echo "    Đã cập nhật hình ảnh mặc định cho sản phẩm: {$product->name}\n";
    }
}