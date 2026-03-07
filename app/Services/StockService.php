<?php

namespace App\Services;

use App\Models\ProductStock;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;

class StockService
{
    public function stockIn(array $data): StockMovement
    {
        return DB::transaction(function () use ($data) {
            $movement = StockMovement::create($data);

            $branchId = $data['branch_id'] ?? null;

            $stock = ProductStock::firstOrCreate([
                'product_id' => $data['product_id'],
                'variation_id' => $data['variation_id'] ?? null,
                'warehouse_id' => $data['to_warehouse_id'],
                'branch_id' => $branchId,
            ], [
                'quantity' => 0,
                'alert_quantity' => null,
            ]);

            $stock->quantity += $data['quantity'];
            $stock->save();

            return $movement;
        });
    }

    public function stockOut(array $data): StockMovement
    {
        return DB::transaction(function () use ($data) {
            $movement = StockMovement::create($data);

            $warehouseId = $data['from_warehouse_id'];
            $productId = $data['product_id'];
            $variationId = $data['variation_id'] ?? null;
            $quantity = $data['quantity'];

            // Stock is tracked by warehouse only - not branch dependent for sales
            // First try exact match (with branch_id if provided)
            $stock = ProductStock::where([
                'product_id' => $productId,
                'variation_id' => $variationId,
                'warehouse_id' => $warehouseId,
            ]);

            // If branch_id provided, try to find stock with that branch first
            if (! empty($data['branch_id'])) {
                $stock = $stock->where('branch_id', $data['branch_id']);
            }

            $stock = $stock->lockForUpdate()->first();

            // If no stock found with branch, try without branch constraint (warehouse-only stock)
            if (! $stock) {
                $stock = ProductStock::where([
                    'product_id' => $productId,
                    'variation_id' => $variationId,
                    'warehouse_id' => $warehouseId,
                ])->lockForUpdate()->first();
            }

            if (! $stock || $stock->quantity < $quantity) {
                $available = $stock ? $stock->quantity : 0;
                throw new \Exception("Not enough stock. Available: {$available}");
            }

            $stock->quantity -= $quantity;
            $stock->save();

            return $movement;
        });
    }

    public function transfer(array $data): StockMovement
    {
        return DB::transaction(function () use ($data) {

            // OUT from source
            $this->stockOut([
                ...$data,
                'type' => 'transfer',
            ]);

            // IN to destination
            return $this->stockIn([
                ...$data,
                'type' => 'transfer',
                'to_warehouse_id' => $data['to_warehouse_id'],
                'from_warehouse_id' => $data['from_warehouse_id'],
            ]);
        });
    }

    public function adjust(array $data): StockMovement
    {
        return DB::transaction(function () use ($data) {
            $movement = StockMovement::create($data);

            $stock = ProductStock::firstOrCreate([
                'product_id' => $data['product_id'],
                'variation_id' => $data['variation_id'] ?? null,
                'warehouse_id' => $data['to_warehouse_id'],
                'branch_id' => $data['branch_id'] ?? null,
            ], [
                'quantity' => 0,
                'alert_quantity' => null,
            ]);

            // quantity here means "new quantity target"
            $stock->quantity = $data['quantity'];
            $stock->save();

            return $movement;
        });
    }
}
