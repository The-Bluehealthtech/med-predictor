<?php

namespace App\Services\Modules;

use App\Models\Appointment;
use App\Models\NationalSelection;
use App\Models\PCMA;
use App\Models\Player;
use App\Models\Transfer;
use App\Models\TUERequest;
use App\Models\User;
use App\Services\Analytics\PlayerFormAnalytics;
use App\Services\Dtn\DtnAccess;
use App\Services\MedicalRecordAccess;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * Ce qui reste à faire à chaque étape des parcours de /modules, pour le compte
 * connecté. Chaque compteur reprend la requête et le périmètre du module qu'il
 * résume, n'est calculé que si le compte voit l'étape, et ne renvoie qu'un nombre.
 */
final class WorkflowCounters
{
    private const MEDICAL_ROLES = ['system_admin', 'association_medical', 'club_medical', 'doctor', 'team_doctor', 'medical_staff'];

    public function __construct(
        private readonly MedicalRecordAccess $medical,
        private readonly DtnAccess $dtn,
        private readonly PlayerFormAnalytics $analytics,
    ) {
    }

    /** @return array<string, array{count:int, label:string, tone:string}> clé de compteur => valeur (seulement si > 0) */
    public function forUser(User $user): array
    {
        return Cache::remember("modules:todo:v1:{$user->id}", now()->addMinute(), fn () => $this->compute($user));
    }

    /** Tous les compteurs qui s'appliquent à ce compte, zéros compris (tableau de bord général). */
    public function allForUser(User $user): array
    {
        return Cache::remember("modules:todo:all:v1:{$user->id}", now()->addMinute(), function () use ($user) {
            $this->keepZeros = true;
            try {
                return $this->compute($user);
            } finally {
                $this->keepZeros = false;
            }
        });
    }

    private bool $keepZeros = false;

    private function compute(User $user): array
    {
        $isMedical = $user->hasAnyRole(self::MEDICAL_ROLES);
        $isSecretary = $user->role === 'secretary';
        $counters = [];

        if ($isMedical || $isSecretary) {
            $this->add($counters, 'clinic_today', 'aujourd\'hui', 'info', fn () => Appointment::query()
                ->whereHas('athlete', fn ($a) => $a->whereIn('player_id', $this->clinicPlayers($user, $isSecretary)->select('players.id')))
                ->whereBetween('appointment_date', [now()->startOfDay(), now()->endOfDay()])->count());
        }
        if ($isMedical) {
            $players = fn () => $this->medical->scopePlayers($user, Player::query())->select('players.id');
            $this->add($counters, 'clinic_waiting', 'en attente', 'action', fn () => Appointment::query()
                ->whereHas('athlete', fn ($a) => $a->whereIn('player_id', $players()))
                ->where('status', 'Enregistré')->count());
            $this->add($counters, 'clinic_aut', 'AUT en préparation', 'action', fn () => TUERequest::query()
                ->whereIn('player_id', $players())->where('status', 'pending')->count());
            $this->add($counters, 'clinic_pcma', 'en attente', 'action', fn () => $this->medical
                ->scope($user, PCMA::query())->where('status', 'pending')->count());
        }

        if ($this->dtn->isDtnSide($user)) {
            $federation = fn () => $this->dtn->scopeFederation(NationalSelection::query(), $user);
            $this->add($counters, 'dtn_waiting_club', 'en attente du club', 'info', fn () => $federation()
                ->where('status', NationalSelection::STATUS_CONVOKED)->count());
            $this->add($counters, 'dtn_returns', 'à rédiger', 'action', fn () => $federation()
                ->whereIn('status', [NationalSelection::STATUS_DEPARTURE_SENT, NationalSelection::STATUS_IN_SELECTION])->count());
        }
        if ($this->dtn->isClubSide($user)) {
            $club = fn () => $this->dtn->scopeClub(NationalSelection::query(), $user);
            $this->add($counters, 'club_departures', 'à préparer', 'action', fn () => $club()
                ->where('status', NationalSelection::STATUS_CONVOKED)->count());
            $this->add($counters, 'club_returns', 'à lire', 'action', fn () => $club()
                ->where('status', NationalSelection::STATUS_RETURN_SENT)->count());
        }

        // Licences : demandes à approuver (fédération) et compléments à fournir (club), même périmètre que leurs pages.
        $licensing = app(\App\Services\Licensing\LicenseWorkflow::class);
        if ($licensing->canApprove($user)) {
            $this->add($counters, 'licences_pending', 'à approuver', 'action', fn () => $licensing->licenses($user)->where('status', 'pending')->count());
        }
        if ($licensing->canRequest($user)) {
            $this->add($counters, 'licences_info_requested', 'compléments à fournir', 'action', fn () => $licensing->licenses($user)->where('status', 'justification_requested')->count());
        }

        // Transferts en attente : même périmètre que la gestion des transferts.
        if ($user->isSystemAdmin() || ($user->isClubUser() && $user->club_id) || ($user->isAssociationUser() && $user->association_id)) {
            $this->add($counters, 'transfers_pending', 'en attente', 'info', fn () => $this->transfers($user)
                ->where('transfer_status', 'pending')->count());
        }

        // Points d'attention de l'analyse des performances, pour le club du compte.
        if ($user->isClubUser() && $user->club_id && !$user->isPlayer()) {
            $this->add($counters, 'perf_alerts', 'points d\'attention', 'action', function () use ($user) {
                $data = $this->analytics->forClub((int) $user->club_id, '10');

                return $data ? collect($data['alerts'])->where('level', 'warning')->count() : 0;
            });
        }

        return $counters;
    }

    private function add(array &$counters, string $key, string $label, string $tone, callable $count): void
    {
        try {
            $value = (int) $count();
        } catch (\Throwable $e) {
            // Un compteur indisponible (table absente, panne) ne bloque jamais la page des modules.
            Log::warning("modules: compteur {$key} indisponible", ['error' => $e->getMessage()]);

            return;
        }
        if ($value > 0 || $this->keepZeros) {
            $counters[$key] = ['count' => $value, 'label' => $label, 'tone' => $tone];
        }
    }

    /** Joueurs visibles par l'accueil médical : le secrétariat suit son club ou sa fédération. */
    private function clinicPlayers(User $user, bool $isSecretary): Builder
    {
        if (!$isSecretary) {
            return $this->medical->scopePlayers($user, Player::query());
        }
        $query = Player::query();
        if ($user->club_id) {
            return $query->where('club_id', $user->club_id);
        }

        return $user->association_id
            ? $query->whereHas('club', fn ($c) => $c->where('association_id', $user->association_id))
            : $query->whereRaw('1 = 0');
    }

    private function transfers(User $user): Builder
    {
        $query = Transfer::query();
        if ($user->isSystemAdmin()) {
            return $query;
        }
        if ($user->isClubUser()) {
            return $query->where(fn ($q) => $q->where('club_origin_id', $user->club_id)->orWhere('club_destination_id', $user->club_id));
        }

        return $query->where(fn ($q) => $q
            ->whereHas('clubOrigin', fn ($c) => $c->where('association_id', $user->association_id))
            ->orWhereHas('clubDestination', fn ($c) => $c->where('association_id', $user->association_id)));
    }
}
