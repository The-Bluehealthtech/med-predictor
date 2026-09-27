<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('health_records_extra.label_08a045dd9b3c') }}</title>
    <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 20px;
            background: #f8fafc;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            text-align: center;
        }
        
        .content {
            padding: 20px;
        }
        
        .tabs {
            display: flex;
            border-bottom: 2px solid #e2e8f0;
            margin-bottom: 20px;
        }
        
        .tab {
            padding: 10px 20px;
            cursor: pointer;
            border-bottom: 2px solid transparent;
            transition: all 0.3s ease;
        }
        
        .tab.active {
            border-bottom-color: #3b82f6;
            color: #3b82f6;
            font-weight: bold;
        }
        
        .tab-content {
            display: none;
        }
        
        .tab-content.active {
            display: block;
        }
        
        .dental-chart-container {
            display: flex;
            gap: 20px;
            align-items: flex-start;
        }
        
        .chart-section {
            flex: 1;
        }
        
        .info-section {
            flex: 0 0 300px;
            background: #f8fafc;
            padding: 20px;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
        }
        
        .svg-container {
            background: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            border: 1px solid #e2e8f0;
        }
        
        .tooth-info {
            margin-bottom: 20px;
        }
        
        .tooth-info h3 {
            color: #374151;
            margin: 0 0 10px 0;
            font-size: 1.1em;
        }
        
        .tooth-details {
            background: white;
            padding: 15px;
            border-radius: 6px;
            border-left: 4px solid #3b82f6;
        }
        
        .btn {
            background: #3b82f6;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 6px;
            cursor: pointer;
            margin: 5px;
            font-size: 14px;
            transition: all 0.3s ease;
        }
        
        .btn:hover {
            background: #2563eb;
        }
        
        .no-selection {
            text-align: center;
            color: #6b7280;
            font-style: italic;
            padding: 40px 20px;
        }
        
        .form-group {
            margin-bottom: 15px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
            color: #374151;
        }
        
        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 8px 12px;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            font-size: 14px;
        }
        
        .form-group textarea {
            resize: vertical;
            min-height: 80px;
        }
    </style>
