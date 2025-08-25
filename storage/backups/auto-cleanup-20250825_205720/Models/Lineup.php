<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Competition;
use App\Models\GameMatch;
use App\Models\Player;
use App\Models\LineupPlayer;
use App\Models\Team;
use App\Models\Club;

class Lineup extends Model
{
    use HasFactory;

    protected $fillable = [
        'team_id',
        'club_id',
        'competition_id',
        'match_id',
        'name',
        'formation',
        'tactical_style',
        'playing_philosophy',
        'captain_id',
        'vice_captain_id',
        'penalty_taker_id',
        'free_kick_taker_id',
        'corner_taker_id',
        'match_type',
        'opponent',
        'venue',
        'weather_conditions',
        'pitch_condition',
        'tactical_notes',
        'substitutions_plan',
        'set_pieces_strategy',
        'pressing_intensity',
        'possession_style',
        'counter_attack_style',
        'defensive_line_height',
        'marking_system',
        'status',
        'created_by',
        'approved_by',
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function club(): BelongsTo
    {
        return $this->belongsTo(Club::class);
    }

    public function competition(): BelongsTo
    {
        return $this->belongsTo(Competition::class);
    }

    public function match(): BelongsTo
    {
        return $this->belongsTo(MatchModel::class);
    }

    public function captain(): BelongsTo
    {
        return $this->belongsTo(Player::class, 'captain_id');
    }

    public function viceCaptain(): BelongsTo
    {
        return $this->belongsTo(Player::class, 'vice_captain_id');
    }

    public function penaltyTaker(): BelongsTo
    {
        return $this->belongsTo(Player::class, 'penalty_taker_id');
    }

    public function freeKickTaker(): BelongsTo
    {
        return $this->belongsTo(Player::class, 'free_kick_taker_id');
    }

    public function cornerTaker(): BelongsTo
    {
        return $this->belongsTo(Player::class, 'corner_taker_id');
    }

    public function players(): HasMany
    {
        return $this->hasMany(LineupPlayer::class);
    }

    public function substitutes(): HasMany
    {
        return $this->hasMany(LineupPlayer::class)->where('is_substitute', true);
    }

    public function starters(): HasMany
    {
        return $this->hasMany(LineupPlayer::class)->where('is_substitute', false);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeByMatch($query, $matchId)
    {
        return $query->where('match_id', $matchId);
    }

    public function scopeByCompetition($query, $competitionId)
    {
        return $query->where('competition_id', $competitionId);
    }

    public function scopeByFormation($query, $formation)
    {
        return $query->where('formation', $formation);
    }

    public function getStartingEleven()
    {
        return $this->starters()->with('player')->orderBy('position_order')->get();
    }

    public function getSubstitutes()
    {
        return $this->substitutes()->with('player')->orderBy('position_order')->get();
    }

    public function getPlayerByPosition(string $position)
    {
        return $this->starters()->whereHas('player', function ($query) use ($position) {
            $query->where('position', $position);
        })->first();
    }

    public function getFormationAnalysis()
    {
        $starters = $this->getStartingEleven();
        
        $analysis = [
            'formation' => $this->formation,
            'total_rating' => $starters->sum(function ($lineupPlayer) {
                return $lineupPlayer->player->overall_rating;
            }),
            'average_rating' => $starters->avg(function ($lineupPlayer) {
                return $lineupPlayer->player->overall_rating;
            }),
            'positions' => $starters->groupBy(function ($lineupPlayer) {
                return $lineupPlayer->player->position;
            })->map->count(),
            'age_distribution' => [
                'average_age' => $starters->avg(function ($lineupPlayer) {
                    return $lineupPlayer->player->age;
                }),
                'youngest' => $starters->min(function ($lineupPlayer) {
                    return $lineupPlayer->player->age;
                }),
                'oldest' => $starters->max(function ($lineupPlayer) {
                    return $lineupPlayer->player->age;
                })
            ],
            'strengths' => [
                'attack' => $starters->whereIn('player.position', ['ST', 'RW', 'LW', 'CAM'])->avg('player.overall_rating'),
                'midfield' => $starters->whereIn('player.position', ['CM', 'CDM', 'CAM'])->avg('player.overall_rating'),
                'defense' => $starters->whereIn('player.position', ['CB', 'RB', 'LB'])->avg('player.overall_rating'),
                'goalkeeping' => $starters->where('player.position', 'GK')->avg('player.overall_rating'),
            ]
        ];

        return $analysis;
    }

    public function getTacticalInstructions()
    {
        return [
            'pressing_intensity' => $this->pressing_intensity,
            'possession_style' => $this->possession_style,
            'counter_attack_style' => $this->counter_attack_style,
            'defensive_line_height' => $this->defensive_line_height,
            'marking_system' => $this->marking_system,
            'set_pieces_strategy' => $this->set_pieces_strategy,
            'tactical_notes' => $this->tactical_notes,
            'substitutions_plan' => $this->substitutions_plan
        ];
    }

    public function validateLineup()
    {
        $errors = [];
        $starters = $this->getStartingEleven();
        $substitutes = $this->getSubstitutes();

        if ($starters->count() !== 11) {
            $errors[] = "Lineup must have exactly 11 starting players. Current: {$starters->count()}";
        }

        if ($substitutes->count() < 5) {
            $errors[] = "Lineup must have at least 5 substitutes. Current: {$substitutes->count()}";
        }

        $goalkeepers = $starters->where('player.position', 'GK');
        if ($goalkeepers->count() !== 1) {
            $errors[] = "Lineup must have exactly 1 goalkeeper. Current: {$goalkeepers->count()}";
        }

        $formation = $this->formation;
        $expectedPositions = $this->parseFormation($formation);
        $actualPositions = $starters->groupBy('player.position')->map->count();

        foreach ($expectedPositions as $position => $count) {
            $actual = $actualPositions->get($position, 0);
            if ($actual !== $count) {
                $errors[] = "Formation {$formation} requires {$count} {$position} players. Current: {$actual}";
            }
        }

        return $errors;
    }

    public function parseFormation(string $formation)
    {
        $parts = explode('-', $formation);
        $positions = [];

        if (count($parts) === 2) {
            $positions = [
                'GK' => 1,
                'CB' => $parts[0],
                'CM' => $parts[1],
                'ST' => 1
            ];
        } elseif (count($parts) === 3) {
            $positions = [
                'GK' => 1,
                'CB' => $parts[0],
                'CM' => $parts[1],
                'ST' => $parts[2]
            ];
        } elseif (count($parts) === 4) {
            $positions = [
                'GK' => 1,
                'CB' => $parts[0],
                'CM' => $parts[1],
                'CAM' => $parts[2],
                'ST' => $parts[3]
            ];
        }

        return $positions;
    }

    public function getLineupStrength()
    {
        $starters = $this->getStartingEleven();
        return $starters->avg(function ($lineupPlayer) {
            return $lineupPlayer->player->overall_rating;
        });
    }

    public function getBenchStrength()
    {
        $substitutes = $this->getSubstitutes();
        return $substitutes->avg(function ($lineupPlayer) {
            return $lineupPlayer->player->overall_rating;
        });
    }
} 