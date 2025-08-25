<template>
  <div class="medical-history-tab">
    <div class="mb-6">
      <h2 class="text-2xl font-bold text-gray-900 mb-2">📋 Historique Médical</h2>
      <p class="text-gray-600">Questionnaire du joueur - Informations personnelles et antécédents médicaux</p>
    </div>

    <form @submit.prevent="validateForm" class="space-y-8">
      <!-- Personal Details Section -->
      <div class="bg-white rounded-lg shadow-md p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Informations Personnelles</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Nom complet *
            </label>
            <input
              v-model="formData.personal_details.name"
              type="text"
              required
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              placeholder="Nom et prénom"
            />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Date de naissance *
            </label>
            <input
              v-model="formData.personal_details.date_of_birth"
              type="date"
              required
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Âge
            </label>
            <input
              v-model="formData.personal_details.age"
              type="number"
              min="0"
              max="120"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              placeholder="Âge"
            />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Genre
            </label>
            <select
              v-model="formData.personal_details.gender"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            >
              <option value="">Sélectionner</option>
              <option value="male">Masculin</option>
              <option value="female">Féminin</option>
              <option value="other">Autre</option>
            </select>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Nationalité
            </label>
            <input
              v-model="formData.personal_details.nationality"
              type="text"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              placeholder="Nationalité"
            />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Numéro de passeport
            </label>
            <input
              v-model="formData.personal_details.passport_number"
              type="text"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              placeholder="Numéro de passeport"
            />
          </div>
        </div>
      </div>

      <!-- Contact Information Section -->
      <div class="bg-white rounded-lg shadow-md p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Informations de Contact</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div class="md:col-span-2">
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Adresse
            </label>
            <textarea
              v-model="formData.contact_information.address"
              rows="3"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              placeholder="Adresse complète"
            ></textarea>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Téléphone
            </label>
            <input
              v-model="formData.contact_information.phone"
              type="tel"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              placeholder="Numéro de téléphone"
            />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Email
            </label>
            <input
              v-model="formData.contact_information.email"
              type="email"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              placeholder="Adresse email"
            />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Contact d'urgence
            </label>
            <input
              v-model="formData.contact_information.emergency_contact"
              type="text"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              placeholder="Nom du contact d'urgence"
            />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Téléphone d'urgence
            </label>
            <input
              v-model="formData.contact_information.emergency_phone"
              type="tel"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              placeholder="Numéro d'urgence"
            />
          </div>
        </div>
      </div>

      <!-- Medical Questionnaire Section -->
      <div class="bg-white rounded-lg shadow-md p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Questionnaire Médical</h3>
        <p class="text-gray-600 mb-6">Veuillez répondre aux questions suivantes concernant votre santé</p>
        
        <div class="space-y-6">
          <!-- Cardiovascular Questions -->
          <div class="border-l-4 border-red-500 pl-4">
            <h4 class="text-md font-semibold text-gray-900 mb-4">Questions Cardiovasculaires</h4>
            
            <div class="space-y-4">
              <div class="flex items-start space-x-3">
                <input
                  v-model="formData.medical_questionnaire.has_heart_problems"
                  type="checkbox"
                  id="heart_problems"
                  class="mt-1 h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                />
                <div class="flex-1">
                  <label for="heart_problems" class="block text-sm font-medium text-gray-700">
                    Avez-vous des problèmes cardiaques ?
                  </label>
                  <textarea
                    v-if="formData.medical_questionnaire.has_heart_problems"
                    v-model="formData.medical_questionnaire.heart_problem_details"
                    rows="2"
                    class="mt-2 w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    placeholder="Décrivez vos problèmes cardiaques..."
                  ></textarea>
                </div>
              </div>

              <div class="flex items-start space-x-3">
                <input
                  v-model="formData.medical_questionnaire.has_chest_pain"
                  type="checkbox"
                  id="chest_pain"
                  class="mt-1 h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                />
                <div class="flex-1">
                  <label for="chest_pain" class="block text-sm font-medium text-gray-700">
                    Avez-vous des douleurs thoraciques ?
                  </label>
                  <textarea
                    v-if="formData.medical_questionnaire.has_chest_pain"
                    v-model="formData.medical_questionnaire.chest_pain_details"
                    rows="2"
                    class="mt-2 w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    placeholder="Décrivez vos douleurs thoraciques..."
                  ></textarea>
                </div>
              </div>

              <div class="flex items-start space-x-3">
                <input
                  v-model="formData.medical_questionnaire.has_dizziness"
                  type="checkbox"
                  id="dizziness"
                  class="mt-1 h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                />
                <div class="flex-1">
                  <label for="dizziness" class="block text-sm font-medium text-gray-700">
                    Avez-vous des vertiges ?
                  </label>
                  <textarea
                    v-if="formData.medical_questionnaire.has_dizziness"
                    v-model="formData.medical_questionnaire.dizziness_details"
                    rows="2"
                    class="mt-2 w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    placeholder="Décrivez vos vertiges..."
                  ></textarea>
                </div>
              </div>

              <div class="flex items-start space-x-3">
                <input
                  v-model="formData.medical_questionnaire.has_shortness_of_breath"
                  type="checkbox"
                  id="shortness_of_breath"
                  class="mt-1 h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                />
                <div class="flex-1">
                  <label for="shortness_of_breath" class="block text-sm font-medium text-gray-700">
                    Avez-vous un essoufflement ?
                  </label>
                  <textarea
                    v-if="formData.medical_questionnaire.has_shortness_of_breath"
                    v-model="formData.medical_questionnaire.shortness_of_breath_details"
                    rows="2"
                    class="mt-2 w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    placeholder="Décrivez votre essoufflement..."
                  ></textarea>
                </div>
              </div>

              <div class="flex items-start space-x-3">
                <input
                  v-model="formData.medical_questionnaire.has_fainting"
                  type="checkbox"
                  id="fainting"
                  class="mt-1 h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                />
                <div class="flex-1">
                  <label for="fainting" class="block text-sm font-medium text-gray-700">
                    Avez-vous déjà eu des évanouissements ?
                  </label>
                  <textarea
                    v-if="formData.medical_questionnaire.has_fainting"
                    v-model="formData.medical_questionnaire.fainting_details"
                    rows="2"
                    class="mt-2 w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    placeholder="Décrivez vos évanouissements..."
                  ></textarea>
                </div>
              </div>

              <div class="flex items-start space-x-3">
                <input
                  v-model="formData.medical_questionnaire.has_high_blood_pressure"
                  type="checkbox"
                  id="high_blood_pressure"
                  class="mt-1 h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                />
                <div class="flex-1">
                  <label for="high_blood_pressure" class="block text-sm font-medium text-gray-700">
                    Avez-vous une hypertension artérielle ?
                  </label>
                  <textarea
                    v-if="formData.medical_questionnaire.has_high_blood_pressure"
                    v-model="formData.medical_questionnaire.high_blood_pressure_details"
                    rows="2"
                    class="mt-2 w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    placeholder="Décrivez votre hypertension..."
                  ></textarea>
                </div>
              </div>
            </div>
          </div>

          <!-- Other Medical Conditions -->
          <div class="border-l-4 border-orange-500 pl-4">
            <h4 class="text-md font-semibold text-gray-900 mb-4">Autres Conditions Médicales</h4>
            
            <div class="space-y-4">
              <div class="flex items-start space-x-3">
                <input
                  v-model="formData.medical_questionnaire.has_diabetes"
                  type="checkbox"
                  id="diabetes"
                  class="mt-1 h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                />
                <div class="flex-1">
                  <label for="diabetes" class="block text-sm font-medium text-gray-700">
                    Avez-vous un diabète ?
                  </label>
                  <textarea
                    v-if="formData.medical_questionnaire.has_diabetes"
                    v-model="formData.medical_questionnaire.diabetes_details"
                    rows="2"
                    class="mt-2 w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    placeholder="Décrivez votre diabète..."
                  ></textarea>
                </div>
              </div>

              <div class="flex items-start space-x-3">
                <input
                  v-model="formData.medical_questionnaire.has_asthma"
                  type="checkbox"
                  id="asthma"
                  class="mt-1 h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                />
                <div class="flex-1">
                  <label for="asthma" class="block text-sm font-medium text-gray-700">
                    Avez-vous de l'asthme ?
                  </label>
                  <textarea
                    v-if="formData.medical_questionnaire.has_asthma"
                    v-model="formData.medical_questionnaire.asthma_details"
                    rows="2"
                    class="mt-2 w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    placeholder="Décrivez votre asthme..."
                  ></textarea>
                </div>
              </div>

              <div class="flex items-start space-x-3">
                <input
                  v-model="formData.medical_questionnaire.has_epilepsy"
                  type="checkbox"
                  id="epilepsy"
                  class="mt-1 h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                />
                <div class="flex-1">
                  <label for="epilepsy" class="block text-sm font-medium text-gray-700">
                    Avez-vous de l'épilepsie ?
                  </label>
                  <textarea
                    v-if="formData.medical_questionnaire.has_epilepsy"
                    v-model="formData.medical_questionnaire.epilepsy_details"
                    rows="2"
                    class="mt-2 w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    placeholder="Décrivez votre épilepsie..."
                  ></textarea>
                </div>
              </div>

              <div class="flex items-start space-x-3">
                <input
                  v-model="formData.medical_questionnaire.has_other_conditions"
                  type="checkbox"
                  id="other_conditions"
                  class="mt-1 h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                />
                <div class="flex-1">
                  <label for="other_conditions" class="block text-sm font-medium text-gray-700">
                    Avez-vous d'autres conditions médicales ?
                  </label>
                  
                  <!-- ICD-11 Diagnostic Search -->
                  <div v-if="formData.medical_questionnaire.has_other_conditions" class="mt-2 space-y-3">
                    <div>
                      <label class="block text-sm font-medium text-gray-700 mb-2">
                        Diagnostic ICD-11
                      </label>
                      <ICDSearchInput
                        v-model="formData.medical_questionnaire.icd11_conditions"
                        placeholder="Rechercher des conditions médicales ICD-11..."
                        @selected="onConditionSelected"
                      />
                      <p class="mt-1 text-sm text-gray-500">
                        Sélectionnez les conditions médicales standardisées ICD-11
                      </p>
                    </div>
                    
                    <!-- Selected Conditions Display -->
                    <div v-if="selectedConditions.length > 0" class="space-y-2">
                      <label class="block text-sm font-medium text-gray-700">
                        Conditions sélectionnées:
                      </label>
                      <div class="space-y-2">
                        <div 
                          v-for="(condition, index) in selectedConditions" 
                          :key="index"
                          class="flex items-center justify-between p-2 bg-blue-50 border border-blue-200 rounded-md"
                        >
                          <div>
                            <span class="text-sm font-medium text-blue-900">{{ condition.code }}</span>
                            <span class="text-sm text-blue-700 ml-2">{{ condition.label }}</span>
                          </div>
                          <button 
                            @click="removeCondition(index)"
                            class="text-red-600 hover:text-red-800 text-sm"
                          >
                            ✕
                          </button>
                        </div>
                      </div>
                    </div>
                    
                    <!-- Additional Notes -->
                    <div>
                      <label class="block text-sm font-medium text-gray-700 mb-2">
                        Notes supplémentaires
                      </label>
                      <textarea
                        v-model="formData.medical_questionnaire.other_conditions_details"
                        rows="2"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        placeholder="Décrivez vos autres conditions ou ajoutez des détails..."
                      ></textarea>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Family History Section -->
      <div class="bg-white rounded-lg shadow-md p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Antécédents Familiaux</h3>
        
        <div class="space-y-4">
          <div class="flex items-start space-x-3">
            <input
              v-model="formData.family_history.has_family_heart_problems"
              type="checkbox"
              id="family_heart_problems"
              class="mt-1 h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
            />
            <div class="flex-1">
              <label for="family_heart_problems" class="block text-sm font-medium text-gray-700">
                Y a-t-il des problèmes cardiaques dans votre famille ?
              </label>
              <textarea
                v-if="formData.family_history.has_family_heart_problems"
                v-model="formData.family_history.family_heart_problem_details"
                rows="2"
                class="mt-2 w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                placeholder="Décrivez les problèmes cardiaques familiaux..."
              ></textarea>
            </div>
          </div>

          <div class="flex items-start space-x-3">
            <input
              v-model="formData.family_history.has_family_sudden_death"
              type="checkbox"
              id="family_sudden_death"
              class="mt-1 h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
            />
            <div class="flex-1">
              <label for="family_sudden_death" class="block text-sm font-medium text-gray-700">
                Y a-t-il eu des morts subites dans votre famille ?
              </label>
              <textarea
                v-if="formData.family_history.has_family_sudden_death"
                v-model="formData.family_history.family_sudden_death_details"
                rows="2"
                class="mt-2 w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                placeholder="Décrivez les morts subites familiales..."
              ></textarea>
            </div>
          </div>

          <div class="flex items-start space-x-3">
            <input
              v-model="formData.family_history.has_family_other_conditions"
              type="checkbox"
              id="family_other_conditions"
              class="mt-1 h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
            />
            <div class="flex-1">
              <label for="family_other_conditions" class="block text-sm font-medium text-gray-700">
                Y a-t-il d'autres conditions médicales dans votre famille ?
              </label>
              <textarea
                v-if="formData.family_history.has_family_other_conditions"
                v-model="formData.family_history.family_other_conditions_details"
                rows="2"
                class="mt-2 w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                placeholder="Décrivez les autres conditions familiales..."
              ></textarea>
            </div>
          </div>
        </div>
      </div>

      <!-- Medication History Section -->
      <div class="bg-white rounded-lg shadow-md p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Historique des Médicaments</h3>
        
        <div class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Médicaments actuels
            </label>
            <textarea
              v-model="formData.medication_history.current_medications"
              rows="3"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              placeholder="Listez vos médicaments actuels..."
            ></textarea>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Allergies
            </label>
            <textarea
              v-model="formData.medication_history.allergies"
              rows="3"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              placeholder="Listez vos allergies..."
            ></textarea>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Suppléments
            </label>
            <textarea
              v-model="formData.medication_history.supplements"
              rows="3"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              placeholder="Listez vos suppléments..."
            ></textarea>
          </div>
        </div>
      </div>

      <!-- Lifestyle Factors Section -->
      <div class="bg-white rounded-lg shadow-md p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Facteurs de Mode de Vie</h3>
        
        <div class="space-y-4">
          <div class="flex items-start space-x-3">
            <input
              v-model="formData.lifestyle_factors.smoking"
              type="checkbox"
              id="smoking"
              class="mt-1 h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
            />
            <div class="flex-1">
              <label for="smoking" class="block text-sm font-medium text-gray-700">
                Fumez-vous ?
              </label>
              <textarea
                v-if="formData.lifestyle_factors.smoking"
                v-model="formData.lifestyle_factors.smoking_details"
                rows="2"
                class="mt-2 w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                placeholder="Décrivez votre consommation de tabac..."
              ></textarea>
            </div>
          </div>

          <div class="flex items-start space-x-3">
            <input
              v-model="formData.lifestyle_factors.alcohol"
              type="checkbox"
              id="alcohol"
              class="mt-1 h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
            />
            <div class="flex-1">
              <label for="alcohol" class="block text-sm font-medium text-gray-700">
                Consommez-vous de l'alcool ?
              </label>
              <textarea
                v-if="formData.lifestyle_factors.alcohol"
                v-model="formData.lifestyle_factors.alcohol_details"
                rows="2"
                class="mt-2 w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                placeholder="Décrivez votre consommation d'alcool..."
              ></textarea>
            </div>
          </div>

          <div class="flex items-start space-x-3">
            <input
              v-model="formData.lifestyle_factors.drugs"
              type="checkbox"
              id="drugs"
              class="mt-1 h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
            />
            <div class="flex-1">
              <label for="drugs" class="block text-sm font-medium text-gray-700">
                Consommez-vous des drogues ?
              </label>
              <textarea
                v-if="formData.lifestyle_factors.drugs"
                v-model="formData.lifestyle_factors.drugs_details"
                rows="2"
                class="mt-2 w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                placeholder="Décrivez votre consommation de drogues..."
              ></textarea>
            </div>
          </div>
        </div>
      </div>
    </form>
  </div>
