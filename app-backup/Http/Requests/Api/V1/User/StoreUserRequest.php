<?php

namespace App\Http\Requests\Api\V1\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('create', \App\Models\User::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|string|in:club_admin,club_manager,club_medical,association_admin,association_registrar,association_medical,system_admin,referee',
            'club_id' => [
                'nullable',
                'integer',
                'exists:clubs,id',
                Rule::requiredIf(function () {
                    return in_array($this->role, ['club_admin', 'club_manager', 'club_medical']);
                })
            ],
            'association_id' => [
                'nullable',
                'integer',
                'exists:associations,id',
                Rule::requiredIf(function () {
                    return in_array($this->role, ['association_admin', 'association_registrar', 'association_medical']);
                })
            ],
            'team_id' => 'nullable|integer|exists:teams,id',
            'status' => 'sometimes|string|in:active,inactive,suspended',
            'timezone' => 'sometimes|string|max:50',
            'language' => 'sometimes|string|max:10',
            'notifications_email' => 'sometimes|boolean',
            'notifications_sms' => 'sometimes|boolean',
            'profile_picture_url' => 'sometimes|string|url|max:500',
            'profile_picture_alt' => 'sometimes|string|max:255',
            'permissions' => 'sometimes|array',
            'permissions.*' => 'string|in:player_registration_access,competition_management_access,healthcare_access,user_management_access,audit_trail_access',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'User name is required.',
            'email.required' => 'Email address is required.',
            'email.email' => 'Please provide a valid email address.',
            'email.unique' => 'This email address is already registered.',
            'password.required' => 'Password is required.',
            'password.min' => 'Password must be at least 8 characters long.',
            'password.confirmed' => 'Password confirmation does not match.',
            'role.required' => 'User role is required.',
            'role.in' => 'Invalid user role selected.',
            'club_id.required' => 'Club is required for club users.',
            'club_id.exists' => 'Selected club does not exist.',
            'association_id.required' => 'Association is required for association users.',
            'association_id.exists' => 'Selected association does not exist.',
            'team_id.exists' => 'Selected team does not exist.',
            'status.in' => 'Invalid status selected.',
            'permissions.*.in' => 'Invalid permission selected.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'name' => 'user name',
            'email' => 'email address',
            'password' => 'password',
            'role' => 'user role',
            'club_id' => 'club',
            'association_id' => 'association',
            'team_id' => 'team',
            'status' => 'status',
            'timezone' => 'timezone',
            'language' => 'language',
            'notifications_email' => 'email notifications',
            'notifications_sms' => 'SMS notifications',
            'profile_picture_url' => 'profile picture URL',
            'profile_picture_alt' => 'profile picture alt text',
            'permissions' => 'permissions',
        ];
    }
} 