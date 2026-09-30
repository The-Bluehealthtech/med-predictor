<?php

namespace App\Services\RoleEvaluationDemo;

/**
 * Données du référentiel des 12 postes détaillés (Livrable 1, §9,
 * `position_catalog`) dupliquées ici en constantes PHP : le générateur en a
 * besoin pour construire les effectifs et compositions en mémoire avant
 * d'écrire quoi que ce soit, indépendamment de l'état des migrations sur la
 * base cible au moment de l'exécution (la table `position_catalog` reste la
 * source de vérité pour l'affichage/les jointures ; ces constantes ne la
 * dupliquent que pour la logique de génération).
 */
class PositionCatalog
{
    public const CODES = ['GK', 'LCB', 'RCB', 'LB', 'RB', 'CDM', 'LCM', 'CAM', 'RCAM', 'LAM', 'RAM', 'CF'];

    public const BROAD_GROUP = [
        'GK' => 'GK',
        'LCB' => 'DEF', 'RCB' => 'DEF', 'LB' => 'DEF', 'RB' => 'DEF',
        'CDM' => 'MID', 'LCM' => 'MID', 'CAM' => 'MID', 'RCAM' => 'MID', 'LAM' => 'MID', 'RAM' => 'MID',
        'CF' => 'FWD',
    ];

    /**
     * Repli vers le vocabulaire RESTREINT déjà en place sur
     * player_match_detailed_stats.position_played (contrainte CHECK
     * pré-existante en base, antérieure à ce mandat — cf.
     * RowValidator::LEGACY_POSITION_CODES du Livrable 2). Correspondance
     * many-to-one assumée (ex. LCB et RCB donnent tous les deux "CB") :
     * décision documentée dans le rapport Livrable 3, pas de round-trip
     * possible vers le code détaillé d'origine à partir de cette seule
     * colonne historique.
     */
    public const TO_LEGACY = [
        'GK' => 'GK',
        'LCB' => 'CB', 'RCB' => 'CB',
        'LB' => 'LB', 'RB' => 'RB',
        'CDM' => 'DM',
        'LCM' => 'CM',
        'CAM' => 'AM', 'RCAM' => 'AM',
        'LAM' => 'LW', 'RAM' => 'RW',
        'CF' => 'ST',
    ];

    /**
     * Gabarit d'effectif ~20 joueurs par équipe (mandat Livrable 3 :
     * "effectifs d'environ 20 joueurs"). Fixe UNIQUEMENT combien de joueurs
     * par poste détaillé sont recrutés dans l'effectif — pas quels matchs ils
     * jouent (aucun produit cartésien joueurs × matchs n'en découle, voir
     * DemoDataGenerator).
     */
    public const SQUAD_TEMPLATE = [
        'GK' => 2,
        'LCB' => 2, 'RCB' => 2, 'LB' => 2, 'RB' => 2,
        'CDM' => 2, 'LCM' => 2,
        'CAM' => 1, 'RCAM' => 1, 'LAM' => 1, 'RAM' => 1,
        'CF' => 2,
    ]; // total = 20

    /**
     * Onze de départ type (4-3-3), un joueur par poste de cette liste.
     * RCAM reste volontairement hors du onze type : il complète le banc
     * (rotation offensive), cf. rapport Livrable 3.
     */
    public const STARTING_ELEVEN_SLOTS = ['GK', 'LCB', 'RCB', 'LB', 'RB', 'CDM', 'LCM', 'CAM', 'LAM', 'RAM', 'CF'];

    public static function squadSize(): int
    {
        return array_sum(self::SQUAD_TEMPLATE);
    }
}
