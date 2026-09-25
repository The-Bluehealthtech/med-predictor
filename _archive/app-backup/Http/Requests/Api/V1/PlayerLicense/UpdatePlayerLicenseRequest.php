<?php

namespace App\Http\Requests\Api\V1\PlayerLicense;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePlayerLicenseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('player_license'));
    }

    public function rules(): array
    {
        return [
            'player_id' => 'sometimes|integer|exists:players,id',
            'club_id' => 'sometimes|integer|exists:clubs,id',
            'license_type' => 'sometimes|string|in:professional,amateur,youth,international',
            'status' => 'sometimes|string|in:pending,active,suspended,expired,revoked',
            'issue_date' => 'sometimes|date',
            'expiry_date' => 'sometimes|date|after:today',
            'issuing_authority' => 'sometimes|string|max:255',
            'license_category' => 'sometimes|string|in:A,B,C,D,E',
            'registration_number' => 'sometimes|string|max:255',
            'transfer_status' => 'sometimes|string|in:registered,pending_transfer,transferred',
            'contract_type' => 'sometimes|string|in:permanent,loan,free_agent',
            'contract_start_date' => 'sometimes|date',
            'contract_end_date' => 'sometimes|date|after:contract_start_date',
            'wage_agreement' => 'sometimes|numeric|min:0',
            'bonus_structure' => 'sometimes|string|max:500',
            'release_clause' => 'sometimes|numeric|min:0',
            'medical_clearance' => 'sometimes|boolean',
            'fitness_certificate' => 'sometimes|boolean',
            'disciplinary_record' => 'sometimes|string|max:1000',
            'international_clearance' => 'sometimes|boolean',
            'work_permit' => 'sometimes|boolean',
            'visa_status' => 'sometimes|string|max:255',
            'documentation_status' => 'sometimes|string|in:complete,incomplete,pending',
            'approval_status' => 'sometimes|string|in:pending,approved,rejected',
            'notes' => 'sometimes|string|max:1000',
            'requested_by' => 'sometimes|integer|exists:users,id',
            'document_path' => 'sometimes|string|max:500',
        ];
    }

    public function messages(): array
    {
        return [
            'player_id.exists' => 'Selected player does not exist.',
            'club_id.exists' => 'Selected club does not exist.',
            'license_type.in' => 'Invalid license type selected.',
            'expiry_date.after' => 'Expiry date must be in the future.',
            'license_category.in' => 'Invalid license category selected.',
            'contract_end_date.after' => 'Contract end date must be after start date.',
            'wage_agreement.numeric' => 'Wage agreement must be a number.',
            'wage_agreement.min' => 'Wage agreement cannot be negative.',
            'release_clause.numeric' => 'Release clause must be a number.',
            'release_clause.min' => 'Release clause cannot be negative.',
        ];
    }

    public function attributes(): array
    {
        return [
            'player_id' => 'player',
            'club_id' => 'club',
            'license_type' => 'license type',
            'status' => 'status',
            'issue_date' => 'issue date',
            'expiry_date' => 'expiry date',
            'issuing_authority' => 'issuing authority',
            'license_category' => 'license category',
            'registration_number' => 'registration number',
            'transfer_status' => 'transfer status',
            'contract_type' => 'contract type',
            'contract_start_date' => 'contract start date',
            'contract_end_date' => 'contract end date',
            'wage_agreement' => 'wage agreement',
            'bonus_structure' => 'bonus structure',
            'release_clause' => 'release clause',
            'medical_clearance' => 'medical clearance',
            'fitness_certificate' => 'fitness certificate',
            'disciplinary_record' => 'disciplinary record',
            'international_clearance' => 'international clearance',
            'work_permit' => 'work permit',
            'visa_status' => 'visa status',
            'documentation_status' => 'documentation status',
            'approval_status' => 'approval status',
            'notes' => 'notes',
            'requested_by' => 'requested by',
            'document_path' => 'document path',
        ];
    }
} 