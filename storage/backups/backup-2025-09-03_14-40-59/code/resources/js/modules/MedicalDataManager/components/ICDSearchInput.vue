<template>
  <div class="icd-search-input">
    <div class="relative">
      <input
        v-model="searchQuery"
        @input="debouncedSearch"
        @focus="showDropdown = true"
        @blur="handleBlur"
        @keydown="handleKeydown"
        type="text"
        :placeholder="placeholder"
        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
        :class="{ 'border-red-500': hasError }"
        :disabled="loading"
      />
      
      <!-- Loading indicator -->
      <div v-if="loading" class="absolute right-3 top-1/2 transform -translate-y-1/2">
        <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-blue-500"></div>
      </div>
      
      <!-- Search icon -->
      <div v-else class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
        </svg>
      </div>
    </div>

    <!-- Dropdown results -->
    <div
      v-if="showDropdown && (searchResults.length > 0 || error)"
      class="absolute z-50 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-lg max-h-60 overflow-y-auto"
    >
      <!-- Error message -->
      <div v-if="error" class="px-4 py-2 text-red-600 text-sm">
        {{ error }}
      </div>
      
      <!-- No results message -->
      <div v-else-if="searchResults.length === 0 && searchQuery.length > 0" class="px-4 py-2 text-gray-500 text-sm">
        Aucun résultat trouvé pour "{{ searchQuery }}"
      </div>
      
      <!-- Results list -->
      <div v-else>
        <div
          v-for="(result, index) in searchResults"
          :key="result.code"
          @click="selectResult(result)"
          @mouseenter="hoveredIndex = index"
          class="px-4 py-2 cursor-pointer hover:bg-blue-50 transition-colors"
          :class="{ 'bg-blue-100': index === hoveredIndex }"
        >
          <div class="flex items-start justify-between">
            <div class="flex-1">
              <div class="font-medium text-gray-900">
                {{ result.code }} - {{ result.label }}
              </div>
              <div v-if="result.definition" class="text-sm text-gray-600 mt-1 line-clamp-2">
                {{ result.definition }}
              </div>
              <div v-if="result.chapter" class="text-xs text-gray-500 mt-1">
                Chapitre: {{ result.chapter }}
              </div>
            </div>
            <div class="ml-2 text-xs text-gray-400">
              ICD-11
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Selected result display -->
    <div v-if="selectedResult" class="mt-2 p-3 bg-green-50 border border-green-200 rounded-lg">
      <div class="flex items-start justify-between">
        <div class="flex-1">
          <div class="font-medium text-green-800">
            {{ selectedResult.code }} - {{ selectedResult.label }}
          </div>
          <div v-if="selectedResult.definition" class="text-sm text-green-600 mt-1">
            {{ selectedResult.definition }}
          </div>
        </div>
        <button
          @click="clearSelection"
          class="ml-2 text-green-600 hover:text-green-800"
          title="Effacer la sélection"
        >
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
          </svg>
        </button>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, watch, nextTick } from 'vue'
import { debounce } from 'lodash'

export default {
  name: 'ICDSearchInput',
  props: {
    modelValue: {
      type: Object,
      default: null
    },
    placeholder: {
      type: String,
      default: 'Rechercher un diagnostic ICD-11...'
    },
    language: {
      type: String,
      default: 'fr'
    },
    limit: {
      type: Number,
      default: 10
    },
    required: {
      type: Boolean,
      default: false
    }
  },
  emits: ['update:modelValue', 'selected', 'cleared'],
  setup(props, { emit }) {
    const searchQuery = ref('')
    const searchResults = ref([])
    const selectedResult = ref(props.modelValue)
    const showDropdown = ref(false)
    const loading = ref(false)
    const error = ref('')
    const hasError = ref(false)
    const hoveredIndex = ref(-1)

    // Debounced search function
    const debouncedSearch = debounce(async () => {
      if (searchQuery.value.length < 2) {
        searchResults.value = []
        return
      }

      loading.value = true
      error.value = ''
      hasError.value = false

      try {
        const response = await fetch(`/api/v1/icd11/search?query=${encodeURIComponent(searchQuery.value)}&language=${props.language}&limit=${props.limit}`, {
          headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
          }
        })

        const data = await response.json()

        if (data.success) {
          searchResults.value = data.data
        } else {
          error.value = data.error || 'Erreur lors de la recherche'
          hasError.value = true
        }
      } catch (err) {
        error.value = 'Erreur de connexion au service ICD-11'
        hasError.value = true
        console.error('ICD-11 search error:', err)
      } finally {
        loading.value = false
      }
    }, 300)

    // Select a result
    const selectResult = (result) => {
      selectedResult.value = result
      searchQuery.value = `${result.code} - ${result.label}`
      showDropdown.value = false
      emit('update:modelValue', result)
      emit('selected', result)
    }

    // Clear selection
    const clearSelection = () => {
      selectedResult.value = null
      searchQuery.value = ''
      searchResults.value = []
      emit('update:modelValue', null)
      emit('cleared')
    }

    // Handle blur event
    const handleBlur = () => {
      setTimeout(() => {
        showDropdown.value = false
      }, 200)
    }

    // Handle keyboard navigation
    const handleKeydown = (event) => {
      if (!showDropdown.value || searchResults.value.length === 0) return

      switch (event.key) {
        case 'ArrowDown':
          event.preventDefault()
          hoveredIndex.value = Math.min(hoveredIndex.value + 1, searchResults.value.length - 1)
          break
        case 'ArrowUp':
          event.preventDefault()
          hoveredIndex.value = Math.max(hoveredIndex.value - 1, -1)
          break
        case 'Enter':
          event.preventDefault()
          if (hoveredIndex.value >= 0) {
            selectResult(searchResults.value[hoveredIndex.value])
          }
          break
        case 'Escape':
          showDropdown.value = false
          hoveredIndex.value = -1
          break
      }
    }

    // Watch for external modelValue changes
    watch(() => props.modelValue, (newValue) => {
      if (newValue) {
        selectedResult.value = newValue
        searchQuery.value = `${newValue.code} - ${newValue.label}`
      } else {
        selectedResult.value = null
        searchQuery.value = ''
      }
    }, { immediate: true })

    return {
      searchQuery,
      searchResults,
      selectedResult,
      showDropdown,
      loading,
      error,
      hasError,
      hoveredIndex,
      debouncedSearch,
      selectResult,
      clearSelection,
      handleBlur,
      handleKeydown
    }
  }
}
</script>

<style scoped>
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.icd-search-input {
  position: relative;
}
</style> 