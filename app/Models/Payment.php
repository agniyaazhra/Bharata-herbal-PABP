<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id', 'method', 'status', 'proof',
        'account_name', 'account_number', 'paid_at', 'payment_deadline',
    ];

    protected $casts = [
        'paid_at'          => 'datetime',
        'payment_deadline' => 'datetime',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function getMethodLabelAttribute(): string
    {
        $labels = [
            'cod'          => 'Bayar di Tempat (COD)',
            'dana'         => 'Dana',
            'gopay'        => 'GoPay',
            'qris'         => 'QRIS',
            'bank_transfer'=> 'Virtual Account / Bank Transfer',
            'ewallet'      => 'E-Wallet',
        ];
        return $labels[$this->method] ?? ucfirst($this->method);
    }

    public function getStatusLabelAttribute(): string
    {
        $labels = [
            'pending'  => 'Menunggu Pembayaran',
            'paid'     => 'Dibayar',
            'verified' => 'Dikonfirmasi',
            'failed'   => 'Gagal',
        ];
        return $labels[$this->status] ?? ucfirst($this->status);
    }
}
