<template>
  <div class="postural-chart-container">
    <!-- Toolbar -->
    <div class="toolbar bg-white border-b border-gray-200 p-4">
      <div class="flex items-center justify-between">
        <div class="flex items-center space-x-4">
          <!-- View Selector -->
          <div class="flex items-center space-x-2">
            <label class="text-sm font-medium text-gray-700">Vue :</label>
            <select v-model="currentView" @change="changeView" class="border border-gray-300 rounded px-2 py-1 text-sm">
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
          <div v-html="currentSvgContent" @click="handleCanvasClick" class="cursor-crosshair w-full h-full"></div>
          
          <!-- Annotations Layer -->
          <svg class="absolute top-0 left-0 w-full h-full pointer-events-none" style="width: 600px; height: 800px; max-width: 100%; max-height: 80vh;">
            <!-- Markers -->
            <circle 
              v-for="(marker, index) in markers" 
              :key="`marker-${index}`"
              :cx="marker.x" 
              :cy="marker.y" 
              r="8" 
              :fill="marker.color" 
              stroke="#000" 
              stroke-width="2"
              opacity="0.8"
            />
            
            <!-- Angle Measurements -->
            <g v-for="(angle, index) in angles" :key="`angle-${index}`">
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
                :x="angle.points[1].x" 
                :y="angle.points[1].y - 10" 
                text-anchor="middle" 
                font-size="12" 
                fill="#ff6b6b" 
                font-weight="bold"
              >
                {{ angle.value }}°
              </text>
            </g>
            
            <!-- Plumb Line -->
            <line 
              v-if="showPlumbLine" 
              x1="300" 
              y1="0" 
              x2="300" 
              y2="800" 
              stroke="#6c757d" 
              stroke-width="2" 
              stroke-dasharray="5,5"
            />
          </svg>
        </div>
        
        <!-- Sidebar -->
        <div class="sidebar mt-4 lg:mt-0 lg:ml-4 w-full lg:w-64">
          <div class="bg-white rounded-lg shadow p-4">
            <h3 class="font-semibold text-lg mb-4">Annotations</h3>
            
            <!-- Markers List -->
            <div v-if="markers.length > 0" class="mb-4">
              <h4 class="font-medium text-sm text-gray-700 mb-2">Marqueurs ({{ markers.length }})</h4>
              <div class="space-y-2">
                <div 
                  v-for="(marker, index) in markers" 
                  :key="`marker-list-${index}`"
                  class="flex items-center justify-between p-2 bg-gray-50 rounded"
                >
                  <div class="flex items-center space-x-2">
                    <div class="w-4 h-4 rounded" :style="{ backgroundColor: marker.color }"></div>
                    <span class="text-sm">{{ marker.landmark || 'Point' }}</span>
                  </div>
                  <button @click="removeMarker(index)" class="text-red-500 text-sm">×</button>
                </div>
              </div>
            </div>
            
            <!-- Angles List -->
            <div v-if="angles.length > 0" class="mb-4">
              <h4 class="font-medium text-sm text-gray-700 mb-2">Angles ({{ angles.length }})</h4>
              <div class="space-y-2">
                <div 
                  v-for="(angle, index) in angles" 
                  :key="`angle-list-${index}`"
                  class="flex items-center justify-between p-2 bg-gray-50 rounded"
                >
                  <span class="text-sm">{{ angle.label || 'Angle' }}: {{ angle.value }}°</span>
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
  </div>
</template>

