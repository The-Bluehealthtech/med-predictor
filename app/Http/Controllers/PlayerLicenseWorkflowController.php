<?php

namespace App\Http\Controllers;

use App\Models\ClubOfficial;
use App\Models\Player;
use App\Models\PlayerLicense;
use App\Services\Licensing\LicenseWorkflow;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use InvalidArgumentException;

/**
 * Côté club : demande de licence (choisir un joueur, remplir, envoyer à la
 * fédération) et suivi des demandes, dont les compléments demandés.
 */
class PlayerLicenseWorkflowController extends Controller
{
    public function __construct(private readonly LicenseWorkflow $workflow)
    {
    }

    public function index(Request $request): View
    {
        $user = Auth::user();
        abort_unless($user && $this->workflow->canRequest($user), 403);

        $search = trim((string) $request->query('q', ''));
        $requests = $this->workflow->licenses($user)->with(['player:id,first_name,last_name,name', 'clubOfficial', 'club:id,name'])->withCount('documents')
            ->orderByRaw("CASE status WHEN 'justification_requested' THEN 0 WHEN 'pending' THEN 1 ELSE 2 END")->orderByDesc('updated_at')
            ->limit(100)->get();

        // Joueurs sans licence active ni demande en cours : ceux pour qui une demande est à déposer.
        $players = $this->workflow->requestablePlayers($user)->with('club:id,name')
            ->whereDoesntHave('licenses', fn ($l) => $l->whereIn('status', ['active', 'pending', 'justification_requested']))
            ->when($search !== '', fn ($q) => $q->where(fn ($w) => $w->whereRaw('LOWER(first_name) LIKE ?', ['%' . mb_strtolower($search) . '%'])
                ->orWhereRaw('LOWER(last_name) LIKE ?', ['%' . mb_strtolower($search) . '%'])))
            ->orderBy('last_name')->orderBy('first_name')->paginate(15)->withQueryString();

        // Officiels et dirigeants sans licence active ni demande en cours pour la saison en cours.
        $scale = app(\App\Services\Licensing\LicenseScale::class);
        $season = $scale->season($scale->settings($user->association_id ?: $scale->associationOfClub($user->club_id)))['label'];
        $officials = $this->workflow->requestableOfficials($user)->where('status', 'active')
            ->whereNotIn('id', PlayerLicense::query()->whereNotNull('club_official_id')->where('season', $season)
                ->whereIn('status', ['active', 'pending', 'justification_requested'])->select('club_official_id'))
            ->orderBy('international_last_name')->limit(50)->get();

        return view('modules.licenses.index', [
            'officials' => $officials,
            'season' => $season,
            'requests' => $requests,
            'players' => $players,
            'search' => $search,
            'counts' => [
                'info' => $requests->where('status', 'justification_requested')->count(),
                'pending' => $requests->where('status', 'pending')->count(),
                'active' => $this->workflow->licenses($user)->where('status', 'active')->count(),
            ],
            'canApprove' => $this->workflow->canApprove($user),
        ]);
    }

    public function create(Player $player): View
    {
        $this->authorizePlayer($player);
        $scale = app(\App\Services\Licensing\LicenseScale::class);
        $settings = $scale->settings($scale->associationOfClub($player->club_id));
        $disciplines = config('licensing.disciplines');
        $levels = config('licensing.levels');

        return view('licenses.player-request', [
            'player' => $player,
            'seasons' => $scale->selectableSeasons($settings),
            'documents' => config('licensing.documents'),
            // Règles du barème pour ce joueur : [genre][saison][discipline][niveau] (catégorie, tarif, PCMA, pièces).
            // Genre non renseigné : règles des deux barèmes, le formulaire suit le genre choisi.
            'rules' => collect($player->gender ? [$player->gender] : array_keys(config('licensing.genders')))->mapWithKeys(function ($gender) use ($player, $scale, $settings, $disciplines, $levels) {
                $person = (clone $player)->forceFill(['gender' => $gender]);

                return [$gender => collect($scale->selectableSeasons($settings))->map(fn ($season) => collect($disciplines)->map(fn ($l, $d) => collect($levels)
                    ->map(fn ($ll, $level) => $scale->playerRules($person, $d, $level, 'Registration', $season))->all())->all())->all()];
            })->all(),
            'pcmaStatus' => app(\App\Services\Licensing\PcmaRequirement::class)->status($player),
            // Motifs possibles selon l'historique du joueur : [discipline][niveau][motif] => allowed, why.
            'reasons' => collect($disciplines)->map(fn ($l, $d) => collect($levels)->map(fn ($ll, $level) => collect($this->workflow->reasonsFor($player, $d, $level))
                ->map(fn ($r) => ['allowed' => $r['allowed'], 'why' => $r['why']])->all())->all())->all(),
        ]);
    }

