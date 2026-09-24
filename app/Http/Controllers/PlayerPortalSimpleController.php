<?php

namespace App\Http\Controllers;

use App\Models\Association;
use App\Models\Club;
use App\Models\Confederation;
use App\Models\Player;
use App\Services\PlayerPortalDataService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PlayerPortalSimpleController extends Controller
{
    public function __construct(
        private readonly PlayerPortalDataService $portalDataService
    ) {
    }

    public function show(Request $request): View
    {
        $user = $request->user();

        abort_unless($user, 401);

        $requestedPlayerId = null;

        if ($request->has('player_id')) {
            $validatedPlayerId = filter_var(
                $request->query('player_id'),
                FILTER_VALIDATE_INT,
                ['options' => ['min_range' => 1]]
            );

            abort_if(
                $validatedPlayerId === false,
                404,
                'Joueur introuvable.'
            );

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

            abort_if(
                $requestedPlayerId !== null
                    && $requestedPlayerId !== (int) $user->player_id,
                403,
                'Accès non autorisé à ce joueur.'
            );

            /*
             * L'identité est établie par user.player_id.
             * Aucun player_id fourni par le navigateur n'est utilisé ici.
             */
            $player = Player::withoutGlobalScopes()
                ->with(['club', 'association'])
                ->findOrFail((int) $user->player_id);
        } else {
            abort_unless(
                $requestedPlayerId !== null,
                404,
                'Aucun joueur sélectionné.'
            );

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
                ->findOrFail($requestedPlayerId);

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

        $associations = Association::with(['confederation'])
            ->orderBy('name')
            ->get();

        $clubs = Club::with(['association'])
            ->orderBy('name')
            ->get();

        /*
         * Confederation ne possède actuellement pas le tenant scope.
         * Son comportement de référentiel global reste inchangé.
         */
        $confederations = Confederation::orderBy('name')
            ->get();

        $portalData = $this->portalDataService->forPlayer($player);

        return view(
            'test-portail-joueur-simple',
            array_merge(
                compact(
                    'player',
                    'associations',
                    'clubs',
                    'confederations'
                ),
                $portalData
            )
        );
    }
}
