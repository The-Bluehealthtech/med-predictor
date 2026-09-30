<?php

namespace App\Http\Controllers;

use App\Models\Competition;
use App\Models\Player;
use Illuminate\Http\Request;

final class ClubEligibilityController extends Controller
{
    public function getEligiblePlayers(Request $request, Competition $competition)
    {
        $user = $request->user();
        abort_unless($user && $user->isClubUser() && $user->club_id, 403);
        abort_unless($user->club && (int) $user->club->association_id === (int) $competition->association_id, 403);
        $players = Player::where('club_id', $user->club_id)->with('playerLicenses')->get();
        $eligible = $ineligible = [];
        foreach ($players as $player) {
            $license = $player->playerLicenses->first(fn ($license) => $license->is_active);
            $reasons = [];
            if ($competition->require_federation_license && !$license) {
                $reasons[] = 'Licence active absente ou expirée';
            }
            // Seule la condition explicitement portée par la compétition est évaluée.
            $row = $player->only(['id', 'name', 'position', 'nationality']);
            $row['license_status'] = $license?->status;
            $row['reasons'] = $reasons;
            if ($reasons) $ineligible[] = $row;
            else $eligible[] = $row;
        }
        return response()->json(['success' => true, 'data' => [
            'competition' => $competition->only(['id', 'name', 'season', 'require_federation_license']),
            'eligible_players' => $eligible, 'ineligible_players' => $ineligible,
            'total_eligible' => count($eligible), 'total_ineligible' => count($ineligible),
            'eligibility_scope' => 'configured_federation_license_requirement',
        ]]);
    }
}
