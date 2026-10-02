<?php

namespace App\Http\Controllers;

use App\Models\Club;
use App\Models\Player;
use App\Models\PlayerLicense;
use App\Models\User;
use App\Services\Modules\ModuleCatalog;
use App\Services\Passports\PassportAccess;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

/**
 * Annuaire des joueurs (/modules/players) : recherche et filtres combinables,
 * périmètre selon le compte, et pour chaque joueur les seules actions qu'il
 * peut réellement faire.
 */
class PlayerDirectoryController extends Controller
{
    public const LINES = ['GK' => 'Gardiens', 'DEF' => 'Défenseurs', 'MID' => 'Milieux', 'FWD' => 'Attaquants'];

    /** Statut de la dernière licence (table player_licenses) : clé de filtre => [libellé, statuts en base]. */
    public const LICENSES = [
        'active' => ['Active', ['active']],
        'pending' => ['En attente', ['pending', 'justification_requested']],
        'expired' => ['Expirée', ['expired']],
        'suspended' => ['Suspendue ou révoquée', ['suspended', 'revoked']],
        'none' => ['Sans licence', []],
    ];

    public const SORTS = ['name' => 'Nom (A → Z)', 'club' => 'Club', 'recent' => 'Derniers ajoutés'];

    private const EDITORS = ['system_admin', 'admin', 'super_admin', 'association_admin', 'association_registrar', 'club_admin', 'club_manager'];

    public function __construct(private readonly ModuleCatalog $catalog, private readonly PassportAccess $passports)
    {
    }

