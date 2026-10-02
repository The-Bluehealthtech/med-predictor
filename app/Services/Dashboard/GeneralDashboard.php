<?php

namespace App\Services\Dashboard;

use App\Models\PCMA;
use App\Models\User;
use App\Services\Dtn\DtnAccess;
use App\Services\MedicalRecordAccess;
use App\Services\Modules\ModuleCatalog;
use App\Services\Modules\WorkflowCounters;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;

/**
 * Tableau de bord général affiché après la connexion : indicateurs clés, cartes
 * par domaine, actions à traiter, modules et journal d'activité, dans le
 * périmètre et selon les droits du compte.
 *
 * Garde-fous : aucune valeur estimée. Un indicateur sans source réelle vaut
 * null (« Non disponible ») ; les courbes d'activité n'apparaissent qu'avec
 * 30 jours d'historique réel ; aucune tendance n'est simulée.
 */
final class GeneralDashboard
{
    public const HISTORY_DAYS = 30;

    /** Compteur « à faire » => [domaine, libellé, route de destination]. */
    private const TODO = [
        'clinic_today' => ['clinique', 'Rendez-vous du jour', 'secretary.dashboard'],
        'clinic_waiting' => ['clinique', 'Joueurs en attente de prise en charge', 'modules.medical.index'],
        'clinic_aut' => ['clinique', 'AUT en préparation', 'modules.healthcare.index'],
        'clinic_pcma' => ['clinique', 'Bilans PCMA en attente', 'pcma.index'],
        'perf_alerts' => ['performance', 'Points d\'attention sur la forme des joueurs', 'performances.analytics'],
        'dtn_waiting_club' => ['selections', 'Convocations en attente de réponse du club', 'dtn.index'],
        'dtn_returns' => ['selections', 'Rapports de retour à rédiger', 'dtn.index'],
        'club_departures' => ['selections', 'Départs en sélection à préparer', 'club.selections.index'],
        'club_returns' => ['selections', 'Rapports de retour à lire', 'club.selections.returns'],
        'licences_pending' => ['administration', 'Demandes de licence à approuver', 'licenses.validation'],
        'licences_info_requested' => ['administration', 'Compléments de licence à fournir', 'modules.licenses.index'],
        'transfers_pending' => ['administration', 'Transferts en attente', 'admin.transfer-management.index'],
    ];

    public function __construct(
        private readonly ModuleCatalog $catalog,
        private readonly WorkflowCounters $counters,
        private readonly DtnAccess $dtn,
        private readonly MedicalRecordAccess $medical,
    ) {
    }

    public function forUser(User $user): array
    {
        $domains = $this->visibleDomains($user);
        $todo = $this->counters->allForUser($user);
        $modules = $this->catalog->visibleFor($user);
        $activity = $this->activity($user, $domains);

        return [
            'domains' => $domains,
            'kpis' => $this->kpis($user, $domains, $todo, $modules->count()),
            'cards' => $this->cards($user, $domains, $todo),
            'alerts' => $this->alerts($todo, $domains),
            'modules' => collect(ModuleCatalog::SECTIONS)->keys()
                ->mapWithKeys(fn ($key) => [$key => $modules->where('category', $key)->count()])
                ->filter()->all(),
            'activity' => $activity,
            'scope' => $this->scopeLabel($user),
        ];
    }

    /** Domaines auxquels le compte a accès, selon les mêmes règles que /modules. */
    public function visibleDomains(User $user): array
    {
        $domains = [];
        foreach (ModuleCatalog::SECTIONS as $key => $label) {
            $visible = match ($key) {
                'clinique' => $this->catalog->isMedical($user) || $user->role === 'secretary',
                'selections' => $this->dtn->isDtnSide($user) || $this->dtn->isClubSide($user),
                default => true,
            };
            if ($visible) {
                $domains[$key] = $label;
            }
        }

        return $domains;
    }

