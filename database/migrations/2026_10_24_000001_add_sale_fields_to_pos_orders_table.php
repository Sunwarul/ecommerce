<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pos_orders', function (Blueprint $table) {
            // warehouse_id / due_amount were written by the controller but never existed,
            // so voids restocked into a NULL warehouse and due was silently dropped.
            if (! Schema::hasColumn('pos_orders', 'warehouse_id')) {
                $table->unsignedBigInteger('warehouse_id')->nullable()->after('branch_id')->index();
            }
            if (! Schema::hasColumn('pos_orders', 'discount_id')) {
                $table->unsignedBigInteger('discount_id')->nullable()->after('customer_id');
            }

            // regular | installment | emi
            $table->string('sale_type', 30)->default('regular')->after('status')->index();

            $table->decimal('emi_charge_amount', 12, 2)->default(0)->after('tax_amount');
            $table->decimal('installment_charge_amount', 12, 2)->default(0)->after('emi_charge_amount');
            $table->decimal('late_fee_amount', 12, 2)->default(0)->after('installment_charge_amount');
            $table->decimal('due_amount', 12, 2)->default(0)->after('paid_amount');

            $table->text('notes')->nullable()->after('warranty_info');
        });

        // Backfill warehouse from the POS session and due from totals.
        DB::table('pos_orders')
            ->whereNull('warehouse_id')
            ->orderBy('id')
            ->chunkById(500, function ($orders) {
                foreach ($orders as $order) {
                    $warehouseId = DB::table('pos_sessions')->where('id', $order->pos_session_id)->value('warehouse_id');
                    DB::table('pos_orders')->where('id', $order->id)->update([
                        'warehouse_id' => $warehouseId,
                        'due_amount' => $order->status === 'completed'
                            ? max(0, (float) $order->total_amount - (float) $order->paid_amount)
                            : 0,
                    ]);
                }
            });
    }

    public function down(): void
    {
        Schema::table('pos_orders', function (Blueprint $table) {
            $table->dropColumn([
                'sale_type', 'emi_charge_amount', 'installment_charge_amount',
                'late_fee_amount', 'due_amount', 'notes',
            ]);
        });
    }
};
