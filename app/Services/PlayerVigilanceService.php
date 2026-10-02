<?php

namespace App\Services;

use App\Models\Athlete;
use App\Models\HealthRecord;
use App\Models\Injury;
use App\Models\PCMA;
use App\Models\Player;
use App\Models\PlayerFitnessLog;
use App\Models\PlayerPassport;
use App\Models\PlayerSeasonStat;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;

/**
 * Explainable vigilance indicators for the longitudinal medical record.
 *
 * External-reference-v1 maps local, traceable data to published FIFA/IOC
 * surveillance frameworks. It does not diagnose, prove fraud, establish
 * eligibility or calculate future injury/sudden-death probabilities.
 */
final class PlayerVigilanceService
{
    private const FIFA_CONNECT_URL = 'https://legal.fifa.com/advancing-football/fifa-connect/programme-details';
    private const FIFA_MINOR_URL = 'https://legal.fifa.com/data-protection-portal/underage-players';
    private const FIFA_CARDIAC_URL = 'https://inside.fifa.com/health-and-medical/education-awareness/sudden-cardiac-arrest';
    private const IOC_INJURY_URL = 'https://bjsm.bmj.com/content/54/7/372';
    private const FOOTBALL_INJURY_URL = 'https://bjsm.bmj.com/content/40/3/193';

    public function assess(Player $player): array
    {
        return [
            'identity' => $this->identity($player),
            'injury' => $this->injury($player),
            'cardiac' => $this->cardiac($player),
            'generated_at' => now(),
            'method' => 'external-reference-v1',
        ];
    }

    private function identity(Player $player): array
    {
        $flags = [];
        $confirmed = [];
        $age = $player->date_of_birth?->age;

        if ($player->date_of_birth) {
            $confirmed[] = 'Date de naissance présente dans le profil joueur.';
        } else {
            $flags[] = $this->flag('review', 'Date de naissance absente du profil joueur.', 'FIFA Connect');
        }

        if ($player->fifa_connect_id) {
            $confirmed[] = 'Identifiant FIFA Connect renseigné.';
        } else {
            $flags[] = $this->flag('review', 'Identifiant FIFA Connect non renseigné.', 'FIFA Connect');
        }

        $storedAge = $player->getRawOriginal('age');
        if ($storedAge !== null && $player->date_of_birth) {
            $computedAge = (int) $player->date_of_birth->age;
            if (abs((int)$storedAge - $computedAge) > 1) {
                $flags[] = $this->flag(
                    'attention',
                    "Âge historique ({$storedAge}) incohérent avec la date de naissance ({$computedAge} ans).",
                    'Contrôle de cohérence FIT / FIFA Connect'
                );
            }
        }

        $passport = null;
        if (Schema::hasTable('player_passports')) {
            $passport = PlayerPassport::where('player_id', $player->id)->latest('id')->first();
        }

        if (!$passport) {
            $flags[] = $this->flag(
                $age !== null && $age < 18 ? 'review' : 'incomplete',
                $age !== null && $age < 18
                    ? 'Preuve documentaire d’identité/date de naissance non disponible dans le passeport joueur FIT.'
                    : 'Passeport joueur / preuve documentaire non disponible dans FIT pour contrôle croisé.',
                $age !== null && $age < 18 ? 'FIFA — joueurs de moins de 18 ans' : 'FIFA Connect'
            );
        } else {
            $confirmed[] = 'Passeport joueur disponible pour contrôle documentaire.';

            if ($player->date_of_birth && $passport->fifa_date_of_birth
                && !$player->date_of_birth->isSameDay($passport->fifa_date_of_birth)) {
                $flags[] = $this->flag('attention', 'Date de naissance différente entre le profil joueur et le passeport FIFA.', 'FIFA Connect');
            }

            if ($player->fifa_connect_id && $passport->fifa_connect_id
                && (string)$player->fifa_connect_id !== (string)$passport->fifa_connect_id) {
                $flags[] = $this->flag('attention', 'Identifiant FIFA Connect différent entre le profil et le passeport joueur.', 'FIFA Connect');
            }

            if ($passport->status && $passport->status !== 'active') {
                $flags[] = $this->flag('review', 'Statut du passeport joueur : '.str_replace('_',' ', $passport->status).'.', 'FIFA Connect');
            }
        }

        try {
            $ageReview = app(AgeVerificationService::class)->assess($player, false);
            $flags = array_merge($flags, $ageReview['flags']);
            $confirmed = array_merge($confirmed, $ageReview['confirmed']);
        } catch (\Throwable $e) {
            \Log::warning('Age review unavailable', ['player_id'=>$player->id]);
            $flags[] = $this->flag('incomplete', 'Revue U-17 temporairement indisponible.', 'FIT');
        }

        return $this->axis(
            'Identité / âge',
            $flags,
            $confirmed,
            'Contrôle de cohérence documentaire : une anomalie déclenche une vérification humaine et ne constitue jamais, à elle seule, une conclusion de fraude.',
            [
                $this->reference('FIFA Connect — services et qualité des données', self::FIFA_CONNECT_URL),
                $this->reference('FIFA — données requises pour les joueurs de moins de 18 ans', self::FIFA_MINOR_URL),
            ]
        );
    }

