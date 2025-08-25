<?php

namespace App\Http\Controllers;

use App\Models\Player;
use App\Models\PlayerLicense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\PlayerDocument;
use Illuminate\Support\Facades\Storage;
use App\Notifications\LicenseRequestSubmitted;

class PlayerLicenseController extends Controller
{
    public function requestForm(Player $player)
    {
        // Load existing license if any (legacy data)
        $license = PlayerLicense::where('player_id', $player->id)->latest()->first();
        return view('player-licenses.request', compact('player', 'license'));
    }

    public function storeRequest(Request $request, Player $player)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'date_of_birth' => 'required|date',
            'club_id' => 'required|integer|exists:clubs,id',
            'id_document' => 'nullable|file|mimes:pdf,jpg,jpeg,png',
            'medical_certificate' => 'nullable|file|mimes:pdf,jpg,jpeg,png',
            'proof_of_age' => 'nullable|file|mimes:pdf,jpg,jpeg,png',
            // Add other fields as needed
        ]);

        // Update player info
        $player->update([
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'date_of_birth' => $validated['date_of_birth'],
            'club_id' => $validated['club_id'],
            // Add other fields as needed
        ]);

        // Handle file uploads
        $docTypes = [
            'id_document' => 'id_document',
            'medical_certificate' => 'medical_certificate',
            'proof_of_age' => 'proof_of_age',
        ];
        foreach ($docTypes as $input => $type) {
            if ($request->hasFile($input)) {
                $file = $request->file($input);
                $path = $file->store('player_documents', 'public');
                PlayerDocument::updateOrCreate(
                    [ 'player_id' => $player->id, 'document_type' => $type ],
                    [ 'file_path' => $path, 'file_name' => $file->getClientOriginalName() ]
                );
            }
        }

        // Create or update license (set to pending)
        $license = \App\Models\PlayerLicense::updateOrCreate(
            [ 'player_id' => $player->id ],
            [
                'club_id' => $player->club_id,
                'status' => 'pending',
                'requested_by' => auth()->id(),
                // Add other fields as needed
            ]
        );

        // Notify association agents
        $associationId = $player->club ? $player->club->association_id : null;
        if ($associationId) {
            $associationAgents = \App\Models\User::where('association_id', $associationId)
                ->whereIn('role', ['association_admin', 'association_registrar', 'association_medical'])
                ->get();
            foreach ($associationAgents as $agent) {
                $agent->notify(new LicenseRequestSubmitted($license));
            }
        }

        return redirect()->route('players.index')->with('success', 'License request submitted and sent to association for review.');
    }

    public function editRequest(PlayerLicense $license)
    {
        $user = Auth::user();
        $isClubUser = in_array($user->role, ['club_admin', 'club_manager', 'club_medical']);
        $isSystemAdmin = $user->role === 'system_admin';
        if (!$isSystemAdmin && (!$isClubUser || $license->club_id !== $user->club_id)) {
            abort(403);
        }
        if ($license->status !== 'justification_requested') {
            return redirect()->route('club.player-licenses.show', $license->id)->with('error', 'You can only edit requests that require explanation.');
        }
        $player = $license->player;
        return view('player-licenses.request', compact('player', 'license'));
    }

    public function updateRequest(Request $request, PlayerLicense $license)
    {
        $user = Auth::user();
        $isClubUser = in_array($user->role, ['club_admin', 'club_manager', 'club_medical']);
        $isSystemAdmin = $user->role === 'system_admin';
        if (!$isSystemAdmin && (!$isClubUser || $license->club_id !== $user->club_id)) {
            abort(403);
        }
        if ($license->status !== 'justification_requested') {
            return redirect()->route('club.player-licenses.show', $license->id)->with('error', 'You can only edit requests that require explanation.');
        }
        $player = $license->player;
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'date_of_birth' => 'required|date',
            'club_id' => 'required|integer|exists:clubs,id',
            'id_document' => 'nullable|file|mimes:pdf,jpg,jpeg,png',
            'medical_certificate' => 'nullable|file|mimes:pdf,jpg,jpeg,png',
            'proof_of_age' => 'nullable|file|mimes:pdf,jpg,jpeg,png',
        ]);
        $player->update([
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'date_of_birth' => $validated['date_of_birth'],
            'club_id' => $validated['club_id'],
        ]);
        $docTypes = [
            'id_document' => 'id_document',
            'medical_certificate' => 'medical_certificate',
            'proof_of_age' => 'proof_of_age',
        ];
        foreach ($docTypes as $input => $type) {
            if ($request->hasFile($input)) {
                $file = $request->file($input);
                $path = $file->store('player_documents', 'public');
                \App\Models\PlayerDocument::updateOrCreate(
                    [ 'player_id' => $player->id, 'document_type' => $type ],
                    [ 'file_path' => $path, 'file_name' => $file->getClientOriginalName() ]
                );
            }
        }
        $license->update([
            'club_id' => $player->club_id,
            'status' => 'pending',
            'requested_by' => auth()->id(),
            'rejection_reason' => null,
        ]);
        $associationId = $player->club ? $player->club->association_id : null;
        if ($associationId) {
            $associationAgents = \App\Models\User::where('association_id', $associationId)
                ->whereIn('role', ['association_admin', 'association_registrar', 'association_medical'])
                ->get();
            foreach ($associationAgents as $agent) {
                $agent->notify(new \App\Notifications\LicenseRequestSubmitted($license));
            }
        }
        return redirect()->route('club.player-licenses.show', $license->id)->with('success', 'License request updated and resubmitted for review.');
    }
} 