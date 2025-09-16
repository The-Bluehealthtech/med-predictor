<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Player;

class PlayerAgentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🌍 Ajout des informations d\'agent aux joueurs...');

        // Données d'agents réalistes
        $agents = [
            [
                'agent_name' => 'Jorge Mendes',
                'agent_phone' => '+351 21 123 4567',
                'agent_email' => 'jorge.mendes@gestifute.pt',
                'agent_agency' => 'Gestifute',
                'agent_country' => 'Portugal',
                'agent_contract_type' => 'Exclusif',
                'agent_commission' => 10.00,
                'agent_emergency_phone' => '+351 91 987 6543',
                'agent_emergency_relation' => 'Assistante',
                'agent_notes' => 'Agent principal de nombreux joueurs portugais',
                'agent_fifa_id' => 'FIFA_AGENT_001',
                'agent_license_number' => 'PT-AG-2024-001',
                'agent_status' => 'active'
            ],
            [
                'agent_name' => 'Mino Raiola',
                'agent_phone' => '+39 02 123 4567',
                'agent_email' => 'mino.raiola@raiola.com',
                'agent_agency' => 'Raiola & Associates',
                'agent_country' => 'Italie',
                'agent_contract_type' => 'Exclusif',
                'agent_commission' => 12.00,
                'agent_emergency_phone' => '+39 33 987 6543',
                'agent_emergency_relation' => 'Partenaire',
                'agent_notes' => 'Spécialiste des transferts internationaux',
                'agent_fifa_id' => 'FIFA_AGENT_002',
                'agent_license_number' => 'IT-AG-2024-002',
                'agent_status' => 'active'
            ],
            [
                'agent_name' => 'Pini Zahavi',
                'agent_phone' => '+972 3 123 4567',
                'agent_email' => 'pini.zahavi@zahavi.co.il',
                'agent_agency' => 'Zahavi Sports',
                'agent_country' => 'Israël',
                'agent_contract_type' => 'Standard',
                'agent_commission' => 8.00,
                'agent_emergency_phone' => '+972 50 987 6543',
                'agent_emergency_relation' => 'Associé',
                'agent_notes' => 'Agent expérimenté dans les grandes ligues',
                'agent_fifa_id' => 'FIFA_AGENT_003',
                'agent_license_number' => 'IL-AG-2024-003',
                'agent_status' => 'active'
            ],
            [
                'agent_name' => 'Jonathan Barnett',
                'agent_phone' => '+44 20 123 4567',
                'agent_email' => 'jonathan.barnett@stella.com',
                'agent_agency' => 'Stellar Group',
                'agent_country' => 'Angleterre',
                'agent_contract_type' => 'Premium',
                'agent_commission' => 9.00,
                'agent_emergency_phone' => '+44 79 987 6543',
                'agent_emergency_relation' => 'Directeur',
                'agent_notes' => 'Spécialiste du marché anglais',
                'agent_fifa_id' => 'FIFA_AGENT_004',
                'agent_license_number' => 'GB-AG-2024-004',
                'agent_status' => 'active'
            ],
            [
                'agent_name' => 'Volker Struth',
                'agent_phone' => '+49 30 123 4567',
                'agent_email' => 'volker.struth@sportstotal.de',
                'agent_agency' => 'SportsTotal',
                'agent_country' => 'Allemagne',
                'agent_contract_type' => 'Standard',
                'agent_commission' => 7.50,
                'agent_emergency_phone' => '+49 17 987 6543',
                'agent_emergency_relation' => 'Associé',
                'agent_notes' => 'Agent principal du championnat allemand',
                'agent_fifa_id' => 'FIFA_AGENT_005',
                'agent_license_number' => 'DE-AG-2024-005',
                'agent_status' => 'active'
            ],
            [
                'agent_name' => 'Fernando Felicevich',
                'agent_phone' => '+56 2 123 4567',
                'agent_email' => 'fernando.felicevich@felicevich.cl',
                'agent_agency' => 'Felicevich & Associates',
                'agent_country' => 'Chili',
                'agent_contract_type' => 'Exclusif',
                'agent_commission' => 8.50,
                'agent_emergency_phone' => '+56 9 987 6543',
                'agent_emergency_relation' => 'Partenaire',
                'agent_notes' => 'Spécialiste des joueurs sud-américains',
                'agent_fifa_id' => 'FIFA_AGENT_006',
                'agent_license_number' => 'CL-AG-2024-006',
                'agent_status' => 'active'
            ],
            [
                'agent_name' => 'Ahmed Benchaabane',
                'agent_phone' => '+212 5 123 4567',
                'agent_email' => 'ahmed.benchaabane@benchaabane.ma',
                'agent_agency' => 'Benchaabane Sports',
                'agent_country' => 'Maroc',
                'agent_contract_type' => 'Standard',
                'agent_commission' => 6.00,
                'agent_emergency_phone' => '+212 6 987 6543',
                'agent_emergency_relation' => 'Associé',
                'agent_notes' => 'Agent spécialisé dans le football africain',
                'agent_fifa_id' => 'FIFA_AGENT_007',
                'agent_license_number' => 'MA-AG-2024-007',
                'agent_status' => 'active'
            ],
            [
                'agent_name' => 'Hassan Al-Zahrani',
                'agent_phone' => '+966 11 123 4567',
                'agent_email' => 'hassan.alzahrani@alzahrani.sa',
                'agent_agency' => 'Al-Zahrani Sports',
                'agent_country' => 'Arabie Saoudite',
                'agent_contract_type' => 'Premium',
                'agent_commission' => 7.00,
                'agent_emergency_phone' => '+966 50 987 6543',
                'agent_emergency_relation' => 'Directeur',
                'agent_notes' => 'Agent principal du Pro League',
                'agent_fifa_id' => 'FIFA_AGENT_008',
                'agent_license_number' => 'SA-AG-2024-008',
                'agent_status' => 'active'
            ],
            [
                'agent_name' => 'Tarek Bouchamaoui',
                'agent_phone' => '+216 71 123 456',
                'agent_email' => 'tarek.bouchamaoui@bouchamaoui.tn',
                'agent_agency' => 'Bouchamaoui Sports',
                'agent_country' => 'Tunisie',
                'agent_contract_type' => 'Standard',
                'agent_commission' => 5.50,
                'agent_emergency_phone' => '+216 98 987 654',
                'agent_emergency_relation' => 'Associé',
                'agent_notes' => 'Agent spécialisé dans le football tunisien',
                'agent_fifa_id' => 'FIFA_AGENT_009',
                'agent_license_number' => 'TN-AG-2024-009',
                'agent_status' => 'active'
            ],
            [
                'agent_name' => 'Karim Benzema Sr.',
                'agent_phone' => '+33 1 123 4567',
                'agent_email' => 'karim.benzema@benzema.fr',
                'agent_agency' => 'Benzema Sports',
                'agent_country' => 'France',
                'agent_contract_type' => 'Exclusif',
                'agent_commission' => 8.00,
                'agent_emergency_phone' => '+33 6 987 6543',
                'agent_emergency_relation' => 'Famille',
                'agent_notes' => 'Agent familial et expérimenté',
                'agent_fifa_id' => 'FIFA_AGENT_010',
                'agent_license_number' => 'FR-AG-2024-010',
                'agent_status' => 'active'
            ]
        ];

        // Récupérer tous les joueurs
        $players = Player::all();
        $playerCount = $players->count();

        $this->command->info("📊 Nombre de joueurs trouvés: {$playerCount}");

        // Répartir les agents parmi les joueurs
        foreach ($players as $index => $player) {
            $agentIndex = $index % count($agents);
            $agent = $agents[$agentIndex];
            
            // Ajouter une variation pour rendre les données plus réalistes
            $agent['agent_signed_date'] = $this->getRandomSignedDate();
            $agent['agent_commission'] = $this->getRandomCommission($agent['agent_commission']);
            
            // Mettre à jour le joueur avec les informations d'agent
            $player->update($agent);
            
            if (($index + 1) % 10 === 0) {
                $this->command->info("✅ " . ($index + 1) . " joueurs traités...");
            }
        }

        $this->command->info('🎉 Informations d\'agent ajoutées avec succès !');
        
        // Afficher un résumé
        $this->displaySummary();
    }

    /**
     * Génère une date de signature aléatoire
     */
    private function getRandomSignedDate(): string
    {
        $startDate = strtotime('2015-01-01');
        $endDate = strtotime('2024-01-01');
        $randomTimestamp = rand($startDate, $endDate);
        return date('Y-m-d', $randomTimestamp);
    }

    /**
     * Génère une commission aléatoire avec variation
     */
    private function getRandomCommission(float $baseCommission): float
    {
        $variation = rand(-200, 200) / 100; // Variation de -2% à +2%
        return round($baseCommission + $variation, 2);
    }

    /**
     * Affiche un résumé des données ajoutées
     */
    private function displaySummary(): void
    {
        $this->command->info("\n📋 RÉSUMÉ DES DONNÉES D'AGENT:");
        $this->command->info("=================================");
        
        $stats = [
            'total_players' => Player::count(),
            'with_agent_name' => Player::whereNotNull('agent_name')->count(),
            'with_agent_phone' => Player::whereNotNull('agent_phone')->count(),
            'with_agent_email' => Player::whereNotNull('agent_email')->count(),
            'active_agents' => Player::where('agent_status', 'active')->count(),
            'avg_commission' => Player::avg('agent_commission'),
            'countries_represented' => Player::whereNotNull('agent_country')->select('agent_country')->distinct()->count()
        ];

        foreach ($stats as $key => $value) {
            $label = str_replace('_', ' ', ucfirst($key));
            $formattedValue = is_numeric($value) ? number_format($value, 2) : $value;
            $this->command->info("   {$label}: {$formattedValue}");
        }

        $this->command->info("\n🌍 Pays d'agents représentés:");
        $countries = Player::whereNotNull('agent_country')
            ->select('agent_country')
            ->distinct()
            ->pluck('agent_country')
            ->sort()
            ->values();

        foreach ($countries as $country) {
            $count = Player::where('agent_country', $country)->count();
            $this->command->info("   {$country}: {$count} joueurs");
        }
    }
}
