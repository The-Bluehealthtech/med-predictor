<template>
  <div class="dental-chart-container">
    <div class="chart-header">
      <h3 class="text-lg font-semibold text-gray-800 mb-2">
        🦷 Schéma Dentaire Interactif (ICD-10: K00-K14)
      </h3>
      <div class="flex items-center space-x-4 text-sm">
        <div class="flex items-center space-x-2">
          <div class="w-4 h-4 bg-white border border-gray-300 rounded"></div>
          <span>Santé</span>
        </div>
        <div class="flex items-center space-x-2">
          <div class="w-4 h-4 bg-red-500 rounded"></div>
          <span>Carie</span>
        </div>
        <div class="flex items-center space-x-2">
          <div class="w-4 h-4 bg-blue-500 rounded"></div>
          <span>Restauration</span>
        </div>
        <div class="flex items-center space-x-2">
          <div class="w-4 h-4 bg-gray-500 opacity-50 rounded"></div>
          <span>Manquante</span>
        </div>
      </div>
    </div>

    <div class="chart-wrapper" ref="chartContainer">
      <div 
        class="dental-svg-container" 
        @click="handleChartClick"
        @mouseover="handleMouseover"
        @mouseleave="hideTooltip"
        v-html="dentalChartSvg"
      ></div>
    </div>

    <!-- Tooltip -->
    <div 
      v-if="tooltip.visible" 
      :style="tooltipStyle"
      class="dental-tooltip"
    >
      <div class="tooltip-content">
        <div class="font-semibold">Dent {{ tooltip.toothNumber }}</div>
        <div class="text-sm">{{ tooltip.status }}</div>
        <div class="text-xs text-gray-500">{{ tooltip.condition }}</div>
      </div>
    </div>

    <!-- Dental Status Summary -->
    <div class="mt-6 bg-gray-50 rounded-lg p-4">
      <h4 class="text-sm font-semibold text-gray-700 mb-3">Résumé Dentaire</h4>
      <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
        <div class="flex items-center justify-between">
          <span>Dents saines:</span>
          <span class="font-semibold text-green-600">{{ healthyTeethCount }}</span>
        </div>
        <div class="flex items-center justify-between">
          <span>Caries:</span>
          <span class="font-semibold text-red-600">{{ cariesCount }}</span>
        </div>
        <div class="flex items-center justify-between">
          <span>Restaurations:</span>
          <span class="font-semibold text-blue-600">{{ restorationCount }}</span>
        </div>
        <div class="flex items-center justify-between">
          <span>Manquantes:</span>
          <span class="font-semibold text-gray-600">{{ missingTeethCount }}</span>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'InteractiveDentalChart',
  props: {
    patientDentalRecord: {
      type: Object,
      default: () => ({})
    }
  },
  data() {
    return {
      teethStatus: {},
      tooltip: {
        visible: false,
        x: 0,
        y: 0,
        toothNumber: '',
        status: '',
        condition: ''
      },
      dentalChartSvg: this.generateDentalChartSvg()
    }
  },
  computed: {
    tooltipStyle() {
      return {
        position: 'absolute',
        left: this.tooltip.x + 'px',
        top: this.tooltip.y + 'px',
        zIndex: 1000
      }
    },
    healthyTeethCount() {
      return Object.values(this.teethStatus).filter(tooth => 
        tooth.status === 'healthy'
      ).length
    },
    cariesCount() {
      return Object.values(this.teethStatus).filter(tooth => 
        tooth.status === 'caries'
      ).length
    },
    restorationCount() {
      return Object.values(this.teethStatus).filter(tooth => 
        tooth.status === 'restoration'
      ).length
    },
    missingTeethCount() {
      return Object.values(this.teethStatus).filter(tooth => 
        tooth.status === 'missing'
      ).length
    }
  },
  watch: {
    patientDentalRecord: {
      handler(newRecord) {
        this.teethStatus = JSON.parse(JSON.stringify(newRecord))
        this.updateChartStyles()
      },
      immediate: true,
      deep: true
    },
    teethStatus: {
      handler() {
        this.updateChartStyles()
      },
      deep: true
    }
  },
  mounted() {
    this.initializeTeethStatus()
    this.updateChartStyles()
  },
  methods: {
    generateDentalChartSvg() {
      // Generate a simple dental chart SVG
      return `
        <svg width="800" height="400" viewBox="0 0 800 400" xmlns="http://www.w3.org/2000/svg">
          <defs>
            <style>
              .tooth { cursor: pointer; transition: fill 0.2s ease; }
              .tooth-surface { cursor: pointer; transition: fill 0.2s ease; }
              .status-healthy { fill: #ffffff; stroke: #cccccc; }
              .status-caries { fill: #ff4d4d; }
              .status-restoration { fill: #4d94ff; }
              .status-crown { stroke: #4d94ff; stroke-width: 3px; fill-opacity: 0.1; }
              .status-missing { fill: #808080; opacity: 0.5; }
            </style>
          </defs>
          
          <!-- Upper teeth (1-16) -->
          <g id="upper-teeth">
            ${this.generateTeethRow(1, 16, 50, 100, 'upper')}
          </g>
          
          <!-- Lower teeth (17-32) -->
          <g id="lower-teeth">
            ${this.generateTeethRow(17, 32, 50, 300, 'lower')}
          </g>
          
          <!-- Labels -->
          <text x="400" y="30" text-anchor="middle" font-size="16" font-weight="bold">Dentition Supérieure</text>
          <text x="400" y="370" text-anchor="middle" font-size="16" font-weight="bold">Dentition Inférieure</text>
        </svg>
      `
    },
    generateTeethRow(start, end, y, baseY, position) {
      let svg = ''
      const teeth = end - start + 1
      const spacing = 700 / teeth
      
      for (let i = 0; i < teeth; i++) {
        const toothNumber = start + i
        const x = 50 + (i * spacing)
        
        // Main tooth
        svg += `
          <g id="tooth-${toothNumber}" class="tooth">
            <rect 
              id="tooth-${toothNumber}-main"
              x="${x}" y="${baseY}" 
              width="40" height="60" 
              rx="5" 
              class="tooth-surface status-healthy"
              data-tooth="${toothNumber}"
            />
            <text 
              x="${x + 20}" y="${baseY + 35}" 
              text-anchor="middle" 
              font-size="12" 
              font-weight="bold"
            >${toothNumber}</text>
          </g>
        `
        
        // Surfaces
        const surfaces = ['mesial', 'distal', 'occlusal', 'lingual', 'buccal']
        surfaces.forEach((surface, index) => {
          const surfaceX = x + (index * 8)
          const surfaceY = baseY + (index * 12)
          svg += `
            <rect 
              id="tooth-${toothNumber}-${surface}"
              x="${surfaceX}" y="${surfaceY}" 
              width="6" height="8" 
              class="tooth-surface status-healthy"
              data-tooth="${toothNumber}"
              data-surface="${surface}"
            />
          `
        })
      }
      
      return svg
    },
    initializeTeethStatus() {
      // Initialize teeth status if not provided
      if (Object.keys(this.teethStatus).length === 0) {
        for (let i = 1; i <= 32; i++) {
          this.teethStatus[i] = {
            status: 'healthy',
            condition: 'Normal',
            surfaces: {
              mesial: { condition: 'healthy', status: 'healthy' },
              distal: { condition: 'healthy', status: 'healthy' },
              occlusal: { condition: 'healthy', status: 'healthy' },
              lingual: { condition: 'healthy', status: 'healthy' },
              buccal: { condition: 'healthy', status: 'healthy' }
            },
            notes: ''
          }
        }
      }
    },
    handleChartClick(event) {
      const target = event.target
      const toothId = target.getAttribute('data-tooth')
      const surface = target.getAttribute('data-surface')
      
      if (!toothId) return
      
      const toothNumber = parseInt(toothId)
      
      if (surface) {
        // Surface clicked
        this.cycleSurfaceStatus(toothNumber, surface)
      } else {
        // Main tooth clicked
        this.cycleToothStatus(toothNumber)
      }
      
      this.$emit('update:record', this.teethStatus)
    },
    cycleSurfaceStatus(toothNumber, surface) {
      const statuses = ['healthy', 'caries', 'restoration', 'crown']
      const currentStatus = this.teethStatus[toothNumber].surfaces[surface].status
      const currentIndex = statuses.indexOf(currentStatus)
      const nextIndex = (currentIndex + 1) % statuses.length
      const newStatus = statuses[nextIndex]
      
      this.teethStatus[toothNumber].surfaces[surface].status = newStatus
      this.teethStatus[toothNumber].surfaces[surface].condition = this.getConditionLabel(newStatus)
      
      // Update main tooth status based on surfaces
      this.updateMainToothStatus(toothNumber)
    },
    cycleToothStatus(toothNumber) {
      const statuses = ['healthy', 'caries', 'restoration', 'missing']
      const currentStatus = this.teethStatus[toothNumber].status
      const currentIndex = statuses.indexOf(currentStatus)
      const nextIndex = (currentIndex + 1) % statuses.length
      const newStatus = statuses[nextIndex]
      
      this.teethStatus[toothNumber].status = newStatus
      this.teethStatus[toothNumber].condition = this.getConditionLabel(newStatus)
      
      // Update all surfaces to match main tooth status
      Object.keys(this.teethStatus[toothNumber].surfaces).forEach(surface => {
        this.teethStatus[toothNumber].surfaces[surface].status = newStatus
        this.teethStatus[toothNumber].surfaces[surface].condition = this.getConditionLabel(newStatus)
      })
    },
    updateMainToothStatus(toothNumber) {
      const surfaces = this.teethStatus[toothNumber].surfaces
      const surfaceStatuses = Object.values(surfaces).map(s => s.status)
      
      // Determine main tooth status based on surface statuses
      if (surfaceStatuses.every(s => s === 'healthy')) {
        this.teethStatus[toothNumber].status = 'healthy'
      } else if (surfaceStatuses.some(s => s === 'caries')) {
        this.teethStatus[toothNumber].status = 'caries'
      } else if (surfaceStatuses.some(s => s === 'restoration')) {
        this.teethStatus[toothNumber].status = 'restoration'
      } else {
        this.teethStatus[toothNumber].status = 'restoration'
      }
      
      this.teethStatus[toothNumber].condition = this.getConditionLabel(this.teethStatus[toothNumber].status)
    },
    getConditionLabel(status) {
      const labels = {
        'healthy': 'Normal',
        'caries': 'Carie',
        'restoration': 'Restauration',
        'crown': 'Couronne',
        'missing': 'Manquante'
      }
      return labels[status] || 'Normal'
    },
    handleMouseover(event) {
      const target = event.target
      const toothId = target.getAttribute('data-tooth')
      
      if (!toothId) return
      
      const toothNumber = parseInt(toothId)
      const tooth = this.teethStatus[toothNumber]
      
      this.tooltip = {
        visible: true,
        x: event.clientX + 10,
        y: event.clientY - 10,
        toothNumber: toothNumber,
        status: tooth.status,
        condition: tooth.condition
      }
    },
    hideTooltip() {
      this.tooltip.visible = false
    },
    updateChartStyles() {
      // Update main teeth
      Object.keys(this.teethStatus).forEach(toothNumber => {
        const tooth = this.teethStatus[toothNumber]
        const mainElement = document.getElementById(`tooth-${toothNumber}-main`)
        
        if (mainElement) {
          mainElement.className = `tooth-surface status-${tooth.status}`
        }
        
        // Update surfaces
        Object.keys(tooth.surfaces).forEach(surface => {
          const surfaceElement = document.getElementById(`tooth-${toothNumber}-${surface}`)
          if (surfaceElement) {
            surfaceElement.className = `tooth-surface status-${tooth.surfaces[surface].status}`
          }
        })
      })
    }
  }
}
</script>

