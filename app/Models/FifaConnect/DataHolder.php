<?php

namespace App\Models\FifaConnect;

use App\Models\Association;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DataHolder extends Model
{
    protected $table = 'fifa_connect_data_holders';

    protected $fillable = [
        'association_id',
        'person_fifa_id',
        'claim_status',
        'claim_reason',
        'registered_as_holder_at',
        'last_verified_at',
        'merged_into_fifa_id',
        'remote_deleted',
        'last_remote_status',
        'last_error',
    ];

    protected $casts = [
        'registered_as_holder_at' => 'datetime',
        'last_verified_at' => 'datetime',
        'remote_deleted' => 'boolean',
    ];

    public function association(): BelongsTo
    {
        return $this->belongsTo(Association::class);
    }
}
