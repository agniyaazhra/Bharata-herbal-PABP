<?php

namespace App\Services;

use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;


class StockNotificationMailer
{
    
    public static function send(User $admin, Product $product, string $message): void
    {
        try {
            Mail::send('emails.stock-alert', [
                'admin'   => $admin,
                'product' => $product,
                'message' => $message,
            ], function ($mail) use ($admin, $product) {
                $mail->to($admin->email, $admin->name)
                     ->subject("[Bharata Herbal] Peringatan Stok: {$product->name}");
            });

            Log::info("Email notif stok terkirim ke {$admin->email} untuk produk");
        } catch (\Throwable $e) {
            \Log::error("Gagal kirim email notif stok: {$e->getMessage()}");
        }
    }
}
