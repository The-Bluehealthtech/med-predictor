@extends('layouts.app')

@section('title', 'Nouvelle Performance')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-6">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Nouvelle Performance</h1>
                    <p class="mt-1 text-sm text-gray-500">Enregistrer une nouvelle évaluation de performance</p>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('performances.index') }}" 
                       class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Retour
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Form -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <form id="performanceForm" class="space-y-8">
            @csrf
            
            <!-- Basic Information -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Informations de base</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <div>
                            <label for="player_id" class="block text-sm font-medium text-gray-700">Joueur *</label>
                            <select name="player_id" id="player_id" required
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                <option value="">Sélectionner un joueur</option>
                                @foreach($players as $player)
                                    <option value="{{ $player->id }}">{{ $player->first_name }} {{ $player->last_name }} ({{ $player->fifa_id }})</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="performance_date" class="block text-sm font-medium text-gray-700">Date de performance *</label>
                            <input type="datetime-local" name="performance_date" id="performance_date" required
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        </div>

                        <div>
                            <label for="data_source" class="block text-sm font-medium text-gray-700">Source de données *</label>
                            <select name="data_source" id="data_source" required
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                <option value="assessment">Évaluation</option>
                                <option value="match">Match</option>
                                <option value="training">Entraînement</option>
                                <option value="fifa_connect">FIFA Connect</option>
                                <option value="medical_device">Dispositif médical</option>
                            </select>
                        </div>

                        <div>
                            <label for="competition_id" class="block text-sm font-medium text-gray-700">Compétition</label>
                            <select name="competition_id" id="competition_id"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                <option value="">Aucune compétition</option>
                                @foreach($competitions ?? [] as $competition)
                                    <option value="{{ $competition->id }}">{{ $competition->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="team_id" class="block text-sm font-medium text-gray-700">Équipe</label>
                            <select name="team_id" id="team_id"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                <option value="">Aucune équipe</option>
                                @foreach($teams ?? [] as $team)
                                    <option value="{{ $team->id }}">{{ $team->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="assessment_method" class="block text-sm font-medium text-gray-700">Méthode d'évaluation</label>
                            <select name="assessment_method" id="assessment_method"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                <option value="coach_evaluation">Évaluation entraîneur</option>
                                <option value="ai_analysis">Analyse IA</option>
                                <option value="sensor_data">Données capteurs</option>
                                <option value="video_analysis">Analyse vidéo</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Physical Performance -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Performance Physique</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <div>
                            <label for="physical_score" class="block text-sm font-medium text-gray-700">Score physique global</label>
                            <input type="number" name="physical_score" id="physical_score" step="0.01" min="0" max="100"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                   placeholder="0-100">
                        </div>

                        <div>
                            <label for="endurance_score" class="block text-sm font-medium text-gray-700">Endurance</label>
                            <input type="number" name="endurance_score" id="endurance_score" step="0.01" min="0" max="100"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                   placeholder="0-100">
                        </div>

                        <div>
                            <label for="strength_score" class="block text-sm font-medium text-gray-700">Force</label>
                            <input type="number" name="strength_score" id="strength_score" step="0.01" min="0" max="100"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                   placeholder="0-100">
                        </div>

                        <div>
                            <label for="speed_score" class="block text-sm font-medium text-gray-700">Vitesse</label>
                            <input type="number" name="speed_score" id="speed_score" step="0.01" min="0" max="100"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                   placeholder="0-100">
                        </div>

                        <div>
                            <label for="agility_score" class="block text-sm font-medium text-gray-700">Agilité</label>
                            <input type="number" name="agility_score" id="agility_score" step="0.01" min="0" max="100"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                   placeholder="0-100">
                        </div>

                        <div>
                            <label for="recovery_time" class="block text-sm font-medium text-gray-700">Temps de récupération (min)</label>
                            <input type="number" name="recovery_time" id="recovery_time" min="0"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                   placeholder="Minutes">
                        </div>

                        <div>
                            <label for="fatigue_level" class="block text-sm font-medium text-gray-700">Niveau de fatigue</label>
                            <input type="number" name="fatigue_level" id="fatigue_level" step="0.01" min="0" max="100"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                   placeholder="0-100">
                        </div>

                        <div>
                            <label for="muscle_mass" class="block text-sm font-medium text-gray-700">Masse musculaire (kg)</label>
                            <input type="number" name="muscle_mass" id="muscle_mass" step="0.01" min="0"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                   placeholder="kg">
                        </div>

                        <div>
                            <label for="body_fat_percentage" class="block text-sm font-medium text-gray-700">% de graisse corporelle</label>
                            <input type="number" name="body_fat_percentage" id="body_fat_percentage" step="0.01" min="0" max="100"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                   placeholder="%">
                        </div>

                        <div>
                            <label for="vo2_max" class="block text-sm font-medium text-gray-700">VO2 Max (ml/kg/min)</label>
                            <input type="number" name="vo2_max" id="vo2_max" step="0.01" min="0"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                   placeholder="ml/kg/min">
                        </div>

                        <div>
                            <label for="lactate_threshold" class="block text-sm font-medium text-gray-700">Seuil lactique (mmol/L)</label>
                            <input type="number" name="lactate_threshold" id="lactate_threshold" step="0.01" min="0"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                   placeholder="mmol/L">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Technical Performance -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Performance Technique</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <div>
                            <label for="technical_score" class="block text-sm font-medium text-gray-700">Score technique global</label>
                            <input type="number" name="technical_score" id="technical_score" step="0.01" min="0" max="100"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                   placeholder="0-100">
                        </div>

                        <div>
                            <label for="passing_accuracy" class="block text-sm font-medium text-gray-700">Précision des passes</label>
                            <input type="number" name="passing_accuracy" id="passing_accuracy" step="0.01" min="0" max="100"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                   placeholder="0-100">
                        </div>

                        <div>
                            <label for="shooting_accuracy" class="block text-sm font-medium text-gray-700">Précision des tirs</label>
                            <input type="number" name="shooting_accuracy" id="shooting_accuracy" step="0.01" min="0" max="100"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                   placeholder="0-100">
                        </div>

                        <div>
                            <label for="dribbling_skill" class="block text-sm font-medium text-gray-700">Dribble</label>
                            <input type="number" name="dribbling_skill" id="dribbling_skill" step="0.01" min="0" max="100"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                   placeholder="0-100">
                        </div>

                        <div>
                            <label for="tackling_effectiveness" class="block text-sm font-medium text-gray-700">Efficacité des tacles</label>
                            <input type="number" name="tackling_effectiveness" id="tackling_effectiveness" step="0.01" min="0" max="100"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                   placeholder="0-100">
                        </div>

                        <div>
                            <label for="heading_accuracy" class="block text-sm font-medium text-gray-700">Précision des têtes</label>
                            <input type="number" name="heading_accuracy" id="heading_accuracy" step="0.01" min="0" max="100"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                   placeholder="0-100">
                        </div>

                        <div>
                            <label for="crossing_accuracy" class="block text-sm font-medium text-gray-700">Précision des centres</label>
                            <input type="number" name="crossing_accuracy" id="crossing_accuracy" step="0.01" min="0" max="100"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                   placeholder="0-100">
                        </div>

                        <div>
                            <label for="free_kick_accuracy" class="block text-sm font-medium text-gray-700">Précision des coups francs</label>
                            <input type="number" name="free_kick_accuracy" id="free_kick_accuracy" step="0.01" min="0" max="100"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                   placeholder="0-100">
                        </div>

                        <div>
                            <label for="penalty_accuracy" class="block text-sm font-medium text-gray-700">Précision des penalties</label>
                            <input type="number" name="penalty_accuracy" id="penalty_accuracy" step="0.01" min="0" max="100"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                   placeholder="0-100">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tactical Performance -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Performance Tactique</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <div>
                            <label for="tactical_score" class="block text-sm font-medium text-gray-700">Score tactique global</label>
                            <input type="number" name="tactical_score" id="tactical_score" step="0.01" min="0" max="100"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                   placeholder="0-100">
                        </div>

                        <div>
                            <label for="positioning_awareness" class="block text-sm font-medium text-gray-700">Conscience positionnelle</label>
                            <input type="number" name="positioning_awareness" id="positioning_awareness" step="0.01" min="0" max="100"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                   placeholder="0-100">
                        </div>

                        <div>
                            <label for="decision_making" class="block text-sm font-medium text-gray-700">Prise de décision</label>
                            <input type="number" name="decision_making" id="decision_making" step="0.01" min="0" max="100"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                   placeholder="0-100">
                        </div>

                        <div>
                            <label for="game_intelligence" class="block text-sm font-medium text-gray-700">Intelligence de jeu</label>
                            <input type="number" name="game_intelligence" id="game_intelligence" step="0.01" min="0" max="100"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                   placeholder="0-100">
                        </div>

                        <div>
                            <label for="team_work_rate" class="block text-sm font-medium text-gray-700">Taux de travail d'équipe</label>
                            <input type="number" name="team_work_rate" id="team_work_rate" step="0.01" min="0" max="100"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                   placeholder="0-100">
                        </div>

                        <div>
                            <label for="pressing_intensity" class="block text-sm font-medium text-gray-700">Intensité du pressing</label>
                            <input type="number" name="pressing_intensity" id="pressing_intensity" step="0.01" min="0" max="100"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                   placeholder="0-100">
                        </div>

                        <div>
                            <label for="defensive_organization" class="block text-sm font-medium text-gray-700">Organisation défensive</label>
                            <input type="number" name="defensive_organization" id="defensive_organization" step="0.01" min="0" max="100"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                   placeholder="0-100">
                        </div>

                        <div>
                            <label for="attacking_movement" class="block text-sm font-medium text-gray-700">Mouvement offensif</label>
                            <input type="number" name="attacking_movement" id="attacking_movement" step="0.01" min="0" max="100"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                   placeholder="0-100">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Mental Performance -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Performance Mentale</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <div>
                            <label for="mental_score" class="block text-sm font-medium text-gray-700">Score mental global</label>
                            <input type="number" name="mental_score" id="mental_score" step="0.01" min="0" max="100"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                   placeholder="0-100">
                        </div>

                        <div>
                            <label for="confidence_level" class="block text-sm font-medium text-gray-700">Niveau de confiance</label>
                            <input type="number" name="confidence_level" id="confidence_level" step="0.01" min="0" max="100"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                   placeholder="0-100">
                        </div>

                        <div>
                            <label for="stress_management" class="block text-sm font-medium text-gray-700">Gestion du stress</label>
                            <input type="number" name="stress_management" id="stress_management" step="0.01" min="0" max="100"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                   placeholder="0-100">
                        </div>

                        <div>
                            <label for="focus_concentration" class="block text-sm font-medium text-gray-700">Focus et concentration</label>
                            <input type="number" name="focus_concentration" id="focus_concentration" step="0.01" min="0" max="100"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                   placeholder="0-100">
                        </div>

                        <div>
                            <label for="motivation_level" class="block text-sm font-medium text-gray-700">Niveau de motivation</label>
                            <input type="number" name="motivation_level" id="motivation_level" step="0.01" min="0" max="100"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                   placeholder="0-100">
                        </div>

                        <div>
                            <label for="leadership_qualities" class="block text-sm font-medium text-gray-700">Qualités de leadership</label>
                            <input type="number" name="leadership_qualities" id="leadership_qualities" step="0.01" min="0" max="100"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                   placeholder="0-100">
                        </div>

                        <div>
                            <label for="pressure_handling" class="block text-sm font-medium text-gray-700">Gestion de la pression</label>
                            <input type="number" name="pressure_handling" id="pressure_handling" step="0.01" min="0" max="100"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                   placeholder="0-100">
                        </div>

                        <div>
                            <label for="mental_toughness" class="block text-sm font-medium text-gray-700">Résistance mentale</label>
                            <input type="number" name="mental_toughness" id="mental_toughness" step="0.01" min="0" max="100"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                   placeholder="0-100">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Social Performance -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Performance Sociale</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <div>
                            <label for="social_score" class="block text-sm font-medium text-gray-700">Score social global</label>
                            <input type="number" name="social_score" id="social_score" step="0.01" min="0" max="100"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                   placeholder="0-100">
                        </div>

                        <div>
                            <label for="team_cohesion" class="block text-sm font-medium text-gray-700">Cohésion d'équipe</label>
                            <input type="number" name="team_cohesion" id="team_cohesion" step="0.01" min="0" max="100"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                   placeholder="0-100">
                        </div>

                        <div>
                            <label for="communication_skills" class="block text-sm font-medium text-gray-700">Compétences communication</label>
                            <input type="number" name="communication_skills" id="communication_skills" step="0.01" min="0" max="100"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                   placeholder="0-100">
                        </div>

                        <div>
                            <label for="coachability" class="block text-sm font-medium text-gray-700">Capacité d'apprentissage</label>
                            <input type="number" name="coachability" id="coachability" step="0.01" min="0" max="100"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                   placeholder="0-100">
                        </div>

                        <div>
                            <label for="discipline_level" class="block text-sm font-medium text-gray-700">Niveau de discipline</label>
                            <input type="number" name="discipline_level" id="discipline_level" step="0.01" min="0" max="100"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                   placeholder="0-100">
                        </div>

                        <div>
                            <label for="professional_attitude" class="block text-sm font-medium text-gray-700">Attitude professionnelle</label>
                            <input type="number" name="professional_attitude" id="professional_attitude" step="0.01" min="0" max="100"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                   placeholder="0-100">
                        </div>

                        <div>
                            <label for="media_handling" class="block text-sm font-medium text-gray-700">Gestion médias</label>
                            <input type="number" name="media_handling" id="media_handling" step="0.01" min="0" max="100"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                   placeholder="0-100">
                        </div>

                        <div>
                            <label for="fan_interaction" class="block text-sm font-medium text-gray-700">Interaction fans</label>
                            <input type="number" name="fan_interaction" id="fan_interaction" step="0.01" min="0" max="100"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                   placeholder="0-100">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Analysis and Notes -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Analyse et Notes</h3>
                    <div class="space-y-6">
                        <div>
                            <label for="improvement_areas" class="block text-sm font-medium text-gray-700">Zones d'amélioration</label>
                            <textarea name="improvement_areas" id="improvement_areas" rows="3"
                                      class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                      placeholder="Listez les zones d'amélioration identifiées..."></textarea>
                        </div>

                        <div>
                            <label for="strengths_highlighted" class="block text-sm font-medium text-gray-700">Points forts mis en évidence</label>
                            <textarea name="strengths_highlighted" id="strengths_highlighted" rows="3"
                                      class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                      placeholder="Listez les points forts identifiés..."></textarea>
                        </div>

                        <div>
                            <label for="notes" class="block text-sm font-medium text-gray-700">Notes additionnelles</label>
                            <textarea name="notes" id="notes" rows="4"
                                      class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                      placeholder="Notes, observations, commentaires..."></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex justify-end space-x-3">
                <a href="{{ route('performances.index') }}" 
                   class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Annuler
                </a>
                <button type="submit" 
                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Enregistrer la Performance
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Auto-calculation Modal -->
<div id="calculationModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Calcul automatique</h3>
                <button onclick="closeCalculationModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <div id="calculationContent">
                <div class="text-center">
                    <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto"></div>
                    <p class="mt-4 text-gray-600">Calcul des scores globaux...</p>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.getElementById('performanceForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    // Show calculation modal
    document.getElementById('calculationModal').classList.remove('hidden');
    
    // Auto-calculate overall scores
    calculateOverallScores();
    
    // Submit form
    const formData = new FormData(this);
    
    fetch('{{ route("performances.store") }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.location.href = '{{ route("performances.index") }}';
        } else {
            alert('Erreur lors de l\'enregistrement: ' + data.message);
            closeCalculationModal();
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Erreur lors de l\'enregistrement');
        closeCalculationModal();
    });
});

function calculateOverallScores() {
    // Calculate physical score
    const physicalScores = [
        'endurance_score', 'strength_score', 'speed_score', 'agility_score'
    ].map(id => parseFloat(document.getElementById(id).value) || 0);
    
    const physicalScore = physicalScores.reduce((a, b) => a + b, 0) / physicalScores.length;
    document.getElementById('physical_score').value = physicalScore.toFixed(2);
    
    // Calculate technical score
    const technicalScores = [
        'passing_accuracy', 'shooting_accuracy', 'dribbling_skill', 'tackling_effectiveness',
        'heading_accuracy', 'crossing_accuracy', 'free_kick_accuracy', 'penalty_accuracy'
    ].map(id => parseFloat(document.getElementById(id).value) || 0);
    
    const technicalScore = technicalScores.reduce((a, b) => a + b, 0) / technicalScores.length;
    document.getElementById('technical_score').value = technicalScore.toFixed(2);
    
    // Calculate tactical score
    const tacticalScores = [
        'positioning_awareness', 'decision_making', 'game_intelligence', 'team_work_rate',
        'pressing_intensity', 'defensive_organization', 'attacking_movement'
    ].map(id => parseFloat(document.getElementById(id).value) || 0);
    
    const tacticalScore = tacticalScores.reduce((a, b) => a + b, 0) / tacticalScores.length;
    document.getElementById('tactical_score').value = tacticalScore.toFixed(2);
    
    // Calculate mental score
    const mentalScores = [
        'confidence_level', 'stress_management', 'focus_concentration', 'motivation_level',
        'leadership_qualities', 'pressure_handling', 'mental_toughness'
    ].map(id => parseFloat(document.getElementById(id).value) || 0);
    
    const mentalScore = mentalScores.reduce((a, b) => a + b, 0) / mentalScores.length;
    document.getElementById('mental_score').value = mentalScore.toFixed(2);
    
    // Calculate social score
    const socialScores = [
        'team_cohesion', 'communication_skills', 'coachability', 'discipline_level',
        'professional_attitude', 'media_handling', 'fan_interaction'
    ].map(id => parseFloat(document.getElementById(id).value) || 0);
    
    const socialScore = socialScores.reduce((a, b) => a + b, 0) / socialScores.length;
    document.getElementById('social_score').value = socialScore.toFixed(2);
    
    // Calculate overall performance score
    const weights = {
        physical: 0.25,
        technical: 0.25,
        tactical: 0.20,
        mental: 0.20,
        social: 0.10
    };
    
    const overallScore = (physicalScore * weights.physical) +
                        (technicalScore * weights.technical) +
                        (tacticalScore * weights.tactical) +
                        (mentalScore * weights.mental) +
                        (socialScore * weights.social);
    
    // Add overall score field if it doesn't exist
    if (!document.getElementById('overall_performance_score')) {
        const overallField = document.createElement('input');
        overallField.type = 'hidden';
        overallField.name = 'overall_performance_score';
        overallField.id = 'overall_performance_score';
        overallField.value = overallScore.toFixed(2);
        document.getElementById('performanceForm').appendChild(overallField);
    } else {
        document.getElementById('overall_performance_score').value = overallScore.toFixed(2);
    }
}

function closeCalculationModal() {
    document.getElementById('calculationModal').classList.add('hidden');
}

// Auto-calculate scores when individual scores change
document.addEventListener('DOMContentLoaded', function() {
    const scoreInputs = document.querySelectorAll('input[type="number"]');
    scoreInputs.forEach(input => {
        input.addEventListener('change', calculateOverallScores);
    });
});
</script>
@endpush 