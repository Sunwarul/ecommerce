<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductVariationAttributesTable extends Migration
{
    public function up()
    {
        Schema::create('product_variation_attributes', function (Blueprint $table) {
            $table->id();

            $table->foreignId('product_id')
                ->constrained('products')
                ->onDelete('CASCADE');

            $table->foreignId('variation_id')->nullable()->constrained('product_variations')->onDelete('CASCADE');
            $table->foreignId('attribute_id')->constrained('product_attributes')->onDelete('CASCADE');
            $table->foreignId('attribute_value_id')->constrained('product_attribute_values')->onDelete('CASCADE');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('product_variation_attributes');
    }
}