    public function index(Request $request)
    {
        $user = $request->user();
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'club_id' => ['nullable', 'integer'],
            'line' => ['nullable', 'in:' . implode(',', array_keys(self::LINES))],
            'nationality' => ['nullable', 'string', 'max:100'],
            'license' => ['nullable', 'in:' . implode(',', array_keys(self::LICENSES))],
            'sort' => ['nullable', 'in:' . implode(',', array_keys(self::SORTS))],
        ]);
        $filters['sort'] ??= 'name';

        $scope = $this->scope($user);
        $query = $this->filtered(clone $scope, $filters);
        $this->sort($query, $filters['sort']);

        $players = $query->with(['club:id,name,city', 'licenses' => fn ($l) => $l->orderByDesc('id')])
            ->paginate(25)->withQueryString();

        // Compteurs par statut de licence, pour les autres filtres en cours (puces cliquables).
        $withoutLicense = $this->filtered(clone $scope, array_merge($filters, ['license' => null]));
        $licenseCounts = ['all' => (clone $withoutLicense)->count()];
        foreach (array_keys(self::LICENSES) as $status) {
            $licenseCounts[$status] = $this->license(clone $withoutLicense, $status)->count();
        }

        $clubIds = (clone $scope)->whereNotNull('club_id')->distinct()->pluck('club_id');

        return view('modules.players.index', [
            'players' => $players,
            'filters' => $filters,
            'scopeTotal' => (clone $scope)->count(),
            'licenseCounts' => $licenseCounts,
            'clubs' => Club::query()->whereIn('id', $clubIds)->orderBy('name')->get(['id', 'name']),
            'nationalities' => (clone $scope)->whereNotNull('nationality')->where('nationality', '!=', '')
                ->distinct()->orderBy('nationality')->pluck('nationality'),
            'actions' => $players->getCollection()->mapWithKeys(fn (Player $p) => [$p->id => $this->actions($user, $p)])->all(),
            'canCreate' => in_array($user->role, self::EDITORS, true) && Route::has('player-registration.create'),
        ]);
    }

    /** Joueurs visibles : tous pour l'admin, sinon ceux de la fédération ou du club du compte. */
    private function scope(User $user): Builder
    {
        $query = Player::query();
        if (in_array($user->role, ['system_admin', 'admin', 'super_admin'], true)) {
            return $query;
        }
        if ($user->isAssociationUser() && $user->association_id) {
            return $query->whereIn('club_id', Club::query()->where('association_id', $user->association_id)->select('id'));
        }
        if ($user->club_id) {
            return $query->where('club_id', $user->club_id);
        }

        return $query->whereRaw('1 = 0');
    }

    private function filtered(Builder $query, array $filters): Builder
    {
        if ($term = trim((string) ($filters['q'] ?? ''))) {
            $like = '%' . mb_strtolower($term) . '%';
            $query->where(fn ($q) => $q->whereRaw('LOWER(first_name) LIKE ?', [$like])->orWhereRaw('LOWER(last_name) LIKE ?', [$like])
                ->orWhereRaw('LOWER(name) LIKE ?', [$like])->orWhereRaw("LOWER(COALESCE(fifa_connect_id, '')) LIKE ?", [$like]));
        }
        if (!empty($filters['club_id'])) {
            $query->where('club_id', (int) $filters['club_id']);
        }
        if (!empty($filters['line'])) {
            // Poste générique (GK, DEF, MID, FWD) ou poste détaillé rattaché à cette ligne.
            $codes = DB::table('position_catalog')->where('broad_group', $filters['line'])->pluck('code')->push($filters['line'])->all();
            $query->whereIn('position', $codes);
        }
        if (!empty($filters['nationality'])) {
            $query->where('nationality', $filters['nationality']);
        }
        if (!empty($filters['license'])) {
            $this->license($query, $filters['license']);
        }

        return $query;
    }

    /** Statut de la licence la plus récente du joueur. */
    private function license(Builder $query, string $status): Builder
    {
        if ($status === 'none') {
            return $query->whereDoesntHave('licenses');
        }
        $table = (new PlayerLicense)->getTable();
        $latest = DB::table("{$table} as l2")->selectRaw('MAX(l2.id)')->whereColumn('l2.player_id', "{$table}.player_id");

        return $query->whereHas('licenses', fn ($l) => $l->whereIn("{$table}.status", self::LICENSES[$status][1])->where("{$table}.id", $latest));
    }

    private function sort(Builder $query, string $sort): void
    {
        match ($sort) {
            'club' => $query->orderBy(Club::query()->select('name')->whereColumn('clubs.id', 'players.club_id')->limit(1))->orderBy('last_name'),
            'recent' => $query->orderByDesc('players.created_at')->orderByDesc('players.id'),
            default => $query->orderBy('last_name')->orderBy('first_name'),
        };
    }

    /** Actions réellement disponibles pour ce joueur et ce compte, avec un libellé explicite. */
    private function actions(User $user, Player $player): array
    {
        $actions = [];
        $add = function (string $route, $params, string $label, string $icon) use (&$actions) {
            if (Route::has($route)) {
                $actions[] = ['url' => route($route, $params), 'label' => $label, 'icon' => $icon];
            }
        };
        if (in_array($user->role, self::EDITORS, true)) {
            $add('players.edit', $player, 'Modifier la fiche', 'edit');
        }
        $status = $player->licenses->first()?->status;
        if (in_array($user->role, self::EDITORS, true) && !in_array($status, ['active', 'pending', 'justification_requested'], true)) {
            $add('player-licenses.request.create', $player, $status ? 'Renouveler la licence' : 'Demander une licence', 'license');
        }
        if ($this->passports->canViewTransfer($user, $player)) {
            $add('passports.transfer.show', $player->id, 'Passeport de transfert', 'passport');
        }
        if ($this->catalog->isMedical($user)) {
            $add('players.health-records', $player, 'Dossier médical', 'medical');
        }
        if (in_array($user->role, ['system_admin', 'super_admin', 'admin', 'association_admin'], true)) {
            $add('test.portail.joueur.simple', ['player_id' => $player->id], 'Portail joueur FIT', 'portal');
        }

        return $actions;
    }
}
