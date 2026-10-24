<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function __invoke()
    {
        // Daily Stats
        $completed = fn () => \App\Models\PosOrder::where('status', 'completed');

        $dailySales = $completed()->whereDate('created_at', today())->sum('total_amount');
        $dailyOrders = $completed()->whereDate('created_at', today())->count();

        // Weekly Stats
        $weeklySales = $completed()->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->sum('total_amount');
        $weeklyOrders = $completed()->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count();

        // Monthly Stats
        $monthlySales = $completed()->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])->sum('total_amount');
        $monthlyOrders = $completed()->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])->count();

        // Chart Data (Last 7 Days)
        $chartLabels = [];
        $chartData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $chartLabels[] = $date->format('D'); // Mon, Tue, etc.
            $chartData[] = $completed()->whereDate('created_at', $date)->sum('total_amount');
        }

        // Top Selling Products
        $topProducts = \App\Models\PosOrderItem::join('pos_orders', 'pos_order_items.order_id', '=', 'pos_orders.id')
            ->whereNull('pos_orders.deleted_at')
            ->where('pos_orders.status', 'completed')
            ->selectRaw('pos_order_items.product_id, pos_order_items.name, sum(pos_order_items.quantity) as total_qty')
            ->groupBy('pos_order_items.product_id', 'pos_order_items.name')
            ->orderByDesc('total_qty')
            ->limit(5)
            ->get();

        // Low Stock Alert (Quantity < 10)
        $lowStockProducts = \App\Models\ProductStock::with(['product:id,name,sku', 'branch:id,name'])
            ->where('quantity', '<', 10)
            ->limit(5)
            ->get();

        // Recent Transactions
        $recentOrders = \App\Models\PosOrder::with('user:id,name')
            ->latest()
            ->limit(5)
            ->get();

        return Inertia::render('Admin/Dashboard', [
            'stats' => [
                'daily_sales' => $dailySales,
                'daily_orders' => $dailyOrders,
                'weekly_sales' => $weeklySales,
                'weekly_orders' => $weeklyOrders,
                'monthly_sales' => $monthlySales,
                'monthly_orders' => $monthlyOrders,
            ],
            'chart' => [
                'labels' => $chartLabels,
                'data' => $chartData,
            ],
            'top_products' => $topProducts,
            'low_stock' => $lowStockProducts,
            'recent_orders' => $recentOrders,
        ]);
    }
}
