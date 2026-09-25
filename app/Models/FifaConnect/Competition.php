<?php

namespace App\Models\FifaConnect;

use App\Models\FifaConnect\Concerns\HasFifaPicture;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Competition extends Model
{
    use HasFifaPicture;

    public const FIFA_PICTURE_OWNER_TYPE = 'competition';

    protected $table = 'fifa_connect_competitions';
    protected $guarded = [];
    protected $casts = ['date_from' => 'date', 'date_to' => 'date'];

    public function parent(): BelongsTo { return $this->belongsTo(self::class, 'parent_competition_id'); }
    public function elements(): HasMany { return $this->hasMany(self::class, 'parent_competition_id'); }
    public function teams(): HasMany { return $this->hasMany(CompetitionTeam::class, 'competition_id'); }
    public function localNames(): HasMany { return $this->hasMany(CompetitionLocalName::class, 'competition_id'); }
    public function matches(): HasMany { return $this->hasMany(MatchRecord::class, 'competition_fifa_id', 'competition_fifa_id'); }
}
