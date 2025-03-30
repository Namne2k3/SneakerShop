<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\ProductVariant;

class ProductVariantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = Product::all();
        
        // Common sizes for shoes
        $sizes = ['US 7', 'US 8', 'US 8.5', 'US 9', 'US 9.5', 'US 10', 'US 10.5', 'US 11', 'US 12'];
        $kidsSizes = ['US 3Y', 'US 3.5Y', 'US 4Y', 'US 4.5Y', 'US 5Y', 'US 5.5Y', 'US 6Y'];
        $toddlerSizes = ['US 10C', 'US 11C', 'US 12C', 'US 13C', 'US 1Y', 'US 2Y'];
        
        // Common colors in Vietnamese with color codes for SKU
        $colors = [
            'Đen' => 'BLK',
            'Trắng' => 'WHT',
            'Đỏ' => 'RED',
            'Xanh dương' => 'BLU',
            'Xám' => 'GRY',
            'Xanh lá' => 'GRN',
            'Cam' => 'ORG',
            'Vàng' => 'YLW',
            'Tím' => 'PRP',
            'Hồng' => 'PNK'
        ];
        
        $colorNames = array_keys($colors);
        
        foreach ($products as $product) {
            // Determine if it's a kids or toddler product
            $isKidsProduct = $product->categories->contains(function ($category) {
                return $category->name === 'Giày bé trai' || $category->name === 'Giày bé gái';
            });
            
            $isToddlerProduct = $product->categories->contains(function ($category) {
                return $category->name === 'Giày trẻ mới biết đi';
            });
            
            // Select appropriate sizes based on product type
            $productSizes = $isToddlerProduct ? $toddlerSizes : ($isKidsProduct ? $kidsSizes : $sizes);
            
            // Select 2-3 random colors for this product
            $numColors = rand(2, 3);
            $colorKeys = array_rand($colorNames, $numColors);
            $productColors = [];
            
            // Ensure $colorKeys is always an array
            if (!is_array($colorKeys)) {
                $colorKeys = [$colorKeys];
            }
            
            // Get selected color names
            foreach ($colorKeys as $key) {
                $productColors[] = $colorNames[$key];
            }
            
            // Create variants for each size and color combination
            foreach ($productSizes as $size) {
                foreach ($productColors as $color) {
                    // Create a unique SKU using ASCII characters only
                    $productCode = strtoupper(substr(str_replace(' ', '', $product->name), 0, 3));
                    $colorCode = $colors[$color]; // Use the color code instead of Vietnamese character
                    $sizeCode = str_replace(['US', ' ', '.'], '', $size);
                    $uniqueCode = rand(1000, 9999);
                    
                    $sku = "{$productCode}-{$colorCode}-{$sizeCode}-{$uniqueCode}";
                    
                    // Random stock between 5 and 30
                    $stock = rand(5, 30);
                    
                    // Random additional price based on size (larger sizes may cost more)
                    $additionalPrice = 0;
                    if (strpos($size, '11') !== false || strpos($size, '12') !== false) {
                        $additionalPrice = 120000;
                    }
                    
                    ProductVariant::create([
                        'product_id' => $product->id,
                        'size' => $size,
                        'color' => $color,
                        'sku' => $sku,
                        'stock' => $stock,
                        'additional_price' => $additionalPrice,
                    ]);
                }
            }
        }
    }
}