<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();

            $table->foreignId('category_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('tax_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('brand_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('unit_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->cascadeOnDelete();

            $table->string('name');
            $table->string('slug')->unique();

            $table->string('thumbnail')->nullable();
            $table->json('images')->nullable();

            $table->string('sku')->nullable()->unique();
            $table->string('barcode')->nullable()->unique();
            $table->string('code')->nullable()->unique();

            // Pricing
            $table->decimal('base_price', 10, 2);
            $table->decimal('base_discount_price', 10, 2)->nullable();

            // Product type
            $table->enum('type', ['simple', 'variable'])->default('simple');

            // Physical data
            $table->decimal('weight', 10, 2)->nullable();
            $table->json('dimensions')->nullable();
            $table->json('materials')->nullable();

            // Content
            $table->longText('description')->nullable();
            $table->longText('additional_info')->nullable();

            $table->boolean('is_active')->default(true);

            // SEO
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->string('meta_keywords')->nullable();

            $table->softDeletes();
            $table->timestamps();

            $table->index(['slug', 'sku', 'code', 'type']);
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('products');
    }
};
