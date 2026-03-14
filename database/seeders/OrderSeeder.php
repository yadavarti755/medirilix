<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        $userId = 1;
        $createdBy = 1;

        for ($i = 1; $i <= 5; $i++) {

            $orderNumber = 'ORD-' . now()->format('Ymd') . '-' . str_pad($i, 4, '0', STR_PAD_LEFT);
            $subtotal = rand(500, 3000);
            $additionalCharges = rand(0, 200);
            $total = $subtotal + $additionalCharges + (($subtotal * 18) / 100);

            /* ======================
             | ORDERS
             ====================== */
            DB::table('orders')->insert([
                'user_id'            => $userId,
                'order_number'       => $orderNumber,
                'order_date'         => Carbon::now()->subDays(rand(1, 10)),
                'subtotal_price'     => $subtotal,
                'additional_charges' => $additionalCharges,
                'total_price'        => round($total, 2),
                'invoice_number'     => 'INV-' . strtoupper(Str::random(8)),
                'payment_type'       => collect(['COD', 'ONLINE'])->random(),
                'order_status'       => collect(['PLACED', 'CONFIRMED', 'SHIPPED'])->random(),
                'payment_status'     => collect(['PENDING', 'PAID'])->random(),
                'remarks'            => 'Test order seeded data',
                'created_by'         => $createdBy,
                'updated_by'         => null,
                'created_at'         => now(),
                'updated_at'         => now(),
            ]);

            /* ======================
             | ORDER PRODUCTS
             ====================== */
            DB::table('order_product_lists')->insert([
                'user_id'                 => $userId,
                'order_number'            => $orderNumber,
                'product_id'              => rand(2, 19), // from your products table
                'product_featured_image'  => 'no-image.png',
                'product_name'            => 'Sample Product ' . $i,
                'size'                    => null,
                'material'                => null,
                'price'                   => $subtotal,
                'quantity'                => rand(1, 3),
                'total_price'             => $subtotal,
                'created_by'              => $createdBy,
                'updated_by'              => null,
                'created_at'              => now(),
                'updated_at'              => now(),
            ]);

            /* ======================
             | ORDER HISTORY
             ====================== */
            DB::table('order_histories')->insert([
                'user_id'              => $userId,
                'order_number'         => $orderNumber,
                'order_status'         => 'PLACED',
                'status_changed_date'  => now(),
                'remarks'              => 'Order placed successfully',
                'created_by'           => $createdBy,
                'updated_by'           => null,
                'created_at'           => now(),
                'updated_at'           => now(),
            ]);

            /* ======================
             | ORDER ADDRESS
             ====================== */
            DB::table('order_addresses')->insert([
                'user_id'                     => $userId,
                'order_number'                => $orderNumber,
                'person_name'                 => 'John Doe',
                'person_contact_number'       => '9876543210',
                'person_alt_contact_number'   => null,
                'address'                     => '123, Sample Street',
                'locality'                    => 'Downtown',
                'landmark'                    => 'Near City Mall',
                'city'                        => 'New York',
                'state'                       => 1,
                'country'                     => 1,
                'pincode'                     => 100001,
                'created_by'                  => $createdBy,
                'updated_by'                  => null,
                'created_at'                  => now(),
                'updated_at'                  => now(),
            ]);
        }
    }
}
