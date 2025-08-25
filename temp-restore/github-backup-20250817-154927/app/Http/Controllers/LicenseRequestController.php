<?php

namespace App\Http\Controllers;

use App\Models\LicenseRequest;
use App\Models\LicenseRequestAttachment;
use App\Models\LicenseRequestStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Gate;
use App\Notifications\LicenseRequestStatusChanged;

class LicenseRequestController extends Controller
{
    // Affichage des demandes (club ou association)
    public function index()
    {
        $user = Auth::user();
        if ($user->isClub()) {
            $requests = LicenseRequest::where('club_id', $user->club_id)->with('attachments', 'statuses')->latest()->paginate(15);
        } else if ($user->isAssociation()) {
            $requests = LicenseRequest::with('attachments', 'statuses', 'club', 'player', 'staff')->latest()->paginate(15);
        } else {
            abort(403);
        }
        return view('licenses.requests.index', compact('requests'));
    }

    // Formulaire de création (club)
    public function create()
    {
        $user = Auth::user();
        if (!$user->isClub()) abort(403);
        // Charger joueurs/staff du club si besoin
        return view('licenses.requests.create');
    }

    // Enregistrement d'une demande (club)
    public function store(Request $request)
    {
        $user = Auth::user();
        if (!$user->isClub()) abort(403);
        $validated = $request->validate([
            'player_id' => 'nullable|exists:players,id',
            'staff_id' => 'nullable|exists:staff,id',
            'type' => 'required|string',
            'attachments.*' => 'file|max:4096|mimes:pdf,jpg,jpeg,png',
        ]);
        $licenseRequest = LicenseRequest::create([
            'club_id' => $user->club_id,
            'player_id' => $validated['player_id'] ?? null,
            'staff_id' => $validated['staff_id'] ?? null,
            'type' => $validated['type'],
            'status' => 'pending',
        ]);
        // Historique
        LicenseRequestStatus::create([
            'license_request_id' => $licenseRequest->id,
            'status' => 'pending',
            'user_id' => $user->id,
            'comment' => null,
        ]);
        // Pièces jointes
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('license-requests/'.$licenseRequest->id, 'public');
                LicenseRequestAttachment::create([
                    'license_request_id' => $licenseRequest->id,
                    'file_path' => $path,
                    'type' => $file->getClientOriginalExtension(),
                ]);
            }
        }
        return redirect()->route('license-requests.index')->with('success', 'Demande créée.');
    }

    // Détail d'une demande
    public function show($id)
    {
        $request = LicenseRequest::with('attachments', 'statuses', 'club', 'player', 'staff')->findOrFail($id);
        $user = Auth::user();
        if ($user->isClub() && $request->club_id !== $user->club_id) abort(403);
        if ($user->isAssociation() || $user->isAdmin()) {
            // ok
        } else if ($user->isClub() && $request->club_id === $user->club_id) {
            // ok
        } else {
            abort(403);
        }
        return view('licenses.requests.show', compact('request'));
    }

    // Formulaire d'édition (club, si pending)
    public function edit($id)
    {
        $request = LicenseRequest::findOrFail($id);
        $user = Auth::user();
        if (!$user->isClub() || $request->club_id !== $user->club_id || $request->status !== 'pending') abort(403);
        return view('licenses.requests.edit', compact('request'));
    }

    // Mise à jour (club, si pending)
    public function update(Request $request, $id)
    {
        $licenseRequest = LicenseRequest::findOrFail($id);
        $user = Auth::user();
        if (!$user->isClub() || $licenseRequest->club_id !== $user->club_id || $licenseRequest->status !== 'pending') abort(403);
        $validated = $request->validate([
            'type' => 'required|string',
        ]);
        $licenseRequest->update($validated);
        return redirect()->route('license-requests.show', $licenseRequest->id)->with('success', 'Demande modifiée.');
    }

    // Suppression (club, si pending)
    public function destroy($id)
    {
        $licenseRequest = LicenseRequest::findOrFail($id);
        $user = Auth::user();
        if (!$user->isClub() || $licenseRequest->club_id !== $user->club_id || $licenseRequest->status !== 'pending') abort(403);
        $licenseRequest->delete();
        return redirect()->route('license-requests.index')->with('success', 'Demande supprimée.');
    }

    // Validation (association)
    public function validateRequest(Request $request, $id)
    {
        $licenseRequest = LicenseRequest::findOrFail($id);
        $user = Auth::user();
        if (!$user->isAssociation()) abort(403);
        $validated = $request->validate([
            'comment' => 'nullable|string',
        ]);
        $licenseRequest->update([
            'status' => 'validated',
            'association_comment' => $validated['comment'] ?? null,
        ]);
        LicenseRequestStatus::create([
            'license_request_id' => $licenseRequest->id,
            'status' => 'validated',
            'user_id' => $user->id,
            'comment' => $validated['comment'] ?? null,
        ]);
        // Notification club
        $clubUser = $licenseRequest->club ? $licenseRequest->club->userPrincipal() : null;
        if ($clubUser) {
            $clubUser->notify(new LicenseRequestStatusChanged($licenseRequest, 'validated', $validated['comment'] ?? null));
        }
        return redirect()->route('license-requests.show', $licenseRequest->id)->with('success', 'Demande validée.');
    }

    // Refus (association)
    public function refuseRequest(Request $request, $id)
    {
        $licenseRequest = LicenseRequest::findOrFail($id);
        $user = Auth::user();
        if (!$user->isAssociation()) abort(403);
        $validated = $request->validate([
            'comment' => 'required|string',
        ]);
        $licenseRequest->update([
            'status' => 'refused',
            'association_comment' => $validated['comment'],
        ]);
        LicenseRequestStatus::create([
            'license_request_id' => $licenseRequest->id,
            'status' => 'refused',
            'user_id' => $user->id,
            'comment' => $validated['comment'],
        ]);
        // Notification club
        $clubUser = $licenseRequest->club ? $licenseRequest->club->userPrincipal() : null;
        if ($clubUser) {
            $clubUser->notify(new LicenseRequestStatusChanged($licenseRequest, 'refused', $validated['comment']));
        }
        return redirect()->route('license-requests.show', $licenseRequest->id)->with('success', 'Demande refusée.');
    }

    // Upload pièce jointe (club, si pending)
    public function uploadAttachment(Request $request, $id)
    {
        $licenseRequest = LicenseRequest::findOrFail($id);
        $user = Auth::user();
        if (!$user->isClub() || $licenseRequest->club_id !== $user->club_id || $licenseRequest->status !== 'pending') abort(403);
        $validated = $request->validate([
            'attachment' => 'required|file|max:4096|mimes:pdf,jpg,jpeg,png',
        ]);
        $file = $request->file('attachment');
        $path = $file->store('license-requests/'.$licenseRequest->id, 'public');
        LicenseRequestAttachment::create([
            'license_request_id' => $licenseRequest->id,
            'file_path' => $path,
            'type' => $file->getClientOriginalExtension(),
        ]);
        return back()->with('success', 'Pièce jointe ajoutée.');
    }

    // Téléchargement pièce jointe (club ou association)
    public function downloadAttachment($attachmentId)
    {
        $attachment = LicenseRequestAttachment::findOrFail($attachmentId);
        $request = $attachment->licenseRequest;
        $user = Auth::user();
        if ($user->isClub() && $request->club_id !== $user->club_id) abort(403);
        if ($user->isAssociation() || $user->isAdmin()) {
            // ok
        } else if ($user->isClub() && $request->club_id === $user->club_id) {
            // ok
        } else {
            abort(403);
        }
        return Storage::disk('public')->download($attachment->file_path);
    }
}
