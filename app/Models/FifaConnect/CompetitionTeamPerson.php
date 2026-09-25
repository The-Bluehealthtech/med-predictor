<?php

namespace App\Models\FifaConnect;

use App\Models\FifaConnect\Concerns\HasFifaPicture;
use Illuminate\Database\Eloquent\Model;

class CompetitionTeamPerson extends Model
{
    use HasFifaPicture;

    public const FIFA_PICTURE_OWNER_TYPE = 'competition_team_person';

    protected $table = 'fifa_connect_competition_team_persons';

    protected $guarded = [];

    protected $casts = [
        'date_of_birth' => 'date',
    ];

}
