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
        Schema::create('sms_logs', function (Blueprint $table) {
            $table->id();
            $table->string('recipient_sms');
            $table->string('recipient_name')->nullable();
            $table->string('sms_type'); // 'password_reset', 'otp', 'application_confirmation', etc.
            $table->string('subject');
            $table->enum('status', ['sent', 'failed'])->default('failed');
            $table->text('error_message')->nullable();
            $table->json('metadata')->nullable(); // Store additional data like OTP, URLs, etc.
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();

            // Indexes for better performance
            $table->index(['recipient_sms', 'created_at']);
            $table->index(['sms_type', 'status']);
            $table->index('sent_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sms_logs');
    }
};
