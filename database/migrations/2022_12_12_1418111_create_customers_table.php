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
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->enum('customer_type', ["Retailer","Wholesaler"])->default('Retailer');
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('mobile')->nullable();
            $table->string('whatsapp_number')->nullable();
            $table->string('tax_number')->nullable();
            $table->string('currency_id')->nullable();
            $table->boolean('status')->default(true);
            $table->text('billing_address')->nullable();
            $table->text('shipping_address')->nullable();
            $table->string('opening_balance')->default('0.00');
            $table->date('opening_balance_date')->nullable();
            $table->enum('opening_balance_type', ["to_pay","to_receive"])->default('to_receive');
            $table->string('credit_limit')->nullable();
            $table->boolean('has_credit_limit')->default(false);
            $table->string('photo')->nullable();
            $table->string('file')->nullable();
            $table->string('created_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
