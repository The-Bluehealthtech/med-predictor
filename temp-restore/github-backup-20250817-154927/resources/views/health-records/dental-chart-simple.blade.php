@extends('layouts.app')

@section('title', 'Diagramme Dentaire - Test')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">
                        <i class="fas fa-tooth"></i> 
                        Diagramme Dentaire - Test
                    </h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-9">
                            <!-- Zone du diagramme dentaire -->
                            <div class="dental-chart-container">
                                <div class="image-container" id="dentalImageContainer">
                                    <img src="{{ asset('images/dental-reference.jpg') }}?v=test&ts={{ time() }}" 
                                         alt="Diagramme dentaire réaliste" 
                                         class="dental-image" 
                                         id="dentalImage">
                                    <div class="tooth-overlay" id="toothOverlay">
                                        <!-- Les zones dentaires seront générées dynamiquement -->
                                    </div>
                                </div>
                                
                                <!-- Contrôles -->
                                <div class="controls mt-3">
                                    <button type="button" class="btn btn-primary" id="saveAnnotations">
                                        <i class="fas fa-save"></i> Sauvegarder
                                    </button>
                                    <button type="button" class="btn btn-secondary" id="resetZones">
                                        <i class="fas fa-undo"></i> Réinitialiser
                                    </button>
                                    <button type="button" class="btn btn-success" id="fixAllZones">
                                        <i class="fas fa-lock"></i> Fixer toutes
                                    </button>
                                    <button type="button" class="btn btn-warning" id="unfixAllZones">
                                        <i class="fas fa-unlock"></i> Défixer toutes
                                    </button>
                                    <button type="button" class="btn btn-info" id="toggleDebug">
                                        <i class="fas fa-bug"></i> Mode Debug
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-3">
                            <!-- Panneau d'informations -->
                            <div class="card">
                                <div class="card-header">
                                    <h5><i class="fas fa-info-circle"></i> Informations</h5>
                                </div>
                                <div class="card-body">
                                    <div id="toothInfo">
                                        <p class="text-muted">Cliquez sur une zone pour voir les détails</p>
                                    </div>
                                    
                                    <hr>
                                    
                                    <div class="stats">
                                        <h6>Statistiques</h6>
                                        <div class="row">
                                            <div class="col-6">
                                                <div class="stat-item">
                                                    <div class="stat-number" id="totalZones">0</div>
                                                    <div class="stat-label">Total</div>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="stat-item">
                                                    <div class="stat-number" id="fixedZones">0</div>
                                                    <div class="stat-label">Fixées</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <hr>
                                    
                                    <div class="notes-section">
                                        <h6>Notes</h6>
                                        <textarea class="form-control" id="toothNotes" rows="3" placeholder="Ajouter des notes..."></textarea>
                                        <button type="button" class="btn btn-sm btn-primary mt-2" id="saveNotes">
                                            <i class="fas fa-save"></i> Sauvegarder notes
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .dental-chart-container {
        position: relative;
        border: 2px solid #e2e8f0;
        border-radius: 8px;
        overflow: hidden;
        background: white;
        width: 100%;
        height: 600px; /* Même hauteur que working-drag.html original */
        cursor: default;
        margin-bottom: 20px;
    }
    
    .dental-image {
        width: 100%;
        height: 100%;
        object-fit: contain;
        opacity: 0.9;
        pointer-events: none;
        max-height: 100%;
    }
    
    .tooth-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        pointer-events: none;
    }
    
    .tooth-zone {
        position: absolute;
        width: 40px; /* Même taille que working-drag.html original */
        height: 25px; /* Même taille que working-drag.html original */
        border: 3px solid #3b82f6; /* Même bordure que working-drag.html original */
        background: rgba(59, 130, 246, 0.3);
        cursor: grab;
        pointer-events: all;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px; /* Même police que working-drag.html original */
        font-weight: bold;
        color: #1f2937;
        border-radius: 6px; /* Même border-radius que working-drag.html original */
        user-select: none;
        z-index: 10;
    }
    
    .tooth-zone:hover {
        background: rgba(59, 130, 246, 0.5);
        border-color: #1d4ed8;
        transform: scale(1.1);
        z-index: 20;
    }
    
    .tooth-zone.selected {
        background: rgba(59, 130, 246, 0.7);
        border-color: #1d4ed8;
        box-shadow: 0 0 10px rgba(59, 130, 246, 0.8);
        z-index: 30;
    }
    
    .tooth-zone.dragging {
        opacity: 0.9;
        z-index: 100;
        cursor: grabbing !important;
        transform: scale(1.2);
        box-shadow: 0 6px 20px rgba(0,0,0,0.4);
        border-color: #f59e0b;
        background: rgba(245, 158, 11, 0.4);
    }
    
    .tooth-zone.fixed {
        border-color: #10b981;
        background: rgba(16, 185, 129, 0.4);
        box-shadow: 0 0 6px rgba(16, 185, 129, 0.6);
    }
    
    .stat-item {
        background: #f7fafc;
        padding: 10px;
        border-radius: 6px;
        text-align: center;
        margin-bottom: 10px;
    }
    
    .stat-number {
        font-size: 1.5em;
        font-weight: bold;
        color: #3b82f6;
    }
    
    .stat-label {
        font-size: 0.9em;
        color: #718096;
    }
    
    /* Responsive design - adapté pour mobile */
    @media (max-width: 768px) {
        .dental-chart-container {
            height: 400px; /* Plus petit sur mobile mais pas trop */
        }
        
        .tooth-zone {
            width: 35px;
            height: 20px;
            font-size: 10px;
        }
    }
    
    @media (max-width: 576px) {
        .dental-chart-container {
            height: 300px; /* Encore plus petit sur très petits écrans */
        }
        
        .tooth-zone {
            width: 30px;
            height: 18px;
            font-size: 9px;
        }
    }
