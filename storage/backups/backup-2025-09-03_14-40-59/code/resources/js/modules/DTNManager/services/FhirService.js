/**
 * Service FHIR pour l'interopérabilité des données médicales
 * Gère l'échange de données selon les standards HL7 FHIR
 */

class FhirService {
  constructor() {
    this.baseUrl = process.env.VUE_APP_FHIR_URL || '/api/fhir'
    this.accessToken = null
  }

  /**
   * Authentification avec le serveur FHIR
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
          client_id: process.env.VUE_APP_FHIR_CLIENT_ID,
          client_secret: process.env.VUE_APP_FHIR_CLIENT_SECRET
        })
      })

      if (!response.ok) {
        throw new Error('Échec de l\'authentification FHIR')
      }

      const data = await response.json()
      this.accessToken = data.access_token
      
      return data
    } catch (error) {
      console.error('Erreur d\'authentification FHIR:', error)
      throw error
    }
  }

  /**
   * Récupérer le dossier médical d'un patient
   */
  async getPatientRecord(patientId) {
    try {
      await this.ensureAuthenticated()
      
      const response = await fetch(`${this.baseUrl}/Patient/${patientId}`, {
        headers: {
          'Authorization': `Bearer ${this.accessToken}`,
          'Content-Type': 'application/fhir+json'
        }
      })

      if (!response.ok) {
        throw new Error(`Erreur lors de la récupération du dossier patient ${patientId}`)
      }

      return await response.json()
    } catch (error) {
      console.error('Erreur lors de la récupération du dossier patient:', error)
      throw error
    }
  }

  /**
   * Récupérer les observations médicales d'un patient
   */
  async getPatientObservations(patientId, observationType = null) {
    try {
      await this.ensureAuthenticated()
      
      let url = `${this.baseUrl}/Observation?patient=${patientId}`
      if (observationType) {
        url += `&code=${observationType}`
      }
      
      const response = await fetch(url, {
        headers: {
          'Authorization': `Bearer ${this.accessToken}`,
          'Content-Type': 'application/fhir+json'
        }
      })

      if (!response.ok) {
        throw new Error(`Erreur lors de la récupération des observations pour ${patientId}`)
      }

      return await response.json()
    } catch (error) {
      console.error('Erreur lors de la récupération des observations:', error)
      throw error
    }
  }

  /**
   * Récupérer les conditions médicales d'un patient
   */
  async getPatientConditions(patientId) {
    try {
      await this.ensureAuthenticated()
      
      const response = await fetch(`${this.baseUrl}/Condition?patient=${patientId}`, {
        headers: {
          'Authorization': `Bearer ${this.accessToken}`,
          'Content-Type': 'application/fhir+json'
        }
      })

      if (!response.ok) {
        throw new Error(`Erreur lors de la récupération des conditions pour ${patientId}`)
      }

      return await response.json()
    } catch (error) {
      console.error('Erreur lors de la récupération des conditions:', error)
      throw error
    }
  }

  /**
   * Récupérer les procédures médicales d'un patient
   */
  async getPatientProcedures(patientId) {
    try {
      await this.ensureAuthenticated()
      
      const response = await fetch(`${this.baseUrl}/Procedure?patient=${patientId}`, {
        headers: {
          'Authorization': `Bearer ${this.accessToken}`,
          'Content-Type': 'application/fhir+json'
        }
      })

      if (!response.ok) {
        throw new Error(`Erreur lors de la récupération des procédures pour ${patientId}`)
      }

      return await response.json()
    } catch (error) {
      console.error('Erreur lors de la récupération des procédures:', error)
      throw error
    }
  }

  /**
   * Récupérer les médicaments d'un patient
   */
  async getPatientMedications(patientId) {
    try {
      await this.ensureAuthenticated()
      
      const response = await fetch(`${this.baseUrl}/MedicationRequest?patient=${patientId}`, {
        headers: {
          'Authorization': `Bearer ${this.accessToken}`,
          'Content-Type': 'application/fhir+json'
        }
      })

      if (!response.ok) {
        throw new Error(`Erreur lors de la récupération des médicaments pour ${patientId}`)
      }

      return await response.json()
    } catch (error) {
      console.error('Erreur lors de la récupération des médicaments:', error)
      throw error
    }
  }

  /**
   * Créer une nouvelle observation médicale
   */
  async createObservation(observationData) {
    try {
      await this.ensureAuthenticated()
      
      const fhirObservation = this.convertToFhirObservation(observationData)
      
      const response = await fetch(`${this.baseUrl}/Observation`, {
        method: 'POST',
        headers: {
          'Authorization': `Bearer ${this.accessToken}`,
          'Content-Type': 'application/fhir+json'
        },
        body: JSON.stringify(fhirObservation)
      })

      if (!response.ok) {
        throw new Error('Erreur lors de la création de l\'observation')
      }

      return await response.json()
    } catch (error) {
      console.error('Erreur lors de la création de l\'observation:', error)
      throw error
    }
  }

