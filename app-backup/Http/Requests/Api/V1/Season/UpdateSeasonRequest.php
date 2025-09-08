<?php

namespace App\Http\Requests\Api\V1\Season;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSeasonRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('season'));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $season = $this->route('season');

        return [
            'name' => [
                'sometimes',
                'string',
                'max:255',
                Rule::unique('seasons', 'name')->ignore($season->id)
            ],
            'short_name' => [
                'sometimes',
                'string',
                'max:50',
                Rule::unique('seasons', 'short_name')->ignore($season->id)
            ],
            'start_date' => 'sometimes|date',
            'end_date' => 'sometimes|date|after:start_date',
            'registration_start_date' => 'sometimes|date|before_or_equal:start_date',
            'registration_end_date' => 'sometimes|date|after:registration_start_date|before_or_equal:start_date',
            'status' => 'sometimes|string|in:upcoming,active,completed,archived',
            'is_current' => 'sometimes|boolean',
            'description' => 'sometimes|string|max:1000',
            'settings' => 'sometimes|array',
            'settings.allow_player_registration' => 'sometimes|boolean',
            'settings.allow_competition_creation' => 'sometimes|boolean',
            'settings.max_competitions_per_season' => 'sometimes|integer|min:1|max:100',
            'settings.registration_fee' => 'sometimes|numeric|min:0',
            'settings.transfer_window_open' => 'sometimes|boolean',
            'settings.transfer_window_start' => 'sometimes|date|after:start_date',
            'settings.transfer_window_end' => 'sometimes|date|before:end_date',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.unique' => 'A season with this name already exists.',
            'short_name.unique' => 'A season with this short name already exists.',
            'end_date.after' => 'End date must be after start date.',
            'registration_start_date.before_or_equal' => 'Registration start date must be before or equal to season start date.',
            'registration_end_date.after' => 'Registration end date must be after registration start date.',
            'registration_end_date.before_or_equal' => 'Registration end date must be before or equal to season start date.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'name' => 'season name',
            'short_name' => 'season short name',
            'start_date' => 'start date',
            'end_date' => 'end date',
            'registration_start_date' => 'registration start date',
            'registration_end_date' => 'registration end date',
            'status' => 'status',
            'is_current' => 'current season flag',
            'description' => 'description',
            'settings' => 'settings',
        ];
    }
} 