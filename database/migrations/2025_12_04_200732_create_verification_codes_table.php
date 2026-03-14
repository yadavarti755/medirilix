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
        Schema::create('verification_codes', function (Blueprint $table) {
            $table->id();

            // Email on which OTP is sent
            $table->string('email_id');

            // OTP code (integer)
            $table->integer('code');

            // Expiry time
            $table->dateTime('expiration_time');

            // 0 = unused, 1 = used
            $table->tinyInteger('otp_used')->default(0);

            $table->timestamps();
            $table->softDeletes();

            // Optional helpful indexes
            $table->index('email_id');
            $table->index('code');
            $table->index('otp_used');
            $table->index('expiration_time');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('verification_codes');
    }
};
