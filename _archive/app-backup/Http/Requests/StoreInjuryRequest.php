<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreInjuryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Will be controlled by middleware
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'athlete_id' => 'required|exists:athletes,id',
            'date' => 'required|date|before_or_equal:today',
            'type' => 'required|string|max:100',
            'body_zone' => 'required|string|max:100',
            'severity' => 'required|in:minor,moderate,severe',
            'description' => 'required|string|max:1000',
            'status' => 'sometimes|in:open,resolved,recurring',
            'estimated_recovery_days' => 'nullable|integer|min:1|max:365',
            'expected_return_date' => 'nullable|date|after:today',
            'actual_return_date' => 'nullable|date|before_or_equal:today',
            'diagnosed_by' => 'nullable|exists:users,id',
            'treatment_plan' => 'nullable|array',
            'rehabilitation_progress' => 'nullable|array',
            'fifa_injury_data' => 'nullable|array',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'athlete_id.required' => 'The athlete is required.',
            'athlete_id.exists' => 'The selected athlete does not exist.',
            'date.required' => 'The injury date is required.',
            'date.before_or_equal' => 'The injury date cannot be in the future.',
            'type.required' => 'The injury type is required.',
            'body_zone.required' => 'The body zone is required.',
            'severity.required' => 'The injury severity is required.',
            'severity.in' => 'The severity must be minor, moderate, or severe.',
            'description.required' => 'The injury description is required.',
            'description.max' => 'The description must not exceed 1000 characters.',
            'status.in' => 'The status must be open, resolved, or recurring.',
            'estimated_recovery_days.integer' => 'The estimated recovery days must be a number.',
            'estimated_recovery_days.min' => 'The estimated recovery days must be at least 1.',
            'estimated_recovery_days.max' => 'The estimated recovery days cannot exceed 365.',
            'expected_return_date.after' => 'The expected return date must be in the future.',
            'actual_return_date.before_or_equal' => 'The actual return date cannot be in the future.',
            'diagnosed_by.exists' => 'The selected physician does not exist.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'athlete_id' => 'athlete',
            'date' => 'injury date',
            'type' => 'injury type',
            'body_zone' => 'body zone',
            'severity' => 'severity',
            'description' => 'description',
            'status' => 'status',
            'estimated_recovery_days' => 'estimated recovery days',
            'expected_return_date' => 'expected return date',
            'actual_return_date' => 'actual return date',
            'diagnosed_by' => 'physician',
            'treatment_plan' => 'treatment plan',
            'rehabilitation_progress' => 'rehabilitation progress',
            'fifa_injury_data' => 'FIFA injury data',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Set default status if not provided
        if (!$this->has('status')) {
            $this->merge(['status' => 'open']);
        }

        // Set diagnosed_by to current user if not provided
        if (!$this->has('diagnosed_by') && auth()->check()) {
            $this->merge(['diagnosed_by' => auth()->id()]);
        }
    }
} 