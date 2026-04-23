<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'             => $this->id,
            'name'           => $this->name,
            'slug'           => $this->slug,
            'description'    => $this->description,
            'usage'          => $this->usage,
            'benefits'       => $this->benefits,
            'composition'    => $this->composition,
            'price'          => (int) $this->price,
            'discount_price' => $this->discount_price ? (int) $this->discount_price : null,
            'effective_price' => (int) $this->effective_price,
            'has_discount'   => $this->discount_price && $this->discount_price < $this->price,
            'discount_percent' => (int) $this->discount_percent,
            'stock'          => $this->stock,
            'status'         => $this->status,
            'image_url'      => $this->image ? asset('storage/' . $this->image) : null,
            'is_featured'    => $this->is_featured,
            'is_bestseller'  => $this->is_bestseller,
            'rating'         => (float) $this->rating,
            'rating_count'   => $this->rating_count,
            'sales_count'    => $this->sales_count,
            'categories'     => CategoryResource::collection($this->whenLoaded('categories')),
            'reviews'        => ReviewResource::collection($this->whenLoaded('reviews')),
            'created_at'     => $this->created_at->toISOString(),
        ];
    }
}
