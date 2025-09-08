<?php

namespace App\Http\Controllers;

use App\Models\Confederation;
use App\Models\Association;
use App\Models\Club;
use App\Models\Player;
use App\Services\FifaSyncService;
use Illuminate\Http\Request;

class FifaController extends Controller
{
    /**
     * Affiche le dashboard FIFA avec données dynamiques
     */
    public function dashboard(Request $request)
    {
        $fifaService = new FifaSyncService();
        
        // Récupérer les données de base
        $confederations = Confederation::with('associations')->orderBy('name')->get();
        $associations = Association::with(['confederation', 'clubs'])->orderBy('name')->get();
        $clubs = Club::with(['association'])->orderBy('name')->get();
        $players = Player::with(['club', 'association'])->orderBy('name')->get();

        // Statistiques FIFA
        $fifaStats = [
            'confederations' => [
                'total' => $confederations->count(),
                'synced' => $confederations->where('fifa_sync_status', 'synced')->count(),
                'pending' => $confederations->where('fifa_sync_status', 'pending')->count(),
                'failed' => $confederations->where('fifa_sync_status', 'failed')->count(),
            ],
            'associations' => [
                'total' => $associations->count(),
                'synced' => $associations->where('fifa_sync_status', 'synced')->count(),
                'pending' => $associations->where('fifa_sync_status', 'pending')->count(),
                'failed' => $associations->where('fifa_sync_status', 'failed')->count(),
            ],
            'clubs' => [
                'total' => $clubs->count(),
                'synced' => $clubs->where('fifa_sync_status', 'synced')->count(),
                'pending' => $clubs->where('fifa_sync_status', 'pending')->count(),
                'failed' => $clubs->where('fifa_sync_status', 'failed')->count(),
            ],
            'players' => [
                'total' => $players->count(),
                'synced' => $players->where('fifa_sync_status', 'synced')->count(),
                'pending' => $players->where('fifa_sync_status', 'pending')->count(),
                'failed' => $players->where('fifa_sync_status', 'failed')->count(),
            ]
        ];

        // Vérifier la connectivité FIFA
        $connectivity = $fifaService->checkConnectivity();

        // Filtrer par confédération si spécifié
        $filteredConfederation = null;
        if ($request->has('confederation_id') && $request->confederation_id) {
            $filteredConfederation = Confederation::with('associations')->find($request->confederation_id);
        }

        return view('modules.fifa.dashboard', compact(
            'confederations', 
            'associations', 
            'clubs', 
            'players', 
            'fifaStats', 
            'connectivity',
            'filteredConfederation'
        ));
    }

    /**
     * Synchronise toutes les entités avec FIFA
     */
    public function syncAll()
    {
        $fifaService = new FifaSyncService();
        
        try {
            $results = $fifaService->syncAllConfederations();
            
            return response()->json([
                'success' => true,
                'message' => 'Synchronisation globale terminée',
                'results' => $results
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la synchronisation : ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Affiche les statistiques FIFA détaillées
     */
    public function statistics()
    {
        $confederations = Confederation::with('associations')->get();
        $associations = Association::with(['confederation', 'clubs'])->get();
        
        $stats = [
            'confederations' => $confederations->map(function($conf) {
                return [
                    'id' => $conf->id,
                    'name' => $conf->name,
                    'short_name' => $conf->short_name,
                    'sync_status' => $conf->fifa_sync_status,
                    'last_sync' => $conf->fifa_sync_date,
                    'associations_count' => $conf->associations->count(),
                    'associations_synced' => $conf->associations->where('fifa_sync_status', 'synced')->count(),
                ];
            }),
            'associations' => $associations->map(function($assoc) {
                return [
                    'id' => $assoc->id,
                    'name' => $assoc->name,
                    'short_name' => $assoc->short_name,
                    'confederation' => $assoc->confederation ? $assoc->confederation->name : 'N/A',
                    'sync_status' => $assoc->fifa_sync_status,
                    'last_sync' => $assoc->fifa_sync_date,
                    'clubs_count' => $assoc->clubs->count(),
                ];
            })
        ];

        return view('modules.fifa.statistics', compact('stats'));
    }

    /**
     * Affiche le dashboard de synchronisation
     */
    public function syncDashboard()
    {
        $fifaService = new FifaSyncService();
        $connectivity = $fifaService->checkConnectivity();
        
        $confederations = Confederation::with('associations')->get();
        $associations = Association::with('confederation')->get();
        
        return view('modules.fifa.sync-dashboard', compact('connectivity', 'confederations', 'associations'));
    }
}
