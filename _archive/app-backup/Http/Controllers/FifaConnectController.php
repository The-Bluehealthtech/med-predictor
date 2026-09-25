<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Player;
use App\Models\PlayerLicense;
use App\Models\Competition;
use App\Models\Club;
use App\Models\Association;
use App\Models\Confederation;
use Illuminate\Support\Facades\DB;

class FifaConnectController extends Controller
{
    /**
     * Vérifier la conformité FIFA Connect d'un joueur
     */
    public function checkPlayerCompliance($playerId)
    {
        $player = Player::with([
            'club.association.confederation',
            'association.confederation',
            'playerLicenses',
            'pcmas'
        ])->findOrFail($playerId);

        $compliance = [
            'player_id' => $player->id,
            'age_category' => $player->getFifaAgeCategory(),
            'age_category_label' => $player->getFifaAgeCategoryLabel(),
            'license_type' => $player->getRecommendedFifaLicenseType(),
            'eligibility_status' => $player->getFifaEligibilityStatus(),
            'has_valid_license' => $player->hasValidFifaLicense(),
            'has_valid_pcma' => $player->hasValidPCMA(),
            'is_eligible' => $player->isEligibleForFifaLicense(),
            'compliance_score' => $this->calculateComplianceScore($player),
            'issues' => $this->getComplianceIssues($player),
            'recommendations' => $this->getComplianceRecommendations($player)
        ];

        return response()->json($compliance);
    }

    /**
     * Vérifier la conformité FIFA Connect d'un club
     */
    public function checkClubCompliance($clubId)
    {
        $club = Club::with([
            'association.confederation',
            'players.playerLicenses',
            'players.pcmas'
        ])->findOrFail($clubId);

        $players = $club->players;
        $compliance = [
            'club_id' => $club->id,
            'club_name' => $club->name,
            'association' => $club->association->name ?? 'N/A',
            'confederation' => $club->association->confederation->name ?? 'N/A',
            'total_players' => $players->count(),
            'eligible_players' => $players->filter(fn($p) => $p->isEligibleForFifaLicense())->count(),
            'valid_licenses' => $players->filter(fn($p) => $p->hasValidFifaLicense())->count(),
            'valid_pcma' => $players->filter(fn($p) => $p->hasValidPCMA())->count(),
            'compliance_score' => $this->calculateClubComplianceScore($club),
            'issues' => $this->getClubComplianceIssues($club),
            'recommendations' => $this->getClubComplianceRecommendations($club)
        ];

        return response()->json($compliance);
    }

    /**
     * Vérifier la conformité FIFA Connect d'une association
     */
    public function checkAssociationCompliance($associationId)
    {
        $association = Association::with([
            'confederation',
            'clubs.players.playerLicenses',
            'clubs.players.pcmas'
        ])->findOrFail($associationId);

        $clubs = $association->clubs;
        $allPlayers = $clubs->flatMap->players;

        $compliance = [
            'association_id' => $association->id,
            'association_name' => $association->name,
            'confederation' => $association->confederation->name ?? 'N/A',
            'total_clubs' => $clubs->count(),
            'total_players' => $allPlayers->count(),
            'eligible_players' => $allPlayers->filter(fn($p) => $p->isEligibleForFifaLicense())->count(),
            'valid_licenses' => $allPlayers->filter(fn($p) => $p->hasValidFifaLicense())->count(),
            'valid_pcma' => $allPlayers->filter(fn($p) => $p->hasValidPCMA())->count(),
            'compliance_score' => $this->calculateAssociationComplianceScore($association),
            'issues' => $this->getAssociationComplianceIssues($association),
            'recommendations' => $this->getAssociationComplianceRecommendations($association)
        ];

        return response()->json($compliance);
    }

    /**
     * Obtenir les statistiques FIFA Connect globales
     */
    public function getGlobalStats()
    {
        $stats = [
            'total_players' => Player::count(),
            'eligible_players' => Player::whereNotNull('date_of_birth')
                ->where('date_of_birth', '<=', now()->subYears(13))
                ->count(),
            'valid_licenses' => Player::whereHas('playerLicenses', function($query) {
                $query->where('status', PlayerLicense::STATUS_ACTIVE)
                      ->where('end_date', '>', now());
            })->count(),
            'valid_pcma' => Player::whereHas('pcmas', function($query) {
                $query->where('status', 'completed')
                      ->where('completed_at', '>=', now()->subYear());
            })->count(),
            'total_clubs' => Club::count(),
            'total_associations' => Association::count(),
            'total_confederations' => Confederation::count(),
            'active_competitions' => DB::table('competitions')->where('status', 'active')->count(),
            'fifa_compliance_rate' => $this->calculateGlobalComplianceRate()
        ];

        return response()->json($stats);
    }

    /**
     * Calculer le score de conformité d'un joueur
     */
    private function calculateComplianceScore($player): int
    {
        $score = 0;
        
        if ($player->isEligibleForFifaLicense()) $score += 33;
        if ($player->hasValidFifaLicense()) $score += 33;
        if ($player->hasValidPCMA()) $score += 34;
        
        return $score;
    }

    /**
     * Calculer le score de conformité d'un club
     */
    private function calculateClubComplianceScore($club): int
    {
        $players = $club->players;
        if ($players->isEmpty()) return 0;
        
        $totalScore = $players->sum(fn($p) => $this->calculateComplianceScore($p));
        return round($totalScore / $players->count());
    }

