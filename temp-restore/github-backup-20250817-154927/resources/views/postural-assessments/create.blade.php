@extends('layouts.app')

@section('title', 'Nouvelle Évaluation Posturale - Med Predictor')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">📊 Nouvelle Évaluation Posturale</h1>
                    <p class="text-gray-600 mt-2">Analyse interactive de la posture du joueur avec images 3D professionnelles</p>
                </div>
                <a href="{{ route('postural-assessments.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg">
                    ← Retour
                </a>
            </div>
        </div>



        <!-- Form -->
        <form action="{{ route('postural-assessments.store') }}" method="POST" id="posturalAssessmentForm">
            @csrf
            
            <!-- Player Selection -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <h2 class="text-xl font-semibold mb-4">👤 Sélection du Joueur</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="player_id" class="block text-sm font-medium text-gray-700 mb-2">
                            Joueur *
                        </label>
                        <select name="player_id" id="player_id" required class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Sélectionner un joueur</option>
                            @foreach($players as $player)
                                <option value="{{ $player->id }}" {{ old('player_id') == $player->id ? 'selected' : '' }}>
                                    {{ $player->name }} ({{ $player->position }})
                                </option>
                            @endforeach
                        </select>
                        @error('player_id')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="assessment_type" class="block text-sm font-medium text-gray-700 mb-2">
                            Type d'Évaluation *
                        </label>
                        <select name="assessment_type" id="assessment_type" required class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Sélectionner le type</option>
                            <option value="routine" {{ old('assessment_type') == 'routine' ? 'selected' : '' }}>Routine</option>
                            <option value="injury" {{ old('assessment_type') == 'injury' ? 'selected' : '' }}>Blessure</option>
                            <option value="follow_up" {{ old('assessment_type') == 'follow_up' ? 'selected' : '' }}>Suivi</option>
                        </select>
                        @error('assessment_type')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                
                <div class="mt-4">
                    <label for="assessment_date" class="block text-sm font-medium text-gray-700 mb-2">
                        Date d'Évaluation *
                    </label>
                    <input type="datetime-local" name="assessment_date" id="assessment_date" 
                           value="{{ old('assessment_date', now()->format('Y-m-d\TH:i')) }}" required
                           class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @error('assessment_date')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Interactive Chart -->
            <div class="bg-white rounded-lg shadow-md mb-6">
                <div class="p-6 border-b border-gray-200">
                    <h2 class="text-xl font-semibold">🎯 Analyse Posturale Interactive avec Images 3D</h2>
                    <p class="text-gray-600 mt-2">Utilisez les outils ci-dessous pour analyser la posture du joueur avec les illustrations anatomiques professionnelles</p>
                </div>
                
                <!-- Vue Component Container -->
                <div id="postural-chart-app"></div>
            </div>

            <!-- Hidden Fields for Form Data -->
            <input type="hidden" name="view" id="view" value="anterior">
            <input type="hidden" name="annotations" id="annotations" value="">
            <input type="hidden" name="markers" id="markers" value="">
            <input type="hidden" name="angles" id="angles" value="">

            <!-- Clinical Notes -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <h2 class="text-xl font-semibold mb-4">📝 Notes Cliniques</h2>
                <div class="space-y-4">
                    <div>
                        <label for="observations" class="block text-sm font-medium text-gray-700 mb-2">
                            Observations *
                        </label>
                        <textarea name="observations" id="observations" rows="4" required
                                  class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                  placeholder="Décrivez vos observations posturales...">{{ old('observations') }}</textarea>
                        @error('observations')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="recommendations" class="block text-sm font-medium text-gray-700 mb-2">
                            Recommandations
                        </label>
                        <textarea name="recommendations" id="recommendations" rows="3"
                                  class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                  placeholder="Recommandations pour améliorer la posture...">{{ old('recommendations') }}</textarea>
                        @error('recommendations')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="flex justify-end">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium">
                    💾 Sauvegarder l'Évaluation
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Vue.js and Component Scripts -->
<script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
<script>
// Initialize Vue app directly on the postural-chart-app container
document.addEventListener('DOMContentLoaded', function() {
    const { createApp } = Vue;
    
    const app = createApp({
        data() {
            return {
                currentView: 'anterior',
                currentTool: 'marker',
                selectedColor: '#ff0000',
                showPlumbLine: false,
                markers: [],
                angles: [],
                anglePoints: [],
                markerColors: ['#ff0000', '#00ff00', '#0000ff', '#ffff00', '#ff00ff', '#00ffff', '#ff8800', '#8800ff']
            }
        },
        mounted() {
            console.log('✅ Vue app mounted with professional 3D postural chart');
        },
        methods: {
            changeView() {
                this.anglePoints = [];
                this.handleViewChanged(this.currentView);
            },
            
            togglePlumbLine() {
                this.showPlumbLine = !this.showPlumbLine;
            },
            
            handleCanvasClick(event) {
                const rect = event.target.getBoundingClientRect();
                const x = event.clientX - rect.left;
                const y = event.clientY - rect.top;
                
                if (this.currentTool === 'marker') {
                    this.addMarker(x, y);
                } else if (this.currentTool === 'angle') {
                    this.addAnglePoint(x, y);
                }
            },
            
            addMarker(x, y) {
                const landmark = this.findClosestLandmark(x, y);
                
                this.markers.push({
                    x: x,
                    y: y,
                    color: this.selectedColor,
                    landmark: landmark,
                    timestamp: new Date().toISOString()
                });
                
                this.handleSessionSaved();
            },
            
            addAnglePoint(x, y) {
                this.anglePoints.push({ x, y });
                
                if (this.anglePoints.length === 3) {
                    const angle = this.calculateAngle(this.anglePoints);
                    this.angles.push({
                        points: [...this.anglePoints],
                        value: angle,
                        label: `Angle ${this.angles.length + 1}`,
                        timestamp: new Date().toISOString()
                    });
                    
                    this.anglePoints = [];
                    this.handleSessionSaved();
                }
            },
            
            findClosestLandmark(x, y) {
                const landmarks = [
                    { name: 'Tête', x: 300, y: 50 },
                    { name: 'Épaules', x: 300, y: 120 },
                    { name: 'Coudes', x: 250, y: 200 },
                    { name: 'Poignets', x: 200, y: 280 },
                    { name: 'Hanche', x: 300, y: 350 },
                    { name: 'Genoux', x: 300, y: 450 },
                    { name: 'Chevilles', x: 300, y: 550 }
                ];
                
                let closest = landmarks[0];
                let minDistance = this.calculateDistance(x, y, closest.x, closest.y);
                
                for (const landmark of landmarks) {
                    const distance = this.calculateDistance(x, y, landmark.x, landmark.y);
                    if (distance < minDistance) {
                        minDistance = distance;
                        closest = landmark;
                    }
                }
                
                return closest.name;
            },
            
            calculateDistance(x1, y1, x2, y2) {
                return Math.sqrt(Math.pow(x2 - x1, 2) + Math.pow(y2 - y1, 2));
            },
            
            calculateAngle(points) {
                if (points.length !== 3) return 0;
                
                const [p1, p2, p3] = points;
                const angle1 = Math.atan2(p1.y - p2.y, p1.x - p2.x);
                const angle2 = Math.atan2(p3.y - p2.y, p3.x - p2.x);
                
                let angle = (angle2 - angle1) * 180 / Math.PI;
                if (angle < 0) angle += 360;
                
                return Math.round(angle);
            },
            
            clearAnnotations() {
                this.markers = [];
                this.angles = [];
                this.anglePoints = [];
            },
            
            removeMarker(index) {
                this.markers.splice(index, 1);
            },
            
            removeAngle(index) {
                this.angles.splice(index, 1);
            },
            
            exportData() {
                const data = {
                    view: this.currentView,
                    markers: this.markers,
                    angles: this.angles,
                    timestamp: new Date().toISOString()
                };
                
                const blob = new Blob([JSON.stringify(data, null, 2)], { type: 'application/json' });
                const url = URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = `postural-assessment-${new Date().toISOString().split('T')[0]}.json`;
                a.click();
                URL.revokeObjectURL(url);
            },
            
            handleSessionSaved(data) {
                document.getElementById('view').value = this.currentView;
                document.getElementById('annotations').value = JSON.stringify([]);
                document.getElementById('markers').value = JSON.stringify(this.markers);
                document.getElementById('angles').value = JSON.stringify(this.angles);
                this.showNotification('Session sauvegardée avec succès', 'success');
            },
            
            handleViewChanged(view) {
                document.getElementById('view').value = view;
            },
            
            showNotification(message, type = 'info') {
                const notification = document.createElement('div');
                notification.className = `fixed top-4 right-4 px-6 py-3 rounded-lg text-white z-50 ${
                    type === 'success' ? 'bg-green-500' : 'bg-blue-500'
                }`;
                notification.textContent = message;
                document.body.appendChild(notification);
                
                setTimeout(() => {
                    notification.remove();
                }, 3000);
            }
        },
        template: `
            <div class="postural-chart-container">
                <!-- Toolbar -->
                <div class="toolbar bg-white border-b border-gray-200 p-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-4">
                            <!-- View Selector -->
                            <div class="flex items-center space-x-2">
                                <label class="text-sm font-medium text-gray-700">Vue :</label>
                                <select v-model="currentView" @change="changeView" class="border border-gray-300 rounded px-2 py-1 text-sm">
                                    <option value="anterior">Antérieure (3D)</option>
                                    <option value="posterior">Postérieure (3D)</option>
                                    <option value="lateral">Latérale (3D)</option>
                                </select>
                            </div>
                            
                            <!-- Tool Selector -->
                            <div class="flex items-center space-x-2">
                                <label class="text-sm font-medium text-gray-700">Outil :</label>
                                <div class="flex space-x-1">
                                    <button 
                                        @click="currentTool = 'marker'" 
                                        :class="['px-3 py-1 rounded text-sm', currentTool === 'marker' ? 'bg-blue-500 text-white' : 'bg-gray-200 text-gray-700']"
                                        title="Marqueur"
                                    >
                                        🎯
                                    </button>
                                    <button 
                                        @click="currentTool = 'angle'" 
                                        :class="['px-3 py-1 rounded text-sm', currentTool === 'angle' ? 'bg-blue-500 text-white' : 'bg-gray-200 text-gray-700']"
                                        title="Mesure d'angle"
                                    >
                                        📐
                                    </button>
                                    <button 
                                        @click="togglePlumbLine" 
                                        :class="['px-3 py-1 rounded text-sm', showPlumbLine ? 'bg-blue-500 text-white' : 'bg-gray-200 text-gray-700']"
                                        title="Fil à plomb"
                                    >
                                        📏
                                    </button>
                                </div>
                            </div>
                            
                            <!-- Color Palette -->
                            <div class="flex items-center space-x-2" v-if="currentTool === 'marker'">
                                <label class="text-sm font-medium text-gray-700">Couleur :</label>
                                <div class="flex space-x-1">
                                    <button 
                                        v-for="color in markerColors" 
                                        :key="color"
                                        @click="selectedColor = color"
                                        :class="['w-6 h-6 rounded border-2', selectedColor === color ? 'border-gray-800' : 'border-gray-300']"
                                        :style="{ backgroundColor: color }"
                                        :title="color"
                                    ></button>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Actions -->
                        <div class="flex items-center space-x-2">
                            <button @click="clearAnnotations" class="px-3 py-1 bg-red-500 text-white rounded text-sm">
                                🗑️ Effacer
                            </button>
                            <button @click="exportData" class="px-3 py-1 bg-green-500 text-white rounded text-sm">
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
                            <img v-if="currentView === 'anterior'" src="/images/postural/anterior-view.svg" @click="handleCanvasClick" class="cursor-crosshair w-full h-full object-contain" alt="Vue Antérieure - Anatomie 3D Professionnelle">
                            <img v-else-if="currentView === 'posterior'" src="/images/postural/posterior-view.svg" @click="handleCanvasClick" class="cursor-crosshair w-full h-full object-contain" alt="Vue Postérieure - Anatomie 3D Professionnelle">
                            <img v-else-if="currentView === 'lateral'" src="/images/postural/lateral-view.svg" @click="handleCanvasClick" class="cursor-crosshair w-full h-full object-contain" alt="Vue Latérale - Anatomie 3D Professionnelle">
                            <div v-else class="bg-gray-200 w-full h-full flex items-center justify-center">
                                <p class="text-gray-500">Sélectionnez une vue</p>
                            </div>
                            
                            <!-- Annotations Layer -->
                            <svg class="absolute top-0 left-0 w-full h-full pointer-events-none" style="width: 600px; height: 800px; max-width: 100%; max-height: 80vh;">
                                <!-- Markers -->
                                <circle 
                                    v-for="(marker, index) in markers" 
                                    :key="'marker-' + index"
                                    :cx="marker.x" 
                                    :cy="marker.y" 
                                    r="8" 
                                    :fill="marker.color" 
                                    stroke="#000" 
                                    stroke-width="2"
                                    opacity="0.8"
                                />
                                
                                <!-- Angle Measurements -->
                                <g v-for="(angle, index) in angles" :key="'angle-item-' + index">
                                    <!-- Lines connecting points -->
                                    <line 
                                        :x1="angle.points[0].x" 
                                        :y1="angle.points[0].y" 
                                        :x2="angle.points[1].x" 
                                        :y2="angle.points[1].y" 
                                        stroke="#ff6b6b" 
                                        stroke-width="2"
                                    />
                                    <line 
                                        :x1="angle.points[1].x" 
                                        :y1="angle.points[1].y" 
                                        :x2="angle.points[2].x" 
                                        :y2="angle.points[2].y" 
                                        stroke="#ff6b6b" 
                                        stroke-width="2"
                                    />
                                    
                                    <!-- Angle value text -->
                                    <text 
                                        :x="angle.points[1].x + 10" 
                                        :y="angle.points[1].y - 10" 
                                        fill="#ff6b6b" 
                                        font-size="12" 
                                        font-weight="bold"
                                    >
                                        @{{ angle.value }}°
                                    </text>
                                </g>
                                
                                <!-- Current angle measurement -->
                                <g v-if="anglePoints.length > 0">
                                    <line 
                                        v-for="(point, index) in anglePoints.slice(0, -1)" 
                                        :key="'temp-line-item-' + index"
                                        :x1="point.x" 
                                        :y1="point.y" 
                                        :x2="anglePoints[index + 1].x" 
                                        :y2="anglePoints[index + 1].y" 
                                        stroke="#ff6b6b" 
                                        stroke-width="2" 
                                        stroke-dasharray="5,5"
                                    />
                                </g>
                                
                                <!-- Plumb line -->
                                <line 
                                    v-if="showPlumbLine" 
                                    x1="300" y1="0" x2="300" y2="800" 
                                    stroke="#4a90e2" 
                                    stroke-width="2" 
                                    stroke-dasharray="10,5"
                                />
                            </svg>
                        </div>
                        
                        <!-- Sidebar -->
                        <div class="lg:ml-6 w-full lg:w-80">
                            <!-- Markers List -->
                            <div v-if="markers.length > 0" class="mb-4">
                                <h4 class="font-medium text-sm text-gray-700 mb-2">Marqueurs (@{{ markers.length }})</h4>
                                <div class="space-y-2">
                                    <div 
                                        v-for="(marker, index) in markers" 
                                        :key="'marker-list-' + index"
                                        class="flex items-center justify-between p-2 bg-gray-50 rounded"
                                    >
                                        <div class="flex items-center space-x-2">
                                            <div class="w-4 h-4 rounded" :style="{ backgroundColor: marker.color }"></div>
                                            <span class="text-sm">@{{ marker.landmark || 'Point' }}</span>
                                        </div>
                                        <button @click="removeMarker(index)" class="text-red-500 text-sm">×</button>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Angles List -->
                            <div v-if="angles.length > 0" class="mb-4">
                                <h4 class="font-medium text-sm text-gray-700 mb-2">Angles (@{{ angles.length }})</h4>
                                <div class="space-y-2">
                                    <div 
                                        v-for="(angle, index) in angles" 
                                        :key="'angle-list-item-' + index"
                                        class="flex items-center justify-between p-2 bg-gray-50 rounded"
                                    >
                                        <span class="text-sm">@{{ angle.label || 'Angle' }}: @{{ angle.value }}°</span>
                                        <button @click="removeAngle(index)" class="text-red-500 text-sm">×</button>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Current Tool Info -->
                            <div class="mt-4 p-3 bg-blue-50 rounded">
                                <h4 class="font-medium text-sm text-blue-800 mb-2">Outil actuel</h4>
                                <p class="text-sm text-blue-700">
                                    <span v-if="currentTool === 'marker'">
                                        🎯 Marqueur - Cliquez sur un point de repère pour ajouter un marqueur
                                    </span>
                                    <span v-else-if="currentTool === 'angle'">
                                        📐 Angle - Cliquez sur 3 points pour mesurer un angle
                                    </span>
                                    <span v-else>
                                        Sélectionnez un outil pour commencer
                                    </span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `
    });
    
    app.mount('#postural-chart-app');
});
</script>

<style>
/* Additional styles for the postural assessment */
.postural-chart-container {
    min-height: 600px;
}

.toolbar {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.chart-wrapper {
    background: #f8fafc;
}

.notes-panel {
    max-height: 300px;
    overflow-y: auto;
}
</style>
@endsection 