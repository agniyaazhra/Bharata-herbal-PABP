<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderTrackingUpdate extends Model
{
    use HasFactory;

    public const STATUS_LABELS = [
        'packed' => 'Paket dikemas oleh penjual',
        'handed_to_courier' => 'Paket diserahkan ke kurir',
        'sorting_center' => 'Paket tiba di hub / sorting center',
        'in_transit' => 'Paket dalam perjalanan ke kota tujuan',
        'out_for_delivery' => 'Paket sedang diantar',
        'delivered' => 'Paket diterima',
    ];

    protected $fillable = [
        'order_id',
        'status',
        'location_name',
        'description',
        'latitude',
        'longitude',
        'tracked_at',
    ];

    protected $casts = [
        'latitude' => 'decimal:6',
        'longitude' => 'decimal:6',
        'tracked_at' => 'datetime',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUS_LABELS[$this->status] ?? ucfirst(str_replace('_', ' ', $this->status));
    }
}
