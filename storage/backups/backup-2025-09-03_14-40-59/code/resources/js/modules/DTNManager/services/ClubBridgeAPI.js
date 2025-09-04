/**
 * Service ClubBridge pour la communication avec les clubs étrangers
 * Gère l'échange sécurisé de données médicales et de performance
 */

class ClubBridgeAPI {
  constructor() {
    this.baseUrl = process.env.VUE_APP_CLUB_BRIDGE_URL || '/api/club'
    this.apiKey = process.env.VUE_APP_CLUB_BRIDGE_KEY
    this.accessToken = null
  }

  /**
   * Authentification avec le club étranger
   */
  async authenticateClub(clubId, credentials) {
    try {
      const response = await fetch(`${this.baseUrl}/auth/${clubId}`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-API-Key': this.apiKey
        },
        body: JSON.stringify(credentials)
      })

      if (!response.ok) {
        throw new Error('Échec de l\'authentification avec le club')
      }

      const data = await response.json()
      this.accessToken = data.access_token
      
      return data
    } catch (error) {
      console.error('Erreur d\'authentification club:', error)
      throw error
    }
  }

  /**
   * Récupérer les données médicales d'un joueur depuis son club
   */
  async getPlayerMedicalData(fifaId, clubId) {
    try {
      await this.ensureAuthenticated()
      
      const response = await fetch(`${this.baseUrl}/player/${fifaId}/medical`, {
        headers: {
          'Authorization': `Bearer ${this.accessToken}`,
          'Content-Type': 'application/json',
          'X-Club-ID': clubId
        }
      })

      if (!response.ok) {
        throw new Error(`Erreur lors de la récupération des données médicales pour ${fifaId}`)
      }

      return await response.json()
    } catch (error) {
      console.error('Erreur lors de la récupération des données médicales:', error)
      throw error
    }
  }

  /**
   * Récupérer la charge d'entraînement d'un joueur
   */
  async getPlayerTrainingLoad(fifaId, clubId, period = '7d') {
    try {
      await this.ensureAuthenticated()
      
      const response = await fetch(`${this.baseUrl}/player/${fifaId}/trainingload?period=${period}`, {
        headers: {
          'Authorization': `Bearer ${this.accessToken}`,
          'Content-Type': 'application/json',
          'X-Club-ID': clubId
        }
      })

      if (!response.ok) {
        throw new Error(`Erreur lors de la récupération de la charge d'entraînement pour ${fifaId}`)
      }

      return await response.json()
    } catch (error) {
      console.error('Erreur lors de la récupération de la charge d\'entraînement:', error)
      throw error
    }
  }

  /**
   * Envoyer un feedback au club sur un joueur
   */
  async sendPlayerFeedback(fifaId, clubId, feedback) {
    try {
      await this.ensureAuthenticated()
      
      const response = await fetch(`${this.baseUrl}/player/${fifaId}/feedback`, {
        method: 'POST',
        headers: {
          'Authorization': `Bearer ${this.accessToken}`,
          'Content-Type': 'application/json',
          'X-Club-ID': clubId
        },
        body: JSON.stringify({
          feedback_type: feedback.type,
          message: feedback.message,
          priority: feedback.priority || 'normal',
          attachments: feedback.attachments || []
        })
      })

      if (!response.ok) {
        throw new Error(`Erreur lors de l'envoi du feedback pour ${fifaId}`)
      }

      return await response.json()
    } catch (error) {
      console.error('Erreur lors de l\'envoi du feedback:', error)
      throw error
    }
  }

  /**
   * Notifier un club d'une convocation
   */
  async notifyCallup(fifaId, clubId, callupData) {
    try {
      await this.ensureAuthenticated()
      
      const response = await fetch(`${this.baseUrl}/player/${fifaId}/callup`, {
        method: 'POST',
        headers: {
          'Authorization': `Bearer ${this.accessToken}`,
          'Content-Type': 'application/json',
          'X-Club-ID': clubId
        },
        body: JSON.stringify({
          competition: callupData.competition,
          match_date: callupData.matchDate,
          reporting_date: callupData.reportingDate,
          venue: callupData.venue,
          contact_person: callupData.contactPerson,
          emergency_contact: callupData.emergencyContact
        })
      })

      if (!response.ok) {
        throw new Error(`Erreur lors de la notification de convocation pour ${fifaId}`)
      }

      return await response.json()
    } catch (error) {
      console.error('Erreur lors de la notification de convocation:', error)
      throw error
    }
  }

  /**
   * Demander la libération d'un joueur pour une compétition
   */
  async requestPlayerRelease(fifaId, clubId, releaseData) {
    try {
      await this.ensureAuthenticated()
      
      const response = await fetch(`${this.baseUrl}/player/${fifaId}/release`, {
        method: 'POST',
        headers: {
          'Authorization': `Bearer ${this.accessToken}`,
          'Content-Type': 'application/json',
          'X-Club-ID': clubId
        },
        body: JSON.stringify({
          competition: releaseData.competition,
          start_date: releaseData.startDate,
          end_date: releaseData.endDate,
          reason: releaseData.reason,
          fifa_regulation: releaseData.fifaRegulation
        })
      })

      if (!response.ok) {
        throw new Error(`Erreur lors de la demande de libération pour ${fifaId}`)
      }

      return await response.json()
    } catch (error) {
      console.error('Erreur lors de la demande de libération:', error)
      throw error
    }
  }

  /**
   * Récupérer les performances récentes d'un joueur
   */
  async getPlayerPerformance(fifaId, clubId, period = '30d') {
    try {
      await this.ensureAuthenticated()
      
      const response = await fetch(`${this.baseUrl}/player/${fifaId}/performance?period=${period}`, {
        headers: {
          'Authorization': `Bearer ${this.accessToken}`,
          'Content-Type': 'application/json',
          'X-Club-ID': clubId
        }
      })

      if (!response.ok) {
        throw new Error(`Erreur lors de la récupération des performances pour ${fifaId}`)
      }

      return await response.json()
    } catch (error) {
      console.error('Erreur lors de la récupération des performances:', error)
      throw error
    }
  }

  /**
   * Vérifier la disponibilité d'un joueur pour une période donnée
   */
  async checkPlayerAvailability(fifaId, clubId, startDate, endDate) {
    try {
      await this.ensureAuthenticated()
      
      const response = await fetch(`${this.baseUrl}/player/${fifaId}/availability`, {
        method: 'POST',
        headers: {
          'Authorization': `Bearer ${this.accessToken}`,
          'Content-Type': 'application/json',
          'X-Club-ID': clubId
        },
        body: JSON.stringify({
          start_date: startDate,
          end_date: endDate
        })
      })

      if (!response.ok) {
        throw new Error(`Erreur lors de la vérification de disponibilité pour ${fifaId}`)
      }

      return await response.json()
    } catch (error) {
      console.error('Erreur lors de la vérification de disponibilité:', error)
      throw error
    }
  }

  /**
   * Récupérer les informations du club
   */
  async getClubInfo(clubId) {
    try {
      await this.ensureAuthenticated()
      
      const response = await fetch(`${this.baseUrl}/club/${clubId}/info`, {
        headers: {
          'Authorization': `Bearer ${this.accessToken}`,
          'Content-Type': 'application/json'
        }
      })

      if (!response.ok) {
        throw new Error(`Erreur lors de la récupération des informations du club ${clubId}`)
      }

      return await response.json()
    } catch (error) {
      console.error('Erreur lors de la récupération des informations du club:', error)
      throw error
    }
  }

  /**
   * Envoyer un message au club
   */
  async sendMessageToClub(clubId, message) {
    try {
      await this.ensureAuthenticated()
      
      const response = await fetch(`${this.baseUrl}/club/${clubId}/message`, {
        method: 'POST',
        headers: {
          'Authorization': `Bearer ${this.accessToken}`,
          'Content-Type': 'application/json'
        },
        body: JSON.stringify({
          subject: message.subject,
          content: message.content,
          priority: message.priority || 'normal',
          attachments: message.attachments || []
        })
      })

      if (!response.ok) {
        throw new Error(`Erreur lors de l'envoi du message au club ${clubId}`)
      }

      return await response.json()
    } catch (error) {
      console.error('Erreur lors de l\'envoi du message:', error)
      throw error
    }
  }

  /**
   * Récupérer l'historique des communications avec un club
   */
  async getCommunicationHistory(clubId, limit = 50) {
    try {
      await this.ensureAuthenticated()
      
      const response = await fetch(`${this.baseUrl}/club/${clubId}/communications?limit=${limit}`, {
        headers: {
          'Authorization': `Bearer ${this.accessToken}`,
          'Content-Type': 'application/json'
        }
      })

      if (!response.ok) {
        throw new Error(`Erreur lors de la récupération de l'historique pour le club ${clubId}`)
      }

      return await response.json()
    } catch (error) {
      console.error('Erreur lors de la récupération de l\'historique:', error)
      throw error
    }
  }

  /**
   * S'assurer que l'authentification est valide
   */
  async ensureAuthenticated() {
    if (!this.accessToken) {
      throw new Error('Authentification requise avec le club')
    }
  }

  /**
   * Valider les données selon les standards ClubBridge
   */
  validateClubData(data, type) {
    const validators = {
      medical: this.validateMedicalData,
      performance: this.validatePerformanceData,
      callup: this.validateCallupData,
      feedback: this.validateFeedbackData
    }

    const validator = validators[type]
    if (!validator) {
      throw new Error(`Type de validation non supporté: ${type}`)
    }

    return validator(data)
  }

  /**
   * Valider les données médicales
   */
  validateMedicalData(medicalData) {
    const required = ['player_id', 'assessment_date', 'medical_status']
    const missing = required.filter(field => !medicalData[field])
    
    if (missing.length > 0) {
      throw new Error(`Champs médicaux manquants: ${missing.join(', ')}`)
    }

    return true
  }

  /**
   * Valider les données de performance
   */
  validatePerformanceData(performanceData) {
    const required = ['player_id', 'period', 'metrics']
    const missing = required.filter(field => !performanceData[field])
    
    if (missing.length > 0) {
      throw new Error(`Champs de performance manquants: ${missing.join(', ')}`)
    }

    return true
  }

  /**
   * Valider les données de convocation
   */
  validateCallupData(callupData) {
    const required = ['competition', 'match_date', 'reporting_date']
    const missing = required.filter(field => !callupData[field])
    
    if (missing.length > 0) {
      throw new Error(`Champs de convocation manquants: ${missing.join(', ')}`)
    }

    return true
  }

  /**
   * Valider les données de feedback
   */
  validateFeedbackData(feedbackData) {
    const required = ['feedback_type', 'message']
    const missing = required.filter(field => !feedbackData[field])
    
    if (missing.length > 0) {
      throw new Error(`Champs de feedback manquants: ${missing.join(', ')}`)
    }

    return true
  }
}

// Export singleton
export default new ClubBridgeAPI() 