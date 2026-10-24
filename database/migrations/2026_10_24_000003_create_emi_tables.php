<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('emi_banks', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('short_name', 50)->nullable();
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        // Tenure-wise rates agreed with each bank
        Schema::create('emi_bank_tenures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('emi_bank_id')->constrained('emi_banks')->cascadeOnDelete();
            $table->unsignedSmallInteger('months');
            $table->decimal('customer_charge_percent', 6, 3)->default(0); // added to customer bill
            $table->decimal('merchant_charge_percent', 6, 3)->default(0); // deducted by bank at settlement
            $table->decimal('min_amount', 12, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->unique(['emi_bank_id', 'months']);
        });

        Schema::create('emi_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('pos_orders')->cascadeOnDelete();
            $table->foreignId('pos_payment_id')->nullable()->constrained('pos_payments')->nullOnDelete();
            $table->foreignId('emi_bank_id')->constrained('emi_banks');
            $table->foreignId('emi_bank_tenure_id')->nullable()->constrained('emi_bank_tenures')->nullOnDelete();
            $table->unsignedBigInteger('branch_id')->nullable()->index();
            $table->unsignedSmallInteger('months');

            $table->decimal('principal_amount', 12, 2);   // part of the bill put on EMI
            $table->decimal('customer_charge_percent', 6, 3)->default(0);
            $table->decimal('customer_charge_amount', 12, 2)->default(0);
            $table->decimal('card_amount', 12, 2);        // swiped = principal + customer charge
            $table->decimal('merchant_charge_percent', 6, 3)->default(0);
            $table->decimal('merchant_charge_amount', 12, 2)->default(0);
            $table->decimal('net_receivable', 12, 2);     // card_amount - merchant charge
            $table->decimal('monthly_amount', 12, 2);     // what the customer pays the bank monthly

            $table->string('card_holder_name')->nullable();
            $table->string('card_last4', 4)->nullable();
            $table->string('approval_code', 50)->nullable();
            $table->string('terminal_ref', 100)->nullable();

            $table->string('settlement_status', 20)->default('pending')->index(); // pending | settled | cancelled
            $table->decimal('settled_amount', 12, 2)->nullable();
            $table->date('settled_at')->nullable();
            $table->string('settlement_ref', 100)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('emi_transactions');
        Schema::dropIfExists('emi_bank_tenures');
        Schema::dropIfExists('emi_banks');
    }
};
