<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('product_stocks', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('variation_id')->nullable();
            $table->unsignedBigInteger('warehouse_id');

            $table->decimal('quantity', 12, 2)->default(0);
            $table->decimal('alert_quantity', 12, 2)->nullable();

            $table->timestamps();

            // 🔗 Foreign Keys
            $table->foreign('product_id')
                ->references('id')
                ->on('products')
                ->cascadeOnDelete();

            $table->foreign('variation_id')
                ->references('id')
                ->on('product_variations')
                ->cascadeOnDelete();

            $table->foreign('warehouse_id')
                ->references('id')
                ->on('warehouses')
                ->cascadeOnDelete();

            // 🚫 Prevent duplicate stock rows
            $table->unique(
                ['product_id', 'variation_id', 'warehouse_id'],
                'product_stock_unique'
            );

            // ⚡ Helpful indexes
            $table->index(['product_id', 'warehouse_id']);
            $table->index(['variation_id', 'warehouse_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('product_stocks');
    }
};