    private function kpis(User $user, array $domains, array $todo, int $moduleCount): array
    {
        $actions = collect($todo)->where('tone', 'action')->sum('count');
        $kpis = [
            ['key' => 'players', 'label' => 'Joueurs actifs', 'value' => $this->safe(fn () => $this->scoped(DB::table('players'), $user)->where('status', 'active')->count()),
                'hint' => $this->scopeLabel($user)],
        ];
        if (isset($domains['clinique']) && $this->catalog->isMedical($user)) {
            $kpis[] = ['key' => 'pcma_pending', 'label' => 'Bilans PCMA en attente', 'value' => $todo['clinic_pcma']['count'] ?? null, 'hint' => 'Dans votre périmètre médical'];
        }
        $kpis[] = ['key' => 'actions', 'label' => 'Actions à traiter', 'value' => $actions, 'hint' => 'Total des points « à faire » de vos modules'];
        $kpis[] = ['key' => 'modules', 'label' => 'Modules disponibles', 'value' => $moduleCount, 'hint' => 'Selon vos droits'];
        $kpis[] = ['key' => 'completeness', 'label' => 'Complétude des dossiers', 'value' => null, 'hint' => 'Règle métier à définir et valider'];
        if (isset($domains['clinique']) && $this->catalog->isMedical($user)) {
            $delay = $this->pcmaDelay($user);
            $kpis[] = ['key' => 'pcma_delay', 'label' => 'Délai moyen PCMA', 'value' => $delay, 'unit' => 'jours',
                'hint' => $delay === null ? 'Aucune date de signature enregistrée sur les bilans' : 'De la création à la signature, 180 derniers jours'];
        }

        return $kpis;
    }

    private function cards(User $user, array $domains, array $todo): array
    {
        $count = fn (string $key) => $todo[$key]['count'] ?? null;
        $c = fn (string $label, string $key) => [$label, $count($key), true];
        $cards = [];
        foreach ($domains as $key => $label) {
            $metrics = match ($key) {
                'clinique' => [
                    $c('Rendez-vous du jour', 'clinic_today'),
                    $c('En attente de prise en charge', 'clinic_waiting'),
                    $c('AUT en préparation', 'clinic_aut'),
                ],
                'performance' => [
                    ['Joueurs avec un score « Rôle et apport »', $this->safe(fn () => $this->scopedPlayers(DB::table('player_role_evaluations as e'), $user, 'e.player_id')->distinct()->count('e.player_id'))],
                    ['Profils de saison importés', $this->safe(fn () => $this->scopedPlayers(DB::table('external_player_performance_metrics as m'), $user, 'm.player_id')
                        ->where('m.metric_name', 'minutes_played')->where('m.score_origin', 'observed')->distinct()->count('m.player_id'))],
                    $c('Points d\'attention sur la forme', 'perf_alerts'),
                ],
                'selections' => $this->dtn->isDtnSide($user) ? [
                    ['Convocations en cours', $this->safe(fn () => $this->scoped(DB::table('national_selections'), $user)->whereNotIn('status', ['closed', 'cancelled'])->count())],
                    $c('En attente de réponse du club', 'dtn_waiting_club'),
                    $c('Rapports de retour à rédiger', 'dtn_returns'),
                ] : [
                    ['Convocations reçues', $this->safe(fn () => $this->scoped(DB::table('national_selections'), $user)->count())],
                    $c('Départs à préparer', 'club_departures'),
                    $c('Rapports de retour à lire', 'club_returns'),
                ],
                'administration' => [
                    ['Clubs', $this->safe(fn () => $this->clubCount($user))],
                    $c('Licences à approuver', 'licences_pending'),
                    $c('Transferts en attente', 'transfers_pending'),
                ],
            };
            // Un compteur qui ne s'applique pas à ce compte (null) est retiré, pas affiché « non disponible ».
            $cards[$key] = ['label' => $label, 'metrics' => array_values(array_filter($metrics, fn ($m) => $m[1] !== null || !($m[2] ?? false)))];
        }

        return $cards;
    }

    /** Actions à traiter (compteurs « à faire » non nuls), domaine par domaine. */
    private function alerts(array $todo, array $domains): array
    {
        $alerts = [];
        foreach (self::TODO as $key => [$domain, $label, $route]) {
            if (!isset($todo[$key], $domains[$domain]) || $todo[$key]['count'] === 0) {
                continue;
            }
            $alerts[] = ['domain' => $domain, 'label' => $label, 'count' => $todo[$key]['count'],
                'level' => $todo[$key]['tone'] === 'action' ? 'todo' : 'info',
                'url' => Route::has($route) ? route($route) : null];
        }
        usort($alerts, fn ($a, $b) => [$a['level'] === 'todo' ? 0 : 1, -$a['count']] <=> [$b['level'] === 'todo' ? 0 : 1, -$b['count']]);

        return $alerts;
    }

