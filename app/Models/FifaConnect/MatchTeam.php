<?php

namespace App\Models\FifaConnect;

use App\Models\FifaConnect\Concerns\HasFifaPicture;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MatchTeam extends Model
{
    use HasFifaPicture;

    public const FIFA_PICTURE_OWNER_TYPE = 'match_team';

    protected $table = 'fifa_connect_match_teams';

    protected $guarded = [];

    public function match(): BelongsTo
    {
        return $this->belongsTo(MatchRecord::class, 'match_id');
    }

    public function players(): HasMany
    {
        return $this->hasMany(MatchPlayer::class, 'match_team_id');
    }

    public function officials(): HasMany
    {
        return $this->hasMany(TeamOfficial::class, 'match_team_id');
    }

    public function teamOfficials(): HasMany
    {
        return $this->officials();
    }
}
