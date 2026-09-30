<?php

namespace App\Http\Controllers;

use App\Models\Player;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/** Les pages personnelles utilisent exclusivement le joueur lié au compte connecté. */
final class PlayerPortalAccountController extends Controller
{
    private function player(Request $request): Player
    {
        $user = $request->user();
        abort_unless($user && $user->isPlayer() && $user->player_id, 403);
        return Player::withoutGlobalScopes()->findOrFail($user->player_id);
    }

    public function profile(Request $request)
    {
        return view('player-portal.contact-profile', ['player' => $this->player($request)]);
    }

    public function updateProfile(Request $request)
    {
        $player = $this->player($request);
        // Les identités officielles, rattachements et rôles ne sont pas modifiables ici.
        $data = $request->validate([
            'address' => 'sometimes|nullable|string|max:1000',
            'contact_email' => 'sometimes|nullable|email|max:255',
            'contact_phone' => 'sometimes|nullable|string|max:50',
        ]);
        DB::transaction(fn () => $player->update($data));
        return $request->expectsJson()
            ? response()->json(['success' => true, 'data' => $player->only(array_keys($data))])
            : redirect()->route('player-portal.profile')->with('success', __('Profil mis à jour'));
    }

    public function performances(Request $request) { return $this->portal($request); }
    public function matches(Request $request) { return $this->portal($request); }
    public function fifaUltimateDashboard(Request $request) { return $this->portal($request); }

    private function portal(Request $request)
    {
        return redirect()->route('test.portail.joueur.simple', ['player_id' => $this->player($request)->id]);
    }

    public function predictions(Request $request)
    {
        $player = $this->player($request);
        return view('player-portal.record-list', ['title' => __('navigation.medical_predictions'),
            'player' => $player, 'records' => $player->medicalPredictions()->orderByDesc('prediction_date')->get()
                ->map(fn ($row) => ['date' => $row->prediction_date, 'label' => $row->prediction_type,
                    'status' => $row->status, 'description' => $row->recommendations])]);
    }

    public function documents(Request $request)
    {
        $player = $this->player($request);
        return view('player-portal.record-list', ['title' => __('Documents'), 'player' => $player,
            'records' => DB::table('player_documents')->where('player_id', $player->id)->orderByDesc('created_at')->get()
                ->map(fn ($row) => ['date' => $row->created_at, 'label' => $row->title,
                    'status' => $row->status, 'description' => $row->description])]);
    }

    public function settings(Request $request)
    {
        $this->player($request);
        return view('modules.profile.show', ['user' => $request->user()]);
    }
}
