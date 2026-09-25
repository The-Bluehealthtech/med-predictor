<?php

namespace App\Http\Controllers;

use App\Models\Player;
use App\Models\PlayerLicense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class PlayerLicenseWorkflowController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();
        abort_unless($user, 401);

        $players = Player::query()
            ->with(['club', 'association', 'licenses'])
            ->when($user->isClubUser(), function ($query) use ($user) {
                abort_unless($user->club_id, 403);
                $query->where('club_id', $user->club_id);
            })
            ->when($user->isAssociationUser(), function ($query) use ($user) {
                abort_unless($user->association_id, 403);
                $query->where('association_id', $user->association_id);
            });

        abort_unless(
            $user->isSystemAdmin()
            || $user->isClubUser()
            || $user->isAssociationUser(),
            403
        );

        return view('modules.licenses.index', [
            'footballType' => 'association',
            'players' => $players->orderBy('last_name')->get(),
        ]);
    }

    public function create(Player $player): View
    {
        $this->authorizePlayer($player);

        return view('licenses.player-request', compact('player'));
    }

    public function store(Request $request, Player $player)
    {
        $this->authorizePlayer($player);

        $validated = $request->validate([
            'license_type' => 'required|in:amateur,professional,futsal,beach_soccer,youth,international',
            'contract_start_date' => 'required|date',
            'expiry_date' => 'required|date|after:contract_start_date',
            'notes' => 'nullable|string|max:2000',
        ]);

        $existing = PlayerLicense::query()
            ->where('player_id', $player->id)
            ->where('license_type', $validated['license_type'])
            ->whereIn('status', ['pending', 'active'])
            ->exists();

        if ($existing) {
            return back()
                ->withInput()
                ->with('error', 'Une licence active ou en attente existe déjà pour ce joueur et ce type.');
        }

        PlayerLicense::create([
            'player_id' => $player->id,
            'club_id' => $player->club_id,
            'license_type' => $validated['license_type'],
            'status' => 'pending',
            'approval_status' => 'pending',
            'contract_start_date' => $validated['contract_start_date'],
            'contract_end_date' => $validated['expiry_date'],
            'expiry_date' => $validated['expiry_date'],
            'fifa_connect_id' => $player->fifa_connect_id,
            'requested_by' => Auth::id(),
            'notes' => $validated['notes'] ?? null,
        ]);

        return redirect()
            ->route('modules.licenses.index')
            ->with('success', 'Demande de licence créée pour le joueur.');
    }

    private function authorizePlayer(Player $player): void
    {
        $user = Auth::user();
        abort_unless($user, 401);

        if ($user->isSystemAdmin()) {
            return;
        }

        if ($user->isClubUser()) {
            abort_unless(
                $user->club_id
                && (int) $player->club_id === (int) $user->club_id,
                403
            );
            return;
        }

        if ($user->isAssociationUser()) {
            abort_unless(
                $user->association_id
                && (int) $player->association_id === (int) $user->association_id,
                403
            );
            return;
        }

        abort(403);
    }
}
