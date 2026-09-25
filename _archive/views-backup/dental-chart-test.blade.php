@extends('layouts.empty')

@section('title', 'Test Diagramme Dentaire Adapté')

@section('styles')
<style>
    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        margin: 0;
        padding: 20px;
        background: #f8fafc;
    }
    
    .container {
        max-width: 900px;
        margin: 0 auto;
        background: white;
        border-radius: 10px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        padding: 20px;
    }
    
    .header {
        text-align: center;
        margin-bottom: 30px;
    }
    
    .header h1 {
        color: #1f2937;
        margin: 0;
    }
    
    .header p {
        color: #6b7280;
        margin: 10px 0 0 0;
    }
    
    .dental-chart-container {
        border: 2px solid #e2e8f0;
        border-radius: 8px;
        overflow: hidden;
        background: white;
        margin-bottom: 20px;
    }
    
    .chart-info {
        background: #f8fafc;
        padding: 15px;
        border-radius: 8px;
        margin-top: 20px;
    }
    
    .chart-info h3 {
        color: #374151;
        margin: 0 0 10px 0;
    }
    
    .chart-info p {
        color: #6b7280;
        margin: 5px 0;
        font-size: 14px;
    }
    
    .instructions {
        background: #eff6ff;
        border: 1px solid #3b82f6;
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 20px;
    }
    
    .instructions h3 {
        color: #1e40af;
        margin: 0 0 10px 0;
    }
    
    .instructions ul {
        color: #1e40af;
        margin: 0;
        padding-left: 20px;
    }
    
    .instructions li {
        margin: 5px 0;
    }
    
    .feedback {
        background: #f0fdf4;
        border: 1px solid #22c55e;
        border-radius: 8px;
        padding: 15px;
        margin-top: 20px;
        display: none;
    }
    
    .feedback.show {
        display: block;
    }
    
    .feedback h4 {
        color: #166534;
        margin: 0 0 10px 0;
    }
    
    .feedback p {
        color: #166534;
        margin: 5px 0;
    }
    
    .dental-controls {
        background: #fef3c7;
        border: 1px solid #f59e0b;
        border-radius: 8px;
        padding: 15px;
        margin-top: 20px;
    }
    
    .dental-controls h4 {
        color: #92400e;
        margin: 0 0 10px 0;
    }
    
    .dental-controls select, .dental-controls textarea {
        margin: 5px 0;
        padding: 5px;
        border: 1px solid #d1d5db;
        border-radius: 4px;
        font-size: 12px;
    }
    
    .dental-controls button {
        background: #f59e0b;
        color: white;
        border: none;
        padding: 8px 16px;
        border-radius: 4px;
        cursor: pointer;
        margin: 5px 5px 5px 0;
        font-size: 12px;
    }
    
    .dental-controls button:hover {
        background: #d97706;
    }
</style>
@endsection

@section('content')
<div class="container">
    <div class="header">
        <h1>🦷 Test Diagramme Dentaire Adapté</h1>
        <p>Vérification de l'intégration du diagramme dentaire dans l'espace disponible</p>
    </div>
    
    <div class="instructions">
        <h3>📋 Instructions de Test</h3>
        <ul>
            <li><strong>Cliquez sur une dent</strong> pour la sélectionner (elle doit devenir plus foncée)</li>
            <li><strong>Survolez les dents</strong> pour voir l'effet de hover</li>
            <li><strong>Vérifiez que les numéros</strong> sont bien centrés dans chaque zone</li>
            <li><strong>Testez l'interactivité</strong> en cliquant sur différentes dents</li>
            <li><strong>Utilisez les contrôles</strong> pour changer l'état des dents</li>
        </ul>
    </div>
    
    <div class="dental-chart-container">
        <div id="dental-chart-svg-container" style="width: 100%; height: 300px; border: 1px solid #e2e8f0; border-radius: 8px; background: #f8fafc;">
            <!-- Le SVG sera chargé ici dynamiquement -->
        </div>
    </div>
    
    <div class="feedback" id="feedback">
        <h4>✅ Feedback Interactif</h4>
        <p><strong>Dent sélectionnée :</strong> <span id="selected-tooth">Aucune</span></p>
        <p><strong>Statut :</strong> <span id="tooth-status">En attente de sélection</span></p>
    </div>
    
    <div class="dental-controls" id="dental-controls" style="display: none;">
        <h4>🦷 Contrôles de la Dent</h4>
        <div>
            <label><strong>État de la dent :</strong></label>
            <select id="tooth-status-select">
                <option value="healthy">Sain</option>
                <option value="cavity">Carie</option>
                <option value="filling">Obturation</option>
                <option value="crown">Couronne</option>
                <option value="missing">Manquante</option>
                <option value="implant">Implant</option>
                <option value="treatment">En traitement</option>
            </select>
        </div>
        <div>
            <label><strong>Notes :</strong></label>
            <textarea id="tooth-notes" rows="2" placeholder="Notes sur cette dent..."></textarea>
        </div>
        <div>
            <button onclick="saveToothData()">💾 Sauvegarder</button>
            <button onclick="clearSelection()">🗑️ Effacer</button>
        </div>
    </div>
    
    <div class="chart-info">
        <h3>Informations sur l'adaptation :</h3>
        <p><strong>Hauteur originale :</strong> 600px</p>
        <p><strong>Hauteur adaptée :</strong> 300px</p>
        <p><strong>ViewBox adapté :</strong> 0 0 800 400</p>
        <p><strong>Position des dents :</strong> Ajustée pour la nouvelle taille</p>
        <p><strong>Numéros :</strong> Repositionnés et redimensionnés (10px)</p>
        <p><strong>Interactivité :</strong> Améliorée avec feedback visuel</p>
        <p><strong>Légende :</strong> Repositionnée et redimensionnée</p>
        <p><strong>Chargement :</strong> Dynamique via fetch()</p>
    </div>
