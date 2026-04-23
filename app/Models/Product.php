<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'slug', 'description', 'usage', 'benefits', 'composition',
        'price', 'discount_price', 'stock', 'image', 'is_featured',
        'is_bestseller', 'rating', 'rating_count', 'sales_count', 'status',
    ];

    protected $casts = [
        'price'         => 'decimal:2',
        'discount_price'=> 'decimal:2',
        'is_featured'   => 'boolean',
        'is_bestseller' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($product) {
            $product->slug   = $product->slug ?? Str::slug($product->name);
            $product->status = $product->status ?? static::resolveStatus($product->stock);
        });

        static::updating(function ($product) {
            if ($product->isDirty('stock')) {
                $product->status = static::resolveStatus($product->stock);
            }
        });

        static::updated(function ($product) {
           
            if ($product->wasChanged('stock')) {
                static::dispatchStockNotification($product);
            }
        });
    }

    public static function resolveStatus(int $stock): string
    {
        $autoNonaktif  = (bool) Setting::get('product', 'auto_nonaktif_stok_habis', true);
        $autoWarning   = (bool) Setting::get('product', 'auto_warning_stok_minimum', true);
        $stockMinimum  = (int)  Setting::get('product', 'stock_minimum', 10);

        if ($stock === 0 && $autoNonaktif) {
            return 'inactive';
        }

        if ($stock <= $stockMinimum && $stock > 0 && $autoWarning) {
            return 'warning';
        }

        return 'active';
    }

    public static function dispatchStockNotification(Product $product): void
    {
        $notifType = Setting::get('product', 'notification_type', 'dashboard');

        if ($product->status === 'inactive') {
            $message = "Stok produk **{$product->name}** telah habis (0). Produk dinonaktifkan otomatis.";
            $type    = 'danger';
        } elseif ($product->status === 'warning') {
            $stockMin = Setting::get('product', 'stock_minimum', 10);
            $message  = "Stok produk **{$product->name}** di bawah minimum ({$product->stock} dari min. {$stockMin}).";
            $type     = 'warning';
        } else {
            return;
        }

        $admins = User::whereIn('role', ['admin', 'super_admin'])->get();
        foreach ($admins as $admin) {
            Notification::create([
                'user_id'          => $admin->id,
                'title'            => 'Peringatan Stok Produk',
                'message'          => $message,
                'type'             => $type,
                'notifiable_type'  => Product::class,
                'notifiable_id'    => $product->id,
                'is_read'          => false,
            ]);
        }

        if ($notifType === 'email') {
            $admins = User::whereIn('role', ['admin', 'super_admin'])->get();
            foreach ($admins as $admin) {
                \App\Services\StockNotificationMailer::send($admin, $product, $message);
            }
        }
    }

    public function getEffectivePriceAttribute()
    {
        return $this->discount_price ?? $this->price;
    }

    public function getDiscountPercentAttribute()
    {
        if ($this->discount_price && $this->price > 0) {
            return round((($this->price - $this->discount_price) / $this->price) * 100);
        }
        return 0;
    }

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'active'   => '<span class="badge bg-success">Aktif</span>',
            'inactive' => '<span class="badge bg-danger">Nonaktif</span>',
            'warning'  => '<span class="badge bg-warning text-dark">Peringatan Stok</span>',
            default    => '<span class="badge bg-secondary">-</span>',
        };
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'product_category');
    }

    public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeBestseller($query)
    {
        return $query->where('is_bestseller', true);
    }

    public function scopeInStock($query)
    {
        return $query->where('stock', '>', 0);
    }

    public function scopeAvailableForSale($query)
    {
        return $query->where('stock', '>', 0)
            ->where('status', '!=', 'inactive');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeWarning($query)
    {
        return $query->where('status', 'warning');
    }

    public function getAverageRatingAttribute()
    {
        return $this->rating ?: 0;
    }

    public function getRatingCountAttribute()
    {
        return $this->attributes['rating_count'] ?? 0;
    }

    public function getSalesCountAttribute()
    {
        return $this->attributes['sales_count'] ?? 0;
    }

    public function updateRatingStats()
    {
        $reviews = $this->reviews()->get();
        
        if ($reviews->isEmpty()) {
            $this->update([
                'rating' => 0,
                'rating_count' => 0,
            ]);
            return;
        }

        $averageRating = $reviews->avg('rating');
        $totalReviews = $reviews->count();

        $this->update([
            'rating' => round($averageRating, 1),
            'rating_count' => $totalReviews,
        ]);
    }

    public function updateSalesCount()
    {
        $salesCount = $this->orderItems()
            ->whereHas('order', function ($query) {
                $query->whereIn('status', Order::REVENUE_STATUSES);
            })
            ->sum('quantity');

        $this->update(['sales_count' => $salesCount]);
    }

    public function getRatingPercentageAttribute()
    {
        return $this->rating_count > 0 ? round(($this->rating / 5) * 100) : 0;
    }

    public function getFormattedRatingAttribute()
    {
        return number_format($this->rating, 1);
    }
}
