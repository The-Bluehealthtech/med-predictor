<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentSignatureRequest extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'metadata' => 'array',
        'requested_at' => 'datetime',
        'signed_at' => 'datetime',
    ];
}
