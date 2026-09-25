<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class MatchSheet extends Model
{
    use HasFactory;

    protected $fillable = [
        'match_id',
        'match_number',
        'stadium_venue',
        'weather_conditions',
        'pitch_conditions',
        'kickoff_time',
        'home_team_roster',
        'away_team_roster',
        'home_team_substitutes',
        'away_team_substitutes',
        'home_team_coach',
        'away_team_coach',
        'home_team_manager',
        'away_team_manager',
        'referee_id',
        'main_referee_id',
        'assistant_referee_1_id',
        'assistant_referee_2_id',
        'fourth_official_id',
        'var_referee_id',
        'var_assistant_id',
        'match_statistics',
        'home_team_score',
        'away_team_score',
        'referee_report',
        'match_status',
        'suspension_reason',
        'crowd_issues',
        'protests_incidents',
        'notes',
        'home_team_signature',
        'away_team_signature',
        'home_team_signed_at',
        'away_team_signed_at',
        'match_observer_id',
        'observer_comments',
        'observer_signed_at',
        'referee_digital_signature',
        'referee_signed_at',
        'penalty_shootout_data',
        'var_decisions',
        'status',
        'submitted_at',
        'validated_at',
        'validated_by',
        'validation_notes',
        'rejected_at',
        'rejected_by',
        'rejection_reason',
        'version',
        'change_log',
        // Stage-based fields
        'stage',
        'stage_in_progress_at',
        'stage_before_game_signed_at',
        'stage_after_game_signed_at',
        'stage_fa_validated_at',
        'home_team_lineup_signature',
        'away_team_lineup_signature',
        'home_team_lineup_signed_at',
        'away_team_lineup_signed_at',
        'home_team_post_match_signature',
        'away_team_post_match_signature',
        'home_team_post_match_signed_at',
        'away_team_post_match_signed_at',
        'home_team_post_match_comments',
        'away_team_post_match_comments',
        'fa_validated_by',
        'fa_validation_notes',
        'assigned_referee_id',
        'referee_assigned_at',
        'lineups_locked',
        'lineups_locked_at',
        'match_events_locked',
        'match_events_locked_at',
        'stage_transition_log',
        'user_action_log',
        'signed_sheet_path',
    ];

    protected $casts = [
        'home_team_roster' => 'array',
        'away_team_roster' => 'array',
        'home_team_substitutes' => 'array',
        'away_team_substitutes' => 'array',
        'match_statistics' => 'array',
        'penalty_shootout_data' => 'array',
        'var_decisions' => 'array',
        'change_log' => 'array',
        'stage_transition_log' => 'array',
        'user_action_log' => 'array',
        'kickoff_time' => 'datetime',
        'submitted_at' => 'datetime',
        'validated_at' => 'datetime',
        'rejected_at' => 'datetime',
        'home_team_signed_at' => 'datetime',
        'away_team_signed_at' => 'datetime',
        'observer_signed_at' => 'datetime',
        'referee_signed_at' => 'datetime',
        'stage_in_progress_at' => 'datetime',
        'stage_before_game_signed_at' => 'datetime',
        'stage_after_game_signed_at' => 'datetime',
        'stage_fa_validated_at' => 'datetime',
        'home_team_lineup_signed_at' => 'datetime',
        'away_team_lineup_signed_at' => 'datetime',
        'home_team_post_match_signed_at' => 'datetime',
        'away_team_post_match_signed_at' => 'datetime',
        'referee_assigned_at' => 'datetime',
        'lineups_locked_at' => 'datetime',
        'match_events_locked_at' => 'datetime',
        'lineups_locked' => 'boolean',
        'match_events_locked' => 'boolean',
    ];

    // Relationships
    public function match(): BelongsTo
    {
        return $this->belongsTo(MatchModel::class, 'match_id');
    }

    public function referee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'referee_id');
    }

    public function mainReferee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'main_referee_id');
    }

    public function assistantReferee1(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assistant_referee_1_id');
    }

    public function assistantReferee2(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assistant_referee_2_id');
    }

    public function fourthOfficial(): BelongsTo
    {
        return $this->belongsTo(User::class, 'fourth_official_id');
    }

    public function varReferee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'var_referee_id');
    }

    public function varAssistant(): BelongsTo
    {
        return $this->belongsTo(User::class, 'var_assistant_id');
    }

    public function matchObserver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'match_observer_id');
    }

    public function validator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'validated_by');
    }

    public function rejector(): BelongsTo
    {
        return $this->belongsTo(User::class, 'rejected_by');
    }

    public function faValidator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'fa_validated_by');
    }

    public function assignedReferee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_referee_id');
    }

    public function events(): HasMany
    {
        return $this->hasMany(MatchEvent::class, 'match_id', 'match_id');
    }

    public function homeTeamRoster(): HasOne
    {
        return $this->hasOne(MatchRoster::class, 'match_id', 'match_id')
            ->where('team_id', $this->match->home_team_id ?? null);
    }

    public function awayTeamRoster(): HasOne
    {
        return $this->hasOne(MatchRoster::class, 'match_id', 'match_id')
            ->where('team_id', $this->match->away_team_id ?? null);
    }

    // Status Methods
    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'draft' => 'Draft',
            'submitted' => 'Submitted',
            'validated' => 'Validated',
            'rejected' => 'Rejected',
            default => ucfirst($this->status),
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'draft' => 'gray',
            'submitted' => 'yellow',
            'validated' => 'green',
            'rejected' => 'red',
            default => 'gray',
        };
    }

    public function getMatchStatusLabelAttribute(): string
    {
        return match($this->match_status) {
            'completed' => 'Completed',
            'suspended' => 'Suspended',
            'abandoned' => 'Abandoned',
            default => ucfirst($this->match_status),
        };
    }

    public function getMatchStatusColorAttribute(): string
    {
        return match($this->match_status) {
            'completed' => 'green',
            'suspended' => 'yellow',
            'abandoned' => 'red',
            default => 'gray',
        };
    }

    // Stage Methods
    public function getStageLabelAttribute(): string
    {
        return match($this->stage) {
            'in_progress' => 'In Progress',
            'before_game_signed' => 'Before Game Signed',
            'after_game_signed' => 'After Game Signed',
            'fa_validated' => 'FA Validated',
            default => ucfirst($this->stage),
        };
    }

    public function getStageColorAttribute(): string
    {
        return match($this->stage) {
            'in_progress' => 'blue',
            'before_game_signed' => 'yellow',
            'after_game_signed' => 'orange',
            'fa_validated' => 'green',
            default => 'gray',
        };
    }

    public function isInProgress(): bool
    {
        return $this->stage === 'in_progress';
    }

    public function isBeforeGameSigned(): bool
    {
        return $this->stage === 'before_game_signed';
    }

    public function isAfterGameSigned(): bool
    {
        return $this->stage === 'after_game_signed';
    }

    public function isFaValidated(): bool
    {
        return $this->stage === 'fa_validated';
    }

    public function canTransitionToStage(string $newStage): bool
    {
        $allowedTransitions = [
            'in_progress' => ['before_game_signed'],
            'before_game_signed' => ['after_game_signed'],
            'after_game_signed' => ['fa_validated'],
            'fa_validated' => [], // Final stage
        ];

        return in_array($newStage, $allowedTransitions[$this->stage] ?? []);
    }

    public function transitionToStage(string $newStage, User $user): bool
    {
        if (!$this->canTransitionToStage($newStage)) {
            return false;
        }

        $this->update([
            'stage' => $newStage,
            "stage_{$newStage}_at" => now(),
        ]);

        $this->logStageTransition($newStage, $user);
        return true;
    }

    public function logStageTransition(string $newStage, User $user): void
    {
        $log = $this->stage_transition_log ?? [];
        $log[] = [
            'from_stage' => $this->stage,
            'to_stage' => $newStage,
            'user_id' => $user->id,
            'user_name' => $user->name,
            'timestamp' => now()->toISOString(),
        ];

        $this->update(['stage_transition_log' => $log]);
    }

    public function logUserAction(string $action, User $user, array $details = []): void
    {
        $log = $this->user_action_log ?? [];
        $log[] = [
            'action' => $action,
            'user_id' => $user->id,
            'user_name' => $user->name,
            'timestamp' => now()->toISOString(),
            'details' => $details,
        ];

        $this->update(['user_action_log' => $log]);
    }

    // Permission Methods
    public function canEdit(User $user): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        // Team officials can edit lineups in stage 1
        if ($this->isInProgress() && $this->canTeamOfficialEdit($user)) {
            return true;
        }

        // Referees can edit match events in stage 2
        if ($this->isBeforeGameSigned() && $this->canRefereeEdit($user)) {
            return true;
        }

        return false;
    }

    public function canTeamOfficialEdit(User $user): bool
    {
        // Check if user is a team official for either team
        $homeTeamId = $this->match->home_team_id ?? null;
        $awayTeamId = $this->match->away_team_id ?? null;
        
        return $user->isTeamOfficial() && 
               ($user->team_id === $homeTeamId || $user->team_id === $awayTeamId);
    }

    public function canRefereeEdit(User $user): bool
    {
        return $user->isReferee() && $this->assigned_referee_id === $user->id;
    }

    public function canFaAdminValidate(User $user): bool
    {
        return $user->isAdmin() && $this->isAfterGameSigned();
    }

    public function canAssignReferee(User $user): bool
    {
        return $user->isAdmin() && $this->isInProgress();
    }

    public function canSignLineup(User $user, string $teamType): bool
    {
        if (!$this->isInProgress()) {
            return false;
        }

        $teamId = $teamType === 'home' ? $this->match->home_team_id : $this->match->away_team_id;
        return $user->isTeamOfficial() && $user->team_id === $teamId;
    }

    public function canSignPostMatch(User $user, string $teamType): bool
    {
        if (!$this->isAfterGameSigned()) {
            return false;
        }

        $teamId = $teamType === 'home' ? $this->match->home_team_id : $this->match->away_team_id;
        return $user->isTeamOfficial() && $user->team_id === $teamId;
    }

    public function canValidate(User $user): bool
    {
        return $this->canFaAdminValidate($user);
    }

    public function canReject(User $user): bool
    {
        return $this->canFaAdminValidate($user);
    }

    public function canView(User $user): bool
    {
        // System admins can always view
        if ($user->isSystemAdmin()) {
            return true;
        }

        // Association admins can always view
        if ($user->isAssociationAdmin()) {
            return true;
        }

        // FA validated match sheets are publicly viewable
        if ($this->isFaValidated()) {
            return true;
        }

        // Referees can view if assigned to this match
        if ($user->isReferee() && $this->assigned_referee_id === $user->id) {
            return true;
        }

        // Club users (admin, manager) can view if associated with either team
        if ($user->isClubUser()) {
            $homeTeamId = $this->match->home_team_id ?? null;
            $awayTeamId = $this->match->away_team_id ?? null;
            
            // Check if user's club is associated with either team
            if ($user->club_id) {
                $homeTeamClubId = $this->match->homeTeam->club_id ?? null;
                $awayTeamClubId = $this->match->awayTeam->club_id ?? null;
                
                return $user->club_id === $homeTeamClubId || $user->club_id === $awayTeamClubId;
            }
            
            // Fallback to team_id check
            return $user->team_id === $homeTeamId || $user->team_id === $awayTeamId;
        }

        // Committee members can view (for league championship)
        if ($user->hasRole('committee')) {
            return true;
        }

        return false;
    }

    // Business Logic Methods
    public function submit(): void
    {
        $this->update([
            'status' => 'submitted',
            'submitted_at' => now(),
        ]);
    }

    public function validate(User $validator, ?string $notes = null): void
    {
        $this->update([
            'status' => 'validated',
            'validated_at' => now(),
            'validated_by' => $validator->id,
            'validation_notes' => $notes,
        ]);
    }

    public function reject(User $rejector, string $reason): void
    {
        $this->update([
            'status' => 'rejected',
            'rejected_at' => now(),
            'rejected_by' => $rejector->id,
            'rejection_reason' => $reason,
        ]);
    }

    public function signByTeam(string $teamType, string $signature): void
    {
        if ($teamType === 'home') {
            $this->update([
                'home_team_signature' => $signature,
                'home_team_signed_at' => now(),
            ]);
        } else {
            $this->update([
                'away_team_signature' => $signature,
                'away_team_signed_at' => now(),
            ]);
        }
    }

    public function signByReferee(string $signature): void
    {
        $this->update([
            'referee_digital_signature' => $signature,
            'referee_signed_at' => now(),
        ]);
    }

    public function signByObserver(string $signature, ?string $comments = null): void
    {
        $this->update([
            'observer_comments' => $comments,
            'observer_signed_at' => now(),
        ]);
    }

    // Stage-based business logic methods
    public function assignReferee(User $referee, User $admin): bool
    {
        if (!$this->canAssignReferee($admin)) {
            return false;
        }

        $this->update([
            'assigned_referee_id' => $referee->id,
            'referee_assigned_at' => now(),
        ]);

        $this->logUserAction('referee_assigned', $admin, ['referee_id' => $referee->id]);
        return true;
    }

    public function signLineup(string $teamType, User $user, string $signature): bool
    {
        if (!$this->canSignLineup($user, $teamType)) {
            return false;
        }

        $field = $teamType === 'home' ? 'home_team_lineup_signature' : 'away_team_lineup_signature';
        $timestampField = $teamType === 'home' ? 'home_team_lineup_signed_at' : 'away_team_lineup_signed_at';

        $this->update([
            $field => $signature,
            $timestampField => now(),
        ]);

        $this->logUserAction('lineup_signed', $user, ['team_type' => $teamType]);

        // Check if both teams have signed lineups
        if ($this->home_team_lineup_signature && $this->away_team_lineup_signature) {
            $this->lockLineups();
            $this->transitionToStage('before_game_signed', $user);
        }

        return true;
    }

    public function lockLineups(): void
    {
        $this->update([
            'lineups_locked' => true,
            'lineups_locked_at' => now(),
        ]);
    }

    public function unlockLineups(): void
    {
        $this->update([
            'lineups_locked' => false,
            'lineups_locked_at' => null,
        ]);
    }

    public function lockMatchEvents(): void
    {
        $this->update([
            'match_events_locked' => true,
            'match_events_locked_at' => now(),
        ]);
    }

    public function unlockMatchEvents(): void
    {
        $this->update([
            'match_events_locked' => false,
            'match_events_locked_at' => null,
        ]);
    }

    public function signPostMatch(string $teamType, User $user, string $signature, ?string $comments = null): bool
    {
        if (!$this->canSignPostMatch($user, $teamType)) {
            return false;
        }

        $signatureField = $teamType === 'home' ? 'home_team_post_match_signature' : 'away_team_post_match_signature';
        $timestampField = $teamType === 'home' ? 'home_team_post_match_signed_at' : 'away_team_post_match_signed_at';
        $commentsField = $teamType === 'home' ? 'home_team_post_match_comments' : 'away_team_post_match_comments';

        $this->update([
            $signatureField => $signature,
            $timestampField => now(),
            $commentsField => $comments,
        ]);

        $this->logUserAction('post_match_signed', $user, ['team_type' => $teamType]);

        // Check if both teams have signed post-match
        if ($this->home_team_post_match_signature && $this->away_team_post_match_signature) {
            $this->transitionToStage('after_game_signed', $user);
        }

        return true;
    }

    public function faValidate(User $admin, ?string $notes = null): bool
    {
        if (!$this->canFaAdminValidate($admin)) {
            return false;
        }

        $this->update([
            'fa_validated_by' => $admin->id,
            'fa_validation_notes' => $notes,
        ]);

        $this->transitionToStage('fa_validated', $admin);
        $this->logUserAction('fa_validated', $admin, ['notes' => $notes]);

        return true;
    }

    public function getStageProgress(): array
    {
        $stages = [
            'in_progress' => 'Stage 1: In Progress',
            'before_game_signed' => 'Stage 2: Before Game Signed',
            'after_game_signed' => 'Stage 3: After Game Signed',
            'fa_validated' => 'Stage 4: FA Validated',
        ];

        $currentStageIndex = array_search($this->stage, array_keys($stages));
        $progress = [];

        foreach ($stages as $stage => $label) {
            $stageIndex = array_search($stage, array_keys($stages));
            $progress[$stage] = [
                'label' => $label,
                'completed' => $stageIndex <= $currentStageIndex,
                'current' => $stage === $this->stage,
                'timestamp' => $this->{"stage_{$stage}_at"},
            ];
        }

        return $progress;
    }

    // Statistics Methods
    public function getTotalGoals(): int
    {
        return ($this->home_team_score ?? 0) + ($this->away_team_score ?? 0);
    }

    public function getGoalDifference(): int
    {
        return ($this->home_team_score ?? 0) - ($this->away_team_score ?? 0);
    }

    public function getWinner(): ?string
    {
        if ($this->home_team_score === null || $this->away_team_score === null) {
            return null;
        }

        if ($this->home_team_score > $this->away_team_score) {
            return 'home';
        } elseif ($this->away_team_score > $this->home_team_score) {
            return 'away';
        } else {
            return 'draw';
        }
    }

    // Export Methods
    public function toJsonExport(): array
    {
        return [
            'match_sheet_id' => $this->id,
            'match_number' => $this->match_number,
            'competition' => $this->match->competition->name ?? null,
            'season' => $this->match->competition->season ?? null,
            'matchday' => $this->match->matchday ?? null,
            'date' => $this->match->match_date ? (is_string($this->match->match_date) ? $this->match->match_date : $this->match->match_date->format('Y-m-d')) : null,
            'kickoff_time' => $this->kickoff_time ? (is_string($this->kickoff_time) ? $this->kickoff_time : $this->kickoff_time->format('H:i')) : null,
            'stadium_venue' => $this->stadium_venue,
            'weather_conditions' => $this->weather_conditions,
            'pitch_conditions' => $this->pitch_conditions,
            'home_team' => $this->match->homeTeam->name ?? null,
            'away_team' => $this->match->awayTeam->name ?? null,
            'home_team_score' => $this->home_team_score,
            'away_team_score' => $this->away_team_score,
            'home_team_roster' => $this->home_team_roster,
            'away_team_roster' => $this->away_team_roster,
            'home_team_substitutes' => $this->home_team_substitutes,
            'away_team_substitutes' => $this->away_team_substitutes,
            'officials' => [
                'main_referee' => $this->mainReferee?->name ?? null,
                'assistant_referee_1' => $this->assistantReferee1?->name ?? null,
                'assistant_referee_2' => $this->assistantReferee2?->name ?? null,
                'fourth_official' => $this->fourthOfficial?->name ?? null,
                'var_referee' => $this->varReferee?->name ?? null,
                'var_assistant' => $this->varAssistant?->name ?? null,
            ],
            'match_statistics' => $this->match_statistics,
            'referee_report' => $this->referee_report,
            'match_status' => $this->match_status,
            'status' => $this->status,
            'version' => $this->version,
            'created_at' => $this->created_at ? (is_string($this->created_at) ? $this->created_at : $this->created_at->format('Y-m-d H:i:s')) : null,
            'updated_at' => $this->updated_at ? (is_string($this->updated_at) ? $this->updated_at : $this->updated_at->format('Y-m-d H:i:s')) : null,
        ];
    }
}
