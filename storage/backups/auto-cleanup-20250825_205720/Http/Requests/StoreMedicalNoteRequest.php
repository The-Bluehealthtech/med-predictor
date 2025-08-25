<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMedicalNoteRequest extends FormRequest
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
            'note_json' => 'required|array',
            'note_type' => 'required|string|max:100',
            'generated_by_ai' => 'boolean',
            'approved_by_physician_id' => 'nullable|exists:users,id',
            'signed_at' => 'nullable|date',
            'status' => 'sometimes|in:draft,approved,signed',
            'ai_metadata' => 'nullable|array',
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
            'note_json.required' => 'The medical note content is required.',
            'note_json.array' => 'The note content must be in JSON format.',
            'note_type.required' => 'The note type is required.',
            'note_type.max' => 'The note type must not exceed 100 characters.',
            'generated_by_ai.boolean' => 'The AI generation flag must be true or false.',
            'approved_by_physician_id.exists' => 'The selected physician does not exist.',
            'signed_at.date' => 'The signed date must be a valid date.',
            'status.in' => 'The status must be draft, approved, or signed.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'athlete_id' => 'athlete',
            'note_json' => 'note content',
            'note_type' => 'note type',
            'generated_by_ai' => 'AI generation',
            'approved_by_physician_id' => 'physician',
            'signed_at' => 'signed date',
            'status' => 'status',
            'ai_metadata' => 'AI metadata',
            'fifa_compliance_data' => 'FIFA compliance data',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Set generated_by_ai to false if not provided
        if (!$this->has('generated_by_ai')) {
            $this->merge(['generated_by_ai' => false]);
        }

        // Set status to draft if not provided
        if (!$this->has('status')) {
            $this->merge(['status' => 'draft']);
        }

        // Set approved_by_physician_id to current user if not provided and status is signed
        if ($this->status === 'signed' && !$this->has('approved_by_physician_id') && auth()->check()) {
            $this->merge(['approved_by_physician_id' => auth()->id()]);
        }

        // Set signed_at to current time if status is signed and not provided
        if ($this->status === 'signed' && !$this->has('signed_at')) {
            $this->merge(['signed_at' => now()]);
        }
    }
} 