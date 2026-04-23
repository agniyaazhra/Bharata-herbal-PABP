<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
       
        $totalProducts  = Product::count();
        $totalCustomers = User::where('role', 'customer')->count();
        $totalOrders    = Order::count();
        $totalSales     = Order::revenueRelevant()->sum('total_price');

        $newProducts  = Product::whereMonth('created_at', now()->month)->count();
        $newCustomers = User::where('role', 'customer')->whereMonth('created_at', now()->month)->count();
        $newOrders    = Order::whereDate('created_at', today())->count();

        $recentOrders = Order::with('user')
            ->latest()
            ->take(10)
            ->get();

        $salesRaw = Order::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(total_price) as total')
            )
            ->revenueRelevant()
            ->whereBetween('created_at', [now()->subDays(6)->startOfDay(), now()->endOfDay()])
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('total', 'date')
            ->toArray();

        $dayNames = ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'];
        $salesLabels = [];
        $salesData   = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->toDateString();
            $salesLabels[] = $dayNames[now()->subDays($i)->dayOfWeek];
            $salesData[]   = (float) ($salesRaw[$date] ?? 0);
        }

        $categorySales = OrderItem::join('product_category', 'order_items.product_id', '=', 'product_category.product_id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('categories', 'product_category.category_id', '=', 'categories.id')
            ->select('categories.name', DB::raw('COUNT(*) as total'))
            ->whereIn('orders.status', Order::revenueStatuses())
            ->groupBy('categories.id', 'categories.name')
            ->orderByDesc('total')
            ->limit(6)
            ->get();

        $categoryData = $categorySales->isNotEmpty()
            ? $categorySales->map(fn($c) => ['name' => $c->name, 'value' => (int) $c->total])->toArray()
            : [
                ['name' => 'Imunitas',    'value' => 30],
                ['name' => 'Diabetes',    'value' => 25],
                ['name' => 'Asam Urat',   'value' => 20],
                ['name' => 'Stroke',      'value' => 12],
                ['name' => 'Pencernaan',  'value' => 8],
                ['name' => 'Pelangsing',  'value' => 5],
            ];

        $topProducts = Product::with('reviews')
            ->orderByDesc('sales_count')
            ->orderByDesc('rating')
            ->take(8)
            ->get();

        return view('admin.dashboard', compact(
            'totalProducts', 'totalCustomers', 'totalOrders', 'totalSales',
            'newProducts', 'newCustomers', 'newOrders',
            'recentOrders', 'salesLabels', 'salesData', 'categoryData', 'topProducts'
        ));
    }
}
