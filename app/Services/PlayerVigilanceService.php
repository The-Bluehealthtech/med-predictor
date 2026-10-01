<?php

namespace App\Services;

use App\Models\Athlete;
use App\Models\HealthRecord;
use App\Models\Injury;
use App\Models\PCMA;
use App\Models\Player;
use App\Models\PlayerPassport;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;

/**
 * Explainable vigilance indicators for the longitudinal medical record.
 *
 * This service does not diagnose, establish fraud, or predict an event.
 * It only surfaces traceable inconsistencies, recent history and missing
 * screening information for human review.
 */
final class PlayerVigilanceService
{
    public function assess(Player $player): array
    {
        return [
            'identity' => $this->identity($player),
            'injury' => $this->injury($player),
            'cardiac' => $this->cardiac($player),
            'generated_at' => now(),
            'method' => 'rules-v1',
        ];
    }

    private function identity(Player $player): array
    {
        $flags = [];
        $confirmed = [];

        if ($player->date_of_birth) {
            $confirmed[] = 'Date de naissance présente dans le profil joueur.';
        } else {
            $flags[] = ['severity'=>'review','label'=>'Date de naissance absente du profil joueur.'];
        }

        if ($player->fifa_connect_id) {
            $confirmed[] = 'Identifiant FIFA Connect renseigné.';
        } else {
            $flags[] = ['severity'=>'review','label'=>'Identifiant FIFA Connect non renseigné.'];
        }

        $storedAge = $player->getRawOriginal('age');
        if ($storedAge !== null && $player->date_of_birth) {
            $computedAge = (int) $player->date_of_birth->age;
            if (abs((int)$storedAge - $computedAge) > 1) {
                $flags[] = [
                    'severity'=>'attention',
                    'label'=>"Âge historique ({$storedAge}) incohérent avec la date de naissance ({$computedAge} ans).",
                ];
            }
        }

        if (Schema::hasTable('player_passports')) {
            $passport = PlayerPassport::where('player_id', $player->id)->latest('id')->first();
            if (!$passport) {
                $flags[] = ['severity'=>'review','label'=>'Passeport joueur / dossier d’identité non disponible dans FIT.'];
            } else {
                $confirmed[] = 'Passeport joueur disponible pour contrôle documentaire.';

                if ($player->date_of_birth && $passport->fifa_date_of_birth
                    && !$player->date_of_birth->isSameDay($passport->fifa_date_of_birth)) {
                    $flags[] = ['severity'=>'attention','label'=>'Date de naissance différente entre le profil joueur et le passeport FIFA.'];
                }

                if ($player->fifa_connect_id && $passport->fifa_connect_id
                    && (string)$player->fifa_connect_id !== (string)$passport->fifa_connect_id) {
                    $flags[] = ['severity'=>'attention','label'=>'Identifiant FIFA Connect différent entre le profil et le passeport joueur.'];
                }

                if ($passport->status && !in_array($passport->status, ['active'], true)) {
                    $flags[] = ['severity'=>'review','label'=>'Statut du passeport joueur : '.str_replace('_',' ', $passport->status).'.'];
                }
            }
        }

        return $this->axis('Identité / âge', $flags, $confirmed,
            'Contrôle de cohérence documentaire. Une alerte ne constitue pas une conclusion de fraude.');
    }

    private function injury(Player $player): array
    {
        $flags = [];
        $confirmed = [];
        $injuries = collect();

        if (Schema::hasTable('athletes') && Schema::hasTable('injuries')) {
            $athleteIds = Athlete::where('player_id', $player->id)->pluck('id');
            if ($athleteIds->isNotEmpty()) {
                $injuries = Injury::whereIn('athlete_id', $athleteIds)
                    ->orderByDesc('date')
                    ->get();
            }
        }

        if ($injuries->isEmpty()) {
            $confirmed[] = 'Aucune blessure structurée trouvée dans la table de suivi des blessures.';
        } else {
            $recent = $injuries->filter(fn($i) => $i->date && $i->date->gte(now()->subDays(60)));
            $active = $injuries->filter(fn($i) => !in_array($i->status, ['resolved','closed'], true));

            if ($active->isNotEmpty()) {
                $flags[] = ['severity'=>'attention','label'=>$active->count().' blessure(s) non clôturée(s) dans le suivi.'];
            }
            if ($recent->isNotEmpty()) {
                $flags[] = ['severity'=>'review','label'=>$recent->count().' blessure(s) enregistrée(s) sur les 60 derniers jours.'];
            }

            $recurrentZones = $injuries
                ->filter(fn($i) => $i->date && $i->date->gte(now()->subYear()) && filled($i->body_zone))
                ->groupBy(fn($i) => mb_strtolower(trim((string)$i->body_zone)))
                ->filter(fn(Collection $group) => $group->count() >= 2);

            foreach ($recurrentZones as $zone => $group) {
                $flags[] = [
                    'severity'=>'review',
                    'label'=>$group->count().' blessures enregistrées dans la zone « '.$zone.' » sur 12 mois.',
                ];
            }

            if (!$flags) {
                $confirmed[] = 'Historique structuré présent sans signal simple de récidive récente détecté.';
            }
        }

        $flags[] = [
            'severity'=>'info',
            'label'=>'La charge d’exposition entraînement/match n’est pas encore intégrée à cette version du moteur.',
        ];

        return $this->axis('Blessure', $flags, $confirmed,
            'Vigilance basée sur l’historique documenté ; ce n’est pas une probabilité prédictive de blessure.');
    }