  /**
   * Mettre à jour une observation existante
   */
  async updateObservation(observationId, observationData) {
    try {
      await this.ensureAuthenticated()
      
      const fhirObservation = this.convertToFhirObservation(observationData)
      fhirObservation.id = observationId
      
      const response = await fetch(`${this.baseUrl}/Observation/${observationId}`, {
        method: 'PUT',
        headers: {
          'Authorization': `Bearer ${this.accessToken}`,
          'Content-Type': 'application/fhir+json'
        },
        body: JSON.stringify(fhirObservation)
      })

      if (!response.ok) {
        throw new Error('Erreur lors de la mise à jour de l\'observation')
      }

      return await response.json()
    } catch (error) {
      console.error('Erreur lors de la mise à jour de l\'observation:', error)
      throw error
    }
  }

  /**
   * Rechercher des patients selon des critères
   */
  async searchPatients(searchCriteria) {
    try {
      await this.ensureAuthenticated()
      
      const params = new URLSearchParams()
      Object.keys(searchCriteria).forEach(key => {
        if (searchCriteria[key]) {
          params.append(key, searchCriteria[key])
        }
      })
      
      const response = await fetch(`${this.baseUrl}/Patient?${params}`, {
        headers: {
          'Authorization': `Bearer ${this.accessToken}`,
          'Content-Type': 'application/fhir+json'
        }
      })

      if (!response.ok) {
        throw new Error('Erreur lors de la recherche de patients')
      }

      return await response.json()
    } catch (error) {
      console.error('Erreur lors de la recherche de patients:', error)
      throw error
    }
  }

  /**
   * Récupérer les ressources d'un bundle FHIR
   */
  async getBundleResources(bundleId) {
    try {
      await this.ensureAuthenticated()
      
      const response = await fetch(`${this.baseUrl}/Bundle/${bundleId}`, {
        headers: {
          'Authorization': `Bearer ${this.accessToken}`,
          'Content-Type': 'application/fhir+json'
        }
      })

      if (!response.ok) {
        throw new Error(`Erreur lors de la récupération du bundle ${bundleId}`)
      }

      return await response.json()
    } catch (error) {
      console.error('Erreur lors de la récupération du bundle:', error)
      throw error
    }
  }

  /**
   * Convertir les données en format FHIR Observation
   */
  convertToFhirObservation(data) {
    return {
      resourceType: 'Observation',
      status: data.status || 'final',
      category: [{
        coding: [{
          system: 'http://terminology.hl7.org/CodeSystem/observation-category',
          code: data.category || 'exam',
          display: data.categoryDisplay || 'Examination'
        }]
      }],
      code: {
        coding: [{
          system: data.codeSystem || 'http://loinc.org',
          code: data.code,
          display: data.codeDisplay
        }]
      },
      subject: {
        reference: `Patient/${data.patientId}`
      },
      effectiveDateTime: data.effectiveDateTime,
      valueQuantity: data.valueQuantity ? {
        value: data.valueQuantity.value,
        unit: data.valueQuantity.unit,
        system: data.valueQuantity.system || 'http://unitsofmeasure.org',
        code: data.valueQuantity.code
      } : undefined,
      valueCodeableConcept: data.valueCodeableConcept ? {
        coding: [{
          system: data.valueCodeableConcept.system,
          code: data.valueCodeableConcept.code,
          display: data.valueCodeableConcept.display
        }]
      } : undefined,
      interpretation: data.interpretation ? [{
        coding: [{
          system: 'http://terminology.hl7.org/CodeSystem/v3-ObservationInterpretation',
          code: data.interpretation.code,
          display: data.interpretation.display
        }]
      }] : undefined,
      note: data.note ? [{
        text: data.note
      }] : undefined
    }
  }

  /**
   * Convertir une ressource FHIR en format utilisable
   */
  convertFromFhir(fhirResource) {
    switch (fhirResource.resourceType) {
      case 'Patient':
        return this.convertPatient(fhirResource)
      case 'Observation':
        return this.convertObservation(fhirResource)
      case 'Condition':
        return this.convertCondition(fhirResource)
      case 'Procedure':
        return this.convertProcedure(fhirResource)
      case 'MedicationRequest':
        return this.convertMedication(fhirResource)
      default:
        return fhirResource
    }
  }

