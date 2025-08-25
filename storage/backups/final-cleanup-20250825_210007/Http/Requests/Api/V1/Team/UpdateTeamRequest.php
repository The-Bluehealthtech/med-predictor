<?php

namespace App\Http\Requests\Api\V1\Team;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTeamRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->hasAnyRole(['admin', 'association_manager', 'club_manager']);
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'name' => 'sometimes|required|string|max:255',
            'club_id' => 'sometimes|required|exists:clubs,id',
            'association_id' => 'sometimes|required|exists:associations,id',
            'status' => 'sometimes|required|in:active,inactive,suspended',
            'founded_year' => 'nullable|integer|min:1800|max:' . (date('Y') + 1),
            'home_ground' => 'nullable|string|max:255',
            'capacity' => 'nullable|integer|min:0',
            'colors' => 'nullable|string|max:100',
            'logo_url' => 'nullable|url|max:500',
            'website' => 'nullable|url|max:500',
            'description' => 'nullable|string|max:1000',
            'competition_ids' => 'nullable|array',
            'competition_ids.*' => 'exists:competitions,id',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Team name is required',
            'name.max' => 'Team name cannot exceed 255 characters',
            'club_id.required' => 'Club is required',
            'club_id.exists' => 'Selected club does not exist',
            'association_id.required' => 'Association is required',
            'association_id.exists' => 'Selected association does not exist',
            'status.required' => 'Team status is required',
            'status.in' => 'Team status must be active, inactive, or suspended',
            'founded_year.integer' => 'Founded year must be a valid year',
            'founded_year.min' => 'Founded year cannot be before 1800',
            'founded_year.max' => 'Founded year cannot be in the future',
            'capacity.integer' => 'Capacity must be a number',
            'capacity.min' => 'Capacity cannot be negative',
            'logo_url.url' => 'Logo URL must be a valid URL',
            'website.url' => 'Website must be a valid URL',
            'competition_ids.array' => 'Competitions must be an array',
            'competition_ids.*.exists' => 'One or more selected competitions do not exist',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'name' => 'team name',
            'club_id' => 'club',
            'association_id' => 'association',
            'founded_year' => 'founded year',
            'home_ground' => 'home ground',
            'logo_url' => 'logo URL',
            'competition_ids' => 'competitions',
        ];
    }
} 