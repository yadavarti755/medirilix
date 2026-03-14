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
        Schema::create('products', function (Blueprint $table) {
            $table->id();

            // Core
            $table->unsignedBigInteger('category_id'); // keep as ID only, no FK
            $table->string('slug');
            $table->string('name');

            // Pricing
            $table->string('mrp')->nullable();
            $table->string('selling_price')->nullable();
            $table->string('upc')->nullable();

            // Product attributes
            $table->string('brand_id')->nullable();
            $table->string('type_id')->nullable();
            $table->string('material_id')->nullable();
            $table->string('intended_use_id')->nullable();
            $table->string('model')->nullable();
            $table->string('mpn')->nullable();
            $table->date('expiration_date')->nullable();
            $table->integer('unit_quantity')->nullable();
            $table->string('unit_type_id')->nullable();
            $table->tinyText('california_prop_65_warning')->nullable(); // Changed to TINYTEXT
            $table->string('country_of_origin')->nullable(); // Changed to VARCHAR(100) -> String

            // Stock
            $table->integer('quantity'); // Removed nullable as per SQL "NOT NULL"
            $table->integer('available_quantity')->default(1);
            $table->integer('stock_availability')->default(1);

            // Listing & visibility
            $table->integer('product_listing_type')->nullable()->default(0);
            $table->tinyInteger('is_published')->default(0); // Changed default to 0 as per SQL

            // Return Policy
            $table->integer('return_till_days')->nullable();
            $table->longText('return_description')->nullable();

            // Content
            $table->mediumText('description')->nullable();
            $table->string('featured_image')->default('no-image.png');

            // SEO
            $table->string('meta_keywords')->nullable();
            $table->string('meta_description')->nullable();

            // Analytics
            $table->integer('view_count')->default(0);

            // Audit
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
        Schema::dropIfExists('products');
    }
};
