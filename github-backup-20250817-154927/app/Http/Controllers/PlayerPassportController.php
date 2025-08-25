<?php

namespace App\Http\Controllers;

use App\Models\Player;
use App\Models\PlayerPassport;
use App\Models\Club;
use Illuminate\Http\Request;

class PlayerPassportController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = PlayerPassport::with(['player.club', 'player.association', 'currentClub']);

        // Apply filters
        if ($request->filled('club_id')) {
            $query->whereHas('player', function ($q) use ($request) {
                $q->where('club_id', $request->club_id);
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $query->whereHas('player', function ($q) use ($request) {
                $q->where('first_name', 'like', '%' . $request->search . '%')
                  ->orWhere('last_name', 'like', '%' . $request->search . '%')
                  ->orWhere('fifa_connect_id', 'like', '%' . $request->search . '%');
            });
        }

        $passports = $query->orderBy('created_at', 'desc')->paginate(15);
        $clubs = Club::orderBy('name')->get();

        return view('player-passports.index', compact('passports', 'clubs'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $players = Player::with('club')->orderBy('first_name')->get();
        return view('player-passports.create', compact('players'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'player_id' => 'required|exists:players,id',
            'passport_number' => 'required|string|max:255|unique:player_passports,passport_number',
            'passport_type' => 'required|in:electronic,physical,temporary',
            'status' => 'required|in:active,suspended,expired,revoked,pending_validation',
            'issue_date' => 'required|date',
            'expiry_date' => 'required|date|after:issue_date',
            'renewal_date' => 'nullable|date|after:issue_date',
            'issuing_authority' => 'required|string|max:255',
            'issuing_country' => 'required|string|max:255',
            'registration_number' => 'nullable|string|max:255',
            'license_type' => 'nullable|in:professional,amateur,youth,international',
            'license_category' => 'nullable|in:A,B,C,D,E',
            'notes' => 'nullable|string',
        ]);

        // Set default values
        $validated['created_by'] = auth()->id();
        $validated['version'] = '1.0';
        $validated['compliance_status'] = 'pending';

        PlayerPassport::create($validated);

        return redirect()->route('player-passports.index')
            ->with('success', 'Passeport joueur créé avec succès.');
    }

    /**
     * Display the specified resource.
     */
    public function show(PlayerPassport $playerPassport)
    {
        $playerPassport->load(['player.club', 'player.association', 'currentClub', 'player.performances']);
        return view('player-passports.show', compact('playerPassport'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PlayerPassport $playerPassport)
    {
        return view('player-passports.edit', compact('playerPassport'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PlayerPassport $playerPassport)
    {
        $validated = $request->validate([
            'passport_number' => 'required|string|max:255',
            'passport_type' => 'required|in:electronic,physical,temporary',
            'status' => 'required|in:active,suspended,expired,revoked,pending_validation',
            'issue_date' => 'required|date',
            'expiry_date' => 'required|date|after:issue_date',
            'issuing_authority' => 'required|string|max:255',
            'issuing_country' => 'required|string|max:255',
        ]);

        $playerPassport->update($validated);

        return redirect()->route('player-passports.index')
            ->with('success', 'Player passport updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PlayerPassport $playerPassport)
    {
        $playerPassport->delete();

        return redirect()->route('player-passports.index')
            ->with('success', 'Player passport deleted successfully.');
    }
}
