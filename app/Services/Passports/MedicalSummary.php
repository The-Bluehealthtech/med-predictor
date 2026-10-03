<?php

namespace App\Services\Passports;

use App\Models\Athlete;
use App\Models\HealthRecord;
use App\Models\Immunisation;
use App\Models\Injury;
use App\Models\PCMA;
use App\Models\Player;
use App\Models\TUERequest;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

/**
 * Passeport médical : résumé de santé du joueur structuré selon l'International
 * Patient Summary (HL7 FHIR IPS, profil IHE IPS). Construit uniquement à partir
 * des données enregistrées dans FIT, avec la date et la source de chaque élément.
 * Une section vide signifie « aucune information enregistrée », jamais « aucun
 * problème » : l'absence d'allergie n'est affirmée que si elle a été saisie.
 */
final class MedicalSummary
{
    public const PURPOSES = [
        'transfer' => 'Transfert du joueur',
        'selection' => 'Sélection nationale',
        'player_share' => 'Partage par le joueur',
        'general' => 'Résumé médical',
    ];

    /** Sections IPS dans l'ordre du standard (obligatoires, recommandées, optionnelles), puis l'extension sport. */
    public const SECTIONS = [
        'allergies' => ['title' => 'Allergies et intolérances', 'ips' => 'Allergies and Intolerances', 'loinc' => '48765-2', 'level' => 'obligatoire'],
        'problems' => ['title' => 'Problèmes de santé', 'ips' => 'Problem List', 'loinc' => '11450-4', 'level' => 'obligatoire'],
        'medications' => ['title' => 'Traitements', 'ips' => 'Medication Summary', 'loinc' => '10160-0', 'level' => 'obligatoire'],
        'immunizations' => ['title' => 'Vaccinations', 'ips' => 'Immunizations', 'loinc' => '11369-6', 'level' => 'recommandée'],
        'results' => ['title' => 'Résultats d\'examens', 'ips' => 'Results', 'loinc' => '30954-2', 'level' => 'recommandée'],
        'history' => ['title' => 'Antécédents', 'ips' => 'History of Past Illness', 'loinc' => '11348-0', 'level' => 'optionnelle'],
        'vital_signs' => ['title' => 'Signes vitaux', 'ips' => 'Vital Signs', 'loinc' => '8716-3', 'level' => 'optionnelle'],
        'sport' => ['title' => 'Aptitude sportive et antidopage', 'ips' => 'Extension FIT (Functional Status / Plan of Care)', 'loinc' => '47420-5', 'level' => 'extension'],
    ];

    public function build(Player $player, string $purpose = 'general', ?string $author = null): array
    {
        $records = HealthRecord::query()->where('player_id', $player->id)
            ->orderByDesc('record_date')->orderByDesc('id')->get();
        $athleteIds = Athlete::query()->where('player_id', $player->id)->pluck('id');
        $latest = $records->first();

        $sections = [
            'allergies' => $this->allergies($records),
            'problems' => $this->problems($records, $athleteIds),
            'medications' => $this->medications($records),
            'immunizations' => $this->immunizations($athleteIds),
            'results' => $this->results($records),
            'history' => $this->history($athleteIds),
            'vital_signs' => $this->vitalSigns($latest),
            'sport' => $this->sport($player, $records, $athleteIds),
        ];

        return [
            'document' => [
                'id' => (string) Str::uuid(),
                'standard' => 'HL7 FHIR International Patient Summary (IPS) — profil IHE IPS',
                'type_code' => 'LOINC 60591-5 (Patient summary Document)',
                'purpose' => $purpose,
                'purpose_label' => self::PURPOSES[$purpose] ?? self::PURPOSES['general'],
                'generated_at' => now(),
                'author' => $author,
                'custodian' => $player->club?->name,
                'attested' => false,
                'records_count' => $records->count(),
                'last_record_date' => $latest?->record_date,
            ],
            'patient' => [
                'id' => $player->id,
                'name' => trim($player->first_name . ' ' . $player->last_name) ?: $player->name,
                'birth_date' => $player->date_of_birth,
                'nationality' => $player->nationality,
                'fifa_connect_id' => $player->fifa_connect_id,
                'club' => $player->club?->name,
                'blood_type' => $records->pluck('blood_type')->filter()->first(),
            ],
            'sections' => $sections,
        ];
    }

