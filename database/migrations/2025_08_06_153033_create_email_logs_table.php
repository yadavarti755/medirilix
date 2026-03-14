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
        Schema::create('email_logs', function (Blueprint $table) {
            $table->id();
            $table->string('recipient_email');
            $table->string('recipient_name')->nullable();
            $table->string('email_type'); // 'password_reset', 'otp', 'application_confirmation', etc.
            $table->string('subject');
            $table->enum('status', ['sent', 'failed'])->default('failed');
            $table->text('error_message')->nullable();
            $table->json('metadata')->nullable(); // Store additional data like OTP, URLs, etc.
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();

            // Indexes for better performance
            $table->index(['recipient_email', 'created_at']);
            $table->index(['email_type', 'status']);
            $table->index('sent_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('email_logs');
    }
};
