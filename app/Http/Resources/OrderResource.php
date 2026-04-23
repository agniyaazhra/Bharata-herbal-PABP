<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                    => $this->id,
            'order_number'          => 'ORD-' . str_pad($this->id, 6, '0', STR_PAD_LEFT),
            'status'                => $this->status,
            'subtotal'              => (int) $this->subtotal,
            'shipping_cost'         => (int) $this->shipping_cost,
            'total_price'           => (int) $this->total_price,
            'notes'                 => $this->notes,
            'tracking_number'       => $this->tracking_number,
            'courier_name'          => $this->courier_name,
            'cancel_reason'         => $this->cancel_reason,
            'payment_deadline'      => $this->payment_deadline?->toISOString(),
            'estimated_delivery_at' => $this->estimated_delivery_at?->toISOString(),
            'items'                 => OrderItemResource::collection($this->whenLoaded('items')),
            'address'               => new AddressResource($this->whenLoaded('address')),
            'payment'               => $this->whenLoaded('payment'),
            'created_at'            => $this->created_at->toISOString(),
            'updated_at'            => $this->updated_at->toISOString(),
        ];
    }
}
