<?php
namespace App\Services;
use App\Models\{Athlete, PCMA, Player, User};
use Illuminate\Database\Eloquent\Builder;

// Même périmètre médical que l'API existante ; TeamDoctor est un médecin signataire.
final class MedicalRecordAccess
{
    public function authorizeRole(?User $user): void
    {
        abort_unless($user, 401);
        abort_unless($user->hasAnyRole(['system_admin', 'association_medical',
            'club_medical', 'doctor', 'team_doctor', 'medical_staff']), 403);
    }
    public function scopePlayers(User $user, Builder $query): Builder
    {
        $this->authorizeRole($user);
        if ($user->isSystemAdmin()) return $query;
        return $query->whereHas('club', fn ($club) => $this->scopeClubs($user, $club));
    }
    private function scopeClubs(User $user, Builder $query): Builder
    {
        return $query->where(function ($club) use ($user) {
            $club->whereRaw('1 = 0');
            if ($user->club_id) $club->orWhere('id', $user->club_id);
            if ($user->association_id) $club->orWhere('association_id', $user->association_id);
        });
    }
    public function scope(User $user, Builder $query): Builder
    {
        $this->authorizeRole($user);
        if ($user->isSystemAdmin()) return $query;
        return $query->where(function ($records) use ($user) {
            $records->whereHas('player', fn ($p) => $this->scopePlayers($user, $p))
                ->orWhere(function ($legacy) use ($user) {
                    $legacy->whereNull('player_id')->whereHas('athlete.team.club',
                        fn ($club) => $this->scopeClubs($user, $club));
                });
        });
    }
    public function authorize(User $user, ?Player $player, ?Athlete $athlete): void
    {
        $this->authorizeRole($user);
        if ($user->isSystemAdmin()) return;
        $club = $player?->club ?? $athlete?->team?->club;
        abort_unless($club && (($user->club_id && (int) $user->club_id === (int) $club->id)
            || ($user->association_id && (int) $user->association_id === (int) $club->association_id)), 403);
    }
    public function record(User $user, PCMA $pcma, bool $mutable = false): void
    {
        $this->authorize($user, $pcma->player, $pcma->athlete);
        if ($mutable) abort_if($pcma->is_signed, 409, 'Un PCMA signé ne peut plus être modifié.');
    }
    public function input(User $user, array $data): array
    {
        $player = isset($data['player_id']) ? Player::findOrFail($data['player_id']) : null;
        $athlete = isset($data['athlete_id']) ? Athlete::findOrFail($data['athlete_id']) : null;
        abort_unless($player || $athlete, 422, 'Sélectionnez un joueur.');
        abort_if($player && $athlete && (int) $athlete->player_id !== (int) $player->id,
            422, 'Les identifiants du joueur sont incohérents.');
        $this->authorize($user, $player, $athlete);
        abort_unless((int) ($data['assessor_id'] ?? 0) === (int) $user->id, 403,
            'Le médecin évaluateur doit être l’utilisateur connecté.');
        return $data;
    }
}
