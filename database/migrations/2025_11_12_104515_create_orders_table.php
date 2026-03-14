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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('order_number');
            $table->timestamp('order_date')->useCurrent()->useCurrentOnUpdate();
            $table->string('subtotal_price');
            $table->string('additional_charges')->nullable();
            $table->string('total_price');
            $table->string('invoice_number')->nullable();
            $table->string('payment_type');
            $table->string('order_status');
            $table->string('payment_status');
            $table->timestamp('order_status_changed_date')->nullable();
            $table->text('cancel_reason')->nullable();
            $table->text('remarks')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
