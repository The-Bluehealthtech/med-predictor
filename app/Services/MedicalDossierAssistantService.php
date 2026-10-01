<?php

namespace App\Services;

use App\Models\HealthRecord;

final class MedicalDossierAssistantService
{
    public function respond(HealthRecord $dossier, array $vigilance, string $mode, ?string $question = null): array
    {
        return match ($mode) {
            'verify' => $this->verify($dossier, $vigilance),
            'question' => $this->answerQuestion($dossier, $vigilance, trim((string) $question)),
            default => $this->analyze($dossier, $vigilance),
        };
    }

    private function analyze(HealthRecord $dossier, array $vigilance): array
    {
        $items = [];
        foreach (['identity', 'injury', 'cardiac'] as $axis) {
            foreach ($vigilance[$axis]['flags'] ?? [] as $flag) {
                if (($flag['severity'] ?? 'info') !== 'info') {
                    $items[] = $flag['label'];
                }
            }
        }

        $records = HealthRecord::where('player_id', $dossier->player_id)
            ->orderByDesc('record_date')->limit(10)->get();
        $latest = $records->first();

        $summary = $items
            ? 'Le dossier comporte '.count($items).' élément(s) de vigilance nécessitant une revue humaine.'
            : 'Aucun signal de vigilance majeur n’est identifié par les règles actuellement disponibles.';

        if ($latest?->record_date) {
            $summary .= ' Dernière donnée médicale enregistrée le '.$latest->record_date->format('d/m/Y').'.';
        }

        return [
            'title' => 'Analyse du dossier',
            'summary' => $summary,
            'items' => array_slice($items, 0, 8),
            'sources' => ['Vigilance identité/âge', 'Vigilance blessure', 'Vigilance cardiovasculaire', 'Historique médical'],
            'mode' => 'local-rules-v1',
        ];
    }

    private function verify(HealthRecord $dossier, array $vigilance): array
    {
        $missing = [];
        foreach (['identity', 'injury', 'cardiac'] as $axis) {
            foreach ($vigilance[$axis]['flags'] ?? [] as $flag) {
                $label = (string) ($flag['label'] ?? '');
                if (preg_match('/absent|non renseign|aucun|pas encore|à vérifier|a vérifier|non disponible|non retrouvé/i', $label)) {
                    $missing[] = $label;
                }
            }
        }

        $latest = HealthRecord::where('player_id', $dossier->player_id)
            ->orderByDesc('record_date')->first();

        if ($latest) {
            if (empty($latest->allergies)) $missing[] = 'Allergies non renseignées dans la dernière donnée disponible.';
            if (empty($latest->medications)) $missing[] = 'Traitements actuels non renseignés dans la dernière donnée disponible.';
        }

        return [
            'title' => 'Vérification avant validation',
            'summary' => $missing
                ? count($missing).' élément(s) sont à confirmer ou compléter avant une validation clinique.'
                : 'Aucun manque simple n’est détecté par les contrôles déterministes actuels.',
            'items' => array_values(array_unique(array_slice($missing, 0, 10))),
            'sources' => ['Dossier médical', 'PCMA', 'Contrôles de vigilance'],
            'mode' => 'local-rules-v1',
        ];
    }

    private function answerQuestion(HealthRecord $dossier, array $vigilance, string $question): array
    {
        if ($question === '') {
            return [
                'title' => 'Question clinique',
                'summary' => 'Saisis une question sur les données présentes dans ce dossier.',
                'items' => [],
                'sources' => [],
                'mode' => 'local-rules-v1',
            ];
        }

        $q = mb_strtolower($question);
        $items = [];
        $summary = 'La première version de l’assistant répond uniquement à partir des données structurées du dossier.';
        $sources = [];

        if (str_contains($q, 'card') || str_contains($q, 'ecg') || str_contains($q, 'coeur') || str_contains($q, 'cœur')) {
            $axis = $vigilance['cardiac'] ?? [];
            $items = array_values(array_map(fn($f) => $f['label'] ?? '', $axis['flags'] ?? []));
            $summary = $items ? 'Voici les éléments cardiovasculaires actuellement signalés.' : 'Aucun signal cardiovasculaire simple n’est relevé par les règles locales.';
            $sources = ['Vigilance cardiovasculaire', 'PCMA', 'ECG'];
        } elseif (str_contains($q, 'bless') || str_contains($q, 'lésion') || str_contains($q, 'douleur') || str_contains($q, 'recid') || str_contains($q, 'récid')) {
            $axis = $vigilance['injury'] ?? [];
            $items = array_values(array_map(fn($f) => $f['label'] ?? '', $axis['flags'] ?? []));
            $summary = $items ? 'Voici les éléments de vigilance liés aux blessures.' : 'Aucun signal simple de blessure n’est relevé par les règles locales.';
            $sources = ['Vigilance blessure', 'Historique des blessures'];
        } elseif (str_contains($q, 'ident') || str_contains($q, 'âge') || str_contains($q, 'age') || str_contains($q, 'fifa') || str_contains($q, 'naissance')) {
            $axis = $vigilance['identity'] ?? [];
            $items = array_values(array_map(fn($f) => $f['label'] ?? '', $axis['flags'] ?? []));
            $summary = $items ? 'Voici les incohérences ou données d’identité à vérifier.' : 'Aucune incohérence simple n’est relevée par les règles locales.';
            $sources = ['Vigilance identité/âge', 'Profil joueur', 'Passeport joueur'];
        } else {
            $records = HealthRecord::where('player_id', $dossier->player_id)
                ->orderByDesc('record_date')->limit(5)->get();
            $items = $records->map(function ($record) {
                $date = $record->record_date?->format('d/m/Y') ?? 'date non renseignée';
                $text = $record->chief_complaint ?: $record->diagnosis ?: 'Visite médicale';
                return $date.' — '.$text;
            })->values()->all();
            $summary = 'Je ne peux pas encore interpréter librement cette question. Voici les éléments récents du dossier pouvant aider à la revue.';
            $sources = ['Historique médical'];
        }

        return [
            'title' => 'Réponse à la question',
            'summary' => $summary,
            'items' => array_slice(array_filter($items), 0, 8),
            'sources' => $sources,
            'mode' => 'local-rules-v1',
        ];
    }
}
