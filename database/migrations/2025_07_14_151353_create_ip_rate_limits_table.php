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
        Schema::create('ip_rate_limits', function (Blueprint $table) {
            $table->id();
            $table->string('ip_address', 45); // IPv6 support
            $table->string('action'); // 'otp_verification'
            $table->integer('attempts')->default(0);
            $table->timestamp('first_attempt_at');
            $table->timestamp('last_attempt_at')->nullable();
            $table->timestamp('blocked_until')->nullable();
            $table->timestamps();

            $table->index(['ip_address', 'action']);
            $table->index('blocked_until');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ip_rate_limits');
    }
};
