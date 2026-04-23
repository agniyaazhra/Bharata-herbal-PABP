<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Setting;
use Illuminate\Support\Facades\Log;

class ProductStockService
{

    public function updateStock(Product $product, int $newStock): Product
    {
        $oldStock = $product->stock;

        $product->update(['stock' => $newStock]);
        $product->refresh();

        Log::info("Stock updated: Product {$product->id}, from {$oldStock} to {$newStock}");

        return $product;
    }

    public function syncAllProductStatuses(): int
    {
        $products = Product::all();
        $updated  = 0;

        foreach ($products as $product) {
            $newStatus = Product::resolveStatus($product->stock);

            if ($product->status !== $newStatus) {

                Product::withoutEvents(function () use ($product, $newStatus) {
                    $product->update(['status' => $newStatus]);
                });
                $updated++;
            }
        }

        Log::info("Stock sync: {$updated} produk diperbarui statusnya.");

        return $updated;
    }

    public function getStockSummary(): array
    {
        return [
            'total'    => Product::count(),
            'active'   => Product::where('status', 'active')->count(),
            'warning'  => Product::where('status', 'warning')->count(),
            'inactive' => Product::where('status', 'inactive')->count(),
            'minimum'  => (int) Setting::get('product', 'stock_minimum', 10),
        ];
    }
}
