<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('player_sdoh_data', function (Blueprint $table) {
            $table->id();
            $table->foreignId('player_id')->constrained('joueurs')->onDelete('cascade');
            $table->date('assessment_date')->comment('Date de l\'évaluation SDOH');
            $table->enum('assessment_type', ['initial', 'quarterly', 'annual', 'event_based'])->default('initial');
            $table->foreignId('assessed_by')->nullable()->constrained('users')->onDelete('set null');
            
            // === ENVIRONNEMENT DE VIE ===
            $table->decimal('living_environment_score', 5, 2)->nullable()->comment('Score environnement de vie (0-100)');
            $table->enum('housing_quality', ['excellent', 'good', 'adequate', 'poor', 'inadequate'])->nullable();
            $table->enum('housing_stability', ['stable', 'moderately_stable', 'unstable', 'homeless'])->nullable();
            $table->boolean('safe_neighborhood')->nullable()->comment('Quartier sûr');
            $table->boolean('access_to_green_spaces')->nullable()->comment('Accès aux espaces verts');
            $table->boolean('air_quality_good')->nullable()->comment('Bonne qualité de l\'air');
            $table->boolean('noise_pollution_low')->nullable()->comment('Faible pollution sonore');
            $table->boolean('access_to_public_transport')->nullable()->comment('Accès aux transports publics');
            $table->integer('commute_time_minutes')->nullable()->comment('Temps de trajet en minutes');
            $table->text('living_environment_notes')->nullable()->comment('Notes sur l\'environnement');
            
            // === SOUTIEN SOCIAL ===
            $table->decimal('social_support_score', 5, 2)->nullable()->comment('Score soutien social (0-100)');
            $table->integer('family_members_nearby')->nullable()->comment('Membres de famille à proximité');
            $table->integer('close_friends_count')->nullable()->comment('Nombre d\'amis proches');
            $table->boolean('has_emotional_support')->nullable()->comment('Soutien émotionnel disponible');
            $table->boolean('has_practical_support')->nullable()->comment('Soutien pratique disponible');
            $table->boolean('involved_in_community')->nullable()->comment('Impliqué dans la communauté');
            $table->boolean('has_mentor')->nullable()->comment('A un mentor');
            $table->enum('relationship_status', ['single', 'in_relationship', 'married', 'divorced', 'widowed'])->nullable();
            $table->boolean('has_children')->nullable()->comment('A des enfants');
            $table->text('social_support_notes')->nullable()->comment('Notes sur le soutien social');
            
            // === ACCÈS AUX SOINS ===
            $table->decimal('healthcare_access_score', 5, 2)->nullable()->comment('Score accès aux soins (0-100)');
            $table->boolean('has_health_insurance')->nullable()->comment('A une assurance santé');
            $table->enum('insurance_quality', ['excellent', 'good', 'adequate', 'poor', 'none'])->nullable();
            $table->integer('distance_to_hospital_km')->nullable()->comment('Distance à l\'hôpital en km');
            $table->integer('distance_to_pharmacy_km')->nullable()->comment('Distance à la pharmacie en km');
            $table->boolean('has_primary_care_physician')->nullable()->comment('A un médecin traitant');
            $table->boolean('can_afford_medications')->nullable()->comment('Peut se payer les médicaments');
            $table->boolean('has_access_to_specialists')->nullable()->comment('Accès aux spécialistes');
            $table->boolean('has_access_to_mental_health')->nullable()->comment('Accès à la santé mentale');
            $table->text('healthcare_access_notes')->nullable()->comment('Notes sur l\'accès aux soins');
            
            // === SITUATION FINANCIÈRE ===
            $table->decimal('financial_situation_score', 5, 2)->nullable()->comment('Score situation financière (0-100)');
            $table->enum('income_level', ['very_low', 'low', 'middle', 'high', 'very_high'])->nullable();
            $table->boolean('has_stable_income')->nullable()->comment('A un revenu stable');
            $table->boolean('can_afford_basic_needs')->nullable()->comment('Peut se payer les besoins de base');
            $table->boolean('has_emergency_savings')->nullable()->comment('A des économies d\'urgence');
            $table->boolean('has_debt')->nullable()->comment('A des dettes');
            $table->enum('debt_level', ['none', 'low', 'moderate', 'high', 'crippling'])->nullable();
            $table->boolean('has_financial_advisor')->nullable()->comment('A un conseiller financier');
            $table->boolean('can_afford_luxury_items')->nullable()->comment('Peut se payer des articles de luxe');
            $table->text('financial_situation_notes')->nullable()->comment('Notes sur la situation financière');
            
            // === BIEN-ÊTRE MENTAL ===
            $table->decimal('mental_wellbeing_score', 5, 2)->nullable()->comment('Score bien-être mental (0-100)');
            $table->boolean('has_mental_health_history')->nullable()->comment('A des antécédents de santé mentale');
            $table->boolean('currently_in_therapy')->nullable()->comment('Actuellement en thérapie');
            $table->boolean('takes_psychiatric_medications')->nullable()->comment('Prend des médicaments psychiatriques');
            $table->enum('stress_level', ['very_low', 'low', 'moderate', 'high', 'very_high'])->nullable();
            $table->enum('anxiety_level', ['very_low', 'low', 'moderate', 'high', 'very_high'])->nullable();
            $table->enum('depression_level', ['very_low', 'low', 'moderate', 'high', 'very_high'])->nullable();
            $table->boolean('has_sleep_disorders')->nullable()->comment('A des troubles du sommeil');
            $table->boolean('has_eating_disorders')->nullable()->comment('A des troubles alimentaires');
            $table->text('mental_wellbeing_notes')->nullable()->comment('Notes sur le bien-être mental');
            
            // === ÉDUCATION ET EMPLOI ===
            $table->enum('education_level', ['none', 'primary', 'secondary', 'bachelor', 'master', 'doctorate'])->nullable();
            $table->boolean('currently_studying')->nullable()->comment('Étudie actuellement');
            $table->enum('employment_status', ['employed', 'unemployed', 'student', 'retired', 'disabled'])->nullable();
            $table->boolean('job_satisfaction')->nullable()->comment('Satisfait de son travail');
            $table->boolean('work_life_balance')->nullable()->comment('Équilibre travail-vie personnelle');
            $table->integer('work_hours_per_week')->nullable()->comment('Heures de travail par semaine');
            $table->boolean('has_career_goals')->nullable()->comment('A des objectifs de carrière');
            $table->text('education_employment_notes')->nullable()->comment('Notes sur l\'éducation et l\'emploi');
            
            // === COMPORTEMENTS DE SANTÉ ===
            $table->enum('smoking_status', ['never', 'former', 'current', 'occasional'])->nullable();
            $table->integer('cigarettes_per_day')->nullable()->comment('Cigarettes par jour');
            $table->enum('alcohol_consumption', ['none', 'light', 'moderate', 'heavy', 'excessive'])->nullable();
            $table->integer('drinks_per_week')->nullable()->comment('Verres par semaine');
            $table->boolean('uses_recreational_drugs')->nullable()->comment('Utilise des drogues récréatives');
            $table->boolean('exercises_regularly')->nullable()->comment('Fait de l\'exercice régulièrement');
            $table->integer('exercise_hours_per_week')->nullable()->comment('Heures d\'exercice par semaine');
            $table->boolean('has_healthy_diet')->nullable()->comment('A une alimentation saine');
            $table->text('health_behaviors_notes')->nullable()->comment('Notes sur les comportements de santé');
            
            // === SCORES COMPOSITES ===
            $table->decimal('overall_sdoh_score', 5, 2)->nullable()->comment('Score SDOH global (0-100)');
            $table->enum('risk_category', ['very_low', 'low', 'moderate', 'high', 'very_high'])->nullable();
            $table->json('priority_areas')->nullable()->comment('Zones prioritaires d\'intervention');
            $table->json('recommendations')->nullable()->comment('Recommandations d\'amélioration');
            
            // === MÉTADONNÉES ===
            $table->enum('assessment_method', ['interview', 'questionnaire', 'observation', 'medical_record', 'self_report'])->default('questionnaire');
            $table->decimal('assessment_reliability', 5, 2)->default(100)->comment('Fiabilité de l\'évaluation en %');
            $table->boolean('requires_follow_up')->default(false)->comment('Nécessite un suivi');
            $table->date('next_assessment_date')->nullable()->comment('Date de la prochaine évaluation');
            $table->text('general_notes')->nullable()->comment('Notes générales');
            $table->json('metadata')->nullable()->comment('Métadonnées supplémentaires');
            
            $table->timestamps();
            
            // Index pour optimiser les requêtes
            $table->index(['player_id', 'assessment_date']);
            $table->index(['overall_sdoh_score', 'risk_category']);
            $table->index(['assessment_type', 'assessment_date']);
            $table->index(['requires_follow_up', 'next_assessment_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('player_sdoh_data');
    }
};












