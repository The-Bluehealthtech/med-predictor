<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FifaConnectId extends Model
{
    protected $fillable = [
        'fifa_id',
        'entity_type',
        'entity_id',
        'status',
        'synced_at',
        'metadata'
    ];

    protected $casts = [
        'metadata' => 'array',
        'synced_at' => 'datetime'
    ];

    public function players(): HasMany
    {
        return $this->hasMany(Player::class, 'fifa_connect_id');
    }
}
