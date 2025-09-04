import { defineStore } from 'pinia'

export const useFootballTypeStore = defineStore('footballType', {
  state: () => ({
    selectedType: localStorage.getItem('footballType') || null,
    footballTypes: {
      '11aside': {
        id: '11aside',
        name: '11-a-side Football',
        displayName: '11-a-side Football',
        emoji: '⚽',
        players: 11,
        fieldType: 'Full pitch',
        duration: '90 min',
        description: 'Classic men\'s football with 11 players per team on a full-size pitch.',
        rules: {
          teamSize: 11,
          substitutes: 3,
          fieldDimensions: '100-130m x 50-100m',
          ballSize: 5
        }
      },
      'womens': {
        id: 'womens',
        name: 'Women\'s Football',
        displayName: 'Women\'s Football',
        emoji: '👩‍🦰',
        players: 11,
        fieldType: 'Full pitch',
        duration: '90 min',
        description: 'Women\'s football with the same rules as 11-a-side.',
        rules: {
          teamSize: 11,
          substitutes: 3,
          fieldDimensions: '100-130m x 50-100m',
          ballSize: 5
        }
      },
      'futsal': {
        id: 'futsal',
        name: 'Futsal',
        displayName: 'Futsal',
        emoji: '🏐',
        players: 5,
        fieldType: 'Indoor court',
        duration: '40 min',
        description: 'Indoor football with 5 players per team on a smaller court.',
        rules: {
          teamSize: 5,
          substitutes: 7,
          fieldDimensions: '38-42m x 20-25m',
          ballSize: 4
        }
      },
      'beach': {
        id: 'beach',
        name: 'Beach Soccer',
        displayName: 'Beach Soccer',
        emoji: '🏖️',
        players: 5,
        fieldType: 'Sand court',
        duration: '36 min',
        description: 'Football played on sand with 5 players per team.',
        rules: {
          teamSize: 5,
          substitutes: 3,
          fieldDimensions: '35-37m x 26-28m',
          ballSize: 5
        }
      }
    }
  }),

  getters: {
    currentType: (state) => state.selectedType ? state.footballTypes[state.selectedType] : null,
    currentTypeId: (state) => state.selectedType,
    currentTypeName: (state) => state.selectedType ? state.footballTypes[state.selectedType].name : null,
    currentTypeDisplayName: (state) => state.selectedType ? state.footballTypes[state.selectedType].displayName : null,
    currentTypeEmoji: (state) => state.selectedType ? state.footballTypes[state.selectedType].emoji : null,
    currentTypeRules: (state) => state.selectedType ? state.footballTypes[state.selectedType].rules : null,
    allTypes: (state) => Object.values(state.footballTypes),
    hasSelectedType: (state) => !!state.selectedType
  },

  actions: {
    setFootballType(typeId) {
      if (this.footballTypes[typeId]) {
        this.selectedType = typeId
        localStorage.setItem('footballType', typeId)
        
        // Emit a custom event for other components to listen to
        window.dispatchEvent(new CustomEvent('footballTypeChanged', {
          detail: { typeId, type: this.footballTypes[typeId] }
        }))
      }
    },

    clearFootballType() {
      this.selectedType = null
      localStorage.removeItem('footballType')
      
      window.dispatchEvent(new CustomEvent('footballTypeChanged', {
        detail: { typeId: null, type: null }
      }))
    },

    getTypeById(typeId) {
      return this.footballTypes[typeId] || null
    },

    getTypeRules(typeId) {
      const type = this.footballTypes[typeId]
      return type ? type.rules : null
    },

    // Initialize from URL if needed
    initializeFromUrl() {
      const path = window.location.pathname
      const match = path.match(/^\/([^\/]+)\//)
      if (match && this.footballTypes[match[1]]) {
        this.setFootballType(match[1])
      }
    },

    // Get dashboard URL for current type
    getDashboardUrl() {
      return this.selectedType ? `/${this.selectedType}/dashboard` : '/'
    },

    // Get specific module URL for current type
    getModuleUrl(module) {
      return this.selectedType ? `/${this.selectedType}/${module}` : '/'
    }
  }
}) 