<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'title',
        'content',
        'icon',
        'action_url',
        'is_read',
        'read_at',
        'metadata'
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'read_at' => 'datetime',
        'metadata' => 'array'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function markAsRead(): void
    {
        $this->update([
            'is_read' => true,
            'read_at' => now()
        ]);
    }

    public function getIconClassAttribute(): string
    {
        return match($this->type) {
            'medical' => 'fas fa-stethoscope text-blue-500',
            'message' => 'fas fa-envelope text-green-500',
            'appointment' => 'fas fa-calendar-check text-purple-500',
            'system' => 'fas fa-cog text-gray-500',
            default => 'fas fa-bell text-yellow-500'
        };
    }

    public function getStatusColorAttribute(): string
    {
        return $this->is_read ? 'text-gray-400' : 'text-blue-600';
    }
}