  /**
   * Convertir un Patient FHIR
   */
  convertPatient(fhirPatient) {
    return {
      id: fhirPatient.id,
      identifier: fhirPatient.identifier?.map(id => ({
        system: id.system,
        value: id.value
      })),
      name: fhirPatient.name?.[0] ? {
        given: fhirPatient.name[0].given,
        family: fhirPatient.name[0].family
      } : null,
      birthDate: fhirPatient.birthDate,
      gender: fhirPatient.gender,
      address: fhirPatient.address?.[0] ? {
        line: fhirPatient.address[0].line,
        city: fhirPatient.address[0].city,
        state: fhirPatient.address[0].state,
        postalCode: fhirPatient.address[0].postalCode,
        country: fhirPatient.address[0].country
      } : null,
      telecom: fhirPatient.telecom?.map(t => ({
        system: t.system,
        value: t.value,
        use: t.use
      }))
    }
  }

  /**
   * Convertir une Observation FHIR
   */
  convertObservation(fhirObservation) {
    return {
      id: fhirObservation.id,
      status: fhirObservation.status,
      category: fhirObservation.category?.[0]?.coding?.[0]?.display,
      code: fhirObservation.code?.coding?.[0]?.display,
      subject: fhirObservation.subject?.reference,
      effectiveDateTime: fhirObservation.effectiveDateTime,
      value: fhirObservation.valueQuantity ? {
        value: fhirObservation.valueQuantity.value,
        unit: fhirObservation.valueQuantity.unit
      } : fhirObservation.valueCodeableConcept?.coding?.[0]?.display,
      interpretation: fhirObservation.interpretation?.[0]?.coding?.[0]?.display,
      note: fhirObservation.note?.[0]?.text
    }
  }

  /**
   * Convertir une Condition FHIR
   */
  convertCondition(fhirCondition) {
    return {
      id: fhirCondition.id,
      clinicalStatus: fhirCondition.clinicalStatus?.coding?.[0]?.display,
      verificationStatus: fhirCondition.verificationStatus?.coding?.[0]?.display,
      category: fhirCondition.category?.[0]?.coding?.[0]?.display,
      code: fhirCondition.code?.coding?.[0]?.display,
      subject: fhirCondition.subject?.reference,
      onsetDateTime: fhirCondition.onsetDateTime,
      severity: fhirCondition.severity?.coding?.[0]?.display
    }
  }

  /**
   * Convertir une Procedure FHIR
   */
  convertProcedure(fhirProcedure) {
    return {
      id: fhirProcedure.id,
      status: fhirProcedure.status,
      code: fhirProcedure.code?.coding?.[0]?.display,
      subject: fhirProcedure.subject?.reference,
      performedDateTime: fhirProcedure.performedDateTime,
      performer: fhirProcedure.performer?.[0]?.actor?.display
    }
  }

  /**
   * Convertir une MedicationRequest FHIR
   */
  convertMedication(fhirMedication) {
    return {
      id: fhirMedication.id,
      status: fhirMedication.status,
      intent: fhirMedication.intent,
      medicationCodeableConcept: fhirMedication.medicationCodeableConcept?.coding?.[0]?.display,
      subject: fhirMedication.subject?.reference,
      authoredOn: fhirMedication.authoredOn,
      dosageInstruction: fhirMedication.dosageInstruction?.[0]?.text
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
   * Valider les données selon les standards FHIR
   */
  validateFhirData(data, resourceType) {
    const validators = {
      Patient: this.validatePatient,
      Observation: this.validateObservation,
      Condition: this.validateCondition
    }

    const validator = validators[resourceType]
    if (!validator) {
      throw new Error(`Type de ressource FHIR non supporté: ${resourceType}`)
    }

    return validator(data)
  }

  /**
   * Valider un Patient FHIR
   */
  validatePatient(patientData) {
    if (!patientData.resourceType || patientData.resourceType !== 'Patient') {
      throw new Error('Type de ressource invalide pour Patient')
    }

    if (!patientData.name || patientData.name.length === 0) {
      throw new Error('Le patient doit avoir au moins un nom')
    }

    return true
  }

  /**
   * Valider une Observation FHIR
   */
  validateObservation(observationData) {
    if (!observationData.resourceType || observationData.resourceType !== 'Observation') {
      throw new Error('Type de ressource invalide pour Observation')
    }

    if (!observationData.code) {
      throw new Error('L\'observation doit avoir un code')
    }

    if (!observationData.subject) {
      throw new Error('L\'observation doit avoir un sujet')
    }

    return true
  }

  /**
   * Valider une Condition FHIR
   */
  validateCondition(conditionData) {
    if (!conditionData.resourceType || conditionData.resourceType !== 'Condition') {
      throw new Error('Type de ressource invalide pour Condition')
    }

    if (!conditionData.code) {
      throw new Error('La condition doit avoir un code')
    }

    if (!conditionData.subject) {
      throw new Error('La condition doit avoir un sujet')
    }

    return true
  }
}

// Export singleton
export default new FhirService() 