</style>
@endpush

@push('scripts')
<script>
// Variables globales
let selectedTooth = null;
let isDragging = false;
let currentDraggingTooth = null;
let initialMouseX = 0;
let initialMouseY = 0;
let initialToothX = 0;
let initialToothY = 0;
let annotations = [];
let debugMode = false;

// Initialisation
document.addEventListener('DOMContentLoaded', function() {
    console.log('Initialisation du diagramme dentaire...');
    createToothZones();
    setupEventListeners();
});

function createToothZones() {
    const overlay = document.getElementById('toothOverlay');
    const teeth = [
        // Quadrant 1 (droite supérieure)
        '11', '12', '13', '14', '15', '16', '17', '18',
        // Quadrant 2 (gauche supérieure)
        '21', '22', '23', '24', '25', '26', '27', '28',
        // Quadrant 3 (gauche inférieure)
        '31', '32', '33', '34', '35', '36', '37', '38',
        // Quadrant 4 (droite inférieure)
        '41', '42', '43', '44', '45', '46', '47', '48'
    ];
    
    const defaultPositions = {
        // Quadrant 1
        '11': {top: 100, left: 350}, '12': {top: 100, left: 320}, '13': {top: 100, left: 290}, '14': {top: 100, left: 260},
        '15': {top: 100, left: 230}, '16': {top: 100, left: 200}, '17': {top: 100, left: 170}, '18': {top: 100, left: 140},
        // Quadrant 2
        '21': {top: 100, left: 430}, '22': {top: 100, left: 460}, '23': {top: 100, left: 490}, '24': {top: 100, left: 520},
        '25': {top: 100, left: 550}, '26': {top: 100, left: 580}, '27': {top: 100, left: 610}, '28': {top: 100, left: 640},
        // Quadrant 3
        '31': {top: 460, left: 430}, '32': {top: 460, left: 460}, '33': {top: 460, left: 490}, '34': {top: 460, left: 520},
        '35': {top: 460, left: 550}, '36': {top: 460, left: 580}, '37': {top: 460, left: 610}, '38': {top: 460, left: 640},
        // Quadrant 4
        '41': {top: 460, left: 350}, '42': {top: 460, left: 320}, '43': {top: 460, left: 290}, '44': {top: 460, left: 260},
        '45': {top: 460, left: 230}, '46': {top: 460, left: 200}, '47': {top: 460, left: 170}, '48': {top: 460, left: 140}
    };
    
    teeth.forEach(toothId => {
        const zone = document.createElement('div');
        zone.className = 'tooth-zone';
        zone.setAttribute('data-tooth-id', toothId);
        zone.textContent = toothId;
        
        const pos = defaultPositions[toothId];
        zone.style.top = pos.top + 'px';
        zone.style.left = pos.left + 'px';
        
        overlay.appendChild(zone);
    });
    
    setupZoneEvents();
}

function setupZoneEvents() {
    const zones = document.querySelectorAll('.tooth-zone');
    zones.forEach(zone => {
        // Clic pour sélection
        zone.addEventListener('click', function(e) {
            if (!isDragging) {
                selectTooth(this.getAttribute('data-tooth-id'));
            }
        });
        
        // Mousedown pour drag
        zone.addEventListener('mousedown', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const toothId = this.getAttribute('data-tooth-id');
            
            isDragging = true;
            currentDraggingTooth = toothId;
            
            initialMouseX = e.clientX;
            initialMouseY = e.clientY;
            initialToothX = parseInt(this.style.left);
            initialToothY = parseInt(this.style.top);
            
            this.classList.add('dragging');
        });
    });
    
    // Events sur le conteneur
    const container = document.getElementById('dentalImageContainer');
    container.addEventListener('mousemove', handleMouseMove);
    container.addEventListener('mouseup', handleMouseUp);
    container.addEventListener('mouseleave', handleMouseLeave);
}

