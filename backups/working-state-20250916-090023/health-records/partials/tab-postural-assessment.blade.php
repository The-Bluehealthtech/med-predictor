<!-- Onglet: Évaluation Posturale -->
<div class="space-y-6">
    <div class="bg-purple-50 border border-purple-200 rounded-lg p-6">
        <h3 class="text-lg font-semibold text-purple-900 mb-4">🦴 Évaluation Posturale Interactive (ICD-10: M40-M54)</h3>
        <p class="text-purple-700 mb-4">Utilisez l'outil interactif pour analyser la posture du patient :</p>
        
        <div class="bg-white border border-gray-200 rounded-lg p-4">
            <div class="mb-4">
                <ul class="text-sm text-gray-600 space-y-1 mb-4">
                    <li>• <strong>🎯 Marqueur :</strong> Cliquez sur un point anatomique pour ajouter une note d'anomalie</li>
                    <li>• <strong>📐 Angle :</strong> Cliquez sur 3 points pour mesurer un angle</li>
                    <li>• <strong>📏 Fil à Plomb :</strong> Affiche une ligne de référence verticale</li>
                    <li>• <strong>🗑️ Effacer :</strong> Supprime toutes les annotations</li>
                    <li>• <strong>💾 Exporter :</strong> Télécharge les données d'évaluation en JSON</li>
                </ul>
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3">
                    <p class="text-sm text-yellow-800">
                        <strong>💡 Astuce :</strong> Les données posturales sont automatiquement sauvegardées dans le formulaire et seront incluses dans le dossier médical.
                    </p>
                </div>
            </div>
            
            <!-- Composant d'analyse posturale -->
            <div id="postural-chart-container">
                <div class="postural-chart-container">
                    <!-- Toolbar -->
                    <div class="toolbar bg-white border-b border-gray-200 p-4">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-4">
                                <!-- View Selector -->
                                <div class="flex items-center space-x-2">
                                    <label class="text-sm font-medium text-gray-700">Vue :</label>
                                    <select id="postural-view-selector" class="border border-gray-300 rounded px-2 py-1 text-sm">
                                        <option value="anterior">Antérieure</option>
                                        <option value="posterior">Postérieure</option>
                                        <option value="lateral">Latérale</option>
                                    </select>
                                </div>
                                
                                <!-- Tool Selector -->
                                <div class="flex items-center space-x-2">
                                    <label class="text-sm font-medium text-gray-700">Outil :</label>
                                    <div class="flex space-x-1">
                                        <button 
                                            id="postural-marker-tool"
                                            class="px-3 py-1 rounded text-sm bg-blue-500 text-white"
                                            title="Marqueur"
                                        >
                                            🎯
                                        </button>
                                        <button 
                                            id="postural-angle-tool"
                                            class="px-3 py-1 rounded text-sm bg-gray-200 text-gray-700"
                                            title="Mesure d'angle"
                                        >
                                            📐
                                        </button>
                                        <button 
                                            id="postural-plumb-tool"
                                            class="px-3 py-1 rounded text-sm bg-gray-200 text-gray-700"
                                            title="Fil à plomb"
                                        >
                                            📏
                                        </button>
                                    </div>
                                </div>
                                
                                <!-- Color Palette -->
                                <div id="postural-color-palette" class="flex items-center space-x-2" style="display: none;">
                                    <label class="text-sm font-medium text-gray-700">Couleur :</label>
                                    <div class="flex space-x-1">
                                        <button class="w-6 h-6 rounded border-2 border-gray-800 bg-red-500" data-color="#ff0000"></button>
                                        <button class="w-6 h-6 rounded border-2 border-gray-300 bg-green-500" data-color="#00ff00"></button>
                                        <button class="w-6 h-6 rounded border-2 border-gray-300 bg-blue-500" data-color="#0000ff"></button>
                                        <button class="w-6 h-6 rounded border-2 border-gray-300 bg-yellow-500" data-color="#ffff00"></button>
                                        <button class="w-6 h-6 rounded border-2 border-gray-300 bg-magenta-500" data-color="#ff00ff"></button>
                                        <button class="w-6 h-6 rounded border-2 border-gray-300 bg-cyan-500" data-color="#00ffff"></button>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Actions -->
                            <div class="flex items-center space-x-2">
                                <button id="postural-clear-btn" class="px-3 py-1 bg-red-500 text-white rounded text-sm">
                                    🗑️ Effacer
                                </button>
                                <button id="postural-export-btn" class="px-3 py-1 bg-green-500 text-white rounded text-sm">
                                    💾 Exporter
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Chart Container -->
                    <div class="chart-area bg-gray-50 p-4">
                        <div class="flex flex-col lg:flex-row">
                            <!-- SVG Chart -->
                            <div class="svg-container relative mx-auto lg:mx-0" style="width: 600px; height: 800px; max-width: 100%; max-height: 80vh;">
                                <div id="postural-svg-content" class="cursor-crosshair w-full h-full">
                                    <!-- SVG content will be loaded here -->
                                </div>
                                
                                <!-- Annotations Layer -->
                                <svg id="postural-annotations" class="absolute top-0 left-0 w-full h-full pointer-events-none" style="width: 600px; height: 800px; max-width: 100%; max-height: 80vh;">
                                    <!-- Markers and angles will be added here -->
                                </svg>
                            </div>
                            
                            <!-- Sidebar -->
                            <div class="lg:ml-6 mt-4 lg:mt-0 flex-1">
                                <div class="bg-white rounded-lg shadow p-4">
                                    <h3 class="text-lg font-semibold mb-4">📊 Données d'Évaluation</h3>
                                    
                                    <!-- Markers List -->
                                    <div class="mb-4">
                                        <h4 class="font-medium text-gray-700 mb-2">🎯 Marqueurs (<span id="postural-markers-count">0</span>)</h4>
                                        <div id="postural-markers-list" class="space-y-2 max-h-32 overflow-y-auto">
                                            <!-- Markers will be listed here -->
                                        </div>
                                    </div>
                                    
                                    <!-- Angles List -->
                                    <div class="mb-4">
                                        <h4 class="font-medium text-gray-700 mb-2">📐 Angles (<span id="postural-angles-count">0</span>)</h4>
                                        <div id="postural-angles-list" class="space-y-2 max-h-32 overflow-y-auto">
                                            <!-- Angles will be listed here -->
                                        </div>
                                    </div>
                                    
                                    <!-- Export Data -->
                                    <div class="mt-4 p-3 bg-blue-50 rounded">
                                        <h4 class="font-medium text-blue-800 mb-2">💾 Données d'Export</h4>
                                        <textarea 
                                            id="postural-export-data"
                                            rows="4" 
                                            class="w-full text-xs border border-blue-200 rounded p-2"
                                            readonly
                                        ></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Données cachées pour le formulaire -->
            <input type="hidden" name="postural_assessment_data" id="postural_assessment_data" value="{{ old('postural_assessment_data') }}">
            
            <!-- Postural Assessment Summary -->
            <div class="mt-4 bg-blue-50 border border-blue-200 rounded-lg p-4">
                <h4 class="text-sm font-semibold text-blue-800 mb-3 flex items-center">
                    <span class="mr-2">📊</span>
                    Résumé de l'Évaluation Posturale
                </h4>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                    <div class="flex items-center justify-between">
                        <span>Marqueurs:</span>
                        <span class="font-semibold text-blue-600" id="postural-markers-count">0</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span>Angles:</span>
                        <span class="font-semibold text-green-600" id="postural-angles-count">0</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span>Vue actuelle:</span>
                        <span class="font-semibold text-purple-600" id="postural-current-view">Antérieure</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span>Statut:</span>
                        <span class="font-semibold text-orange-600" id="postural-status">En cours</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Postural Analysis Results -->
    <div class="bg-green-50 border border-green-200 rounded-lg p-6">
        <h3 class="text-lg font-semibold text-green-900 mb-4">📋 Analyse Posturale</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label for="postural_analysis" class="block text-sm font-medium text-gray-700 mb-2">
                    Analyse Posturale
                </label>
                <textarea 
                    id="postural_analysis" 
                    name="postural_analysis" 
                    rows="6"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                    placeholder="Analyse détaillée de la posture du patient..."
                >{{ old('postural_analysis') }}</textarea>
            </div>
            
            <div>
                <label for="postural_recommendations" class="block text-sm font-medium text-gray-700 mb-2">
                    Recommandations Posturales
                </label>
                <textarea 
                    id="postural_recommendations" 
                    name="postural_recommendations" 
                    rows="6"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                    placeholder="Recommandations pour améliorer la posture..."
                >{{ old('postural_recommendations') }}</textarea>
            </div>
        </div>
    </div>
