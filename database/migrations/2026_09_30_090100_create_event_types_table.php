<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Référentiel des types d'événements de match (Livrable 1, §4).
     * N'ajoute rien de structurel à match_events elle-même : voir la migration
     * dédiée add_event_extension_columns_to_match_events_table pour les colonnes.
     * Les colonnes existantes "type" et "event_type" de match_events restent
     * inchangées ; ce référentiel sert de source de vérité pour leurs valeurs
     * contrôlées côté application (pas de contrainte SQL FK ajoutée sur un
     * varchar existant, pour rester non destructif).
     */
    public function up(): void
    {
        Schema::create('event_types', function (Blueprint $table) {
            $table->string('code')->primary();
            $table->string('label_fr');
            $table->string('label_en');
            $table->timestamps();
        });

        $rows = [
            ['goal', 'But', 'Goal'],
            ['own_goal', 'But contre son camp', 'Own goal'],
            ['yellow_card', 'Carton jaune', 'Yellow card'],
            ['second_yellow_card', 'Deuxième carton jaune', 'Second yellow card'],
            ['red_card', 'Carton rouge', 'Red card'],
            ['substitution', 'Remplacement', 'Substitution'],
            ['pass', 'Passe', 'Pass'],
        ];

        $now = now();
        foreach ($rows as [$code, $labelFr, $labelEn]) {
            DB::table('event_types')->insert([
                'code' => $code,
                'label_fr' => $labelFr,
                'label_en' => $labelEn,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('event_types');
    }
};
