<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Reusable plan templates (e.g. "6 months 0%", "12 months 10% flat")
        Schema::create('installment_plans', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->unsignedSmallInteger('number_of_installments');
            $table->string('frequency', 20)->default('monthly'); // weekly | biweekly | monthly | custom
            $table->unsignedSmallInteger('interval_days')->nullable(); // for custom
            $table->string('interest_type', 20)->default('none'); // none | flat | reducing
            $table->decimal('interest_rate', 8, 4)->default(0); // flat: % of financed for whole term, reducing: annual %
            $table->decimal('min_down_payment_percent', 5, 2)->default(0);
            $table->string('processing_fee_type', 10)->default('fixed'); // fixed | percent
            $table->decimal('processing_fee', 12, 2)->default(0);
            $table->string('late_fee_type', 10)->default('fixed'); // fixed | percent (of installment)
            $table->decimal('late_fee_amount', 12, 2)->default(0);
            $table->unsignedSmallInteger('grace_days')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        // One agreement per order
        Schema::create('installment_sales', function (Blueprint $table) {
            $table->id();
            $table->string('agreement_no', 50)->unique();
            $table->foreignId('order_id')->constrained('pos_orders')->cascadeOnDelete();
            $table->foreignId('customer_id')->constrained('customers');
            $table->unsignedBigInteger('branch_id')->nullable()->index();
            $table->foreignId('installment_plan_id')->nullable()->constrained('installment_plans')->nullOnDelete();

            $table->decimal('sale_amount', 12, 2);          // order total before installment charges
            $table->decimal('down_payment', 12, 2)->default(0);
            $table->decimal('processing_fee', 12, 2)->default(0);
            $table->decimal('financed_amount', 12, 2);      // principal spread over installments
            $table->string('interest_type', 20)->default('none');
            $table->decimal('interest_rate', 8, 4)->default(0);
            $table->decimal('interest_amount', 12, 2)->default(0);
            $table->decimal('total_payable', 12, 2);        // financed + interest (sum of schedules)

            $table->unsignedSmallInteger('number_of_installments');
            $table->string('frequency', 20)->default('monthly');
            $table->unsignedSmallInteger('interval_days')->nullable();
            $table->decimal('installment_amount', 12, 2);
            $table->date('first_due_date');

            $table->string('late_fee_type', 10)->default('fixed');
            $table->decimal('late_fee_amount', 12, 2)->default(0);
            $table->unsignedSmallInteger('grace_days')->default(0);

            $table->decimal('paid_amount', 12, 2)->default(0);       // collected against schedules (excl. late fee)
            $table->decimal('late_fee_charged', 12, 2)->default(0);
            $table->decimal('late_fee_paid', 12, 2)->default(0);

            $table->string('status', 20)->default('active')->index(); // active | completed | defaulted | cancelled

            // KYC / guarantor (common requirement for hire-purchase)
            $table->string('customer_nid', 50)->nullable();
            $table->string('guarantor_name')->nullable();
            $table->string('guarantor_phone', 30)->nullable();
            $table->string('guarantor_nid', 50)->nullable();
            $table->text('guarantor_address')->nullable();
            $table->text('notes')->nullable();

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });

        Schema::create('installment_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('installment_sale_id')->constrained('installment_sales')->cascadeOnDelete();
            $table->unsignedSmallInteger('installment_no');
            $table->date('due_date')->index();
            $table->decimal('principal', 12, 2)->default(0);
            $table->decimal('interest', 12, 2)->default(0);
            $table->decimal('amount', 12, 2);              // principal + interest
            $table->decimal('paid_amount', 12, 2)->default(0);
            $table->decimal('late_fee', 12, 2)->default(0);
            $table->decimal('late_fee_paid', 12, 2)->default(0);
            $table->string('status', 20)->default('pending')->index(); // pending | partial | paid | cancelled
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('reminded_at')->nullable();
            $table->timestamps();
        });

        Schema::create('installment_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('installment_sale_id')->constrained('installment_sales')->cascadeOnDelete();
            $table->foreignId('installment_schedule_id')->nullable()->constrained('installment_schedules')->nullOnDelete();
            $table->foreignId('pos_payment_id')->nullable()->constrained('pos_payments')->nullOnDelete();
            $table->decimal('amount', 12, 2)->default(0);   // towards installment
            $table->decimal('late_fee', 12, 2)->default(0); // towards late fee
            $table->foreignId('received_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('installment_payments');
        Schema::dropIfExists('installment_schedules');
        Schema::dropIfExists('installment_sales');
        Schema::dropIfExists('installment_plans');
    }
};