function handleMouseMove(e) {
    if (isDragging && currentDraggingTooth) {
        const zone = document.querySelector(`[data-tooth-id="${currentDraggingTooth}"]`);
        if (zone) {
            const dx = e.clientX - initialMouseX;
            const dy = e.clientY - initialMouseY;
            
            const newX = initialToothX + dx;
            const newY = initialToothY + dy;
            
            // Limiter dans les limites du conteneur
            const containerRect = container.getBoundingClientRect();
            const zoneRect = zone.getBoundingClientRect();
            
            const maxX = containerRect.width - zoneRect.width;
            const maxY = containerRect.height - zoneRect.height;
            
            const clampedX = Math.max(0, Math.min(newX, maxX));
            const clampedY = Math.max(0, Math.min(newY, maxY));
            
            zone.style.left = clampedX + 'px';
            zone.style.top = clampedY + 'px';
        }
    }
}

function handleMouseUp(e) {
    if (isDragging && currentDraggingTooth) {
        const zone = document.querySelector(`[data-tooth-id="${currentDraggingTooth}"]`);
        if (zone) {
            zone.classList.remove('dragging');
            zone.classList.add('fixed');
        }
        
        isDragging = false;
        currentDraggingTooth = null;
        updateStats();
    }
}

function handleMouseLeave(e) {
    if (isDragging && currentDraggingTooth) {
        handleMouseUp(e);
    }
}

function selectTooth(toothId) {
    // Désélectionner la dent précédente
    if (selectedTooth) {
        const prevZone = document.querySelector(`[data-tooth-id="${selectedTooth}"]`);
        if (prevZone) {
            prevZone.classList.remove('selected');
        }
    }
    
    // Sélectionner la nouvelle dent
    selectedTooth = toothId;
    const zone = document.querySelector(`[data-tooth-id="${toothId}"]`);
    if (zone) {
        zone.classList.add('selected');
    }
    
    // Mettre à jour les informations
    updateToothInfo(toothId);
}

function updateToothInfo(toothId) {
    const toothInfo = document.getElementById('toothInfo');
    toothInfo.innerHTML = `
        <h6>Dent ${toothId}</h6>
        <p><strong>Type:</strong> ${getToothType(toothId)}</p>
        <p><strong>Quadrant:</strong> ${getQuadrant(toothId)}</p>
        <p><strong>Statut:</strong> <span class="badge bg-primary">Normal</span></p>
    `;
}

function getToothType(toothId) {
    const position = parseInt(toothId.slice(1));
    if (position <= 2) return 'Incisive';
    if (position === 3) return 'Canine';
    if (position <= 5) return 'Prémolaire';
    return 'Molaire';
}

function getQuadrant(toothId) {
    const quadrant = toothId.slice(0, 1);
    const names = {
        '1': 'Supérieur Droit',
        '2': 'Supérieur Gauche', 
        '3': 'Inférieur Gauche',
        '4': 'Inférieur Droit'
    };
    return names[quadrant] || 'Inconnu';
}

function updateStats() {
    const zones = document.querySelectorAll('.tooth-zone');
    const fixed = document.querySelectorAll('.tooth-zone.fixed');
    
    document.getElementById('totalZones').textContent = zones.length;
    document.getElementById('fixedZones').textContent = fixed.length;
}

function setupEventListeners() {
    // Fixer toutes les zones
    document.getElementById('fixAllZones').addEventListener('click', function() {
        const zones = document.querySelectorAll('.tooth-zone');
        zones.forEach(zone => {
            zone.classList.add('fixed');
        });
        updateStats();
    });
    
    // Défixer toutes les zones
    document.getElementById('unfixAllZones').addEventListener('click', function() {
        const zones = document.querySelectorAll('.tooth-zone');
        zones.forEach(zone => {
            zone.classList.remove('fixed');
        });
        updateStats();
    });
    
    // Réinitialiser les zones
    document.getElementById('resetZones').addEventListener('click', function() {
        if (confirm('Êtes-vous sûr de vouloir réinitialiser toutes les zones ?')) {
            location.reload();
        }
    });
    
    // Mode debug
    document.getElementById('toggleDebug').addEventListener('click', function() {
        debugMode = !debugMode;
        const zones = document.querySelectorAll('.tooth-zone');
        zones.forEach(zone => {
            if (debugMode) {
                zone.title = `X: ${zone.style.left}, Y: ${zone.style.top}`;
            } else {
                zone.title = '';
            }
        });
    });
    
    // Sauvegarder
    document.getElementById('saveAnnotations').addEventListener('click', function() {
        alert('Fonctionnalité de sauvegarde à implémenter avec l\'API Laravel');
    });
    
    // Sauvegarder notes
    document.getElementById('saveNotes').addEventListener('click', function() {
        alert('Notes sauvegardées !');
    });
    
    // Initialiser les stats
    updateStats();
}
</script>
@endpush 