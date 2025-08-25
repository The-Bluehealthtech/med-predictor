<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SocialMediaAlert extends Model
{
    use HasFactory;

    protected $fillable = [
        'player_id',
        'title',
        'content',
        'alert_timestamp',
        'views',
        'engagement',
        'sentiment',
        'platform',
        'needs_response',
        'hashtag',
        'user_mention'
    ];

    protected $casts = [
        'alert_timestamp' => 'datetime',
        'needs_response' => 'boolean'
    ];

    public function player()
    {
        return $this->belongsTo(Player::class);
    }
}
