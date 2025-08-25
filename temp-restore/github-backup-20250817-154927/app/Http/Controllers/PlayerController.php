<?php

namespace App\Http\Controllers;

use App\Models\Player;
use App\Models\Club;
use App\Models\Association;
use App\Models\HealthRecord;
use App\Models\MedicalPrediction;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class PlayerController extends Controller
{
    public function index(): View|RedirectResponse
    {
        $user = auth()->user();
        
        // If user is a club user, redirect to club management players
        if ($user->role && in_array($user->role, ['club_admin', 'club_manager', 'club_medical'])) {
            return redirect()->route('club-management.players.index');
        }
        
        $players = Player::with(['club', 'association', 'healthRecords', 'medicalPredictions'])
            ->orderBy('name')
            ->paginate(20);

        return view('players.index', compact('players'));
    }

    public function create(): View
    {
        $clubs = Club::orderBy('name')->get();
        $associations = Association::orderBy('name')->get();
        
        return view('players.create', compact('clubs', 'associations'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'fifa_connect_id' => 'nullable|string|max:255',
            'name' => 'required|string|max:255',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'date_of_birth' => 'required|date',
            'nationality' => 'required|string|max:255',
            'position' => 'required|string|max:100',
            'club_id' => 'nullable|exists:clubs,id',
            'association_id' => 'nullable|exists:associations,id',
            'height' => 'nullable|integer|min:150|max:220',
            'weight' => 'nullable|integer|min:40|max:120',
            'preferred_foot' => 'nullable|in:Left,Right',
            'weak_foot' => 'nullable|integer|min:1|max:5',
            'skill_moves' => 'nullable|integer|min:1|max=5',
            'international_reputation' => 'nullable|integer|min:1|max=5',
            'work_rate' => 'nullable|string|max:50',
            'body_type' => 'nullable|string|max:100',
            'real_face' => 'nullable|boolean',
            'release_clause_eur' => 'nullable|numeric|min:0',
            'player_face_url' => 'nullable|url',
            'club_logo_url' => 'nullable|url',
            'nation_flag_url' => 'nullable|url',
            'overall_rating' => 'nullable|integer|min:1|max=99',
            'potential_rating' => 'nullable|integer|min:1|max=99',
            'value_eur' => 'nullable|numeric|min:0',
            'wage_eur' => 'nullable|numeric|min:0',
            'contract_valid_until' => 'nullable|date',
            'fifa_version' => 'nullable|string|max:50',
        ]);

        $validated['last_updated'] = now();
        $validated['real_face'] = $validated['real_face'] ?? false;

        $player = Player::create($validated);

        return redirect()->route('players.show', $player)
            ->with('success', 'Joueur créé avec succès.');
    }

    public function show(Player $player): View
    {
        $player->load(['club', 'association', 'healthRecords', 'medicalPredictions']);
        
        // Statistiques du joueur
        $stats = [
            'total_health_records' => $player->healthRecords()->count(),
            'recent_health_records' => $player->healthRecords()->where('record_date', '>=', now()->subMonths(3))->count(),
            'total_predictions' => $player->medicalPredictions()->count(),
            'active_predictions' => $player->medicalPredictions()->where('status', 'active')->count(),
            'high_risk_predictions' => $player->medicalPredictions()->where('risk_probability', '>', 0.7)->count(),
        ];

        return view('players.show', compact('player', 'stats'));
    }

    public function edit(Player $player): View
    {
        $clubs = Club::orderBy('name')->get();
        $associations = Association::orderBy('name')->get();
        
        return view('players.edit', compact('player', 'clubs', 'associations'));
    }

    public function update(Request $request, Player $player): RedirectResponse
    {
        $validated = $request->validate([
            'fifa_connect_id' => 'nullable|string|max:255',
            'name' => 'required|string|max:255',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'date_of_birth' => 'required|date',
            'nationality' => 'required|string|max:255',
            'position' => 'required|string|max:100',
            'club_id' => 'nullable|exists:clubs,id',
            'association_id' => 'nullable|exists:associations,id',
            'height' => 'nullable|integer|min:150|max:220',
            'weight' => 'nullable|integer|min:40|max:120',
            'preferred_foot' => 'nullable|in:Left,Right',
            'weak_foot' => 'nullable|integer|min:1|max:5',
            'skill_moves' => 'nullable|integer|min:1|max=5',
            'international_reputation' => 'nullable|integer|min:1|max=5',
            'work_rate' => 'nullable|string|max:50',
            'body_type' => 'nullable|string|max:100',
            'real_face' => 'nullable|boolean',
            'release_clause_eur' => 'nullable|numeric|min:0',
            'player_face_url' => 'nullable|url',
            'club_logo_url' => 'nullable|url',
            'nation_flag_url' => 'nullable|url',
            'overall_rating' => 'nullable|integer|min:1|max=99',
            'potential_rating' => 'nullable|integer|min:1|max=99',
            'value_eur' => 'nullable|numeric|min:0',
            'wage_eur' => 'nullable|numeric|min:0',
            'contract_valid_until' => 'nullable|date',
            'fifa_version' => 'nullable|string|max:50',
        ]);

        $validated['last_updated'] = now();

        $player->update($validated);

        return redirect()->route('players.show', $player)
            ->with('success', 'Joueur mis à jour avec succès.');
    }

    public function destroy(Player $player): RedirectResponse
    {
        $player->delete();
        
        return redirect()->route('players.index')
            ->with('success', 'Joueur supprimé avec succès.');
    }

    public function healthRecords(Player $player): View
    {
        $healthRecords = $player->healthRecords()
            ->orderBy('record_date', 'desc')
            ->paginate(10);

        return view('players.health-records', compact('player', 'healthRecords'));
    }

    public function predictions(Player $player): View
    {
        $predictions = $player->medicalPredictions()
            ->with(['healthRecord', 'user'])
            ->orderBy('prediction_date', 'desc')
            ->paginate(10);

        return view('players.predictions', compact('player', 'predictions'));
    }

    public function dashboard(): View
    {
        // Use cache for expensive operations
        $cacheKey = 'player_dashboard_stats_' . auth()->id();
        $stats = Cache::remember($cacheKey, 300, function () {
            $totalPlayers = Player::count();
            $playersWithHealthRecords = Player::has('healthRecords')->count();
            $playersWithPredictions = Player::has('medicalPredictions')->count();
            
            // Optimize age distribution queries with single query
            $ageDistribution = DB::select("
                SELECT 
                    CASE 
                        WHEN date_of_birth <= ? AND date_of_birth > ? THEN '18-25'
                        WHEN date_of_birth <= ? AND date_of_birth > ? THEN '26-30'
                        WHEN date_of_birth <= ? AND date_of_birth > ? THEN '31-35'
                        ELSE '36+'
                    END as age_group,
                    COUNT(*) as count
                FROM players 
                GROUP BY age_group
            ", [
                now()->subYears(18), now()->subYears(25),
                now()->subYears(26), now()->subYears(30),
                now()->subYears(31), now()->subYears(35)
            ]);
            
            $ageDistributionMap = [];
            foreach ($ageDistribution as $age) {
                $ageDistributionMap[$age->age_group] = $age->count;
            }
            
            return [
                'totalPlayers' => $totalPlayers,
                'playersWithHealthRecords' => $playersWithHealthRecords,
                'playersWithPredictions' => $playersWithPredictions,
                'ageDistribution' => $ageDistributionMap
            ];
        });
        
        $recentPlayers = Player::with(['club', 'association'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $playersByPosition = Player::selectRaw('position, COUNT(*) as count')
            ->groupBy('position')
            ->orderBy('count', 'desc')
            ->get();

        $playersByNationality = Player::selectRaw('nationality, COUNT(*) as count')
            ->groupBy('nationality')
            ->orderBy('count', 'desc')
            ->limit(10)
            ->get();

        $totalPlayers = $stats['totalPlayers'];
        $playersWithHealthRecords = $stats['playersWithHealthRecords'];
        $playersWithPredictions = $stats['playersWithPredictions'];
        $ageDistribution = $stats['ageDistribution'];

        return view('players.dashboard', compact(
            'totalPlayers',
            'playersWithHealthRecords',
            'playersWithPredictions',
            'recentPlayers',
            'playersByPosition',
            'playersByNationality',
            'ageDistribution'
        ));
    }

    public function search(Request $request): JsonResponse
    {
        $query = $request->get('q');
        
        $players = Player::where('name', 'like', "%{$query}%")
            ->orWhere('first_name', 'like', "%{$query}%")
            ->orWhere('last_name', 'like', "%{$query}%")
            ->orWhere('nationality', 'like', "%{$query}%")
            ->with(['club', 'association'])
            ->limit(10)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $players
        ]);
    }

    public function apiPlayers(): JsonResponse
    {
        $players = Player::with(['club', 'association'])
            ->orderBy('name')
            ->limit(50)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $players,
            'total' => Player::count()
        ]);
    }

    public function importFromFifa(Request $request): JsonResponse
    {
        // Simulation d'import depuis l'API FIFA
        $fifaData = [
            'name' => 'Lionel Messi',
            'first_name' => 'Lionel',
            'last_name' => 'Messi',
            'date_of_birth' => '1987-06-24',
            'nationality' => 'Argentina',
            'position' => 'RW',
            'height' => 170,
            'weight' => 72,
            'overall_rating' => 93,
            'potential_rating' => 93,
            'value_eur' => 50000000,
            'wage_eur' => 500000,
        ];

        $player = Player::create($fifaData);

        return response()->json([
            'success' => true,
            'message' => 'Joueur importé depuis FIFA avec succès.',
            'player' => $player
        ]);
    }

    public function bulkImport(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'players' => 'required|array|min:1|max:100',
                'players.*.fifa_connect_id' => 'nullable|string|max:255',
                'players.*.name' => 'required|string|max:255',
                'players.*.first_name' => 'required|string|max:255',
                'players.*.last_name' => 'required|string|max:255',
                'players.*.date_of_birth' => 'required|date',
                'players.*.nationality' => 'required|string|max:255',
                'players.*.position' => 'required|string|max:100',
                'players.*.height' => 'nullable|integer|min:150|max:220',
                'players.*.weight' => 'nullable|integer|min:40|max:120',
                'players.*.overall_rating' => 'nullable|integer|min:1|max:99',
                'players.*.potential_rating' => 'nullable|integer|min:1|max:99',
                'players.*.value_eur' => 'nullable|numeric|min:0',
                'players.*.wage_eur' => 'nullable|numeric|min:0',
                'players.*.preferred_foot' => 'nullable|in:Left,Right',
                'players.*.weak_foot' => 'nullable|integer|min:1|max:5',
                'players.*.skill_moves' => 'nullable|integer|min:1|max:5',
                'players.*.international_reputation' => 'nullable|integer|min:1|max:5',
                'players.*.work_rate' => 'nullable|string|max:50',
                'players.*.body_type' => 'nullable|string|max:100',
                'players.*.real_face' => 'nullable|boolean',
                'players.*.release_clause_eur' => 'nullable|numeric|min:0',
                'players.*.player_face_url' => 'nullable|url',
                'players.*.club_logo_url' => 'nullable|url',
                'players.*.nation_flag_url' => 'nullable|url',
                'players.*.contract_valid_until' => 'nullable|date',
                'players.*.fifa_version' => 'nullable|string|max:50',
            ]);

            $players = $request->input('players');
            $importedCount = 0;
            $updatedCount = 0;
            $errors = [];

            foreach ($players as $index => $playerData) {
                try {
                    $playerData['last_updated'] = now();
                    $playerData['real_face'] = $playerData['real_face'] ?? false;

                    $existingPlayer = null;
                    if (!empty($playerData['fifa_connect_id'])) {
                        $existingPlayer = Player::where('fifa_connect_id', $playerData['fifa_connect_id'])->first();
                    }

                    if ($existingPlayer) {
                        $existingPlayer->update($playerData);
                        $updatedCount++;
                    } else {
                        Player::create($playerData);
                        $importedCount++;
                    }
                } catch (\Exception $e) {
                    $errors[] = "Ligne " . ($index + 1) . " ({$playerData['name']}): " . $e->getMessage();
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Import en lot terminé',
                'imported_count' => $importedCount,
                'updated_count' => $updatedCount,
                'total_processed' => count($players),
                'errors' => $errors
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'import en lot',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function bulkImportForm(): View
    {
        return view('players.bulk-import');
    }

    public function exportPlayers(Request $request): JsonResponse
    {
        try {
            $players = Player::with(['club', 'association'])
                ->when($request->get('position'), function ($query, $position) {
                    return $query->where('position', $position);
                })
                ->when($request->get('nationality'), function ($query, $nationality) {
                    return $query->where('nationality', 'like', "%{$nationality}%");
                })
                ->when($request->get('min_rating'), function ($query, $rating) {
                    return $query->where('overall_rating', '>=', $rating);
                })
                ->when($request->get('max_rating'), function ($query, $rating) {
                    return $query->where('overall_rating', '<=', $rating);
                })
                ->get();

            $exportData = $players->map(function ($player) {
                return [
                    'fifa_connect_id' => $player->fifa_connect_id,
                    'name' => $player->name,
                    'first_name' => $player->first_name,
                    'last_name' => $player->last_name,
                    'date_of_birth' => $player->date_of_birth?->format('Y-m-d'),
                    'nationality' => $player->nationality,
                    'position' => $player->position,
                    'height' => $player->height,
                    'weight' => $player->weight,
                    'overall_rating' => $player->overall_rating,
                    'potential_rating' => $player->potential_rating,
                    'value_eur' => $player->value_eur,
                    'wage_eur' => $player->wage_eur,
                    'preferred_foot' => $player->preferred_foot,
                    'weak_foot' => $player->weak_foot,
                    'skill_moves' => $player->skill_moves,
                    'international_reputation' => $player->international_reputation,
                    'work_rate' => $player->work_rate,
                    'body_type' => $player->body_type,
                    'real_face' => $player->real_face,
                    'release_clause_eur' => $player->release_clause_eur,
                    'player_face_url' => $player->player_face_url,
                    'club_logo_url' => $player->club_logo_url,
                    'nation_flag_url' => $player->nation_flag_url,
                    'contract_valid_until' => $player->contract_valid_until?->format('Y-m-d'),
                    'fifa_version' => $player->fifa_version,
                    'club_name' => $player->club?->name,
                    'association_name' => $player->association?->name,
                    'created_at' => $player->created_at?->format('Y-m-d H:i:s'),
                    'updated_at' => $player->updated_at?->format('Y-m-d H:i:s'),
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $exportData,
                'total' => $exportData->count(),
                'filename' => 'players_export_' . now()->format('Y-m-d_H-i-s') . '.json'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'export',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
