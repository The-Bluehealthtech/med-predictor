<?php

namespace App\Models\FifaConnect;

use App\Models\PlayerLicense;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Registration extends Model
{
    public const TYPE_PLAYER = 'Player';
    public const TYPE_MATCH_OFFICIAL = 'MatchOfficial';
    public const TYPE_TEAM_OFFICIAL = 'TeamOfficial';
    public const TYPE_ORGANISATION_OFFICIAL = 'OrganisationOfficial';

    protected $table = 'fifa_connect_registrations';

    protected $guarded = [];

    protected $casts = [
        'registration_valid_from' => 'date',
        'registration_valid_to' => 'date',
    ];

    public function person(): BelongsTo
    {
        return $this->belongsTo(Person::class);
    }

    public function playerLicense(): BelongsTo
    {
        return $this->belongsTo(PlayerLicense::class);
    }
}
