<?php

namespace App\Services;

use App\Models\Athlete;
use App\Models\Player;
use App\Models\User;

final class MedicalRecordAccess
{
    public function authorize(User $user, ?Player $player, ?Athlete $athlete): void
    {
        abort_unless($user->hasAnyRole(['system_admin', 'association_medical', 'club_medical', 'doctor', 'medical_staff']), 403);
        if ($user->isSystemAdmin()) return;
        $athlete?->loadMissing('team.club');
        $club = $player?->club ?? $athlete?->team?->club;
        abort_unless($club && (
            ($user->club_id && (int) $user->club_id === (int) $club->id)
            || ($user->association_id && (int) $user->association_id === (int) $club->association_id)
        ), 403);
    }
}
