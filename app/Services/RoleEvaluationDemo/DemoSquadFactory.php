<?php

namespace App\Services\RoleEvaluationDemo;

/**
 * Construit en mémoire des clubs, équipes et joueurs de démonstration
 * fictifs (noms et identifiants FIFA Connect volontairement non réels et
 * clairement marqués "DEMO", cf. rapport Livrable 3 — clubs et competitions
 * n'ont pas de colonne is_demo, voir Livrable 1, donc le marquage par
 * convention de nom/identifiant est le seul repère disponible sur ces deux
 * tables).
 *
 * Ne touche PAS à la base : produit des tableaux PHP. L'écriture effective
 * (avec récupération des id auto-incrémentés) est faite par
 * DemoDataGenerator, qui est seul à parler à la base.
 */
class DemoSquadFactory
{
    private const CLUB_PREFIXES = ['AS', 'FC', 'Racing', 'Stade', 'Étoile', 'Olympique', 'US', 'RC', 'CS', 'SC'];

    private const CLUB_PLACE_NAMES = [
        'Verchamps', 'Portelune', 'Malmont', 'Clairval', 'Rochebrune', 'Ferrières',
        'Solmont', 'Aigreval', 'Brancourt', 'Fontenoy', 'Grandpré', 'Val-Sereine',
        'Beaulande', 'Noiremont', 'Sourcelle', 'Cantavel',
    ];

    private const FIRST_NAMES = [
        'Alexandre', 'Baptiste', 'Damien', 'Gabriel', 'Mathieu', 'Nicolas', 'Julien',
        'Romain', 'Antoine', 'Louis', 'Hugo', 'Thomas', 'Maxime', 'Florian', 'Clément',
        'Théo', 'Quentin', 'Benjamin', 'Kevin', 'Lucas', 'Enzo', 'Rayan', 'Noah', 'Yanis',
        'Malo', 'Samuel', 'Adrien', 'Victor', 'Simon', 'Paul',
    ];

    private const LAST_NAMES = [
        'Michel', 'Simon', 'Leroy', 'David', 'Mercier', 'Petit', 'Girard', 'Roux',
        'Fontaine', 'Chevalier', 'Robin', 'Morel', 'Fournier', 'Gauthier', 'Perrin',
        'Lambert', 'Barbier', 'Rolland', 'Renard', 'Faure', 'Aubert', 'Dumas', 'Marchand',
        'Noel', 'Meunier', 'Legrand', 'Guerin', 'Boyer', 'Riviere', 'Brun',
    ];

    public function __construct(private SeededRandom $rng)
    {
    }

    /**
     * @return array<int, array{name:string, fifa_connect_id:string}>
     */
    public function buildClubs(int $count): array
    {
        $prefixes = $this->rng->shuffleArray(self::CLUB_PREFIXES);
        $places = $this->rng->shuffleArray(self::CLUB_PLACE_NAMES);

        $clubs = [];
        for ($i = 0; $i < $count; $i++) {
            $prefix = $prefixes[$i % count($prefixes)];
            $place = $places[$i % count($places)];
            $clubs[] = [
                'name' => sprintf('%s %s (Démo)', $prefix, $place),
                'fifa_connect_id' => sprintf('DEMO-CLUB-%03d', $i + 1),
            ];
        }

        return $clubs;
    }

    /**
     * Effectif d'un club : ~20 joueurs suivant PositionCatalog::SQUAD_TEMPLATE,
     * chacun avec un "niveau" [0,1] propre au joueur (dérivé de la graine,
     * PAS persisté en base — mandat : "un niveau propre à chaque joueur").
     *
     * @return array<int, array{name:string, first_name:string, last_name:string, detailed_position:string, broad_group:string, skill:float, jersey_number:int}>
     */
    public function buildSquad(): array
    {
        $players = [];
        $jersey = 1;
        foreach (PositionCatalog::SQUAD_TEMPLATE as $detailedPosition => $count) {
            for ($i = 0; $i < $count; $i++) {
                $firstName = $this->rng->pick(self::FIRST_NAMES);
                $lastName = $this->rng->pick(self::LAST_NAMES);
                // Niveau centré sur 0.5, écart-type 0.15, borné [0.1, 0.95] :
                // donne une variance de qualité au sein de l'effectif sans
                // joueur "parfait" ni "nul".
                $skill = max(0.1, min(0.95, 0.5 + $this->rng->gaussianNoise(0.15)));
                $players[] = [
                    'name' => $firstName.' '.$lastName,
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'detailed_position' => $detailedPosition,
                    'broad_group' => PositionCatalog::BROAD_GROUP[$detailedPosition],
                    'skill' => $skill,
                    'jersey_number' => $jersey,
                ];
                $jersey++;
            }
        }

        return $players;
    }
}
