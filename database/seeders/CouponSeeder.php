<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Coupon;
use Carbon\Carbon;

class CouponSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Coupon::create([
            'code' => 'WELCOME10',
            'description' => '10% off for new users',
            'discount_type' => 'percentage',
            'value' => 10,
            'is_active' => true,
        ]);

        Coupon::create([
            'code' => 'FLAT50',
            'description' => 'Flat 50 discount on orders above 500',
            'discount_type' => 'fixed',
            'value' => 50,
            'min_spend' => 500,
            'is_active' => true,
        ]);

        Coupon::create([
            'code' => 'LIMITED',
            'description' => 'Limited usage coupon',
            'discount_type' => 'percentage',
            'value' => 20,
            'usage_limit_per_coupon' => 10,
            'is_active' => true,
        ]);

        Coupon::create([
            'code' => 'EXPIRED',
            'description' => 'Expired coupon',
            'discount_type' => 'fixed',
            'value' => 100,
            'end_date' => Carbon::yesterday(),
            'is_active' => true,
        ]);

        $coupon = Coupon::create([
            'code' => 'FUTURE',
            'description' => 'Future coupon',
            'discount_type' => 'fixed',
            'value' => 100,
            'start_date' => Carbon::tomorrow(),
            'is_active' => true,
        ]);

        $restrictedCoupon = Coupon::create([
            'code' => 'RESTRICTED',
            'description' => 'Restricted to first product',
            'discount_type' => 'percentage',
            'value' => 50,
            'is_active' => true,
        ]);
        // Attach first product if any exist
        $product = \App\Models\Product::first();
        if ($product) {
            $restrictedCoupon->products()->attach($product->id);
        }
    }
}
