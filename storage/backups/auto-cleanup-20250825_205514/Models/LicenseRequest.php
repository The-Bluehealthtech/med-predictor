<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LicenseRequest extends Model
{
    protected $fillable = [
        'club_id', 'player_id', 'staff_id', 'type', 'status', 'association_comment'
    ];

    public function club()
    {
        return $this->belongsTo(Club::class);
    }
    public function player()
    {
        return $this->belongsTo(Player::class);
    }
    public function staff()
    {
        return $this->belongsTo(Staff::class);
    }
    public function attachments()
    {
        return $this->hasMany(LicenseRequestAttachment::class);
    }
    public function statuses()
    {
        return $this->hasMany(LicenseRequestStatus::class);
    }
}
