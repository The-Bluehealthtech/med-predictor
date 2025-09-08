<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PlayerDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'player_id',
        'document_type',
        'title',
        'description',
        'file_path',
        'file_name',
        'file_type',
        'file_size',
        'mime_type',
        'is_private',
        'expiry_date',
        'status',
        'metadata'
    ];

    protected $casts = [
        'expiry_date' => 'date',
        'is_private' => 'boolean',
        'file_size' => 'integer',
        'metadata' => 'array'
    ];

    public function player(): BelongsTo
    {
        return $this->belongsTo(Player::class);
    }

    public function getFileUrlAttribute(): string
    {
        return asset('storage/' . $this->file_path);
    }

    public function getFileSizeFormattedAttribute(): string
    {
        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }

    public function isExpired(): bool
    {
        return $this->expiry_date && $this->expiry_date->isPast();
    }

    public function getStatusColorClass(): string
    {
        return match($this->status) {
            'active' => 'text-green-600 bg-green-50',
            'expired' => 'text-red-600 bg-red-50',
            'archived' => 'text-gray-600 bg-gray-50',
            default => 'text-gray-600 bg-gray-50'
        };
    }

    public function getDocumentTypeIcon(): string
    {
        return match($this->document_type) {
            'medical_certificate' => '🏥',
            'fitness_report' => '💪',
            'contract' => '📄',
            'license' => '🪪',
            'medical_clearance' => '✅',
            'injury_report' => '🩹',
            'training_certificate' => '🎓',
            default => '📋'
        };
    }
} 