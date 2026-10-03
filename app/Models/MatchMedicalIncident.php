<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class MatchMedicalIncident extends Model
{
    protected $fillable = [
        'match_id','player_id','health_record_id','injury_id','incident_type','match_minute',
        'mechanism','contact','abcde_assessment','loss_of_consciousness','aed_used','oxygen_used',
        'evacuated','evacuation_destination','doctor_user_id','doctor_name','initial_diagnosis',
        'notes','created_by',
    ];

    protected $casts = [
        'contact'=>'boolean',
        'abcde_assessment'=>'array',
        'loss_of_consciousness'=>'boolean',
        'aed_used'=>'boolean',
        'oxygen_used'=>'boolean',
        'evacuated'=>'boolean',
    ];

    public function match(): BelongsTo { return $this->belongsTo(MatchModel::class, 'match_id'); }
    public function player(): BelongsTo { return $this->belongsTo(Player::class); }
    public function healthRecord(): BelongsTo { return $this->belongsTo(HealthRecord::class); }
    public function doctor(): BelongsTo { return $this->belongsTo(User::class, 'doctor_user_id'); }
    public function creator(): BelongsTo { return $this->belongsTo(User::class, 'created_by'); }
}
