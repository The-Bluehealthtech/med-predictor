@extends('layouts.app')

@section('title', 'Diagramme Dentaire')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">
                        <i class="fas fa-tooth"></i> 
                        Diagramme Dentaire - {{ $healthRecord->patient_name ?? 'Patient' }}
                    </h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-9">
                            <!-- Zone du diagramme dentaire -->
                            <div class="dental-chart-container">
                                <div class="image-container" id="dentalImageContainer">
                                    <img src="{{ asset('images/dental-reference.jpg') }}?v=laravel&ts={{ time() }}" 
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

<!-- Modal pour les détails de la dent -->
<div class="modal fade" id="toothModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Détails de la dent <span id="modalToothId"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Type:</strong> <span id="modalToothType"></span></p>
                        <p><strong>Quadrant:</strong> <span id="modalToothQuadrant"></span></p>
                        <p><strong>Position:</strong> <span id="modalToothPosition"></span></p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Statut:</strong> <span id="modalToothStatus"></span></p>
                        <p><strong>Coordonnées:</strong> <span id="modalToothCoords"></span></p>
                    </div>
                </div>
                <hr>
                <div class="form-group">
                    <label for="modalToothNotes">Notes:</label>
                    <textarea class="form-control" id="modalToothNotes" rows="3"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                <button type="button" class="btn btn-primary" id="saveModalNotes">Sauvegarder</button>
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
        height: 600px;
        cursor: default;
    }
    
    .dental-image {
        width: 100%;
        height: 100%;
        object-fit: contain;
        opacity: 0.9;
        pointer-events: none;
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
        width: 40px;
        height: 25px;
        border: 3px solid #3b82f6;
        background: rgba(59, 130, 246, 0.3);
        cursor: grab;
        pointer-events: all;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        font-weight: bold;
        color: #1f2937;
        border-radius: 6px;
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
    
    .tooth-zone.problem {
        border-color: #ef4444;
        background: rgba(239, 68, 68, 0.4);
    }
    
    .tooth-zone.warning {
        border-color: #f59e0b;
        background: rgba(245, 158, 11, 0.4);
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
    
    .debug-info {
        background: #fef3c7;
        border: 1px solid #f59e0b;
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 20px;
        font-size: 0.9em;
    }
</style>
@endpush

@push('scripts')
<script>
// Configuration globale
const DENTAL_CONFIG = {
    healthRecordId: {{ $healthRecord->id ?? 'null' }},
    apiBase: '{{ route("api.dental.annotations") }}',
    saveAllUrl: '{{ route("api.dental.save-all") }}',
    statsUrl: '{{ route("api.dental.stats") }}',
    resetUrl: '{{ route("api.dental.reset") }}',
    csrfToken: '{{ csrf_token() }}'
};

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
    initializeDentalChart();
    loadAnnotations();
    setupEventListeners();
});

function initializeDentalChart() {
    console.log('Initialisation du diagramme dentaire...');
    createToothZones();
}

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
            const annotation = annotations.find(a => a.tooth_id === toothId);
            
            if (annotation && annotation.status === 'fixed') {
                console.log('Zone fixée, impossible de déplacer:', toothId);
                return;
            }
            
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
            
            // Mettre à jour l'annotation
            updateAnnotation(currentDraggingTooth, {
                position_x: parseInt(zone.style.left),
                position_y: parseInt(zone.style.top),
                status: 'fixed'
            });
        }
        
        isDragging = false;
        currentDraggingTooth = null;
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
    
    // Ouvrir le modal
    openToothModal(toothId);
}

function updateToothInfo(toothId) {
    const annotation = annotations.find(a => a.tooth_id === toothId) || {
        tooth_id: toothId,
        status: 'normal',
        notes: ''
    };
    
    const toothInfo = document.getElementById('toothInfo');
    toothInfo.innerHTML = `
        <h6>Dent ${toothId}</h6>
        <p><strong>Type:</strong> ${getToothType(toothId)}</p>
        <p><strong>Quadrant:</strong> ${getQuadrant(toothId)}</p>
        <p><strong>Statut:</strong> <span class="badge bg-${getStatusColor(annotation.status)}">${annotation.status}</span></p>
        <p><strong>Notes:</strong> ${annotation.notes || 'Aucune'}</p>
    `;
    
    // Mettre à jour les notes
    document.getElementById('toothNotes').value = annotation.notes || '';
}

