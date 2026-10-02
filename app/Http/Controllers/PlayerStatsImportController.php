<?php

namespace App\Http\Controllers;

use App\Models\Club;
use App\Services\PlayerStatsImport\PlayerStatisticsFile;
use App\Services\PlayerStatsImport\PlayerStatisticsImporter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Téléversement des exports « Player statistics » des clubs (Excel ou CSV) :
 * reconnaissance automatique du modèle, aperçu (joueurs reconnus ou non),
 * puis enregistrement après confirmation. Rien n'est écrit avant la confirmation.
 */
class PlayerStatsImportController extends Controller
{
    private const DIR = 'player-stats-imports';

    public function __construct(private readonly PlayerStatisticsFile $reader, private readonly PlayerStatisticsImporter $importer)
    {
    }

    public function create(Request $request)
    {
        return view('player-stats-import.create', ['clubs' => $this->clubs($request), 'clubId' => $request->integer('club_id') ?: null]);
    }

    public function preview(Request $request)
    {
        $data = $request->validate([
            'file' => ['required', 'file', 'max:10240'],
            'club_id' => ['nullable', 'integer'],
        ]);
        $upload = $request->file('file');
        $extension = strtolower($upload->getClientOriginalExtension());
        if (!in_array($extension, ['xlsx', 'csv'], true)) {
            return back()->withErrors(['file' => 'Format accepté : Excel (.xlsx) ou CSV.'])->withInput();
        }
        try {
            $file = $this->reader->read($upload->getRealPath(), $upload->getClientOriginalName());
        } catch (\Throwable $e) {
            return back()->withErrors(['file' => 'Fichier illisible : ' . $e->getMessage()])->withInput();
        }

        $clubs = $this->clubs($request);
        $club = isset($data['club_id']) ? $clubs->firstWhere('id', (int) $data['club_id']) : null;
        $club ??= $this->clubFromFilename($clubs, $file['file_club']);
        $token = (string) Str::uuid();
        Storage::disk('local')->putFileAs(self::DIR, $upload, $token . '.' . $extension);
        $request->session()->put("player_stats_import.{$token}", ['name' => $upload->getClientOriginalName(), 'extension' => $extension, 'user_id' => $request->user()->id]);

        return view('player-stats-import.preview', [
            'file' => $file,
            'token' => $token,
            'clubs' => $clubs,
            'club' => $club,
            'preview' => $club ? $this->importer->preview($file, $club) : null,
            'competition' => $club ? $this->defaultCompetition($club) : '',
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'token' => ['required', 'uuid'],
            'club_id' => ['required', 'integer'],
            'season' => ['required', 'string', 'max:20'],
            'competition' => ['required', 'string', 'max:120'],
            'source' => ['required', 'string', 'max:40'],
            'create_missing' => ['nullable', 'boolean'],
        ]);
        $pending = $request->session()->pull("player_stats_import.{$data['token']}");
        abort_unless($pending && (int) $pending['user_id'] === (int) $request->user()->id, 410, 'Import expiré : téléversez le fichier à nouveau.');
        $club = $this->clubs($request)->firstWhere('id', (int) $data['club_id']);
        abort_unless($club, 403);

        $path = self::DIR . '/' . $data['token'] . '.' . $pending['extension'];
        try {
            $file = $this->reader->read(Storage::disk('local')->path($path), $pending['name']);
            abort_unless($file['is_template'], 422, 'Le fichier ne correspond pas au modèle « Player statistics ».');
            if ($file['kind'] !== 'period') {
                return redirect()->route('player-stats-import.create', ['club_id' => $club->id])
                    ->withErrors(['file' => 'Ce fichier contient les statistiques d\'un seul match : rattachez-le à une feuille de match (import par match à venir).']);
            }
            $stats = $this->importer->importPeriod($file, $club, $data);
        } finally {
            Storage::disk('local')->delete($path);
        }

        return redirect()->route('modules.coach-cockpit', ['club_id' => $club->id])->with('success',
            "Export importé pour {$club->name} : {$stats['players']} joueur(s), {$stats['metrics']} indicateur(s) enregistré(s)"
            . ($stats['created'] ? ", {$stats['created']} joueur(s) créé(s)" : '') . ($stats['skipped'] ? ", {$stats['skipped']} ignoré(s)" : '') . '.');
    }

    /** Clubs accessibles : tous pour l'admin système, sinon le club ou les clubs de la fédération du compte. */
    private function clubs(Request $request)
    {
        $user = $request->user();
        $query = Club::query()->orderBy('name');
        if (!$user->isSystemAdmin()) {
            $query->where(fn ($q) => $q->where('id', $user->club_id ?: 0)->orWhere(fn ($a) => $a->whereNotNull('association_id')->where('association_id', $user->association_id ?: 0)));
        }

        return $query->get(['id', 'name', 'association_id']);
    }

    private function clubFromFilename($clubs, ?string $name): ?Club
    {
        if (!$name) {
            return null;
        }
        $wanted = $this->importer->normalize($name);

        return $clubs->first(fn ($c) => $this->importer->normalize(str_replace(' (Démo)', '', $c->name)) === $wanted);
    }

    /** Compétition déjà utilisée pour les joueurs de ce club, à défaut vide. */
    private function defaultCompetition(Club $club): string
    {
        return (string) \Illuminate\Support\Facades\DB::table('external_player_performance_metrics as m')
            ->join('players as p', 'p.id', '=', 'm.player_id')->where('p.club_id', $club->id)
            ->orderByDesc('m.measured_at')->value('m.competition');
    }
}
