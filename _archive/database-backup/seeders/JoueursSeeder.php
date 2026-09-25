<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Joueur;

class JoueursSeeder extends Seeder
{
    public function run(): void
    {
        $joueurs = [
            [
                'fifa_id' => 'FIFA2025001',
                'nom' => 'Ben Salah',
                'prenom' => 'Youssef',
                'date_naissance' => '1995-04-12',
                'nationalite' => 'Tunisie',
                'poste' => 'Milieu',
                'taille_cm' => 178,
                'poids_kg' => 75,
                'club' => 'ES Tunis',
                'buts' => 8,
                'passes_decisives' => 12,
                'matchs' => 30,
                'minutes_jouees' => 2700,
                'note_moyenne' => 7.8,
                'fifa_ovr' => 82,
                'fifa_pot' => 85,
                'score_fit' => 87,
                'risque_blessure' => 12,
                'valeur_marchande' => 15000000.00
            ],
            [
                'fifa_id' => 'FIFA2025002',
                'nom' => 'Diallo',
                'prenom' => 'Moussa',
                'date_naissance' => '1998-09-25',
                'nationalite' => 'Sénégal',
                'poste' => 'Attaquant',
                'taille_cm' => 185,
                'poids_kg' => 80,
                'club' => 'Al Ahly',
                'buts' => 20,
                'passes_decisives' => 5,
                'matchs' => 28,
                'minutes_jouees' => 2520,
                'note_moyenne' => 8.2,
                'fifa_ovr' => 85,
                'fifa_pot' => 88,
                'score_fit' => 89,
                'risque_blessure' => 8,
                'valeur_marchande' => 25000000.00
            ],
            [
                'fifa_id' => 'FIFA2025003',
                'nom' => 'Benali',
                'prenom' => 'Karim',
                'date_naissance' => '2000-02-10',
                'nationalite' => 'Maroc',
                'poste' => 'Défenseur',
                'taille_cm' => 190,
                'poids_kg' => 85,
                'club' => 'Wydad AC',
                'buts' => 2,
                'passes_decisives' => 3,
                'matchs' => 32,
                'minutes_jouees' => 2880,
                'note_moyenne' => 7.5,
                'fifa_ovr' => 78,
                'fifa_pot' => 82,
                'score_fit' => 84,
                'risque_blessure' => 15,
                'valeur_marchande' => 12000000.00
            ],
            [
                'fifa_id' => 'FIFA2025004',
                'nom' => 'Haddad',
                'prenom' => 'Omar',
                'date_naissance' => '1997-07-15',
                'nationalite' => 'Algérie',
                'poste' => 'Gardien',
                'taille_cm' => 192,
                'poids_kg' => 88,
                'club' => 'CR Belouizdad',
                'buts' => 0,
                'passes_decisives' => 0,
                'matchs' => 29,
                'minutes_jouees' => 2610,
                'note_moyenne' => 7.8,
                'fifa_ovr' => 80,
                'fifa_pot' => 83,
                'score_fit' => 86,
                'risque_blessure' => 10,
                'valeur_marchande' => 18000000.00
            ],
            [
                'fifa_id' => 'FIFA2025005',
                'nom' => 'Koné',
                'prenom' => 'Ibrahim',
                'date_naissance' => '1996-11-03',
                'nationalite' => 'Côte d\'Ivoire',
                'poste' => 'Milieu défensif',
                'taille_cm' => 182,
                'poids_kg' => 78,
                'club' => 'ASEC Mimosas',
                'buts' => 4,
                'passes_decisives' => 7,
                'matchs' => 31,
                'minutes_jouees' => 2790,
                'note_moyenne' => 7.6,
                'fifa_ovr' => 79,
                'fifa_pot' => 84,
                'score_fit' => 85,
                'risque_blessure' => 18,
                'valeur_marchande' => 14000000.00
            ]
        ];

        foreach ($joueurs as $joueurData) {
            Joueur::create($joueurData);
        }
    }
}
