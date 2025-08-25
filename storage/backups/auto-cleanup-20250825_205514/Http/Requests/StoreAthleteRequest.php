<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAthleteRequest extends FormRequest
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
            'fifa_id' => [
                'required',
                'string',
                'max:50',
                Rule::unique('athletes', 'fifa_id')->ignore($this->athlete),
            ],
            'name' => 'required|string|max:255',
            'dob' => 'required|date|before:today',
            'nationality' => 'required|string|max:100',
            'team_id' => 'required|exists:teams,id',
            'position' => 'nullable|string|max:50',
            'jersey_number' => 'nullable|integer|min:1|max:99',
            'gender' => 'required|in:male,female,other',
            'blood_type' => 'nullable|string|in:A+,A-,B+,B-,AB+,AB-,O+,O-',
            'emergency_contact' => 'nullable|array',
            'emergency_contact.name' => 'required_with:emergency_contact|string|max:255',
            'emergency_contact.phone' => 'required_with:emergency_contact|string|max:20',
            'emergency_contact.relationship' => 'required_with:emergency_contact|string|max:100',
            'medical_history' => 'nullable|array',
            'allergies' => 'nullable|array',
            'medications' => 'nullable|array',
            'active' => 'boolean',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'fifa_id.required' => 'The FIFA ID is required.',
            'fifa_id.unique' => 'This FIFA ID is already registered.',
            'name.required' => 'The athlete name is required.',
            'dob.required' => 'The date of birth is required.',
            'dob.before' => 'The date of birth must be in the past.',
            'nationality.required' => 'The nationality is required.',
            'team_id.required' => 'The team is required.',
            'team_id.exists' => 'The selected team does not exist.',
            'gender.required' => 'The gender is required.',
            'gender.in' => 'The gender must be male, female, or other.',
            'blood_type.in' => 'The blood type must be a valid type.',
            'emergency_contact.name.required_with' => 'Emergency contact name is required when emergency contact is provided.',
            'emergency_contact.phone.required_with' => 'Emergency contact phone is required when emergency contact is provided.',
            'emergency_contact.relationship.required_with' => 'Emergency contact relationship is required when emergency contact is provided.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'fifa_id' => 'FIFA ID',
            'name' => 'athlete name',
            'dob' => 'date of birth',
            'nationality' => 'nationality',
            'team_id' => 'team',
            'position' => 'position',
            'jersey_number' => 'jersey number',
            'gender' => 'gender',
            'blood_type' => 'blood type',
            'emergency_contact' => 'emergency contact',
            'medical_history' => 'medical history',
            'allergies' => 'allergies',
            'medications' => 'medications',
        ];
    }
} 