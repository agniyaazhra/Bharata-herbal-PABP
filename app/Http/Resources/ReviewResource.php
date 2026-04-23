<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReviewResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'              => $this->id,
            'product_id'      => $this->product_id,
            'product_name'    => $this->product?->name,
            'user_id'         => $this->user_id,
            'user_name'       => $this->user?->name,
            'rating'          => $this->rating,
            'comment'         => $this->comment,
            'image_url'       => $this->image ? asset('storage/' . $this->image) : null,
            'reviewer_name'   => $this->reviewer_name,
            'reviewer_title'  => $this->reviewer_title,
            'is_featured'     => $this->is_featured,
            'created_at'      => $this->created_at->toISOString(),
        ];
    }
}
