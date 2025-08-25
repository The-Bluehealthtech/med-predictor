<template>
  <div class="pcma-form">
    <div class="max-w-4xl mx-auto p-6">
      <!-- Header -->
      <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Évaluation PCMA</h1>
        <p class="text-gray-600">Formulaire d'évaluation médicale pré-compétition</p>
      </div>

      <!-- AI-Assisted Section -->
      <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 mb-8">
        <div class="flex items-center mb-4">
          <div class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center mr-3">
            <span class="text-white text-sm">🤖</span>
          </div>
          <h2 class="text-xl font-semibold text-gray-900">Remplissage Assisté par IA</h2>
        </div>
        
        <p class="text-gray-700 mb-4">
          Collez le transcript de l'examen médical ci-dessous pour que l'IA pré-remplisse automatiquement le formulaire.
        </p>

        <div class="space-y-4">
          <!-- Transcript Input -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Transcript de l'examen
            </label>
            <textarea
              v-model="transcript"
              rows="4"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              placeholder="Exemple: Patient reports blood pressure is 120 over 80, heart rate is 65 bpm, resting heart rate is 58. No chest pain or shortness of breath. Family history includes heart disease in father..."
            ></textarea>
          </div>

          <!-- AI Processing Controls -->
          <div class="flex items-center space-x-4">
            <button
              @click="prefillWithAI"
              :disabled="!transcript.trim() || aiProcessing"
              class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
            >
              <span v-if="aiProcessing" class="flex items-center">
                <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-white mr-2"></div>
                Traitement IA...
              </span>
              <span v-else>Pré-remplir avec l'IA</span>
            </button>

            <button
              @click="clearTranscript"
              :disabled="!transcript.trim()"
              class="px-4 py-2 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
            >
              Effacer
            </button>
          </div>

          <!-- AI Results -->
          <div v-if="aiResults" class="bg-white border border-gray-200 rounded-lg p-4">
            <div class="flex items-center justify-between mb-3">
              <h3 class="text-lg font-medium text-gray-900">Résultats de l'IA</h3>
              <div class="flex items-center space-x-2">
                <span class="text-sm text-gray-600">Confiance:</span>
                <span class="px-2 py-1 text-xs rounded-full" :class="getConfidenceClass(aiResults.confidence_score)">
                  {{ Math.round(aiResults.confidence_score * 100) }}%
                </span>
              </div>
            </div>
            
            <div class="grid grid-cols-2 md:grid-cols-3 gap-2 text-sm">
              <div v-for="field in aiResults.extracted_fields" :key="field" class="flex items-center">
                <span class="w-2 h-2 bg-green-500 rounded-full mr-2"></span>
                <span class="text-gray-700">{{ formatFieldName(field) }}</span>
              </div>
            </div>

            <div class="mt-3 pt-3 border-t border-gray-200">
              <button
                @click="applyAIResults"
                class="px-3 py-1 bg-green-600 text-white text-sm rounded-md hover:bg-green-700 transition-colors"
              >
                Appliquer les résultats
              </button>
            </div>
          </div>

          <!-- AI Error -->
          <div v-if="aiError" class="bg-red-50 border border-red-200 rounded-lg p-4">
            <div class="flex items-center">
              <span class="text-red-600 mr-2">❌</span>
              <span class="text-red-800">{{ aiError }}</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Manual Form Section -->
      <div class="bg-white border border-gray-200 rounded-lg p-6">
        <h2 class="text-xl font-semibold text-gray-900 mb-6">Formulaire PCMA</h2>

        <form @submit.prevent="submitForm" class="space-y-6">
          <!-- Basic Information -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">
                Type d'évaluation
              </label>
              <select
                v-model="formData.type"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
              >
                <option value="cardio">Cardiovasculaire</option>
                <option value="neurological">Neurologique</option>
                <option value="musculoskeletal">Musculo-squelettique</option>
                <option value="general">Général</option>
              </select>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">
                Évaluateur
              </label>
              <input
                v-model="formData.assessor_name"
                type="text"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                placeholder="Nom de l'évaluateur"
              />
            </div>
          </div>

          <!-- Cardiovascular Section -->
          <div v-if="formData.type === 'cardio'" class="space-y-4">
            <h3 class="text-lg font-medium text-gray-900 border-b border-gray-200 pb-2">
              Évaluation Cardiovasculaire
            </h3>

            <!-- ICD-11 Diagnostic Section -->
            <div class="bg-green-50 border border-green-200 rounded-lg p-4">
              <h4 class="text-md font-medium text-green-900 mb-3">🔍 Diagnostic ICD-11</h4>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                  Diagnostic Cardiovasculaire
                </label>
                <ICDSearchInput
                  v-model="formData.cardiovascular_diagnosis"
                  placeholder="Rechercher un diagnostic cardiovasculaire ICD-11..."
                  @selected="onCardiovascularDiagnosticSelected"
                />
                <div v-if="selectedCardiovascularDiagnostic" class="mt-2">
                  <div class="flex items-center justify-between bg-green-100 rounded-lg p-3">
                    <div>
                      <span class="text-sm font-medium text-green-800">{{ selectedCardiovascularDiagnostic.label }}</span>
                      <span class="text-xs text-green-600 ml-2">({{ selectedCardiovascularDiagnostic.code }})</span>
                    </div>
                    <button
                      @click="clearCardiovascularDiagnostic"
                      class="text-green-600 hover:text-green-800 text-sm"
                    >
                      ✕
                    </button>
                  </div>
                </div>
              </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                  Tension artérielle systolique
                </label>
                <input
                  v-model.number="formData.bp_systolic"
                  type="number"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                  placeholder="120"
                />
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                  Tension artérielle diastolique
                </label>
                <input
                  v-model.number="formData.bp_diastolic"
                  type="number"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                  placeholder="80"
                />
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                  Fréquence cardiaque
                </label>
                <input
                  v-model.number="formData.heart_rate"
                  type="number"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                  placeholder="65"
                />
              </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                  Fréquence cardiaque au repos
                </label>
                <input
                  v-model.number="formData.resting_heart_rate"
                  type="number"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                  placeholder="58"
                />
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                  Catégorie tension artérielle
                </label>
                <select
                  v-model="formData.bp_category"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                >
                  <option value="">Sélectionner</option>
                  <option value="normal">Normal</option>
                  <option value="elevated">Élevé</option>
                  <option value="stage1_hypertension">Hypertension stade 1</option>
                  <option value="stage2_hypertension">Hypertension stade 2</option>
                </select>
              </div>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">
                Symptômes cardiovasculaires
              </label>
              <div class="grid grid-cols-2 md:grid-cols-3 gap-2">
                <label class="flex items-center">
                  <input
                    v-model="formData.cardiovascular_symptoms"
                    type="checkbox"
                    value="chest_pain"
                    class="mr-2"
                  />
                  <span class="text-sm">Douleur thoracique</span>
                </label>
                <label class="flex items-center">
                  <input
                    v-model="formData.cardiovascular_symptoms"
                    type="checkbox"
                    value="shortness_of_breath"
                    class="mr-2"
                  />
                  <span class="text-sm">Essoufflement</span>
                </label>
                <label class="flex items-center">
                  <input
                    v-model="formData.cardiovascular_symptoms"
                    type="checkbox"
                    value="palpitations"
                    class="mr-2"
                  />
                  <span class="text-sm">Palpitations</span>
                </label>
                <label class="flex items-center">
                  <input
                    v-model="formData.cardiovascular_symptoms"
                    type="checkbox"
                    value="dizziness"
                    class="mr-2"
                  />
                  <span class="text-sm">Vertiges</span>
                </label>
                <label class="flex items-center">
                  <input
                    v-model="formData.cardiovascular_symptoms"
                    type="checkbox"
                    value="syncope"
                    class="mr-2"
                  />
                  <span class="text-sm">Syncope</span>
                </label>
                <label class="flex items-center">
                  <input
                    v-model="formData.cardiovascular_symptoms"
                    type="checkbox"
                    value="fatigue"
                    class="mr-2"
                  />
                  <span class="text-sm">Fatigue</span>
                </label>
              </div>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">
                Antécédents familiaux
              </label>
              <textarea
                v-model="formData.family_history_notes"
                rows="3"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                placeholder="Antécédents familiaux de maladies cardiovasculaires..."
              ></textarea>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">
                Médicaments actuels
              </label>
              <input
                v-model="formData.current_medications"
                type="text"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                placeholder="Liste des médicaments..."
              />
            </div>
          </div>

          <!-- Neurological Section -->
          <div v-if="formData.type === 'neurological'" class="space-y-4">
            <h3 class="text-lg font-medium text-gray-900 border-b border-gray-200 pb-2">
              Évaluation Neurologique
            </h3>

            <!-- ICD-11 Diagnostic Section -->
            <div class="bg-purple-50 border border-purple-200 rounded-lg p-4">
              <h4 class="text-md font-medium text-purple-900 mb-3">🔍 Diagnostic ICD-11</h4>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                  Diagnostic Neurologique
                </label>
                <ICDSearchInput
                  v-model="formData.neurological_diagnosis"
                  placeholder="Rechercher un diagnostic neurologique ICD-11..."
                  @selected="onNeurologicalDiagnosticSelected"
                />
                <div v-if="selectedNeurologicalDiagnostic" class="mt-2">
                  <div class="flex items-center justify-between bg-purple-100 rounded-lg p-3">
                    <div>
                      <span class="text-sm font-medium text-purple-800">{{ selectedNeurologicalDiagnostic.label }}</span>
                      <span class="text-xs text-purple-600 ml-2">({{ selectedNeurologicalDiagnostic.code }})</span>
                    </div>
                    <button
                      @click="clearNeurologicalDiagnostic"
                      class="text-purple-600 hover:text-purple-800 text-sm"
                    >
                      ✕
                    </button>
                  </div>
                </div>
              </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                  État mental
                </label>
                <select
                  v-model="formData.mental_status"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                >
                  <option value="">Sélectionner</option>
                  <option value="alert_and_oriented">Alerte et orienté</option>
                  <option value="confused">Confus</option>
                  <option value="lethargic">Léthargique</option>
                </select>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                  Échelle de Glasgow
                </label>
                <input
                  v-model.number="formData.glasgow_coma_scale"
                  type="number"
                  min="3"
                  max="15"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                  placeholder="15"
                />
              </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                  Réponse pupillaire
                </label>
                <select
                  v-model="formData.pupillary_response"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                >
                  <option value="">Sélectionner</option>
                  <option value="equal_and_reactive">Égales et réactives</option>
                  <option value="unequal">Inégales</option>
                  <option value="fixed">Fixées</option>
                </select>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                  Fonction motrice
                </label>
                <select
                  v-model="formData.motor_function"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                >
                  <option value="">Sélectionner</option>
                  <option value="normal">Normale</option>
                  <option value="weakness">Faiblesse</option>
                  <option value="paralysis">Paralysie</option>
                </select>
              </div>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">
                Symptômes neurologiques
              </label>
              <div class="grid grid-cols-2 md:grid-cols-3 gap-2">
                <label class="flex items-center">
                  <input
                    v-model="formData.neurological_symptoms"
                    type="checkbox"
                    value="headache"
                    class="mr-2"
                  />
                  <span class="text-sm">Maux de tête</span>
                </label>
                <label class="flex items-center">
                  <input
                    v-model="formData.neurological_symptoms"
                    type="checkbox"
                    value="seizure"
                    class="mr-2"
                  />
                  <span class="text-sm">Crise d'épilepsie</span>
                </label>
                <label class="flex items-center">
                  <input
                    v-model="formData.neurological_symptoms"
                    type="checkbox"
                    value="memory_loss"
                    class="mr-2"
                  />
                  <span class="text-sm">Perte de mémoire</span>
                </label>
                <label class="flex items-center">
                  <input
                    v-model="formData.neurological_symptoms"
                    type="checkbox"
                    value="speech_difficulty"
                    class="mr-2"
                  />
                  <span class="text-sm">Difficulté d'élocution</span>
                </label>
                <label class="flex items-center">
                  <input
                    v-model="formData.neurological_symptoms"
                    type="checkbox"
                    value="vision_changes"
                    class="mr-2"
                  />
                  <span class="text-sm">Troubles visuels</span>
                </label>
              </div>
            </div>
          </div>

          <!-- Musculoskeletal Section -->
          <div v-if="formData.type === 'musculoskeletal'" class="space-y-4">
            <h3 class="text-lg font-medium text-gray-900 border-b border-gray-200 pb-2">
              Évaluation Musculo-squelettique
            </h3>

            <!-- ICD-11 Diagnostic Section -->
            <div class="bg-orange-50 border border-orange-200 rounded-lg p-4">
              <h4 class="text-md font-medium text-orange-900 mb-3">🔍 Diagnostic ICD-11</h4>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                  Diagnostic Musculo-squelettique
                </label>
                <ICDSearchInput
                  v-model="formData.musculoskeletal_diagnosis"
                  placeholder="Rechercher un diagnostic musculo-squelettique ICD-11..."
                  @selected="onMusculoskeletalDiagnosticSelected"
                />
                <div v-if="selectedMusculoskeletalDiagnostic" class="mt-2">
                  <div class="flex items-center justify-between bg-orange-100 rounded-lg p-3">
                    <div>
                      <span class="text-sm font-medium text-orange-800">{{ selectedMusculoskeletalDiagnostic.label }}</span>
                      <span class="text-xs text-orange-600 ml-2">({{ selectedMusculoskeletalDiagnostic.code }})</span>
                    </div>
                    <button
                      @click="clearMusculoskeletalDiagnostic"
                      class="text-orange-600 hover:text-orange-800 text-sm"
                    >
                      ✕
                    </button>
                  </div>
                </div>
              </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                  Amplitude de mouvement
                </label>
                <select
                  v-model="formData.range_of_motion"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                >
                  <option value="">Sélectionner</option>
                  <option value="full">Complète</option>
                  <option value="limited">Limitée</option>
                  <option value="none">Aucune</option>
                </select>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                  Grade de force
                </label>
                <input
                  v-model.number="formData.strength_grade"
                  type="number"
                  min="0"
                  max="5"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                  placeholder="5"
                />
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                  Niveau de douleur
                </label>
                <input
                  v-model.number="formData.pain_level"
                  type="number"
                  min="0"
                  max="10"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                  placeholder="0"
                />
              </div>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">
                Stabilité articulaire
              </label>
              <select
                v-model="formData.joint_stability"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
              >
                <option value="">Sélectionner</option>
                <option value="stable">Stable</option>
                <option value="unstable">Instable</option>
              </select>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">
                Type de blessure
              </label>
              <select
                v-model="formData.injury_type"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
              >
                <option value="">Sélectionner</option>
                <option value="sprain">Entorse</option>
                <option value="strain">Élongation</option>
                <option value="fracture">Fracture</option>
                <option value="dislocation">Luxation</option>
              </select>
            </div>
          </div>

          <!-- General Section -->
          <div v-if="formData.type === 'general'" class="space-y-4">
            <h3 class="text-lg font-medium text-gray-900 border-b border-gray-200 pb-2">
              Évaluation Générale
            </h3>

            <!-- ICD-11 Diagnostic Section -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
              <h4 class="text-md font-medium text-blue-900 mb-3">🔍 Diagnostic ICD-11</h4>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                  Diagnostic Général
                </label>
                <ICDSearchInput
                  v-model="formData.general_diagnosis"
                  placeholder="Rechercher un diagnostic général ICD-11..."
                  @selected="onGeneralDiagnosticSelected"
                />
                <div v-if="selectedGeneralDiagnostic" class="mt-2">
                  <div class="flex items-center justify-between bg-blue-100 rounded-lg p-3">
                    <div>
                      <span class="text-sm font-medium text-blue-800">{{ selectedGeneralDiagnostic.label }}</span>
                      <span class="text-xs text-blue-600 ml-2">({{ selectedGeneralDiagnostic.code }})</span>
                    </div>
                    <button
                      @click="clearGeneralDiagnostic"
                      class="text-blue-600 hover:text-blue-800 text-sm"
                    >
                      ✕
                    </button>
                  </div>
                </div>
              </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                  Température (°C)
                </label>
                <input
                  v-model.number="formData.temperature"
                  type="number"
                  step="0.1"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                  placeholder="37.0"
                />
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                  Poids (kg)
                </label>
                <input
                  v-model.number="formData.weight"
                  type="number"
                  step="0.1"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                  placeholder="70.0"
                />
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                  Taille (cm)
                </label>
                <input
                  v-model.number="formData.height"
                  type="number"
                  step="0.1"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                  placeholder="175.0"
                />
              </div>
            </div>

            <div v-if="formData.weight && formData.height" class="bg-gray-50 p-4 rounded-md">
              <span class="text-sm font-medium text-gray-700">IMC calculé: {{ calculateBMI() }}</span>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">
                Allergies
              </label>
              <input
                v-model="formData.allergies"
                type="text"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                placeholder="Liste des allergies..."
              />
            </div>
          </div>

          <!-- Common Fields -->
          <div class="space-y-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">
                Notes cliniques
              </label>
              <textarea
                v-model="formData.clinical_notes"
                rows="4"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                placeholder="Observations cliniques détaillées..."
              ></textarea>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">
                Recommandations
              </label>
              <textarea
                v-model="formData.recommendations"
                rows="3"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                placeholder="Recommandations pour l'athlète..."
              ></textarea>
            </div>
          </div>

          <!-- Submit Button -->
          <div class="flex justify-end space-x-4 pt-6 border-t border-gray-200">
            <button
              type="button"
              @click="$router.go(-1)"
              class="px-6 py-2 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50 transition-colors"
            >
              Annuler
            </button>
            <button
              type="submit"
              :disabled="submitting"
              class="px-6 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
            >
              <span v-if="submitting" class="flex items-center">
                <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-white mr-2"></div>
                Enregistrement...
              </span>
              <span v-else>Soumettre PCMA</span>
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
import ICDSearchInput from './ICDSearchInput.vue'