    /**
     * Calculer le score de conformité d'une association
     */
    private function calculateAssociationComplianceScore($association): int
    {
        $clubs = $association->clubs;
        if ($clubs->isEmpty()) return 0;
        
        $totalScore = $clubs->sum(fn($c) => $this->calculateClubComplianceScore($c));
        return round($totalScore / $clubs->count());
    }

    /**
     * Calculer le taux de conformité global
     */
    private function calculateGlobalComplianceRate(): float
    {
        $totalPlayers = Player::count();
        if ($totalPlayers === 0) return 0;
        
        $compliantPlayers = Player::whereHas('playerLicenses', function($query) {
                $query->where('status', PlayerLicense::STATUS_ACTIVE)
                      ->where('end_date', '>', now());
            })
            ->whereHas('pcmas', function($query) {
                $query->where('status', 'completed')
                      ->where('completed_at', '>=', now()->subYear());
            })
            ->count();
        
        return round(($compliantPlayers / $totalPlayers) * 100, 2);
    }

    /**
     * Obtenir les problèmes de conformité d'un joueur
     */
    private function getComplianceIssues($player): array
    {
        $issues = [];
        
        if (!$player->isEligibleForFifaLicense()) {
            $issues[] = 'Joueur non éligible pour une licence FIFA Connect';
        }
        
        if (!$player->hasValidFifaLicense()) {
            $issues[] = 'Licence FIFA Connect invalide ou expirée';
        }
        
        if (!$player->hasValidPCMA()) {
            $issues[] = 'PCMA non valide ou expiré';
        }
        
        return $issues;
    }

    /**
     * Obtenir les recommandations de conformité d'un joueur
     */
    private function getComplianceRecommendations($player): array
    {
        $recommendations = [];
        
        if (!$player->hasValidFifaLicense()) {
            $recommendations[] = 'Renouveler la licence FIFA Connect';
        }
        
        if (!$player->hasValidPCMA()) {
            $recommendations[] = 'Effectuer un nouveau PCMA conforme FIFA';
        }
        
        return $recommendations;
    }

    /**
     * Obtenir les problèmes de conformité d'un club
     */
    private function getClubComplianceIssues($club): array
    {
        $issues = [];
        $players = $club->players;
        
        $ineligiblePlayers = $players->filter(fn($p) => !$p->isEligibleForFifaLicense());
        if ($ineligiblePlayers->count() > 0) {
            $issues[] = "{$ineligiblePlayers->count()} joueur(s) non éligible(s)";
        }
        
        $invalidLicenses = $players->filter(fn($p) => !$p->hasValidFifaLicense());
        if ($invalidLicenses->count() > 0) {
            $issues[] = "{$invalidLicenses->count()} licence(s) invalide(s)";
        }
        
        $invalidPcma = $players->filter(fn($p) => !$p->hasValidPCMA());
        if ($invalidPcma->count() > 0) {
            $issues[] = "{$invalidPcma->count()} PCMA invalide(s)";
        }
        
        return $issues;
    }

    /**
     * Obtenir les recommandations de conformité d'un club
     */
    private function getClubComplianceRecommendations($club): array
    {
        $recommendations = [];
        $players = $club->players;
        
        $ineligiblePlayers = $players->filter(fn($p) => !$p->isEligibleForFifaLicense());
        if ($ineligiblePlayers->count() > 0) {
            $recommendations[] = "Vérifier l'éligibilité de {$ineligiblePlayers->count()} joueur(s)";
        }
        
        $invalidLicenses = $players->filter(fn($p) => !$p->hasValidFifaLicense());
        if ($invalidLicenses->count() > 0) {
            $recommendations[] = "Renouveler {$invalidLicenses->count()} licence(s)";
        }
        
        $invalidPcma = $players->filter(fn($p) => !$p->hasValidPCMA());
        if ($invalidPcma->count() > 0) {
            $recommendations[] = "Effectuer {$invalidPcma->count()} nouveau(x) PCMA";
        }
        
        return $recommendations;
    }

    /**
     * Obtenir les problèmes de conformité d'une association
     */
    private function getAssociationComplianceIssues($association): array
    {
        $issues = [];
        $clubs = $association->clubs;
        $allPlayers = $clubs->flatMap->players;
        
        $ineligiblePlayers = $allPlayers->filter(fn($p) => !$p->isEligibleForFifaLicense());
        if ($ineligiblePlayers->count() > 0) {
            $issues[] = "{$ineligiblePlayers->count()} joueur(s) non éligible(s) dans l'association";
        }
        
        $invalidLicenses = $allPlayers->filter(fn($p) => !$p->hasValidFifaLicense());
        if ($invalidLicenses->count() > 0) {
            $issues[] = "{$invalidLicenses->count()} licence(s) invalide(s) dans l'association";
        }
        
        return $issues;
    }

    /**
     * Obtenir les recommandations de conformité d'une association
     */
    private function getAssociationComplianceRecommendations($association): array
    {
        $recommendations = [];
        $clubs = $association->clubs;
        $allPlayers = $clubs->flatMap->players;
        
        $ineligiblePlayers = $allPlayers->filter(fn($p) => !$p->isEligibleForFifaLicense());
        if ($ineligiblePlayers->count() > 0) {
            $recommendations[] = "Audit de conformité FIFA Connect pour {$ineligiblePlayers->count()} joueur(s)";
        }
        
        $invalidLicenses = $allPlayers->filter(fn($p) => !$p->hasValidFifaLicense());
        if ($invalidLicenses->count() > 0) {
            $recommendations[] = "Campagne de renouvellement de licences pour {$invalidLicenses->count()} joueur(s)";
        }
        
        return $recommendations;
    }
}
