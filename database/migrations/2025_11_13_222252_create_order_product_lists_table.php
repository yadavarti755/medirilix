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
        Schema::create('order_product_lists', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('order_number');
            $table->unsignedBigInteger('product_id');
            $table->string('product_featured_image', 190)->default('no-image.png');
            $table->string('product_name');
            $table->integer('size')->nullable();
            $table->integer('material')->nullable();
            $table->string('price');
            $table->integer('quantity');
            $table->string('total_price');
            $table->string('product_order_status');
            $table->datetime('status_changed_date')->nullable();
            $table->bigInteger('status_changed_by')->nullable();
            $table->text('remarks')->nullable();
            $table->text('cancel_reason')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_product_lists');
    }
};
