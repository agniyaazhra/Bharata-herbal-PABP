<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index(Request $request)
    {
       
        $startDate = $request->input('start_date', now()->startOfMonth()->toDateString());
        $endDate   = $request->input('end_date',   now()->toDateString());
        $txStatus  = $request->input('tx_status', '');

        $range = $request->input('range', 'this_week');

        if ($range === 'last_week') {
            $chartFrom = now()->subWeeks(2)->startOfWeek(Carbon::SUNDAY);
            $chartTo   = now()->subWeeks(1)->endOfWeek(Carbon::SATURDAY);
        } else {
            $chartFrom = now()->subWeeks(1)->startOfWeek(Carbon::SUNDAY);
            $chartTo   = now()->endOfWeek(Carbon::SATURDAY);
        }

        $today     = now()->toDateString();
        $sevenAgo  = now()->subDays(6)->toDateString();
        $prevFrom  = now()->subDays(13)->toDateString();
        $prevTo    = now()->subDays(7)->toDateString();

        $revenueThis = Order::whereBetween(DB::raw('DATE(created_at)'), [$sevenAgo, $today])
            ->revenueRelevant()
            ->sum('total_price');

        $revenuePrev = Order::whereBetween(DB::raw('DATE(created_at)'), [$prevFrom, $prevTo])
            ->revenueRelevant()
            ->sum('total_price');

        $revenueGrowth = $revenuePrev > 0
            ? round((($revenueThis - $revenuePrev) / $revenuePrev) * 100, 1)
            : ($revenueThis > 0 ? 100 : 0);

        $customersThis = User::where('role', '!=', 'admin')
            ->whereBetween(DB::raw('DATE(created_at)'), [$sevenAgo, $today])
            ->count();

        $customersPrev = User::where('role', '!=', 'admin')
            ->whereBetween(DB::raw('DATE(created_at)'), [$prevFrom, $prevTo])
            ->count();

        $customersTotal = User::where('role', '!=', 'admin')->count();

        $customersGrowth = $customersPrev > 0
            ? round((($customersThis - $customersPrev) / $customersPrev) * 100, 1)
            : ($customersThis > 0 ? 100 : 0);

        $profitThis = Order::whereBetween(DB::raw('DATE(created_at)'), [$sevenAgo, $today])
            ->revenueRelevant()
            ->selectRaw('SUM(total_price - shipping_cost) as profit')
            ->value('profit') ?? 0;

        $profitPrev = Order::whereBetween(DB::raw('DATE(created_at)'), [$prevFrom, $prevTo])
            ->revenueRelevant()
            ->selectRaw('SUM(total_price - shipping_cost) as profit')
            ->value('profit') ?? 0;

        $profitGrowth = $profitPrev > 0
            ? round((($profitThis - $profitPrev) / $profitPrev) * 100, 1)
            : ($profitThis > 0 ? 100 : 0);

        $chartRaw = Order::selectRaw('DATE(created_at) as date, SUM(total_price) as total')
            ->whereBetween(DB::raw('DATE(created_at)'), [
                $chartFrom->toDateString(),
                $chartTo->toDateString(),
            ])
            ->revenueRelevant()
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('total', 'date')
            ->toArray();

        $chartLabels = [];
        $chartData   = [];
        $dayNames    = ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'];

        for ($i = 0; $i < 7; $i++) {
            $date          = $chartFrom->copy()->addDays($i)->toDateString();
            $dow           = Carbon::parse($date)->dayOfWeek;
            $chartLabels[] = $dayNames[$dow] . ' ' . Carbon::parse($date)->format('d/m');
            $chartData[]   = round($chartRaw[$date] ?? 0);
        }

        $totalOrders   = Order::revenueRelevant()->count();
        $totalProducts = Product::count();
        $totalStock    = Product::sum('stock');
        $totalRevenue  = Order::revenueRelevant()->sum('total_price');

        $topProductSearch = $request->input('product_search', '');

        $topProducts = Product::withSum(
                ['orderItems' => fn($q) => $q->whereHas('order', fn($o) => $o->revenueRelevant())],
                'quantity'
            )
            ->withCount([
                'orderItems as orders_count' => fn($q) => $q->whereHas('order', fn($o) => $o->revenueRelevant())
            ])
            ->when($topProductSearch, fn($q) => $q->where('name', 'like', "%{$topProductSearch}%"))
            ->orderByDesc('order_items_sum_quantity')
            ->limit(8)
            ->get();

        $txQuery = Order::with('user', 'items.product', 'payment')
            ->whereBetween(DB::raw('DATE(created_at)'), [$startDate . '', $endDate . ''])
            ->latest();

        if ($txStatus) {
            $txQuery->where('status', $txStatus);
        }

        $transactions = $txQuery->paginate(10)->withQueryString();

        return view('admin.reports.index', compact(
           
            'revenueThis', 'revenuePrev', 'revenueGrowth',
            'customersThis', 'customersPrev', 'customersTotal', 'customersGrowth',
            'profitThis', 'profitPrev', 'profitGrowth',
           
            'chartLabels', 'chartData', 'range',
           
            'totalOrders', 'totalProducts', 'totalStock', 'totalRevenue',
           
            'topProducts', 'topProductSearch',
           
            'transactions', 'startDate', 'endDate', 'txStatus',
        ));
    }

    public function export(Request $request)
    {
        $startDate = $request->input('start_date', now()->startOfMonth()->toDateString());
        $endDate   = $request->input('end_date',   now()->toDateString());

        $orders = Order::with('user', 'items.product', 'payment')
            ->whereBetween(DB::raw('DATE(created_at)'), [$startDate, $endDate])
            ->revenueRelevant()
            ->latest()
            ->get();

        $filename = 'laporan-penjualan-' . $startDate . '-' . $endDate . '.csv';
        $headers  = ['Content-Type' => 'text/csv; charset=UTF-8'];

        $callback = function () use ($orders) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));

            fputcsv($handle, ['No. Pesanan', 'Tanggal', 'Pelanggan', 'Produk', 'Total (Rp)', 'Metode Bayar', 'Status']);

            foreach ($orders as $order) {
                $products = $order->items->pluck('product.name')->implode(', ');
                fputcsv($handle, [
                    '#' . str_pad($order->id, 5, '0', STR_PAD_LEFT),
                    $order->created_at->format('d/m/Y H:i'),
                    $order->user->name ?? '-',
                    $products ?: '-',
                    number_format($order->total_price, 0, ',', '.'),
                    $order->payment?->method_label ?? '-',
                    $order->status_label,
                ]);
            }

            fclose($handle);
        };

        return response()->streamDownload($callback, $filename, $headers);
    }
}
