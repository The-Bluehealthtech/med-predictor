<?php

namespace App\Http\Controllers;

use App\Models\Player;
use App\Models\Club;
use App\Models\Association;
use App\Models\Competition;
use App\Models\Team;
use App\Models\FifaConnectId;
use App\Models\PlayerLicense;
use App\Services\FifaConnectService;
use App\Events\PlayerRegistered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class PlayerRegistrationController extends Controller
{
    protected $fifaConnectService;

    public function __construct(FifaConnectService $fifaConnectService)
    {
        $this->fifaConnectService = $fifaConnectService;
        $this->middleware('auth');
        $this->middleware('role:admin,club_admin,club_manager,club_medical,association_admin,association_registrar,association_medical,system_admin');
    }

    public function index()
    {
        $user = Auth::user();
        
        // Debug logging
        \Log::info('PlayerRegistrationController::index called', [
            'user_id' => $user->id,
            'user_email' => $user->email,
            'user_role' => $user->role,
            'club_id' => $user->club_id,
            'association_id' => $user->association_id
        ]);

        $players = collect();
        $licenseRequests = collect();

        // Check if user has club-related role - ONLY show club-specific data
        if (in_array($user->role, ['club_admin', 'club_manager', 'club_medical'])) {
            if (!$user->club_id) {
                return redirect()->route('dashboard')
                    ->with('error', 'You are not associated with any club.');
            }
            
            $players = Player::where('club_id', $user->club_id)
                ->with(['club', 'association', 'fifaConnectId'])
                ->orderBy('created_at', 'desc')
                ->paginate(15);
                
            // Get license requests for this club only
            $licenseRequests = PlayerLicense::where('club_id', $user->club_id)
                ->with(['player', 'club', 'approvedByUser', 'requestedByUser'])
                ->orderBy('created_at', 'desc')
                ->get();
        } 
        // Check if user has association-related role - show all clubs in association
        elseif (in_array($user->role, ['association_admin', 'association_registrar', 'association_medical'])) {
            if (!$user->association_id) {
                return redirect()->route('dashboard')
                    ->with('error', 'You are not associated with any association.');
            }
            
            $players = Player::whereHas('club', function ($query) use ($user) {
                $query->where('association_id', $user->association_id);
            })
            ->with(['club', 'association', 'fifaConnectId'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);
            
            // Get all license requests for clubs in this association
            $licenseRequests = PlayerLicense::whereHas('club', function ($query) use ($user) {
                $query->where('association_id', $user->association_id);
            })
            ->with(['player', 'club', 'approvedByUser', 'requestedByUser'])
            ->orderBy('created_at', 'desc')
            ->get();
        }
        // For system admin and admin, show all players
        elseif (in_array($user->role, ['system_admin', 'admin'])) {
            $players = Player::with(['club', 'association', 'fifaConnectId'])
                ->orderBy('created_at', 'desc')
                ->paginate(15);
                
            // Get all license requests
            $licenseRequests = PlayerLicense::with(['player', 'club', 'approvedByUser', 'requestedByUser'])
                ->orderBy('created_at', 'desc')
                ->get();
        }

        // Debug logging for players
        \Log::info('Players query result', [
            'total_players' => $players->total(),
            'current_page_players' => $players->count(),
            'user_role' => $user->role,
            'club_id' => $user->club_id,
            'association_id' => $user->association_id
        ]);

        // Calculate stats based on the actual players collection
        $allPlayers = $players->getCollection();
        $stats = [
            'total' => $players->total(),
            'with_fifa_id' => $allPlayers->whereNotNull('fifaConnectId')->count(),
            'without_fifa_id' => $allPlayers->whereNull('fifaConnectId')->count(),
            'with_club' => $allPlayers->whereNotNull('club_id')->count(),
        ];

        // Calculate license request stats
        $licenseStats = [
            'total_requests' => $licenseRequests->count(),
            'pending_approval' => $licenseRequests->where('status', 'pending')->count(),
            'approved' => $licenseRequests->where('status', 'active')->count(),
            'rejected' => $licenseRequests->where('status', 'revoked')->count(),
            'expired' => $licenseRequests->where('status', 'expired')->count(),
            'suspended' => $licenseRequests->where('status', 'suspended')->count(),
            'needs_renewal' => $licenseRequests->where('status', 'active')->filter(function($license) {
                return $license->requiresRenewal();
            })->count(),
        ];

        // Debug logging for stats
        \Log::info('Stats calculated', $stats);
        \Log::info('License stats calculated', $licenseStats);

        return view('player-registration.index', compact('players', 'stats', 'licenseRequests', 'licenseStats'));
    }

    public function create()
    {
        $user = Auth::user();
        $clubs = collect();
        $associations = collect();
        $competitions = collect();
        $teams = collect();

        // Get clubs based on user role
        if (in_array($user->role, ['association_admin', 'association_registrar', 'association_medical'])) {
            $clubs = Club::where('association_id', $user->association_id)
                ->orderBy('name')
                ->get();
        } elseif (in_array($user->role, ['system_admin', 'admin'])) {
            $clubs = Club::orderBy('name')->get();
        }

        // Get associations for system admin and admin
        if (in_array($user->role, ['system_admin', 'admin'])) {
            $associations = Association::orderBy('name')->get();
        }

        // Get competitions
        $competitions = Competition::orderBy('name')->get();

        // Get teams for selected clubs
        if ($clubs->isNotEmpty()) {
            $teams = Team::whereIn('club_id', $clubs->pluck('id'))
                ->orderBy('name')
                ->get();
        }

        // Get helper data
        $nationalities = \App\Helpers\NationalityHelper::getNationalities();
        $positions = \App\Helpers\NationalityHelper::getPositions();
        $stakeholderRoles = \App\Helpers\NationalityHelper::getStakeholderRoles();

        return view('modules.player-registration.create', compact(
            'clubs', 
            'associations', 
            'competitions', 
            'teams',
            'nationalities',
            'positions',
            'stakeholderRoles'
        ));
    }

    public function createStakeholder()
    {
        $user = Auth::user();
        $clubs = collect();
        $associations = collect();

        // Get clubs based on user role
        if (in_array($user->role, ['association_admin', 'association_registrar', 'association_medical'])) {
            $clubs = Club::where('association_id', $user->association_id)
                ->orderBy('name')
                ->get();
        } elseif (in_array($user->role, ['system_admin', 'admin'])) {
            $clubs = Club::orderBy('name')->get();
        }

        // Get associations for system admin and admin
        if (in_array($user->role, ['system_admin', 'admin'])) {
            $associations = Association::orderBy('name')->get();
        }

        // Get helper data
        $nationalities = \App\Helpers\NationalityHelper::getNationalities();
        $stakeholderRoles = \App\Helpers\NationalityHelper::getStakeholderRoles();
        $licenseTypes = \App\Helpers\NationalityHelper::getLicenseTypes();

        return view('modules.player-registration.create-stakeholder', compact(
            'clubs', 
            'associations',
            'nationalities',
            'stakeholderRoles',
            'licenseTypes'
        ));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'date_of_birth' => 'required|date|before:today',
            'nationality' => 'required|string|max:255',
            'position' => 'required|string|max:255',
            'jersey_number' => 'nullable|integer|min:1|max:99',
            'height' => 'nullable|numeric|min:100|max:250',
            'weight' => 'nullable|numeric|min:30|max:150',
            'email' => 'nullable|email|unique:players,email',
            'phone' => 'nullable|string|max:20',
            'club_id' => in_array($user->role, ['association_admin', 'association_registrar', 'association_medical']) ? 'required|exists:clubs,id' : 'nullable',
            'fifa_connect_id' => 'nullable|string|unique:fifa_connect_ids,fifa_id',
            'player_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        DB::beginTransaction();
        try {
            // Set club_id based on user role
            if (in_array($user->role, ['club_admin', 'club_manager', 'club_medical'])) {
                $validated['club_id'] = $user->club_id;
            }

            // Create FIFA Connect ID if provided or generate one
            $fifaConnectId = null;
            if (!empty($validated['fifa_connect_id'])) {
                $fifaConnectId = FifaConnectId::create([
                    'fifa_id' => $validated['fifa_connect_id'],
                    'entity_type' => 'player',
                    'status' => 'active'
                ]);
            } else {
                // Generate FIFA Connect ID
                $fifaConnectId = $this->fifaConnectService->generatePlayerId();
            }

            // Handle player picture upload
            $playerPicturePath = null;
            if ($request->hasFile('player_picture')) {
                $playerPicturePath = $request->file('player_picture')->store('players/pictures', 'public');
            }

            // Create player
            $player = Player::create([
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'date_of_birth' => $validated['date_of_birth'],
                'nationality' => $validated['nationality'],
                'position' => $validated['position'],
                'jersey_number' => $validated['jersey_number'],
                'height' => $validated['height'],
                'weight' => $validated['weight'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'club_id' => $validated['club_id'],
                'fifa_connect_id' => $fifaConnectId->id,
                'player_picture' => $playerPicturePath,
            ]);

            // Handle supporting documents
            $documentFields = [
                'id_document' => 'Pièce d\'identité',
                'medical_certificate' => 'Certificat médical',
                'proof_of_age' => 'Justificatif d\'âge',
            ];
            foreach ($documentFields as $field => $title) {
                if ($request->hasFile($field)) {
                    $file = $request->file($field);
                    $path = $file->store('players/documents', 'public');
                    $player->documents()->create([
                        'document_type' => $field,
                        'title' => $title,
                        'file_path' => $path,
                        'file_name' => $file->getClientOriginalName(),
                        'file_type' => $file->extension(),
                        'file_size' => $file->getSize(),
                        'mime_type' => $file->getMimeType(),
                        'is_private' => true,
                        'status' => 'active',
                    ]);
                }
            }

            // Create a pending PlayerLicense for association review
            $license = \App\Models\PlayerLicense::create([
                'player_id' => $player->id,
                'club_id' => $player->club_id,
                'status' => 'pending',
                'requested_by' => $user->id,
                'license_type' => 'standard', // or use a value from the form if available
            ]);

            // Notify association agents (stub)
            // TODO: Implement actual notification logic
            // $associationAgents = User::where('association_id', $player->club->association_id)->whereIn('role', ['association_admin', 'association_registrar'])->get();
            // Notification::send($associationAgents, new PlayerLicenseSubmittedNotification($license));

            // Notify club (stub)
            // TODO: Implement actual notification logic for club on approval/rejection
            // Notification::send($user, new PlayerLicenseStatusChangedNotification($license));

            // Broadcast player registered event
            event(new PlayerRegistered($player));

            DB::commit();

            return redirect()->route('player-registration.players.index')
                ->with('success', 'Player registered successfully with FIFA Connect ID: ' . $fifaConnectId->fifa_id);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Failed to register player: ' . $e->getMessage());
        }
    }

    public function storeStakeholder(Request $request)
    {
        $user = Auth::user();
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'role' => 'required|string|max:255',
            'nationality' => 'required|string|max:255',
            'club_id' => 'nullable|exists:clubs,id',
            'association_id' => 'nullable|exists:associations,id',
            'phone' => 'nullable|string|max:20',
            'date_of_birth' => 'nullable|date|before:today',
            'license_number' => 'nullable|string|max:255',
            'license_type' => 'nullable|string|max:255',
            'specialization' => 'nullable|string|max:255',
            'experience_years' => 'nullable|integer|min:0|max:50',
        ]);

        DB::beginTransaction();
        try {
            // Set club_id or association_id based on user role
            if (in_array($user->role, ['club_admin', 'club_manager', 'club_medical'])) {
                $validated['club_id'] = $user->club_id;
            }

            // Create FIFA Connect ID
            $fifaConnectId = FifaConnectId::create([
                'fifa_id' => 'STAKEHOLDER-' . strtoupper(substr($validated['role'], 0, 3)) . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT),
                'entity_type' => 'stakeholder',
                'status' => 'active',
                'metadata' => [
                    'role' => $validated['role'],
                    'created_by' => $user->id
                ]
            ]);

            // Determine permissions based on role
            $permissions = $this->getPermissionsForRole($validated['role']);

            // Create user/stakeholder
            $stakeholder = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make('password123'), // Default password
                'role' => $validated['role'],
                'club_id' => $validated['club_id'],
                'association_id' => $validated['association_id'],
                'fifa_connect_id' => $fifaConnectId->id,
                'permissions' => $permissions,
                'status' => 'active',
                'phone' => $validated['phone'],
                'timezone' => 'UTC',
                'language' => 'en',
            ]);

            // Create player license if applicable
            if (!empty($validated['license_number'])) {
                PlayerLicense::create([
                    'player_id' => null, // Not a player
                    'user_id' => $stakeholder->id,
                    'license_number' => $validated['license_number'],
                    'type' => $validated['license_type'] ?? 'federation',
                    'status' => 'approved',
                    'issued_date' => now(),
                    'expiry_date' => now()->addYears(2),
                    'issued_by' => 'FA',
                    'metadata' => [
                        'specialization' => $validated['specialization'],
                        'experience_years' => $validated['experience_years'],
                        'nationality' => $validated['nationality'],
                    ]
                ]);
            }

            DB::commit();

            return redirect()->route('player-registration.index')
                ->with('success', 'Stakeholder registered successfully: ' . $stakeholder->name);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Failed to register stakeholder: ' . $e->getMessage());
        }
    }

    private function getPermissionsForRole(string $role): array
    {
        $permissionsMap = [
            'referee' => ['match_sheet_management', 'referee_access'],
            'assistant_referee' => ['match_sheet_management'],
            'fourth_official' => ['match_sheet_management'],
            'var_official' => ['match_sheet_management'],
            'match_commissioner' => ['match_sheet_management', 'competition_management_access'],
            'club_admin' => ['club_management', 'player_registration_access', 'license_management'],
            'club_manager' => ['club_management', 'player_registration_access'],
            'club_medical' => ['healthcare_access', 'health_record_management'],
            'association_admin' => ['association_management', 'competition_management_access', 'user_management'],
            'association_registrar' => ['player_registration_access', 'license_management'],
            'association_medical' => ['healthcare_access', 'health_record_management', 'medical_prediction_access'],
            'team_doctor' => ['healthcare_access', 'health_record_management'],
            'physiotherapist' => ['healthcare_access'],
            'sports_scientist' => ['healthcare_access'],
            'system_admin' => ['system_administration', 'user_management'],
        ];

        return $permissionsMap[$role] ?? [];
    }

    public function show(Player $player)
    {
        $this->authorizePlayerAccess($player);
        
        $player->load([
            'club', 
            'association', 
            'fifaConnectId', 
            'healthRecords',
            'teamPlayers.team.club',
            'seasonStats.competition',
            'matchEvents.match',
            'matchEvents.team',
            'matchRosterPlayers.matchRoster.match',
            'matchRosterPlayers.matchRoster.team'
        ]);
        
        return view('modules.player-registration.show', compact('player'));
    }

    public function edit(Player $player)
    {
        $this->authorizePlayerAccess($player);
        
        $user = Auth::user();
        $clubs = collect();

        if (in_array($user->role, ['association_admin', 'association_registrar', 'association_medical'])) {
            $clubs = Club::where('association_id', $user->association_id)
                ->orderBy('name')
                ->get();
        }

        $player->load(['club', 'association', 'fifaConnectId']);
        
        return view('modules.player-registration.edit', compact('player', 'clubs'));
    }

    public function update(Request $request, Player $player)
    {
        $this->authorizePlayerAccess($player);
        
        $user = Auth::user();
        
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'date_of_birth' => 'required|date|before:today',
            'nationality' => 'required|string|max:255',
            'position' => 'required|string|max:255',
            'jersey_number' => 'nullable|integer|min:1|max:99',
            'height' => 'nullable|numeric|min:100|max:250',
            'weight' => 'nullable|numeric|min:30|max:150',
            'email' => ['nullable', 'email', Rule::unique('players')->ignore($player->id)],
            'phone' => 'nullable|string|max:20',
            'club_id' => in_array($user->role, ['association_admin', 'association_registrar', 'association_medical']) ? 'required|exists:clubs,id' : 'nullable',
            'player_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        DB::beginTransaction();
        try {
            // Set club_id based on user role
            if (in_array($user->role, ['club_admin', 'club_manager', 'club_medical'])) {
                $validated['club_id'] = $user->club_id;
            }

            // Handle player picture upload
            if ($request->hasFile('player_picture')) {
                // Delete old picture if exists
                if ($player->player_picture) {
                    \Storage::disk('public')->delete($player->player_picture);
                }
                
                $validated['player_picture'] = $request->file('player_picture')->store('players/pictures', 'public');
            }

            $player->update($validated);

            // Sync with FIFA Connect if needed
            if ($player->fifaConnectId) {
                $this->fifaConnectService->syncPlayer($player);
            }

            DB::commit();

            return redirect()->route('player-registration.players.show', $player)
                ->with('success', 'Player updated successfully');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Failed to update player: ' . $e->getMessage());
        }
    }

    public function destroy(Player $player)
    {
        $this->authorizePlayerAccess($player);
        
        try {
            // Delete player picture if exists
            if ($player->player_picture) {
                \Storage::disk('public')->delete($player->player_picture);
            }
            
            $player->delete();
            return redirect()->route('player-registration.players.index')
                ->with('success', 'Player deleted successfully');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete player: ' . $e->getMessage());
        }
    }

    public function sync(Player $player)
    {
        $this->authorizePlayerAccess($player);
        
        try {
            $result = $this->fifaConnectService->syncPlayer($player);
            return back()->with('success', 'Player synced with FIFA Connect successfully');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to sync player: ' . $e->getMessage());
        }
    }

    public function bulkSync()
    {
        $user = Auth::user();
        $playerIds = [];

        if (in_array($user->role, ['club_admin', 'club_manager', 'club_medical'])) {
            $playerIds = Player::where('club_id', $user->club_id)->pluck('id')->toArray();
        } elseif (in_array($user->role, ['association_admin', 'association_registrar', 'association_medical'])) {
            $playerIds = Player::whereHas('club', function ($query) use ($user) {
                $query->where('association_id', $user->association_id);
            })->pluck('id')->toArray();
        } elseif ($user->role === 'system_admin') {
            $playerIds = Player::pluck('id')->toArray();
        }

        if (empty($playerIds)) {
            return back()->with('info', 'No players found to sync');
        }

        $results = $this->fifaConnectService->bulkSyncPlayers($playerIds);

        $message = "Bulk sync completed: {$results['success']} synced, {$results['failed']} failed";
        
        if (!empty($results['errors'])) {
            $message .= ". Errors: " . implode(', ', array_slice($results['errors'], 0, 3));
            if (count($results['errors']) > 3) {
                $message .= " and " . (count($results['errors']) - 3) . " more";
            }
        }

        return back()->with('success', $message);
    }

    public function export()
    {
        $user = Auth::user();
        $players = collect();

        if (in_array($user->role, ['club_admin', 'club_manager', 'club_medical'])) {
            $players = Player::where('club_id', $user->club_id)
                ->with(['club', 'association', 'fifaConnectId'])
                ->get();
        } elseif (in_array($user->role, ['association_admin', 'association_registrar', 'association_medical'])) {
            $players = Player::whereHas('club', function ($query) use ($user) {
                $query->where('association_id', $user->association_id);
            })
            ->with(['club', 'association', 'fifaConnectId'])
            ->get();
        } elseif ($user->role === 'system_admin') {
            $players = Player::with(['club', 'association', 'fifaConnectId'])->get();
        }

        $filename = 'players_' . date('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($players) {
            $file = fopen('php://output', 'w');
            
            // CSV headers
            fputcsv($file, [
                'FIFA Connect ID', 'First Name', 'Last Name', 'Date of Birth', 
                'Nationality', 'Position', 'Jersey Number', 'Height', 'Weight',
                'Email', 'Phone', 'Club', 'Status', 'Created At'
            ]);

            foreach ($players as $player) {
                fputcsv($file, [
                    $player->fifaConnectId?->fifa_id ?? 'N/A',
                    $player->first_name,
                    $player->last_name,
                    $player->date_of_birth,
                    $player->nationality,
                    $player->position,
                    $player->jersey_number,
                    $player->height,
                    $player->weight,
                    $player->email,
                    $player->phone,
                    $player->club?->name ?? 'N/A',
                    $player->status,
                    $player->created_at
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function healthRecords(Player $player)
    {
        $this->authorizePlayerAccess($player);
        
        // Get health records for the player
        $healthRecords = $player->healthRecords()->orderBy('created_at', 'desc')->get();
        
        return view('modules.player-registration.health-records', compact('player', 'healthRecords'));
    }

    protected function authorizePlayerAccess(Player $player)
    {
        $user = Auth::user();
        
        if ($user->role === 'club' && $player->club_id !== $user->club_id) {
            abort(403, 'Unauthorized access to player');
        }
        
        if ($user->role === 'association') {
            $club = $player->club;
            if (!$club || $club->association_id !== $user->association_id) {
                abort(403, 'Unauthorized access to player');
            }
        }
    }
}
