<?php

namespace App\Http\Requests\Api\V1\Player;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePlayerRequest extends FormRequest
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
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:players,email'],
            'phone' => ['nullable', 'string', 'max:20'],
            'date_of_birth' => ['required', 'date', 'before:today'],
            'nationality' => ['required', 'string', 'max:100'],
            'position' => ['required', 'string', 'max:50'],
            'club_id' => ['required', 'exists:clubs,id'],
            'team_id' => ['nullable', 'exists:teams,id'],
            'status' => ['required', Rule::in(['active', 'inactive', 'suspended'])],
            'fifa_connect_id' => ['nullable', 'string', 'max:255', 'unique:players,fifa_connect_id'],
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