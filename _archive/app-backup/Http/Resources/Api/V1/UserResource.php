<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'role' => $this->role,
            'status' => $this->status,
            'timezone' => $this->timezone,
            'language' => $this->language,
            'notifications_email' => $this->notifications_email,
            'notifications_sms' => $this->notifications_sms,
            'profile_picture_url' => $this->profile_picture_url,
            'profile_picture_alt' => $this->profile_picture_alt,
            'last_login_at' => $this->last_login_at?->toISOString(),
            'login_count' => $this->login_count,
            'permissions' => $this->permissions,
            'club' => $this->whenLoaded('club', function () {
                return [
                    'id' => $this->club->id,
                    'name' => $this->club->name,
                    'short_name' => $this->club->short_name,
                ];
            }),
            'association' => $this->whenLoaded('association', function () {
                return [
                    'id' => $this->association->id,
                    'name' => $this->association->name,
                    'short_name' => $this->association->short_name,
                ];
            }),
            'team' => $this->whenLoaded('team', function () {
                return [
                    'id' => $this->team->id,
                    'name' => $this->team->name,
                ];
            }),
            'role_info' => [
                'is_club_admin' => $this->isClubAdmin(),
                'is_club_manager' => $this->isClubManager(),
                'is_club_medical' => $this->isClubMedical(),
                'is_association_admin' => $this->isAssociationAdmin(),
                'is_association_registrar' => $this->isAssociationRegistrar(),
                'is_association_medical' => $this->isAssociationMedical(),
                'is_system_admin' => $this->isSystemAdmin(),
                'is_referee' => $this->isReferee(),
                'is_club_user' => $this->isClubUser(),
                'is_association_user' => $this->isAssociationUser(),
                'is_team_official' => $this->isTeamOfficial(),
                'is_admin' => $this->isAdmin(),
            ],
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
} 