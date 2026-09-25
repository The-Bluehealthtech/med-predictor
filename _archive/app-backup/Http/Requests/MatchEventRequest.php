<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\GameMatch;
use App\Models\Player;
use App\Models\Team;
use Illuminate\Support\Facades\DB;

class MatchEventRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'event_type' => 'required|string|in:' . implode(',', $this->getValidEventTypes()),
            'player_id' => 'nullable|exists:players,id',
            'team_id' => 'required|exists:teams,id',
            'minute' => 'required|integer|min:1|max:120',
            'extra_time_minute' => 'nullable|integer|min:1|max:10',
            'period' => 'required|string|in:first_half,second_half,extra_time_first,extra_time_second',
            'description' => 'nullable|string|max:500',
            'location' => 'nullable|string|max:100',
            'severity' => 'nullable|string|in:low,medium,high',
            'event_data' => 'nullable|array',
            'assisted_by_player_id' => 'nullable|exists:players,id',
            'substituted_player_id' => 'nullable|exists:players,id',
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $this->validateEventSpecificRules($validator);
            $this->validatePlayerEligibility($validator);
            $this->validateMatchTimeline($validator);
            $this->validateTeamParticipation($validator);
        });
    }

    protected function validateEventSpecificRules($validator): void
    {
        $eventType = $this->input('event_type');
        $playerId = $this->input('player_id');
        $assistedByPlayerId = $this->input('assisted_by_player_id');
        $substitutedPlayerId = $this->input('substituted_player_id');

        // Goal events require a player
        if (in_array($eventType, ['goal', 'own_goal', 'penalty_goal', 'free_kick_goal', 'header_goal', 'volley_goal', 'long_range_goal'])) {
            if (!$playerId) {
                $validator->errors()->add('player_id', 'A player is required for goal events.');
            }
        }

        // Assist events require both scorer and assister
        if ($eventType === 'assist') {
            if (!$playerId) {
                $validator->errors()->add('player_id', 'Assister player is required.');
            }
            if (!$assistedByPlayerId) {
                $validator->errors()->add('assisted_by_player_id', 'Scorer player is required for assist events.');
            }
            if ($playerId === $assistedByPlayerId) {
                $validator->errors()->add('assisted_by_player_id', 'A player cannot assist themselves.');
            }
        }

        // Substitution events require both players
        if (in_array($eventType, ['substitution_in', 'substitution_out'])) {
            if (!$playerId) {
                $validator->errors()->add('player_id', 'Substituted player is required.');
            }
            if (!$substitutedPlayerId) {
                $validator->errors()->add('substituted_player_id', 'Replacement player is required for substitutions.');
            }
            if ($playerId === $substitutedPlayerId) {
                $validator->errors()->add('substituted_player_id', 'A player cannot be substituted for themselves.');
            }
        }

        // Card events require a player
        if (in_array($eventType, ['yellow_card', 'red_card'])) {
            if (!$playerId) {
                $validator->errors()->add('player_id', 'A player is required for card events.');
            }
        }

        // Injury events require a player
        if ($eventType === 'injury') {
            if (!$playerId) {
                $validator->errors()->add('player_id', 'A player is required for injury events.');
            }
        }
    }

    protected function validatePlayerEligibility($validator): void
    {
        $playerId = $this->input('player_id');
        $teamId = $this->input('team_id');
        $matchId = $this->route('gameMatch')->id;

        if ($playerId && $teamId) {
            // Check if player is in the team's roster for this match
            $isInRoster = DB::table('match_roster_players')
                ->join('match_rosters', 'match_roster_players.match_roster_id', '=', 'match_rosters.id')
                ->where('match_rosters.match_id', $matchId)
                ->where('match_rosters.team_id', $teamId)
                ->where('match_roster_players.player_id', $playerId)
                ->exists();

            if (!$isInRoster) {
                $validator->errors()->add('player_id', 'Player is not in the team roster for this match.');
            }

            // Check if player belongs to the team
            $player = Player::find($playerId);
            if ($player && $player->club_id) {
                $team = Team::find($teamId);
                if ($team && $player->club_id !== $team->club_id) {
                    $validator->errors()->add('player_id', 'Player does not belong to the specified team.');
                }
            }
        }
    }

    protected function validateMatchTimeline($validator): void
    {
        $minute = $this->input('minute');
        $extraTimeMinute = $this->input('extra_time_minute');
        $period = $this->input('period');
        $match = $this->route('gameMatch');

        // Validate minute ranges based on period
        if ($period === 'first_half' && $minute > 45) {
            $validator->errors()->add('minute', 'First half cannot exceed 45 minutes.');
        }

        if ($period === 'second_half' && ($minute < 46 || $minute > 90)) {
            $validator->errors()->add('minute', 'Second half must be between 46 and 90 minutes.');
        }

        if ($period === 'extra_time_first' && ($minute < 91 || $minute > 105)) {
            $validator->errors()->add('minute', 'Extra time first half must be between 91 and 105 minutes.');
        }

        if ($period === 'extra_time_second' && ($minute < 106 || $minute > 120)) {
            $validator->errors()->add('minute', 'Extra time second half must be between 106 and 120 minutes.');
        }

        // Extra time minutes validation
        if ($extraTimeMinute && !in_array($period, ['first_half', 'second_half', 'extra_time_first', 'extra_time_second'])) {
            $validator->errors()->add('extra_time_minute', 'Extra time minutes are only valid for specific periods.');
        }

        if ($extraTimeMinute && $extraTimeMinute > 10) {
            $validator->errors()->add('extra_time_minute', 'Extra time cannot exceed 10 minutes.');
        }

        // Check for duplicate events at the same time
        $existingEvent = DB::table('match_events')
            ->where('match_id', $match->id)
            ->where('minute', $minute)
            ->where('extra_time_minute', $extraTimeMinute)
            ->where('period', $period)
            ->where('player_id', $this->input('player_id'))
            ->where('event_type', $this->input('event_type'))
            ->exists();

        if ($existingEvent) {
            $validator->errors()->add('minute', 'Duplicate event detected at the same time.');
        }
    }

    protected function validateTeamParticipation($validator): void
    {
        $teamId = $this->input('team_id');
        $match = $this->route('gameMatch');

        // Check if team is participating in this match
        if ($teamId !== $match->home_team_id && $teamId !== $match->away_team_id) {
            $validator->errors()->add('team_id', 'Team is not participating in this match.');
        }
    }

    protected function getValidEventTypes(): array
    {
        return [
            'goal', 'assist', 'yellow_card', 'red_card', 'substitution_in', 'substitution_out',
            'injury', 'save', 'missed_penalty', 'penalty_saved', 'own_goal', 'var_decision',
            'free_kick_goal', 'header_goal', 'volley_goal', 'long_range_goal', 'penalty_goal',
            'pass_completed', 'pass_attempted', 'tackle_won', 'tackle_attempted', 'interception',
            'foul_committed', 'foul_suffered', 'offside', 'corner', 'throw_in', 'goal_kick',
            'match_start', 'half_time', 'full_time', 'extra_time_start', 'extra_time_end',
            'penalty_shootout_start', 'penalty_shootout_end'
        ];
    }

    public function messages(): array
    {
        return [
            'event_type.required' => 'Event type is required.',
            'event_type.in' => 'Invalid event type specified.',
            'team_id.required' => 'Team is required.',
            'team_id.exists' => 'Selected team does not exist.',
            'minute.required' => 'Match minute is required.',
            'minute.integer' => 'Match minute must be a number.',
            'minute.min' => 'Match minute cannot be less than 1.',
            'minute.max' => 'Match minute cannot exceed 120.',
            'period.required' => 'Match period is required.',
            'period.in' => 'Invalid match period specified.',
            'player_id.exists' => 'Selected player does not exist.',
            'assisted_by_player_id.exists' => 'Selected assist player does not exist.',
            'substituted_player_id.exists' => 'Selected substituted player does not exist.',
        ];
    }
}
