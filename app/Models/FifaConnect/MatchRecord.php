<?php

namespace App\Models\FifaConnect;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class MatchRecord extends Model
{
    protected $table = 'fifa_connect_matches';
    protected $guarded = [];
    protected $casts = ['date_time_local' => 'datetime'];

    public function competition(): BelongsTo
    {
        return $this->belongsTo(Competition::class, 'competition_fifa_id', 'competition_fifa_id');
    }

    public function phases(): HasMany
    {
        return $this->hasMany(MatchPhase::class, 'match_id');
    }

    public function teams(): HasMany
    {
        return $this->hasMany(MatchTeam::class, 'match_id');
    }

    public function facility(): BelongsTo
    {
        return $this->belongsTo(Facility::class, 'facility_fifa_id', 'facility_fifa_id');
    }

    public function events(): HasMany
    {
        return $this->hasMany(MatchEvent::class, 'match_id');
    }

    public function competitionContext(): HasOne
    {
        return $this->hasOne(MatchCompetitionContext::class, 'match_id')->where('depth', 0);
    }

    public function facilityContext(): HasOne
    {
        return $this->hasOne(MatchFacilityContext::class, 'match_id');
    }

    public function officials(): HasMany
    {
        return $this->hasMany(MatchOfficial::class, 'match_id');
    }
}
