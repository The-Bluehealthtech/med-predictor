<?php

namespace App\Services\PlayerStatsImport;

use Illuminate\Support\Carbon;
use RuntimeException;
use ZipArchive;

/**
 * Lecture et reconnaissance d'un export « Player statistics » fourni par un club
 * (Excel .xlsx ou CSV) : en-têtes, lignes, colonnes reconnues ou inconnues,
 * date et club tirés du nom du fichier, nature du fichier (match unique ou
 * cumul de période en moyennes par match).
 */
final class PlayerStatisticsFile
{
    public function read(string $path, string $originalName): array
    {
        $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
        $table = $extension === 'xlsx' ? $this->readXlsx($path) : $this->readCsv($path);
        if (count($table) < 2) {
            throw new RuntimeException('Le fichier ne contient pas de ligne de données.');
        }
        $headers = array_map(fn ($h) => trim((string) $h), array_shift($table));
        $rows = [];
        foreach ($table as $line) {
            $line = array_pad($line, count($headers), '');
            $row = array_combine($headers, array_slice($line, 0, count($headers)));
            if (trim((string) ($row['Player'] ?? '')) !== '') {
                $rows[] = $row;
            }
        }

        return ['headers' => $headers, 'rows' => $rows] + $this->recognize($headers, $rows) + $this->fromFilename($originalName);
    }

    /** Correspondance avec le modèle : colonnes connues, inconnues, manquantes, nature du fichier. */
    public function recognize(array $headers, array $rows): array
    {
        $config = config('player_statistics_export');
        $known = array_merge($config['identity'], $config['metrics']);
        $recognized = array_values(array_intersect($headers, $known));
        $unknown = array_values(array_diff($headers, $known));
        $missingRequired = array_values(array_diff($config['required'], $headers));
        $ratio = count($headers) ? count($recognized) / count($headers) : 0;
        $minutes = array_filter(array_map(fn ($r) => is_numeric($r['Minutes played'] ?? null) ? (float) $r['Minutes played'] : null, $rows), fn ($m) => $m !== null);
        $isPeriod = $minutes && max($minutes) > $config['single_match_max_minutes'];

        return [
            'template' => $config['name'],
            'is_template' => $missingRequired === [] && $ratio >= $config['min_known_ratio'],
            'recognized' => $recognized,
            'unknown' => $unknown,
            'missing_required' => $missingRequired,
            'metric_columns' => array_values(array_intersect($headers, $config['metrics'])),
            'kind' => $isPeriod ? 'period' : 'match',
        ];
    }

    /** Date d'export et club, d'après « JJ.MM.AAAA - Club - Player statistics ». */
    public function fromFilename(string $name): array
    {
        if (!preg_match(config('player_statistics_export.filename_pattern'), $name, $m)) {
            return ['file_date' => null, 'file_club' => null];
        }
        try {
            $date = Carbon::createFromDate((int) $m['year'], (int) $m['month'], (int) $m['day'])->startOfDay();
        } catch (\Throwable) {
            $date = null;
        }

        return ['file_date' => $date, 'file_club' => trim($m['club'])];
    }

    /** Valeur numérique d'une cellule ; « - » ou vide signifie « donnée absente ». */
    public static function number($raw): ?float
    {
        $raw = trim((string) $raw);
        if ($raw === '' || $raw === '-' || strcasecmp($raw, 'N/A') === 0) {
            return null;
        }
        $raw = str_replace([' ', ','], ['', '.'], $raw);

        return is_numeric($raw) ? (float) $raw : null;
    }

    private function readCsv(string $path): array
    {
        $handle = fopen($path, 'r');
        $first = fgets($handle);
        rewind($handle);
        $delimiter = substr_count((string) $first, ';') > substr_count((string) $first, ',') ? ';' : ',';
        $table = [];
        while (($line = fgetcsv($handle, 0, $delimiter)) !== false) {
            $table[] = $line;
        }
        fclose($handle);
        if (isset($table[0][0])) {
            $table[0][0] = preg_replace('/^\xEF\xBB\xBF/', '', $table[0][0]);
        }

        return $table;
    }

    /** Première feuille d'un .xlsx (chaînes partagées, chaînes en ligne et nombres). */
    private function readXlsx(string $path): array
    {
        $zip = new ZipArchive;
        if ($zip->open($path) !== true) {
            throw new RuntimeException('Fichier Excel illisible.');
        }
        $shared = [];
        if (($xml = $zip->getFromName('xl/sharedStrings.xml')) !== false) {
            foreach (simplexml_load_string($xml)->si as $si) {
                $text = (string) $si->t;
                foreach ($si->r as $run) {
                    $text .= (string) $run->t;
                }
                $shared[] = $text;
            }
        }
        $sheet = $zip->getFromName('xl/worksheets/sheet1.xml');
        $zip->close();
        if ($sheet === false) {
            throw new RuntimeException('Aucune feuille trouvée dans le fichier Excel.');
        }
        $table = [];
        foreach (simplexml_load_string($sheet)->sheetData->row as $row) {
            $line = [];
            foreach ($row->c as $cell) {
                $index = $this->columnIndex((string) $cell['r']);
                $type = (string) $cell['t'];
                $value = match ($type) {
                    's' => $shared[(int) $cell->v] ?? '',
                    'inlineStr' => (string) $cell->is->t,
                    default => (string) $cell->v,
                };
                $line[$index] = $value;
            }
            if ($line) {
                $max = max(array_keys($line));
                $table[] = array_map(fn ($i) => $line[$i] ?? '', range(0, $max));
            }
        }

        return $table;
    }

    private function columnIndex(string $reference): int
    {
        $letters = preg_replace('/\d+/', '', $reference);
        $index = 0;
        foreach (str_split($letters) as $char) {
            $index = $index * 26 + (ord($char) - 64);
        }

        return $index - 1;
    }
}
