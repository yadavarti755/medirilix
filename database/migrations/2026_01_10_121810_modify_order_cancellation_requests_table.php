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
        Schema::table('order_cancellation_requests', function (Blueprint $table) {
            $table->unsignedBigInteger('cancel_reason_id')->nullable()->after('user_id');
            $table->dropColumn('title');

            $table->foreign('cancel_reason_id')->references('id')->on('cancel_reasons')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_cancellation_requests', function (Blueprint $table) {
            $table->string('title')->nullable()->after('user_id');
            $table->dropForeign(['cancel_reason_id']);
            $table->dropColumn('cancel_reason_id');
        });
    }
};
