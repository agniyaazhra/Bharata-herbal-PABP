<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $table = 'notifications';

    protected $fillable = [
        'user_id', 'title', 'message', 'type',
        'notifiable_type', 'notifiable_id', 'is_read', 'read_at',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'read_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function notifiable()
    {
        return $this->morphTo();
    }

    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    public function scopeForAdmin($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function markAsRead(): void
    {
        $this->update([
            'is_read' => true,
            'read_at' => now(),
        ]);
    }

    public function getTypeBadgeClassAttribute(): string
    {
        return match ($this->type) {
            'danger'  => 'text-red-600 bg-red-50',
            'warning' => 'text-yellow-600 bg-yellow-50',
            'success' => 'text-green-600 bg-green-50',
            default   => 'text-blue-600 bg-blue-50',
        };
    }
}
