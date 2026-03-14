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
        Schema::create('payu_responses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('order_number');
            $table->string('mihpayid');
            $table->string('mode');
            $table->string('status');
            $table->string('unmappedstatus');
            $table->string('key');
            $table->string('txnid');
            $table->string('amount');
            $table->string('cardcategory');
            $table->string('discount');
            $table->string('net_amount_debit');
            $table->string('addedon');
            $table->string('productinfo');
            $table->string('firstname');
            $table->string('lastname');
            $table->string('email');
            $table->string('phone');
            $table->string('address1');
            $table->string('address2');
            $table->string('city');
            $table->string('state');
            $table->string('country');
            $table->string('zipcode');
            $table->string('payment_source');
            $table->string('pg_type');
            $table->string('bank_ref_num');
            $table->string('bankcode');
            $table->string('error');
            $table->string('error_message');
            $table->string('name_on_card');
            $table->string('cardnum');
            $table->string('message');
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
        Schema::dropIfExists('payu_responses');
    }
};
