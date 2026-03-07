<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Add individual indexes for better query performance
        Schema::table('products', function (Blueprint $table) {
            $table->index('name')->after('slug');
            $table->index('sku')->after('name');
            $table->index('barcode')->after('sku');
            $table->index('category_id')->after('barcode');
            $table->index('brand_id')->after('category_id');
            $table->index('type')->after('brand_id');
            $table->index('is_active')->after('type');
            $table->index('base_price')->after('is_active');
            $table->index('created_at')->after('updated_at');
        });

        // Index for product_stocks table
        Schema::table('product_stocks', function (Blueprint $table) {
            $table->index('product_id')->after('product_id');
            $table->index('warehouse_id')->after('warehouse_id');
            $table->index('quantity')->after('quantity');
            $table->index('alert_quantity')->after('quantity');
        });

        // Index for pos_orders table
        Schema::table('pos_orders', function (Blueprint $table) {
            $table->index('status')->after('status');
            $table->index('payment_status')->after('payment_status');
            $table->index('branch_id')->after('branch_id');
            $table->index('created_at')->after('updated_at');
        });
    }

    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex(['name']);
            $table->dropIndex(['sku']);
            $table->dropIndex(['barcode']);
            $table->dropIndex(['category_id']);
            $table->dropIndex(['brand_id']);
            $table->dropIndex(['type']);
            $table->dropIndex(['is_active']);
            $table->dropIndex(['base_price']);
            $table->dropIndex(['created_at']);
        });

        Schema::table('product_stocks', function (Blueprint $table) {
            $table->dropIndex(['product_id']);
            $table->dropIndex(['warehouse_id']);
            $table->dropIndex(['quantity']);
            $table->dropIndex(['alert_quantity']);
        });

        Schema::table('pos_orders', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropIndex(['payment_status']);
            $table->dropIndex(['branch_id']);
            $table->dropIndex(['created_at']);
        });
    }
};
