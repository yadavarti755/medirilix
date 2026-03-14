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
        if (!Schema::hasTable('order_cancellation_request_messages')) {
            Schema::create('order_cancellation_request_messages', function (Blueprint $table) {
                $table->id();
                $table->foreignId('order_cancellation_request_id')
                    ->constrained('order_cancellation_requests', 'ocr_messages_req_id_fk')
                    ->onDelete('cascade');
                $table->foreignId('message_by')->constrained('users');
                $table->text('message');
                $table->softDeletes();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_cancellation_request_messages');
    }
};
