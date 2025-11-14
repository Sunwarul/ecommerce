<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->nullable()->constrained('categories')->onDelete('CASCADE');
            $table->foreignId('brand_id')->nullable()->constrained('brands')->onDelete('CASCADE');
            $table->foreignId('unit_id')->nullable()->constrained('units')->nullOnDelete();
            $table->foreignId('tax_id')->nullable()->constrained('taxes')->onDelete('CASCADE');
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('CASCADE');

            $table->string('name');
            $table->string('slug')->unique();
            $table->string('thumbnail')->nullable();
            $table->text('images')->nullable();
            $table->text('description')->nullable();

            $table->string('sku')->nullable()->unique();
            $table->string('barcode')->nullable()->unique();
            $table->string('code')->nullable()->unique();

            // Base price (for simple products)
            $table->decimal('base_price', 10, 2);
            $table->decimal('base_discount_price', 10, 2)->nullable();

            // Stock will be managed at variation level for variable products
            $table->integer('stock_quantity')->default(0)->nullable();
            $table->enum('stock_status', ['in_stock', 'out_of_stock', 'pre_order'])->default('in_stock');

            // Product type
            $table->enum('type', ['simple', 'variable'])->default('simple');

            $table->string('weight')->nullable();
            $table->string('dimensions')->nullable();
            $table->string('materials')->nullable();

            $table->boolean('is_active')->default(true);
            $table->boolean('has_variants')->default(false);

            // SEO fields
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
