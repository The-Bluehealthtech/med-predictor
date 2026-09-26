<?php
declare(strict_types=1);

require dirname(__DIR__) . '/vendor/autoload.php';
$app = require dirname(__DIR__) . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Player;
use App\Models\User;
use App\Services\PlayerPortalDataService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

if (DB::getDriverName() !== 'pgsql') {
    throw new RuntimeException('Audit réservé à PostgreSQL.');
}

$ids = DB::table('player_real_time_health')->where('notes', 'like', 'synthetic_demo%')
    ->distinct()->orderBy('player_id')->pluck('player_id');
$limit = isset($argv[1]) ? filter_var($argv[1], FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]) : null;
if (isset($argv[1]) && $limit === false) {
    throw new InvalidArgumentException('Limite attendue : entier positif.');
}
if ($limit !== null) {
    $ids = $ids->take($limit);
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
printf("Rendu du portail : %d joueurs, lecture seule.\n", $total);
foreach ($ids as $index => $id) {
    try {
        $user->player_id = (int) $id;
        $player = Player::withoutGlobalScopes()
            ->with(['club', 'association', 'passport'])
            ->findOrFail((int) $id);
        $html = view('test-portail-joueur-simple', array_merge(
            ['player' => $player], $service->forPlayer($player)
        ))->render();
        $visible = preg_replace('~<script\b[^>]*>.*?</script>|<style\b[^>]*>.*?</style>~si', ' ', $html);
        $visible = html_entity_decode(strip_tags((string) $visible), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        foreach (['Données non disponibles', 'N/A', 'Non renseigné'] as $marker) {
            $count = substr_count($visible, $marker);
            if ($count > 0) {
                $counts[$marker] = ($counts[$marker] ?? 0) + $count;
                $examples[$marker] ??= [];
                if (count($examples[$marker]) < 8) {
                    $examples[$marker][(int) $id] = $count;
                }
            }
        }
    } catch (Throwable $error) {
        $errors[(int) $id] = get_class($error) . ': ' . $error->getMessage();
        printf("ERREUR rendu joueur %d : %s\n", $id, $errors[(int) $id]);
        break;
    }
    if ((($index + 1) % 50) === 0 || ($index + 1) === $total) {
        printf("Rendus : %d/%d (%.0f s).\n", $index + 1, $total, microtime(true) - $start);
    }
}
foreach ($counts as $marker => $count) {
    printf("AFFICHAGE %s : %d occurrences (joueurs exemples : %s)\n", $marker, $count,
        implode(',', array_keys($examples[$marker] ?? [])));
}
printf("Erreurs de rendu : %d.\n", count($errors));
exit($errors ? 2 : ($counts ? 1 : 0));
