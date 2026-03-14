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
        Schema::create('return_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('order_product_list_id');
            $table->unsignedBigInteger('return_list_id'); // Foreign key to return_reasons
            $table->text('return_description')->nullable();
            $table->string('return_status')->default('RETURN_REQUESTED');
            $table->text('return_pickup_details')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            // Assuming order_product_lists table exists, referencing it. If name allows plural/singular variance, typical Laravel is singular_id -> plural_table
            // Checking assumption: usually 'order_product_lists'. I will not enforce FK on this if I am not 100% sure of table name, 
            // but requirements say "order_product_list_id". I'll assume standard naming but if it fails I might need to correct table name.
            // Safe bet: referencing order_product_lists if it matches user's previous structure pattern. 
            // Let's rely on standard FKs but make them nullable on delete to be safe, or just index them. 
            // User requirement: "order_product_list_id". 

            $table->foreign('return_list_id')->references('id')->on('return_reasons');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('return_requests');
    }
};
