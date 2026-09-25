<?php

namespace App\Http\Requests\Api\V1\Player;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePlayerRequest extends FormRequest
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
        $playerId = $this->route('player')->id ?? null;

        return [
            'first_name' => ['sometimes', 'required', 'string', 'max:255'],
            'last_name' => ['sometimes', 'required', 'string', 'max:255'],
            'email' => [
                'sometimes', 
                'required', 
                'email', 
                Rule::unique('players', 'email')->ignore($playerId)
            ],
            'phone' => ['nullable', 'string', 'max:20'],
            'date_of_birth' => ['sometimes', 'required', 'date', 'before:today'],
            'nationality' => ['sometimes', 'required', 'string', 'max:100'],
            'position' => ['sometimes', 'required', 'string', 'max:50'],
            'club_id' => ['sometimes', 'required', 'exists:clubs,id'],
            'team_id' => ['nullable', 'exists:teams,id'],
            'status' => ['sometimes', 'required', Rule::in(['active', 'inactive', 'suspended'])],
            'fifa_connect_id' => [
                'nullable', 
                'string', 
                'max:255', 
                Rule::unique('players', 'fifa_connect_id')->ignore($playerId)
            ],
            'emergency_contact_name' => ['nullable', 'string', 'max:255'],
            'emergency_contact_phone' => ['nullable', 'string', 'max:20'],
            'medical_conditions' => ['nullable', 'string'],
            'allergies' => ['nullable', 'string'],
            'blood_type' => ['nullable', Rule::in(['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'])],
        ];
    }

    /**
     * Get custom error messages for validation rules.
     */
    public function messages(): array
    {
        return [
            'first_name.required' => 'First name is required.',
            'last_name.required' => 'Last name is required.',
            'email.required' => 'Email is required.',
            'email.email' => 'Please provide a valid email address.',
            'email.unique' => 'This email is already registered.',
            'date_of_birth.required' => 'Date of birth is required.',
            'date_of_birth.before' => 'Date of birth must be in the past.',
            'nationality.required' => 'Nationality is required.',
            'position.required' => 'Position is required.',
            'club_id.required' => 'Club is required.',
            'club_id.exists' => 'Selected club does not exist.',
            'team_id.exists' => 'Selected team does not exist.',
            'status.required' => 'Status is required.',
            'status.in' => 'Invalid status value.',
            'fifa_connect_id.unique' => 'This FIFA Connect ID is already registered.',
            'blood_type.in' => 'Invalid blood type.',
        ];
    }
} 