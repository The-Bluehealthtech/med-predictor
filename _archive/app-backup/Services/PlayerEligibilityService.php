<?php

namespace App\Services;

use App\Models\Player;
use App\Models\PlayerLicense;
use App\Models\HealthRecord;
use App\Models\Competition;

class PlayerEligibilityService
{
    /**
     * Vérifie si un joueur est éligible pour une compétition donnée (licence + santé)
     * Retourne [bool, string|null] (éligible, raison si refus)
     */
    public function isEligible(Player $player, Competition $competition): array
    {
        // 1. Licence fédération si requise
        if ($competition->require_federation_license) {
            $hasValidLicense = PlayerLicense::where('player_id', $player->id)
                ->where('status', 'approved')
                ->where('season', $competition->season)
                ->exists();
            if (!$hasValidLicense) {
                return [false, 'Licence fédération non valide ou non approuvée pour la saison'];
            }
        }

        // 2. Statut médical basé sur le dernier dossier de santé
        $lastHealth = $player->healthRecords()->latest('record_date')->first();
        if ($lastHealth) {
            // Vérifier le risk_score (si > 0.8, considérer comme inéligible)
            if ($lastHealth->risk_score > 0.8) {
                return [false, 'Risque médical élevé (score: ' . round($lastHealth->risk_score * 100, 1) . '%)'];
            }
            
            // Vérifier s'il y a des symptômes graves
            if ($lastHealth->symptoms && is_array($lastHealth->symptoms)) {
                $seriousSymptoms = ['douleur intense', 'fracture', 'déchirure', 'rupture', 'traumatisme'];
                foreach ($seriousSymptoms as $symptom) {
                    if (in_array($symptom, $lastHealth->symptoms)) {
                        return [false, 'Symptômes médicaux graves détectés'];
                    }
                }
            }
            
            // Vérifier le diagnostic
            if ($lastHealth->diagnosis) {
                $seriousDiagnoses = ['fracture', 'déchirure', 'rupture', 'traumatisme', 'blessure grave'];
                foreach ($seriousDiagnoses as $diagnosis) {
                    if (stripos($lastHealth->diagnosis, $diagnosis) !== false) {
                        return [false, 'Diagnostic médical incompatible avec la pratique sportive'];
                    }
                }
            }
        }

        return [true, null];
    }
} 