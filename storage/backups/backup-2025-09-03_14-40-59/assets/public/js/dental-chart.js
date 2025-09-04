// Composant Vue.js pour le diagramme dentaire interactif
const { createApp, ref, reactive, onMounted, watch } = Vue;

// Configuration Axios - avec vérification pour éviter l'erreur null
const csrfTokenElement = document.querySelector('meta[name="csrf-token"]');
if (csrfTokenElement) {
    axios.defaults.headers.common['X-CSRF-TOKEN'] = csrfTokenElement.getAttribute('content');
}

// Composant principal du diagramme dentaire
const DentalChart = {
    props: {
        patientId: {
            type: [String, Number],
            default: null
        },
        recordId: {
            type: [String, Number],
            default: null
        },
        apiBaseUrl: {
            type: String,
            default: '/api'
        },
        csrfToken: {
            type: String,
            default: ''
        }
    },
    setup(props) {
        // Données réactives
        const dentalData = ref({});
        const selectedTooth = ref(null);
        const showAnnotationPanel = ref(false);
        const loading = ref(false);
        const error = ref(null);
        const statistics = ref({
            healthy: 0,
            cavity: 0,
            crown: 0,
            extracted: 0,
            treatment: 0
        });

        // Variables pour les IDs (au lieu des props)
        const currentPatientId = ref(props.patientId);
        const currentRecordId = ref(props.recordId);

        // Données d'annotation réactives
        const annotationData = ref({
            toothNumber: '',
            status: 'healthy',
            notes: '',
            lastUpdated: null
        });

        // Couleurs pour chaque état
        const statusColors = {
            healthy: '#4ade80',
            cavity: '#ef4444',
            crown: '#fbbf24',
            extracted: '#9ca3af',
            treatment: '#3b82f6'
        };

        // Labels pour chaque état
        const statusLabels = {
            healthy: 'Sain',
            cavity: 'Carie',
            crown: 'Couronne',
            extracted: 'Extrait',
            treatment: 'En traitement'
        };

        // Initialiser les données dentaires par défaut
        const initializeDentalData = () => {
            console.log('Initializing dental data...');
            for (let quadrant = 1; quadrant <= 4; quadrant++) {
                for (let tooth = 1; tooth <= 8; tooth++) {
                    const toothNumber = `${quadrant}${tooth}`;
                    dentalData.value[toothNumber] = {
                        status: 'healthy',
                        notes: '',
                        lastUpdated: null
                    };
                }
            }
            updateStatistics();
        };

        // Charger les données dentaires depuis l'API
        const loadDentalData = async () => {
            console.log('Loading dental data for patient:', currentPatientId.value, 'record:', currentRecordId.value);
            
            // Réinitialiser les données d'abord
            dentalData.value = {};
            selectedTooth.value = null;
            showAnnotationPanel.value = false;
            error.value = null;
            
            if (!currentPatientId.value) {
                console.log('No patient ID provided, initializing with default data');
                initializeDentalData();
                return;
            }

            loading.value = true;

            try {
                let response;
                
                if (currentRecordId.value) {
                    // Charger un enregistrement spécifique
                    console.log('Loading specific record:', currentRecordId.value);
                    response = await axios.get(`${props.apiBaseUrl}/dental-records/${currentRecordId.value}`);
                } else {
                    // Charger tous les enregistrements du patient
                    response = await axios.get(`${props.apiBaseUrl}/dental-records`, {
                        params: { patient_id: currentPatientId.value }
                    });
                }

                console.log('API response:', response.data);

                if (response.data.success) {
                    if (currentRecordId.value && response.data.data.record) {
                        // Charger un enregistrement spécifique
                        const record = response.data.data.record;
                        console.log('Loading specific record data:', record);
                        console.log('Record dental_data:', record.dental_data);
                        Object.assign(dentalData.value, record.dental_data);
                    } else if (response.data.data.records && response.data.data.records.length > 0) {
                        // Charger le dernier enregistrement
                        const latestRecord = response.data.data.records[0];
                        console.log('Loading latest record:', latestRecord);
                        console.log('Latest record dental_data:', latestRecord.dental_data);
                        Object.assign(dentalData.value, latestRecord.dental_data);
                    } else {
                        // Aucun enregistrement trouvé, initialiser avec des données par défaut
                        console.log('No records found, initializing with default data');
                        initializeDentalData();
                    }
                    
                    // Ajouter les couleurs aux données chargées
                    Object.keys(dentalData.value).forEach(toothNumber => {
                        const toothData = dentalData.value[toothNumber];
                        if (toothData && toothData.status && !toothData.color) {
                            toothData.color = statusColors[toothData.status];
                        }
                    });
                    
                    console.log('Final dental data after loading:', dentalData.value);
                    
                    // Mettre à jour les statistiques après chargement
                    updateStatistics();
                    
                    // Réattacher les événements après chargement des données
                    setTimeout(() => {
                        attachToothClickEvents();
                    }, 100);
                } else {
                    console.log('API response not successful, initializing with default data');
                    initializeDentalData();
                }
            } catch (error) {
                console.error('Error loading dental data:', error);
                error.value = 'Erreur lors du chargement des données';
                initializeDentalData();
            } finally {
                loading.value = false;
            }
        };

        // Sauvegarder les données dentaires
        const saveDentalData = async () => {
            if (!currentPatientId.value) {
                console.log('No patient selected, cannot save');
                return;
            }

            console.log('Saving dental data...');
            loading.value = true;
            error.value = null;

            try {
                const data = {
                    patient_id: currentPatientId.value,
                    dental_data: dentalData.value,
                    status: 'completed',
                    notes: 'Examen dentaire mis à jour'
                };

                let url = `${props.apiBaseUrl}/dental-records`;
                let method = 'post';

                if (currentRecordId.value) {
                    url = `${props.apiBaseUrl}/dental-records/${currentRecordId.value}`;
                    method = 'put';
                }

                const response = await axios[method](url, data);
                
                if (response.data.success) {
                    console.log('Dental data saved successfully:', response.data);
                    updateStatistics();
                } else {
                    console.error('Save error:', response.data);
                    error.value = response.data.message || 'Erreur lors de la sauvegarde';
                }
            } catch (err) {
                console.error('Error saving dental data:', err);
                error.value = 'Erreur lors de la sauvegarde';
            } finally {
                loading.value = false;
            }
        };

        // Mettre à jour les statistiques
        const updateStatistics = () => {
            const counts = {
                healthy: 0,
                cavity: 0,
                crown: 0,
                extracted: 0,
                treatment: 0
            };

            Object.values(dentalData.value).forEach(tooth => {
                counts[tooth.status]++;
            });

            Object.assign(statistics.value, counts);
            
            // Émettre un événement pour informer la page des nouvelles statistiques
            window.dispatchEvent(new CustomEvent('statistics-updated', {
                detail: statistics.value
            }));
            
            console.log('Statistics updated:', statistics.value);
        };

        // Ouvrir le panneau d'annotation
        const openAnnotationPanel = (toothNumber) => {
            console.log('🔍 openAnnotationPanel called for tooth:', toothNumber);
            
            // Mettre à jour la dent sélectionnée
            selectedTooth.value = toothNumber;
            
            // Récupérer les données actuelles de la dent
            const toothData = dentalData.value[toothNumber] || {
                status: 'healthy',
                notes: '',
                lastUpdated: null
            };
            
            // Mettre à jour les données d'annotation
            annotationData.value.toothNumber = toothNumber;
            annotationData.value.status = toothData.status;
            annotationData.value.notes = toothData.notes;
            annotationData.value.lastUpdated = toothData.lastUpdated;
            
            // Afficher le panneau
            showAnnotationPanel.value = true;
        };

        // Fermer le panneau d'annotation
        const closeAnnotationPanel = () => {
            showAnnotationPanel.value = false;
            selectedTooth.value = null;
        };

        // Sauvegarder l'annotation
        const saveAnnotation = async () => {
            if (!selectedTooth.value) return;

            console.log('Saving annotation for tooth:', selectedTooth.value);

            // Mettre à jour les données locales
            dentalData.value[selectedTooth.value] = {
                status: annotationData.value.status,
                notes: annotationData.value.notes,
                color: statusColors[annotationData.value.status],
                lastUpdated: new Date().toISOString()
            };

            console.log('Updated dental data:', dentalData.value);
            console.log('Updated tooth data for', selectedTooth.value, ':', dentalData.value[selectedTooth.value]);

            // Mettre à jour les statistiques
            updateStatistics();

            // Sauvegarder sur le serveur
            try {
                await saveDentalData();
                console.log('Annotation saved successfully');
                
                // Afficher un message de succès
                alert(`Dent ${selectedTooth.value} sauvegardée avec succès !`);
            } catch (error) {
                console.error('Error saving annotation:', error);
                alert('Erreur lors de la sauvegarde. Veuillez réessayer.');
            }
            
            // Ne pas fermer le panneau - laisser l'utilisateur continuer avec d'autres dents
            console.log('Annotation saved, panel remains open for next tooth');
        };

        // Attacher les événements de clic aux dents
        const attachToothClickEvents = () => {
            console.log('Attaching tooth click events...');
            
            // Attendre que le DOM soit complètement rendu
            setTimeout(() => {
                const teeth = document.querySelectorAll('[data-tooth]');
                console.log('Found teeth elements:', teeth.length);
                
                teeth.forEach(tooth => {
                    const toothNumber = tooth.getAttribute('data-tooth');
                    console.log('Attaching click to tooth:', toothNumber);
                    
                    // Supprimer les anciens événements
                    tooth.removeEventListener('click', tooth._clickHandler);
                    
                    // Créer un nouveau gestionnaire
                    tooth._clickHandler = (event) => {
                        event.preventDefault();
                        event.stopPropagation();
                        console.log('Tooth clicked:', toothNumber);
                        openAnnotationPanel(toothNumber);
                    };
                    
                    // Attacher l'événement
                    tooth.addEventListener('click', tooth._clickHandler);
                    tooth.style.cursor = 'pointer';
                });
                
                console.log('Tooth click events attached successfully');
            }, 200);
        };

        // Initialisation
        onMounted(() => {
            console.log('DentalChart component mounted');
            initializeDentalData();
            
            // Attendre un peu avant d'attacher les événements
            setTimeout(() => {
                attachToothClickEvents();
            }, 100);
            
            // Écouter les événements de sélection de patient
            window.addEventListener('patient-selected', (event) => {
                if (event.detail.patientId) {
                    console.log('Patient selected event received:', event.detail.patientId);
                    // Mettre à jour les variables réactives
                    currentPatientId.value = event.detail.patientId;
                    currentRecordId.value = null; // Réinitialiser l'enregistrement
                    loadDentalData();
                }
            });

            window.addEventListener('record-selected', (event) => {
                if (event.detail.recordId) {
                    console.log('Record selected event received:', event.detail.recordId);
                    // Mettre à jour les variables réactives
                    currentRecordId.value = event.detail.recordId;
                    loadDentalData();
                }
            });
        });

        // Surveiller les changements de dentalData pour réattacher les événements
        watch(dentalData, () => {
            console.log('Dental data changed, reattaching events...');
            attachToothClickEvents();
        }, { deep: true });

        return {
            dentalData,
            selectedTooth,
            showAnnotationPanel,
            loading,
            error,
            statistics,
            annotationData,
            statusColors,
            statusLabels,
            openAnnotationPanel,
            closeAnnotationPanel,
            saveAnnotation,
            saveDentalData,
            loadDentalData
        };
    },
    template: `
        <div class="dental-chart">
            <!-- Diagramme SVG avec formes anatomiques réalistes -->
            <div class="mt-6">
                <svg viewBox="0 0 600 400" class="w-full h-auto border border-gray-300 rounded-lg bg-white">
                    <!-- Légende -->
                    <text x="300" y="20" text-anchor="middle" class="text-sm font-bold" fill="#374151">DIAGRAMME DENTAIRE - VUE OCCLUSALE</text>
                    
                    <!-- Dents supérieures droites (11-18) - Formes anatomiques ultra-réalistes -->
                    <g class="upper-right">
                        <!-- 11 - Incisive centrale droite (forme ovale naturelle avec bord supérieur concave) -->
                        <path data-tooth="11" class="tooth" d="M 95 142 Q 105 140 115 142 Q 120 144 120 147 L 120 157 Q 120 160 115 162 Q 105 164 95 162 Q 90 160 90 157 L 90 147 Q 90 144 95 142 Z M 97 162 L 113 162 L 113 172 Q 113 177 105 177 Q 97 177 97 172 Z" 
                              :fill="dentalData[11]?.color || '#f3f4f6'" stroke="#374151" stroke-width="1.5"/>
                        <text x="105" y="155" text-anchor="middle" class="text-xs font-bold" fill="#374151">11</text>
                        
                        <!-- 12 - Incisive latérale droite (forme triangulaire naturelle) -->
                        <path data-tooth="12" class="tooth" d="M 125 142 Q 132 140 140 142 Q 145 144 145 147 L 145 157 Q 145 160 140 162 Q 132 164 125 162 Q 120 160 120 157 L 120 147 Q 120 144 125 142 Z M 127 162 L 138 162 L 138 172 Q 138 177 132 177 Q 127 177 127 172 Z" 
                              :fill="dentalData[12]?.color || '#f3f4f6'" stroke="#374151" stroke-width="1.5"/>
                        <text x="132" y="155" text-anchor="middle" class="text-xs font-bold" fill="#374151">12</text>
                        
                        <!-- 13 - Canine droite (forme pointue avec cuspide prononcée) -->
                        <path data-tooth="13" class="tooth" d="M 150 137 Q 157 135 165 137 Q 170 139 170 147 L 170 162 Q 170 165 165 167 Q 157 169 150 167 Q 145 165 145 162 L 145 147 Q 145 139 150 137 Z M 155 167 L 160 167 L 160 182 Q 160 187 157 187 Q 155 187 155 182 Z" 
                              :fill="dentalData[13]?.color || '#f3f4f6'" stroke="#374151" stroke-width="1.5"/>
                        <text x="157" y="155" text-anchor="middle" class="text-xs font-bold" fill="#374151">13</text>
                        
                        <!-- 14 - Première prémolaire droite (forme avec deux cuspides distinctes) -->
                        <path data-tooth="14" class="tooth" d="M 175 142 Q 185 140 195 142 Q 200 144 200 147 L 200 157 Q 200 160 195 162 Q 185 164 175 162 Q 170 160 170 157 L 170 147 Q 170 144 175 142 Z M 177 162 L 193 162 L 193 172 Q 193 177 185 177 Q 177 177 177 172 Z" 
                              :fill="dentalData[14]?.color || '#f3f4f6'" stroke="#374151" stroke-width="1.5"/>
                        <text x="185" y="155" text-anchor="middle" class="text-xs font-bold" fill="#374151">14</text>
                        
                        <!-- 15 - Deuxième prémolaire droite (forme avec cuspides asymétriques) -->
                        <path data-tooth="15" class="tooth" d="M 205 142 Q 215 140 225 142 Q 230 144 230 147 L 230 157 Q 230 160 225 162 Q 215 164 205 162 Q 200 160 200 157 L 200 147 Q 200 144 205 142 Z M 207 162 L 223 162 L 223 172 Q 223 177 215 177 Q 207 177 207 172 Z" 
                              :fill="dentalData[15]?.color || '#f3f4f6'" stroke="#374151" stroke-width="1.5"/>
                        <text x="215" y="155" text-anchor="middle" class="text-xs font-bold" fill="#374151">15</text>
                        
                        <!-- 16 - Première molaire droite (forme large avec 4 cuspides bien définies) -->
                        <path data-tooth="16" class="tooth" d="M 235 142 Q 250 140 265 142 Q 270 144 270 147 L 270 157 Q 270 160 265 162 Q 250 164 235 162 Q 230 160 230 157 L 230 147 Q 230 144 235 142 Z M 237 162 L 263 162 L 263 172 Q 263 177 250 177 Q 237 177 237 172 Z" 
                              :fill="dentalData[16]?.color || '#f3f4f6'" stroke="#374151" stroke-width="1.5"/>
                        <text x="250" y="155" text-anchor="middle" class="text-xs font-bold" fill="#374151">16</text>
                        
                        <!-- 17 - Deuxième molaire droite (forme avec cuspides moins prononcées) -->
                        <path data-tooth="17" class="tooth" d="M 275 142 Q 290 140 305 142 Q 310 144 310 147 L 310 157 Q 310 160 305 162 Q 290 164 275 162 Q 270 160 270 157 L 270 147 Q 270 144 275 142 Z M 277 162 L 303 162 L 303 172 Q 303 177 290 177 Q 277 177 277 172 Z" 
                              :fill="dentalData[17]?.color || '#f3f4f6'" stroke="#374151" stroke-width="1.5"/>
                        <text x="290" y="155" text-anchor="middle" class="text-xs font-bold" fill="#374151">17</text>
                        
                        <!-- 18 - Troisième molaire droite (forme plus petite et arrondie) -->
                        <path data-tooth="18" class="tooth" d="M 315 142 Q 330 140 345 142 Q 350 144 350 147 L 350 157 Q 350 160 345 162 Q 330 164 315 162 Q 310 160 310 157 L 310 147 Q 310 144 315 142 Z M 317 162 L 343 162 L 343 172 Q 343 177 330 177 Q 317 177 317 172 Z" 
                              :fill="dentalData[18]?.color || '#f3f4f6'" stroke="#374151" stroke-width="1.5"/>
                        <text x="330" y="155" text-anchor="middle" class="text-xs font-bold" fill="#374151">18</text>
                    </g>
                    
                                                <!-- Dents supérieures gauches (21-28) - Formes anatomiques ultra-réalistes -->
                            <g class="upper-left">
                                <!-- 21 - Incisive centrale gauche (forme ovale naturelle) -->
                                <path data-tooth="21" class="tooth" d="M 485 142 Q 495 140 505 142 Q 510 144 510 147 L 510 157 Q 510 160 505 162 Q 495 164 485 162 Q 480 160 480 157 L 480 147 Q 480 144 485 142 Z M 487 162 L 503 162 L 503 172 Q 503 177 495 177 Q 487 177 487 172 Z" 
                                      :fill="dentalData[21]?.color || '#f3f4f6'" stroke="#374151" stroke-width="1.5"/>
                                <text x="495" y="155" text-anchor="middle" class="text-xs font-bold" fill="#374151">21</text>
                                
                                <!-- 22 - Incisive latérale gauche (forme triangulaire naturelle) -->
                                <path data-tooth="22" class="tooth" d="M 460 142 Q 467 140 475 142 Q 480 144 480 147 L 480 157 Q 480 160 475 162 Q 467 164 460 162 Q 455 160 455 157 L 455 147 Q 455 144 460 142 Z M 462 162 L 473 162 L 473 172 Q 473 177 467 177 Q 462 177 462 172 Z" 
                                      :fill="dentalData[22]?.color || '#f3f4f6'" stroke="#374151" stroke-width="1.5"/>
                                <text x="467" y="155" text-anchor="middle" class="text-xs font-bold" fill="#374151">22</text>
                                
                                <!-- 23 - Canine gauche (forme pointue avec cuspide prononcée) -->
                                <path data-tooth="23" class="tooth" d="M 435 137 Q 442 135 450 137 Q 455 139 455 147 L 455 162 Q 455 165 450 167 Q 442 169 435 167 Q 430 165 430 162 L 430 147 Q 430 139 435 137 Z M 440 167 L 445 167 L 445 182 Q 445 187 442 187 Q 440 187 440 182 Z" 
                                      :fill="dentalData[23]?.color || '#f3f4f6'" stroke="#374151" stroke-width="1.5"/>
                                <text x="442" y="155" text-anchor="middle" class="text-xs font-bold" fill="#374151">23</text>
                                
                                <!-- 24 - Première prémolaire gauche (forme avec deux cuspides distinctes) -->
                                <path data-tooth="24" class="tooth" d="M 405 142 Q 415 140 425 142 Q 430 144 430 147 L 430 157 Q 430 160 425 162 Q 415 164 405 162 Q 400 160 400 157 L 400 147 Q 400 144 405 142 Z M 407 162 L 423 162 L 423 172 Q 423 177 415 177 Q 407 177 407 172 Z" 
                                      :fill="dentalData[24]?.color || '#f3f4f6'" stroke="#374151" stroke-width="1.5"/>
                                <text x="415" y="155" text-anchor="middle" class="text-xs font-bold" fill="#374151">24</text>
                                
                                <!-- 25 - Deuxième prémolaire gauche (forme avec cuspides asymétriques) -->
                                <path data-tooth="25" class="tooth" d="M 375 142 Q 385 140 395 142 Q 400 144 400 147 L 400 157 Q 400 160 395 162 Q 385 164 375 162 Q 370 160 370 157 L 370 147 Q 370 144 375 142 Z M 377 162 L 393 162 L 393 172 Q 393 177 385 177 Q 377 177 377 172 Z" 
                                      :fill="dentalData[25]?.color || '#f3f4f6'" stroke="#374151" stroke-width="1.5"/>
                                <text x="385" y="155" text-anchor="middle" class="text-xs font-bold" fill="#374151">25</text>
                                
                                <!-- 26 - Première molaire gauche (forme large avec 4 cuspides bien définies) -->
                                <path data-tooth="26" class="tooth" d="M 335 142 Q 350 140 365 142 Q 370 144 370 147 L 370 157 Q 370 160 365 162 Q 350 164 335 162 Q 330 160 330 157 L 330 147 Q 330 144 335 142 Z M 337 162 L 363 162 L 363 172 Q 363 177 350 177 Q 337 177 337 172 Z" 
                                      :fill="dentalData[26]?.color || '#f3f4f6'" stroke="#374151" stroke-width="1.5"/>
                                <text x="350" y="155" text-anchor="middle" class="text-xs font-bold" fill="#374151">26</text>
                                
                                <!-- 27 - Deuxième molaire gauche (forme avec cuspides moins prononcées) -->
                                <path data-tooth="27" class="tooth" d="M 295 142 Q 310 140 325 142 Q 330 144 330 147 L 330 157 Q 330 160 325 162 Q 310 164 295 162 Q 290 160 290 157 L 290 147 Q 290 144 295 142 Z M 297 162 L 323 162 L 323 172 Q 323 177 310 177 Q 297 177 297 172 Z" 
                                      :fill="dentalData[27]?.color || '#f3f4f6'" stroke="#374151" stroke-width="1.5"/>
                                <text x="310" y="155" text-anchor="middle" class="text-xs font-bold" fill="#374151">27</text>
                                
                                <!-- 28 - Troisième molaire gauche (forme plus petite et arrondie) -->
                                <path data-tooth="28" class="tooth" d="M 255 142 Q 270 140 285 142 Q 290 144 290 147 L 290 157 Q 290 160 285 162 Q 270 164 255 162 Q 250 160 250 157 L 250 147 Q 250 144 255 142 Z M 257 162 L 283 162 L 283 172 Q 283 177 270 177 Q 257 177 257 172 Z" 
                                      :fill="dentalData[28]?.color || '#f3f4f6'" stroke="#374151" stroke-width="1.5"/>
                                <text x="270" y="155" text-anchor="middle" class="text-xs font-bold" fill="#374151">28</text>
                            </g>
                    
                                                <!-- Dents inférieures droites (31-38) - Formes anatomiques ultra-réalistes -->
                            <g class="lower-right">
                                <!-- 31 - Incisive centrale droite inférieure (forme ovale naturelle) -->
                                <path data-tooth="31" class="tooth" d="M 95 342 Q 105 340 115 342 Q 120 344 120 347 L 120 357 Q 120 360 115 362 Q 105 364 95 362 Q 90 360 90 357 L 90 347 Q 90 344 95 342 Z M 97 362 L 113 362 L 113 372 Q 113 377 105 377 Q 97 377 97 372 Z" 
                                      :fill="dentalData[31]?.color || '#f3f4f6'" stroke="#374151" stroke-width="1.5"/>
                                <text x="105" y="355" text-anchor="middle" class="text-xs font-bold" fill="#374151">31</text>
                                
                                <!-- 32 - Incisive latérale droite inférieure (forme triangulaire naturelle) -->
                                <path data-tooth="32" class="tooth" d="M 125 342 Q 132 340 140 342 Q 145 344 145 347 L 145 357 Q 145 360 140 362 Q 132 364 125 362 Q 120 360 120 357 L 120 347 Q 120 344 125 342 Z M 127 362 L 138 362 L 138 372 Q 138 377 132 377 Q 127 377 127 372 Z" 
                                      :fill="dentalData[32]?.color || '#f3f4f6'" stroke="#374151" stroke-width="1.5"/>
                                <text x="132" y="355" text-anchor="middle" class="text-xs font-bold" fill="#374151">32</text>
                                
                                <!-- 33 - Canine droite inférieure (forme pointue avec cuspide prononcée) -->
                                <path data-tooth="33" class="tooth" d="M 150 337 Q 157 335 165 337 Q 170 339 170 347 L 170 362 Q 170 365 165 367 Q 157 369 150 367 Q 145 365 145 362 L 145 347 Q 145 339 150 337 Z M 155 367 L 160 367 L 160 382 Q 160 387 157 387 Q 155 387 155 382 Z" 
                                      :fill="dentalData[33]?.color || '#f3f4f6'" stroke="#374151" stroke-width="1.5"/>
                                <text x="157" y="355" text-anchor="middle" class="text-xs font-bold" fill="#374151">33</text>
                                
                                <!-- 34 - Première prémolaire droite inférieure (forme avec deux cuspides distinctes) -->
                                <path data-tooth="34" class="tooth" d="M 175 342 Q 185 340 195 342 Q 200 344 200 347 L 200 357 Q 200 360 195 362 Q 185 364 175 362 Q 170 360 170 357 L 170 347 Q 170 344 175 342 Z M 177 362 L 193 362 L 193 372 Q 193 377 185 377 Q 177 377 177 372 Z" 
                                      :fill="dentalData[34]?.color || '#f3f4f6'" stroke="#374151" stroke-width="1.5"/>
                                <text x="185" y="355" text-anchor="middle" class="text-xs font-bold" fill="#374151">34</text>
                                
                                <!-- 35 - Deuxième prémolaire droite inférieure (forme avec cuspides asymétriques) -->
                                <path data-tooth="35" class="tooth" d="M 205 342 Q 215 340 225 342 Q 230 344 230 347 L 230 357 Q 230 360 225 362 Q 215 364 205 362 Q 200 360 200 357 L 200 347 Q 200 344 205 342 Z M 207 362 L 223 362 L 223 372 Q 223 377 215 377 Q 207 377 207 372 Z" 
                                      :fill="dentalData[35]?.color || '#f3f4f6'" stroke="#374151" stroke-width="1.5"/>
                                <text x="215" y="355" text-anchor="middle" class="text-xs font-bold" fill="#374151">35</text>
                                
                                <!-- 36 - Première molaire droite inférieure (forme large avec 4 cuspides bien définies) -->
                                <path data-tooth="36" class="tooth" d="M 235 342 Q 250 340 265 342 Q 270 344 270 347 L 270 357 Q 270 360 265 362 Q 250 364 235 362 Q 230 360 230 357 L 230 347 Q 230 344 235 342 Z M 237 362 L 263 362 L 263 372 Q 263 377 250 377 Q 237 377 237 372 Z" 
                                      :fill="dentalData[36]?.color || '#f3f4f6'" stroke="#374151" stroke-width="1.5"/>
                                <text x="250" y="355" text-anchor="middle" class="text-xs font-bold" fill="#374151">36</text>
                                
                                <!-- 37 - Deuxième molaire droite inférieure (forme avec cuspides moins prononcées) -->
                                <path data-tooth="37" class="tooth" d="M 275 342 Q 290 340 305 342 Q 310 344 310 347 L 310 357 Q 310 360 305 362 Q 290 364 275 362 Q 270 360 270 357 L 270 347 Q 270 344 275 342 Z M 277 362 L 303 362 L 303 372 Q 303 377 290 377 Q 277 377 277 372 Z" 
                                      :fill="dentalData[37]?.color || '#f3f4f6'" stroke="#374151" stroke-width="1.5"/>
                                <text x="290" y="355" text-anchor="middle" class="text-xs font-bold" fill="#374151">37</text>
                                
                                <!-- 38 - Troisième molaire droite inférieure (forme plus petite et arrondie) -->
                                <path data-tooth="38" class="tooth" d="M 315 342 Q 330 340 345 342 Q 350 344 350 347 L 350 357 Q 350 360 345 362 Q 330 364 315 362 Q 310 360 310 357 L 310 347 Q 310 344 315 342 Z M 317 362 L 343 362 L 343 372 Q 343 377 330 377 Q 317 377 317 372 Z" 
                                      :fill="dentalData[38]?.color || '#f3f4f6'" stroke="#374151" stroke-width="1.5"/>
                                <text x="330" y="355" text-anchor="middle" class="text-xs font-bold" fill="#374151">38</text>
                            </g>
                    
                    <!-- Dents inférieures gauches (41-48) - Formes anatomiques ultra-réalistes -->
                    <g class="lower-left">
                        <!-- 41 - Incisive centrale gauche inférieure (forme ovale naturelle) -->
                        <path data-tooth="41" class="tooth" d="M 485 342 Q 495 340 505 342 Q 510 344 510 347 L 510 357 Q 510 360 505 362 Q 495 364 485 362 Q 480 360 480 357 L 480 347 Q 480 344 485 342 Z M 487 362 L 503 362 L 503 372 Q 503 377 495 377 Q 487 377 487 372 Z" 
                              :fill="dentalData[41]?.color || '#f3f4f6'" stroke="#374151" stroke-width="1.5"/>
                        <text x="495" y="355" text-anchor="middle" class="text-xs font-bold" fill="#374151">41</text>
                        
                        <!-- 42 - Incisive latérale gauche inférieure (forme triangulaire naturelle) -->
                        <path data-tooth="42" class="tooth" d="M 460 342 Q 467 340 475 342 Q 480 344 480 347 L 480 357 Q 480 360 475 362 Q 467 364 460 362 Q 455 360 455 357 L 455 347 Q 455 344 460 342 Z M 462 362 L 473 362 L 473 372 Q 473 377 467 377 Q 462 377 462 372 Z" 
                              :fill="dentalData[42]?.color || '#f3f4f6'" stroke="#374151" stroke-width="1.5"/>
                        <text x="467" y="355" text-anchor="middle" class="text-xs font-bold" fill="#374151">42</text>
                        
                        <!-- 43 - Canine gauche inférieure (forme pointue avec cuspide prononcée) -->
                        <path data-tooth="43" class="tooth" d="M 435 337 Q 442 335 450 337 Q 455 339 455 347 L 455 362 Q 455 365 450 367 Q 442 369 435 367 Q 430 365 430 362 L 430 347 Q 430 339 435 337 Z M 440 367 L 445 367 L 445 382 Q 445 387 442 387 Q 440 387 440 382 Z" 
                              :fill="dentalData[43]?.color || '#f3f4f6'" stroke="#374151" stroke-width="1.5"/>
                        <text x="442" y="355" text-anchor="middle" class="text-xs font-bold" fill="#374151">43</text>
                        
                        <!-- 44 - Première prémolaire gauche inférieure (forme avec deux cuspides distinctes) -->
                        <path data-tooth="44" class="tooth" d="M 405 342 Q 415 340 425 342 Q 430 344 430 347 L 430 357 Q 430 360 425 362 Q 415 364 405 362 Q 400 360 400 357 L 400 347 Q 400 344 405 342 Z M 407 362 L 423 362 L 423 372 Q 423 377 415 377 Q 407 377 407 372 Z" 
                              :fill="dentalData[44]?.color || '#f3f4f6'" stroke="#374151" stroke-width="1.5"/>
                        <text x="415" y="355" text-anchor="middle" class="text-xs font-bold" fill="#374151">44</text>
                        
                        <!-- 45 - Deuxième prémolaire gauche inférieure (forme avec cuspides asymétriques) -->
                        <path data-tooth="45" class="tooth" d="M 375 342 Q 385 340 395 342 Q 400 344 400 347 L 400 357 Q 400 360 395 362 Q 385 364 375 362 Q 370 360 370 357 L 370 347 Q 370 344 375 342 Z M 377 362 L 393 362 L 393 372 Q 393 377 385 377 Q 377 377 377 372 Z" 
                              :fill="dentalData[45]?.color || '#f3f4f6'" stroke="#374151" stroke-width="1.5"/>
                        <text x="385" y="355" text-anchor="middle" class="text-xs font-bold" fill="#374151">45</text>
                        
                        <!-- 46 - Première molaire gauche inférieure (forme large avec 4 cuspides bien définies) -->
                        <path data-tooth="46" class="tooth" d="M 335 342 Q 350 340 365 342 Q 370 344 370 347 L 370 357 Q 370 360 365 362 Q 350 364 335 362 Q 330 360 330 357 L 330 347 Q 330 344 335 342 Z M 337 362 L 363 362 L 363 372 Q 363 377 350 377 Q 337 377 337 372 Z" 
                              :fill="dentalData[46]?.color || '#f3f4f6'" stroke="#374151" stroke-width="1.5"/>
                        <text x="350" y="355" text-anchor="middle" class="text-xs font-bold" fill="#374151">46</text>
                        
                        <!-- 47 - Deuxième molaire gauche inférieure (forme avec cuspides moins prononcées) -->
                        <path data-tooth="47" class="tooth" d="M 295 342 Q 310 340 325 342 Q 330 344 330 347 L 330 357 Q 330 360 325 362 Q 310 364 295 362 Q 290 360 290 357 L 290 347 Q 290 344 295 342 Z M 297 362 L 323 362 L 323 372 Q 323 377 310 377 Q 297 377 297 372 Z" 
                              :fill="dentalData[47]?.color || '#f3f4f6'" stroke="#374151" stroke-width="1.5"/>
                        <text x="310" y="355" text-anchor="middle" class="text-xs font-bold" fill="#374151">47</text>
                        
                        <!-- 48 - Troisième molaire gauche inférieure (forme plus petite et arrondie) -->
                        <path data-tooth="48" class="tooth" d="M 255 342 Q 270 340 285 342 Q 290 344 290 347 L 290 357 Q 290 360 285 362 Q 270 364 255 362 Q 250 360 250 357 L 250 347 Q 250 344 255 342 Z M 257 362 L 283 362 L 283 372 Q 283 377 270 377 Q 257 377 257 372 Z" 
                              :fill="dentalData[48]?.color || '#f3f4f6'" stroke="#374151" stroke-width="1.5"/>
                        <text x="270" y="355" text-anchor="middle" class="text-xs font-bold" fill="#374151">48</text>
                    </g>
                    
                    <!-- Légende des quadrants -->
                    <text x="50" y="200" class="text-xs" fill="#6B7280">Quadrant 1 (Supérieur Droit)</text>
                    <text x="400" y="200" class="text-xs" fill="#6B7280">Quadrant 2 (Supérieur Gauche)</text>
                    <text x="50" y="380" class="text-xs" fill="#6B7280">Quadrant 4 (Inférieur Droit)</text>
                    <text x="400" y="380" class="text-xs" fill="#6B7280">Quadrant 3 (Inférieur Gauche)</text>
                </svg>
            </div>

            <!-- Panneau d'annotation fixe (sans popup) -->
            <div v-if="showAnnotationPanel" class="mt-6 bg-white rounded-lg shadow-lg p-6 border border-gray-200">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">
                        Modification - Dent {{ annotationData.toothNumber }}
                    </h3>
                    <button @click="closeAnnotationPanel" class="text-gray-400 hover:text-gray-600">
                        ✕
                    </button>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- État de la dent -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            État de la dent
                        </label>
                        <select v-model="annotationData.status" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                            <option value="healthy">Sain</option>
                            <option value="cavity">Carie</option>
                            <option value="crown">Couronne</option>
                            <option value="extracted">Extrait</option>
                            <option value="treatment">En traitement</option>
                        </select>
                    </div>
                    <!-- Notes -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Notes
                        </label>
                        <textarea 
                            v-model="annotationData.notes"
                            rows="3"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md"
                            placeholder="Notes sur cette dent..."
                        ></textarea>
                    </div>
                </div>
                <div class="flex justify-between space-x-3 mt-6">
                    <button 
                        @click="closeAnnotationPanel"
                        class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50"
                    >
                        Fermer
                    </button>
                    <button 
                        @click="saveAnnotation"
                        type="button"
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700"
                    >
                        Sauvegarder
                    </button>
                </div>
            </div>
        </div>
    `,
};

// Initialisation de l'application Vue.js
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM Content Loaded - Initializing Vue.js app');
    
    // Vérifier que Vue.js est chargé
    if (typeof Vue === 'undefined') {
        console.error('Vue.js is not loaded!');
        return;
    }
    
    // Vérifier que l'élément existe
    const appElement = document.getElementById('dental-chart-app');
    if (!appElement) {
        console.error('Element #dental-chart-app not found!');
        return;
    }
    
    try {
        const app = createApp({
            components: {
                DentalChart
            },
            setup() {
                const selectedPatientId = ref(null);
                const selectedRecordId = ref(null);

                return {
                    selectedPatientId,
                    selectedRecordId
                };
            }
        });

        app.mount('#dental-chart-app');
        console.log('Vue.js app mounted successfully');
    } catch (error) {
        console.error('Error mounting Vue.js app:', error);
    }
}); 