</head>
<body>
    <div id="app">
        <div class="container">
            <div class="header">
                <h1>{{ __('health_records_extra.label_84d669ed7554') }}</h1>
                <p>{{ __('health_records_extra.label_abe516745002') }}</p>
            </div>
            
            <div class="content">
                <div class="tabs">
                    <div 
                        v-for="tab in tabs" 
                        :key="tab.id"
                        :class="['tab', { active: activeTab === tab.id }]"
                        @click="activeTab = tab.id"
                    >
                        {{ tab.name }}
                    </div>
                </div>
                
                <!-- Onglet Général -->
                <div v-show="activeTab === 'general'" class="tab-content">
                    <h2>{{ __('health_records_create.tab_general') }}</h2>
                    <div class="form-group">
                        <label>{{ __('health_records_extra.label_d6717996e7bb') }}</label>
                        <input type="text" v-model="patientName" placeholder="{{ __('health_records_extra.label_d6717996e7bb') }}">
                    </div>
                    <div class="form-group">
                        <label>{{ __('health_records_extra.label_8851f385b271') }}</label>
                        <input type="date" v-model="patientBirthDate">
                    </div>
                    <div class="form-group">
                        <label>{{ __('health_records_extra.label_3f6ad7aca547') }}</label>
                        <textarea v-model="generalNotes" placeholder="{{ __('health_records_extra.label_4876348a3862') }}"></textarea>
                    </div>
                </div>
                
                <!-- Onglet Dossier Dentaire -->
                <div v-show="activeTab === 'dental'" class="tab-content">
                    <h2>{{ __('health_records_create.dental_record_heading') }}</h2>
                    
                    <div class="dental-chart-container">
                        <div class="chart-section">
                            <div class="svg-container">
                                <object 
                                    data="/images/dental-chart-interactive.svg" 
                                    type="image/svg+xml"
                                    width="100%"
                                    height="600"
                                    @load="initializeDentalChart">
                                </object>
                            </div>
                            
                            <div style="margin-top: 20px;">
                                <button @click="clearDentalSelection" class="btn">{{ __('health_records_extra.label_4bddb0193083') }}</button>
                                <button @click="saveDentalData" class="btn">{{ __('health_records_create.save_button') }}</button>
                            </div>
                        </div>
                        
                        <div class="info-section">
                            <div v-if="selectedDentalTooth" class="tooth-info">
                                <h3>Dent sélectionnée : {{ selectedDentalTooth }}</h3>
                                <div class="tooth-details">
                                    <div class="form-group">
                                        <label>{{ __('health_records_extra.label_55b97e79c228') }}</label>
                                        <select v-model="dentalToothStatus">
                                            <option value="healthy">{{ __('health_records_edit.healthy_2') }}</option>
                                            <option value="cavity">{{ __('health_records_edit.caries') }}</option>
                                            <option value="filling">{{ __('health_records_create.tooth_status_filling') }}</option>
                                            <option value="crown">{{ __('health_records_create.tooth_status_crown') }}</option>
                                            <option value="missing">{{ __('health_records_edit.missing') }}</option>
                                            <option value="implant">Implant</option>
                                            <option value="treatment">{{ __('health_records_create.tooth_status_treatment') }}</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>Notes</label>
                                        <textarea v-model="dentalToothNotes" placeholder="{{ __('Notes sur cette dent') }}"></textarea>
                                    </div>
                                </div>
                            </div>
                            
                            <div v-else class="no-selection">
                                <h3>{{ __('health_records_extra.label_f2079eb60ab2') }}</h3>
                                <p>{{ __('health_records_extra.label_f1f2198da75c') }}</p>
                            </div>
                            
                            <div style="margin-top: 20px;">
                                <h4>{{ __('secretary.nav_stats') }}</h4>
                                <div style="font-size: 0.9em; color: #6b7280;">
                                    <p>Dents saines: {{ dentalStats.healthy }}</p>
                                    <p>Caries: {{ dentalStats.cavity }}</p>
                                    <p>Obturations: {{ dentalStats.filling }}</p>
                                    <p>Couronnes: {{ dentalStats.crown }}</p>
                                    <p>Manquantes: {{ dentalStats.missing }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Onglet Autres -->
                <div v-show="activeTab === 'other'" class="tab-content">
                    <h2>{{ __('health_records_extra.label_61e32119fd51') }}</h2>
                    <p>{{ __('health_records_extra.label_b1aebeaba826') }}</p>
                </div>
            </div>
        </div>
    </div>

    <script>
        const { createApp, ref } = Vue;
        
        createApp({
            setup() {
                const activeTab = ref('general');
                const patientName = ref('');
                const patientBirthDate = ref('');
                const generalNotes = ref('');
                
                // Données pour le diagramme dentaire interactif
                const selectedDentalTooth = ref(null);
                const dentalToothStatus = ref('healthy');
                const dentalToothNotes = ref('');
                const dentalHistory = ref([]);
                const dentalData = ref({});
                const dentalStats = ref({
                    healthy: 32, cavity: 0, filling: 0, crown: 0, missing: 0, implant: 0, treatment: 0, unevaluated: 0
                });

                const tabs = [
                    { id: 'general', name: '📋 Général' },
                    { id: 'dental', name: '🦷 Dossier Dentaire' },
                    { id: 'other', name: '📄 Autres' }
                ];

                // Méthodes pour le diagramme dentaire
                const initializeDentalChart = () => {
                    console.log('🦷 Initialisation du diagramme dentaire...');
                    
                    // Écouter les événements de sélection de dents
                    document.addEventListener('toothSelected', (event) => {
                        selectedDentalTooth.value = event.detail.toothId;
                        dentalHistory.value.push(event.detail.toothId);
                        
                        // Charger les données existantes de la dent
                        if (dentalData.value[event.detail.toothId]) {
                            dentalToothStatus.value = dentalData.value[event.detail.toothId].status || 'healthy';
                            dentalToothNotes.value = dentalData.value[event.detail.toothId].notes || '';
                        } else {
                            dentalToothStatus.value = 'healthy';
                            dentalToothNotes.value = '';
                        }
                        
                        console.log('🦷 Dent sélectionnée:', event.detail.toothId);
                    });
                };
                
                const clearDentalSelection = () => {
                    selectedDentalTooth.value = null;
                    dentalToothStatus.value = 'healthy';
                    dentalToothNotes.value = '';
                    const selectedToothElement = document.querySelector('.tooth.selected');
                    if (selectedToothElement) {
                        selectedToothElement.classList.remove('selected');
                    }
                };
                
                const saveDentalData = () => {
                    if (selectedDentalTooth.value) {
                        dentalData.value[selectedDentalTooth.value] = {
                            status: dentalToothStatus.value,
                            notes: dentalToothNotes.value,
                            timestamp: new Date().toISOString()
                        };
                        
                        updateDentalStats();
                        alert('✅ Données dentaires sauvegardées !');
                    } else {
                        alert('⚠️ Veuillez sélectionner une dent d\'abord.');
                    }
                };
                
                const updateDentalStats = () => {
                    // Réinitialiser les stats
                    const stats = {
                        healthy: 0, cavity: 0, filling: 0, crown: 0, missing: 0, implant: 0, treatment: 0, unevaluated: 0
                    };
                    
                    // Compter les dents par statut
                    Object.values(dentalData.value).forEach(tooth => {
                        if (stats.hasOwnProperty(tooth.status)) {
                            stats[tooth.status]++;
                        }
                    });
                    
                    // Les dents non évaluées sont celles qui n'ont pas de données
                    stats.unevaluated = 32 - Object.keys(dentalData.value).length;
                    
                    dentalStats.value = stats;
                };

                return {
                    activeTab,
                    patientName,
                    patientBirthDate,
                    generalNotes,
                    selectedDentalTooth,
                    dentalToothStatus,
                    dentalToothNotes,
                    dentalHistory,
                    dentalData,
                    dentalStats,
                    tabs,
                    initializeDentalChart,
                    clearDentalSelection,
                    saveDentalData
                };
            }
        }).mount('#app');
    </script>
</body>
</html> 