    /** Journal : actions récentes, et volumes par jour et par domaine dès 30 jours d'historique. */
    private function activity(User $user, array $domains): array
    {
        $empty = ['ready' => false, 'history_days' => 0, 'recent' => [], 'daily' => [], 'by_domain' => []];
        if (!Schema::hasTable('platform_activities') || $domains === []) {
            return $empty;
        }
        $base = fn () => $this->activityScope(DB::table('platform_activities as a'), $user)->whereIn('a.domain', array_keys($domains));

        $first = $base()->min('a.created_at');
        $historyDays = $first ? (int) Carbon::parse($first)->startOfDay()->diffInDays(now()->startOfDay()) + 1 : 0;
        $recent = $base()->leftJoin('users as u', 'u.id', '=', 'a.user_id')->leftJoin('clubs as c', 'c.id', '=', 'a.club_id')
            ->orderByDesc('a.created_at')->orderByDesc('a.id')->limit(12)
            ->get(['a.domain', 'a.action', 'a.created_at', 'u.name as user_name', 'c.name as club_name'])
            ->map(fn ($r) => ['domain' => $r->domain, 'action' => $r->action, 'at' => Carbon::parse($r->created_at),
                'user' => $r->user_name, 'club' => $r->club_name ? str_replace(' (Démo)', '', $r->club_name) : null])->all();

        $ready = $historyDays >= self::HISTORY_DAYS;
        $daily = $byDomain = [];
        if ($ready) {
            $since = now()->subDays(self::HISTORY_DAYS - 1)->startOfDay();
            $rows = $base()->where('a.created_at', '>=', $since)->get(['a.domain', 'a.created_at']);
            for ($d = 0; $d < self::HISTORY_DAYS; $d++) {
                $daily[$since->copy()->addDays($d)->toDateString()] = 0;
            }
            foreach ($rows as $row) {
                $day = Carbon::parse($row->created_at)->toDateString();
                $daily[$day] = ($daily[$day] ?? 0) + 1;
                $byDomain[$row->domain] = ($byDomain[$row->domain] ?? 0) + 1;
            }
        }

        return ['ready' => $ready, 'history_days' => $historyDays, 'recent' => $recent, 'daily' => $daily, 'by_domain' => $byDomain];
    }

    private function pcmaDelay(User $user): ?float
    {
        return $this->safe(function () use ($user) {
            $rows = PCMA::query()->whereIn('player_id', $this->medical->scopePlayers($user, \App\Models\Player::query())->select('players.id'))
                ->where('created_at', '>=', now()->subDays(180))
                ->where(fn ($q) => $q->whereNotNull('signed_at')->orWhereNotNull('completed_at'))
                ->get(['created_at', 'signed_at', 'completed_at']);
            if ($rows->isEmpty()) {
                return null;
            }

            return round($rows->avg(fn ($r) => Carbon::parse($r->created_at)->diffInHours(Carbon::parse($r->signed_at ?? $r->completed_at)) / 24), 1);
        });
    }

    /** Périmètre : tout pour l'admin système, sinon le club ou la fédération du compte. */
    private function scoped(Builder $query, User $user): Builder
    {
        if ($user->isSystemAdmin()) {
            return $query;
        }
        if ($user->club_id) {
            return $query->where('club_id', $user->club_id);
        }
        if ($user->association_id) {
            return $query->where('association_id', $user->association_id);
        }

        return $query->whereRaw('1 = 0');
    }

    private function scopedPlayers(Builder $query, User $user, string $playerColumn): Builder
    {
        if ($user->isSystemAdmin()) {
            return $query;
        }

        return $query->whereIn($playerColumn, $this->scoped(DB::table('players'), $user)->select('id'));
    }

    private function activityScope(Builder $query, User $user): Builder
    {
        if ($user->isSystemAdmin()) {
            return $query;
        }
        if ($user->club_id) {
            return $query->where('a.club_id', $user->club_id);
        }
        if ($user->association_id) {
            return $query->where('a.association_id', $user->association_id);
        }

        return $query->where('a.user_id', $user->id);
    }

    private function clubCount(User $user): int
    {
        if ($user->isSystemAdmin()) {
            return DB::table('clubs')->count();
        }
        if ($user->club_id) {
            return 1;
        }

        return $user->association_id ? DB::table('clubs')->where('association_id', $user->association_id)->count() : 0;
    }

    private function scopeLabel(User $user): string
    {
        if ($user->isSystemAdmin()) {
            return 'Toute la plateforme';
        }
        if ($user->club_id) {
            return 'Votre club';
        }

        return $user->association_id ? 'Votre fédération' : 'Votre compte';
    }

    /** Un indicateur indisponible (table absente, panne) vaut null et n'empêche jamais l'affichage. */
    private function safe(callable $compute): mixed
    {
        try {
            return $compute();
        } catch (\Throwable $e) {
            Log::warning('tableau de bord : indicateur indisponible', ['error' => $e->getMessage()]);

            return null;
        }
    }
}