    public function store(Request $request, Player $player)
    {
        $this->authorizePlayer($player);
        $validated = $request->validate([
            'discipline' => 'required|in:' . implode(',', array_keys(config('licensing.disciplines'))),
            'level' => 'required|in:' . implode(',', array_keys(config('licensing.levels'))),
            'request_reason' => 'required|in:' . implode(',', array_keys(config('licensing.request_reasons'))),
            'season' => 'required|string|max:9',
            'gender' => ($player->gender ? 'nullable' : 'required') . '|in:' . implode(',', array_keys(config('licensing.genders'))),
            'notes' => 'nullable|string|max:2000',
        ] + $this->documentRules());

        try {
            $license = $this->workflow->submitPlayer($player, Auth::user(), $validated, $request->file('documents', []));
        } catch (InvalidArgumentException $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }

        return redirect()->route('player-licenses.show', $license)
            ->with('success', 'Demande envoyée à la fédération pour ' . trim($player->first_name . ' ' . $player->last_name) . '. Vous serez notifié de sa décision.');
    }

    /** Licence d'un officiel d'équipe ou d'un dirigeant (fiche « Dirigeants et staff »). */
    public function createOfficial(ClubOfficial $official): View
    {
        $this->authorizeOfficial($official);
        $scale = app(\App\Services\Licensing\LicenseScale::class);
        $settings = $scale->settings($scale->associationOfClub($official->club_id));

        return view('licenses.official-request', [
            'official' => $official,
            'seasons' => $scale->selectableSeasons($settings),
            'rules' => $scale->officialRules($official),
            'documents' => config('licensing.documents'),
        ]);
    }

    public function storeOfficial(Request $request, ClubOfficial $official)
    {
        $this->authorizeOfficial($official);
        $validated = $request->validate([
            'discipline' => 'required|in:' . implode(',', array_keys(config('licensing.disciplines'))),
            'season' => 'required|string|max:9',
            'notes' => 'nullable|string|max:2000',
        ] + $this->documentRules());

        try {
            $license = $this->workflow->submitOfficial($official, Auth::user(), $validated, $request->file('documents', []));
        } catch (InvalidArgumentException $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }

        return redirect()->route('player-licenses.show', $license)->with('success', 'Demande de licence envoyée à la fédération.');
    }

    /** Suivi d'une demande côté club : historique, pièces, complément à fournir. */
    public function show(PlayerLicense $license): View
    {
        $this->authorizeLicense($license);
        $license->load(['player.club', 'clubOfficial', 'club', 'documents:id,player_license_id,document_type,original_name,size,created_at', 'events.user:id,name']);

        return view('licenses.request-show', [
            'license' => $license,
            'missing' => $this->workflow->missingDocuments($license),
            'required' => $this->workflow->requiredDocuments($license),

            'pcma' => app(\App\Services\Licensing\PcmaRequirement::class)->check($license),
        ]);
    }

    /** Ajout de pièces justificatives tant que la demande n'est pas tranchée. */
    public function addDocuments(Request $request, PlayerLicense $license)
    {
        $this->authorizeLicense($license);
        $request->validate($this->documentRules());
        try {
            $count = $this->workflow->addDocuments($license, Auth::user(), $request->file('documents', []));
        } catch (InvalidArgumentException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with($count ? 'success' : 'error', $count ? "{$count} pièce(s) ajoutée(s)." : 'Aucun fichier reçu.');
    }

    /** Le club complète sa demande (message et pièces) ; elle repart à la fédération. */
    public function respond(Request $request, PlayerLicense $license)
    {
        $this->authorizeLicense($license);
        $data = $request->validate(['club_response' => 'nullable|string|max:2000'] + $this->documentRules());

        try {
            $this->workflow->respond($license, Auth::user(), (string) ($data['club_response'] ?? ''), $request->file('documents', []));
        } catch (InvalidArgumentException $e) {
            return back()->with('error', $e->getMessage());
        }

        return redirect()->route('player-licenses.show', $license)->with('success', 'Complément envoyé : la demande repart à la fédération, qui est notifiée.');
    }

    private function documentRules(): array
    {
        return [
            'documents' => 'nullable|array',
            'documents.*' => 'nullable|file|mimes:' . implode(',', config('licensing.mimes')) . '|max:' . config('licensing.max_kilobytes'),
        ];
    }

    private function authorizeLicense(PlayerLicense $license): void
    {
        $user = Auth::user();
        abort_unless($user && $this->workflow->canRequest($user) && $this->workflow->canAccess($user, $license), 403);
    }

    private function authorizeOfficial(ClubOfficial $official): void
    {
        $user = Auth::user();
        abort_unless($user && $this->workflow->canRequest($user)
            && $this->workflow->requestableOfficials($user)->whereKey($official->getKey())->exists(), 403);
    }

    private function authorizePlayer(Player $player): void
    {
        $user = Auth::user();
        abort_unless($user && $this->workflow->canRequest($user)
            && $this->workflow->requestablePlayers($user)->whereKey($player->getKey())->exists(), 403);
    }
}
