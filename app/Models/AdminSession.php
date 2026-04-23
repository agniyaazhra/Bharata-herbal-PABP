<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdminSession extends Model
{
    protected $table = 'admin_sessions';

    protected $fillable = [
        'user_id', 'token_id', 'device_name', 'browser',
        'ip_address', 'location', 'last_active', 'is_current', 'is_active',
    ];

    protected $casts = [
        'last_active' => 'datetime',
        'is_current'  => 'boolean',
        'is_active'   => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function getLastActiveForHumansAttribute(): string
    {
        return $this->last_active?->diffForHumans() ?? 'Tidak diketahui';
    }
}
