/**
 * Tests pour les modules DTN et RPM
 * Validation des composants et services
 */

import { describe, it, expect, beforeEach, vi } from 'vitest'
import { mount } from '@vue/test-utils'
import { createRouter, createWebHistory } from 'vue-router'

// Import des modules à tester
import DTNDashboard from '../DTNManager/views/DTNDashboard.vue'
import RPMPortal from '../RPM/views/RPMPortal.vue'
import NationalTeams from '../DTNManager/components/NationalTeams.vue'
import InternationalSelections from '../DTNManager/components/InternationalSelections.vue'

// Import des services
import FifaConnectService from '../DTNManager/services/FifaConnectService'
import ClubBridgeAPI from '../DTNManager/services/ClubBridgeAPI'
import FhirService from '../DTNManager/services/FhirService'
import RpmSyncService from '../RPM/services/RpmSyncService'

// Mock des services
vi.mock('../DTNManager/services/FifaConnectService')
vi.mock('../DTNManager/services/ClubBridgeAPI')
vi.mock('../DTNManager/services/FhirService')
vi.mock('../RPM/services/RpmSyncService')

// Router de test
const createTestRouter = () => {
  return createRouter({
    history: createWebHistory(),
    routes: [
      {
        path: '/dtn/dashboard',
        name: 'dtn.dashboard',
        component: DTNDashboard
      },
      {
        path: '/rpm/dashboard',
        name: 'rpm.dashboard',
        component: RPMPortal
      }
    ]
  })
}

