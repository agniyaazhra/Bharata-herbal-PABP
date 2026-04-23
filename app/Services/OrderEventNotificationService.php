<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\Order;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class OrderEventNotificationService
{
    private const GROUP = 'notification';

    public function notify(string $event, Order $order): void
    {
        if (! Setting::get(self::GROUP, "event_{$event}", false)) {
            return;
        }

        [$title, $message, $type] = $this->resolveContent($event, $order);

        $admins = User::whereIn('role', ['admin', 'super_admin'])->get();

        foreach ($admins as $admin) {
            Notification::create([
                'user_id' => $admin->id,
                'title' => $title,
                'message' => $message,
                'type' => $type,
                'notifiable_type' => Order::class,
                'notifiable_id' => $order->id,
                'is_read' => false,
            ]);
        }

        $this->logExternalChannel('email_primary', $event, $order, $title);
        $this->logExternalChannel('email_backup', $event, $order, $title);
        $this->logExternalChannel('whatsapp_primary', $event, $order, $title);
        $this->logExternalChannel('whatsapp_backup', $event, $order, $title);
    }

    private function resolveContent(string $event, Order $order): array
    {
        $orderNumber = '#' . str_pad((string) $order->id, 5, '0', STR_PAD_LEFT);
        $customerName = $order->user?->name ?? 'Pelanggan';

        return match ($event) {
            'order_created' => [
                'Pesanan Baru Masuk',
                "{$orderNumber} dibuat oleh {$customerName} dengan total Rp " . number_format((float) $order->total_price, 0, ',', '.'),
                'info',
            ],
            'payment_confirmed' => [
                'Pembayaran Terkonfirmasi',
                "Pembayaran {$orderNumber} telah dikonfirmasi dan siap diproses.",
                'success',
            ],
            'order_shipped' => [
                'Pesanan Dikirim',
                "{$orderNumber} telah dikirim melalui " . ($order->courier_label ?? 'kurir') . '.',
                'info',
            ],
            'order_completed' => [
                'Pesanan Selesai',
                "{$orderNumber} untuk {$customerName} telah selesai.",
                'success',
            ],
            default => [
                'Update Pesanan',
                "Ada pembaruan untuk pesanan {$orderNumber}.",
                'info',
            ],
        };
    }

    private function logExternalChannel(string $key, string $event, Order $order, string $title): void
    {
        $destination = trim((string) Setting::get(self::GROUP, $key, ''));

        if ($destination === '') {
            return;
        }

        Log::info('ORDER_EVENT_NOTIFICATION', [
            'event' => $event,
            'destination_key' => $key,
            'destination' => $destination,
            'order_id' => $order->id,
            'title' => $title,
        ]);
    }
}
