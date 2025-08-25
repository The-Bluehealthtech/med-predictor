<?php

namespace App\Http\Controllers;

use App\Models\HealthRecord;
use App\Models\Player;
use App\Models\Club;
use App\Models\Association;
use App\Models\FifaConnectId;
use App\Services\FifaConnectService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\Rule;

class HealthcareController extends Controller
{
    protected $fifaConnectService;

    public function __construct(FifaConnectService $fifaConnectService)
    {
        $this->fifaConnectService = $fifaConnectService;
        $this->middleware('auth');
        $this->middleware('role:club_admin,club_manager,club_medical,association_admin,association_registrar,association_medical,system_admin');
    }

    public function index()
    {
        $user = Auth::user();
        $cacheKey = "healthcare_index_{$user->id}_" . request()->get('page', 1);
        
        $data = Cache::remember($cacheKey, 300, function () use ($user) {
            $healthRecords = collect();

            if (in_array($user->role, ['club_admin', 'club_manager', 'club_medical'])) {
                $healthRecords = HealthRecord::whereHas('player', function ($query) use ($user) {
                    $query->where('club_id', $user->club_id);
                })
                ->with(['player', 'player.club', 'player.fifaConnectId'])
                ->orderBy('created_at', 'desc')
                ->paginate(15);
            } elseif (in_array($user->role, ['association_admin', 'association_registrar', 'association_medical'])) {
                $healthRecords = HealthRecord::whereHas('player.club', function ($query) use ($user) {
                    $query->where('association_id', $user->association_id);
                })
                ->with(['player', 'player.club', 'player.fifaConnectId'])
                ->orderBy('created_at', 'desc')
                ->paginate(15);
            } elseif ($user->role === 'system_admin') {
                // System admin can see all records
                $healthRecords = HealthRecord::with(['player', 'player.club', 'player.fifaConnectId'])
                    ->orderBy('created_at', 'desc')
                    ->paginate(15);
            }

            // Optimize stats calculation with single query
            $statsQuery = HealthRecord::query();
            
            if (in_array($user->role, ['club_admin', 'club_manager', 'club_medical'])) {
                $statsQuery->whereHas('player', function ($query) use ($user) {
                    $query->where('club_id', $user->club_id);
                });
            } elseif (in_array($user->role, ['association_admin', 'association_registrar', 'association_medical'])) {
                $statsQuery->whereHas('player.club', function ($query) use ($user) {
                    $query->where('association_id', $user->association_id);
                });
            }
            // system_admin can see all stats
            
            $stats = $statsQuery->selectRaw('
                COUNT(*) as total,
                SUM(CASE WHEN status = "active" THEN 1 ELSE 0 END) as healthy,
                SUM(CASE WHEN status = "archived" THEN 1 ELSE 0 END) as injured,
                SUM(CASE WHEN status = "pending" THEN 1 ELSE 0 END) as recovering
            ')->first();
            
            return [
                'healthRecords' => $healthRecords,
                'stats' => [
                    'total' => $stats->total ?? 0,
                    'healthy' => $stats->healthy ?? 0,
                    'injured' => $stats->injured ?? 0,
                    'recovering' => $stats->recovering ?? 0,
                ]
            ];
        });
        
        $healthRecords = $data['healthRecords'];
        $stats = $data['stats'];

        return view('healthcare.index', compact('healthRecords', 'stats'));
    }

    public function create()
    {
        $user = Auth::user();
        $players = collect();

        if (in_array($user->role, ['club_admin', 'club_manager', 'club_medical'])) {
            $players = Player::where('club_id', $user->club_id)
                ->orderBy('first_name')
                ->orderBy('last_name')
                ->get();
        } elseif (in_array($user->role, ['association_admin', 'association_registrar', 'association_medical'])) {
            $players = Player::whereHas('club', function ($query) use ($user) {
                $query->where('association_id', $user->association_id);
            })
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get();
        } elseif ($user->role === 'system_admin') {
            // System admin can see all players
            $players = Player::with('club')
                ->orderBy('first_name')
                ->orderBy('last_name')
                ->get();
        }

        return view('healthcare.records.create', compact('players'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        
        $validated = $request->validate([
            'player_id' => 'required|exists:players,id',
            'status' => 'required|in:healthy,injured,recovering,unfit',
            'diagnosis' => 'nullable|string',
            'treatment_plan' => 'nullable|string',
            'medications' => 'nullable|string',
            'symptoms' => 'nullable|string',
            'next_checkup_date' => 'nullable|date|after:today',
            'record_date' => 'nullable|date',
            'fifa_connect_id' => 'nullable|string|unique:fifa_connect_ids,fifa_id',
        ]);

        // Verify player access
        $player = Player::findOrFail($validated['player_id']);
        $this->authorizePlayerAccess($player);

        DB::beginTransaction();
        try {
            // Create FIFA Connect ID if provided or generate one
            $fifaConnectId = null;
            if (!empty($validated['fifa_connect_id'])) {
                $fifaConnectId = FifaConnectId::create([
                    'fifa_id' => $validated['fifa_connect_id'],
                    'entity_type' => 'health_record',
                    'status' => 'active'
                ]);
            } else {
                // Generate FIFA Connect ID
                $fifaConnectId = $this->fifaConnectService->generateHealthRecordId();
            }

            // Create health record
            $healthRecord = HealthRecord::create([
                'player_id' => $validated['player_id'],
                'status' => $validated['status'],
                'diagnosis' => $validated['diagnosis'],
                'treatment_plan' => $validated['treatment_plan'],
                'medications' => $validated['medications'],
                'symptoms' => $validated['symptoms'],
                'next_checkup_date' => $validated['next_checkup_date'],
                'record_date' => $validated['record_date'] ?? now(),
                'fifa_connect_id' => $fifaConnectId->id,
            ]);

            // Update player status if needed
            if (in_array($validated['status'], ['injured', 'unfit'])) {
                $player->update(['status' => 'suspended']);
            }

            DB::commit();

            return redirect()->route('healthcare.records.index')
                ->with('success', 'Health record created successfully with FIFA Connect ID: ' . $fifaConnectId->fifa_id);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Failed to create health record: ' . $e->getMessage());
        }
    }

    public function show(HealthRecord $healthRecord)
    {
        $this->authorizeHealthRecordAccess($healthRecord);
        
        $healthRecord->load(['player', 'player.club']);
        
        return view('healthcare.records.show', compact('healthRecord'));
    }

    public function edit(HealthRecord $healthRecord)
    {
        $this->authorizeHealthRecordAccess($healthRecord);
        
        $user = Auth::user();
        $players = collect();

        if (in_array($user->role, ['club_admin', 'club_manager', 'club_medical'])) {
            $players = Player::where('club_id', $user->club_id)
                ->orderBy('first_name')
                ->orderBy('last_name')
                ->get();
        } elseif (in_array($user->role, ['association_admin', 'association_registrar', 'association_medical'])) {
            $players = Player::whereHas('club', function ($query) use ($user) {
                $query->where('association_id', $user->association_id);
            })
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get();
        }

        $healthRecord->load(['player', 'fifaConnectId']);
        
        return view('healthcare.records.edit', compact('healthRecord', 'players'));
    }

    public function update(Request $request, HealthRecord $healthRecord)
    {
        $this->authorizeHealthRecordAccess($healthRecord);
        
        $validated = $request->validate([
            'player_id' => 'required|exists:players,id',
            'record_type' => 'required|in:medical_check,injury_report,recovery_update,health_assessment',
            'status' => 'required|in:healthy,injured,recovering,unfit',
            'description' => 'required|string',
            'diagnosis' => 'nullable|string',
            'treatment' => 'nullable|string',
            'medications' => 'nullable|string',
            'restrictions' => 'nullable|string',
            'next_checkup' => 'nullable|date',
            'doctor_name' => 'nullable|string|max:255',
            'hospital_clinic' => 'nullable|string|max:255',
            'severity' => 'nullable|in:low,medium,high,critical',
        ]);

        // Verify player access
        $player = Player::findOrFail($validated['player_id']);
        $this->authorizePlayerAccess($player);

        DB::beginTransaction();
        try {
            $oldStatus = $healthRecord->status;
            $healthRecord->update($validated);

            // Update player status if needed
            if (in_array($validated['status'], ['injured', 'unfit']) && $oldStatus !== $validated['status']) {
                $player->update(['status' => 'suspended']);
            } elseif ($validated['status'] === 'healthy' && $oldStatus !== 'healthy') {
                $player->update(['status' => 'active']);
            }

            // Sync with FIFA Connect if needed
            if ($healthRecord->fifaConnectId) {
                $this->fifaConnectService->syncHealthRecord($healthRecord);
            }

            DB::commit();

            return redirect()->route('healthcare.records.show', ['record' => $healthRecord->id])
                ->with('success', 'Health record updated successfully');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Failed to update health record: ' . $e->getMessage());
        }
    }

    public function destroy(HealthRecord $healthRecord)
    {
        $this->authorizeHealthRecordAccess($healthRecord);
        
        try {
            $healthRecord->delete();
            return redirect()->route('healthcare.records.index')
                ->with('success', 'Health record deleted successfully');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete health record: ' . $e->getMessage());
        }
    }

    public function sync(HealthRecord $healthRecord)
    {
        $this->authorizeHealthRecordAccess($healthRecord);
        
        try {
            $result = $this->fifaConnectService->syncHealthRecord($healthRecord);
            return back()->with('success', 'Health record synced with FIFA Connect successfully');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to sync health record: ' . $e->getMessage());
        }
    }

    public function bulkSync()
    {
        $user = Auth::user();
        $healthRecords = collect();

        if ($user->role === 'club') {
            $healthRecords = HealthRecord::whereHas('player', function ($query) use ($user) {
                $query->where('club_id', $user->club_id);
            })->get();
        } elseif ($user->role === 'association') {
            $healthRecords = HealthRecord::whereHas('player.club', function ($query) use ($user) {
                $query->where('association_id', $user->association_id);
            })->get();
        }

        $synced = 0;
        $failed = 0;

        foreach ($healthRecords as $healthRecord) {
            try {
                $this->fifaConnectService->syncHealthRecord($healthRecord);
                $synced++;
            } catch (\Exception $e) {
                $failed++;
            }
        }

        return back()->with('success', "Bulk sync completed: {$synced} synced, {$failed} failed");
    }

    public function export()
    {
        $user = Auth::user();
        $healthRecords = collect();

        if ($user->role === 'club') {
            $healthRecords = HealthRecord::whereHas('player', function ($query) use ($user) {
                $query->where('club_id', $user->club_id);
            })
            ->with(['player', 'player.club', 'fifaConnectId'])
            ->get();
        } elseif ($user->role === 'association') {
            $healthRecords = HealthRecord::whereHas('player.club', function ($query) use ($user) {
                $query->where('association_id', $user->association_id);
            })
            ->with(['player', 'player.club', 'fifaConnectId'])
            ->get();
        }

        $filename = 'health_records_' . date('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($healthRecords) {
            $file = fopen('php://output', 'w');
            
            // CSV headers
            fputcsv($file, [
                'FIFA Connect ID', 'Player Name', 'Club', 'Record Type', 'Status', 
                'Severity', 'Doctor', 'Hospital/Clinic', 'Next Checkup', 'Created At'
            ]);

            foreach ($healthRecords as $record) {
                fputcsv($file, [
                    $record->fifaConnectId?->fifa_id ?? 'N/A',
                    $record->player ? ($record->player->first_name . ' ' . $record->player->last_name) : 'N/A',
                    $record->player?->club?->name ?? 'N/A',
                    $record->record_type,
                    $record->status,
                    $record->severity ?? 'N/A',
                    $record->doctor_name ?? 'N/A',
                    $record->hospital_clinic ?? 'N/A',
                    $record->next_checkup ?? 'N/A',
                    $record->created_at
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function predictions()
    {
        $user = Auth::user();
        $players = collect();

        if (in_array($user->role, ['club_admin', 'club_manager', 'club_medical'])) {
            $players = Player::where('club_id', $user->club_id)
                ->with(['healthRecords' => function ($query) {
                    $query->latest()->take(10);
                }])
                ->get();
        } elseif (in_array($user->role, ['association_admin', 'association_registrar', 'association_medical'])) {
            $players = Player::whereHas('club', function ($query) use ($user) {
                $query->where('association_id', $user->association_id);
            })
            ->with(['healthRecords' => function ($query) {
                $query->latest()->take(10);
            }])
            ->get();
        } elseif ($user->role === 'system_admin') {
            $players = Player::with(['healthRecords' => function ($query) {
                $query->latest()->take(10);
            }])
            ->get();
        }

        // Analyze health patterns and generate predictions
        $predictions = [];
        foreach ($players as $player) {
            $recentRecords = $player->healthRecords;
            $activeRecords = $recentRecords->where('status', 'active');
            $archivedRecords = $recentRecords->where('status', 'archived');
            $pendingRecords = $recentRecords->where('status', 'pending');
            
            // Calculate risk factors
            $highRiskFactors = $activeRecords->filter(function ($record) {
                return $record->risk_score > 0.7;
            })->count();
            
            $mediumRiskFactors = $activeRecords->filter(function ($record) {
                return $record->risk_score > 0.4 && $record->risk_score <= 0.7;
            })->count();
            
            $avgRiskScore = $activeRecords->avg('risk_score') ?? 0;
            $avgConfidence = $activeRecords->avg('prediction_confidence') ?? 0;
            
            // Determine risk level
            $riskLevel = 'low';
            if ($highRiskFactors > 2 || $avgRiskScore > 0.7) {
                $riskLevel = 'high';
            } elseif ($highRiskFactors > 0 || $mediumRiskFactors > 2 || $avgRiskScore > 0.4) {
                $riskLevel = 'medium';
            }
            
            // Generate predictions
            $predictions[] = [
                'player' => $player,
                'risk_level' => $riskLevel,
                'risk_score' => round($avgRiskScore, 3),
                'confidence' => round($avgConfidence, 3),
                'active_records' => $activeRecords->count(),
                'archived_records' => $archivedRecords->count(),
                'pending_records' => $pendingRecords->count(),
                'high_risk_factors' => $highRiskFactors,
                'medium_risk_factors' => $mediumRiskFactors,
                'recommendation' => $this->generateRecommendation($riskLevel, $avgRiskScore, $highRiskFactors),
                'next_checkup' => $activeRecords->first()?->next_checkup_date,
            ];
        }

        // Sort by risk level (high first)
        usort($predictions, function ($a, $b) {
            $riskOrder = ['high' => 3, 'medium' => 2, 'low' => 1];
            return $riskOrder[$b['risk_level']] - $riskOrder[$a['risk_level']];
        });

        return view('healthcare.predictions', compact('predictions'));
    }

    protected function generateRecommendation($riskLevel, $avgRiskScore, $highRiskFactors)
    {
        if ($riskLevel === 'high') {
            if ($avgRiskScore > 0.8) {
                return 'Risque très élevé. Recommandation : Arrêt immédiat des activités sportives, consultation médicale urgente, surveillance intensive.';
            } else {
                return 'Risque élevé. Recommandation : Réduction significative de l\'intensité d\'entraînement, monitoring médical régulier, évaluation complète.';
            }
        } elseif ($riskLevel === 'medium') {
            if ($highRiskFactors > 0) {
                return 'Risque modéré avec facteurs de risque élevés. Recommandation : Surveillance accrue, ajustement de l\'entraînement, contrôles médicaux fréquents.';
            } else {
                return 'Risque modéré. Recommandation : Monitoring de la charge d\'entraînement, protocoles de récupération appropriés, contrôles réguliers.';
            }
        } else {
            if ($avgRiskScore > 0.2) {
                return 'Risque faible mais surveillance recommandée. Continuer l\'entraînement actuel avec monitoring standard.';
            } else {
                return 'Risque faible. Continuer le régime d\'entraînement actuel avec surveillance standard.';
            }
        }
    }

    protected function authorizeHealthRecordAccess(HealthRecord $healthRecord)
    {
        $user = Auth::user();
        $player = $healthRecord->player;

        if ($user->role === 'system_admin') {
            // System admin can access all records
            return;
        }

        if (!$player) {
            abort(404, 'Player not found');
        }

        if (in_array($user->role, ['club_admin', 'club_manager', 'club_medical']) && $player->club_id !== $user->club_id) {
            abort(403, 'Unauthorized access to health record');
        }

        if (in_array($user->role, ['association_admin', 'association_registrar', 'association_medical'])) {
            $club = $player->club;
            if (!$club || $club->association_id !== $user->association_id) {
                abort(403, 'Unauthorized access to health record');
            }
        }
    }

    protected function authorizePlayerAccess(Player $player)
    {
        $user = Auth::user();
        
        if (in_array($user->role, ['club_admin', 'club_manager', 'club_medical']) && $player->club_id !== $user->club_id) {
            abort(403, 'Unauthorized access to player');
        }
        
        if (in_array($user->role, ['association_admin', 'association_registrar', 'association_medical'])) {
            $club = $player->club;
            if (!$club || $club->association_id !== $user->association_id) {
                abort(403, 'Unauthorized access to player');
            }
        }
    }
}
