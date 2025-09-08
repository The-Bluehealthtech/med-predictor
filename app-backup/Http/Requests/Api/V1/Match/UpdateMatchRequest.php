<?php

namespace App\Http\Requests\Api\V1\Match;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateMatchRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Authorization handled by policy
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'competition_id' => ['sometimes', 'exists:competitions,id'],
            'home_team_id' => [
                'sometimes', 
                'exists:teams,id',
                function ($attribute, $value, $fail) {
                    if ($this->input('competition_id')) {
                        $competition = \App\Models\Competition::find($this->input('competition_id'));
                        if ($competition) {
                            $teamBelongsToCompetition = $competition->teams()->where('teams.id', $value)->exists();
                            if (!$teamBelongsToCompetition) {
                                $fail('The selected home team does not belong to the specified competition.');
                            }
                        }
                    }
                }
            ],
            'away_team_id' => [
                'sometimes', 
                'exists:teams,id', 
                'different:home_team_id',
                function ($attribute, $value, $fail) {
                    if ($this->input('competition_id')) {
                        $competition = \App\Models\Competition::find($this->input('competition_id'));
                        if ($competition) {
                            $teamBelongsToCompetition = $competition->teams()->where('teams.id', $value)->exists();
                            if (!$teamBelongsToCompetition) {
                                $fail('The selected away team does not belong to the specified competition.');
                            }
                        }
                    }
                }
            ],
            'match_date' => ['sometimes', 'date'],
            'kickoff_time' => ['sometimes', 'date_format:H:i'],
            'venue' => ['nullable', 'string', 'max:255'],
            'stadium' => ['nullable', 'string', 'max:255'],
            'capacity' => ['nullable', 'integer', 'min:0'],
            'referee_id' => ['nullable', 'exists:users,id'],
            'match_status' => ['sometimes', Rule::in(['scheduled', 'in_progress', 'completed', 'cancelled', 'postponed'])],
            'matchday' => ['nullable', 'string', 'max:50'],
            'home_score' => ['nullable', 'integer', 'min:0'],
            'away_score' => ['nullable', 'integer', 'min:0'],
            'attendance' => ['nullable', 'integer', 'min:0'],
            'weather_conditions' => ['nullable', 'string', 'max:100'],
            'pitch_condition' => ['nullable', 'string', 'max:100'],
            'match_highlights' => ['nullable', 'string'],
            'match_report' => ['nullable', 'string'],
        ];
    }

    /**
     * Get custom error messages for validation rules.
     */
    public function messages(): array
    {
        return [
            'competition_id.exists' => 'Selected competition does not exist.',
            'home_team_id.exists' => 'Selected home team does not exist.',
            'away_team_id.exists' => 'Selected away team does not exist.',
            'away_team_id.different' => 'Home team and away team must be different.',
            'kickoff_time.date_format' => 'Kickoff time must be in HH:MM format.',
            'match_status.in' => 'Invalid match status value.',
        ];
    }
} 