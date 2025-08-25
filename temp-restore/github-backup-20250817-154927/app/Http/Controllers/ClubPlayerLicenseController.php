<?php

namespace App\Http\Controllers;

use App\Models\PlayerLicense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClubPlayerLicenseController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        if ($user->role === 'system_admin') {
            $licenses = PlayerLicense::with(['player'])
                ->orderBy('created_at', 'desc')
                ->paginate(20);
        } else {
            $clubId = $user->club_id;
            $licenses = PlayerLicense::where('club_id', $clubId)
                ->with(['player'])
                ->orderBy('created_at', 'desc')
                ->paginate(20);
        }
        return view('club.player-licenses.index', compact('licenses'));
    }

    public function show(PlayerLicense $license)
    {
        // Optional: add policy for club access
        $license->load(['player', 'fraudAnalysis']);
        return view('club.player-licenses.show', compact('license'));
    }
} 