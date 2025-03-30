<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Review;
use App\Models\Product;
use App\Models\User;

class ReviewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = Product::all();
        $users = User::where('role', 'customer')->get();
        
        $comments = [
            'Tuyệt vời, sản phẩm đúng như mô tả, chất lượng tốt.',
            'Rất hài lòng với đôi giày này, đi rất êm chân.',
            'Kiểu dáng đẹp, đúng kích thước, giao hàng nhanh.',
            'Đế giày tốt, chắc chắn. Đi lâu không bị mỏi chân.',
            'Chất lượng tương xứng với giá tiền, sẽ mua lại lần nữa.',
            'Màu sắc đẹp như hình, form giày vừa vặn.',
            'Giày nhẹ, thoáng khí, phù hợp cho cả ngày dài.',
            'Mới đầu hơi chật nhưng đi một thời gian là vừa chân.',
            'Đẹp, nhưng có vài vết xước nhỏ khi nhận hàng.',
            'Đế giày hơi cứng, cần thời gian để làm quen.',
            'Phải nói là rất đẹp, nhận được nhiều lời khen khi mang đôi giày này.',
            'Đúng size, form chuẩn, giao hàng nhanh. Rất hài lòng.',
        ];
        
        // Mỗi sản phẩm sẽ có 0-5 đánh giá
        foreach ($products as $product) {
            $numReviews = rand(0, 5);
            
            // Đảm bảo không có người dùng đánh giá trùng lặp cho cùng một sản phẩm
            $reviewedUsers = $users->random(min($numReviews, count($users)))->pluck('id')->toArray();
            
            for ($i = 0; $i < $numReviews; $i++) {
                if (isset($reviewedUsers[$i])) {
                    Review::create([
                        'product_id' => $product->id,
                        'user_id' => $reviewedUsers[$i],
                        'rating' => rand(3, 5), // Chủ yếu đánh giá tích cực từ 3-5 sao
                        'comment' => $comments[array_rand($comments)],
                        'created_at' => now()->subDays(rand(1, 30)), // Đánh giá trong vòng 1 tháng qua
                    ]);
                }
            }
        }
    }
}