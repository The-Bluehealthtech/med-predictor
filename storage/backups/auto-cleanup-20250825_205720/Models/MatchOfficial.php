<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MatchOfficial extends Model
{
    use HasFactory;

    protected $fillable = [
        'match_id',
        'user_id',
        'role',
    ];

    /**
     * Get the match that owns the official
     */
    public function match(): BelongsTo
    {
        return $this->belongsTo(GameMatch::class, 'match_id');
    }

    /**
     * Get the user (official)
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope for referees
     */
    public function scopeReferees($query)
    {
        return $query->where('role', 'referee');
    }

    /**
     * Scope for assistant referees
     */
    public function scopeAssistantReferees($query)
    {
        return $query->where('role', 'assistant_referee');
    }

    /**
     * Scope for fourth officials
     */
    public function scopeFourthOfficials($query)
    {
        return $query->where('role', 'fourth_official');
    }
} 