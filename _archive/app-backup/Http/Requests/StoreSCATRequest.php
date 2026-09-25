<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSCATRequest extends FormRequest
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
            'data_json' => 'required|array',
            'result' => 'required|in:normal,abnormal,unclear',
            'concussion_confirmed' => 'required|boolean',
            'assessment_date' => 'required|date|before_or_equal:today',
            'assessment_type' => 'required|in:baseline,post_injury,follow_up',
            'scat_score' => 'nullable|integer|min:0|max:132',
            'recommendations' => 'nullable|string|max:1000',
            'fifa_concussion_data' => 'nullable|array',
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
            'data_json.required' => 'The SCAT assessment data is required.',
            'data_json.array' => 'The assessment data must be in JSON format.',
            'result.required' => 'The assessment result is required.',
            'result.in' => 'The result must be normal, abnormal, or unclear.',
            'concussion_confirmed.required' => 'The concussion confirmation status is required.',
            'concussion_confirmed.boolean' => 'The concussion confirmation must be true or false.',
            'assessment_date.required' => 'The assessment date is required.',
            'assessment_date.before_or_equal' => 'The assessment date cannot be in the future.',
            'assessment_type.required' => 'The assessment type is required.',
            'assessment_type.in' => 'The assessment type must be baseline, post_injury, or follow_up.',
            'scat_score.integer' => 'The SCAT score must be a number.',
            'scat_score.min' => 'The SCAT score cannot be negative.',
            'scat_score.max' => 'The SCAT score cannot exceed 132.',
            'recommendations.max' => 'The recommendations must not exceed 1000 characters.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'athlete_id' => 'athlete',
            'data_json' => 'assessment data',
            'result' => 'result',
            'concussion_confirmed' => 'concussion confirmation',
            'assessment_date' => 'assessment date',
            'assessment_type' => 'assessment type',
            'scat_score' => 'SCAT score',
            'recommendations' => 'recommendations',
            'fifa_concussion_data' => 'FIFA concussion data',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Set assessor_id to current user if not provided
        if (!$this->has('assessor_id') && auth()->check()) {
            $this->merge(['assessor_id' => auth()->id()]);
        }

        // Set assessment_date to current date if not provided
        if (!$this->has('assessment_date')) {
            $this->merge(['assessment_date' => now()]);
        }
    }
} 