</div>

<script>
// POSTURAL ASSESSMENT MODULE INTEGRATION
document.addEventListener('DOMContentLoaded', function() {
    // Initialize postural assessment component
    initializePosturalAssessment();
});

// Initialize postural assessment component
function initializePosturalAssessment() {
    console.log('Initializing postural assessment component...');
    
    // Postural assessment state
    const posturalState = {
        currentView: 'anterior',
        currentTool: 'marker',
        selectedColor: '#ff0000',
        markers: [],
        angles: [],
        anglePoints: [],
        showPlumbLine: false
    };

    // Get DOM elements
    const viewSelector = document.getElementById('postural-view-selector');
    const markerTool = document.getElementById('postural-marker-tool');
    const angleTool = document.getElementById('postural-angle-tool');
    const plumbTool = document.getElementById('postural-plumb-tool');
    const clearBtn = document.getElementById('postural-clear-btn');
    const exportBtn = document.getElementById('postural-export-btn');
    const colorPalette = document.getElementById('postural-color-palette');
    const svgContent = document.getElementById('postural-svg-content');
    const annotationsSvg = document.getElementById('postural-annotations');

    // View selector event
    viewSelector.addEventListener('change', function(e) {
        posturalState.currentView = e.target.value;
        loadPosturalView(posturalState.currentView);
        updatePosturalSummary();
    });

    // Tool selection events
    markerTool.addEventListener('click', () => selectTool('marker'));
    angleTool.addEventListener('click', () => selectTool('angle'));
    plumbTool.addEventListener('click', () => selectTool('plumb'));

    // Color palette events
    colorPalette.querySelectorAll('button').forEach(button => {
        button.addEventListener('click', function() {
            posturalState.selectedColor = button.dataset.color;
            updateColorPalette();
        });
    });

    // Tool selection function
    function selectTool(tool) {
        posturalState.currentTool = tool;
        
        // Update tool buttons
        [markerTool, angleTool, plumbTool].forEach(btn => {
            btn.classList.remove('bg-blue-500', 'text-white');
            btn.classList.add('bg-gray-200', 'text-gray-700');
        });
        
        event.target.classList.remove('bg-gray-200', 'text-gray-700');
        event.target.classList.add('bg-blue-500', 'text-white');
        
        // Show/hide color palette
        if (tool === 'marker') {
            colorPalette.style.display = 'flex';
        } else {
            colorPalette.style.display = 'none';
        }
        
        // Toggle plumb line
        if (tool === 'plumb') {
            posturalState.showPlumbLine = !posturalState.showPlumbLine;
            updatePosturalSummary();
        }
    }

    // Clear button event
    clearBtn.addEventListener('click', function() {
        posturalState.markers = [];
        posturalState.angles = [];
        posturalState.anglePoints = [];
        updatePosturalSummary();
        updatePosturalDisplay();
    });

    // Export button event
    exportBtn.addEventListener('click', function() {
        exportPosturalData();
    });

    // SVG click event
    svgContent.addEventListener('click', function(e) {
        const rect = svgContent.getBoundingClientRect();
        const x = e.clientX - rect.left;
        const y = e.clientY - rect.top;
        
        if (posturalState.currentTool === 'marker') {
            addMarker(x, y);
        } else if (posturalState.currentTool === 'angle') {
            addAnglePoint(x, y);
        }
    });

    // Add marker function
    function addMarker(x, y) {
        posturalState.markers.push({
            x: x,
            y: y,
            color: posturalState.selectedColor,
            timestamp: new Date().toISOString()
        });
        updatePosturalSummary();
        updatePosturalDisplay();
    }

    // Add angle point function
    function addAnglePoint(x, y) {
        posturalState.anglePoints.push({ x, y });
        
        if (posturalState.anglePoints.length === 3) {
            calculateAngle();
            posturalState.anglePoints = [];
        }
        
        updatePosturalSummary();
        updatePosturalDisplay();
    }

    // Calculate angle function
    function calculateAngle() {
        const [p1, p2, p3] = posturalState.anglePoints;
        
        // Calculate vectors
        const v1 = { x: p1.x - p2.x, y: p1.y - p2.y };
        const v2 = { x: p3.x - p2.x, y: p3.y - p2.y };
        
        // Calculate angle
        const dot = v1.x * v2.x + v1.y * v2.y;
        const mag1 = Math.sqrt(v1.x * v1.x + v1.y * v1.y);
        const mag2 = Math.sqrt(v2.x * v2.x + v2.y * v2.y);
        const angle = Math.acos(dot / (mag1 * mag2)) * (180 / Math.PI);
        
        posturalState.angles.push({
            points: [...posturalState.anglePoints],
            angle: angle,
            timestamp: new Date().toISOString()
        });
        
        updatePosturalSummary();
        updatePosturalDisplay();
    }

    // Update display function
    function updatePosturalDisplay() {
        // Clear existing annotations
        annotationsSvg.innerHTML = '';
        
        // Draw markers
        posturalState.markers.forEach((marker, index) => {
            const circle = document.createElementNS('http://www.w3.org/2000/svg', 'circle');
            circle.setAttribute('cx', marker.x);
            circle.setAttribute('cy', marker.y);
            circle.setAttribute('r', '8');
            circle.setAttribute('fill', marker.color);
            circle.setAttribute('stroke', '#000');
            circle.setAttribute('stroke-width', '2');
            circle.setAttribute('data-index', index);
            circle.addEventListener('click', () => removeMarker(index));
            annotationsSvg.appendChild(circle);
        });
        
        // Draw angles
        posturalState.angles.forEach((angle, index) => {
            // Draw lines connecting points
            for (let i = 0; i < angle.points.length - 1; i++) {
                const line = document.createElementNS('http://www.w3.org/2000/svg', 'line');
                line.setAttribute('x1', angle.points[i].x);
                line.setAttribute('y1', angle.points[i].y);
                line.setAttribute('x2', angle.points[i + 1].x);
                line.setAttribute('y2', angle.points[i + 1].y);
                line.setAttribute('stroke', '#00ff00');
                line.setAttribute('stroke-width', '2');
                annotationsSvg.appendChild(line);
            }
            
            // Draw angle text
            const text = document.createElementNS('http://www.w3.org/2000/svg', 'text');
            text.setAttribute('x', angle.points[1].x);
            text.setAttribute('y', angle.points[1].y - 10);
            text.setAttribute('text-anchor', 'middle');
            text.setAttribute('fill', '#00ff00');
            text.setAttribute('font-size', '12');
            text.textContent = `${angle.angle.toFixed(1)}°`;
            annotationsSvg.appendChild(text);
        });
        
        // Draw plumb line
        if (posturalState.showPlumbLine) {
            const plumbLine = document.createElementNS('http://www.w3.org/2000/svg', 'line');
            plumbLine.setAttribute('x1', '300');
            plumbLine.setAttribute('y1', '0');
            plumbLine.setAttribute('x2', '300');
            plumbLine.setAttribute('y2', '800');
            plumbLine.setAttribute('stroke', '#0000ff');
            plumbLine.setAttribute('stroke-width', '2');
            plumbLine.setAttribute('stroke-dasharray', '5,5');
            annotationsSvg.appendChild(plumbLine);
        }
    }

    // Update summary function
    function updatePosturalSummary() {
        const markersCount = document.getElementById('postural-markers-count');
        const anglesCount = document.getElementById('postural-angles-count');
        const currentView = document.getElementById('postural-current-view');
        const status = document.getElementById('postural-status');
        
        if (markersCount) markersCount.textContent = posturalState.markers.length;
        if (anglesCount) anglesCount.textContent = posturalState.angles.length;
        if (currentView) currentView.textContent = posturalState.currentView === 'anterior' ? 'Antérieure' : posturalState.currentView === 'posterior' ? 'Postérieure' : 'Latérale';
        if (status) status.textContent = posturalState.markers.length > 0 || posturalState.angles.length > 0 ? 'Complété' : 'En cours';
        
        // Update markers list
        const markersList = document.getElementById('postural-markers-list');
        if (markersList) {
            markersList.innerHTML = '';
            posturalState.markers.forEach((marker, index) => {
                const div = document.createElement('div');
                div.className = 'flex items-center justify-between p-2 bg-gray-100 rounded text-xs';
                div.innerHTML = `
                    <span>Marqueur ${index + 1}</span>
                    <button onclick="removeMarker(${index})" class="text-red-500 hover:text-red-700">×</button>
                `;
                markersList.appendChild(div);
            });
        }
        
        // Update angles list
        const anglesList = document.getElementById('postural-angles-list');
        if (anglesList) {
            anglesList.innerHTML = '';
            posturalState.angles.forEach((angle, index) => {
                const div = document.createElement('div');
                div.className = 'flex items-center justify-between p-2 bg-gray-100 rounded text-xs';
                div.innerHTML = `
                    <span>Angle ${index + 1}: ${angle.angle.toFixed(1)}°</span>
                    <button onclick="removeAngle(${index})" class="text-red-500 hover:text-red-700">×</button>
                `;
                anglesList.appendChild(div);
            });
        }
        
        // Update export data
        const exportData = document.getElementById('postural-export-data');
        if (exportData) {
            exportData.value = JSON.stringify({
                view: posturalState.currentView,
                markers: posturalState.markers,
                angles: posturalState.angles,
                plumbLine: posturalState.showPlumbLine
            }, null, 2);
        }
        
        // Update hidden field for form submission
        const hiddenField = document.getElementById('postural_assessment_data');
        if (hiddenField) {
            hiddenField.value = JSON.stringify({
                view: posturalState.currentView,
                markers: posturalState.markers,
                angles: posturalState.angles,
                plumbLine: posturalState.showPlumbLine
            });
        }
    }

    // Update color palette function
    function updateColorPalette() {
        colorPalette.querySelectorAll('button').forEach(button => {
            if (button.dataset.color === posturalState.selectedColor) {
                button.classList.add('border-gray-800');
                button.classList.remove('border-gray-300');
            } else {
                button.classList.remove('border-gray-800');
                button.classList.add('border-gray-300');
            }
        });
    }

    // Export data function
    function exportPosturalData() {
        const data = {
            view: posturalState.currentView,
            markers: posturalState.markers,
            angles: posturalState.angles,
            plumbLine: posturalState.showPlumbLine
        };
        
        const blob = new Blob([JSON.stringify(data, null, 2)], { type: 'application/json' });
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = 'postural-assessment-data.json';
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        URL.revokeObjectURL(url);
    }

    // Remove marker function
    window.removeMarker = function(index) {
        posturalState.markers.splice(index, 1);
        updatePosturalSummary();
        updatePosturalDisplay();
    };

    // Remove angle function
    window.removeAngle = function(index) {
        posturalState.angles.splice(index, 1);
        updatePosturalSummary();
        updatePosturalDisplay();
    };

    // Load postural view function
    function loadPosturalView(view) {
        const content = {
            anterior: `<svg viewBox="0 0 600 800" xmlns="http://www.w3.org/2000/svg">
                <!-- Anterior view content -->
                <rect width="600" height="800" fill="#f0f0f0"/>
                <text x="300" y="50" text-anchor="middle" font-size="20" fill="#333">Vue Antérieure</text>
                <!-- Add more SVG content for anterior view -->
            </svg>`,
            posterior: `<svg viewBox="0 0 600 800" xmlns="http://www.w3.org/2000/svg">
                <!-- Posterior view content -->
                <rect width="600" height="800" fill="#f0f0f0"/>
                <text x="300" y="50" text-anchor="middle" font-size="20" fill="#333">Vue Postérieure</text>
                <!-- Add more SVG content for posterior view -->
            </svg>`,
            lateral: `<svg viewBox="0 0 600 800" xmlns="http://www.w3.org/2000/svg">
                <!-- Lateral view content -->
                <rect width="600" height="800" fill="#f0f0f0"/>
                <text x="300" y="50" text-anchor="middle" font-size="20" fill="#333">Vue Latérale</text>
                <!-- Add more SVG content for lateral view -->
            </svg>`
        };
        
        svgContent.innerHTML = content[posturalState.currentView] || '';
        updatePosturalDisplay();
    }

    // Initialize the component
    loadPosturalView('anterior');
    updatePosturalSummary();
}
</script> 