    /** Libellé lisible d'une entrée libre (texte, ou objet avec nom/substance/type). */
    private function label($entry): ?string
    {
        if (is_string($entry)) {
            return trim($entry) !== '' ? trim($entry) : null;
        }
        if (is_array($entry)) {
            foreach (['name', 'label', 'substance', 'medication', 'allergen', 'code_label', 'type', 'description'] as $key) {
                if (!empty($entry[$key]) && is_string($entry[$key])) {
                    return trim($entry[$key]);
                }
            }
        }

        return null;
    }

    private function allergies(Collection $records): array
    {
        $items = [];
        foreach ($records as $record) {
            foreach ((array) ($record->allergies ?? []) as $entry) {
                $name = $this->label($entry);
                if ($name === null || isset($items[mb_strtolower($name)])) {
                    continue;
                }
                $items[mb_strtolower($name)] = [
                    'label' => $name,
                    'detail' => is_array($entry) ? trim(implode(' · ', array_filter([$entry['reaction'] ?? null, $entry['severity'] ?? null]))) : null,
                    'date' => $record->record_date,
                ];
            }
        }

        return array_values($items);
    }

    private function problems(Collection $records, Collection $athleteIds): array
    {
        $items = [];
        foreach ($records as $record) {
            foreach ((array) ($record->icd11_diagnoses ?? []) as $diagnosis) {
                $code = is_array($diagnosis) ? ($diagnosis['code'] ?? null) : null;
                $key = $code ?: $this->label($diagnosis);
                if (!$key || isset($items[$key])) {
                    continue;
                }
                $items[$key] = [
                    'label' => is_array($diagnosis) ? ($diagnosis['label'] ?? $code) : (string) $diagnosis,
                    'code' => $code ? 'CIM-11 ' . $code : null,
                    'detail' => null,
                    'status' => 'enregistré',
                    'date' => $record->record_date,
                ];
            }
        }
        if ($athleteIds->isNotEmpty()) {
            Injury::query()->whereIn('athlete_id', $athleteIds)->whereNotIn('status', ['resolved', 'healed', 'closed'])
                ->orderByDesc('date')->get()->each(function ($injury) use (&$items) {
                    $items['injury-' . $injury->id] = [
                        'label' => 'Blessure — ' . str_replace('_', ' ', (string) $injury->body_zone),
                        'code' => null,
                        'detail' => trim(implode(' · ', array_filter([$injury->severity, $injury->expected_return_date ? 'retour prévu le ' . $injury->expected_return_date->format('d/m/Y') : null]))),
                        'status' => 'active',
                        'date' => $injury->date,
                    ];
                });
        }

        return array_values($items);
    }

    private function medications(Collection $records): array
    {
        $items = [];
        foreach ($records as $record) {
            foreach ((array) ($record->medications ?? []) as $entry) {
                $name = $this->label($entry);
                if ($name === null || isset($items[mb_strtolower($name)])) {
                    continue;
                }
                $status = is_array($entry) ? ($entry['status'] ?? null) : null;
                if (in_array($status, ['stopped', 'completed', 'arrêté', 'terminé'], true)) {
                    continue;
                }
                $rxnorm = is_array($entry) && ($entry['source'] ?? null) === 'RxNorm' && preg_match('/^[0-9]+$/', (string) ($entry['rxcui'] ?? ''));
                $antidoping = is_array($entry) && (($entry['antidoping']['status'] ?? null) === 'mentions_found')
                    ? ['version' => (string) ($entry['antidoping']['version'] ?? ''), 'categories' => array_values(array_unique(array_filter(array_column($entry['antidoping']['matches'] ?? [], 'category'))))]
                    : null;
                $items[mb_strtolower($name)] = array_filter([
                    'label' => $name,
                    'detail' => is_array($entry) ? trim(implode(' · ', array_filter([$entry['dose'] ?? $entry['dosage'] ?? null, $entry['frequency'] ?? null, $entry['route'] ?? null]))) : null,
                    'status' => $status ?? 'enregistré',
                    'date' => is_array($entry) && !empty($entry['start_date']) ? $entry['start_date'] : $record->record_date,
                    // Concept RxNorm vérifié et correspondance avec la liste des interdictions AMA (alerte, pas décision).
                    'rxcui' => $rxnorm ? (string) $entry['rxcui'] : null,
                    'antidoping' => $antidoping,
                ], fn ($v) => $v !== null);
                $items[mb_strtolower($name)] += ['detail' => null];
            }
        }

        return array_values($items);
    }

