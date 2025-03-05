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
            $table->unsignedBigInteger('category_id')->nullable();
            $table->unsignedBigInteger('sub_category_id')->nullable();
            $table->unsignedBigInteger('tax_id')->nullable();
            $table->unsignedBigInteger('brand_id')->nullable();
            $table->unsignedBigInteger('tag_id')->nullable();
            $table->unsignedBigInteger('added_by')->nullable();
            $table->string('product_name');
            $table->float('price');
            $table->float('discount_price')->nullable();
            $table->string('code')->nullable()->default();
            $table->string('title')->nullable();
            $table->string('slug')->default();
            $table->string('dimantion')->nullable();
            $table->string('weight')->nullable();
            $table->string('sku')->nullable();
            $table->string('meterials')->nullable();
            $table->longText('description')->nullable();
            $table->longText('other_info')->nullable();
            $table->boolean('is_active')->default(true);
            $table->string('pro_photo')->nullable()->default('product_photo.jpg');
            $table->foreign(['category_id'])->references(['id'])->on('categories')->onDelete('CASCADE');
            $table->foreign(['sub_category_id'])->references(['id'])->on('sub_categories')->onDelete('CASCADE');
            $table->foreign(['tax_id'])->references(['id'])->on('taxes')->onDelete('CASCADE');
            $table->foreign(['brand_id'])->references(['id'])->on('brands')->onDelete('CASCADE');
            $table->foreign(['tag_id'])->references(['id'])->on('tags')->onDelete('CASCADE');
            $table->foreign(['added_by'])->references(['id'])->on('users')->onDelete('CASCADE');
            $table->softDeletes();
            $table->timestamps();
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
