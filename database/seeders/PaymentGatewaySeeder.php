<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PaymentGatewaySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $gateways = [
            [
                'gateway_name' => 'PayPal',
                'client_id_or_key' => 'YOUR_PAYPAL_CLIENT_ID',
                'client_secret' => 'YOUR_PAYPAL_CLIENT_SECRET',
                'is_active' => true,
                'created_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'gateway_name' => 'Razorpay',
                'client_id_or_key' => 'YOUR_RAZORPAY_KEY',
                'client_secret' => 'YOUR_RAZORPAY_SECRET',
                'is_active' => true,
                'created_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ];

        // Ensure we don't have duplicates if running multiple times (upsert on gateway_name if possible, but id is safer. we'll truncate or delete first if needed, but upsert is better)
        // Since we don't have a unique key on gateway_name defined in migration explicitly? 
        // Migration probably has id. Let's just use insertOrIgnore or check first.
        // Actually, user said table was dropped and recreated, so it's empty.

        foreach ($gateways as $gateway) {
            DB::table('payment_gateways')->updateOrInsert(
                ['gateway_name' => $gateway['gateway_name']],
                $gateway
            );
        }
    }
}
