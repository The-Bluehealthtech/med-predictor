/**
 * Service de synchronisation RPM avec le module Performance
 * Gère l'export des données d'entraînement et de charge de travail
 */

class RpmSyncService {
  constructor() {
    this.baseUrl = process.env.VUE_APP_RPM_API_URL || '/api/rpm'
    this.performanceUrl = process.env.VUE_APP_PERFORMANCE_API_URL || '/api/performance'
    this.accessToken = null
  }

  /**
   * Authentification avec l'API RPM
   */
  async authenticate() {
    try {
      const response = await fetch(`${this.baseUrl}/auth/token`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify({
          grant_type: 'client_credentials',
          client_id: process.env.VUE_APP_RPM_CLIENT_ID,
          client_secret: process.env.VUE_APP_RPM_CLIENT_SECRET
        })
      })

      if (!response.ok) {
        throw new Error('Échec de l\'authentification RPM')
      }

      const data = await response.json()
      this.accessToken = data.access_token
      
      return data
    } catch (error) {
      console.error('Erreur d\'authentification RPM:', error)
      throw error
    }
  }

  /**
   * Exporter les données d'une session vers le module Performance
   */
  async exportSessionToPerformance(sessionId) {
    try {
      await this.ensureAuthenticated()
      
      // Récupérer les données de la session
      const sessionData = await this.getSessionData(sessionId)
      
      // Convertir au format Performance
      const performanceData = this.convertSessionToPerformance(sessionData)
      
      // Envoyer au module Performance
      const response = await fetch(`${this.performanceUrl}/sessions/import`, {
        method: 'POST',
        headers: {
          'Authorization': `Bearer ${this.accessToken}`,
          'Content-Type': 'application/json'
        },
        body: JSON.stringify(performanceData)
      })

      if (!response.ok) {
        throw new Error('Erreur lors de l\'export vers le module Performance')
      }

      return await response.json()
    } catch (error) {
      console.error('Erreur lors de l\'export de session:', error)
      throw error
    }
  }

  /**
   * Exporter les données de charge de travail
   */
  async exportWorkloadData(playerId, period = '7d') {
    try {
      await this.ensureAuthenticated()
      
      // Récupérer les données de charge
      const workloadData = await this.getPlayerWorkload(playerId, period)
      
      // Convertir au format Performance
      const performanceData = this.convertWorkloadToPerformance(workloadData)
      
      // Envoyer au module Performance
      const response = await fetch(`${this.performanceUrl}/workload/import`, {
        method: 'POST',
        headers: {
          'Authorization': `Bearer ${this.accessToken}`,
          'Content-Type': 'application/json'
        },
        body: JSON.stringify(performanceData)
      })

      if (!response.ok) {
        throw new Error('Erreur lors de l\'export des données de charge')
      }

      return await response.json()
    } catch (error) {
      console.error('Erreur lors de l\'export des données de charge:', error)
      throw error
    }
  }

  /**
   * Exporter les données d'attendance
   */
  async exportAttendanceData(sessionId) {
    try {
      await this.ensureAuthenticated()
      
      // Récupérer les données de présence
      const attendanceData = await this.getSessionAttendance(sessionId)
      
      // Convertir au format Performance
      const performanceData = this.convertAttendanceToPerformance(attendanceData)
      
      // Envoyer au module Performance
      const response = await fetch(`${this.performanceUrl}/attendance/import`, {
        method: 'POST',
        headers: {
          'Authorization': `Bearer ${this.accessToken}`,
          'Content-Type': 'application/json'
        },
        body: JSON.stringify(performanceData)
      })

      if (!response.ok) {
        throw new Error('Erreur lors de l\'export des données de présence')
      }

      return await response.json()
    } catch (error) {
      console.error('Erreur lors de l\'export des données de présence:', error)
      throw error
    }
  }

  /**
   * Exporter les données de match
   */
  async exportMatchData(matchId) {
    try {
      await this.ensureAuthenticated()
      
      // Récupérer les données du match
      const matchData = await this.getMatchData(matchId)
      
      // Convertir au format Performance
      const performanceData = this.convertMatchToPerformance(matchData)
      
      // Envoyer au module Performance
      const response = await fetch(`${this.performanceUrl}/matches/import`, {
        method: 'POST',
        headers: {
          'Authorization': `Bearer ${this.accessToken}`,
          'Content-Type': 'application/json'
        },
        body: JSON.stringify(performanceData)
      })

      if (!response.ok) {
        throw new Error('Erreur lors de l\'export des données de match')
      }

      return await response.json()
    } catch (error) {
      console.error('Erreur lors de l\'export des données de match:', error)
      throw error
    }
  }

  /**
   * Synchroniser toutes les données RPM avec Performance
   */
  async syncAllData(period = '30d') {
    try {
      await this.ensureAuthenticated()
      
      const syncResults = {
        sessions: [],
        workload: [],
        attendance: [],
        matches: []
      }

      // Synchroniser les sessions
      const sessions = await this.getSessionsInPeriod(period)
      for (const session of sessions) {
        try {
          const result = await this.exportSessionToPerformance(session.id)
          syncResults.sessions.push({ id: session.id, success: true, data: result })
        } catch (error) {
          syncResults.sessions.push({ id: session.id, success: false, error: error.message })
        }
      }

      // Synchroniser les données de charge
      const players = await this.getActivePlayers()
      for (const player of players) {
        try {
          const result = await this.exportWorkloadData(player.id, period)
          syncResults.workload.push({ playerId: player.id, success: true, data: result })
        } catch (error) {
          syncResults.workload.push({ playerId: player.id, success: false, error: error.message })
        }
      }

      // Synchroniser les présences
      for (const session of sessions) {
        try {
          const result = await this.exportAttendanceData(session.id)
          syncResults.attendance.push({ sessionId: session.id, success: true, data: result })
        } catch (error) {
          syncResults.attendance.push({ sessionId: session.id, success: false, error: error.message })
        }
      }

      // Synchroniser les matches
      const matches = await this.getMatchesInPeriod(period)
      for (const match of matches) {
        try {
          const result = await this.exportMatchData(match.id)
          syncResults.matches.push({ id: match.id, success: true, data: result })
        } catch (error) {
          syncResults.matches.push({ id: match.id, success: false, error: error.message })
        }
      }

      return syncResults
    } catch (error) {
      console.error('Erreur lors de la synchronisation complète:', error)
      throw error
    }
  }

  /**
   * Récupérer les données d'une session
   */
  async getSessionData(sessionId) {
    try {
      const response = await fetch(`${this.baseUrl}/sessions/${sessionId}`, {
        headers: {
          'Authorization': `Bearer ${this.accessToken}`,
          'Content-Type': 'application/json'
        }
      })

      if (!response.ok) {
        throw new Error(`Erreur lors de la récupération de la session ${sessionId}`)
      }

      return await response.json()
    } catch (error) {
      console.error('Erreur lors de la récupération de la session:', error)
      throw error
    }
  }

  /**
   * Récupérer les données de charge d'un joueur
   */
  async getPlayerWorkload(playerId, period) {
    try {
      const response = await fetch(`${this.baseUrl}/players/${playerId}/workload?period=${period}`, {
        headers: {
          'Authorization': `Bearer ${this.accessToken}`,
          'Content-Type': 'application/json'
        }
      })

      if (!response.ok) {
        throw new Error(`Erreur lors de la récupération de la charge du joueur ${playerId}`)
      }

      return await response.json()
    } catch (error) {
      console.error('Erreur lors de la récupération de la charge:', error)
      throw error
    }
  }

  /**
   * Récupérer les données de présence d'une session
   */
  async getSessionAttendance(sessionId) {
    try {
      const response = await fetch(`${this.baseUrl}/sessions/${sessionId}/attendance`, {
        headers: {
          'Authorization': `Bearer ${this.accessToken}`,
          'Content-Type': 'application/json'
        }
      })

      if (!response.ok) {
        throw new Error(`Erreur lors de la récupération des présences de la session ${sessionId}`)
      }

      return await response.json()
    } catch (error) {
      console.error('Erreur lors de la récupération des présences:', error)
      throw error
    }
  }

  /**
   * Récupérer les données d'un match
   */
  async getMatchData(matchId) {
    try {
      const response = await fetch(`${this.baseUrl}/matches/${matchId}`, {
        headers: {
          'Authorization': `Bearer ${this.accessToken}`,
          'Content-Type': 'application/json'
        }
      })

      if (!response.ok) {
        throw new Error(`Erreur lors de la récupération du match ${matchId}`)
      }

      return await response.json()
    } catch (error) {
      console.error('Erreur lors de la récupération du match:', error)
      throw error
    }
  }

  /**
   * Récupérer les sessions dans une période
   */
  async getSessionsInPeriod(period) {
    try {
      const response = await fetch(`${this.baseUrl}/sessions?period=${period}`, {
        headers: {
          'Authorization': `Bearer ${this.accessToken}`,
          'Content-Type': 'application/json'
        }
      })

      if (!response.ok) {
        throw new Error('Erreur lors de la récupération des sessions')
      }

      return await response.json()
    } catch (error) {
      console.error('Erreur lors de la récupération des sessions:', error)
      throw error
    }
  }

  /**
   * Récupérer les joueurs actifs
   */
  async getActivePlayers() {
    try {
      const response = await fetch(`${this.baseUrl}/players/active`, {
        headers: {
          'Authorization': `Bearer ${this.accessToken}`,
          'Content-Type': 'application/json'
        }
      })

      if (!response.ok) {
        throw new Error('Erreur lors de la récupération des joueurs actifs')
      }

      return await response.json()
    } catch (error) {
      console.error('Erreur lors de la récupération des joueurs actifs:', error)
      throw error
    }
  }

  /**
   * Récupérer les matches dans une période
   */
  async getMatchesInPeriod(period) {
    try {
      const response = await fetch(`${this.baseUrl}/matches?period=${period}`, {
        headers: {
          'Authorization': `Bearer ${this.accessToken}`,
          'Content-Type': 'application/json'
        }
      })

      if (!response.ok) {
        throw new Error('Erreur lors de la récupération des matches')
      }

      return await response.json()
    } catch (error) {
      console.error('Erreur lors de la récupération des matches:', error)
      throw error
    }
  }

  /**
   * Convertir les données de session au format Performance
   */
  convertSessionToPerformance(sessionData) {
    return {
      session_id: sessionData.id,
      title: sessionData.title,
      type: sessionData.type,
      date: sessionData.date,
      duration: sessionData.duration,
      location: sessionData.location,
      coach: sessionData.coach,
      objectives: sessionData.objectives,
      players: sessionData.players.map(player => ({
        player_id: player.id,
        position: player.position,
        rpe: player.rpe,
        attendance: player.attendance,
        notes: player.notes
      })),
      metrics: {
        average_rpe: sessionData.averageRpe,
        total_distance: sessionData.totalDistance,
        max_heart_rate: sessionData.maxHeartRate,
        average_heart_rate: sessionData.averageHeartRate
      }
    }
  }

  /**
   * Convertir les données de charge au format Performance
   */
  convertWorkloadToPerformance(workloadData) {
    return {
      player_id: workloadData.playerId,
      period: workloadData.period,
      total_sessions: workloadData.totalSessions,
      total_time: workloadData.totalTime,
      average_rpe: workloadData.averageRpe,
      cumulative_load: workloadData.cumulativeLoad,
      acute_load: workloadData.acuteLoad,
      chronic_load: workloadData.chronicLoad,
      acwr_ratio: workloadData.acwrRatio,
      daily_loads: workloadData.dailyLoads.map(load => ({
        date: load.date,
        rpe: load.rpe,
        duration: load.duration,
        session_type: load.sessionType
      }))
    }
  }

  /**
   * Convertir les données de présence au format Performance
   */
  convertAttendanceToPerformance(attendanceData) {
    return {
      session_id: attendanceData.sessionId,
      date: attendanceData.date,
      total_players: attendanceData.totalPlayers,
      present_players: attendanceData.presentPlayers,
      absent_players: attendanceData.absentPlayers,
      attendance_rate: attendanceData.attendanceRate,
      players: attendanceData.players.map(player => ({
        player_id: player.id,
        status: player.status,
        reason: player.reason,
        arrival_time: player.arrivalTime,
        departure_time: player.departureTime
      }))
    }
  }

  /**
   * Convertir les données de match au format Performance
   */
  convertMatchToPerformance(matchData) {
    return {
      match_id: matchData.id,
      title: matchData.title,
      date: matchData.date,
      opponent: matchData.opponent,
      venue: matchData.venue,
      result: matchData.result,
      formation: matchData.formation,
      players: matchData.players.map(player => ({
        player_id: player.id,
        position: player.position,
        minutes_played: player.minutesPlayed,
        goals: player.goals,
        assists: player.assists,
        yellow_cards: player.yellowCards,
        red_cards: player.redCards,
        performance_rating: player.performanceRating
      })),
      tactics: matchData.tactics,
      notes: matchData.notes
    }
  }

  /**
   * S'assurer que l'authentification est valide
   */
  async ensureAuthenticated() {
    if (!this.accessToken) {
      await this.authenticate()
    }
  }

  /**
   * Valider les données selon les standards RPM
   */
  validateRpmData(data, type) {
    const validators = {
      session: this.validateSessionData,
      workload: this.validateWorkloadData,
      attendance: this.validateAttendanceData,
      match: this.validateMatchData
    }

    const validator = validators[type]
    if (!validator) {
      throw new Error(`Type de validation non supporté: ${type}`)
    }

    return validator(data)
  }

  /**
   * Valider les données de session
   */
  validateSessionData(sessionData) {
    const required = ['id', 'title', 'type', 'date', 'duration']
    const missing = required.filter(field => !sessionData[field])
    
    if (missing.length > 0) {
      throw new Error(`Champs de session manquants: ${missing.join(', ')}`)
    }

    return true
  }

  /**
   * Valider les données de charge
   */
  validateWorkloadData(workloadData) {
    const required = ['playerId', 'period', 'totalSessions', 'averageRpe']
    const missing = required.filter(field => !workloadData[field])
    
    if (missing.length > 0) {
      throw new Error(`Champs de charge manquants: ${missing.join(', ')}`)
    }

    return true
  }

  /**
   * Valider les données de présence
   */
  validateAttendanceData(attendanceData) {
    const required = ['sessionId', 'date', 'totalPlayers', 'presentPlayers']
    const missing = required.filter(field => !attendanceData[field])
    
    if (missing.length > 0) {
      throw new Error(`Champs de présence manquants: ${missing.join(', ')}`)
    }

    return true
  }

  /**
   * Valider les données de match
   */
  validateMatchData(matchData) {
    const required = ['id', 'title', 'date', 'opponent', 'result']
    const missing = required.filter(field => !matchData[field])
    
    if (missing.length > 0) {
      throw new Error(`Champs de match manquants: ${missing.join(', ')}`)
    }

    return true
  }
}

// Export singleton
export default new RpmSyncService() 