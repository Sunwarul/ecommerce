<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductStock;
use App\Services\StockService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PosStockController extends Controller
{
    public function adjust(Request $request, StockService $stockService)
    {
        $data = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'variation_id' => ['nullable', 'exists:product_variations,id'],
            'warehouse_id' => ['required', 'exists:warehouses,id'],
            'branch_id' => ['required', 'exists:branches,id'],
            'quantity' => ['required', 'numeric', 'min:0'],
            'note' => ['nullable', 'string', 'max:255'],
        ]);

        $product = Product::findOrFail($data['product_id']);

        // Use StockService to adjust
        $stockService->adjust([
            'type' => 'adjustment',
            'product_id' => $data['product_id'],
            'variation_id' => $data['variation_id'] ?? null,
            'branch_id' => $data['branch_id'],
            'to_warehouse_id' => $data['warehouse_id'], // adjust uses to_warehouse_id
            'from_warehouse_id' => null,
            'quantity' => $data['quantity'], // target quantity
            'reference' => 'POS-ADJUST-' . now()->format('YmdHis'),
            'note' => $data['note'] ?? 'Market quick adjustment from POS',
            'created_by' => Auth::id(),
        ]);

        return back()->with('success', 'Stock adjusted successfully.');
    }
}
