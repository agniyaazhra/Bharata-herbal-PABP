<?php

namespace App\Observers;

use App\Models\OrderItem;

class OrderItemObserver
{
    public function updated(OrderItem $orderItem)
    {
        if ($orderItem->isDirty('status')) {
            $this->updateProductStats($orderItem);
        }
    }

    private function updateProductStats(OrderItem $orderItem)
    {
        if ($orderItem->order->status === 'completed' || in_array($orderItem->order->status, ['paid', 'processing', 'shipped'])) {
            $product = $orderItem->product;
            $product->updateSalesCount();
        }
    }
}
