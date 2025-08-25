<template>
  <div class="tab-view-container">
    <!-- Barre de navigation des onglets -->
    <div class="tabs-nav">
      <button 
        v-for="tab in tabs" 
        :key="tab.id"
        @click="activeTab = tab.id" 
        :class="{ 
          'active': activeTab === tab.id,
          'tab-button': true
        }"
        class="tab-button"
      >
        <span class="tab-icon">{{ tab.icon }}</span>
        <span class="tab-label">{{ tab.label }}</span>
      </button>
    </div>

    <!-- Contenu des onglets -->
    <div class="tab-content">
      <div 
        v-for="tab in tabs" 
        :key="tab.id"
        v-show="activeTab === tab.id"
        class="tab-panel"
      >
        <slot :name="tab.id">
          <div class="tab-default-content">
            <h3>{{ tab.label }}</h3>
            <p>Contenu de l'onglet {{ tab.label }}</p>
          </div>
        </slot>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, defineProps, defineEmits } from 'vue'

const props = defineProps({
  tabs: {
    type: Array,
    required: true,
    default: () => []
  },
  defaultTab: {
    type: String,
    default: null
  }
})

const emit = defineEmits(['tab-change'])

const activeTab = ref(props.defaultTab || (props.tabs.length > 0 ? props.tabs[0].id : ''))

// Émettre un événement quand l'onglet change
const changeTab = (tabId) => {
  activeTab.value = tabId
  emit('tab-change', tabId)
}
</script>

<style scoped>
.tab-view-container {
  @apply w-full;
}

.tabs-nav {
  @apply flex border-b-2 border-gray-200 bg-white rounded-t-lg overflow-hidden;
}

.tab-button {
  @apply flex items-center space-x-2 px-6 py-4 border-none bg-transparent cursor-pointer transition-all duration-200 font-medium text-gray-600 hover:text-gray-800 hover:bg-gray-50;
}

.tab-button.active {
  @apply text-blue-600 border-b-2 border-blue-600 bg-blue-50 font-semibold;
}

.tab-icon {
  @apply text-lg;
}

.tab-label {
  @apply text-sm;
}

.tab-content {
  @apply bg-white border border-gray-200 rounded-b-lg;
}

.tab-panel {
  @apply p-6;
}

.tab-default-content {
  @apply text-center text-gray-500 py-8;
}

/* Responsive design */
@media (max-width: 768px) {
  .tabs-nav {
    @apply flex-wrap;
  }
  
  .tab-button {
    @apply px-4 py-3 text-xs;
  }
  
  .tab-icon {
    @apply text-base;
  }
  
  .tab-label {
    @apply hidden sm:inline;
  }
}
</style> 