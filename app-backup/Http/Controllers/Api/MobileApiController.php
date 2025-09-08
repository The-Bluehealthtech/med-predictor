<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\GameMatch;
use App\Models\Player;
use App\Models\Club;
use App\Models\User;
use App\Models\MatchOfficial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class MobileApiController extends Controller
{
    /**
     * Authentification mobile
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
            'device_token' => 'nullable|string'
        ]);

        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            $token = $user->createToken('mobile-app')->plainTextToken;

            return response()->json([
                'success' => true,
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role
                ],
                'token' => $token
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Identifiants invalides'
        ], 401);
    }

    /**
     * Dashboard mobile pour arbitres
     */
    public function refereeDashboard(Request $request)
    {
        $refereeId = $request->user()->id;

        $stats = [
            'total_assignments' => MatchOfficial::where('user_id', $refereeId)->count(),
            'upcoming_matches' => MatchOfficial::where('user_id', $refereeId)
                ->whereHas('match', function ($query) {
                    $query->where('match_date', '>=', now());
                })->count()
        ];

        return response()->json([
            'success' => true,
            'stats' => $stats
        ]);
    }

    /**
     * Calendrier mobile
     */
    public function calendar(Request $request)
    {
        $userId = $request->user()->id;
        $userRole = $request->user()->role;

        $events = collect();

        if ($userRole === 'referee') {
            $assignments = MatchOfficial::with(['match.homeTeam', 'match.awayTeam'])
                ->where('user_id', $userId)
                ->whereHas('match', function ($query) {
                    $query->where('match_date', '>=', now()->startOfMonth())
                          ->where('match_date', '<=', now()->endOfMonth());
                })
                ->get();

            $events = $assignments->map(function ($assignment) {
                return [
                    'id' => $assignment->id,
                    'title' => $assignment->match->homeTeam->name . ' vs ' . $assignment->match->awayTeam->name,
                    'start' => $assignment->match->match_date,
                    'type' => 'match',
                    'role' => $assignment->role
                ];
            });
        }

        return response()->json([
            'success' => true,
            'events' => $events
        ]);
    }
}
use Carbon\Carbon;

class MobileApiController extends Controller
{
    /**
     * Authentification mobile
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
            'device_token' => 'nullable|string'
        ]);

        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            $token = $user->createToken('mobile-app')->plainTextToken;

            return response()->json([
                'success' => true,
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role
                ],
                'token' => $token
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Identifiants invalides'
        ], 401);
    }

    /**
     * Dashboard mobile pour arbitres
     */
    public function refereeDashboard(Request $request)
    {
        $refereeId = $request->user()->id;

        $stats = [
            'total_assignments' => MatchOfficial::where('user_id', $refereeId)->count(),
            'upcoming_matches' => MatchOfficial::where('user_id', $refereeId)
                ->whereHas('match', function ($query) {
                    $query->where('match_date', '>=', now());
                })->count()
        ];

        return response()->json([
            'success' => true,
            'stats' => $stats
        ]);
    }

    /**
     * Calendrier mobile
     */
    public function calendar(Request $request)
    {
        $userId = $request->user()->id;
        $userRole = $request->user()->role;

        $events = collect();

        if ($userRole === 'referee') {
            $assignments = MatchOfficial::with(['match.homeTeam', 'match.awayTeam'])
                ->where('user_id', $userId)
                ->whereHas('match', function ($query) {
                    $query->where('match_date', '>=', now()->startOfMonth())
                          ->where('match_date', '<=', now()->endOfMonth());
                })
                ->get();

            $events = $assignments->map(function ($assignment) {
                return [
                    'id' => $assignment->id,
                    'title' => $assignment->match->homeTeam->name . ' vs ' . $assignment->match->awayTeam->name,
                    'start' => $assignment->match->match_date,
                    'type' => 'match',
                    'role' => $assignment->role
                ];
            });
        }
        return response()->json([
            'success' => true,
            'events' => $events
        ]);
    }
}
