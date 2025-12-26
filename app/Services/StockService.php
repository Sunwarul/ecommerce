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

            $stock = ProductStock::firstOrCreate([
                'product_id' => $data['product_id'],
                'variation_id' => $data['variation_id'] ?? null,
                'warehouse_id' => $data['to_warehouse_id'],
                'branch_id' => $data['branch_id'],
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

            $stock = ProductStock::where([
                'product_id' => $data['product_id'],
                'variation_id' => $data['variation_id'] ?? null,
                'warehouse_id' => $data['from_warehouse_id'],
                'branch_id' => $data['branch_id'],
            ])->lockForUpdate()->first();

            if (!$stock || $stock->quantity < $data['quantity']) {
                throw new \Exception("Not enough stock.");
            }

            $stock->quantity -= $data['quantity'];
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
                'branch_id' => $data['branch_id'],
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
