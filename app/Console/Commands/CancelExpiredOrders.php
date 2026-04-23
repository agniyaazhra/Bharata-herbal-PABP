<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Models\Payment;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CancelExpiredOrders extends Command
{
    protected $signature = 'app:cancel-expired-orders';

    protected $description = 'Auto-cancel non-COD pending orders that have passed their 2-hour payment deadline.';

    public function handle(): void
    {
        $expiredOrders = Order::where('status', 'pending')
            ->whereNotNull('payment_deadline')
            ->where('payment_deadline', '<', now())
            ->with('items.product', 'payment')
            ->get();

        if ($expiredOrders->isEmpty()) {
            $this->info('No expired orders to cancel.');
            return;
        }

        $count = 0;

        foreach ($expiredOrders as $order) {
            DB::transaction(function () use ($order) {
               
                foreach ($order->items as $item) {
                    if ($item->product) {
                        $item->product->increment('stock', $item->quantity);
                    }
                }

                $order->update([
                    'status'        => 'cancelled',
                    'cancel_reason' => 'expired_payment',
                ]);

                if ($order->payment) {
                    $order->payment->update(['status' => 'failed']);
                }
            });

            $count++;
            $this->line("Cancelled order");
        }

        $this->info("Done. Cancelled {$count} expired order(s).");
    }
}
