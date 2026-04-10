<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('order_product_shipping_details', function (Blueprint $table) {
            $table->string('dhl_tracking_id')->nullable()->after('shipping_details');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_product_shipping_details', function (Blueprint $table) {
            $table->dropColumn('dhl_tracking_id');
        });
    }
};
