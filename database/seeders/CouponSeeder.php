<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Coupon;
use Carbon\Carbon;

class CouponSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Tạo một số mã giảm giá mẫu
        $coupons = [
            [
                'code' => 'CHAOMUNG',
                'type' => 'fixed',
                'value' => 100000,
                'min_order_value' => 500000,
                'start_date' => Carbon::now()->subDays(10),
                'end_date' => Carbon::now()->addDays(20),
                'active' => true,
            ],
            [
                'code' => 'GIAMGIA20',
                'type' => 'percent',
                'value' => 20,
                'min_order_value' => 1000000,
                'start_date' => Carbon::now()->subDays(5),
                'end_date' => Carbon::now()->addDays(10),
                'active' => true,
            ],
            [
                'code' => 'FREESHIP',
                'type' => 'fixed',
                'value' => 50000,
                'min_order_value' => 300000,
                'start_date' => Carbon::now(),
                'end_date' => Carbon::now()->addMonth(),
                'active' => true,
            ],
            [
                'code' => 'TETNGUYENDAN',
                'type' => 'percent',
                'value' => 15,
                'min_order_value' => 800000,
                'start_date' => Carbon::now()->addMonth(),
                'end_date' => Carbon::now()->addMonths(2),
                'active' => true,
            ],
            [
                'code' => 'SALE10',
                'type' => 'percent',
                'value' => 10,
                'min_order_value' => 0,
                'start_date' => Carbon::now()->subDays(15),
                'end_date' => Carbon::now()->addDay(),
                'active' => true,
            ],
        ];

        foreach ($coupons as $couponData) {
            Coupon::create($couponData);
        }
    }
}