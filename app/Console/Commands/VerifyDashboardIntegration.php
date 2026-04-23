<?php

namespace App\Console\Commands;

use App\Models\Order;
use Illuminate\Console\Command;

class VerifyDashboardIntegration extends Command
{
    protected $signature = 'verify:dashboard';
    protected $description = 'Verify dashboard integration and data accuracy';

    public function handle()
    {
        $this->info("\n=== VERIFIKASI DASHBOARD DATA ===\n");

        $this->line('1. Order Status Distribution:');
        $statusCounts = Order::groupBy('status')
            ->selectRaw('status, COUNT(*) as count')
            ->pluck('count', 'status');

        foreach ($statusCounts as $status => $count) {
            $this->line("   - {$status}: {$count} orders");
        }

        $this->line("\n2. Revenue Relevant Orders (paid, processing, shipped, completed):");
        $revenueCount = Order::revenueRelevant()->count();
        $revenueTotal = Order::revenueRelevant()->sum('total_price');
        $this->line("   - Count: {$revenueCount}");
        $this->line("   - Total: Rp " . number_format($revenueTotal, 0, ',', '.'));

        $this->line("\n3. Server Timezone:");
        $this->line("   - Config: " . config('app.timezone'));
        $this->line("   - Current Time: " . now()->format('Y-m-d H:i:s e'));

        $this->line("\n4. Last 7 Days Sales Data:");
        $last7 = Order::revenueRelevant()
            ->whereBetween('created_at', [now()->subDays(6)->startOfDay(), now()->endOfDay()])
            ->selectRaw('DATE(created_at) as date, SUM(total_price) as total, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $dayNames = ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'];

        if ($last7->isEmpty()) {
            $this->line("   (No data in last 7 days - fallback data will be shown in chart)");
        } else {
            foreach ($last7 as $row) {
                $dow = \Carbon\Carbon::parse($row->date)->dayOfWeek;
                $dayName = $dayNames[$dow];
                $total = number_format($row->total, 0, ',', '.');
                $this->line("   - [{$dayName}] {$row->date}: Rp {$total} ({$row->count} orders)");
            }
        }

        $this->line("\n5. Top Categories (from Order Items):");
        $categories = Order::revenueRelevant()
            ->with('items.product.categories')
            ->get()
            ->flatMap(fn($order) => $order->items)
            ->flatMap(fn($item) => $item->product->categories)
            ->groupBy('name')
            ->map(fn($group) => $group->count())
            ->sortDesc()
            ->take(6);

        if ($categories->isEmpty()) {
            $this->line("   (No data - fallback data will be shown)");
        } else {
            foreach ($categories as $category => $count) {
                $this->line("   - {$category}: {$count}");
            }
        }

        $this->info("\n✓ VERIFIKASI SELESAI\n");
    }
}
