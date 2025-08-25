<template>
  <div class="national-league-logo">
    <!-- Logo de la ligue nationale -->
    <img 
      v-if="logoUrl" 
      :src="logoUrl" 
      :alt="`Logo de la ligue nationale ${countryName || countryCode}`"
      :class="logoClasses"
      @error="handleImageError"
      @load="handleImageLoad"
      class="transition-all duration-300"
      :class="{ 'opacity-0 scale-95': loading, 'opacity-100 scale-100': !loading }"
    />
    
    <!-- Logo FIFA générique en cas d'erreur -->
    <img 
      v-if="showFallback" 
      :src="fallbackLogoUrl" 
      :alt="`Logo FIFA générique pour ${countryCode}`"
      :class="logoClasses"
      class="opacity-75"
    />
    
    <!-- Indicateur de chargement -->
    <div 
      v-if="loading" 
      :class="logoClasses"
      class="flex items-center justify-center bg-gradient-to-br from-blue-50 to-blue-100 animate-pulse rounded-lg border border-blue-200"
    >
      <div class="w-4 h-4 bg-blue-400 rounded-full animate-bounce"></div>
    </div>
    
    <!-- Placeholder si pas de code de pays -->
    <div 
      v-if="!countryCode && !loading" 
      :class="logoClasses"
      class="flex items-center justify-center bg-gradient-to-br from-gray-100 to-gray-200 text-gray-500 text-xs font-medium rounded-lg border border-gray-300"
    >
      🌍
    </div>
    
    <!-- Tooltip avec informations -->
    <div 
      v-if="showTooltip && countryName" 
      class="absolute -bottom-8 left-1/2 transform -translate-x-1/2 bg-gray-800 text-white text-xs px-2 py-1 rounded whitespace-nowrap opacity-0 group-hover:opacity-100 transition-opacity duration-200 pointer-events-none z-10"
    >
      {{ countryName }}
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'

// Props
const props = defineProps({
  countryCode: {
    type: String,
    default: ''
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
  showFallback: {
    type: Boolean,
    default: true
  },
  showTooltip: {
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
  },
  hover: {
    type: Boolean,
    default: true
  },
  class: {
    type: String,
    default: ''
  }
})

// Émits
const emit = defineEmits(['loaded', 'error', 'fallback'])

// État réactif
const loading = ref(true)
const hasError = ref(false)
const showFallbackLogo = ref(false)

// Classes CSS dynamiques
const logoClasses = computed(() => {
  const baseClasses = 'object-contain'
  const sizeClasses = {
    xs: 'w-6 h-6',
    sm: 'w-8 h-8',
    md: 'w-10 h-10',
    lg: 'w-12 h-12',
    xl: 'w-16 h-16',
    '2xl': 'w-20 h-20'
  }
  const roundedClasses = props.rounded ? 'rounded-lg' : ''
  const shadowClasses = props.shadow ? 'shadow-md' : ''
  const hoverClasses = props.hover ? 'group hover:scale-105 transition-transform duration-200' : ''
  
  return `${baseClasses} ${sizeClasses[props.size]} ${roundedClasses} ${shadowClasses} ${hoverClasses} ${props.class}`.trim()
})

// URLs des logos des ligues nationales
const logoUrl = computed(() => {
  if (!props.countryCode) return null
  
  const code = props.countryCode.toUpperCase()
  
  // Essayer d'abord le logo local téléchargé
  return `/associations/${code}.png`
})

const fallbackLogoUrl = computed(() => {
  // Logo FIFA générique comme fallback
  return '/images/logos/fifa-generic.png'
})

// Gestion des événements
const handleImageLoad = () => {
  loading.value = false
  hasError.value = false
  showFallbackLogo.value = false
  emit('loaded', props.countryCode)
}

const handleImageError = () => {
  loading.value = false
  hasError.value = true
  
  if (props.showFallback) {
    showFallbackLogo.value = true
    emit('fallback', props.countryCode)
  }
  
  emit('error', props.countryCode)
}

// Méthodes
const resetLogo = () => {
  loading.value = true
  hasError.value = false
  showFallbackLogo.value = false
}

// Watchers
watch(() => props.countryCode, () => {
  resetLogo()
})

// Cycle de vie
onMounted(() => {
  // Si pas de code de pays, afficher directement le placeholder
  if (!props.countryCode) {
    loading.value = false
  }
})
</script>

<style scoped>
.national-league-logo {
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

@keyframes bounce {
  0%, 20%, 53%, 80%, 100% {
    transform: translate3d(0, 0, 0);
  }
  40%, 43% {
    transform: translate3d(0, -8px, 0);
  }
  70% {
    transform: translate3d(0, -4px, 0);
  }
  90% {
    transform: translate3d(0, -2px, 0);
  }
}

.animate-pulse {
  animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

.animate-bounce {
  animation: bounce 1s infinite;
}

/* Transitions fluides */
.transition-all {
  transition-property: all;
  transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
}

.transition-transform {
  transition-property: transform;
  transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
}

.duration-200 {
  transition-duration: 200ms;
}

.duration-300 {
  transition-duration: 300ms;
}

/* Effet de survol */
.group:hover .group-hover\:scale-105 {
  transform: scale(1.05);
}

.group:hover .group-hover\:opacity-100 {
  opacity: 1;
}
</style>

