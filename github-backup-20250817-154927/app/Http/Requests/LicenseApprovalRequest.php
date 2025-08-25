<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class LicenseApprovalRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Seuls les admins et license_agents peuvent approuver/rejeter
        return auth()->check() && in_array(auth()->user()->role, ['admin', 'license_agent']);
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'action' => [
                'required',
                'string',
                'in:approve,reject'
            ],
            'reason' => [
                'required_if:action,reject',
                'nullable',
                'string',
                'max:500'
            ]
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'action.required' => 'L\'action est requise.',
            'action.in' => 'L\'action doit être "approve" ou "reject".',
            'reason.required_if' => 'Une raison est requise lors du rejet d\'une licence.',
            'reason.max' => 'La raison ne doit pas dépasser 500 caractères.'
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'action' => 'action',
            'reason' => 'raison'
        ];
    }
} 