<script>
export default {
  name: 'InteractivePosturalChart',
  props: {
    initialData: {
      type: Object,
      default: () => ({
        view: 'anterior',
        markers: [],
        angles: [],
        notes: []
      })
    }
  },
  data() {
    return {
      currentView: 'anterior',
      currentTool: 'marker',
      selectedColor: '#ff0000',
      showPlumbLine: false,
      markers: [],
      angles: [],
      anglePoints: [],
      markerColors: ['#ff0000', '#00ff00', '#0000ff', '#ffff00', '#ff00ff', '#00ffff', '#ff8800', '#8800ff'],
      svgContents: {
        anterior: '',
        posterior: '',
        lateral: ''
      }
    }
  },
  computed: {
    currentSvgContent() {
      return this.svgContents[this.currentView] || '';
    }
  },
  async mounted() {
    // Load SVG contents
    await this.loadSvgContents();
    
    // Initialize with props data
    if (this.initialData) {
      this.currentView = this.initialData.view || 'anterior';
      this.markers = this.initialData.markers || [];
      this.angles = this.initialData.angles || [];
    }
  },
  methods: {
    async loadSvgContents() {
      try {
        const anteriorResponse = await fetch('/images/postural/anterior-view.svg');
        const posteriorResponse = await fetch('/images/postural/posterior-view.svg');
        const lateralResponse = await fetch('/images/postural/lateral-view.svg');
        
        this.svgContents.anterior = await anteriorResponse.text();
        this.svgContents.posterior = await posteriorResponse.text();
        this.svgContents.lateral = await lateralResponse.text();
      } catch (error) {
        console.error('Error loading SVG contents:', error);
        // Fallback to basic shapes if SVG loading fails
        this.svgContents = {
          anterior: '<div class="bg-blue-100 p-4 rounded">Vue Antérieure</div>',
          posterior: '<div class="bg-blue-100 p-4 rounded">Vue Postérieure</div>',
          lateral: '<div class="bg-blue-100 p-4 rounded">Vue Latérale</div>'
        };
      }
    },
    
    changeView() {
      this.anglePoints = []; // Reset angle measurement when changing view
      this.$emit('view-changed', this.currentView);
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
      // Find closest landmark
      const landmark = this.findClosestLandmark(x, y);
      
      this.markers.push({
        x: x,
        y: y,
        color: this.selectedColor,
        landmark: landmark,
        timestamp: new Date().toISOString()
      });
      
      this.$emit('marker-added', this.markers[this.markers.length - 1]);
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
        
        this.anglePoints = []; // Reset for next measurement
        this.$emit('angle-added', this.angles[this.angles.length - 1]);
      }
    },
    
    findClosestLandmark(x, y) {
      const landmarks = [
        { id: 'head', x: 200, y: 60, name: 'Tête' },
        { id: 'left-shoulder', x: 150, y: 125, name: 'Épaule Gauche' },
        { id: 'right-shoulder', x: 250, y: 125, name: 'Épaule Droite' },
        { id: 'left-elbow', x: 135, y: 165, name: 'Coude Gauche' },
        { id: 'right-elbow', x: 265, y: 165, name: 'Coude Droite' },
        { id: 'left-hip', x: 170, y: 235, name: 'Hanche Gauche' },
        { id: 'right-hip', x: 230, y: 235, name: 'Hanche Droite' },
        { id: 'left-knee', x: 182, y: 295, name: 'Genou Gauche' },
        { id: 'right-knee', x: 218, y: 295, name: 'Genou Droite' },
        { id: 'left-ankle', x: 182, y: 355, name: 'Cheville Gauche' },
        { id: 'right-ankle', x: 218, y: 355, name: 'Cheville Droite' }
      ];
      
      let closest = landmarks[0];
      let minDistance = this.calculateDistance(x, y, closest.x, closest.y);
      
      landmarks.forEach(landmark => {
        const distance = this.calculateDistance(x, y, landmark.x, landmark.y);
        if (distance < minDistance) {
          minDistance = distance;
          closest = landmark;
        }
      });
      
      return closest.name;
    },
    
    calculateDistance(x1, y1, x2, y2) {
      return Math.sqrt(Math.pow(x2 - x1, 2) + Math.pow(y2 - y1, 2));
    },
    
    calculateAngle(points) {
      const [p1, p2, p3] = points;
      
      const a = Math.sqrt(Math.pow(p2.x - p1.x, 2) + Math.pow(p2.y - p1.y, 2));
      const b = Math.sqrt(Math.pow(p3.x - p2.x, 2) + Math.pow(p3.y - p2.y, 2));
      const c = Math.sqrt(Math.pow(p3.x - p1.x, 2) + Math.pow(p3.y - p1.y, 2));
      
      const angle = Math.acos((a * a + b * b - c * c) / (2 * a * b));
      return Math.round(angle * 180 / Math.PI);
    },
    
    removeMarker(index) {
      this.markers.splice(index, 1);
      this.$emit('marker-removed', index);
    },
    
    removeAngle(index) {
      this.angles.splice(index, 1);
      this.$emit('angle-removed', index);
    },
    
    clearAnnotations() {
      this.markers = [];
      this.angles = [];
      this.anglePoints = [];
      this.$emit('annotations-cleared');
    },
    
    exportData() {
      const data = {
        view: this.currentView,
        markers: this.markers,
        angles: this.angles,
        timestamp: new Date().toISOString()
      };
      
      this.$emit('data-exported', data);
      
      // Create download link
      const blob = new Blob([JSON.stringify(data, null, 2)], { type: 'application/json' });
      const url = URL.createObjectURL(blob);
      const a = document.createElement('a');
      a.href = url;
      a.download = `postural-assessment-${new Date().toISOString().split('T')[0]}.json`;
      a.click();
      URL.revokeObjectURL(url);
    },
    
    getData() {
      return {
        view: this.currentView,
        markers: this.markers,
        angles: this.angles,
        showPlumbLine: this.showPlumbLine
      };
    }
  }
}
</script>

<style scoped>
.postural-chart-container {
  border: 1px solid #e5e7eb;
  border-radius: 8px;
  overflow: hidden;
}

.toolbar {
  border-bottom: 1px solid #e5e7eb;
}

.svg-container {
  border: 1px solid #d1d5db;
  border-radius: 4px;
  background: white;
}

.sidebar {
  min-height: 600px;
}

/* SVG interaction styles */
.svg-container svg {
  pointer-events: all;
}

.svg-container svg circle[id^="landmark-"]:hover {
  opacity: 1 !important;
  stroke-width: 3 !important;
}
</style> 