</template>

<script>
import ICDSearchInput from '../ICDSearchInput.vue'

export default {
  name: 'MedicalHistoryTab',
  components: {
    ICDSearchInput
  },
  props: {
    value: {
      type: Object,
      default: () => ({})
    }
  },
  data() {
    return {
      formData: {
        personal_details: {
          name: '',
          date_of_birth: '',
          age: '',
          gender: '',
          nationality: '',
          passport_number: ''
        },
        contact_information: {
          address: '',
          phone: '',
          email: '',
          emergency_contact: '',
          emergency_phone: ''
        },
        medical_questionnaire: {
          has_heart_problems: false,
          heart_problem_details: '',
          has_chest_pain: false,
          chest_pain_details: '',
          has_dizziness: false,
          dizziness_details: '',
          has_shortness_of_breath: false,
          shortness_of_breath_details: '',
          has_fainting: false,
          fainting_details: '',
          has_high_blood_pressure: false,
          high_blood_pressure_details: '',
          has_diabetes: false,
          diabetes_details: '',
          has_asthma: false,
          asthma_details: '',
          has_epilepsy: false,
          epilepsy_details: '',
          has_other_conditions: false,
          other_conditions_details: '',
          icd11_conditions: [] // Added for ICD-11 search
        },
        family_history: {
          has_family_heart_problems: false,
          family_heart_problem_details: '',
          has_family_sudden_death: false,
          family_sudden_death_details: '',
          has_family_other_conditions: false,
          family_other_conditions_details: ''
        },
        medication_history: {
          current_medications: '',
          allergies: '',
          supplements: ''
        },
        lifestyle_factors: {
          smoking: false,
          smoking_details: '',
          alcohol: false,
          alcohol_details: '',
          drugs: false,
          drugs_details: ''
        }
      },
      selectedConditions: [] // Added for ICD-11 search
    }
  },
  watch: {
    value: {
      handler(newVal) {
        if (newVal && Object.keys(newVal).length > 0) {
          this.formData = { ...this.formData, ...newVal }
        }
      },
      immediate: true,
      deep: true
    },
    formData: {
      handler(newVal) {
        this.$emit('input', newVal)
        this.validateForm()
      },
      deep: true
    }
  },
  methods: {
    validateForm() {
      const isValid = this.formData.personal_details.name && 
                     this.formData.personal_details.date_of_birth
      this.$emit('update:valid', isValid)
    },
    onConditionSelected(condition) {
      if (!this.selectedConditions.some(c => c.code === condition.code)) {
        this.selectedConditions.push(condition)
      }
    },
    removeCondition(index) {
      this.selectedConditions.splice(index, 1)
    }
  }
}
</script> 