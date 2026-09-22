<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LicensePhoto extends Model
{
    use HasFactory;

    protected $table = 'license_photos';

    protected $fillable = [
        'player_id',
        'club_id',
        'photo_path',
        'uploaded_by',
        'uploaded_at',
    ];

    protected $casts = [
        'uploaded_at' => 'datetime',
    ];

    public function player()
    {
        return $this->belongsTo(Player::class);
    }

    public function club()
    {
        return $this->belongsTo(Club::class);
    }
}
