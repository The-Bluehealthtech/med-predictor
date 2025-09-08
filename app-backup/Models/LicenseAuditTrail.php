<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LicenseAuditTrail extends Model
{
    use HasFactory;

    protected $fillable = [
        'license_id',
        'user_id',
        'action',
        'old_status',
        'new_status',
        'reason',
        'changes',
        'ip_address',
        'user_agent'
    ];

    protected $casts = [
        'changes' => 'array'
    ];

    public function license()
    {
        return $this->belongsTo(PlayerLicense::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeForLicense($query, $licenseId)
    {
        return $query->where('license_id', $licenseId);
    }

    public function scopeByAction($query, $action)
    {
        return $query->where('action', $action);
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeRecent($query, $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }
}
