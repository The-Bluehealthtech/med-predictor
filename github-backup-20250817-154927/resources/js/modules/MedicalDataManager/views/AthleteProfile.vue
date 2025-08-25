<template>
  <div class="clinical-workstation">
    <!-- Loading State -->
    <div v-if="loading" class="flex justify-center items-center py-12">
      <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-red-600"></div>
    </div>

    <div v-else-if="athlete" class="grid grid-cols-12 h-screen bg-gray-50">
      
      <!-- LEFT PANEL: Navigation & Identity (25%) -->
      <div class="col-span-3 bg-white border-r border-gray-200 flex flex-col">
        
        <!-- Athlete Identity Card -->
        <div class="p-6 border-b border-gray-200 bg-gradient-to-br from-blue-50 to-indigo-50">
          <div class="flex items-center space-x-4 mb-4">
            <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-full flex items-center justify-center text-white font-bold text-xl">
              {{ getInitials(athlete.name) }}
            </div>
            <div class="flex-1">
              <h2 class="text-xl font-bold text-gray-900 truncate">{{ athlete.name }}</h2>
              <p class="text-sm text-gray-600">{{ athlete.team?.name }}</p>
              <p class="text-xs text-gray-500">FIFA ID: {{ athlete.fifa_id }}</p>
            </div>
          </div>
          
          <!-- Quick Stats -->
          <div class="grid grid-cols-2 gap-3 text-xs">
            <div class="bg-white rounded p-2 text-center">
              <div class="font-semibold text-gray-900">{{ athlete.age }} ans</div>
              <div class="text-gray-500">{{ athlete.position || 'N/A' }}</div>
            </div>
            <div class="bg-white rounded p-2 text-center">
              <div class="font-semibold" :class="getHealthScoreColor(athlete.health_score)">
                {{ athlete.health_score }}%
              </div>
              <div class="text-gray-500">Santé</div>
            </div>
          </div>
        </div>

        <!-- Navigation Menu -->
        <nav class="flex-1 p-4 space-y-2">
          <button 
            v-for="item in navigationItems" 
            :key="item.key"
            @click="activeSection = item.key"
            :class="[
              'w-full text-left px-4 py-3 rounded-lg transition-all duration-200 flex items-center space-x-3',
              activeSection === item.key 
                ? 'bg-red-50 border-l-4 border-red-500 text-red-700 font-medium' 
                : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'
            ]"
          >
            <span class="text-lg">{{ item.icon }}</span>
            <span class="text-sm">{{ item.label }}</span>
            <span v-if="item.badge" class="ml-auto bg-red-100 text-red-800 text-xs px-2 py-1 rounded-full">
              {{ item.badge }}
            </span>
          </button>
        </nav>

        <!-- Quick Actions -->
        <div class="p-4 border-t border-gray-200">
          <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3">Actions Rapides</h4>
          <div class="space-y-2">
            <button 
              @click="navigateToPCMAForm"
              class="w-full text-left px-3 py-2 text-sm bg-red-50 text-red-700 rounded-md hover:bg-red-100 transition-colors flex items-center space-x-2"
            >
              <span>📋</span>
              <span>Nouveau PCMA</span>
            </button>
            <button 
              @click="navigateToInjuryForm"
              class="w-full text-left px-3 py-2 text-sm bg-orange-50 text-orange-700 rounded-md hover:bg-orange-100 transition-colors flex items-center space-x-2"
            >
              <span>🩹</span>
              <span>Nouvelle Blessure</span>
            </button>
            <button 
              @click="navigateToSCATForm"
              class="w-full text-left px-3 py-2 text-sm bg-purple-50 text-purple-700 rounded-md hover:bg-purple-100 transition-colors flex items-center space-x-2"
            >
              <span>🧠</span>
              <span>Nouveau SCAT</span>
            </button>
          </div>
        </div>
      </div>

      <!-- CENTER PANEL: Main Workspace (50%) -->
      <div class="col-span-6 bg-white">
        <div class="h-full flex flex-col">
          
          <!-- Workspace Header -->
          <div class="p-6 border-b border-gray-200 bg-white">
            <div class="flex items-center justify-between">
              <div>
                <h1 class="text-2xl font-bold text-gray-900">{{ getSectionTitle() }}</h1>
                <p class="text-sm text-gray-600">{{ getSectionDescription() }}</p>
              </div>
              <div class="flex space-x-2">
                <button 
                  v-if="activeSection === 'dashboard'"
                  @click="navigateToPCMAForm"
                  class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition-colors text-sm"
                >
                  + Nouveau PCMA
                </button>
                <button 
                  v-if="activeSection === 'injuries'"
                  @click="navigateToInjuryForm"
                  class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition-colors text-sm"
                >
                  + Nouvelle Blessure
                </button>
                <button 
                  v-if="activeSection === 'concussions'"
                  @click="navigateToSCATForm"
                  class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition-colors text-sm"
                >
                  + Nouveau SCAT
                </button>
                <button 
                  v-if="activeSection === 'notes'"
                  @click="navigateToNoteEditor"
                  class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition-colors text-sm"
                >
                  + Nouvelle Note
                </button>
              </div>
            </div>
          </div>

          <!-- Workspace Content -->
          <div class="flex-1 overflow-auto">
            <div class="p-6">
              
              <!-- Dashboard Section -->
              <div v-if="activeSection === 'dashboard'" class="space-y-6">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                  <HealthScoreMeter :health-data="healthData" />
                  
                  <!-- Quick Stats -->
                  <div class="bg-gray-50 rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Statistiques Rapides</h3>
                    <div class="space-y-4">
                      <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Blessures actives:</span>
                        <span class="text-sm font-medium">{{ injuries.filter(i => i.status === 'open').length }}</span>
                      </div>
                      <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">PCMA en attente:</span>
                        <span class="text-sm font-medium">{{ pcmas.filter(p => p.status === 'pending').length }}</span>
                      </div>
                      <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Alertes non résolues:</span>
                        <span class="text-sm font-medium">{{ athlete?.risk_alerts?.filter(a => !a.resolved).length || 0 }}</span>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Medical Status Cards -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                  <div class="bg-white rounded-lg shadow p-6 border-l-4 border-red-500">
                    <div class="flex items-center justify-between mb-4">
                      <h3 class="text-lg font-semibold text-gray-900">Blessures Actives</h3>
                      <div class="w-8 h-8 bg-red-100 rounded-full flex items-center justify-center">
                        <span class="text-red-600">🩹</span>
                      </div>
                    </div>
                    <div v-if="athlete.medical_status?.has_active_injuries" class="text-center">
                      <p class="text-red-600 font-medium">Blessures actives détectées</p>
                      <p class="text-sm text-gray-600 mt-1">Consultez le profil médical complet</p>
                    </div>
                    <div v-else class="text-center">
                      <p class="text-green-600 font-medium">Aucune blessure active</p>
                      <p class="text-sm text-gray-600 mt-1">Athlète en bonne santé</p>
                    </div>
                  </div>

                  <div class="bg-white rounded-lg shadow p-6 border-l-4 border-yellow-500">
                    <div class="flex items-center justify-between mb-4">
                      <h3 class="text-lg font-semibold text-gray-900">PCMA en Attente</h3>
                      <div class="w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center">
                        <span class="text-yellow-600">⚠️</span>
                      </div>
                    </div>
                    <div v-if="athlete.medical_status?.has_pending_pcma" class="text-center">
                      <p class="text-yellow-600 font-medium">PCMA en attente</p>
                      <p class="text-sm text-gray-600 mt-1">Évaluations à compléter</p>
                    </div>
                    <div v-else class="text-center">
                      <p class="text-green-600 font-medium">Tous les PCMA complétés</p>
                      <p class="text-sm text-gray-600 mt-1">Conformité à jour</p>
                    </div>
                  </div>

                  <div class="bg-white rounded-lg shadow p-6 border-l-4 border-red-500">
                    <div class="flex items-center justify-between mb-4">
                      <h3 class="text-lg font-semibold text-gray-900">Alertes Non Résolues</h3>
                      <div class="w-8 h-8 bg-red-100 rounded-full flex items-center justify-center">
                        <span class="text-red-600">🚨</span>
                      </div>
                    </div>
                    <div v-if="athlete.medical_status?.has_unresolved_alerts" class="text-center">
                      <p class="text-red-600 font-medium">Alertes non résolues</p>
                      <p class="text-sm text-gray-600 mt-1">Action requise</p>
                    </div>
                    <div v-else class="text-center">
                      <p class="text-green-600 font-medium">Aucune alerte</p>
                      <p class="text-sm text-gray-600 mt-1">Statut normal</p>
                    </div>
                  </div>
                </div>
              </div>

              <!-- PCMA Section -->
              <div v-if="activeSection === 'pcma'" class="space-y-4">
                <div v-if="pcmas.length === 0" class="text-center py-12">
                  <div class="text-6xl mb-4">📋</div>
                  <h3 class="text-lg font-medium text-gray-900 mb-2">Aucun PCMA enregistré</h3>
                  <p class="text-gray-600 mb-4">Aucune évaluation PCMA n'a été effectuée pour cet athlète.</p>
                  <button 
                    @click="navigateToPCMAForm"
                    class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition-colors"
                  >
                    Effectuer le premier PCMA
                  </button>
                </div>

                <div v-else class="space-y-4">
                  <div 
                    v-for="pcma in pcmas" 
                    :key="pcma.id"
                    class="border rounded-lg p-4 hover:bg-gray-50 transition-colors cursor-pointer"
                    @click="viewPCMA(pcma)"
                  >
                    <div class="flex items-center justify-between">
                      <div class="flex items-center space-x-4">
                        <div class="w-10 h-10 bg-gray-200 rounded-full flex items-center justify-center">
                          <span class="text-lg">{{ getPCMAIcon(pcma.type) }}</span>
                        </div>
                        <div>
                          <h4 class="font-medium text-gray-900">{{ pcma.type_display }}</h4>
                          <p class="text-sm text-gray-600">
                            {{ pcma.assessor_name || 'Assesseur non assigné' }}
                          </p>
                          <p class="text-xs text-gray-500">
                            {{ formatDate(pcma.created_at) }}
                            <span v-if="pcma.completed_at">
                              • Complété le {{ formatDate(pcma.completed_at) }}
                            </span>
                          </p>
                        </div>
                      </div>
                      <div class="flex items-center space-x-3">
                        <span class="text-xs px-2 py-1 rounded-full" :class="getPCMAStatusClass(pcma.status)">
                          {{ pcma.status_display }}
                        </span>
                        <span class="text-gray-400">→</span>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Injuries Section -->
              <div v-if="activeSection === 'injuries'" class="space-y-4">
                <div v-if="injuries.length === 0" class="text-center py-12">
                  <div class="text-6xl mb-4">🩹</div>
                  <h3 class="text-lg font-medium text-gray-900 mb-2">Aucune blessure enregistrée</h3>
                  <p class="text-gray-600 mb-4">Aucune blessure n'a été enregistrée pour cet athlète.</p>
                  <button 
                    @click="navigateToInjuryForm"
                    class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition-colors"
                  >
                    Enregistrer une blessure
                  </button>
                </div>

                <div v-else class="space-y-4">
                  <div 
                    v-for="injury in injuries" 
                    :key="injury.id"
                    class="border rounded-lg p-4 hover:bg-gray-50 transition-colors cursor-pointer"
                    @click="viewInjury(injury)"
                  >
                    <div class="flex items-center justify-between">
                      <div class="flex items-center space-x-4">
                        <div class="w-10 h-10 bg-gray-200 rounded-full flex items-center justify-center">
                          <span class="text-lg">{{ getInjuryIcon(injury.severity) }}</span>
                        </div>
                        <div>
                          <h4 class="font-medium text-gray-900">{{ injury.type }}</h4>
                          <p class="text-sm text-gray-600">{{ injury.body_zone }}</p>
                          <p class="text-xs text-gray-500">
                            {{ formatDate(injury.date) }}
                            <span v-if="injury.estimated_recovery_days">
                              • Récupération estimée: {{ injury.estimated_recovery_days }} jours
                            </span>
                          </p>
                        </div>
                      </div>
                      <div class="flex items-center space-x-3">
                        <span class="text-xs px-2 py-1 rounded-full" :class="getInjuryStatusClass(injury.status)">
                          {{ injury.status_display }}
                        </span>
                        <button 
                          v-if="injury.status === 'open'"
                          @click.stop="validateRTP(injury)"
                          :disabled="rtpValidationLoading"
                          class="px-2 py-1 bg-green-600 text-white text-xs rounded-md hover:bg-green-700 transition-colors disabled:opacity-50"
                        >
                          {{ rtpValidationLoading ? 'Validation...' : 'RTP' }}
                        </button>
                        <span class="text-gray-400">→</span>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Concussions Section -->
              <div v-if="activeSection === 'concussions'" class="space-y-4">
                <div v-if="scatAssessments.length === 0" class="text-center py-12">
                  <div class="text-6xl mb-4">🧠</div>
                  <h3 class="text-lg font-medium text-gray-900 mb-2">Aucune évaluation SCAT</h3>
                  <p class="text-gray-600 mb-4">Aucune évaluation SCAT n'a été effectuée pour cet athlète.</p>
                  <button 
                    @click="navigateToSCATForm"
                    class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition-colors"
                  >
                    Effectuer une évaluation SCAT
                  </button>
                </div>

                <div v-else class="space-y-4">
                  <div 
                    v-for="assessment in scatAssessments" 
                    :key="assessment.id"
                    class="border rounded-lg p-4 hover:bg-gray-50 transition-colors cursor-pointer"
                    @click="viewSCATAssessment(assessment)"
                  >
                    <div class="flex items-center justify-between">
                      <div class="flex items-center space-x-4">
                        <div class="w-10 h-10 bg-gray-200 rounded-full flex items-center justify-center">
                          <span class="text-lg">{{ getSCATIcon(assessment.assessment_type) }}</span>
                        </div>
                        <div>
                          <h4 class="font-medium text-gray-900">{{ assessment.assessment_type_display }}</h4>
                          <p class="text-sm text-gray-600">
                            {{ assessment.assessor_name || 'Assesseur non assigné' }}
                            <span v-if="assessment.scat_score">• Score: {{ assessment.scat_score }}</span>
                          </p>
                          <p class="text-xs text-gray-500">
                            {{ formatDate(assessment.assessment_date) }}
                            <span v-if="assessment.concussion_confirmed" class="text-red-600">
                              • Commotion confirmée
                            </span>
                          </p>
                        </div>
                      </div>
                      <div class="flex items-center space-x-3">
                        <span class="text-xs px-2 py-1 rounded-full" :class="getSCATResultClass(assessment.result)">
                          {{ assessment.result_display }}
                        </span>
                        <span class="text-gray-400">→</span>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Medical Notes Section -->
              <div v-if="activeSection === 'notes'" class="space-y-4">
                <div v-if="medicalNotes.length === 0" class="text-center py-12">
                  <div class="text-6xl mb-4">📝</div>
                  <h3 class="text-lg font-medium text-gray-900 mb-2">Aucune note médicale</h3>
                  <p class="text-gray-600 mb-4">Aucune note médicale n'a été créée pour cet athlète.</p>
                  <button 
                    @click="navigateToNoteEditor"
                    class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition-colors"
                  >
                    Créer une note médicale
                  </button>
                </div>

                <div v-else class="space-y-4">
                  <div 
                    v-for="note in medicalNotes" 
                    :key="note.id"
                    class="border rounded-lg p-4 hover:bg-gray-50 transition-colors cursor-pointer"
                    @click="viewMedicalNote(note)"
                  >
                    <div class="flex items-center justify-between">
                      <div class="flex items-center space-x-4">
                        <div class="w-10 h-10 bg-gray-200 rounded-full flex items-center justify-center">
                          <span class="text-lg">{{ getNoteIcon(note.note_type) }}</span>
                        </div>
                        <div>
                          <h4 class="font-medium text-gray-900">{{ note.note_type }}</h4>
                          <p class="text-sm text-gray-600">
                            {{ note.approved_by_physician_name || 'Non signé' }}
                            <span v-if="note.generated_by_ai" class="text-blue-600">• IA</span>
                          </p>
                          <p class="text-xs text-gray-500">
                            {{ formatDate(note.created_at) }}
                            <span v-if="note.signed_at">
                              • Signé le {{ formatDate(note.signed_at) }}
                            </span>
                          </p>
                        </div>
                      </div>
                      <div class="flex items-center space-x-3">
                        <span class="text-xs px-2 py-1 rounded-full" :class="getNoteStatusClass(note.status)">
                          {{ note.status_display }}
                        </span>
                        <span class="text-gray-400">→</span>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Vaccination Section -->
              <div v-if="activeSection === 'vaccinations'" class="space-y-4">
                <VaccinationRecordView :athlete-id="id" />
              </div>

              <!-- Dental Chart Section -->
              <div v-if="activeSection === 'dental'" class="space-y-4">
                <div class="bg-white rounded-lg shadow p-6">
                  <h3 class="text-lg font-semibold text-gray-900 mb-4">Schéma Dentaire</h3>
                  <p class="text-gray-600">Interface du schéma dentaire à implémenter</p>
                </div>
              </div>

              <!-- Postural Analysis Section -->
              <div v-if="activeSection === 'postural'" class="space-y-4">
                <div class="bg-white rounded-lg shadow">
                  <div class="p-6 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                      <div>
                        <h3 class="text-lg font-semibold text-gray-900">Analyse Posturale</h3>
                        <p class="text-sm text-gray-600">Évaluation interactive de la posture avec images anatomiques</p>
                      </div>
                      <button 
                        @click="navigateToPosturalAssessment"
                        class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors text-sm"
                      >
                        + Nouvelle Évaluation
                      </button>
                    </div>
                  </div>
                  
                  <!-- Interactive Postural Chart Container -->
                  <div class="p-4">
                    <div class="bg-gray-50 rounded-lg border-2 border-dashed border-gray-300 p-6">
                      <div class="text-center">
                        <div class="text-4xl mb-4">🧍</div>
                        <h4 class="text-lg font-medium text-gray-900 mb-2">Interface d'Analyse Posturale</h4>
                        <p class="text-sm text-gray-600 mb-4">
                          Utilisez les outils interactifs pour analyser la posture du joueur
                        </p>
                        
                        <!-- Compact Postural Chart -->
                        <div class="max-w-md mx-auto">
                          <div class="bg-white rounded-lg shadow-sm border p-4">
                            <!-- View Selector -->
                            <div class="flex items-center justify-center space-x-2 mb-4">
                              <button 
                                @click="currentPosturalView = 'anterior'"
                                :class="['px-3 py-1 rounded text-sm', currentPosturalView === 'anterior' ? 'bg-blue-500 text-white' : 'bg-gray-200 text-gray-700']"
                              >
                                Vue Antérieure
                              </button>
                              <button 
                                @click="currentPosturalView = 'posterior'"
                                :class="['px-3 py-1 rounded text-sm', currentPosturalView === 'posterior' ? 'bg-blue-500 text-white' : 'bg-gray-200 text-gray-700']"
                              >
                                Vue Postérieure
                              </button>
                              <button 
                                @click="currentPosturalView = 'lateral'"
                                :class="['px-3 py-1 rounded text-sm', currentPosturalView === 'lateral' ? 'bg-blue-500 text-white' : 'bg-gray-200 text-gray-700']"
                              >
                                Vue Latérale
                              </button>
                            </div>
                            
                            <!-- Compact Chart Container -->
                            <div class="relative mx-auto" style="width: 150px; height: 200px;">
                              <div class="w-full h-full bg-gradient-to-b from-blue-50 to-indigo-50 rounded-lg border flex items-center justify-center">
                                <div class="text-center">
                                  <div class="text-4xl mb-1">🧍</div>
                                  <p class="text-xs text-gray-600">{{ getPosturalViewLabel() }}</p>
                                </div>
                              </div>
                              
                              <!-- Quick Tools Overlay -->
                              <div class="absolute top-1 right-1 flex space-x-1">
                                <button 
                                  @click="addPosturalMarker"
                                  class="w-5 h-5 bg-red-500 text-white rounded-full text-xs flex items-center justify-center hover:bg-red-600"
                                  title="Ajouter marqueur"
                                >
                                  🎯
                                </button>
                                <button 
                                  @click="addPosturalAngle"
                                  class="w-5 h-5 bg-blue-500 text-white rounded-full text-xs flex items-center justify-center hover:bg-blue-600"
                                  title="Mesurer angle"
                                >
                                  📐
                                </button>
                              </div>
                            </div>
                            
                            <!-- Quick Actions -->
                            <div class="flex justify-center space-x-2 mt-4">
                              <button 
                                @click="clearPosturalData"
                                class="px-3 py-1 bg-gray-500 text-white rounded text-xs hover:bg-gray-600"
                              >
                                Effacer
                              </button>
                              <button 
                                @click="savePosturalAssessment"
                                class="px-3 py-1 bg-green-500 text-white rounded text-xs hover:bg-green-600"
                              >
                                Sauvegarder
                              </button>
                            </div>
                          </div>
                        </div>
                        
                        <div class="mt-4">
                          <button 
                            @click="navigateToPosturalAssessment"
                            class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors text-sm"
                          >
                            Ouvrir l'analyse complète
                          </button>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Imaging Section -->
              <div v-if="activeSection === 'imaging'" class="space-y-4">
                <AthleteImaging :athlete-id="id" />
              </div>

            </div>
          </div>
        </div>
      </div>

      <!-- RIGHT PANEL: Clinical Intelligence (25%) -->
      <div class="col-span-3 bg-white border-l border-gray-200 flex flex-col">
        
        <!-- Health Score Meter -->
        <div class="p-6 border-b border-gray-200">
          <h3 class="text-sm font-semibold text-gray-900 mb-4">Score de Santé</h3>
          <div class="w-full h-32 bg-gray-100 rounded-lg flex items-center justify-center">
            <HealthScoreMeter :health-data="healthData" />
          </div>
        </div>

        <!-- Active Alerts -->
        <div class="p-6 border-b border-gray-200">
          <h3 class="text-sm font-semibold text-gray-900 mb-4">Alertes Actives</h3>
          <div v-if="athlete?.risk_alerts?.filter(a => !a.resolved).length" class="space-y-3">
            <div 
              v-for="alert in athlete.risk_alerts.filter(a => !a.resolved).slice(0, 3)" 
              :key="alert.id"
              class="bg-red-50 border-l-4 border-red-500 p-3 rounded-r-lg"
            >
              <div class="flex items-start space-x-2">
                <span class="text-red-600 text-sm">🚨</span>
                <div class="flex-1">
                  <p class="text-sm font-medium text-red-800">{{ alert.title }}</p>
                  <p class="text-xs text-red-600 mt-1">{{ alert.description }}</p>
                </div>
              </div>
            </div>
          </div>
          <div v-else class="text-center py-4">
            <p class="text-sm text-green-600 font-medium">Aucune alerte active</p>
            <p class="text-xs text-gray-500 mt-1">Statut normal</p>
          </div>
        </div>

        <!-- Quick Actions Toolbar -->
        <div class="p-6 border-b border-gray-200">
          <h3 class="text-sm font-semibold text-gray-900 mb-4">Actions Rapides</h3>
          <div class="space-y-2">
            <button 
              @click="navigateToInjuryForm"
              class="w-full text-left px-3 py-2 text-sm bg-orange-50 text-orange-700 rounded-md hover:bg-orange-100 transition-colors flex items-center space-x-2"
            >
              <span>🩹</span>
              <span>Nouvelle Blessure</span>
            </button>
            <button 
              @click="navigateToNoteEditor"
              class="w-full text-left px-3 py-2 text-sm bg-blue-50 text-blue-700 rounded-md hover:bg-blue-100 transition-colors flex items-center space-x-2"
            >
              <span>📝</span>
              <span>Nouvelle Note (IA)</span>
            </button>
            <button 
              @click="navigateToSCATForm"
              class="w-full text-left px-3 py-2 text-sm bg-purple-50 text-purple-700 rounded-md hover:bg-purple-100 transition-colors flex items-center space-x-2"
            >
              <span>🧠</span>
              <span>Lancer un SCAT</span>
            </button>
          </div>
        </div>

        <!-- IPS Menu -->
        <div class="p-6">
          <h3 class="text-sm font-semibold text-gray-900 mb-4">International Patient Summary</h3>
          <div class="space-y-2">
            <button 
              @click="generateIPS"
              class="w-full text-left px-3 py-2 text-sm bg-green-50 text-green-700 rounded-md hover:bg-green-100 transition-colors flex items-center space-x-2"
            >
              <span>📋</span>
              <span>Générer IPS</span>
            </button>
            <button 
              @click="downloadIPS"
              class="w-full text-left px-3 py-2 text-sm bg-gray-50 text-gray-700 rounded-md hover:bg-gray-100 transition-colors flex items-center space-x-2"
            >
              <span>⬇️</span>
              <span>Télécharger IPS</span>
            </button>
          </div>
        </div>

      </div>
    </div>

    <!-- Error State -->
    <div v-else-if="error" class="text-center py-12">
      <div class="text-6xl mb-4">❌</div>
      <h3 class="text-lg font-medium text-gray-900 mb-2">Erreur de chargement</h3>
      <p class="text-gray-600 mb-4">{{ error }}</p>
      <button 
        @click="loadAthleteData"
        class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition-colors"
      >
        Réessayer
      </button>
    </div>

    <!-- RTP Validation Modal -->
    <div v-if="showRTPModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4">
        <div class="p-6">
          <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-medium text-gray-900">Validation Retour au Jeu</h3>
            <button 
              @click="closeRTPModal"
              class="text-gray-400 hover:text-gray-600"
            >
              ✕
            </button>
          </div>

          <div v-if="rtpValidationLoading" class="text-center py-8">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-red-600 mx-auto mb-4"></div>
            <p class="text-gray-600">Analyse en cours...</p>
          </div>

          <div v-else-if="rtpValidationResult" class="space-y-4">
            <!-- Status -->
            <div class="text-center">
              <div class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium mb-2" 
                   :class="getRTPStatusClass(rtpValidationResult.status)">
                {{ rtpValidationResult.status }}
              </div>
              <p class="text-sm text-gray-600">Confiance: {{ Math.round(rtpValidationResult.confidence * 100) }}%</p>
            </div>

            <!-- Recommendation -->
            <div class="bg-gray-50 p-4 rounded-md">
              <h4 class="text-sm font-medium text-gray-900 mb-2">Recommandation IA</h4>
              <p class="text-sm text-gray-700">{{ rtpValidationResult.recommendation }}</p>
            </div>

            <!-- Risk Assessment -->
            <div v-if="rtpValidationResult.risk_assessment" class="bg-red-50 p-4 rounded-md">
              <h4 class="text-sm font-medium text-red-800 mb-2">Évaluation des Risques</h4>
              <p class="text-sm text-red-700">{{ rtpValidationResult.risk_assessment }}</p>
            </div>

            <!-- Next Steps -->
            <div v-if="rtpValidationResult.next_steps?.length" class="bg-blue-50 p-4 rounded-md">
              <h4 class="text-sm font-medium text-blue-800 mb-2">Prochaines Étapes</h4>
              <ul class="text-sm text-blue-700 space-y-1">
                <li v-for="step in rtpValidationResult.next_steps" :key="step" class="flex items-start">
                  <span class="mr-2">•</span>
                  <span>{{ step }}</span>
                </li>
              </ul>
            </div>

            <!-- Days in Rehab -->
            <div class="bg-green-50 p-4 rounded-md">
              <h4 class="text-sm font-medium text-green-800 mb-2">Informations de Réhabilitation</h4>
              <p class="text-sm text-green-700">
                Jours en réhabilitation: {{ rtpValidationResult.days_in_rehab }}
              </p>
            </div>
          </div>

          <div class="flex justify-end space-x-3 mt-6">
            <button 
              @click="closeRTPModal"
              class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 transition-colors"
            >
              Fermer
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import HealthScoreMeter from '../components/HealthScoreMeter.vue'
import AthleteImaging from '../components/AthleteImaging.vue'
import VaccinationRecordView from '../components/VaccinationRecordView.vue' // Added VaccinationRecordView import

