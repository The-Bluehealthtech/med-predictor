<?php

namespace App\Models\FifaConnect;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class MatchCompetitionContext extends Model
{
    protected $table = 'fifa_connect_match_competition_contexts';

    protected $guarded = [];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(
            self::class,
            'parent_context_id'
        );
    }

    public function picture(): HasOne
    {
        return $this->hasOne(
            Picture::class,
            'owner_id'
        )->where(
            'owner_type',
            'match_competition_context'
        );
    }
}
