<?php

namespace App\Http\Controllers;

use App\Models\Performance;
use App\Models\Player;
use App\Models\Club;
use App\Models\Federation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class PerformanceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Performance::with(['player']);

        // Apply filters
        if ($request->filled('player_id')) {
            $query->where('player_id', $request->player_id);
        }

        if ($request->filled('start_date')) {
            $query->where('match_date', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->where('match_date', '<=', $request->end_date);
        }

        if ($request->filled('search')) {
            $query->whereHas('player', function ($q) use ($request) {
                $q->where('first_name', 'like', '%' . $request->search . '%')
                  ->orWhere('last_name', 'like', '%' . $request->search . '%')
                  ->orWhere('notes', 'like', '%' . $request->search . '%');
            });
        }

        $performances = $query->orderBy('match_date', 'desc')->paginate(15);

        // Get players for filter dropdown
        $players = Player::orderBy('first_name')->get();

        // Get statistics
        $stats = [
            'total_performances' => Performance::count(),
            'average_distance' => Performance::avg('distance_covered'),
            'average_rating' => Performance::avg('rating'),
        ];

        return view('performances.index', compact('performances', 'stats', 'players'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $players = Player::orderBy('first_name')->get();
        return view('performances.create', compact('players'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'player_id' => 'required|exists:players,id',
            'match_date' => 'required|date',
            'distance_covered' => 'required|integer|min:0',
            'sprint_count' => 'required|integer|min:0',
            'max_speed' => 'required|numeric|min:0',
            'avg_speed' => 'required|numeric|min:0',
            'passes_completed' => 'nullable|integer|min:0',
            'passes_attempted' => 'nullable|integer|min:0',
            'tackles_won' => 'nullable|integer|min:0',
            'tackles_attempted' => 'nullable|integer|min:0',
            'shots_on_target' => 'nullable|integer|min:0',
            'shots_total' => 'nullable|integer|min:0',
            'goals_scored' => 'nullable|integer|min:0',
            'assists' => 'nullable|integer|min:0',
            'yellow_cards' => 'nullable|integer|min:0',
            'red_cards' => 'nullable|integer|min:0',
            'minutes_played' => 'nullable|integer|min:0',
            'rating' => 'required|numeric|min:0|max:10',
            'notes' => 'nullable|string',
        ]);

        Performance::create($validated);

        return redirect()->route('performances.index')
            ->with('success', 'Performance record created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Performance $performance)
    {
        $performance->load('player');
        return view('performances.show', compact('performance'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Performance $performance)
    {
        $players = Player::orderBy('first_name')->get();
        return view('performances.edit', compact('performance', 'players'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Performance $performance)
    {
        $validated = $request->validate([
            'player_id' => 'required|exists:players,id',
            'match_date' => 'required|date',
            'distance_covered' => 'required|integer|min:0',
            'sprint_count' => 'required|integer|min:0',
            'max_speed' => 'required|numeric|min:0',
            'avg_speed' => 'required|numeric|min:0',
            'passes_completed' => 'nullable|integer|min:0',
            'passes_attempted' => 'nullable|integer|min:0',
            'tackles_won' => 'nullable|integer|min:0',
            'tackles_attempted' => 'nullable|integer|min:0',
            'shots_on_target' => 'nullable|integer|min:0',
            'shots_total' => 'nullable|integer|min:0',
            'goals_scored' => 'nullable|integer|min:0',
            'assists' => 'nullable|integer|min:0',
            'yellow_cards' => 'nullable|integer|min:0',
            'red_cards' => 'nullable|integer|min:0',
            'minutes_played' => 'nullable|integer|min:0',
            'rating' => 'required|numeric|min:0|max:10',
            'notes' => 'nullable|string',
        ]);

        $performance->update($validated);

        return redirect()->route('performances.index')
            ->with('success', 'Performance record updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Performance $performance)
    {
        $performance->delete();

        return redirect()->route('performances.index')
            ->with('success', 'Performance record deleted successfully.');
    }

    /**
     * Display the performance dashboard.
     */
    public function dashboard(Request $request)
    {
        $level = $request->get('level', 'player');
        $id = $request->get('id');

        $dashboardData = [];

        switch ($level) {
            case 'federation':
                $federation = Federation::findOrFail($id);
                $dashboardData = $this->getFederationDashboardData($federation);
                break;
            case 'club':
                $club = Club::findOrFail($id);
                $dashboardData = $this->getClubDashboardData($club);
                break;
            case 'player':
                $player = Player::findOrFail($id);
                $dashboardData = $this->getPlayerDashboardData($player);
                break;
        }

        return view('performances.dashboard', compact('dashboardData', 'level'));
    }

    /**
     * Display performance analytics.
     */
    public function analytics(Request $request)
    {
        $playerId = $request->get('player_id');
        
        $query = Performance::query();
        
        if ($playerId) {
            $query->where('player_id', $playerId);
        }

        $analytics = [
            'total_performances' => $query->count(),
            'average_distance' => $query->avg('distance_covered'),
            'average_rating' => $query->avg('rating'),
            'total_matches' => $query->distinct('match_date')->count(),
            'performance_trends' => $this->getPerformanceTrends($query),
            'top_performers' => $this->getTopPerformers($query),
        ];

        return response()->json(['data' => $analytics]);
    }

    /**
     * Export performances.
     */
    public function export(Request $request)
    {
        $format = $request->get('format', 'csv');
        $playerId = $request->get('player_id');

        $query = Performance::with('player');
        
        if ($playerId) {
            $query->where('player_id', $playerId);
        }

        $performances = $query->get();

        if ($format === 'csv') {
            return $this->exportToCsv($performances);
        }

        return response()->json(['data' => $performances]);
    }

    /**
     * Bulk import performances.
     */
    public function bulkImport(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt'
        ]);

        $file = $request->file('file');
        $importedCount = 0;

        if (($handle = fopen($file->getPathname(), "r")) !== FALSE) {
            // Skip header row
            fgetcsv($handle);

            while (($data = fgetcsv($handle)) !== FALSE) {
                if (count($data) >= 5) {
                    Performance::create([
                        'player_id' => $data[0],
                        'match_date' => $data[1],
                        'distance_covered' => $data[2],
                        'sprint_count' => $data[3],
                        'max_speed' => $data[4],
                    ]);
                    $importedCount++;
                }
            }
            fclose($handle);
        }

        return response()->json([
            'message' => 'Performances imported successfully',
            'imported_count' => $importedCount
        ]);
    }

    /**
     * Compare performances.
     */
    public function compare(Request $request)
    {
        $playerIds = $request->get('players', []);
        
        $comparison = [];
        foreach ($playerIds as $playerId) {
            $player = Player::find($playerId);
            if ($player) {
                $comparison[] = [
                    'player' => $player,
                    'performances' => $player->performances()->latest()->take(5)->get(),
                    'averages' => $this->getPlayerAverages($player)
                ];
            }
        }

        return view('performances.compare', compact('comparison'));
    }

    /**
     * Show performance trends.
     */
    public function trends(Request $request)
    {
        $playerId = $request->get('player_id');
        $metric = $request->get('metric', 'distance_covered');

        $trends = $this->getPerformanceTrends(
            Performance::where('player_id', $playerId),
            $metric
        );

        return response()->json(['data' => $trends]);
    }

    /**
     * Generate performance alerts.
     */
    public function generateAlerts()
    {
        // Generate alerts for low performance
        $lowPerformances = Performance::where('distance_covered', '<', 9000)
            ->orWhere('rating', '<', 7.0)
            ->with('player')
            ->get();

        return response()->json([
            'message' => 'Alerts generated successfully',
            'alerts_count' => $lowPerformances->count()
        ]);
    }

    /**
     * Get federation dashboard data.
     */
    private function getFederationDashboardData(Federation $federation)
    {
        $clubs = $federation->clubs;
        $totalPerformances = 0;
        $totalPlayers = 0;

        foreach ($clubs as $club) {
            $totalPerformances += $club->players()->withCount('performances')->get()->sum('performances_count');
            $totalPlayers += $club->players()->count();
        }

        return [
            'federation' => $federation,
            'total_performances' => $totalPerformances,
            'total_players' => $totalPlayers,
            'clubs_count' => $clubs->count(),
        ];
    }

    /**
     * Get club dashboard data.
     */
    private function getClubDashboardData(Club $club)
    {
        $players = $club->players;
        $totalPerformances = $players->withCount('performances')->get()->sum('performances_count');

        return [
            'club' => $club,
            'total_performances' => $totalPerformances,
            'players_count' => $players->count(),
        ];
    }

    /**
     * Get player dashboard data.
     */
    private function getPlayerDashboardData(Player $player)
    {
        $performances = $player->performances()->latest()->take(10)->get();

        return [
            'player' => $player,
            'total_performances' => $player->performances()->count(),
            'average_distance' => $player->performances()->avg('distance_covered'),
            'average_rating' => $player->performances()->avg('rating'),
            'recent_performances' => $performances,
        ];
    }

    /**
     * Get performance trends.
     */
    private function getPerformanceTrends($query, $metric = 'distance_covered')
    {
        return $query->select(DB::raw("DATE(match_date) as date"), DB::raw("AVG($metric) as average"))
            ->groupBy('date')
            ->orderBy('date')
            ->get();
    }

    /**
     * Get top performers.
     */
    private function getTopPerformers($query)
    {
        return $query->with('player')
            ->select('player_id', DB::raw('AVG(rating) as avg_rating'))
            ->groupBy('player_id')
            ->orderBy('avg_rating', 'desc')
            ->limit(10)
            ->get();
    }

    /**
     * Get player averages.
     */
    private function getPlayerAverages(Player $player)
    {
        return [
            'distance_covered' => $player->performances()->avg('distance_covered'),
            'sprint_count' => $player->performances()->avg('sprint_count'),
            'max_speed' => $player->performances()->avg('max_speed'),
            'rating' => $player->performances()->avg('rating'),
        ];
    }

    /**
     * Export to CSV.
     */
    private function exportToCsv($performances)
    {
        $filename = 'performances_' . date('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($performances) {
            $file = fopen('php://output', 'w');
            
            // Header row
            fputcsv($file, [
                'Player ID', 'Player Name', 'Match Date', 'Distance Covered', 
                'Sprint Count', 'Max Speed', 'Rating', 'Notes'
            ]);

            // Data rows
            foreach ($performances as $performance) {
                fputcsv($file, [
                    $performance->player_id,
                    $performance->player->first_name . ' ' . $performance->player->last_name,
                    $performance->match_date,
                    $performance->distance_covered,
                    $performance->sprint_count,
                    $performance->max_speed,
                    $performance->rating,
                    $performance->notes,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
