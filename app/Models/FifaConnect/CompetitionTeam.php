<?php
namespace App\Models\FifaConnect;
use App\Models\FifaConnect\Concerns\HasFifaPicture;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
class CompetitionTeam extends Model
{
    use HasFifaPicture;

    public const FIFA_PICTURE_OWNER_TYPE = 'competition_team';

    protected $table = 'fifa_connect_competition_teams';
    protected $guarded = [];
    public function persons(): HasMany
    {
        return $this->hasMany(CompetitionTeamPerson::class, 'competition_team_id');
    }
}