export default {
  name: 'PCMAForm',
  components: {
    ICDSearchInput
  },
  props: {
    athleteId: {
      type: [String, Number],
      required: true
    }
  },
  data() {
    return {
      transcript: '',
      aiProcessing: false,
      aiResults: null,
      aiError: null,
      submitting: false,
      selectedCardiovascularDiagnostic: null,
      selectedNeurologicalDiagnostic: null,
      selectedMusculoskeletalDiagnostic: null,
      selectedGeneralDiagnostic: null,
      formData: {
        type: 'cardio',
        assessor_name: '',
        cardiovascular_diagnosis: null,
        neurological_diagnosis: null,
        musculoskeletal_diagnosis: null,
        general_diagnosis: null,
        bp_systolic: null,
        bp_diastolic: null,
        heart_rate: null,
        resting_heart_rate: null,
        bp_category: '',
        cardiovascular_symptoms: [],
        family_history_notes: '',
        current_medications: '',
        mental_status: '',
        glasgow_coma_scale: null,
        pupillary_response: '',
        motor_function: '',
        neurological_symptoms: [],
        range_of_motion: '',
        strength_grade: null,
        pain_level: null,
        joint_stability: '',
        injury_type: '',
        affected_body_parts: [],
        temperature: null,
        weight: null,
        height: null,
        allergies: '',
        general_symptoms: [],
        clinical_notes: '',
        recommendations: ''
      }
    }
  },
  methods: {
    onCardiovascularDiagnosticSelected(diagnostic) {
      this.selectedCardiovascularDiagnostic = diagnostic
      this.formData.cardiovascular_diagnosis = diagnostic
    },
    
    clearCardiovascularDiagnostic() {
      this.selectedCardiovascularDiagnostic = null
      this.formData.cardiovascular_diagnosis = null
    },
    
    onNeurologicalDiagnosticSelected(diagnostic) {
      this.selectedNeurologicalDiagnostic = diagnostic
      this.formData.neurological_diagnosis = diagnostic
    },
    
    clearNeurologicalDiagnostic() {
      this.selectedNeurologicalDiagnostic = null
      this.formData.neurological_diagnosis = null
    },
    
    onMusculoskeletalDiagnosticSelected(diagnostic) {
      this.selectedMusculoskeletalDiagnostic = diagnostic
      this.formData.musculoskeletal_diagnosis = diagnostic
    },
    
    clearMusculoskeletalDiagnostic() {
      this.selectedMusculoskeletalDiagnostic = null
      this.formData.musculoskeletal_diagnosis = null
    },
    
    onGeneralDiagnosticSelected(diagnostic) {
      this.selectedGeneralDiagnostic = diagnostic
      this.formData.general_diagnosis = diagnostic
    },
    
    clearGeneralDiagnostic() {
      this.selectedGeneralDiagnostic = null
      this.formData.general_diagnosis = null
    },

    async prefillWithAI() {
      if (!this.transcript.trim()) return;

      this.aiProcessing = true;
      this.aiError = null;
      this.aiResults = null;

      try {
        const response = await this.$http.post('/api/v1/pcmas/prefill-from-transcript', {
          transcript: this.transcript,
          athlete_id: this.athleteId,
          pcma_type: this.formData.type
        });

        if (response.data.success) {
          this.aiResults = response.data;
          this.$toast.success('Données extraites avec succès par l\'IA');
        } else {
          this.aiError = response.data.message || 'Erreur lors de l\'extraction des données';
        }
      } catch (error) {
        console.error('Error pre-filling with AI:', error);
        this.aiError = error.response?.data?.message || 'Erreur de connexion avec le service IA';
      } finally {
        this.aiProcessing = false;
      }
    },

    applyAIResults() {
      if (!this.aiResults?.data) return;

      const extractedData = this.aiResults.data;
      
      // Apply extracted data to form fields
      Object.keys(extractedData).forEach(key => {
        if (key in this.formData) {
          this.formData[key] = extractedData[key];
        }
      });

      this.$toast.success('Données appliquées au formulaire');
    },

    clearTranscript() {
      this.transcript = '';
      this.aiResults = null;
      this.aiError = null;
    },

    calculateBMI() {
      if (this.formData.weight && this.formData.height) {
        const heightInMeters = this.formData.height / 100;
        const bmi = this.formData.weight / (heightInMeters * heightInMeters);
        return bmi.toFixed(1);
      }
      return null;
    },

    getConfidenceClass(score) {
      if (score >= 0.8) return 'bg-green-100 text-green-800';
      if (score >= 0.6) return 'bg-yellow-100 text-yellow-800';
      return 'bg-red-100 text-red-800';
    },

    formatFieldName(field) {
      const fieldNames = {
        bp_systolic: 'Tension systolique',
        bp_diastolic: 'Tension diastolique',
        heart_rate: 'Fréquence cardiaque',
        resting_heart_rate: 'FC au repos',
        bp_category: 'Catégorie tension',
        cardiovascular_symptoms: 'Symptômes cardio',
        family_history_notes: 'Antécédents familiaux',
        current_medications: 'Médicaments',
        mental_status: 'État mental',
        glasgow_coma_scale: 'Échelle Glasgow',
        pupillary_response: 'Réponse pupillaire',
        motor_function: 'Fonction motrice',
        neurological_symptoms: 'Symptômes neuro',
        range_of_motion: 'Amplitude mouvement',
        strength_grade: 'Grade de force',
        pain_level: 'Niveau de douleur',
        joint_stability: 'Stabilité articulaire',
        injury_type: 'Type de blessure',
        temperature: 'Température',
        weight: 'Poids',
        height: 'Taille',
        allergies: 'Allergies'
      };
      
      return fieldNames[field] || field;
    },

    async submitForm() {
      this.submitting = true;

      try {
        const response = await this.$http.post('/api/v1/pcmas', {
          athlete_id: this.athleteId,
          ...this.formData
        });

        this.$toast.success('PCMA enregistré avec succès');
        this.$router.push(`/medical/athletes/${this.athleteId}`);
      } catch (error) {
        console.error('Error submitting PCMA:', error);
        this.$toast.error('Erreur lors de l\'enregistrement du PCMA');
      } finally {
        this.submitting = false;
      }
    }
  }
}
</script>

<style scoped>
.pcma-form {
  @apply bg-gray-50 min-h-screen;
}
</style> 