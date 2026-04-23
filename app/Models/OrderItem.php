<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = ['order_id', 'product_id', 'quantity', 'price', 'status', 'cancel_reason', 'cancelled_at'];

    protected $casts = [
        'price' => 'decimal:2',
        'cancelled_at' => 'datetime',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function getSubtotalAttribute()
    {
        return $this->quantity * $this->price;
    }

    public function canBeCancelled(): bool
    {
       
        if ($this->status === 'cancelled') {
            return false;
        }

        if (in_array($this->order->status, ['shipped', 'completed', 'cancelled'])) {
            return false;
        }

        return $this->order->created_at->diffInMinutes(now()) <= 120;
    }

    public function getStatusLabelAttribute(): string
    {
        $labels = [
            'active'    => 'Aktif',
            'cancelled' => 'Dibatalkan',
        ];
        return $labels[$this->status] ?? ucfirst($this->status);
    }

    public function getStatusColorAttribute(): string
    {
        $colors = [
            'active'    => 'green',
            'cancelled' => 'red',
        ];
        return $colors[$this->status] ?? 'gray';
    }
}
