<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LicenseRequestAttachment extends Model
{
    protected $fillable = [
        'license_request_id', 'file_path', 'type'
    ];

    public function licenseRequest()
    {
        return $this->belongsTo(LicenseRequest::class);
    }
}
