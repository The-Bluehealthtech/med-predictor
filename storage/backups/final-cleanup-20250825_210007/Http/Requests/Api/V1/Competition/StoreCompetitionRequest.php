<?php

namespace App\Http\Requests\Api\V1\Competition;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCompetitionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Authorization handled in controller
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255|unique:competitions,name',
            'description' => 'nullable|string|max:1000',
            'type' => 'required|string|in:league,cup,tournament,friendly',
            'format' => 'required|string|in:round_robin,knockout,group_stage,mixed',
            'status' => 'required|string|in:active,inactive,draft,archived',
            'association_id' => 'required|exists:associations,id',
            'start_date' => 'required|date|after:today',
            'end_date' => 'required|date|after:start_date',
            'registration_deadline' => 'nullable|date|before:start_date',
            'max_teams' => 'nullable|integer|min:2|max:100',
            'min_teams' => [
                'nullable',
                'integer',
                'min:2',
                function ($attribute, $value, $fail) {
                    $max = $this->input('max_teams');
                    if ($max !== null && $value > $max) {
                        $fail('Minimum teams cannot exceed maximum teams.');
                    }
                }
            ],
            'age_group' => 'nullable|string|in:u8,u10,u12,u14,u16,u18,u21,senior,mixed',
            'gender' => 'nullable|string|in:male,female,mixed',
            'venue_type' => 'nullable|string|in:indoor,outdoor,both',
            'rules' => 'nullable|string|max:2000',
            'prize_pool' => 'nullable|numeric|min:0',
            'entry_fee' => 'nullable|numeric|min:0',
            'contact_email' => 'nullable|email|max:255',
            'contact_phone' => 'nullable|string|max:20',
            'website' => 'nullable|url|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'banner' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
            'is_featured' => 'boolean',
            'is_public' => 'boolean',
            'requires_approval' => 'boolean',
            'auto_approve_teams' => 'boolean',
            'allow_substitutions' => 'boolean',
            'max_substitutions' => 'nullable|integer|min:0|max:10',
            'match_duration' => 'nullable|integer|min:30|max:120',
            'extra_time' => 'boolean',
            'penalties' => 'boolean',
            'var_enabled' => 'boolean',
            'streaming_enabled' => 'boolean',
            'ticket_sales_enabled' => 'boolean',
            'sponsorship_enabled' => 'boolean',
            'metadata' => 'nullable|array'
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Competition name is required.',
            'name.unique' => 'A competition with this name already exists.',
            'type.required' => 'Competition type is required.',
            'type.in' => 'Competition type must be league, cup, tournament, or friendly.',
            'format.required' => 'Competition format is required.',
            'format.in' => 'Competition format must be round_robin, knockout, group_stage, or mixed.',
            'association_id.required' => 'Association is required.',
            'association_id.exists' => 'Selected association does not exist.',
            'start_date.required' => 'Start date is required.',
            'start_date.after' => 'Start date must be in the future.',
            'end_date.required' => 'End date is required.',
            'end_date.after' => 'End date must be after start date.',
            'registration_deadline.before' => 'Registration deadline must be before start date.',
            'max_teams.min' => 'Maximum teams must be at least 2.',
            'min_teams.max' => 'Minimum teams cannot exceed maximum teams.',
            'age_group.in' => 'Invalid age group selected.',
            'gender.in' => 'Gender must be male, female, or mixed.',
            'venue_type.in' => 'Venue type must be indoor, outdoor, or both.',
            'prize_pool.min' => 'Prize pool cannot be negative.',
            'entry_fee.min' => 'Entry fee cannot be negative.',
            'contact_email.email' => 'Contact email must be a valid email address.',
            'website.url' => 'Website must be a valid URL.',
            'logo.image' => 'Logo must be an image file.',
            'logo.max' => 'Logo size cannot exceed 2MB.',
            'banner.image' => 'Banner must be an image file.',
            'banner.max' => 'Banner size cannot exceed 5MB.',
            'max_substitutions.min' => 'Maximum substitutions cannot be negative.',
            'max_substitutions.max' => 'Maximum substitutions cannot exceed 10.',
            'match_duration.min' => 'Match duration must be at least 30 minutes.',
            'match_duration.max' => 'Match duration cannot exceed 120 minutes.'
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'name' => 'competition name',
            'description' => 'competition description',
            'type' => 'competition type',
            'format' => 'competition format',
            'status' => 'competition status',
            'association_id' => 'association',
            'start_date' => 'start date',
            'end_date' => 'end date',
            'registration_deadline' => 'registration deadline',
            'max_teams' => 'maximum teams',
            'min_teams' => 'minimum teams',
            'age_group' => 'age group',
            'gender' => 'gender',
            'venue_type' => 'venue type',
            'rules' => 'competition rules',
            'prize_pool' => 'prize pool',
            'entry_fee' => 'entry fee',
            'contact_email' => 'contact email',
            'contact_phone' => 'contact phone',
            'website' => 'website',
            'logo' => 'logo',
            'banner' => 'banner'
        ];
    }
}