export default {
  name: 'AthleteProfile',
  components: {
    HealthScoreMeter,
    AthleteImaging,
    VaccinationRecordView // Added VaccinationRecordView to components
  },
  props: {
    id: {
      type: [String, Number],
      required: true
    }
  },
  data() {
    return {
      athlete: null,
      pcmas: [],
      injuries: [],
      scatAssessments: [],
      medicalNotes: [],
      vaccinations: [], // Added vaccinations data
      healthData: {
        current_score: null,
        historical_scores: [],
        average_score_30_days: 0,
        trend_analysis: {},
      },
      loading: true,
      error: null,
      activeSection: 'dashboard',
      rtpValidationLoading: false,
      rtpValidationResult: null,
      showRTPModal: false,
      currentPosturalView: 'anterior',
      navigationItems: [
        { key: 'dashboard', label: 'Dashboard / Résumé', icon: '📊' },
        { key: 'pcma', label: 'PCMA', icon: '📋', badge: null },
        { key: 'injuries', label: 'Historique des Blessures', icon: '🩹', badge: null },
        { key: 'concussions', label: 'Évaluations SCAT', icon: '🧠', badge: null },
        { key: 'vaccinations', label: 'Vaccinations', icon: '💉', badge: null },
        { key: 'notes', label: 'Notes Médicales (IA)', icon: '📝', badge: null },
        { key: 'dental', label: 'Schéma Dentaire', icon: '🦷', badge: null },
        { key: 'postural', label: 'Analyse Posturale', icon: '🧍', badge: null },
        { key: 'imaging', label: 'Imagerie (PACS)', icon: '📷', badge: null }
      ]
    }
  },
  async mounted() {
    await this.loadAthleteData()
    this.updateNavigationBadges()
  },
  methods: {
    async loadAthleteData() {
      this.loading = true
      this.error = null
      
      try {
        // Load athlete data
        const athleteResponse = await this.$http.get(`/api/v1/athletes/${this.id}`)
        this.athlete = athleteResponse.data.data

        // Load PCMA history
        const pcmaResponse = await this.$http.get(`/api/v1/athletes/${this.id}/pcmas`)
        this.pcmas = pcmaResponse.data.data

        // Load injury history
        const injuryResponse = await this.$http.get(`/api/v1/athletes/${this.id}/injuries`)
        this.injuries = injuryResponse.data.data

        // Load SCAT assessments
        const scatResponse = await this.$http.get(`/api/v1/athletes/${this.id}/scat-assessments`)
        this.scatAssessments = scatResponse.data.data

        // Load medical notes
        const notesResponse = await this.$http.get(`/api/v1/athletes/${this.id}/medical-notes`)
        this.medicalNotes = notesResponse.data.data

        // Load vaccination records
        const vaccinationResponse = await this.$http.get(`/api/v1/athletes/${this.id}/immunisations`)
        this.vaccinations = vaccinationResponse.data.data

        // Load health summary
        const healthResponse = await this.$http.get(`/api/v1/athletes/${this.id}/health-summary`)
        this.healthData = healthResponse.data.data

      } catch (error) {
        console.error('Error loading athlete data:', error)
        this.error = 'Erreur lors du chargement des données de l\'athlète'
      } finally {
        this.loading = false
      }
    },

    updateNavigationBadges() {
      // Update badges based on data
      this.navigationItems = this.navigationItems.map(item => {
        let badge = null
        switch (item.key) {
          case 'pcma':
            const pendingPCMAs = this.pcmas.filter(p => p.status === 'pending').length
            badge = pendingPCMAs > 0 ? pendingPCMAs : null
            break
          case 'injuries':
            const activeInjuries = this.injuries.filter(i => i.status === 'open').length
            badge = activeInjuries > 0 ? activeInjuries : null
            break
          case 'concussions':
            const recentSCATs = this.scatAssessments.filter(s => 
              new Date(s.assessment_date) > new Date(Date.now() - 7 * 24 * 60 * 60 * 1000)
            ).length
            badge = recentSCATs > 0 ? recentSCATs : null
            break
          case 'notes':
            const unsignedNotes = this.medicalNotes.filter(n => n.status === 'draft').length
            badge = unsignedNotes > 0 ? unsignedNotes : null
            break
          case 'vaccinations':
            const pendingVaccinations = this.vaccinations.filter(v => v.status === 'pending').length
            badge = pendingVaccinations > 0 ? pendingVaccinations : null
            break
        }
        return { ...item, badge }
      })
    },

    getSectionTitle() {
      const titles = {
        dashboard: 'Dashboard / Résumé',
        pcma: 'Historique des PCMA',
        injuries: 'Historique des Blessures',
        concussions: 'Évaluations SCAT',
        vaccinations: 'Dossier de Vaccination',
        notes: 'Notes Médicales',
        dental: 'Schéma Dentaire',
        postural: 'Analyse Posturale',
        imaging: 'Imagerie (PACS)'
      }
      return titles[this.activeSection] || 'Section'
    },

    getSectionDescription() {
      const descriptions = {
        dashboard: 'Vue d\'ensemble de la santé de l\'athlète',
        pcma: 'Évaluations PCMA (Pré-Competition Medical Assessment)',
        injuries: 'Historique complet des blessures et récupérations',
        concussions: 'Évaluations SCAT (Sport Concussion Assessment Tool)',
        vaccinations: 'Gestion des vaccins et synchronisation avec le registre national',
        notes: 'Notes médicales et observations cliniques',
        dental: 'Schéma dentaire et santé bucco-dentaire',
        postural: 'Analyse posturale et évaluations physiques',
        imaging: 'Images médicales et radiographies'
      }
      return descriptions[this.activeSection] || 'Description de la section'
    },

    getInitials(name) {
      return name.split(' ').map(n => n[0]).join('').toUpperCase()
    },

    formatDate(date) {
      if (!date) return 'N/A'
      return new Date(date).toLocaleDateString('fr-FR')
    },

    formatGender(gender) {
      const genders = {
        male: 'Masculin',
        female: 'Féminin',
        other: 'Autre'
      }
      return genders[gender] || gender
    },

    getHealthScoreColor(score) {
      if (score >= 80) return 'text-green-600'
      if (score >= 60) return 'text-yellow-600'
      return 'text-red-600'
    },

    getPCMAIcon(type) {
      const icons = {
        bpma: '📋',
        cardio: '❤️',
        dental: '🦷'
      }
      return icons[type] || '📋'
    },

    getPCMAStatusClass(status) {
      switch (status) {
        case 'completed': return 'bg-green-100 text-green-800'
        case 'pending': return 'bg-yellow-100 text-yellow-800'
        case 'failed': return 'bg-red-100 text-red-800'
        case 'cancelled': return 'bg-gray-100 text-gray-800'
        default: return 'bg-gray-100 text-gray-800'
      }
    },

    navigateToPCMAForm() {
      this.$router.push({ 
        name: 'medical.pcma.form', 
        params: { athleteId: this.id }
      })
    },

    viewPCMA(pcma) {
      this.$router.push({ 
        name: 'medical.pcma.detail', 
        params: { id: pcma.id }
      })
    },

    navigateToInjuryForm() {
      this.$router.push({ 
        name: 'medical.injury.form', 
        params: { athleteId: this.id }
      })
    },

    viewInjury(injury) {
      this.$router.push({ 
        name: 'medical.injury.detail', 
        params: { id: injury.id }
      })
    },

    navigateToSCATForm() {
      this.$router.push({ 
        name: 'medical.scat.form', 
        params: { athleteId: this.id }
      })
    },

    viewSCATAssessment(assessment) {
      this.$router.push({ 
        name: 'medical.scat.detail', 
        params: { id: assessment.id }
      })
    },

    navigateToNoteEditor() {
      this.$router.push({ 
        name: 'medical.notes.editor', 
        params: { athleteId: this.id }
      })
    },

    viewMedicalNote(note) {
      this.$router.push({ 
        name: 'medical.notes.detail', 
        params: { id: note.id }
      })
    },

    getInjuryIcon(severity) {
      const icons = {
        minor: '🟢',
        moderate: '🟡',
        severe: '🔴'
      }
      return icons[severity] || '🩹'
    },

    getInjuryStatusClass(status) {
      switch (status) {
        case 'open': return 'bg-red-100 text-red-800'
        case 'resolved': return 'bg-green-100 text-green-800'
        case 'recurring': return 'bg-yellow-100 text-yellow-800'
        default: return 'bg-gray-100 text-gray-800'
      }
    },

    getSCATIcon(type) {
      const icons = {
        baseline: '📊',
        post_injury: '🩹',
        follow_up: '📈'
      }
      return icons[type] || '🧠'
    },

    getSCATResultClass(result) {
      switch (result) {
        case 'normal': return 'bg-green-100 text-green-800'
        case 'abnormal': return 'bg-red-100 text-red-800'
        case 'unclear': return 'bg-yellow-100 text-yellow-800'
        default: return 'bg-gray-100 text-gray-800'
      }
    },

    getNoteIcon(type) {
      const icons = {
        consultation: '👨‍⚕️',
        examination: '🔍',
        treatment: '💊',
        follow_up: '📋',
        emergency: '🚨'
      }
      return icons[type] || '📝'
    },

    getNoteStatusClass(status) {
      switch (status) {
        case 'draft': return 'bg-gray-100 text-gray-800'
        case 'approved': return 'bg-blue-100 text-blue-800'
        case 'signed': return 'bg-green-100 text-green-800'
        default: return 'bg-gray-100 text-gray-800'
      }
    },

    getRTPStatusClass(status) {
      switch (status) {
        case 'Ready': return 'bg-green-100 text-green-800'
        case 'Conditional': return 'bg-yellow-100 text-yellow-800'
        case 'At Risk': return 'bg-orange-100 text-orange-800'
        case 'Not Ready': return 'bg-red-100 text-red-800'
        case 'Error': return 'bg-gray-100 text-gray-800'
        default: return 'bg-gray-100 text-gray-800'
      }
    },

    async validateRTP(injury) {
      this.rtpValidationLoading = true
      this.showRTPModal = true
      
      try {
        const response = await this.$http.get(`/api/v1/injuries/${injury.id}/validate-rtp`)
        this.rtpValidationResult = response.data.data.rtp_validation
        this.$toast.success('Validation RTP terminée')
      } catch (error) {
        console.error('Error validating RTP:', error)
        this.$toast.error('Erreur lors de la validation RTP')
        this.rtpValidationResult = {
          status: 'Error',
          recommendation: 'Erreur lors de la validation. Veuillez réessayer.'
        }
      } finally {
        this.rtpValidationLoading = false
      }
    },

    closeRTPModal() {
      this.showRTPModal = false
      this.rtpValidationResult = null
    },

    generateIPS() {
      // TODO: Implement IPS generation
      this.$toast.info('Génération IPS en cours...')
    },

    downloadIPS() {
      // TODO: Implement IPS download
      this.$toast.info('Téléchargement IPS en cours...')
    },

    // Postural Analysis Methods
    getPosturalViewLabel() {
      const labels = {
        anterior: 'Vue Antérieure',
        posterior: 'Vue Postérieure',
        lateral: 'Vue Latérale'
      }
      return labels[this.currentPosturalView] || 'Vue'
    },

    addPosturalMarker() {
      this.$toast.info('Fonctionnalité de marqueur à implémenter')
    },

    addPosturalAngle() {
      this.$toast.info('Fonctionnalité de mesure d\'angle à implémenter')
    },

    clearPosturalData() {
      this.$toast.info('Données posturales effacées')
    },

    savePosturalAssessment() {
      this.$toast.success('Évaluation posturale sauvegardée')
    },

    navigateToPosturalAssessment() {
      this.$router.push({ 
        name: 'postural-assessments.create', 
        params: { athleteId: this.id }
      })
    }
  }
}
</script>

<style scoped>
.clinical-workstation {
  @apply h-screen overflow-hidden;
}
</style> 