<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    public const REVENUE_STATUSES = ['paid', 'processing', 'shipped', 'completed'];

    protected $fillable = [
        'user_id',
        'address_id',
        'subtotal',
        'shipping_cost',
        'total_price',
        'status',
        'tracking_number',
        'courier_name',
        'notes',
        'cancel_reason',
        'payment_deadline',
        'estimated_delivery_at',
    ];

    protected $casts = [
        'subtotal'              => 'decimal:2',
        'shipping_cost'         => 'decimal:2',
        'total_price'           => 'decimal:2',
        'payment_deadline'      => 'datetime',
        'estimated_delivery_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function address()
    {
        return $this->belongsTo(Address::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function trackingUpdates()
    {
        return $this->hasMany(OrderTracking::class, 'order_id')->orderBy('created_at');
    }

    public function scopeRevenueRelevant($query)
    {
        return $query->whereIn('status', self::REVENUE_STATUSES);
    }

    public static function revenueStatuses(): array
    {
        return self::REVENUE_STATUSES;
    }

    public function canBeCancelled(): bool
    {
        if (in_array($this->status, ['shipped', 'completed', 'cancelled'])) {
            return false;
        }

        return $this->created_at->diffInMinutes(now()) <= 120;
    }

    public function isPaymentExpired(): bool
    {
        return $this->payment_deadline && now()->greaterThan($this->payment_deadline);
    }

    public function getStatusLabelAttribute(): string
    {
        $labels = [
            'pending'    => 'Belum Bayar',
            'paid'       => 'Sudah Bayar',
            'processing' => 'Sedang Dikemas',
            'shipped'    => 'Dikirim',
            'completed'  => 'Selesai',
            'cancelled'  => 'Dibatalkan',
        ];
        return $labels[$this->status] ?? ucfirst($this->status);
    }

    public function getOrderNumberAttribute(): string
    {
        $date = ($this->created_at ?? now())->format('Ymd');
        return 'BHT-' . $date . '-' . str_pad($this->id, 5, '0', STR_PAD_LEFT);
    }

    public function getStatusColorAttribute(): string
    {
        $colors = [
            'pending'    => 'yellow',
            'paid'       => 'blue',
            'processing' => 'indigo',
            'shipped'    => 'orange',
            'completed'  => 'green',
            'cancelled'  => 'red',
        ];
        return $colors[$this->status] ?? 'gray';
    }

    public function getCourierLabelAttribute(): ?string
    {
        if ($this->courier_name) {
            return $this->courier_name;
        }

        $tracking = strtoupper((string) $this->tracking_number);

        return match (true) {
            str_starts_with($tracking, 'JNE') => 'JNE',
            str_starts_with($tracking, 'JNT') => 'J&T Express',
            str_starts_with($tracking, 'SIL') => 'SiCepat',

            !empty($tracking) => 'Kurir Internal',
            default => null,
        };
    }
}
