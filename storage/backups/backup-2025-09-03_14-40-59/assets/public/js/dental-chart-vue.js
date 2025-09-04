// Composant Vue.js pour le diagramme dentaire interactif
const DentalChartInteractive = {
    name: 'DentalChartInteractive',
    props: {
        initialSelectedTooth: {
            type: String,
            default: null
        },
        showInfo: {
            type: Boolean,
            default: true
        },
        showControls: {
            type: Boolean,
            default: true
        }
    },
    data() {
        return {
            selectedTooth: this.initialSelectedTooth,
            toothHistory: [],
            toothData: {},
            isLoading: false,
            error: null
        }
    },
    computed: {
        selectedToothInfo() {
            if (!this.selectedTooth) return null;
            
            return {
                id: this.selectedTooth,
                type: this.getToothType(this.selectedTooth),
                quadrant: this.getQuadrant(this.selectedTooth),
                position: this.getPosition(this.selectedTooth),
                data: this.toothData[this.selectedTooth] || {}
            };
        },
        
        stats() {
            return {
                selectedCount: this.selectedTooth ? 1 : 0,
                totalInteractions: this.toothHistory.length,
                totalTeeth: 32,
                quadrants: this.getQuadrantStats()
            };
        }
    },
    methods: {
        // Gestion de la sélection de dents
        selectTooth(toothId) {
            // Désélectionner la dent précédente
            if (this.selectedTooth) {
                const previousSelected = document.querySelector('.tooth.selected');
                if (previousSelected) {
                    previousSelected.classList.remove('selected');
                }
            }
            
            // Sélectionner la nouvelle dent
            const tooth = document.querySelector(`[data-tooth-id="${toothId}"]`);
            if (tooth) {
                tooth.classList.add('selected');
                this.selectedTooth = toothId;
                this.toothHistory.push(toothId);
                
                // Émettre un événement
                this.$emit('tooth-selected', { toothId, timestamp: new Date() });
                
                // Charger les données de la dent si nécessaire
                this.loadToothData(toothId);
            }
        },
        
        clearSelection() {
            this.selectedTooth = null;
            const selectedToothElement = document.querySelector('.tooth.selected');
            if (selectedToothElement) {
                selectedToothElement.classList.remove('selected');
            }
            this.$emit('selection-cleared');
        },
        
        selectRandomTooth() {
            const teeth = ['11', '12', '13', '14', '15', '16', '17', '18',
                         '21', '22', '23', '24', '25', '26', '27', '28',
                         '31', '32', '33', '34', '35', '36', '37', '38',
                         '41', '42', '43', '44', '45', '46', '47', '48'];
            const randomTooth = teeth[Math.floor(Math.random() * teeth.length)];
            this.selectTooth(randomTooth);
        },
        
        // Informations sur les dents
        getToothType(toothId) {
            const toothTypes = {
                '11': 'Incisive centrale droite',
                '12': 'Incisive latérale droite',
                '13': 'Canine droite',
                '14': 'Première prémolaire droite',
                '15': 'Deuxième prémolaire droite',
                '16': 'Première molaire droite',
                '17': 'Deuxième molaire droite',
                '18': 'Troisième molaire droite',
                '21': 'Incisive centrale gauche',
                '22': 'Incisive latérale gauche',
                '23': 'Canine gauche',
                '24': 'Première prémolaire gauche',
                '25': 'Deuxième prémolaire gauche',
                '26': 'Première molaire gauche',
                '27': 'Deuxième molaire gauche',
                '28': 'Troisième molaire gauche',
                '31': 'Incisive centrale droite inférieure',
                '32': 'Incisive latérale droite inférieure',
                '33': 'Canine droite inférieure',
                '34': 'Première prémolaire droite inférieure',
                '35': 'Deuxième prémolaire droite inférieure',
                '36': 'Première molaire droite inférieure',
                '37': 'Deuxième molaire droite inférieure',
                '38': 'Troisième molaire droite inférieure',
                '41': 'Incisive centrale gauche inférieure',
                '42': 'Incisive latérale gauche inférieure',
                '43': 'Canine gauche inférieure',
                '44': 'Première prémolaire gauche inférieure',
                '45': 'Deuxième prémolaire gauche inférieure',
                '46': 'Première molaire gauche inférieure',
                '47': 'Deuxième molaire gauche inférieure',
                '48': 'Troisième molaire gauche inférieure'
            };
            return toothTypes[toothId] || 'Inconnue';
        },
        
        getQuadrant(toothId) {
            const firstDigit = parseInt(toothId.charAt(0));
            const quadrants = {
                1: 'Quadrant 1 (Supérieur Droit)',
                2: 'Quadrant 2 (Supérieur Gauche)',
                3: 'Quadrant 3 (Inférieur Gauche)',
                4: 'Quadrant 4 (Inférieur Droit)'
            };
            return quadrants[firstDigit] || 'Inconnu';
        },
        
        getPosition(toothId) {
            const secondDigit = parseInt(toothId.charAt(1));
            const positions = {
                1: 'Centrale',
                2: 'Latérale',
                3: 'Canine',
                4: 'Première prémolaire',
                5: 'Deuxième prémolaire',
                6: 'Première molaire',
                7: 'Deuxième molaire',
                8: 'Troisième molaire'
            };
            return positions[secondDigit] || 'Inconnue';
        },
        
        getQuadrantStats() {
            const stats = { 1: 0, 2: 0, 3: 0, 4: 0 };
            this.toothHistory.forEach(toothId => {
                const quadrant = parseInt(toothId.charAt(0));
                if (stats[quadrant] !== undefined) {
                    stats[quadrant]++;
                }
            });
            return stats;
        },
        
        // Chargement des données
        async loadToothData(toothId) {
            if (this.toothData[toothId]) return; // Déjà chargé
            
            this.isLoading = true;
            this.error = null;
            
            try {
                // Simuler un appel API
                const response = await fetch(`/api/dental-records/tooth/${toothId}`);
                if (response.ok) {
                    const data = await response.json();
                    this.$set(this.toothData, toothId, data);
                } else {
                    // Données par défaut si pas d'API
                    this.$set(this.toothData, toothId, {
                        status: 'healthy',
                        notes: '',
                        lastExam: new Date().toISOString(),
                        treatments: []
                    });
                }
            } catch (error) {
                console.error('Erreur lors du chargement des données:', error);
                this.error = 'Erreur lors du chargement des données';
                
                // Données par défaut en cas d'erreur
                this.$set(this.toothData, toothId, {
                    status: 'unknown',
                    notes: '',
                    lastExam: null,
                    treatments: []
                });
            } finally {
                this.isLoading = false;
            }
        },
        
        // Sauvegarde des données
        async saveToothData(toothId, data) {
            try {
                const response = await fetch(`/api/dental-records/tooth/${toothId}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                    },
                    body: JSON.stringify(data)
                });
                
                if (response.ok) {
                    this.$set(this.toothData, toothId, data);
                    this.$emit('tooth-data-saved', { toothId, data });
                } else {
                    throw new Error('Erreur lors de la sauvegarde');
                }
            } catch (error) {
                console.error('Erreur lors de la sauvegarde:', error);
                this.$emit('save-error', { toothId, error: error.message });
            }
        },
        
        // Initialisation
        initializeChart() {
            // Écouter les événements de sélection de dents
            document.addEventListener('toothSelected', (event) => {
                this.selectTooth(event.detail.toothId);
            });
            
            // Ajouter des tooltips
            const teeth = document.querySelectorAll('.tooth');
            teeth.forEach(tooth => {
                tooth.addEventListener('mouseenter', function() {
                    const toothId = this.getAttribute('data-tooth-id');
                    this.setAttribute('title', `Dent ${toothId} - ${this.getToothType(toothId)}`);
                });
            });
        }
    },
    
    mounted() {
        this.$nextTick(() => {
            this.initializeChart();
        });
    },
    
    template: `
        <div class="dental-chart-interactive">
            <div class="chart-container">
                <div class="svg-wrapper">
                    <object 
                        data="/images/dental-chart-interactive.svg" 
                        type="image/svg+xml"
                        width="100%"
                        height="600"
                        @load="initializeChart">
                    </object>
                </div>
                
                <div v-if="showControls" class="controls">
                    <button @click="clearSelection" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Effacer
                    </button>
                    <button @click="selectRandomTooth" class="btn btn-primary">
                        <i class="fas fa-random"></i> Aléatoire
                    </button>
                    <button @click="$emit('export-data')" class="btn btn-success">
                        <i class="fas fa-download"></i> Exporter
                    </button>
                </div>
            </div>
            
            <div v-if="showInfo" class="info-panel">
                <div v-if="selectedToothInfo" class="tooth-info">
                    <h3>Dent {{ selectedToothInfo.id }}</h3>
                    <div class="tooth-details">
                        <p><strong>Type :</strong> {{ selectedToothInfo.type }}</p>
                        <p><strong>Quadrant :</strong> {{ selectedToothInfo.quadrant }}</p>
                        <p><strong>Position :</strong> {{ selectedToothInfo.position }}</p>
                        
                        <div v-if="isLoading" class="loading">
                            <i class="fas fa-spinner fa-spin"></i> Chargement...
                        </div>
                        
                        <div v-else-if="selectedToothInfo.data.status" class="tooth-data">
                            <p><strong>État :</strong> {{ selectedToothInfo.data.status }}</p>
                            <p v-if="selectedToothInfo.data.notes">
                                <strong>Notes :</strong> {{ selectedToothInfo.data.notes }}
                            </p>
                            <p v-if="selectedToothInfo.data.lastExam">
                                <strong>Dernier examen :</strong> {{ new Date(selectedToothInfo.data.lastExam).toLocaleDateString() }}
                            </p>
                        </div>
                    </div>
                </div>
                
                <div v-else class="no-selection">
                    <h3>👆 Sélectionnez une dent</h3>
                    <p>Cliquez sur une dent dans le diagramme pour voir ses informations.</p>
                </div>
                
                <div class="stats">
                    <div class="stat-item">
                        <span class="stat-number">{{ stats.selectedCount }}</span>
                        <span class="stat-label">Sélectionnée</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number">{{ stats.totalInteractions }}</span>
                        <span class="stat-label">Interactions</span>
                    </div>
                </div>
                
                <div v-if="toothHistory.length > 0" class="history">
                    <h4>Historique récent :</h4>
                    <div 
                        v-for="(tooth, index) in toothHistory.slice(-5)" 
                        :key="index"
                        class="history-item"
                    >
                        Dent {{ tooth }} - {{ getToothType(tooth) }}
                    </div>
                </div>
            </div>
        </div>
    `
};

// Export pour utilisation dans d'autres modules
if (typeof module !== 'undefined' && module.exports) {
    module.exports = DentalChartInteractive;
} else if (typeof window !== 'undefined') {
    window.DentalChartInteractive = DentalChartInteractive;
} 