</div>
@endsection

@section('scripts')
<script>
    let selectedTooth = null;
    let dentalData = {};
    
    // Fonction pour initialiser le diagramme dentaire
    function initializeDentalChart() {
        console.log('🦷 Initialisation du diagramme dentaire interactif...');
        
        // Charger le SVG dynamiquement
        fetch('/images/dental-chart-interactive.svg')
            .then(response => response.text())
            .then(svgContent => {
                const container = document.getElementById('dental-chart-svg-container');
                container.innerHTML = svgContent;
                
                // Écouter les événements de sélection de dents
                document.addEventListener('toothSelected', function(event) {
                    const toothId = event.detail.toothId;
                    selectedTooth = toothId;
                    
                    const feedback = document.getElementById('feedback');
                    const selectedToothSpan = document.getElementById('selected-tooth');
                    const toothStatusSpan = document.getElementById('tooth-status');
                    const controls = document.getElementById('dental-controls');
                    
                    selectedToothSpan.textContent = toothId;
                    toothStatusSpan.textContent = 'Dent sélectionnée avec succès !';
                    feedback.classList.add('show');
                    controls.style.display = 'block';
                    
                    // Charger les données existantes
                    if (dentalData[toothId]) {
                        document.getElementById('tooth-status-select').value = dentalData[toothId].status || 'healthy';
                        document.getElementById('tooth-notes').value = dentalData[toothId].notes || '';
                    } else {
                        document.getElementById('tooth-status-select').value = 'healthy';
                        document.getElementById('tooth-notes').value = '';
                    }
                    
                    console.log('🦷 Dent sélectionnée dans la page de test:', toothId);
                });
                
                console.log('🦷 SVG chargé avec succès');
            })
            .catch(error => {
                console.error('🦷 Erreur lors du chargement du SVG:', error);
            });
    }
    
    // Fonction pour sauvegarder les données de la dent
    function saveToothData() {
        if (!selectedTooth) {
            alert('Veuillez sélectionner une dent avant de sauvegarder');
            return;
        }
        
        const status = document.getElementById('tooth-status-select').value;
        const notes = document.getElementById('tooth-notes').value;
        
        dentalData[selectedTooth] = {
            status: status,
            notes: notes,
            lastUpdated: new Date().toISOString()
        };
        
        // Mettre à jour la couleur de la dent
        updateToothColor(selectedTooth, status);
        
        alert(`Données de la dent ${selectedTooth} sauvegardées avec succès !`);
    }
    
    // Fonction pour effacer la sélection
    function clearSelection() {
        selectedTooth = null;
        document.getElementById('feedback').classList.remove('show');
        document.getElementById('dental-controls').style.display = 'none';
        
        // Désélectionner visuellement
        const selectedToothElement = document.querySelector('.tooth.selected');
        if (selectedToothElement) {
            selectedToothElement.classList.remove('selected');
        }
    }
    
    // Fonction pour mettre à jour la couleur de la dent
    function updateToothColor(toothId, status) {
        const tooth = document.querySelector(`[data-tooth-id="${toothId}"]`);
        if (tooth) {
            // Retirer toutes les classes de couleur
            tooth.classList.remove('healthy', 'cavity', 'filling', 'crown', 'missing', 'implant', 'treatment');
            // Ajouter la nouvelle classe
            tooth.classList.add(status);
        }
    }
    
    // Initialiser quand la page se charge
    document.addEventListener('DOMContentLoaded', function() {
        console.log('🦷 Page de test du diagramme dentaire chargée');
        initializeDentalChart();
    });
</script>
@endsection
