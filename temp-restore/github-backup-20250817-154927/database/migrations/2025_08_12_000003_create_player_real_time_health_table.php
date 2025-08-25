<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('player_real_time_health', function (Blueprint $table) {
            $table->id();
            $table->foreignId('player_id')->constrained('joueurs')->onDelete('cascade');
            $table->foreignId('device_id')->nullable()->constrained('player_connected_devices')->onDelete('set null');
            $table->timestamp('measurement_time')->comment('Heure de la mesure');
            $table->enum('data_source', ['smartwatch', 'smartphone', 'medical_device', 'manual_entry', 'fifa_connect'])->default('smartwatch');
            
            // === SIGNAUX VITAUX ===
            $table->integer('heart_rate')->nullable()->comment('Fréquence cardiaque en bpm');
            $table->string('blood_pressure_systolic')->nullable()->comment('Tension artérielle systolique');
            $table->string('blood_pressure_diastolic')->nullable()->comment('Tension artérielle diastolique');
            $table->decimal('temperature', 4, 1)->nullable()->comment('Température en °C');
            $table->integer('oxygen_saturation')->nullable()->comment('Saturation en O2 en %');
            $table->integer('hydration_level')->nullable()->comment('Niveau d\'hydratation en %');
            $table->decimal('cortisol_level', 6, 2)->nullable()->comment('Niveau de cortisol en µg/dL');
            $table->integer('respiratory_rate')->nullable()->comment('Fréquence respiratoire en resp/min');
            
            // === MÉTRIQUES PHYSIQUES ===
            $table->decimal('weight_kg', 5, 2)->nullable()->comment('Poids en kg');
            $table->decimal('body_fat_percentage', 5, 2)->nullable()->comment('Pourcentage de graisse corporelle');
            $table->decimal('muscle_mass_kg', 5, 2)->nullable()->comment('Masse musculaire en kg');
            $table->decimal('bone_mass_kg', 5, 2)->nullable()->comment('Masse osseuse en kg');
            $table->decimal('water_percentage', 5, 2)->nullable()->comment('Pourcentage d\'eau');
            $table->decimal('visceral_fat_level', 5, 2)->nullable()->comment('Niveau de graisse viscérale');
            $table->decimal('bmi', 4, 2)->nullable()->comment('Indice de masse corporelle');
            
            // === PERFORMANCE CARDIOVASCULAIRE ===
            $table->decimal('vo2_max', 5, 2)->nullable()->comment('VO2 max en ml/kg/min');
            $table->integer('max_heart_rate')->nullable()->comment('Fréquence cardiaque maximale');
            $table->integer('resting_heart_rate')->nullable()->comment('Fréquence cardiaque au repos');
            $table->integer('heart_rate_variability')->nullable()->comment('Variabilité de la fréquence cardiaque en ms');
            $table->decimal('cardiac_output', 5, 2)->nullable()->comment('Débit cardiaque en L/min');
            $table->decimal('stroke_volume', 5, 2)->nullable()->comment('Volume d\'éjection systolique en ml');
            
            // === MÉTRIQUES DE SOMMEIL ===
            $table->integer('sleep_duration_hours')->nullable()->comment('Durée du sommeil en heures');
            $table->integer('deep_sleep_percentage')->nullable()->comment('Pourcentage de sommeil profond');
            $table->integer('rem_sleep_percentage')->nullable()->comment('Pourcentage de sommeil REM');
            $table->integer('light_sleep_percentage')->nullable()->comment('Pourcentage de sommeil léger');
            $table->integer('sleep_efficiency')->nullable()->comment('Efficacité du sommeil en %');
            $table->integer('sleep_latency')->nullable()->comment('Latence d\'endormissement en minutes');
            $table->integer('awakenings_count')->nullable()->comment('Nombre de réveils');
            $table->decimal('sleep_quality_score', 3, 1)->nullable()->comment('Score de qualité du sommeil (0-10)');
            
            // === MÉTRIQUES DE STRESS ET BIEN-ÊTRE ===
            $table->decimal('stress_level', 5, 2)->nullable()->comment('Niveau de stress (0-100)');
            $table->decimal('anxiety_level', 5, 2)->nullable()->comment('Niveau d\'anxiété (0-100)');
            $table->decimal('mood_score', 3, 1)->nullable()->comment('Score d\'humeur (0-10)');
            $table->decimal('energy_level', 5, 2)->nullable()->comment('Niveau d\'énergie (0-100)');
            $table->decimal('focus_score', 5, 2)->nullable()->comment('Score de concentration (0-100)');
            $table->decimal('recovery_score', 5, 2)->nullable()->comment('Score de récupération (0-100)');
            
            // === MÉTRIQUES D'ACTIVITÉ ===
            $table->integer('steps_count')->nullable()->comment('Nombre de pas');
            $table->decimal('calories_burned', 6, 2)->nullable()->comment('Calories brûlées');
            $table->decimal('active_minutes', 5, 2)->nullable()->comment('Minutes d\'activité');
            $table->decimal('exercise_minutes', 5, 2)->nullable()->comment('Minutes d\'exercice');
            $table->integer('stand_hours')->nullable()->comment('Heures debout');
            $table->decimal('distance_walked_km', 6, 2)->nullable()->comment('Distance marchée en km');
            
            // === MÉTRIQUES DE RÉCUPÉRATION ===
            $table->decimal('muscle_fatigue', 5, 2)->nullable()->comment('Fatigue musculaire (0-100)');
            $table->decimal('central_fatigue', 5, 2)->nullable()->comment('Fatigue centrale (0-100)');
            $table->decimal('peripheral_fatigue', 5, 2)->nullable()->comment('Fatigue périphérique (0-100)');
            $table->integer('recovery_time_hours')->nullable()->comment('Temps de récupération en heures');
            $table->decimal('readiness_score', 5, 2)->nullable()->comment('Score de préparation (0-100)');
            
            // === ALERTES ET NOTIFICATIONS ===
            $table->boolean('high_heart_rate_alert')->default(false)->comment('Alerte fréquence cardiaque élevée');
            $table->boolean('low_oxygen_alert')->default(false)->comment('Alerte saturation O2 faible');
            $table->boolean('high_temperature_alert')->default(false)->comment('Alerte température élevée');
            $table->boolean('dehydration_alert')->default(false)->comment('Alerte déshydratation');
            $table->boolean('stress_alert')->default(false)->comment('Alerte stress élevé');
            $table->json('alert_details')->nullable()->comment('Détails des alertes');
            
            // === MÉTADONNÉES ===
            $table->decimal('data_accuracy', 5, 2)->default(100)->comment('Précision des données en %');
            $table->enum('data_quality', ['excellent', 'good', 'fair', 'poor'])->default('good');
            $table->json('raw_data')->nullable()->comment('Données brutes de l\'appareil');
            $table->text('notes')->nullable()->comment('Notes médicales');
            $table->json('metadata')->nullable()->comment('Métadonnées supplémentaires');
            
            $table->timestamps();
            
            // Index pour optimiser les requêtes
            $table->index(['player_id', 'measurement_time']);
            $table->index(['device_id', 'measurement_time']);
            $table->index(['data_source', 'measurement_time']);
            $table->index(['heart_rate', 'measurement_time']);
            $table->index(['temperature', 'measurement_time']);
            $table->index(['oxygen_saturation', 'measurement_time']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('player_real_time_health');
    }
};