    private function injury(Player $player): array
    {
        $flags = [];
        $confirmed = [];
        $injuries = collect();

        if (Schema::hasTable('athletes') && Schema::hasTable('injuries')) {
            $athleteIds = Athlete::where('player_id', $player->id)->pluck('id');
            if ($athleteIds->isNotEmpty()) {
                $injuries = Injury::whereIn('athlete_id', $athleteIds)->orderByDesc('date')->get();
            }
        }

        if ($injuries->isEmpty()) {
            $flags[] = $this->flag('incomplete', 'Aucune blessure structurée n’est disponible pour établir un historique de surveillance.', 'IOC / football injury surveillance');
        } else {
            $confirmed[] = $injuries->count().' blessure(s) structurée(s) disponible(s) dans l’historique.';
            $recent = $injuries->filter(fn($i) => $i->date && $i->date->gte(now()->subDays(60)));
            $active = $injuries->filter(fn($i) => !in_array($i->status, ['resolved','closed'], true));

            if ($active->isNotEmpty()) {
                $flags[] = $this->flag('attention', $active->count().' blessure(s) non clôturée(s) dans le suivi.', 'IOC — time loss / disponibilité');
            }
            if ($recent->isNotEmpty()) {
                $flags[] = $this->flag('review', $recent->count().' blessure(s) enregistrée(s) sur les 60 derniers jours.', 'Surveillance locale de récence');
            }

            $recurrentZones = $injuries
                ->filter(fn($i) => $i->date && $i->date->gte(now()->subYear()) && filled($i->body_zone))
                ->groupBy(fn($i) => mb_strtolower(trim((string)$i->body_zone)))
                ->filter(fn(Collection $group) => $group->count() >= 2);

            foreach ($recurrentZones as $zone => $group) {
                $flags[] = $this->flag(
                    'review',
                    $group->count().' blessures enregistrées dans la zone « '.$zone.' » sur 12 mois : vérifier s’il s’agit réellement de récidives du même type/site après retour complet.',
                    'Consensus football — recurrent injury'
                );
            }
        }

        $hasTrainingExposure = false;
        $hasMatchExposure = false;
        $trainingMinutes = 0;
        $matchMinutes = 0;

        if (Schema::hasTable('player_fitness_logs')) {
            $logs = PlayerFitnessLog::where('player_id', $player->id)
                ->where('log_date', '>=', now()->subDays(28))
                ->where('is_completed', true)
                ->get();
            $trainingMinutes = (int) $logs->where('session_type', 'training')->sum('duration_minutes');
            $matchMinutes = (int) $logs->where('session_type', 'match')->sum('duration_minutes');
            $hasTrainingExposure = $trainingMinutes > 0;
            $hasMatchExposure = $matchMinutes > 0;
        }

        if (!$hasMatchExposure && Schema::hasTable('player_season_stats')) {
            $seasonMinutes = (int) PlayerSeasonStat::where('player_id', $player->id)->sum('minutes_played');
            if ($seasonMinutes > 0) {
                $hasMatchExposure = true;
                $matchMinutes = $seasonMinutes;
            }
        }

        if ($hasTrainingExposure) {
            $confirmed[] = 'Exposition entraînement disponible : '.$trainingMinutes.' min enregistrées sur les 28 derniers jours.';
        } else {
            $flags[] = $this->flag('incomplete', 'Exposition individuelle à l’entraînement insuffisante ou absente sur les 28 derniers jours.', 'IOC — individual training exposure');
        }

        if ($hasMatchExposure) {
            $confirmed[] = 'Exposition match disponible dans FIT ('.$matchMinutes.' min enregistrées/agrégées).';
        } else {
            $flags[] = $this->flag('incomplete', 'Exposition individuelle en match non disponible dans les données consultées.', 'IOC — competition exposure');
        }

        return $this->axis(
            'Blessure',
            $flags,
            $confirmed,
            'Le moteur vérifie la qualité des données nécessaires à une surveillance des blessures. Il ne calcule pas encore une probabilité prédictive individuelle.',
            [
                $this->reference('IOC 2020 — injury and illness surveillance', self::IOC_INJURY_URL),
                $this->reference('Football injury consensus — exposition et récidive', self::FOOTBALL_INJURY_URL),
            ]
        );
    }