    private function immunizations(Collection $athleteIds): array
    {
        if ($athleteIds->isEmpty()) {
            return [];
        }

        return Immunisation::query()->whereIn('athlete_id', $athleteIds)->orderByDesc('date_administered')->get()
            ->map(fn ($i) => [
                'label' => $i->vaccine_name ?: $i->vaccine_code,
                'detail' => trim(implode(' · ', array_filter([$i->dose_number && $i->total_doses ? "dose {$i->dose_number}/{$i->total_doses}" : null, $i->status]))),
                'date' => $i->date_administered,
            ])->all();
    }

    private function results(Collection $records): array
    {
        $items = [];
        foreach (['laboratory_results' => 'Biologie', 'imaging_results' => 'Imagerie'] as $field => $kind) {
            $record = $records->first(fn ($r) => trim((string) $r->{$field}) !== '');
            if ($record) {
                $items[] = ['label' => $kind, 'detail' => Str::limit(trim((string) $record->{$field}), 400), 'date' => $record->record_date];
            }
        }

        return $items;
    }

    private function history(Collection $athleteIds): array
    {
        if ($athleteIds->isEmpty()) {
            return [];
        }

        return Injury::query()->whereIn('athlete_id', $athleteIds)->whereIn('status', ['resolved', 'healed', 'closed'])
            ->orderByDesc('date')->limit(10)->get()
            ->map(fn ($injury) => [
                'label' => 'Blessure guérie — ' . str_replace('_', ' ', (string) $injury->body_zone),
                'detail' => trim(implode(' · ', array_filter([$injury->severity, $injury->actual_return_date ? 'retour le ' . $injury->actual_return_date->format('d/m/Y') : null]))),
                'date' => $injury->date,
            ])->all();
    }

    private function vitalSigns(?HealthRecord $latest): array
    {
        if (!$latest) {
            return [];
        }
        $items = [];
        foreach ([
            'Taille' => $latest->height ? $latest->height . ' cm' : null,
            'Poids' => $latest->weight ? $latest->weight . ' kg' : null,
            'IMC' => $latest->bmi ? number_format((float) $latest->bmi, 1, ',', ' ') : null,
            'Tension artérielle' => ($latest->blood_pressure_systolic && $latest->blood_pressure_diastolic) ? "{$latest->blood_pressure_systolic}/{$latest->blood_pressure_diastolic} mmHg" : null,
            'Fréquence cardiaque' => $latest->heart_rate ? $latest->heart_rate . ' bpm' : null,
        ] as $label => $value) {
            if ($value !== null) {
                $items[] = ['label' => $label, 'detail' => $value, 'date' => $latest->record_date];
            }
        }

        return $items;
    }

    /** Extension FIT : aptitude issue du dernier PCMA, AUT en cours, statut antidopage. */
    private function sport(Player $player, Collection $records, Collection $athleteIds): array
    {
        $items = [];
        $pcma = PCMA::query()->where(fn ($q) => $q->where('player_id', $player->id)->orWhereIn('athlete_id', $athleteIds))
            ->where('status', 'completed')->orderByDesc('assessment_date')->orderByDesc('id')->first();
        if ($pcma) {
            $final = $pcma->final_statement ?? [];
            $decision = match (true) {
                ($final['overall_decision'] ?? null) === 'FIT' || !empty($final['cleared_for_competition']) => 'Apte',
                ($final['overall_decision'] ?? null) === 'CONDITIONAL' || !empty($final['cleared_with_restrictions']) => 'Apte avec restrictions',
                ($final['overall_decision'] ?? null) === 'NOT_FIT' || !empty($final['not_cleared']) => 'Inapte',
                default => 'Conclusion non renseignée',
            };
            $items[] = ['label' => 'Aptitude (bilan pré-compétition)', 'detail' => $decision . ($pcma->is_signed ? ' · signé' : ' · non signé'), 'date' => $pcma->assessment_date];
        }
        TUERequest::query()->where('player_id', $player->id)->whereIn('status', ['approved', 'pending'])->orderByDesc('id')->get()
            ->each(function ($aut) use (&$items) {
                $items[] = ['label' => 'AUT — ' . ($aut->medication ?: 'substance non précisée'),
                    'detail' => $aut->status === 'approved' ? 'accordée (décision historique enregistrée)' : 'demande en préparation',
                    'date' => $aut->request_date];
            });
        $doping = $records->first(fn ($r) => $r->doping_test_status || $r->last_doping_test_date);
        if ($doping) {
            $items[] = ['label' => 'Contrôle antidopage', 'detail' => $doping->doping_test_status ?: 'statut non renseigné', 'date' => $doping->last_doping_test_date ?? $doping->record_date];
        }

        return $items;
    }
}
