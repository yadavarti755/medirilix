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
        Schema::create('addresses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->integer('type')->default(1); // Work or Home
            $table->string('person_name');
            $table->string('person_contact_number');
            $table->string('person_alt_contact_number')->nullable();
            $table->mediumText('address');
            $table->string('locality');
            $table->string('landmark')->nullable();
            $table->string('city');
            $table->unsignedBigInteger('state');
            $table->unsignedBigInteger('country');
            $table->bigInteger('pincode');
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
        Schema::dropIfExists('addresses');
    }
};
