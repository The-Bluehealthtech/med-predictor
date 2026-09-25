<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PlayerLicenseResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'player_id' => $this->player_id,
            'club_id' => $this->club_id,
            'fifa_connect_id' => $this->fifa_connect_id,
            'license_number' => $this->license_number,
            'license_type' => $this->license_type,
            'status' => $this->status,
            'issue_date' => $this->issue_date?->toDateString(),
            'expiry_date' => $this->expiry_date?->toDateString(),
            'renewal_date' => $this->renewal_date?->toDateString(),
            'issuing_authority' => $this->issuing_authority,
            'license_category' => $this->license_category,
            'registration_number' => $this->registration_number,
            'transfer_status' => $this->transfer_status,
            'contract_type' => $this->contract_type,
            'contract_start_date' => $this->contract_start_date?->toDateString(),
            'contract_end_date' => $this->contract_end_date?->toDateString(),
            'wage_agreement' => $this->wage_agreement,
            'bonus_structure' => $this->bonus_structure,
            'release_clause' => $this->release_clause,
            'medical_clearance' => $this->medical_clearance,
            'fitness_certificate' => $this->fitness_certificate,
            'disciplinary_record' => $this->disciplinary_record,
            'international_clearance' => $this->international_clearance,
            'work_permit' => $this->work_permit,
            'visa_status' => $this->visa_status,
            'documentation_status' => $this->documentation_status,
            'approval_status' => $this->approval_status,
            'approved_by' => $this->approved_by,
            'approved_at' => $this->approved_at?->toISOString(),
            'rejection_reason' => $this->rejection_reason,
            'notes' => $this->notes,
            'requested_by' => $this->requested_by,
            'document_path' => $this->document_path,
            'player' => $this->whenLoaded('player', function () {
                return [
                    'id' => $this->player->id,
                    'name' => $this->player->name,
                    'first_name' => $this->player->first_name,
                    'last_name' => $this->player->last_name,
                    'position' => $this->player->position,
                    'nationality' => $this->player->nationality,
                ];
            }),
            'club' => $this->whenLoaded('club', function () {
                return [
                    'id' => $this->club->id,
                    'name' => $this->club->name,
                    'short_name' => $this->club->short_name,
                ];
            }),
            'approved_by_user' => $this->whenLoaded('approvedByUser', function () {
                return [
                    'id' => $this->approvedByUser->id,
                    'name' => $this->approvedByUser->name,
                ];
            }),
            'requested_by_user' => $this->whenLoaded('requestedByUser', function () {
                return [
                    'id' => $this->requestedByUser->id,
                    'name' => $this->requestedByUser->name,
                ];
            }),
            'license_info' => [
                'is_active' => $this->isActive(),
                'is_expired' => $this->isExpired(),
                'is_pending' => $this->isPending(),
                'is_suspended' => $this->isSuspended(),
                'days_until_expiry' => $this->daysUntilExpiry(),
                'requires_renewal' => $this->requiresRenewal(),
                'can_play' => $this->isActive() && !$this->isExpired(),
            ],
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
} 