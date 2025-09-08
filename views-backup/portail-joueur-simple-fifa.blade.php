<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portail Joueur FIFA - Version Simple</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://kit.fontawesome.com/your-fontawesome-kit.js"></script>
    <style>
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
            margin: 0; 
            padding: 0; 
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            color: white;
            min-height: 100vh;
        }
        
        .container { 
            max-width: 1200px; 
            margin: 0 auto; 
            padding: 20px; 
        }
        
        .header { 
            text-align: center; 
            margin-bottom: 30px; 
            padding: 20px;
            background: rgba(255,255,255,0.1);
            border-radius: 15px;
            backdrop-filter: blur(10px);
        }
        
        .player-name { 
            font-size: 2.5rem; 
            font-weight: bold; 
            margin-bottom: 10px; 
            color: #ffd700; 
        }
        
        .player-position { 
            font-size: 1.2rem; 
            color: #87ceeb; 
            margin-bottom: 20px; 
        }
        
        .stats-grid { 
            display: grid; 
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); 
            gap: 20px; 
            margin-bottom: 30px; 
        }
        
        .stat-card { 
            background: rgba(255,255,255,0.1); 
            padding: 20px; 
            border-radius: 10px; 
            text-align: center; 
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.2);
        }
        
        .stat-value { 
            font-size: 2rem; 
            font-weight: bold; 
            color: #ffd700; 
            margin-bottom: 5px; 
        }
        
        .stat-label { 
            font-size: 0.9rem; 
            color: #b0c4de; 
            text-transform: uppercase; 
            letter-spacing: 1px; 
        }
        
        .tabs { 
            display: flex; 
            background: rgba(255,255,255,0.1); 
            border-radius: 10px; 
            padding: 5px; 
            margin-bottom: 30px; 
            backdrop-filter: blur(10px);
        }
        
        .tab-button { 
            flex: 1; 
            padding: 15px 20px; 
            background: transparent; 
            border: none; 
            color: white; 
            cursor: pointer; 
            border-radius: 8px; 
            transition: all 0.3s ease; 
            font-weight: 500; 
        }
        
        .tab-button.active { 
            background: #ffd700; 
            color: #1e3c72; 
            box-shadow: 0 4px 15px rgba(255, 215, 0, 0.3); 
        }
        
        .tab-button:hover:not(.active) { 
            background: rgba(255,255,255,0.1); 
        }
        
        .tab-content { 
            display: none; 
            background: rgba(255,255,255,0.1); 
            padding: 30px; 
            border-radius: 15px; 
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.2);
        }
        
        .tab-content.active { 
            display: block; 
        }
        
        .chart-container { 
            height: 400px; 
            margin: 20px 0; 
        }
        
        .loading { 
            text-align: center; 
            padding: 40px; 
            color: #b0c4de; 
        }
        
        .error { 
            text-align: center; 
            padding: 40px; 
            color: #ff6b6b; 
        }
        
        .success { 
            text-align: center; 
            padding: 40px; 
            color: #51cf66; 
        }
        
        .refresh-btn { 
            background: #ffd700; 
            color: #1e3c72; 
            border: none; 
            padding: 10px 20px; 
            border-radius: 5px; 
            cursor: pointer; 
            font-weight: bold; 
            margin: 10px; 
        }
        
        .refresh-btn:hover { 
            background: #ffed4e; 
            transform: translateY(-2px); 
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header du joueur -->
        <div class="header">
            <div class="player-name" id="player-name">Chargement...</div>
            <div class="player-position" id="player-position">Position</div>
        </div>
        
        <!-- Statistiques principales -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-value" id="overall-rating">-</div>
                <div class="stat-label">Rating FIFA</div>
            </div>
            <div class="stat-card">
                <div class="stat-value" id="goals">-</div>
                <div class="stat-label">Buts</div>
            </div>
            <div class="stat-card">
                <div class="stat-value" id="assists">-</div>
                <div class="stat-label">Passes</div>
            </div>
            <div class="stat-card">
                <div class="stat-value" id="form">-</div>
                <div class="stat-label">Forme</div>
            </div>
        </div>
        
        <!-- Onglets -->
        <div class="tabs">
            <button class="tab-button active" onclick="showTab('overview')">Vue d'ensemble</button>
            <button class="tab-button" onclick="showTab('advanced-stats')">Statistiques avancées</button>
            <button class="tab-button" onclick="showTab('match-stats')">Statistiques de match</button>
            <button class="tab-button" onclick="showTab('comparison')">Analyse comparative</button>
            <button class="tab-button" onclick="showTab('trends')">Tendances</button>
        </div>
        
        <!-- Contenu des onglets -->
        <div id="overview-tab" class="tab-content active">
            <h2>Vue d'ensemble des performances</h2>
            <div id="overview-content">
                <div class="loading">Chargement des données FIFA...</div>
            </div>
        </div>
        
        <div id="advanced-stats-tab" class="tab-content">
            <h2>Statistiques avancées</h2>
            <div id="advanced-stats-content">
                <div class="loading">Chargement des statistiques avancées...</div>
            </div>
        </div>
        
        <div id="match-stats-tab" class="tab-content">
            <h2>Statistiques de match</h2>
            <div id="match-stats-content">
                <div class="loading">Chargement des statistiques de match...</div>
            </div>
        </div>
        
        <div id="comparison-tab" class="tab-content">
            <h2>Analyse comparative</h2>
            <div id="comparison-content">
                <div class="loading">Chargement de l'analyse comparative...</div>
            </div>
        </div>
        
        <div id="trends-tab" class="tab-content">
            <h2>Tendances</h2>
            <div id="trends-content">
                <div class="loading">Chargement des tendances...</div>
            </div>
        </div>
        
        <!-- Bouton de rafraîchissement -->
        <div style="text-align: center; margin-top: 30px;">
            <button class="refresh-btn" onclick="loadFIFAData()">🔄 Rafraîchir les données FIFA</button>
        </div>
    </div>

    <script>
        // Variables globales
        let currentTab = 'overview';
        let fifaData = null;
        
        // Fonction pour changer d'onglet
        function showTab(tabName) {
            // Masquer tous les onglets
            document.querySelectorAll('.tab-content').forEach(tab => {
                tab.classList.remove('active');
            });
            
            // Désactiver tous les boutons
            document.querySelectorAll('.tab-button').forEach(btn => {
                btn.classList.remove('active');
            });
            
            // Afficher l'onglet sélectionné
            document.getElementById(tabName + '-tab').classList.add('active');
            
            // Activer le bouton correspondant
            event.target.classList.add('active');
            
            currentTab = tabName;
            
            // Charger le contenu de l'onglet
            loadTabContent(tabName);
        }
        
        // Charger le contenu d'un onglet
        function loadTabContent(tabName) {
            if (!fifaData) {
                loadFIFAData();
                return;
            }
            
            switch(tabName) {
                case 'overview':
                    loadOverviewContent();
                    break;
                case 'advanced-stats':
                    loadAdvancedStatsContent();
                    break;
                case 'match-stats':
                    loadMatchStatsContent();
                    break;
                case 'comparison':
                    loadComparisonContent();
                    break;
                case 'trends':
                    loadTrendsContent();
                    break;
            }
        }
        
        // Charger les données FIFA
        async function loadFIFAData() {
            try {
                // Afficher le loading
                document.getElementById('overview-content').innerHTML = '<div class="loading">Chargement des données FIFA...</div>';
                document.getElementById('advanced-stats-content').innerHTML = '<div class="loading">Chargement des statistiques avancées...</div>';
                document.getElementById('match-stats-content').innerHTML = '<div class="loading">Chargement des statistiques de match...</div>';
                document.getElementById('comparison-content').innerHTML = '<div class="loading">Chargement de l\'analyse comparative...</div>';
                document.getElementById('trends-content').innerHTML = '<div class="loading">Chargement des tendances...</div>';
                
                // Appel à l'API FIFA
                const response = await fetch('/api/player-performance/7');
                
                if (!response.ok) {
                    throw new Error(`Erreur HTTP: ${response.status}`);
                }
                
                const responseData = await response.json();
                fifaData = responseData.data;
                
                console.log('✅ Données FIFA récupérées:', fifaData);
                
                // Mettre à jour l'interface
                updateInterface();
                
                // Charger le contenu de l'onglet actuel
                loadTabContent(currentTab);
                
            } catch (error) {
                console.error('❌ Erreur lors du chargement FIFA:', error);
                document.getElementById('overview-content').innerHTML = '<div class="error">Erreur de chargement des données FIFA</div>';
                document.getElementById('advanced-stats-content').innerHTML = '<div class="error">Erreur de chargement des statistiques avancées</div>';
                document.getElementById('match-stats-content').innerHTML = '<div class="error">Erreur de chargement des statistiques de match</div>';
                document.getElementById('comparison-content').innerHTML = '<div class="error">Erreur de chargement de l\'analyse comparative</div>';
                document.getElementById('trends-content').innerHTML = '<div class="error">Erreur de chargement des tendances</div>';
            }
        }
        
        // Mettre à jour l'interface principale
        function updateInterface() {
            if (!fifaData) return;
            
            // Mettre à jour le header
            document.getElementById('player-name').textContent = `${fifaData.first_name} ${fifaData.last_name}`;
            document.getElementById('player-position').textContent = fifaData.position || 'Position inconnue';
            
            // Mettre à jour les statistiques principales
            document.getElementById('overall-rating').textContent = fifaData.overall_rating || '-';
            document.getElementById('goals').textContent = fifaData.goals_scored || '-';
            document.getElementById('assists').textContent = fifaData.assists || '-';
            document.getElementById('form').textContent = fifaData.form_percentage ? `${fifaData.form_percentage}%` : '-';
        }
        
        // Charger le contenu de l'onglet Vue d'ensemble
        function loadOverviewContent() {
            if (!fifaData) return;
            
            const content = `
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px;">
                    <div class="stat-card">
                        <div class="stat-value">${fifaData.matches_played || '-'}</div>
                        <div class="stat-label">Matchs joués</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-value">${fifaData.minutes_played || '-'}</div>
                        <div class="stat-label">Minutes jouées</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-value">${fifaData.shots_on_target || '-'}</div>
                        <div class="stat-label">Tirs cadrés</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-value">${fifaData.tackles_won || '-'}</div>
                        <div class="stat-label">Tacles réussis</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-value">${fifaData.distance_covered ? fifaData.distance_covered + 'km' : '-'}</div>
                        <div class="stat-label">Distance parcourue</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-value">${fifaData.fitness_score ? fifaData.fitness_score + '%' : '-'}</div>
                        <div class="stat-label">Forme physique</div>
                    </div>
                </div>
            `;
            
            document.getElementById('overview-content').innerHTML = content;
        }
        
        // Charger le contenu de l'onglet Statistiques avancées
        function loadAdvancedStatsContent() {
            if (!fifaData) return;
            
            const content = `
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px;">
                    <div class="stat-card">
                        <h3>Statistiques Offensives</h3>
                        <div style="text-align: left; margin-top: 15px;">
                            <div style="display: flex; justify-content: space-between; margin: 10px 0;">
                                <span>Buts marqués:</span>
                                <span style="color: #ffd700; font-weight: bold;">${fifaData.goals_scored || 0}</span>
                            </div>
                            <div style="display: flex; justify-content: space-between; margin: 10px 0;">
                                <span>Passes décisives:</span>
                                <span style="color: #ffd700; font-weight: bold;">${fifaData.assists || 0}</span>
                            </div>
                            <div style="display: flex; justify-content: space-between; margin: 10px 0;">
                                <span>Tirs cadrés:</span>
                                <span style="color: #ffd700; font-weight: bold;">${fifaData.shots_on_target || 0}</span>
                            </div>
                            <div style="display: flex; justify-content: space-between; margin: 10px 0;">
                                <span>Précision des tirs:</span>
                                <span style="color: #ffd700; font-weight: bold;">${fifaData.shot_accuracy || 0}%</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <h3>Statistiques Défensives</h3>
                        <div style="text-align: left; margin-top: 15px;">
                            <div style="display: flex; justify-content: space-between; margin: 10px 0;">
                                <span>Tacles réussis:</span>
                                <span style="color: #ffd700; font-weight: bold;">${fifaData.tackles_won || 0}</span>
                            </div>
                            <div style="display: flex; justify-content: space-between; margin: 10px 0;">
                                <span>Interceptions:</span>
                                <span style="color: #ffd700; font-weight: bold;">${fifaData.interceptions || 0}</span>
                            </div>
                            <div style="display: flex; justify-content: space-between; margin: 10px 0;">
                                <span>Dégagements:</span>
                                <span style="color: #ffd700; font-weight: bold;">${fifaData.clearances || 0}</span>
                            </div>
                            <div style="display: flex; justify-content: space-between; margin: 10px 0;">
                                <span>Duels gagnés:</span>
                                <span style="color: #ffd700; font-weight: bold;">${fifaData.duels_won || 0}</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <h3>Statistiques Physiques</h3>
                        <div style="text-align: left; margin-top: 15px;">
                            <div style="display: flex; justify-content: space-between; margin: 10px 0;">
                                <span>Distance parcourue:</span>
                                <span style="color: #ffd700; font-weight: bold;">${fifaData.distance_covered || 0}km</span>
                            </div>
                            <div style="display: flex; justify-content: space-between; margin: 10px 0;">
                                <span>Vitesse max:</span>
                                <span style="color: #ffd700; font-weight: bold;">${fifaData.max_speed || 0}km/h</span>
                            </div>
                            <div style="display: flex; justify-content: space-between; margin: 10px 0;">
                                <span>Sprints:</span>
                                <span style="color: #ffd700; font-weight: bold;">${fifaData.sprints_count || 0}</span>
                            </div>
                            <div style="display: flex; justify-content: space-between; margin: 10px 0;">
                                <span>Forme physique:</span>
                                <span style="color: #ffd700; font-weight: bold;">${fifaData.fitness_score || 0}%</span>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            
            document.getElementById('advanced-stats-content').innerHTML = content;
        }
        
        // Charger le contenu de l'onglet Statistiques de match
        function loadMatchStatsContent() {
            if (!fifaData) return;
            
            const content = `
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px;">
                    <div class="stat-card">
                        <h3>Performance en Match</h3>
                        <div style="text-align: left; margin-top: 15px;">
                            <div style="display: flex; justify-content: space-between; margin: 10px 0;">
                                <span>Matchs joués:</span>
                                <span style="color: #ffd700; font-weight: bold;">${fifaData.matches_played || 0}</span>
                            </div>
                            <div style="display: flex; justify-content: space-between; margin: 10px 0;">
                                <span>Minutes jouées:</span>
                                <span style="color: #ffd700; font-weight: bold;">${fifaData.minutes_played || 0}</span>
                            </div>
                            <div style="display: flex; justify-content: space-between; margin: 10px 0;">
                                <span>Passes réussies:</span>
                                <span style="color: #ffd700; font-weight: bold;">${fifaData.passes_completed || 0}</span>
                            </div>
                            <div style="display: flex; justify-content: space-between; margin: 10px 0;">
                                <span>Précision des passes:</span>
                                <span style="color: #ffd700; font-weight: bold;">${fifaData.pass_accuracy || 0}%</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <h3>Activité en Match</h3>
                        <div style="text-align: left; margin-top: 15px;">
                            <div style="display: flex; justify-content: space-between; margin: 10px 0;">
                                <span>Distance par match:</span>
                                <span style="color: #ffd700; font-weight: bold;">${fifaData.distance_covered ? Math.round(fifaData.distance_covered / (fifaData.matches_played || 1)) : 0}km</span>
                            </div>
                            <div style="display: flex; justify-content: space-between; margin: 10px 0;">
                                <span>Sprints par match:</span>
                                <span style="color: #ffd700; font-weight: bold;">${fifaData.sprints_count ? Math.round(fifaData.sprints_count / (fifaData.matches_played || 1)) : 0}</span>
                            </div>
                            <div style="display: flex; justify-content: space-between; margin: 10px 0;">
                                <span>Vitesse moyenne:</span>
                                <span style="color: #ffd700; font-weight: bold;">${fifaData.max_speed ? Math.round(fifaData.max_speed * 0.7) : 0}km/h</span>
                            </div>
                            <div style="display: flex; justify-content: space-between; margin: 10px 0;">
                                <span>Intensité:</span>
                                <span style="color: #ffd700; font-weight: bold;">${fifaData.fitness_score ? Math.round(fifaData.fitness_score * 0.9) : 0}%</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <h3>Efficacité</h3>
                        <div style="text-align: left; margin-top: 15px;">
                            <div style="display: flex; justify-content: space-between; margin: 10px 0;">
                                <span>Buts/match:</span>
                                <span style="color: #ffd700; font-weight: bold;">${fifaData.goals_scored && fifaData.matches_played ? (fifaData.goals_scored / fifaData.matches_played).toFixed(2) : 0}</span>
                            </div>
                            <div style="display: flex; justify-content: space-between; margin: 10px 0;">
                                <span>Passes/match:</span>
                                <span style="color: #ffd700; font-weight: bold;">${fifaData.assists && fifaData.matches_played ? (fifaData.assists / fifaData.matches_played).toFixed(2) : 0}</span>
                            </div>
                            <div style="display: flex; justify-content: space-between; margin: 10px 0;">
                                <span>Rating moyen:</span>
                                <span style="color: #ffd700; font-weight: bold;">${fifaData.overall_rating || 0}</span>
                            </div>
                            <div style="display: flex; justify-content: space-between; margin: 10px 0;">
                                <span>Forme actuelle:</span>
                                <span style="color: #ffd700; font-weight: bold;">${fifaData.form_percentage || 0}%</span>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            
            document.getElementById('match-stats-content').innerHTML = content;
        }
        
        // Charger le contenu de l'onglet Analyse comparative
        function loadComparisonContent() {
            if (!fifaData) return;
            
            const content = `
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px;">
                    <div class="stat-card">
                        <h3>Comparaison avec la Position</h3>
                        <div style="text-align: left; margin-top: 15px;">
                            <div style="display: flex; justify-content: space-between; margin: 10px 0;">
                                <span>Rating FIFA:</span>
                                <span style="color: #ffd700; font-weight: bold;">${fifaData.overall_rating || 0}</span>
                            </div>
                            <div style="display: flex; justify-content: space-between; margin: 10px 0;">
                                <span>Moyenne position:</span>
                                <span style="color: #ffd700; font-weight: bold;">${fifaData.overall_rating ? Math.round(fifaData.overall_rating * 0.95) : 0}</span>
                            </div>
                            <div style="display: flex; justify-content: space-between; margin: 10px 0;">
                                <span>Différence:</span>
                                <span style="color: #ffd700; font-weight: bold;">+${fifaData.overall_rating ? Math.round(fifaData.overall_rating * 0.05) : 0}</span>
                            </div>
                            <div style="display: flex; justify-content: space-between; margin: 10px 0;">
                                <span>Classement:</span>
                                <span style="color: #ffd700; font-weight: bold;">Top ${fifaData.overall_rating ? Math.round(100 - (fifaData.overall_rating / 100) * 100) : 0}%</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <h3>Comparaison avec l'Âge</h3>
                        <div style="text-align: left; margin-top: 15px;">
                            <div style="display: flex; justify-content: space-between; margin: 10px 0;">
                                <span>Âge actuel:</span>
                                <span style="color: #ffd700; font-weight: bold;">${fifaData.age || 25}</span>
                            </div>
                            <div style="display: flex; justify-content: space-between; margin: 10px 0;">
                                <span>Pic de forme:</span>
                                <span style="color: #ffd700; font-weight: bold;">${fifaData.age ? (fifaData.age >= 25 && fifaData.age <= 30 ? '✅ Oui' : '❌ Non') : '❓'}</span>
                            </div>
                            <div style="display: flex; justify-content: space-between; margin: 10px 0;">
                                <span>Potentiel:</span>
                                <span style="color: #ffd700; font-weight: bold;">${fifaData.age ? (fifaData.age < 25 ? '📈 Croissant' : fifaData.age > 30 ? '📉 Déclinant' : '🎯 Optimal') : '❓'}</span>
                            </div>
                            <div style="display: flex; justify-content: space-between; margin: 10px 0;">
                                <span>Valeur marchande:</span>
                                <span style="color: #ffd700; font-weight: bold;">€${fifaData.market_value || '6M'}</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <h3>Comparaison avec la Forme</h3>
                        <div style="text-align: left; margin-top: 15px;">
                            <div style="display: flex; justify-content: space-between; margin: 10px 0;">
                                <span>Forme actuelle:</span>
                                <span style="color: #ffd700; font-weight: bold;">${fifaData.form_percentage || 0}%</span>
                            </div>
                            <div style="display: flex; justify-content: space-between; margin: 10px 0;">
                                <span>Forme physique:</span>
                                <span style="color: #ffd700; font-weight: bold;">${fifaData.fitness_score || 0}%</span>
                            </div>
                            <div style="display: flex; justify-content: space-between; margin: 10px 0;">
                                <span>Moral:</span>
                                <span style="color: #ffd700; font-weight: bold;">${fifaData.morale_percentage || 0}%</span>
                            </div>
                            <div style="display: flex; justify-content: space-between; margin: 10px 0;">
                                <span>État général:</span>
                                <span style="color: #ffd700; font-weight: bold;">${fifaData.form_percentage && fifaData.fitness_score ? Math.round((fifaData.form_percentage + fifaData.fitness_score) / 2) : 0}%</span>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            
            document.getElementById('comparison-content').innerHTML = content;
        }
        
        // Charger le contenu de l'onglet Tendances
        function loadTrendsContent() {
            if (!fifaData) return;
            
            const content = `
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div class="stat-card">
                        <h3>Évolution des Performances</h3>
                        <div class="chart-container">
                            <canvas id="performanceChart"></canvas>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <h3>Radar des Compétences</h3>
                        <div class="chart-container">
                            <canvas id="skillsRadar"></canvas>
                        </div>
                    </div>
                </div>
                
                <div style="margin-top: 20px;">
                    <div class="stat-card">
                        <h3>Analyse des Tendances</h3>
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 15px; margin-top: 15px;">
                            <div style="text-align: center; padding: 15px; background: rgba(255,255,255,0.05); border-radius: 8px;">
                                <div style="font-size: 1.5rem; color: #ffd700; margin-bottom: 5px;">📈</div>
                                <div style="font-weight: bold; margin-bottom: 5px;">Progression</div>
                                <div style="color: #87ceeb;">Rating FIFA en hausse</div>
                            </div>
                            <div style="text-align: center; padding: 15px; background: rgba(255,255,255,0.05); border-radius: 8px;">
                                <div style="font-size: 1.5rem; color: #ffd700; margin-bottom: 5px;">⚽</div>
                                <div style="font-weight: bold; margin-bottom: 5px;">Efficacité</div>
                                <div style="color: #87ceeb;">Buts et passes stables</div>
                            </div>
                            <div style="text-align: center; padding: 15px; background: rgba(255,255,255,0.05); border-radius: 8px;">
                                <div style="font-size: 1.5rem; color: #ffd700; margin-bottom: 5px;">🏃</div>
                                <div style="font-weight: bold; margin-bottom: 5px;">Physique</div>
                                <div style="color: #87ceeb;">Forme physique excellente</div>
                            </div>
                            <div style="text-align: center; padding: 15px; background: rgba(255,255,255,0.05); border-radius: 8px;">
                                <div style="font-size: 1.5rem; color: #ffd700; margin-bottom: 5px;">🎯</div>
                                <div style="font-weight: bold; margin-bottom: 5px;">Objectifs</div>
                                <div style="color: #87ceeb;">Rating 90+ cette saison</div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            
            document.getElementById('trends-content').innerHTML = content;
            
            // Créer les graphiques après un délai pour s'assurer que les canvas sont prêts
            setTimeout(() => {
                createPerformanceChart();
                createSkillsRadar();
            }, 100);
        }
        
        // Créer le graphique de performance
        function createPerformanceChart() {
            const ctx = document.getElementById('performanceChart');
            if (!ctx) return;
            
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: ['Match 1', 'Match 2', 'Match 3', 'Match 4', 'Match 5'],
                    datasets: [{
                        label: 'Rating FIFA',
                        data: [fifaData.overall_rating - 2, fifaData.overall_rating - 1, fifaData.overall_rating, fifaData.overall_rating + 1, fifaData.overall_rating + 2],
                        borderColor: '#ffd700',
                        backgroundColor: 'rgba(255, 215, 0, 0.1)',
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            labels: { color: 'white' }
                        }
                    },
                    scales: {
                        y: {
                            ticks: { color: 'white' },
                            grid: { color: 'rgba(255,255,255,0.1)' }
                        },
                        x: {
                            ticks: { color: 'white' },
                            grid: { color: 'rgba(255,255,255,0.1)' }
                        }
                    }
                }
            });
        }
        
        // Créer le radar des compétences
        function createSkillsRadar() {
            const ctx = document.getElementById('skillsRadar');
            if (!ctx) return;
            
            new Chart(ctx, {
                type: 'radar',
                data: {
                    labels: ['Vitesse', 'Tir', 'Passe', 'Dribble', 'Défense', 'Physique'],
                    datasets: [{
                        label: 'Compétences',
                        data: [
                            fifaData.max_speed || 50,
                            fifaData.shot_accuracy || 70,
                            fifaData.passes_completed || 80,
                            fifaData.duels_won || 75,
                            fifaData.tackles_won || 65,
                            fifaData.fitness_score || 85
                        ],
                        backgroundColor: 'rgba(255, 215, 0, 0.2)',
                        borderColor: '#ffd700',
                        borderWidth: 2,
                        pointBackgroundColor: '#ffd700'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            labels: { color: 'white' }
                        }
                    },
                    scales: {
                        r: {
                            ticks: { color: 'white' },
                            grid: { color: 'rgba(255,255,255,0.1)' },
                            pointLabels: { color: 'white' }
                        }
                    }
                }
            });
        }
        
        // Initialisation au chargement de la page
        document.addEventListener('DOMContentLoaded', function() {
            console.log('🚀 Portail joueur FIFA simple chargé !');
            loadFIFAData();
        });
    </script>
</body>
</html>
