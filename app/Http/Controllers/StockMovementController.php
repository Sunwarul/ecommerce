<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Product;
use App\Models\Warehouse;
use App\Services\StockService;
use Illuminate\Http\Request;

class StockMovementController extends Controller
{
    public function create(Product $product)
    {
        $product->load([
            'variations:id,product_id,sku',
        ]);

        return inertia('Admin/Stocks/Move', [
            'product' => $product->only(['id', 'name', 'type']) + [
                'variations' => $product->variations,
            ],
            'warehouses' => Warehouse::select('id', 'name')->orderBy('name')->get(),
            'branches' => Branch::active()->select('id', 'name')->orderBy('name')->get(),
        ]);
    }
    public function store(Request $request, StockService $stockService)
    {
        $data = $request->validate([
            'type' => ['required', 'in:in,out,transfer,adjust'],
            'product_id' => ['required', 'exists:products,id'],
            'variation_id' => ['nullable', 'exists:product_variations,id'],
            'branch_id' => ['required', 'exists:branches,id'],
            'quantity' => ['required', 'numeric', 'gt:0'],

            'from_warehouse_id' => ['nullable', 'exists:warehouses,id'],
            'to_warehouse_id' => ['nullable', 'exists:warehouses,id'],

            'reference' => ['nullable', 'string', 'max:255'],
            'note' => ['nullable', 'string'],

            // ✅ controls redirect behavior
            'move' => ['nullable', 'boolean'],
        ]);

        $data['created_by'] = auth()->id();

        if ($data['type'] === 'in') {
            if (empty($data['to_warehouse_id'])) {
                return back()->withErrors(['to_warehouse_id' => 'Required']);
            }
            $stockService->stockIn($data);
        }

        if ($data['type'] === 'out') {
            if (empty($data['from_warehouse_id'])) {
                return back()->withErrors(['from_warehouse_id' => 'Required']);
            }
            $stockService->stockOut($data);
        }

        if ($data['type'] === 'transfer') {
            if (empty($data['from_warehouse_id']) || empty($data['to_warehouse_id'])) {
                return back()->withErrors(['warehouse' => 'Both warehouses required']);
            }
            $stockService->transfer($data);
        }

        if ($data['type'] === 'adjust') {
            if (empty($data['to_warehouse_id'])) {
                return back()->withErrors(['to_warehouse_id' => 'Required']);
            }
            $stockService->adjust($data);
        }

        $msg = 'Stock updated successfully.';

        if (!empty($data['move'])) {
            return redirect()
                ->route('products.show', $data['product_id'])
                ->with('success', $msg);
        }

        return back()->with('success', $msg);
    }


}