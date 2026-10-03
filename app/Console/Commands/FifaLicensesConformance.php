<?php

namespace App\Console\Commands;

use App\Models\PlayerLicense;
use App\Services\FifaConnect\LicenseRegistrationExport;
use Illuminate\Console\Command;

/**
 * Test général d'adéquation des licences au FIFA Connect Data Standard 3.3 :
 * chaque licence est traduite en message PersonLocal ; les écarts de données
 * sont comptés par champ, et les licences complètes sont validées contre le
 * XSD officiel. Lecture seule.
 */
class FifaLicensesConformance extends Command
{
    protected $signature = 'fifa:licenses:conformance {--limit=0 : Nombre maximum de licences examinées (0 = toutes)} {--xml= : Dossier où écrire les XML valides}';

    protected $description = 'Adéquation des licences au FIFA Connect Data Standard 3.3 (PersonLocal, validation XSD officielle)';

    public function handle(LicenseRegistrationExport $export): int
    {
        $total = $valid = 0;
        $issues = [];
        $limit = (int) $this->option('limit');
        $dir = $this->option('xml');

        PlayerLicense::query()->with(['player', 'clubOfficial'])->chunkById(200, function ($licenses) use ($export, &$total, &$valid, &$issues, $limit, $dir) {
            foreach ($licenses as $license) {
                if ($limit && $total >= $limit) {
                    return false;
                }
                $total++;
                $result = $export->export($license);
                if ($result['xml'] !== null) {
                    $valid++;
                    if ($dir) {
                        @mkdir($dir, 0775, true);
                        file_put_contents(rtrim($dir, '/') . "/licence-{$license->id}.xml", $result['xml']);
                    }
                    continue;
                }
                foreach ($result['issues'] as $field => $message) {
                    $key = $field . ' — ' . preg_replace('/« [^»]+ »|\([^)]*\)/u', '…', $message);
                    $issues[$key] = ($issues[$key] ?? 0) + 1;
                }
            }
        });

        $this->info("Licences examinées : {$total} · conformes FIFA Connect 3.3 (XSD officiel) : {$valid}" . ($total ? ' (' . round(100 * $valid / $total) . ' %)' : ''));
        if ($issues) {
            arsort($issues);
            $this->table(['Écart (champ — nature)', 'Licences'], collect($issues)->map(fn ($n, $k) => [$k, $n])->values()->all());
        }

        return $total > 0 && $valid === $total ? self::SUCCESS : self::FAILURE;
    }
}