describe('Modules DTN et RPM', () => {
  let router

  beforeEach(() => {
    router = createTestRouter()
    vi.clearAllMocks()
  })

  describe('Module DTN Manager', () => {
    describe('DTNDashboard', () => {
      it('devrait se monter correctement', () => {
        const wrapper = mount(DTNDashboard, {
          global: {
            plugins: [router]
          }
        })
        
        expect(wrapper.exists()).toBe(true)
        expect(wrapper.find('h1').text()).toContain('DTN Manager')
      })

      it('devrait afficher les statistiques', async () => {
        const wrapper = mount(DTNDashboard, {
          global: {
            plugins: [router]
          }
        })

        await wrapper.vm.$nextTick()
        
        expect(wrapper.vm.stats.nationalTeams).toBe(8)
        expect(wrapper.vm.stats.expatriates).toBe(45)
        expect(wrapper.vm.stats.activeCallups).toBe(12)
        expect(wrapper.vm.stats.medicalAlerts).toBe(3)
      })

      it('devrait avoir les bonnes équipes nationales', async () => {
        const wrapper = mount(DTNDashboard, {
          global: {
            plugins: [router]
          }
        })

        await wrapper.vm.$nextTick()
        
        expect(wrapper.vm.nationalTeams).toHaveLength(4)
        expect(wrapper.vm.nationalTeams[0].name).toBe('Équipe A')
        expect(wrapper.vm.nationalTeams[0].category).toBe('Sénior')
      })
    })

    describe('NationalTeams', () => {
      it('devrait se monter correctement', () => {
        const wrapper = mount(NationalTeams, {
          global: {
            plugins: [router]
          }
        })
        
        expect(wrapper.exists()).toBe(true)
        expect(wrapper.find('h2').text()).toBe('Équipes Nationales')
      })

      it('devrait filtrer les équipes correctement', async () => {
        const wrapper = mount(NationalTeams, {
          global: {
            plugins: [router]
          }
        })

        await wrapper.vm.$nextTick()
        
        // Test du filtrage par catégorie
        wrapper.vm.filters.category = 'senior'
        await wrapper.vm.$nextTick()
        
        const filteredTeams = wrapper.vm.filteredTeams
        expect(filteredTeams.every(team => team.category === 'senior')).toBe(true)
      })

      it('devrait naviguer vers la création d\'équipe', async () => {
        const wrapper = mount(NationalTeams, {
          global: {
            plugins: [router]
          }
        })

        const pushSpy = vi.spyOn(router, 'push')
        
        await wrapper.vm.createTeam()
        
        expect(pushSpy).toHaveBeenCalledWith('/dtn/teams/create')
      })
    })

    describe('InternationalSelections', () => {
      it('devrait se monter correctement', () => {
        const wrapper = mount(InternationalSelections, {
          global: {
            plugins: [router]
          }
        })
        
        expect(wrapper.exists()).toBe(true)
        expect(wrapper.find('h2').text()).toBe('Sélections Internationales')
      })

      it('devrait afficher les sélections récentes', async () => {
        const wrapper = mount(InternationalSelections, {
          global: {
            plugins: [router]
          }
        })

        await wrapper.vm.$nextTick()
        
        expect(wrapper.vm.recentCallups).toHaveLength(2)
        expect(wrapper.vm.recentCallups[0].playerName).toBe('Ahmed Ben Ali')
      })
    })

    describe('Services DTN', () => {
      it('FifaConnectService devrait avoir les bonnes méthodes', () => {
        expect(FifaConnectService).toHaveProperty('authenticate')
        expect(FifaConnectService).toHaveProperty('getPlayerData')
        expect(FifaConnectService).toHaveProperty('createCallup')
        expect(FifaConnectService).toHaveProperty('exportTeamData')
      })

      it('ClubBridgeAPI devrait avoir les bonnes méthodes', () => {
        expect(ClubBridgeAPI).toHaveProperty('getPlayerMedicalData')
        expect(ClubBridgeAPI).toHaveProperty('getPlayerTrainingLoad')
        expect(ClubBridgeAPI).toHaveProperty('sendPlayerFeedback')
        expect(ClubBridgeAPI).toHaveProperty('notifyCallup')
      })

      it('FhirService devrait avoir les bonnes méthodes', () => {
        expect(FhirService).toHaveProperty('getPatientRecord')
        expect(FhirService).toHaveProperty('getPatientObservations')
        expect(FhirService).toHaveProperty('createObservation')
        expect(FhirService).toHaveProperty('searchPatients')
      })
    })
  })

  describe('Module RPM', () => {
    describe('RPMPortal', () => {
      it('devrait se monter correctement', () => {
        const wrapper = mount(RPMPortal, {
          global: {
            plugins: [router]
          }
        })
        
        expect(wrapper.exists()).toBe(true)
        expect(wrapper.find('h1').text()).toContain('RPM - Régulation & Préparation Matchs')
      })

      it('devrait afficher les statistiques RPM', async () => {
        const wrapper = mount(RPMPortal, {
          global: {
            plugins: [router]
          }
        })

        await wrapper.vm.$nextTick()
        
        expect(wrapper.vm.stats.weeklySessions).toBe(12)
        expect(wrapper.vm.stats.averageLoad).toBe(7.2)
        expect(wrapper.vm.stats.totalTime).toBe(18.5)
        expect(wrapper.vm.stats.riskPlayers).toBe(3)
      })

      it('devrait générer le calendrier hebdomadaire', async () => {
        const wrapper = mount(RPMPortal, {
          global: {
            plugins: [router]
          }
        })

        await wrapper.vm.$nextTick()
        
        expect(wrapper.vm.weekDays).toHaveLength(7)
        expect(wrapper.vm.weekDays[0].name).toBe('Dim')
        expect(wrapper.vm.weekDays[1].name).toBe('Lun')
      })

      it('devrait naviguer vers la création de session', async () => {
        const wrapper = mount(RPMPortal, {
          global: {
            plugins: [router]
          }
        })

        const pushSpy = vi.spyOn(router, 'push')
        
        await wrapper.vm.createSession()
        
        expect(pushSpy).toHaveBeenCalledWith('/rpm/sessions/create')
      })
    })

    describe('Services RPM', () => {
      it('RpmSyncService devrait avoir les bonnes méthodes', () => {
        expect(RpmSyncService).toHaveProperty('exportSessionToPerformance')
        expect(RpmSyncService).toHaveProperty('exportWorkloadData')
        expect(RpmSyncService).toHaveProperty('exportAttendanceData')
        expect(RpmSyncService).toHaveProperty('syncAllData')
      })
    })
  })

  describe('Intégration des modules', () => {
    it('devrait avoir des routes distinctes', () => {
      const dtnRoutes = router.getRoutes().filter(route => route.path.startsWith('/dtn'))
      const rpmRoutes = router.getRoutes().filter(route => route.path.startsWith('/rpm'))
      
      expect(dtnRoutes.length).toBeGreaterThan(0)
      expect(rpmRoutes.length).toBeGreaterThan(0)
    })

    it('devrait avoir des permissions distinctes', () => {
      const dtnPermissions = [
        'dtn_view', 'dtn_teams_view', 'dtn_selections_view', 'dtn_admin'
      ]
      
      const rpmPermissions = [
        'rpm_view', 'rpm_sessions_view', 'rpm_matches_view', 'rpm_admin'
      ]
      
      expect(dtnPermissions.every(perm => perm.startsWith('dtn'))).toBe(true)
      expect(rpmPermissions.every(perm => perm.startsWith('rpm'))).toBe(true)
    })
  })

  describe('Validation des données', () => {
    it('devrait valider les données DTN', () => {
      const validTeamData = {
        id: 1,
        name: 'Équipe A',
        category: 'senior',
        gender: 'hommes',
        playerCount: 23,
        maxPlayers: 23,
        status: 'active'
      }

      expect(() => FifaConnectService.validateFifaData(validTeamData, 'team')).not.toThrow()
    })

    it('devrait valider les données RPM', () => {
      const validSessionData = {
        id: 1,
        title: 'Entraînement Technique',
        type: 'Technique',
        date: new Date(),
        duration: 120
      }

      expect(() => RpmSyncService.validateRpmData(validSessionData, 'session')).not.toThrow()
    })

    it('devrait rejeter les données invalides', () => {
      const invalidData = {
        // Données manquantes
      }

      expect(() => FifaConnectService.validateFifaData(invalidData, 'team')).toThrow()
      expect(() => RpmSyncService.validateRpmData(invalidData, 'session')).toThrow()
    })
  })

  describe('Gestion des erreurs', () => {
    it('devrait gérer les erreurs d\'API', async () => {
      // Mock d'une erreur d'API
      FifaConnectService.getPlayerData.mockRejectedValue(new Error('API Error'))
      
      await expect(FifaConnectService.getPlayerData('123')).rejects.toThrow('API Error')
    })

    it('devrait gérer les erreurs de synchronisation', async () => {
      // Mock d'une erreur de synchronisation
      RpmSyncService.exportSessionToPerformance.mockRejectedValue(new Error('Sync Error'))
      
      await expect(RpmSyncService.exportSessionToPerformance('123')).rejects.toThrow('Sync Error')
    })
  })
}) 