function openToothModal(toothId) {
    const annotation = annotations.find(a => a.tooth_id === toothId) || {
        tooth_id: toothId,
        status: 'normal',
        notes: ''
    };
    
    document.getElementById('modalToothId').textContent = toothId;
    document.getElementById('modalToothType').textContent = getToothType(toothId);
    document.getElementById('modalToothQuadrant').textContent = getQuadrant(toothId);
    document.getElementById('modalToothPosition').textContent = toothId.slice(1);
    document.getElementById('modalToothStatus').textContent = annotation.status;
    document.getElementById('modalToothCoords').textContent = `${annotation.position_x || 0}, ${annotation.position_y || 0}`;
    document.getElementById('modalToothNotes').value = annotation.notes || '';
    
    const modal = new bootstrap.Modal(document.getElementById('toothModal'));
    modal.show();
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

function getStatusColor(status) {
    const colors = {
        'normal': 'primary',
        'selected': 'info',
        'fixed': 'success',
        'problem': 'danger',
        'warning': 'warning'
    };
    return colors[status] || 'secondary';
}

function updateAnnotation(toothId, data) {
    const index = annotations.findIndex(a => a.tooth_id === toothId);
    if (index >= 0) {
        annotations[index] = { ...annotations[index], ...data };
    } else {
        annotations.push({
            tooth_id: toothId,
            health_record_id: DENTAL_CONFIG.healthRecordId,
            ...data
        });
    }
    
    // Mettre à jour l'apparence de la zone
    const zone = document.querySelector(`[data-tooth-id="${toothId}"]`);
    if (zone) {
        zone.className = `tooth-zone ${data.status || 'normal'}`;
    }
}

function loadAnnotations() {
    if (!DENTAL_CONFIG.healthRecordId) return;
    
    fetch(`${DENTAL_CONFIG.apiBase}?health_record_id=${DENTAL_CONFIG.healthRecordId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                annotations = data.data;
                updateZonesFromAnnotations();
                updateStats();
            }
        })
        .catch(error => {
            console.error('Erreur lors du chargement des annotations:', error);
        });
}

function updateZonesFromAnnotations() {
    annotations.forEach(annotation => {
        const zone = document.querySelector(`[data-tooth-id="${annotation.tooth_id}"]`);
        if (zone) {
            if (annotation.position_x !== null) {
                zone.style.left = annotation.position_x + 'px';
            }
            if (annotation.position_y !== null) {
                zone.style.top = annotation.position_y + 'px';
            }
            zone.className = `tooth-zone ${annotation.status || 'normal'}`;
        }
    });
}

function updateStats() {
    const total = annotations.length;
    const fixed = annotations.filter(a => a.status === 'fixed').length;
    
    document.getElementById('totalZones').textContent = total;
    document.getElementById('fixedZones').textContent = fixed;
}

function setupEventListeners() {
    // Sauvegarder toutes les annotations
    document.getElementById('saveAnnotations').addEventListener('click', saveAllAnnotations);
    
    // Réinitialiser les zones
    document.getElementById('resetZones').addEventListener('click', resetZones);
    
    // Fixer toutes les zones
    document.getElementById('fixAllZones').addEventListener('click', fixAllZones);
    
    // Défixer toutes les zones
    document.getElementById('unfixAllZones').addEventListener('click', unfixAllZones);
    
    // Mode debug
    document.getElementById('toggleDebug').addEventListener('click', toggleDebugMode);
    
    // Sauvegarder les notes
    document.getElementById('saveNotes').addEventListener('click', saveNotes);
    document.getElementById('saveModalNotes').addEventListener('click', saveModalNotes);
}

function saveAllAnnotations() {
    if (!DENTAL_CONFIG.healthRecordId) {
        alert('Aucun dossier de santé sélectionné');
        return;
    }
    
    // Collecter toutes les annotations
    const zones = document.querySelectorAll('.tooth-zone');
    const annotationsToSave = [];
    
    zones.forEach(zone => {
        const toothId = zone.getAttribute('data-tooth-id');
        const annotation = annotations.find(a => a.tooth_id === toothId) || {
            tooth_id: toothId,
            health_record_id: DENTAL_CONFIG.healthRecordId,
            status: 'normal'
        };
        
        annotation.position_x = parseInt(zone.style.left);
        annotation.position_y = parseInt(zone.style.top);
        
        if (zone.classList.contains('fixed')) {
            annotation.status = 'fixed';
        }
        
        annotationsToSave.push(annotation);
    });
    
    // Envoyer au serveur
    fetch(DENTAL_CONFIG.saveAllUrl, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': DENTAL_CONFIG.csrfToken
        },
        body: JSON.stringify({
            health_record_id: DENTAL_CONFIG.healthRecordId,
            annotations: annotationsToSave
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Annotations sauvegardées avec succès !');
            annotations = data.data;
            updateStats();
        } else {
            alert('Erreur lors de la sauvegarde');
        }
    })
    .catch(error => {
        console.error('Erreur lors de la sauvegarde:', error);
        alert('Erreur lors de la sauvegarde');
    });
}

function resetZones() {
    if (confirm('Êtes-vous sûr de vouloir réinitialiser toutes les zones ?')) {
        fetch(DENTAL_CONFIG.resetUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': DENTAL_CONFIG.csrfToken
            },
            body: JSON.stringify({
                health_record_id: DENTAL_CONFIG.healthRecordId
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            }
        })
        .catch(error => {
            console.error('Erreur lors de la réinitialisation:', error);
        });
    }
}

function fixAllZones() {
    const zones = document.querySelectorAll('.tooth-zone');
    zones.forEach(zone => {
        zone.classList.add('fixed');
        updateAnnotation(zone.getAttribute('data-tooth-id'), { status: 'fixed' });
    });
    updateStats();
}

function unfixAllZones() {
    const zones = document.querySelectorAll('.tooth-zone');
    zones.forEach(zone => {
        zone.classList.remove('fixed');
        updateAnnotation(zone.getAttribute('data-tooth-id'), { status: 'normal' });
    });
    updateStats();
}

function toggleDebugMode() {
    debugMode = !debugMode;
    const zones = document.querySelectorAll('.tooth-zone');
    zones.forEach(zone => {
        if (debugMode) {
            zone.title = `X: ${zone.style.left}, Y: ${zone.style.top}`;
        } else {
            zone.title = '';
        }
    });
}

function saveNotes() {
    if (!selectedTooth) return;
    
    const notes = document.getElementById('toothNotes').value;
    updateAnnotation(selectedTooth, { notes: notes });
    
    alert('Notes sauvegardées !');
}

function saveModalNotes() {
    if (!selectedTooth) return;
    
    const notes = document.getElementById('modalToothNotes').value;
    updateAnnotation(selectedTooth, { notes: notes });
    
    // Fermer le modal
    const modal = bootstrap.Modal.getInstance(document.getElementById('toothModal'));
    modal.hide();
    
    alert('Notes sauvegardées !');
}
</script>
@endpush 