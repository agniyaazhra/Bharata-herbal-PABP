<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\CartItem;



class Cart extends Model
{
    use HasFactory;

    protected $fillable = ['user_id'];

public function user()
{
    return $this->belongsTo(User::class);
}

public function items()
{
    return $this->hasMany(CartItem::class);
}

    public function getTotalAttribute()
    {
        return $this->items
            ->where('is_selected', true)
            ->sum(function ($item) {
                return $item->quantity * $item->product->effective_price;
            });
    }

    public function getTotalItemsAttribute()
    {
        return $this->items->where('is_selected', true)->sum('quantity');
    }

    public function getSelectedCountAttribute()
    {
        return $this->items->where('is_selected', true)->count();
    }
}
