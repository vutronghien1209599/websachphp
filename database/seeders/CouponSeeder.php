<?php

namespace Database\Seeders;

use App\Models\Coupon;
use Illuminate\Database\Seeder;

class CouponSeeder extends Seeder
{
    public function run()
    {
        Coupon::create([
            'code' => 'WELCOME10',
            'type' => 'percentage',
            'value' => 10,
            'min_order_amount' => 100000,
            'start_date' => now(),
            'end_date' => now()->addMonths(1),
        ]);

        Coupon::create([
            'code' => 'SAVE50K',
            'type' => 'fixed',
            'value' => 50000,
            'min_order_amount' => 500000,
            'start_date' => now(),
            'end_date' => now()->addMonths(1),
        ]);
    }
} 