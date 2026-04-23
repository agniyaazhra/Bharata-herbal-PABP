<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CartItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->id,
            'product_id'    => $this->product_id,
            'product_name'  => $this->product?->name,
            'product_image' => $this->product?->image ? asset('storage/' . $this->product->image) : null,
            'quantity'      => $this->quantity,
            'unit_price'    => (int) $this->product?->effective_price,
            'subtotal'      => (int) ($this->quantity * ($this->product?->effective_price ?? 0)),
            'is_selected'   => $this->is_selected,
            'created_at'    => $this->created_at->toISOString(),
        ];
    }
}
