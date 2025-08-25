<template>
  <div class="fifa-association-logo">
    <!-- Logo de l'association nationale -->
    <img 
      v-if="logoUrl" 
      :src="logoUrl" 
      :alt="`Logo de ${associationName || associationCode}`"
      :class="logoClasses"
      @error="handleImageError"
      @load="handleImageLoad"
      class="transition-opacity duration-200"
      :class="{ 'opacity-0': loading, 'opacity-100': !loading }"
    />
    
    <!-- Logo FIFA générique en cas d'erreur -->
    <img 
      v-if="showFallback" 
      :src="fallbackLogoUrl" 
      :alt="`Logo FIFA générique pour ${associationCode}`"
      :class="logoClasses"
      class="opacity-75"
    />
    
    <!-- Indicateur de chargement -->
    <div 
      v-if="loading" 
      :class="logoClasses"
      class="flex items-center justify-center bg-gray-100 animate-pulse rounded"
    >
      <div class="w-4 h-4 bg-gray-300 rounded-full"></div>
    </div>
    
    <!-- Placeholder si pas de code d'association -->
    <div 
      v-if="!associationCode && !loading" 
      :class="logoClasses"
      class="flex items-center justify-center bg-gray-200 text-gray-500 text-xs font-medium rounded"
    >
      🏆
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'

// Props
const props = defineProps({
  associationCode: {
    type: String,
    default: ''
  },
  associationName: {
    type: String,
    default: ''
  },
  size: {
    type: String,
    default: 'md',
    validator: (value) => ['xs', 'sm', 'md', 'lg', 'xl', '2xl'].includes(value)
  },
  style: {
    type: String,
    default: 'flat',
    validator: (value) => ['flat', 'shiny', '3d'].includes(value)
  },
  showFallback: {
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
  const roundedClasses = props.rounded ? 'rounded' : ''
  const shadowClasses = props.shadow ? 'shadow-sm' : ''
  
  return `${baseClasses} ${sizeClasses[props.size]} ${roundedClasses} ${shadowClasses} ${props.class}`.trim()
})

// URLs des logos des associations nationales
const logoUrl = computed(() => {
  if (!props.associationCode) return null
  
  const code = props.associationCode.toUpperCase()
  
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
  emit('loaded', props.associationCode)
}

const handleImageError = () => {
  loading.value = false
  hasError.value = true
  
  if (props.showFallback) {
    showFallbackLogo.value = true
    emit('fallback', props.associationCode)
  }
  
  emit('error', props.associationCode)
}

// Méthodes
const resetLogo = () => {
  loading.value = true
  hasError.value = false
  showFallbackLogo.value = false
}

// Watchers
watch(() => props.associationCode, () => {
  resetLogo()
})

watch(() => props.style, () => {
  resetLogo()
})

// Cycle de vie
onMounted(() => {
  // Si pas de code d'association, afficher directement le placeholder
  if (!props.associationCode) {
    loading.value = false
  }
})
</script>

<style scoped>
.fifa-association-logo {
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