    private function cardiac(Player $player): array
    {
        $flags = [];
        $confirmed = [];
        $pcma = Schema::hasTable('pcmas')
            ? PCMA::where('player_id', $player->id)->orderByDesc('assessment_date')->orderByDesc('id')->first()
            : null;

        if (!$pcma) {
            $flags[] = ['severity'=>'review','label'=>'Aucun PCMA retrouvé pour le joueur.'];
        } else {
            $confirmed[] = 'PCMA retrouvé'.($pcma->assessment_date ? ' du '.$pcma->assessment_date->format('d/m/Y') : '').'.';

            if ($pcma->status !== 'completed') {
                $flags[] = ['severity'=>'review','label'=>'Le dernier PCMA n’est pas marqué comme terminé.'];
            }
            if ($pcma->assessment_date && $pcma->assessment_date->lt(now()->subYear())) {
                $flags[] = ['severity'=>'review','label'=>'Le dernier PCMA date de plus de 12 mois : actualisation à vérifier selon le contexte de compétition.'];
            }
            if (!$pcma->ecg_date && blank($pcma->ecg_interpretation)) {
                $flags[] = ['severity'=>'review','label'=>'Aucune donnée ECG identifiée sur le dernier PCMA.'];
            } else {
                $confirmed[] = 'Donnée ECG présente dans le dernier PCMA.';
            }

            $ecg = mb_strtolower((string)$pcma->ecg_interpretation);
            if ($ecg && preg_match('/anormal|abnormal|patholog|aryth|arrhyth|st[- ]?t|à revoir|a revoir|non concluant/u', $ecg)) {
                $flags[] = ['severity'=>'attention','label'=>'L’interprétation ECG contient un élément nécessitant une revue clinique.'];
            }

            $final = $pcma->final_statement ?? [];
            $decision = strtoupper((string)($final['overall_decision'] ?? ''));
            if (in_array($decision, ['NOT_FIT','CONDITIONAL'], true)
                || !empty($final['not_cleared']) || !empty($final['cleared_with_restrictions'])) {
                $flags[] = ['severity'=>'attention','label'=>'La conclusion du dernier PCMA comporte une restriction ou une non-aptitude à revoir.'];
            }
        }

        if (Schema::hasTable('health_records')) {
            $records = HealthRecord::where('player_id', $player->id)->orderByDesc('record_date')->limit(20)->get();
            $cardiacText = $records->map(function (HealthRecord $record) {
                return implode(' ', array_filter([
                    $record->cardiac_risk_factors ?? null,
                    $record->heart_disease_status ?? null,
                    $record->ecg_effort_interpretation ?? null,
                    is_array($record->ecg_effort_findings ?? null) ? implode(' ', $record->ecg_effort_findings) : null,
                ]));
            })->implode(' ');

            $normalized = mb_strtolower($cardiacText);
            if ($normalized && preg_match('/syncope|douleur thorac|chest pain|palpitation|mort subite|sudden death|aryth|arrhyth/u', $normalized)) {
                $flags[] = ['severity'=>'attention','label'=>'Le dossier cardiovasculaire contient un terme de vigilance nécessitant une revue médicale.'];
            }
        }

        return $this->axis('Cardiovasculaire', $flags, $confirmed,
            'Dépistage de signaux documentés et de données manquantes ; aucun risque de mort subite n’est calculé.');
    }

    private function axis(string $label, array $flags, array $confirmed, string $notice): array
    {
        $severities = array_column($flags, 'severity');
        $status = in_array('attention', $severities, true)
            ? 'attention'
            : (in_array('review', $severities, true) ? 'review' : 'clear');

        return compact('label', 'status', 'flags', 'confirmed', 'notice');
    }
}
