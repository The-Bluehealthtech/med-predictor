<?php
declare(strict_types=1);

fwrite(STDERR, "Initialisation Laravel…\n");
fflush(STDERR);
require dirname(__DIR__) . '/vendor/autoload.php';
$app = require dirname(__DIR__) . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Player;
use App\Models\User;
use App\Services\PlayerPortalDataService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

fwrite(STDERR, "Connexion à PostgreSQL…\n");
fflush(STDERR);
if (DB::getDriverName() !== 'pgsql') {
    throw new RuntimeException('Audit réservé à PostgreSQL.');
}

DB::statement('SET statement_timeout = 30000');
fwrite(STDERR, "Chargement des identifiants des joueurs…\n");
fflush(STDERR);
$ids = DB::table('player_real_time_health')->where('notes', 'like', 'synthetic_demo%')
    ->distinct()->orderBy('player_id')->pluck('player_id');
$limit = isset($argv[1]) ? filter_var($argv[1], FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]) : null;
if (isset($argv[1]) && $limit === false) {
    throw new InvalidArgumentException('Limite attendue : entier positif.');
}
$offset = isset($argv[2]) ? filter_var($argv[2], FILTER_VALIDATE_INT, ['options' => ['min_range' => 0]]) : 0;
if ($offset === false) {
    throw new InvalidArgumentException('Décalage attendu : entier positif ou zéro.');
}
if ($limit !== null) {
    $ids = $ids->slice($offset, $limit)->values();
}

if ($ids->isEmpty()) {
    throw new RuntimeException('Aucun joueur de test trouvé.');
}

$user = new User();
$user->forceFill([
    'id' => 900001,
    'name' => 'Audit synthétique du portail',
    'role' => 'player',
    'player_id' => null,
    'status' => 'active',
]);
$user->exists = true;
Auth::setUser($user);
$service = $app->make(PlayerPortalDataService::class);
$counts = [];
$examples = [];
$errors = [];
$start = microtime(true);
$total = $ids->count();
printf("Rendu du portail : %d joueurs (décalage %d), lecture seule.\n", $total, $offset);
fflush(STDOUT);
foreach ($ids as $index => $id) {
    try {
        if ($index === 0 || (($index + 1) % 10) === 0) {
            fprintf(STDERR, "Début du rendu joueur %d/%d (ID %d)…\n", $index + 1, $total, $id);
            fflush(STDERR);
        }
        $user->player_id = (int) $id;
        $player = Player::withoutGlobalScopes()
            ->with(['club', 'association', 'passport'])
            ->findOrFail((int) $id);
        $html = view('test-portail-joueur-simple', array_merge(
            ['player' => $player], $service->forPlayer($player)
        ))->render();
        $visible = preg_replace('~<script\b[^>]*>.*?</script>|<style\b[^>]*>.*?</style>~si', ' ', $html);
        $visible = html_entity_decode(strip_tags((string) $visible), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $playerMarkers = [];
        foreach (['Données non disponibles', 'N/A', 'Non renseigné'] as $marker) {
            $count = substr_count($visible, $marker);
            if ($count > 0) {
                $playerMarkers[] = $marker . '=' . $count;
                $counts[$marker] = ($counts[$marker] ?? 0) + $count;
                $examples[$marker] ??= [];
                if (count($examples[$marker]) < 8) {
                    $examples[$marker][(int) $id] = $count;
                }
            }
        }
        printf("Joueur %d : %s\n", $id, $playerMarkers ? implode(', ', $playerMarkers) : 'aucun marqueur de valeur absente');
        fflush(STDOUT);
    } catch (Throwable $error) {
        $errors[(int) $id] = get_class($error) . ': ' . $error->getMessage();
        printf("ERREUR rendu joueur %d : %s\n", $id, $errors[(int) $id]);
        break;
    }
    if ((($index + 1) % 50) === 0 || ($index + 1) === $total) {
        printf("Rendus : %d/%d (%.0f s).\n", $index + 1, $total, microtime(true) - $start);
        fflush(STDOUT);
    }
}
if (!$errors && $offset === 0) {
    try {
        $html = $app->make(\App\Http\Controllers\PerformanceAnalyticsController::class)->index()->render();
        $visible = preg_replace('~<script\b[^>]*>.*?</script>|<style\b[^>]*>.*?</style>~si', ' ', $html);
        $visible = html_entity_decode(strip_tags((string) $visible), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        foreach (['Données non disponibles', 'N/A', 'Aucune donnée historique disponible'] as $marker) {
            $count = substr_count($visible, $marker);
            if ($count > 0) {
                $counts['analytics : ' . $marker] = $count;
                $examples['analytics : ' . $marker] = ['route /performances/analytics' => $count];
            }
        }
        echo "Route /performances/analytics : rendu réussi.\n";
    } catch (Throwable $error) {
        $errors['analytics'] = get_class($error) . ': ' . $error->getMessage();
        printf("ERREUR route /performances/analytics : %s\n", $errors['analytics']);
    }
}
foreach ($counts as $marker => $count) {
    printf("AFFICHAGE %s : %d occurrences (joueurs exemples : %s)\n", $marker, $count,
        implode(',', array_keys($examples[$marker] ?? [])));
}
printf("Erreurs de rendu : %d.\n", count($errors));
exit($errors ? 2 : ($counts ? 1 : 0));
