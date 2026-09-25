<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePCMARequest extends FormRequest
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
            'type' => 'required|in:bpma,cardio,dental',
            'result_json' => 'required|array',
            'status' => 'sometimes|in:pending,completed,failed,cancelled',
            'completed_at' => 'nullable|date',
            'assessor_id' => 'nullable|exists:users,id',
            'notes' => 'nullable|string|max:1000',
            'fifa_compliance_data' => 'nullable|array',
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
            'type.required' => 'The PCMA type is required.',
            'type.in' => 'The PCMA type must be bpma, cardio, or dental.',
            'result_json.required' => 'The assessment results are required.',
            'result_json.array' => 'The assessment results must be in JSON format.',
            'status.in' => 'The status must be pending, completed, failed, or cancelled.',
            'completed_at.date' => 'The completion date must be a valid date.',
            'assessor_id.exists' => 'The selected assessor does not exist.',
            'notes.max' => 'The notes must not exceed 1000 characters.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'athlete_id' => 'athlete',
            'type' => 'PCMA type',
            'result_json' => 'assessment results',
            'status' => 'status',
            'completed_at' => 'completion date',
            'assessor_id' => 'assessor',
            'notes' => 'notes',
            'fifa_compliance_data' => 'FIFA compliance data',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Set default status if not provided
        if (!$this->has('status')) {
            $this->merge(['status' => 'pending']);
        }

        // Set assessor_id to current user if not provided
        if (!$this->has('assessor_id') && auth()->check()) {
            $this->merge(['assessor_id' => auth()->id()]);
        }

        // Set completed_at to current time if status is completed
        if ($this->status === 'completed' && !$this->has('completed_at')) {
            $this->merge(['completed_at' => now()]);
        }
    }
} 