    private function cardiac(Player $player): array
    {
        $flags = [];
        $confirmed = [];
        $age = $player->date_of_birth?->age;
        $pcma = Schema::hasTable('pcmas')
            ? PCMA::where('player_id', $player->id)->orderByDesc('assessment_date')->orderByDesc('id')->first()
            : null;

        if (!$pcma) {
            $flags[] = $this->flag('review', 'Aucun PCMA / screening cardiaque retrouvé pour le joueur.', 'FIFA cardiac screening');
        } else {
            $confirmed[] = 'PCMA retrouvé'.($pcma->assessment_date ? ' du '.$pcma->assessment_date->format('d/m/Y') : '').'.';

            if ($pcma->status !== 'completed') {
                $flags[] = $this->flag('review', 'Le dernier PCMA n’est pas marqué comme terminé.', 'FIFA cardiac screening');
            }

            if ($age !== null && $age >= 12 && $age < 18 && $pcma->assessment_date) {
                if ($pcma->assessment_date->lt(now()->subYears(4))) {
                    $flags[] = $this->flag('review', 'Le screening du joueur mineur date de plus de 4 ans.', 'FIFA youth cardiac screening — répétition tous les 2–4 ans');
                } elseif ($pcma->assessment_date->lt(now()->subYears(2))) {
                    $flags[] = $this->flag('incomplete', 'Le screening du joueur mineur a plus de 2 ans : vérifier le calendrier de renouvellement (fenêtre recommandée 2–4 ans).', 'FIFA youth cardiac screening');
                }
            }

            if (!$pcma->ecg_date && blank($pcma->ecg_interpretation)) {
                $flags[] = $this->flag('review', 'Aucune donnée ECG identifiée sur le dernier PCMA.', 'FIFA cardiac screening — ECG');
            } else {
                $confirmed[] = 'Donnée ECG présente dans le dernier PCMA.';
            }

            if (empty($pcma->medical_history)) {
                $flags[] = $this->flag('incomplete', 'Antécédents médicaux personnels/familiaux non structurés dans le dernier PCMA.', 'FIFA youth cardiac screening — personal and family history');
            } else {
                $confirmed[] = 'Antécédents médicaux structurés présents dans le PCMA.';
            }

            if (empty($pcma->physical_examination)) {
                $flags[] = $this->flag('incomplete', 'Examen clinique ciblé non structuré dans le dernier PCMA.', 'FIFA cardiac screening — focused physical examination');
            } else {
                $confirmed[] = 'Examen clinique structuré présent dans le PCMA.';
            }

            $ecg = mb_strtolower((string)$pcma->ecg_interpretation);
            if ($ecg && preg_match('/anormal|abnormal|patholog|aryth|arrhyth|st[- ]?t|à revoir|a revoir|non concluant/u', $ecg)) {
                $flags[] = $this->flag('attention', 'L’interprétation ECG contient un élément nécessitant une revue clinique.', 'ECG documenté dans FIT');
            }

            $final = $pcma->final_statement ?? [];
            $decision = strtoupper((string)($final['overall_decision'] ?? ''));
            if (in_array($decision, ['NOT_FIT','CONDITIONAL'], true)
                || !empty($final['not_cleared']) || !empty($final['cleared_with_restrictions'])) {
                $flags[] = $this->flag('attention', 'La conclusion médicale documentée comporte une restriction ou une non-aptitude à revoir.', 'Conclusion PCMA humaine');
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
                $flags[] = $this->flag('attention', 'Le dossier cardiovasculaire contient un terme de vigilance nécessitant une revue médicale.', 'Historique médical FIT');
            }
        }

        return $this->axis(
            'Cardiovasculaire',
            $flags,
            $confirmed,
            'Dépistage de signaux et de données manquantes selon le cadre FIFA. Aucun programme ne supprime totalement le risque ; la préparation RCP/AED et le plan d’urgence terrain restent essentiels.',
            [$this->reference('FIFA — cardiac screening & sudden cardiac arrest', self::FIFA_CARDIAC_URL)]
        );
    }

    private function flag(string $severity, string $label, string $source): array
    {
        return compact('severity', 'label', 'source');
    }

    private function reference(string $title, string $url): array
    {
        return compact('title', 'url');
    }

    private function axis(string $label, array $flags, array $confirmed, string $notice, array $references): array
    {
        $severities = array_column($flags, 'severity');
        $status = in_array('attention', $severities, true)
            ? 'attention'
            : (in_array('review', $severities, true)
                ? 'review'
                : (in_array('incomplete', $severities, true) ? 'incomplete' : 'clear'));

        return compact('label', 'status', 'flags', 'confirmed', 'notice', 'references');
    }
}
