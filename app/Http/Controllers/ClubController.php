<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\Club;
use App\Models\Association;
use App\Traits\Searchable;
use App\Services\ExternalClubImport\ExternalClubImporter;

class ClubController extends Controller
{
    use Searchable;

    /**
     * Afficher la liste des clubs
     */
    public function index(Request $request)
    {
        $clubs = Club::with(['association'])->orderBy('name')->get();
        $filtered = false;
        $association = null;
        
        return view('modules.clubs.index', compact('clubs', 'filtered', 'association'));
    }

    /**
     * Liste JSON des clubs accessibles pour les composants authentifiés.
     */
    public function apiIndex(Request $request): JsonResponse
    {
        $user = $request->user();
        abort_unless($user, 401);

        $query = Club::query()->orderBy('name');

        if ($user->isSystemAdmin()) {
            // Tous les clubs.
        } elseif ($user->isClubUser() && $user->club_id) {
            $query->whereKey($user->club_id);
        } elseif ($user->isAssociationUser() && $user->association_id) {
            $query->where('association_id', $user->association_id);
        } else {
            $query->whereRaw('1 = 0');
        }

        return response()->json([
            'success' => true,
            'data' => $query->get(['id', 'name', 'association_id', 'federation_id']),
        ]);
    }

    /**
     * Afficher un club spécifique
     */
    public function show(Club $club)
    {
        $club->load(['association', 'players']);

        $sourceUrl = \Illuminate\Support\Facades\Schema::hasTable('external_entity_links')
            ? \Illuminate\Support\Facades\DB::table('external_entity_links')
                ->where('source', 'footmercato')
                ->where('entity_type', 'club')
                ->where('local_id', $club->id)
                ->value('source_url')
            : null;

        return view('modules.clubs.show', compact('club', 'sourceUrl'));
    }

    public function previewExternalImport(
        Request $request,
        Club $club,
        ExternalClubImporter $importer
    ) {
        $validated = $request->validate([
            'source_url' => ['required', 'url', 'max:2048', function ($attribute, $value, $fail) {
                $host = strtolower((string) parse_url($value, PHP_URL_HOST));
                $path = (string) parse_url($value, PHP_URL_PATH);

                if (! in_array($host, ['footmercato.net', 'www.footmercato.net'], true)
                    || ! str_contains($path, '/club/')
                    || ! str_contains($path, '/effectif')) {
                    $fail('Fournir une URL Foot Mercato de page effectif.');
                }
            }],
        ]);

        try {
            $preview = $importer->preview($validated['source_url'], $club);
            $club->load(['association', 'players']);

            return view('modules.clubs.show', [
                'club' => $club,
                'sourceUrl' => $validated['source_url'],
                'externalPreview' => $preview,
            ]);
        } catch (\Throwable $exception) {
            report($exception);

            return back()
                ->withInput()
                ->with('error', 'Impossible de récupérer les données Foot Mercato : '.$exception->getMessage());
        }
    }

    public function syncExternalImport(
        Request $request,
        Club $club,
        ExternalClubImporter $importer
    ) {
        $validated = $request->validate([
            'source_url' => ['required', 'url', 'max:2048', function ($attribute, $value, $fail) {
                $host = strtolower((string) parse_url($value, PHP_URL_HOST));
                $path = (string) parse_url($value, PHP_URL_PATH);

                if (! in_array($host, ['footmercato.net', 'www.footmercato.net'], true)
                    || ! str_contains($path, '/club/')
                    || ! str_contains($path, '/effectif')) {
                    $fail('Fournir une URL Foot Mercato de page effectif.');
                }
            }],
            'download_media' => ['nullable', 'boolean'],
        ]);

        try {
            $result = $importer->import(
                $validated['source_url'],
                $request->boolean('download_media', true),
                $club
            );

            return redirect()
                ->route('modules.clubs.show', $club)
                ->with(
                    'success',
                    sprintf(
                        'Synchronisation Foot Mercato terminée : %d joueur(s) créé(s), %d mis à jour.',
                        $result['players_created'],
                        $result['players_updated']
                    )
                );
        } catch (\Throwable $exception) {
            report($exception);

            return back()->with('error', 'Échec de synchronisation Foot Mercato : '.$exception->getMessage());
        }
    }
}
