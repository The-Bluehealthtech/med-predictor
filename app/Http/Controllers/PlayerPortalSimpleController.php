<?php

namespace App\Http\Controllers;

use App\Models\Player;
use App\Services\PlayerPortalDataService;
use App\Services\KsaPlayerCockpitData;
use App\Services\GoalkeeperCockpitData;
use App\Services\RoleEvaluationCockpitData;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PlayerPortalSimpleController extends Controller
{
    public function __construct(
        private readonly PlayerPortalDataService $portalDataService,
        private readonly KsaPlayerCockpitData $cockpitDataService,
        private readonly GoalkeeperCockpitData $goalkeeperCockpitData,
        private readonly RoleEvaluationCockpitData $roleEvaluationCockpitData
    ) {
    }

    public function show(Request $request)
    {
        $user = $request->user();

        abort_unless($user, 401);

        $requestedPlayerId = null;

        if ($request->filled('player_id')) {
            $validatedPlayerId = filter_var(
                $request->query('player_id'),
                FILTER_VALIDATE_INT,
                ['options' => ['min_range' => 1]]
            );

            if ($validatedPlayerId === false) {
                return $this->playerNotFound($request);
            }

            $requestedPlayerId = (int) $validatedPlayerId;
        }

        /*
         * Un joueur authentifié consulte uniquement son propre dossier.
         */
        if ($user->isPlayer()) {
            abort_unless(
                $user->player_id,
                403,
                'Aucun joueur associé à ce compte.'
            );

            if ($requestedPlayerId === null) {
                return $this->playerNotFound($request);
            }

            abort_if(
                $requestedPlayerId !== (int) $user->player_id,
                403,
                'Accès non autorisé à ce joueur.'
            );

            /*
             * L'identité est établie par user.player_id.
             * Aucun player_id fourni par le navigateur n'est utilisé ici.
             */
            $player = Player::withoutGlobalScopes()
                ->with(['club', 'association'])
                ->find((int) $user->player_id);
        } else {
            if ($requestedPlayerId === null) {
                return $this->playerNotFound($request);
            }

            abort_unless(
                $user->isSystemAdmin()
                    || $user->isClubUser()
                    || $user->role === 'association_admin',
                403,
                'Accès non autorisé au portail joueur.'
            );

            /*
             * Le scope tenant reste actif.
             * EnhancedTenantScope ne le désactive automatiquement
             * que pour system_admin.
             */
            $player = Player::with(['club', 'association'])
                ->find($requestedPlayerId);

            if (!$player) {
                return $this->playerNotFound($request);
            }

            if ($user->isClubUser()) {
                abort_unless(
                    $user->club_id
                        && $player->club_id
                        && (int) $user->club_id === (int) $player->club_id,
                    403,
                    'Accès non autorisé à ce joueur.'
                );
            }

            if ($user->role === 'association_admin') {
                abort_unless(
                    $user->association_id
                        && $player->association_id
                        && (int) $user->association_id === (int) $player->association_id,
                    403,
                    'Accès non autorisé à ce joueur.'
                );
            }
        }

        if (!$player) {
            return $this->playerNotFound($request);
        }

        try {
            $portalData = $this->portalDataService->forPlayer($player);
            $cockpitData = $this->cockpitDataService->fromMetrics($portalData['ksaMetrics']);
            $goalkeeperData = $player->position === Player::POSITION_GOALKEEPER
                ? $this->goalkeeperCockpitData->forPlayer($player, $portalData['playerStats']->first())
                : null;
            $roleEvaluationCockpit = $this->roleEvaluationCockpitData->forPlayer($player);
        } catch (\Throwable $exception) {
            report($exception);
            return response()->view('player-data-unavailable', [], 503);
        }

        return view('test-portail-joueur-simple', array_merge(
            [
                'player' => $player,
                'cockpitV2Data' => $cockpitData,
                'goalkeeperCockpitData' => $goalkeeperData,
                'roleEvaluationCockpit' => $roleEvaluationCockpit,
            ],
            $portalData
        ));
    }
    private function playerNotFound(Request $request)
    {
        if ($request->expectsJson()) {
            return response()->json(['message' => 'Joueur introuvable'], 404);
        }

        return response()->view('player-not-found', [], 404);
    }
}
