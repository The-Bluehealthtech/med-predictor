<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LicenseRequestStatus extends Model
{
    protected $fillable = [
        'license_request_id', 'status', 'user_id', 'comment'
    ];

    public function licenseRequest()
    {
        return $this->belongsTo(LicenseRequest::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
