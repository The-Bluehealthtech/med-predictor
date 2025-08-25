// Portail Joueur - FIFA Ultimate Team
const app = new Vue({
    el: '#app',
    data: {
        players: [],
        selectedPlayerId: null,
        selectedPlayer: null,
        loading: false,
        activeTab: 'performance',
        performanceEvolution: {
            labels: ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin'],
            ratings: [75, 78, 76, 82, 85, 88],
            goals: [1, 2, 1, 1, 2, 1],
            assists: [1, 2, 1, 1, 2, 1]
        }
    },
    async mounted() {
        await this.loadPlayers();
    },
    methods: {
        async loadPlayers() {
            try {
                const response = await fetch('/api/players');
                const data = await response.json();
                console.log('Players API response status:', response.status);
                console.log('Players API data:', data);
                if (data.success) {
                    this.players = data.data;
                    console.log('Players loaded:', this.players);
                } else {
                    console.error('Players API returned success: false');
                }
            } catch (error) {
                console.error('Erreur lors du chargement des joueurs:', error);
            }
        },
        async loadPlayerData() {
            if (!this.selectedPlayerId) {
                this.selectedPlayer = null;
                return;
            }

            this.loading = true;
            console.log('Loading player data for ID:', this.selectedPlayerId);
            try {
                const response = await fetch(`/api/players/${this.selectedPlayerId}`);
                console.log('API Response status:', response.status);
                const data = await response.json();
                console.log('API Response data:', data);
                if (data.success) {
                    this.selectedPlayer = data.data;
                    console.log('Player data set:', this.selectedPlayer);
                    // Attendre que le DOM soit mis à jour avant de créer les graphiques
                    setTimeout(() => {
                        this.initializeCharts();
                    }, 100);
                } else {
                    console.error('API returned success: false');
                }
            } catch (error) {
                console.error('Erreur lors du chargement du joueur:', error);
            } finally {
                this.loading = false;
                console.log('Loading finished');
            }
        },
        initializeCharts() {
            console.log('Initializing charts...');
            // Vérifier que les éléments DOM existent
            const performanceChart = document.getElementById('performanceChart');
            const comparisonChart = document.getElementById('comparisonChart');
            
            if (performanceChart && comparisonChart) {
                console.log('Chart elements found, creating charts...');
                this.createPerformanceChart();
                this.createComparisonChart();
            } else {
                console.log('Chart elements not found, retrying in 200ms...');
                setTimeout(() => {
                    this.initializeCharts();
                }, 200);
            }
        },
        createPerformanceChart() {
            console.log('Creating performance chart...');
            const ctx = document.getElementById('performanceChart');
            if (!ctx) {
                console.error('Performance chart canvas not found!');
                return;
            }
            console.log('Performance chart canvas found, creating chart...');

            // Utiliser les données dynamiques de l'évolution des performances
            const labels = this.performanceEvolution.labels || ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin'];
            const ratings = this.performanceEvolution.ratings || [75, 78, 76, 82, 85, 88];
            const goals = this.performanceEvolution.goals || [0, 0, 0, 0, 0, 0];
            const assists = this.performanceEvolution.assists || [0, 0, 0, 0, 0, 0];

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Rating Performance',
                        data: ratings,
                        borderColor: '#3b82f6',
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        tension: 0.4,
                        fill: true,
                        yAxisID: 'y'
                    }, {
                        label: 'Buts marqués',
                        data: goals,
                        borderColor: '#10b981',
                        backgroundColor: 'rgba(16, 185, 129, 0.1)',
                        tension: 0.4,
                        fill: false,
                        yAxisID: 'y1'
                    }, {
                        label: 'Assists délivrés',
                        data: assists,
                        borderColor: '#f59e0b',
                        backgroundColor: 'rgba(245, 158, 11, 0.1)',
                        tension: 0.4,
                        fill: false,
                        yAxisID: 'y1'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top',
                        },
                        title: {
                            display: true,
                            text: 'Évolution des Performances - Saison 2024/25',
                            font: {
                                size: 16,
                                weight: 'bold'
                            }
                        }
                    },
                    scales: {
                        y: {
                            type: 'linear',
                            display: true,
                            position: 'left',
                            beginAtZero: true,
                            max: 10,
                            title: {
                                display: true,
                                text: 'Rating (0-10)'
                            }
                        },
                        y1: {
                            type: 'linear',
                            display: true,
                            position: 'right',
                            beginAtZero: true,
                            max: 5,
                            title: {
                                display: true,
                                text: 'Buts/Assists'
                            },
                            grid: {
                                drawOnChartArea: false,
                            },
                        }
                    }
                }
            });
        },
        createComparisonChart() {
            console.log('Creating comparison chart...');
            const ctx = document.getElementById('comparisonChart');
            if (!ctx) {
                console.error('Comparison chart canvas not found!');
                return;
            }
            console.log('Comparison chart canvas found, creating chart...');

            new Chart(ctx, {
                type: 'radar',
                data: {
                    labels: ['Vitesse', 'Endurance', 'Technique', 'Mental', 'Physique', 'Tactique'],
                    datasets: [{
                        label: 'Mes Stats',
                        data: [88, 85, 92, 78, 86, 82],
                        borderColor: '#10b981',
                        backgroundColor: 'rgba(16, 185, 129, 0.2)',
                        borderWidth: 2,
                        pointBackgroundColor: '#10b981',
                        pointBorderColor: '#fff',
                        pointHoverBackgroundColor: '#fff',
                        pointHoverBorderColor: '#10b981'
                    }, {
                        label: 'Moyenne Ligue',
                        data: [75, 72, 78, 70, 74, 76],
                        borderColor: '#6b7280',
                        backgroundColor: 'rgba(107, 114, 128, 0.2)',
                        borderWidth: 2,
                        pointBackgroundColor: '#6b7280',
                        pointBorderColor: '#fff',
                        pointHoverBackgroundColor: '#fff',
                        pointHoverBorderColor: '#6b7280'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top',
                        },
                        title: {
                            display: true,
                            text: 'Comparaison des Statistiques',
                            font: {
                                size: 16,
                                weight: 'bold'
                            }
                        }
                    },
                    scales: {
                        r: {
                            beginAtZero: true,
                            max: 100,
                            ticks: {
                                stepSize: 20
                            }
                        }
                    }
                }
            });
        }
    }
});








