<template>
  <div class="country-flag-container">
    <!-- Drapeau principal -->
    <img 
      v-if="flagUrl" 
      :src="flagUrl" 
      :alt="`Drapeau de ${countryName}`"
      :class="flagClasses"
      @error="handleImageError"
      @load="handleImageLoad"
      class="transition-opacity duration-200"
      :class="{ 'opacity-0': loading, 'opacity-100': !loading }"
    />
    
    <!-- Drapeau de fallback -->
    <div 
      v-if="showFallback" 
      :class="fallbackClasses"
      class="flex items-center justify-center bg-gray-200 text-gray-500 text-xs font-medium"
    >
      {{ countryCode?.toUpperCase() || '🏳️' }}
    </div>
    
    <!-- Indicateur de chargement -->
    <div 
      v-if="loading" 
      :class="flagClasses"
      class="flex items-center justify-center bg-gray-100 animate-pulse"
    >
      <div class="w-4 h-4 bg-gray-300 rounded-full"></div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'

// Props
const props = defineProps({
  countryCode: {
    type: String,
    required: true,
    validator: (value) => /^[A-Z]{2}$/i.test(value)
  },
  countryName: {
    type: String,
    default: ''
  },
  size: {
    type: String,
    default: 'md',
    validator: (value) => ['xs', 'sm', 'md', 'lg', 'xl', '2xl'].includes(value)
  },
  format: {
    type: String,
    default: 'svg',
    validator: (value) => ['svg', 'png'].includes(value)
  },
  fallback: {
    type: Boolean,
    default: true
  },
  rounded: {
    type: Boolean,
    default: true
  },
  shadow: {
    type: Boolean,
    default: true
  }
})

// Émits
const emit = defineEmits(['loaded', 'error'])

// État réactif
const loading = ref(true)
const showFallback = ref(false)
const hasError = ref(false)

// Classes CSS dynamiques
const flagClasses = computed(() => {
  const baseClasses = 'object-cover'
  const sizeClasses = {
    xs: 'w-4 h-3',
    sm: 'w-6 h-4',
    md: 'w-8 h-6',
    lg: 'w-12 h-8',
    xl: 'w-16 h-12',
    '2xl': 'w-20 h-15'
  }
  const roundedClasses = props.rounded ? 'rounded-sm' : ''
  const shadowClasses = props.shadow ? 'shadow-sm' : ''
  
  return `${baseClasses} ${sizeClasses[props.size]} ${roundedClasses} ${shadowClasses}`
})

const fallbackClasses = computed(() => {
  const sizeClasses = {
    xs: 'w-4 h-3 text-xs',
    sm: 'w-6 h-4 text-xs',
    md: 'w-8 h-6 text-sm',
    lg: 'w-12 h-8 text-base',
    xl: 'w-16 h-12 text-lg',
    '2xl': 'w-20 h-15 text-xl'
  }
  
  return `${sizeClasses[props.size]}`
})

// URL du drapeau
const flagUrl = computed(() => {
  if (!props.countryCode) return null
  
  const code = props.countryCode.toLowerCase()
  
  if (props.format === 'svg') {
    return `https://flagcdn.com/${code}.svg`
  } else {
    // Pour PNG, on utilise une taille appropriée selon la taille demandée
    const pngSizes = {
      xs: 'w40',
      sm: 'w80',
      md: 'w120',
      lg: 'w160',
      xl: 'w240',
      '2xl': 'w320'
    }
    return `https://flagcdn.com/${pngSizes[props.size]}/${code}.png`
  }
})

// Gestion des événements
const handleImageLoad = () => {
  loading.value = false
  hasError.value = false
  showFallback.value = false
  emit('loaded', props.countryCode)
}

const handleImageError = () => {
  loading.value = false
  hasError.value = true
  showFallback.value = props.fallback
  emit('error', props.countryCode)
}

// Méthodes
const resetFlag = () => {
  loading.value = true
  hasError.value = false
  showFallback.value = false
}

// Watchers
watch(() => props.countryCode, () => {
  resetFlag()
})

// Cycle de vie
onMounted(() => {
  // Si pas de countryCode, afficher directement le fallback
  if (!props.countryCode) {
    loading.value = false
    showFallback.value = true
  }
})
</script>

<style scoped>
.country-flag-container {
  position: relative;
  display: inline-block;
}

/* Animation de chargement */
@keyframes pulse {
  0%, 100% {
    opacity: 1;
  }
  50% {
    opacity: 0.5;
  }
}

.animate-pulse {
  animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

/* Transitions fluides */
.transition-opacity {
  transition-property: opacity;
  transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
}

.duration-200 {
  transition-duration: 200ms;
}
</style>

