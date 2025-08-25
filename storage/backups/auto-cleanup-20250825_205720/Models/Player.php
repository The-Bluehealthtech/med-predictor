<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;

class Player extends Model
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'fifa_connect_id',
        'name',
        'first_name',
        'last_name',
        'date_of_birth',
        'nationality',
        'position',
        'club_id',
        'association_id',
        'height',
        'weight',
        'preferred_foot',
        'weak_foot',
        'skill_moves',
        'international_reputation',
        'work_rate',
        'body_type',
        'real_face',
        'release_clause_eur',
        'player_face_url',
        'player_picture',
        'club_logo_url',
        'nation_flag_url',
        'overall_rating',
        'potential_rating',
        'value_eur',
        'wage_eur',
        'age',
        'contract_valid_until',
        'fifa_version',
        'last_updated',
        // Player Dashboard fields
        'ghs_physical_score',
        'ghs_mental_score',
        'ghs_civic_score',
        'ghs_sleep_score',
        'ghs_overall_score',
        'ghs_color_code',
        'ghs_ai_suggestions',
        'ghs_last_updated',
        'contribution_score',
        'data_value_estimate',
        'matches_contributed',
        'training_sessions_logged',
        'health_records_contributed',
        'injury_risk_score',
        'injury_risk_level',
        'injury_risk_reason',
        'weekly_health_tips',
        'injury_risk_last_assessed',
        'match_availability',
        'last_availability_update',
        'last_data_export',
        'data_export_count',
        // FIFA Sync fields
        'fifa_sync_status',
        'fifa_sync_date',
        'fifa_last_error'
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'contract_valid_until' => 'date',
        'last_updated' => 'datetime',
        'height' => 'integer',
        'weight' => 'integer',
        'weak_foot' => 'integer',
        'skill_moves' => 'integer',
        'international_reputation' => 'integer',
        'release_clause_eur' => 'float',
        'overall_rating' => 'integer',
        'potential_rating' => 'integer',
        'value_eur' => 'float',
        'wage_eur' => 'float',
        'age' => 'integer',
        'real_face' => 'boolean',
        // Player Dashboard casts
        'ghs_physical_score' => 'decimal:2',
        'ghs_mental_score' => 'decimal:2',
        'ghs_civic_score' => 'decimal:2',
        'ghs_sleep_score' => 'decimal:2',
        'ghs_overall_score' => 'decimal:2',
        'ghs_ai_suggestions' => 'array',
        'ghs_last_updated' => 'datetime',
        'contribution_score' => 'decimal:2',
        'data_value_estimate' => 'decimal:2',
        'injury_risk_score' => 'decimal:4',
        'weekly_health_tips' => 'array',
        'injury_risk_last_assessed' => 'datetime',
        'match_availability' => 'boolean',
        'last_availability_update' => 'datetime',
        'last_data_export' => 'datetime',
        // FIFA Sync casts
        'fifa_sync_date' => 'datetime'
    ];

    public function club(): BelongsTo
    {
        return $this->belongsTo(Club::class);
    }

    public function association(): BelongsTo
    {
        return $this->belongsTo(Association::class);
    }

    public function fifaConnectId(): BelongsTo
    {
        return $this->belongsTo(FifaConnectId::class);
    }

    public function user(): HasOne
    {
        return $this->hasOne(User::class, 'player_id');
    }

    public function fifaSyncLogs(): HasMany
    {
        return $this->hasMany(FifaSyncLog::class, 'entity_id')
                    ->where('entity_type', 'player');
    }

    public function healthRecords(): HasMany
    {
        return $this->hasMany(HealthRecord::class);
    }

    public function medicalPredictions(): HasMany
    {
        return $this->hasMany(MedicalPrediction::class);
    }

    public function performances(): HasMany
    {
        return $this->hasMany(PlayerPerformance::class);
    }

    public function matchStats(): HasMany
    {
        return $this->hasMany(\App\Models\PlayerMatchDetailedStats::class, 'player_id');
    }

    public function passport(): HasOne
    {
        return $this->hasOne(PlayerPassport::class);
    }

    public function performanceRecommendations(): HasMany
    {
        return $this->hasMany(PerformanceRecommendation::class);
    }

    public function licenses(): HasMany
    {
        return $this->hasMany(\App\Models\PlayerLicense::class);
    }

    public function playerLicenses(): HasMany
    {
        return $this->hasMany(\App\Models\PlayerLicense::class, 'player_id');
    }

    public function pcmas(): HasMany
    {
        return $this->hasMany(PCMA::class);
    }

    public function nationalTeamCallups(): HasMany
    {
        return $this->hasMany(NationalTeamCallup::class);
    }

    public function trainingSessions(): HasMany
    {
        return $this->hasMany(TrainingSession::class);
    }

    public function medicalAppointments(): HasMany
    {
        return $this->hasMany(MedicalAppointment::class);
    }

    public function socialMediaAlerts(): HasMany
    {
        return $this->hasMany(SocialMediaAlert::class);
    }

    public function matchPerformances(): HasMany
    {
        return $this->hasMany(MatchPerformance::class);
    }

    public function matchMetrics(): \Illuminate\Database\Eloquent\Relations\HasManyThrough
    {
        return $this->hasManyThrough(MatchMetric::class, MatchPerformance::class);
    }

    public function trophies(): HasMany
    {
        return $this->hasMany(PlayerTrophy::class);
    }

    /**
     * Get the postural assessments for the player.
     */
    public function posturalAssessments(): HasMany
    {
        return $this->hasMany(PosturalAssessment::class);
    }

    /**
     * Get the most recent valid PCMA for this player
     */
    public function getCurrentPCMA()
    {
        return $this->pcmas()
            ->where('status', 'completed')
            ->where('fifa_compliant', true)
            ->where('completed_at', '>=', now()->subYear())
            ->orderBy('completed_at', 'desc')
            ->first();
    }

    /**
     * Check if player has valid PCMA
     */
    public function hasValidPCMA(): bool
    {
        return $this->pcmas()
            ->where('status', 'completed')
            ->where('fifa_compliant', true)
            ->where('completed_at', '>=', now()->subYear())
            ->exists();
    }

    /**
     * Get PCMA history for this player
     */
    public function getPCMAHistory()
    {
        return $this->pcmas()
            ->where('status', 'completed')
            ->orderBy('completed_at', 'desc')
            ->get();
    }

    /**
     * Check if PCMA is due for renewal (expires within 30 days)
     */
    public function isPCMADueForRenewal(): bool
    {
        $currentPCMA = $this->getCurrentPCMA();
        if (!$currentPCMA) {
            return true; // No valid PCMA, renewal needed
        }
        
        return $currentPCMA->completed_at->addYear()->subDays(30)->isPast();
    }

    public function teamPlayers(): HasMany
    {
        return $this->hasMany(TeamPlayer::class);
    }

    public function seasonStats(): HasMany
    {
        return $this->hasMany(PlayerSeasonStat::class);
    }

    public function matchEvents(): HasMany
    {
        return $this->hasMany(MatchEvent::class);
    }

    public function matchRosterPlayers(): HasMany
    {
        return $this->hasMany(MatchRosterPlayer::class);
    }

    public function matchRosters()
    {
        return $this->hasMany(\App\Models\MatchRoster::class, 'player_id');
    }

    public function teams()
    {
        return $this->belongsToMany(Team::class, 'team_players')
                    ->withPivot(['role', 'squad_number', 'joined_date', 'contract_end_date', 'position_preference', 'notes', 'status'])
                    ->withTimestamps();
    }

    public function competitions()
    {
        return $this->belongsToMany(Competition::class, 'player_season_stats')
                    ->withPivot(['matches_played', 'minutes_played', 'goals', 'assists', 'yellow_cards', 'red_cards', 'clean_sheets', 'saves', 'goals_conceded'])
                    ->withTimestamps();
    }

    public function team()
    {
        return $this->belongsTo(\App\Models\Team::class, 'team_id');
    }

    public function getFullNameAttribute(): string
    {
        return trim($this->first_name . ' ' . $this->last_name);
    }

    public function getBmiAttribute(): float
    {
        if ($this->height && $this->weight) {
            $heightInMeters = $this->height / 100;
            return round($this->weight / ($heightInMeters * $heightInMeters), 2);
        }
        return 0;
    }

    public function getBmiCategoryAttribute(): string
    {
        $bmi = $this->bmi;
        if ($bmi < 18.5) return 'Insuffisance pondérale';
        if ($bmi < 25) return 'Normal';
        if ($bmi < 30) return 'Surpoids';
        return 'Obésité';
    }

    public function getAgeAttribute(): int
    {
        return $this->date_of_birth ? (int) $this->date_of_birth->age : 0;
    }

    public function getCurrentTeamAttribute()
    {
        return $this->teamPlayers()
                    ->where('status', 'active')
                    ->with('team.club')
                    ->first();
    }

    public function getCurrentSquadNumberAttribute()
    {
        $currentTeam = $this->currentTeam;
        return $currentTeam ? $currentTeam->squad_number : null;
    }

    public function getCurrentRoleAttribute()
    {
        $currentTeam = $this->currentTeam;
        return $currentTeam ? $currentTeam->role : null;
    }

    public function getTotalMatchesPlayedAttribute()
    {
        return $this->seasonStats()->sum('matches_played');
    }

    public function getTotalGoalsAttribute()
    {
        return $this->seasonStats()->sum('goals');
    }

    public function getTotalAssistsAttribute()
    {
        return $this->seasonStats()->sum('assists');
    }

    public function getTotalYellowCardsAttribute()
    {
        return $this->seasonStats()->sum('yellow_cards');
    }

    public function getTotalRedCardsAttribute()
    {
        return $this->seasonStats()->sum('red_cards');
    }

    public function getTotalCleanSheetsAttribute()
    {
        return $this->seasonStats()->sum('clean_sheets');
    }

    public function getTotalMinutesPlayedAttribute()
    {
        return $this->seasonStats()->sum('minutes_played');
    }

    public function getGoalsPerMatchAttribute()
    {
        $matches = $this->total_matches_played;
        return $matches > 0 ? round($this->total_goals / $matches, 2) : 0;
    }

    public function getAssistsPerMatchAttribute()
    {
        $matches = $this->total_matches_played;
        return $matches > 0 ? round($this->total_assists / $matches, 2) : 0;
    }

    public function getMinutesPerMatchAttribute()
    {
        $matches = $this->total_matches_played;
        return $matches > 0 ? round($this->total_minutes_played / $matches, 2) : 0;
    }

    public function getCurrentSeasonStatsAttribute()
    {
        return $this->seasonStats()
                    ->with('competition')
                    ->orderBy('created_at', 'desc')
                    ->get();
    }

    public function getRecentMatchEventsAttribute()
    {
        return $this->matchEvents()
                    ->with(['match', 'team'])
                    ->orderBy('created_at', 'desc')
                    ->limit(10)
                    ->get();
    }

    public function getPlayerPictureUrlAttribute()
    {
        // First try uploaded player picture
        if ($this->player_picture) {
            // Check if it's an external URL (like DiceBear)
            if (filter_var($this->player_picture, FILTER_VALIDATE_URL)) {
                return $this->player_picture;
            }
            // Otherwise, it's a stored file
            return asset('storage/' . $this->player_picture);
        }
        
        // Then try FIFA face URL
        if ($this->player_face_url) {
            return $this->player_face_url;
        }
        
        // Return null if no picture available
        return null;
    }

    public function hasPictureAttribute()
    {
        return !empty($this->player_picture) || !empty($this->player_face_url);
    }

    // Player Dashboard Relationships
    public function documents(): HasMany
    {
        return $this->hasMany(PlayerDocument::class);
    }

    public function fitnessLogs(): HasMany
    {
        return $this->hasMany(PlayerFitnessLog::class);
    }

    // Player Dashboard Methods
    public function calculateGHS(): void
    {
        $scores = [
            $this->ghs_physical_score ?? 0,
            $this->ghs_mental_score ?? 0,
            $this->ghs_civic_score ?? 0,
            $this->ghs_sleep_score ?? 0
        ];

        $validScores = array_filter($scores, function($score) { return $score > 0; });
        
        if (empty($validScores)) {
            $this->ghs_overall_score = null;
            $this->ghs_color_code = null;
            return;
        }

        $this->ghs_overall_score = round(array_sum($validScores) / count($validScores), 2);
        
        // Determine color code
        if ($this->ghs_overall_score >= 80) {
            $this->ghs_color_code = 'blue';
        } elseif ($this->ghs_overall_score >= 60) {
            $this->ghs_color_code = 'green';
        } else {
            $this->ghs_color_code = 'red';
        }

        $this->ghs_last_updated = now();
    }

    public function getGHSColorClass(): string
    {
        return match($this->ghs_color_code) {
            'blue' => 'text-blue-600 bg-blue-50',
            'green' => 'text-green-600 bg-green-50',
            'red' => 'text-red-600 bg-red-50',
            default => 'text-gray-600 bg-gray-50'
        };
    }

    public function getInjuryRiskColorClass(): string
    {
        return match($this->injury_risk_level) {
            'low' => 'text-green-600 bg-green-50',
            'medium' => 'text-yellow-600 bg-yellow-50',
            'high' => 'text-red-600 bg-red-50',
            default => 'text-gray-600 bg-gray-50'
        };
    }

    public function updateContributionScore(): void
    {
        $totalContributions = $this->matches_contributed + $this->training_sessions_logged + $this->health_records_contributed;
        
        if ($totalContributions > 0) {
            $this->contribution_score = min(100, round(($totalContributions / 100) * 100, 2));
        } else {
            $this->contribution_score = 0;
        }
    }

    public function calculateDataValue(): void
    {
        $baseValue = 100; // Base value per data point
        $totalDataPoints = $this->matches_contributed + $this->training_sessions_logged + $this->health_records_contributed;
        
        // Add premium for active players
        $activityMultiplier = $this->match_availability ? 1.5 : 1.0;
        
        // Add premium for high-performing players
        $performanceMultiplier = 1.0;
        if ($this->overall_rating && $this->overall_rating > 80) {
            $performanceMultiplier = 1.3;
        } elseif ($this->overall_rating && $this->overall_rating > 70) {
            $performanceMultiplier = 1.1;
        }
        
        $this->data_value_estimate = round($totalDataPoints * $baseValue * $activityMultiplier * $performanceMultiplier, 2);
    }

    public function getCurrentLicense()
    {
        return $this->playerLicenses()
                    ->where('status', 'active')
                    ->where('expiry_date', '>', now())
                    ->latest('issued_date')
                    ->first();
    }

    public function getUpcomingMatches($limit = 5)
    {
        return $this->matchRosters()
                    ->with(['match.homeTeam', 'match.awayTeam', 'match.competition'])
                    ->whereHas('match', function($query) {
                        $query->where('match_date', '>', now());
                    })
                    ->orderBy('match_date')
                    ->limit($limit)
                    ->get();
    }

    public function getRecentPerformanceStats($days = 30)
    {
        $startDate = now()->subDays($days);
        
        return $this->matchEvents()
                    ->where('created_at', '>=', $startDate)
                    ->selectRaw('
                        COUNT(*) as total_events,
                        SUM(CASE WHEN event_type = "goal" THEN 1 ELSE 0 END) as goals,
                        SUM(CASE WHEN event_type = "assist" THEN 1 ELSE 0 END) as assists,
                        SUM(CASE WHEN event_type = "yellow_card" THEN 1 ELSE 0 END) as yellow_cards,
                        SUM(CASE WHEN event_type = "red_card" THEN 1 ELSE 0 END) as red_cards
                    ')
                    ->first();
    }

    public function generateWeeklyHealthTips(): array
    {
        $tips = [];
        
        // Generate tips based on GHS scores
        if ($this->ghs_physical_score && $this->ghs_physical_score < 70) {
            $tips[] = 'Consider increasing your physical training intensity gradually';
        }
        
        if ($this->ghs_sleep_score && $this->ghs_sleep_score < 70) {
            $tips[] = 'Focus on improving sleep quality - aim for 7-9 hours per night';
        }
        
        if ($this->ghs_mental_score && $this->ghs_mental_score < 70) {
            $tips[] = 'Practice stress management techniques and mental wellness exercises';
        }
        
        // Generate tips based on injury risk
        if ($this->injury_risk_level === 'high') {
            $tips[] = 'High injury risk detected - consult with medical staff for preventive measures';
        } elseif ($this->injury_risk_level === 'medium') {
            $tips[] = 'Moderate injury risk - focus on proper warm-up and recovery protocols';
        }
        
        // Add general tips if no specific ones
        if (empty($tips)) {
            $tips = [
                'Maintain consistent training schedule',
                'Stay hydrated throughout the day',
                'Follow proper nutrition guidelines',
                'Get adequate rest between sessions'
            ];
        }
        
        return array_slice($tips, 0, 3); // Return max 3 tips
    }
}
