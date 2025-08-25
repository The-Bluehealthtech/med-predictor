<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Player;
use App\Models\Club;
use App\Models\Association;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;

class StakeholderGalleryController extends Controller
{
    public function __construct()
    {
        // Temporarily disable auth for testing
        // $this->middleware('auth');
    }

    /**
     * Display the stakeholder gallery
     */
    public function index(Request $request): View|JsonResponse
    {
        $user = auth()->user();
        $filter = $request->get('filter', 'all');
        $search = $request->get('search', '');

        // Get all stakeholders based on user permissions
        $stakeholders = $this->getStakeholders($user, $filter, $search);

        // Group stakeholders by type
        $groupedStakeholders = [
            'players' => $stakeholders['players'],
            'staff' => $stakeholders['staff'],
            'officials' => $stakeholders['officials'],
            'medical' => $stakeholders['medical'],
            'admin' => $stakeholders['admin']
        ];

        return view('stakeholder-gallery.index', compact('groupedStakeholders', 'filter', 'search'));
    }

    /**
     * Get stakeholders based on user permissions and filters
     */
    private function getStakeholders($user, $filter, $search)
    {
        $stakeholders = [
            'players' => collect(),
            'staff' => collect(),
            'officials' => collect(),
            'medical' => collect(),
            'admin' => collect()
        ];

        // Get users based on permissions
        $usersQuery = User::query();

        // Apply entity-based filtering - temporarily disabled for testing
        // if ($user->isClubUser()) {
        //     $usersQuery->where('club_id', $user->club_id);
        // } elseif ($user->isAssociationUser()) {
        //     $usersQuery->where('association_id', $user->association_id);
        // } elseif ($user->role === 'player' && $user->club_id) {
        //     // Players can see other users from their club
        //     $usersQuery->where('club_id', $user->club_id);
        // } elseif (!$user->isSystemAdmin()) {
        //     // If not system admin, only show users from same association
        //     $usersQuery->where('association_id', $user->association_id);
        // }

        // Apply search filter
        if ($search) {
            $usersQuery->where(function($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $usersQuery->with(['club', 'association'])->get();

        // Categorize users
        foreach ($users as $userRecord) {
            $stakeholderData = [
                'id' => $userRecord->id,
                'name' => $userRecord->name,
                'email' => $userRecord->email,
                'role' => $userRecord->role,
                'role_display' => $this->getRoleDisplayName($userRecord->role ?? 'unknown'),
                'status' => $userRecord->status,
                'fifa_connect_id' => $userRecord->fifa_connect_id,
                'profile_picture_url' => $userRecord->profile_picture_url,
                'profile_picture_alt' => $userRecord->profile_picture_alt,
                'club' => $userRecord->club,
                'association' => $userRecord->association,
                'type' => 'user',
                'initials' => $this->getInitials($userRecord->name),
                'last_login' => $userRecord->last_login_at
            ];

            // Categorize by role
            if ($userRecord->role === 'player') {
                $stakeholders['players']->push($stakeholderData);
            } elseif (in_array($userRecord->role, ['club_admin', 'club_manager', 'association_admin', 'association_registrar'])) {
                $stakeholders['staff']->push($stakeholderData);
            } elseif (in_array($userRecord->role, ['referee', 'assistant_referee', 'fourth_official', 'var_official', 'match_commissioner'])) {
                $stakeholders['officials']->push($stakeholderData);
            } elseif (in_array($userRecord->role, ['club_medical', 'association_medical', 'team_doctor', 'physiotherapist', 'sports_scientist'])) {
                $stakeholders['medical']->push($stakeholderData);
            } elseif (in_array($userRecord->role, ['system_admin'])) {
                $stakeholders['admin']->push($stakeholderData);
            }
        }

        // Get players if user has access
        if (!$user || $user->hasPermission('player_registration_access') || $user->isSystemAdmin()) {
            $playersQuery = Player::query();

            // Apply entity-based filtering for players - temporarily disabled for testing
            // if ($user->isClubUser()) {
            //     $playersQuery->where('club_id', $user->club_id);
            // } elseif ($user->isAssociationUser()) {
            //     $playersQuery->where('association_id', $user->association_id);
            // } elseif ($user->role === 'player' && $user->club_id) {
            //     // Players can see other players from their club
            //     $playersQuery->where('club_id', $user->club_id);
            // }

            // Apply search filter
            if ($search) {
                $playersQuery->where(function($query) use ($search) {
                    $query->where('first_name', 'like', "%{$search}%")
                          ->orWhere('last_name', 'like', "%{$search}%")
                          ->orWhere('email', 'like', "%{$search}%");
                });
            }

            $players = $playersQuery->with(['club', 'association'])->get();

            foreach ($players as $player) {
                $stakeholderData = [
                    'id' => $player->id,
                    'name' => $player->first_name . ' ' . $player->last_name,
                    'email' => $player->email,
                    'role' => 'player',
                    'role_display' => 'Player',
                    'status' => 'active',
                    'fifa_connect_id' => $player->fifa_connect_id,
                    'profile_picture_url' => $player->player_picture,
                    'profile_picture_alt' => $player->first_name . ' ' . $player->last_name,
                    'club' => $player->club,
                    'association' => $player->association,
                    'type' => 'player',
                    'initials' => $this->getInitials($player->first_name . ' ' . $player->last_name),
                    'position' => $player->position,
                    'nationality' => $player->nationality,
                    'overall_rating' => $player->overall_rating
                ];

                $stakeholders['players']->push($stakeholderData);
            }
        }

        // Apply filter if specified
        if ($filter !== 'all') {
            foreach ($stakeholders as $key => $collection) {
                if ($key !== $filter) {
                    $stakeholders[$key] = collect();
                }
            }
        }

        return $stakeholders;
    }

    /**
     * Get role display name
     */
    private function getRoleDisplayName(string $role): string
    {
        $roleNames = [
            'system_admin' => 'System Administrator',
            'club_admin' => 'Club Administrator',
            'club_manager' => 'Club Manager',
            'club_medical' => 'Club Medical Staff',
            'association_admin' => 'Association Administrator',
            'association_registrar' => 'Association Registrar',
            'association_medical' => 'Association Medical Director',
            'referee' => 'Referee',
            'assistant_referee' => 'Assistant Referee',
            'fourth_official' => 'Fourth Official',
            'var_official' => 'VAR Official',
            'match_commissioner' => 'Match Commissioner',
            'team_doctor' => 'Team Doctor',
            'physiotherapist' => 'Physiotherapist',
            'sports_scientist' => 'Sports Scientist',
            'player' => 'Player'
        ];

        return $roleNames[$role] ?? ucfirst(str_replace('_', ' ', $role));
    }

    /**
     * Get initials from name
     */
    private function getInitials(string $name): string
    {
        $words = explode(' ', trim($name));
        $initials = '';
        
        foreach ($words as $word) {
            if (!empty($word)) {
                $initials .= strtoupper(substr($word, 0, 1));
            }
        }
        
        return substr($initials, 0, 2);
    }
} 