/**
 * Service FIFA Connect pour l'intégration avec l'API FIFA
 * Gère les convocations, les données des joueurs et la synchronisation
 */

class FifaConnectService {
  constructor() {
    this.baseUrl = process.env.VUE_APP_FIFA_API_URL || 'https://api.fifa.com/v1'
    this.apiKey = process.env.VUE_APP_FIFA_API_KEY
    this.accessToken = null
  }

  /**
   * Authentification OAuth2 avec FIFA Connect
   */
  async authenticate() {
    try {
      const response = await fetch(`${this.baseUrl}/auth/token`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-API-Key': this.apiKey
        },
        body: JSON.stringify({
          grant_type: 'client_credentials',
          client_id: process.env.VUE_APP_FIFA_CLIENT_ID,
          client_secret: process.env.VUE_APP_FIFA_CLIENT_SECRET
        })
      })

      if (!response.ok) {
        throw new Error('Échec de l\'authentification FIFA Connect')
      }

      const data = await response.json()
      this.accessToken = data.access_token
      
      return data
    } catch (error) {
      console.error('Erreur d\'authentification FIFA Connect:', error)
      throw error
    }
  }

  /**
   * Récupérer les données d'un joueur via FIFA Connect ID
   */
  async getPlayerData(fifaConnectId) {
    try {
      await this.ensureAuthenticated()
      
      const response = await fetch(`${this.baseUrl}/players/${fifaConnectId}`, {
        headers: {
          'Authorization': `Bearer ${this.accessToken}`,
          'Content-Type': 'application/json'
        }
      })

      if (!response.ok) {
        throw new Error(`Erreur lors de la récupération des données du joueur ${fifaConnectId}`)
      }

      return await response.json()
    } catch (error) {
      console.error('Erreur lors de la récupération des données du joueur:', error)
      throw error
    }
  }

  /**
   * Créer une convocation officielle FIFA
   */
  async createCallup(callupData) {
    try {
      await this.ensureAuthenticated()
      
      const response = await fetch(`${this.baseUrl}/callups`, {
        method: 'POST',
        headers: {
          'Authorization': `Bearer ${this.accessToken}`,
          'Content-Type': 'application/json'
        },
        body: JSON.stringify({
          team_id: callupData.teamId,
          competition_id: callupData.competitionId,
          match_date: callupData.matchDate,
          players: callupData.players.map(player => ({
            fifa_connect_id: player.fifaConnectId,
            position: player.position,
            jersey_number: player.jerseyNumber
          })),
          coach_id: callupData.coachId,
          venue: callupData.venue,
          opponent: callupData.opponent
        })
      })

      if (!response.ok) {
        throw new Error('Erreur lors de la création de la convocation FIFA')
      }

      return await response.json()
    } catch (error) {
      console.error('Erreur lors de la création de la convocation:', error)
      throw error
    }
  }

  /**
   * Mettre à jour une convocation existante
   */
  async updateCallup(callupId, callupData) {
    try {
      await this.ensureAuthenticated()
      
      const response = await fetch(`${this.baseUrl}/callups/${callupId}`, {
        method: 'PUT',
        headers: {
          'Authorization': `Bearer ${this.accessToken}`,
          'Content-Type': 'application/json'
        },
        body: JSON.stringify(callupData)
      })

      if (!response.ok) {
        throw new Error('Erreur lors de la mise à jour de la convocation FIFA')
      }

      return await response.json()
    } catch (error) {
      console.error('Erreur lors de la mise à jour de la convocation:', error)
      throw error
    }
  }

  /**
   * Récupérer le calendrier des compétitions FIFA
   */
  async getCompetitionCalendar(competitionId) {
    try {
      await this.ensureAuthenticated()
      
      const response = await fetch(`${this.baseUrl}/competitions/${competitionId}/calendar`, {
        headers: {
          'Authorization': `Bearer ${this.accessToken}`,
          'Content-Type': 'application/json'
        }
      })

      if (!response.ok) {
        throw new Error('Erreur lors de la récupération du calendrier FIFA')
      }

      return await response.json()
    } catch (error) {
      console.error('Erreur lors de la récupération du calendrier:', error)
      throw error
    }
  }

  /**
   * Vérifier la disponibilité d'un joueur pour une compétition
   */
  async checkPlayerAvailability(fifaConnectId, competitionId, matchDate) {
    try {
      await this.ensureAuthenticated()
      
      const response = await fetch(`${this.baseUrl}/players/${fifaConnectId}/availability`, {
        method: 'POST',
        headers: {
          'Authorization': `Bearer ${this.accessToken}`,
          'Content-Type': 'application/json'
        },
        body: JSON.stringify({
          competition_id: competitionId,
          match_date: matchDate
        })
      })

      if (!response.ok) {
        throw new Error('Erreur lors de la vérification de disponibilité')
      }

      return await response.json()
    } catch (error) {
      console.error('Erreur lors de la vérification de disponibilité:', error)
      throw error
    }
  }

  /**
   * Exporter les données d'une équipe vers FIFA
   */
  async exportTeamData(teamId, competitionId) {
    try {
      await this.ensureAuthenticated()
      
      const response = await fetch(`${this.baseUrl}/teams/${teamId}/export`, {
        method: 'POST',
        headers: {
          'Authorization': `Bearer ${this.accessToken}`,
          'Content-Type': 'application/json'
        },
        body: JSON.stringify({
          competition_id: competitionId,
          export_format: 'fifa_standard',
          include_medical_data: true,
          include_performance_data: true
        })
      })

      if (!response.ok) {
        throw new Error('Erreur lors de l\'export des données d\'équipe')
      }

      return await response.json()
    } catch (error) {
      console.error('Erreur lors de l\'export des données d\'équipe:', error)
      throw error
    }
  }

  /**
   * Récupérer les statistiques d'un joueur depuis FIFA
   */
  async getPlayerStatistics(fifaConnectId, season = null) {
    try {
      await this.ensureAuthenticated()
      
      const params = new URLSearchParams()
      if (season) {
        params.append('season', season)
      }
      
      const response = await fetch(`${this.baseUrl}/players/${fifaConnectId}/statistics?${params}`, {
        headers: {
          'Authorization': `Bearer ${this.accessToken}`,
          'Content-Type': 'application/json'
        }
      })

      if (!response.ok) {
        throw new Error('Erreur lors de la récupération des statistiques')
      }

      return await response.json()
    } catch (error) {
      console.error('Erreur lors de la récupération des statistiques:', error)
      throw error
    }
  }

  /**
   * Synchroniser les données avec FIFA Connect
   */
  async syncData(syncType, data) {
    try {
      await this.ensureAuthenticated()
      
      const response = await fetch(`${this.baseUrl}/sync/${syncType}`, {
        method: 'POST',
        headers: {
          'Authorization': `Bearer ${this.accessToken}`,
          'Content-Type': 'application/json'
        },
        body: JSON.stringify(data)
      })

      if (!response.ok) {
        throw new Error(`Erreur lors de la synchronisation ${syncType}`)
      }

      return await response.json()
    } catch (error) {
      console.error(`Erreur lors de la synchronisation ${syncType}:`, error)
      throw error
    }
  }

  /**
   * S'assurer que l'authentification est valide
   */
  async ensureAuthenticated() {
    if (!this.accessToken) {
      await this.authenticate()
    }
    
    // Vérifier si le token est expiré (optionnel)
    // Ici on pourrait ajouter une vérification de l'expiration du token
  }

  /**
   * Valider les données selon les standards FIFA
   */
  validateFifaData(data, type) {
    const validators = {
      player: this.validatePlayerData,
      callup: this.validateCallupData,
      team: this.validateTeamData
    }

    const validator = validators[type]
    if (!validator) {
      throw new Error(`Type de validation non supporté: ${type}`)
    }

    return validator(data)
  }

  /**
   * Valider les données d'un joueur
   */
  validatePlayerData(playerData) {
    const required = ['fifa_connect_id', 'first_name', 'last_name', 'position']
    const missing = required.filter(field => !playerData[field])
    
    if (missing.length > 0) {
      throw new Error(`Champs manquants pour le joueur: ${missing.join(', ')}`)
    }

    return true
  }

  /**
   * Valider les données d'une convocation
   */
  validateCallupData(callupData) {
    const required = ['team_id', 'competition_id', 'match_date', 'players']
    const missing = required.filter(field => !callupData[field])
    
    if (missing.length > 0) {
      throw new Error(`Champs manquants pour la convocation: ${missing.join(', ')}`)
    }

    if (!Array.isArray(callupData.players) || callupData.players.length === 0) {
      throw new Error('La convocation doit contenir au moins un joueur')
    }

    return true
  }

  /**
   * Valider les données d'une équipe
   */
  validateTeamData(teamData) {
    const required = ['team_id', 'team_name', 'federation_id']
    const missing = required.filter(field => !teamData[field])
    
    if (missing.length > 0) {
      throw new Error(`Champs manquants pour l'équipe: ${missing.join(', ')}`)
    }

    return true
  }
}

// Export singleton
export default new FifaConnectService() 