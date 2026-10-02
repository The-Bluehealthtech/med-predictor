<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use ZipArchive;

/**
 * Téléversement des exports « Player statistics » des clubs : reconnaissance du
 * modèle depuis un vrai fichier Excel, aperçu sans écriture, import confirmé.
 */
class PlayerStatsImportTest extends TestCase
{
    use DatabaseTransactions;

    private int $clubId;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('local');
        $this->clubId = (int) DB::table('clubs')->insertGetId(['name' => 'Club Import Test', 'created_at' => now(), 'updated_at' => now()]);
        foreach ([['Karim', 'Ben Salah'], ['Youssef', 'Al-Amri']] as [$first, $last]) {
            DB::table('players')->insert(['name' => "$first $last", 'first_name' => $first, 'last_name' => $last, 'club_id' => $this->clubId, 'created_at' => now(), 'updated_at' => now()]);
        }
    }

    /** Fabrique un .xlsx minimal au format de l'export (cellules en chaînes en ligne et nombres). */
    private function xlsx(array $rows, string $name): UploadedFile
    {
        $headers = array_merge(['№', 'Player', 'Age', 'Height', 'Weight', 'Nationality'], ['Index', 'Minutes played', 'Position'], array_slice(config('player_statistics_export.metrics'), 2));
        $col = fn (int $i) => ($i >= 26 ? chr(64 + intdiv($i, 26)) : '') . chr(65 + $i % 26);
        $xmlRows = '';
        foreach (array_merge([$headers], $rows) as $r => $values) {
            $cells = '';
            foreach (array_values($values) as $i => $v) {
                $ref = $col($i) . ($r + 1);
                $cells .= is_numeric($v) ? "<c r=\"$ref\"><v>$v</v></c>" : "<c r=\"$ref\" t=\"inlineStr\"><is><t>" . htmlspecialchars((string) $v) . '</t></is></c>';
            }
            $xmlRows .= '<row r="' . ($r + 1) . "\">$cells</row>";
        }
        $path = tempnam(sys_get_temp_dir(), 'xlsx');
        $zip = new ZipArchive;
        $zip->open($path, ZipArchive::OVERWRITE);
        $zip->addFromString('[Content_Types].xml', '<?xml version="1.0"?><Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types"/>');
        $zip->addFromString('xl/worksheets/sheet1.xml', '<?xml version="1.0"?><worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main"><sheetData>' . $xmlRows . '</sheetData></worksheet>');
        $zip->close();

        return new UploadedFile($path, $name, null, null, true);
    }

    private function row(string $player, int $minutes): array
    {
        $metrics = array_fill(0, count(config('player_statistics_export.metrics')) - 2, '-');
        $metrics[array_search('Passes', array_slice(config('player_statistics_export.metrics'), 2))] = 41.5;
        $metrics[array_search('Passes accurate, %', array_slice(config('player_statistics_export.metrics'), 2))] = 0.88;

        return array_merge([7, $player, 25, '-', '-', 'Tunisia', 170, $minutes, 'CM'], $metrics);
    }

    private function admin(): User
    {
        return User::factory()->create(['role' => 'system_admin', 'status' => 'active']);
    }

    public function test_excel_export_is_recognised_previewed_then_imported(): void
    {
        $admin = $this->admin();
        $file = $this->xlsx([$this->row('Karim Ben Salah', 799), $this->row('Youssef Al Amri', 640), $this->row('Joueur Inconnu', 120)], '27.09.2026 - Club Import Test - Player statistics.xlsx');

        $preview = $this->actingAs($admin)->post(route('player-stats-import.preview'), ['file' => $file])->assertOk();
        $preview->assertSee('Reconnu')->assertSee('Cumul de période')->assertSee('27/09/2026')->assertSee('Karim Ben Salah')->assertSee('Joueur Inconnu');
        $this->assertSame($this->clubId, $preview->viewData('club')->id, 'club déduit du nom du fichier');
        $this->assertCount(2, $preview->viewData('preview')['matched'], 'tiret et accents ignorés : « Al Amri » = « Al-Amri »');
        $this->assertSame(0, DB::table('external_player_performance_metrics')->whereIn('player_id', DB::table('players')->where('club_id', $this->clubId)->pluck('id'))->count(), 'rien avant confirmation');

        $this->actingAs($admin)->post(route('player-stats-import.store'), [
            'token' => $preview->viewData('token'), 'club_id' => $this->clubId, 'season' => '2026/27',
            'competition' => 'Saudi Professional League', 'source' => 'KSA', 'create_missing' => '0',
        ])->assertRedirect()->assertSessionHas('success');

        $karim = (int) DB::table('players')->where('club_id', $this->clubId)->where('last_name', 'Ben Salah')->value('id');
        $passes = DB::table('external_player_performance_metrics')->where('player_id', $karim)->where('metric_name', 'passes')->first();
        $this->assertEquals(41.5, (float) $passes->metric_value);
        $this->assertSame(['KSA', '2026/27', 'Saudi Professional League'], [$passes->source, $passes->season, $passes->competition]);
        $this->assertSame('2026-09-27', substr((string) $passes->measured_at, 0, 10));
        $this->assertSame(2, DB::table('players')->where('club_id', $this->clubId)->count(), 'joueur inconnu ignoré sans l\'option');
        $this->assertSame(0, DB::table('external_player_performance_metrics')->where('metric_name', 'minutes_played')->where('player_id', $karim)->where('metric_value', '-')->count());
    }

    public function test_missing_players_can_be_created_and_tokens_are_single_use(): void
    {
        $admin = $this->admin();
        $preview = $this->actingAs($admin)->post(route('player-stats-import.preview'), ['file' => $this->xlsx([$this->row('Nouveau Joueur', 500)], 'export.xlsx'), 'club_id' => $this->clubId])->assertOk();
        $payload = ['token' => $preview->viewData('token'), 'club_id' => $this->clubId, 'season' => '2026/27', 'competition' => 'Ligue test', 'source' => 'KSA', 'create_missing' => '1'];
        $this->actingAs($admin)->post(route('player-stats-import.store'), $payload)->assertRedirect();
        $this->assertSame(1, DB::table('players')->where('club_id', $this->clubId)->where('last_name', 'Joueur')->count());
        $this->actingAs($admin)->post(route('player-stats-import.store'), $payload)->assertStatus(410);
    }

    public function test_single_match_files_and_unknown_formats_are_not_imported(): void
    {
        $admin = $this->admin();
        $this->actingAs($admin)->post(route('player-stats-import.preview'), ['file' => $this->xlsx([$this->row('Karim Ben Salah', 90)], 'match.xlsx'), 'club_id' => $this->clubId])
            ->assertOk()->assertSee('Match unique')->assertSee('il n\'est pas importé ici', false)->assertDontSee('Confirmer l\'import');

        $csv = UploadedFile::fake()->createWithContent('autre.csv', "Nom,Buts\nKarim,3\n");
        $this->actingAs($admin)->post(route('player-stats-import.preview'), ['file' => $csv, 'club_id' => $this->clubId])->assertOk()->assertSee('Non reconnu');
        $this->actingAs($admin)->post(route('player-stats-import.preview'), ['file' => UploadedFile::fake()->create('doc.pdf', 10)])->assertSessionHasErrors('file');

        // Sans la permission de saisir des métriques : renvoyé vers le tableau de bord
        $coach = User::factory()->create(['role' => 'club_admin', 'club_id' => $this->clubId, 'tenant_id' => 1, 'status' => 'active']);
        $this->actingAs($coach)->get(route('player-stats-import.create'))->assertRedirect();
    }

    public function test_the_real_club_export_is_recognised_when_available(): void
    {
        $real = '/Users/izharmahjoub/Desktop/images FIT/27.09.2026 - Al-Hazem SC - Player statistics.xlsx';
        if (!is_file($real)) {
            $this->markTestSkipped('Fichier réel absent de cette machine.');
        }
        $file = app(\App\Services\PlayerStatsImport\PlayerStatisticsFile::class)->read($real, basename($real));
        $this->assertTrue($file['is_template']);
        $this->assertSame('period', $file['kind']);
        $this->assertSame([], $file['unknown']);
        $this->assertCount(57, $file['metric_columns']);
        $this->assertSame('Al-Hazem SC', $file['file_club']);
    }
}
