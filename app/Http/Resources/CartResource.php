<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CartResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $minimumOrderAmount = (int) \App\Models\Setting::get('shipping', 'minimum_order_amount', 0);
        $total = $this->items
            ->where('is_selected', true)
            ->sum(function ($item) {
                return $item->quantity * $item->product->effective_price;
            });

        return [
            'id'                      => $this->id,
            'items'                   => CartItemResource::collection($this->items),
            'total'                   => (int) $total,
            'selected_count'          => $this->items->where('is_selected', true)->count(),
            'total_items'             => $this->items->where('is_selected', true)->sum('quantity'),
            'minimum_order_amount'    => $minimumOrderAmount,
            'is_minimum_met'          => $total >= $minimumOrderAmount,
            'created_at'              => $this->created_at->toISOString(),
        ];
    }
}
