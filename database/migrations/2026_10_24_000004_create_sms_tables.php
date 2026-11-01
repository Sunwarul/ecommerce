<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sms_gateways', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('driver', 30); // bulksmsbd | smsnetbd | greenweb | twilio | vonage | generic | log
            $table->text('credentials')->nullable(); // encrypted json
            $table->string('sender_id', 50)->nullable();
            $table->boolean('is_default')->default(false);
            $table->boolean('is_active')->default(true);
            $table->decimal('balance', 14, 4)->nullable();
            $table->timestamp('balance_checked_at')->nullable();
            $table->timestamps();
        });

        Schema::create('sms_templates', function (Blueprint $table) {
            $table->id();
            $table->string('key', 60)->unique(); // sale_completed | payment_received | installment_reminder | ...
            $table->string('name');
            $table->text('body');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('sms_campaigns', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('message');
            $table->unsignedInteger('recipients_count')->default(0);
            $table->unsignedInteger('sent_count')->default(0);
            $table->unsignedInteger('failed_count')->default(0);
            $table->string('status', 20)->default('processing'); // processing | completed
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('sms_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sms_gateway_id')->nullable()->constrained('sms_gateways')->nullOnDelete();
            $table->foreignId('sms_campaign_id')->nullable()->constrained('sms_campaigns')->nullOnDelete();
            $table->foreignId('customer_id')->nullable()->constrained('customers')->nullOnDelete();
            $table->string('phone', 30)->index();
            $table->text('message');
            $table->string('type', 20)->default('transactional')->index(); // transactional | promotional | test
            $table->string('event', 60)->nullable()->index();
            $table->nullableMorphs('smsable');
            $table->unsignedSmallInteger('segments')->default(1);
            $table->string('status', 20)->default('queued')->index(); // queued | sent | failed
            $table->string('provider_message_id')->nullable();
            $table->text('response')->nullable();
            $table->text('error')->nullable();
            $table->foreignId('sent_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sms_logs');
        Schema::dropIfExists('sms_campaigns');
        Schema::dropIfExists('sms_templates');
        Schema::dropIfExists('sms_gateways');
    }
};
