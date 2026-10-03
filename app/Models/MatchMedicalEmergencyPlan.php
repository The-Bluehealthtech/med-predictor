<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class MatchMedicalEmergencyPlan extends Model
{
    protected $fillable = [
        'match_id','protocol_name','protocol_version','status','stadium',
        'nearest_hospital','nearest_hospital_phone','ambulance_contact',
        'team_leader_user_id','team_leader_name','team_leader_phone',
        'role_assignments','equipment_checklist','timeline_checklist','notes',
        'prepared_at','prepared_by','validated_at','validated_by',
    ];

    protected $casts = [
        'role_assignments' => 'array',
        'equipment_checklist' => 'array',
        'timeline_checklist' => 'array',
        'prepared_at' => 'datetime',
        'validated_at' => 'datetime',
    ];

    public function match(): BelongsTo
    {
        return $this->belongsTo(MatchModel::class, 'match_id');
    }

    public function preparedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'prepared_by');
    }

    public function validatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'validated_by');
    }
}