<style scoped>
.dental-chart-container {
  position: relative;
  background: white;
  border-radius: 8px;
  padding: 20px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.chart-header {
  margin-bottom: 20px;
}

.chart-wrapper {
  display: flex;
  justify-content: center;
  margin: 20px 0;
  overflow-x: auto;
}

.dental-svg-container {
  position: relative;
  min-width: 800px;
}

.dental-tooltip {
  background: rgba(0, 0, 0, 0.8);
  color: white;
  padding: 8px 12px;
  border-radius: 4px;
  font-size: 12px;
  pointer-events: none;
  white-space: nowrap;
}

.tooltip-content {
  line-height: 1.4;
}

/* SVG Styles */
:deep(.tooth) {
  cursor: pointer;
  transition: all 0.2s ease;
}

:deep(.tooth:hover) {
  opacity: 0.8;
}

:deep(.tooth-surface) {
  cursor: pointer;
  transition: fill 0.2s ease;
}

:deep(.status-healthy) {
  fill: #ffffff;
  stroke: #cccccc;
}

:deep(.status-caries) {
  fill: #ff4d4d;
  stroke: #cc0000;
}

:deep(.status-restoration) {
  fill: #4d94ff;
  stroke: #0066cc;
}

:deep(.status-crown) {
  stroke: #4d94ff;
  stroke-width: 3px;
  fill-opacity: 0.1;
}

:deep(.status-missing) {
  fill: #808080;
  opacity: 0.5;
}
</style> 