<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Table de référentiel des postes détaillés (Livrable 1, §9).
     * Ne modifie ni ne supprime rien d'existant : players.position et
     * player_match_detailed_stats/match_participations.detailed_position
     * restent deux colonnes distinctes de cette table.
     */
    public function up(): void
    {
        Schema::create('position_catalog', function (Blueprint $table) {
            $table->string('code')->primary();
            $table->string('label_fr');
            $table->string('label_en');
            $table->string('family');
            $table->string('broad_group'); // GK / DEF / MID / FWD
            $table->unsignedSmallInteger('display_order')->nullable();
            $table->timestamps();
        });

        // Correspondance des 12 codes fournis dans le mandat (Livrable 1, §9).
        // Hypothèses documentées dans docs/role-evaluation/01-schema-cible.md :
        // - LAM/RAM classés en famille "ailier" / regroupement large "MID" (à confirmer).
        // - Aucun code supplémentaire (RCM, LCAM, ST, SW...) n'a été ajouté de ma propre initiative :
        //   la table reste extensible, seuls les 12 codes explicitement communiqués sont insérés ici.
        $rows = [
            ['GK',   'Gardien',              'Goalkeeper',            'gardien',            'GK',  10],
            ['LCB',  'Défenseur central gauche', 'Left Centre-Back',  'défenseur central',  'DEF', 20],
            ['RCB',  'Défenseur central droit',  'Right Centre-Back', 'défenseur central',  'DEF', 21],
            ['LB',   'Latéral gauche',       'Left Back',             'latéral',            'DEF', 30],
            ['RB',   'Latéral droit',        'Right Back',            'latéral',            'DEF', 31],
            ['CDM',  'Milieu défensif',      'Central Defensive Midfielder', 'milieu défensif', 'MID', 40],
            ['LCM',  'Milieu relayeur',      'Left Centre Midfielder', 'milieu relayeur',   'MID', 50],
            ['CAM',  'Milieu offensif',      'Central Attacking Midfielder', 'milieu offensif', 'MID', 60],
            ['RCAM', 'Milieu offensif droit', 'Right Centre Attacking Midfielder', 'milieu offensif', 'MID', 61],
            ['LAM',  'Ailier gauche',        'Left Attacking Midfielder', 'ailier',          'MID', 70],
            ['RAM',  'Ailier droit',         'Right Attacking Midfielder', 'ailier',         'MID', 71],
            ['CF',   'Avant-centre',         'Centre Forward',        'avant-centre',       'FWD', 80],
        ];

        $now = now();
        foreach ($rows as [$code, $labelFr, $labelEn, $family, $broadGroup, $order]) {
            DB::table('position_catalog')->insert([
                'code' => $code,
                'label_fr' => $labelFr,
                'label_en' => $labelEn,
                'family' => $family,
                'broad_group' => $broadGroup,
                'display_order' => $order,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('position_catalog');
    }
};
