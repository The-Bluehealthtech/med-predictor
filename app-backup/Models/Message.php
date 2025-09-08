<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'sender_id',
        'receiver_id',
        'subject',
        'content',
        'is_read',
        'read_at',
        'is_deleted_by_sender',
        'is_deleted_by_receiver',
        'attachments'
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'read_at' => 'datetime',
        'is_deleted_by_sender' => 'boolean',
        'is_deleted_by_receiver' => 'boolean',
        'attachments' => 'array'
    ];

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function receiver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    public function markAsRead(): void
    {
        $this->update([
            'is_read' => true,
            'read_at' => now()
        ]);
    }

    public function isDeletedByUser(int $userId): bool
    {
        if ($this->sender_id === $userId) {
            return $this->is_deleted_by_sender;
        }
        if ($this->receiver_id === $userId) {
            return $this->is_deleted_by_receiver;
        }
        return false;
    }

    public function deleteForUser(int $userId): void
    {
        if ($this->sender_id === $userId) {
            $this->update(['is_deleted_by_sender' => true]);
        } elseif ($this->receiver_id === $userId) {
            $this->update(['is_deleted_by_receiver' => true]);
        }
    }

    public function scopeForUser($query, int $userId)
    {
        return $query->where(function($q) use ($userId) {
            $q->where('sender_id', $userId)
              ->orWhere('receiver_id', $userId);
        })->where(function($q) use ($userId) {
            $q->where('sender_id', $userId)->where('is_deleted_by_sender', false)
              ->orWhere('receiver_id', $userId)->where('is_deleted_by_receiver', false);
        });
    }
}
