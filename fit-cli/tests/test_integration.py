"""
Tests d'intégration pour FIT CLI
"""

import pytest
from unittest.mock import Mock, patch
from fit_cli.agents.patient import PatientAgent
from fit_cli.agents.clinician import ClinicianAgent
from fit_cli.agents.ai import AIAgent
from fit_cli.config import Config

class TestFITCLIIntegration:
    """Tests d'intégration pour FIT CLI"""
    
    def setup_method(self):
        """Configuration des tests"""
        self.config = Mock(spec=Config)
        self.config.is_authenticated.return_value = True
        self.config.token = "test_token"
        self.config.user_info = {"user_id": "test_user"}
        self.config._make_request = Mock()
        self.config._make_fhir_request = Mock()
    
    def test_patient_declare_symptoms(self):
        """Test de déclaration de symptômes par le patient"""
        agent = PatientAgent(self.config)
        
        # Mock de la réponse API
        self.config._make_fhir_request.return_value = {
            "id": "obs_123",
            "status": "final"
        }
        
        result = agent.declare_symptoms("douleur ischio jambe gauche", "moderate", "3 jours")
        
        assert "✅" in result
        assert "Symptômes déclarés avec succès" in result
        assert "douleur ischio jambe gauche" in result
    
    def test_patient_request_appointment(self):
        """Test de demande de rendez-vous par le patient"""
        agent = PatientAgent(self.config)
        
        # Mock de la réponse API
        self.config._make_request.return_value = {
            "success": True,
            "message": "Rendez-vous créé",
            "id": "apt_123"
        }
        
        result = agent.request_appointment("2025-09-20", "contrôle PCMA")
        
        assert "✅" in result
        assert "Demande de rendez-vous envoyée" in result
        assert "2025-09-20" in result
    
    def test_clinician_start_visit(self):
        """Test de début de visite par le clinicien"""
        agent = ClinicianAgent(self.config)
        
        # Mock de la réponse API
        self.config._make_fhir_request.return_value = {
            "id": "enc_123",
            "status": "in-progress"
        }
        
        result = agent.start_visit(10)
        
        assert "✅" in result
        assert "Visite médicale commencée" in result
        assert "Joueur ID: 10" in result
    
    def test_clinician_add_diagnosis(self):
        """Test d'ajout de diagnostic par le clinicien"""
        agent = ClinicianAgent(self.config)
        
        # Mock de la réponse API
        self.config._make_fhir_request.return_value = {
            "id": "cond_123",
            "status": "active"
        }
        
        result = agent.add_diagnosis(10, "Entorse cheville gauche")
        
        assert "✅" in result
        assert "Diagnostic ajouté" in result
        assert "Entorse cheville gauche" in result
    
    def test_clinician_evaluate_pcma(self):
        """Test d'évaluation PCMA par le clinicien"""
        agent = ClinicianAgent(self.config)
        
        # Mock de la réponse API
        self.config._make_request.return_value = {
            "success": True,
            "id": "pcma_123"
        }
        
        result = agent.evaluate_pcma(10)
        
        assert "✅" in result
        assert "Évaluation PCMA démarrée" in result
        assert "Joueur ID: 10" in result
    
    def test_ai_summarize_visit(self):
        """Test de résumé IA d'une visite"""
        agent = AIAgent(self.config)
        
        # Mock de la réponse API
        self.config._make_request.return_value = {
            "success": True,
            "summary": "Résumé de la visite généré par IA",
            "original_length": 1000,
            "summary_length": 200,
            "compression_ratio": 0.2
        }
        
        result = agent.summarize("visit", 10)
        
        assert "🤖" in result
        assert "Résumé IA généré" in result
        assert "Joueur ID: 10" in result
    
    def test_ai_monitor_recovery(self):
        """Test de monitoring de récupération par l'IA"""
        agent = AIAgent(self.config)
        
        # Mock de la réponse API
        self.config._make_request.return_value = {
            "success": True,
            "recovery_data": {
                "last_evaluation": "2025-01-17",
                "progress": "75%",
                "alerts": 2,
                "recommendations": "Continuer la rééducation"
            }
        }
        
        result = agent.monitor_recovery(10)
        
        assert "🔍" in result
        assert "Monitoring de Récupération" in result
        assert "Joueur ID: 10" in result
    
    def test_ai_detect_care_gaps(self):
        """Test de détection d'écarts de soins par l'IA"""
        agent = AIAgent(self.config)
        
        # Mock de la réponse API
        self.config._make_request.return_value = {
            "success": True,
            "care_gaps": [
                {
                    "type": "vaccination",
                    "description": "Vaccination COVID-19 recommandée",
                    "priority": "high",
                    "due_date": "2025-02-01"
                }
            ],
            "total_gaps": 1,
            "high_priority_count": 1
        }
        
        result = agent.detect_care_gaps(10)
        
        assert "⚠️" in result
        assert "Écarts de Soins Détectés" in result
        assert "Joueur ID: 10" in result
    
    def test_ai_send_message(self):
        """Test d'envoi de message par l'IA"""
        agent = AIAgent(self.config)
        
        # Mock de la réponse API
        self.config._make_request.return_value = {
            "success": True,
            "message_id": "msg_123"
        }
        
        result = agent.send_message("player10", "Reprise progressive demain, 20 min vélo")
        
        assert "📤" in result
        assert "Message envoyé" in result
        assert "player10" in result
    
    def test_ai_search_evidence(self):
        """Test de recherche de preuves médicales par l'IA"""
        agent = AIAgent(self.config)
        
        # Mock de la réponse API
        self.config._make_request.return_value = {
            "success": True,
            "trials": [
                {
                    "title": "Essai clinique pour entorse cheville",
                    "phase": "Phase II",
                    "status": "Recruiting",
                    "location": "Paris, France",
                    "contact": "contact@essai.fr"
                }
            ],
            "total_found": 1
        }
        
        result = agent.search_evidence("entorse cheville", 10)
        
        assert "🔬" in result
        assert "Preuves Médicales Trouvées" in result
        assert "entorse cheville" in result

class TestWorkflowIntegration:
    """Tests de workflow complet"""
    
    def setup_method(self):
        """Configuration des tests"""
        self.config = Mock(spec=Config)
        self.config.is_authenticated.return_value = True
        self.config.token = "test_token"
        self.config.user_info = {"user_id": "test_user"}
        self.config._make_request = Mock()
        self.config._make_fhir_request = Mock()
    
    def test_complete_injury_workflow(self):
        """Test du workflow complet: blessure → consultation → plan de soins → suivi IA"""
        
        # 1. Patient déclare des symptômes
        patient_agent = PatientAgent(self.config)
        self.config._make_fhir_request.return_value = {"id": "obs_123"}
        
        symptoms_result = patient_agent.declare_symptoms("douleur cheville", "severe", "1 jour")
        assert "✅" in symptoms_result
        
        # 2. Patient demande un rendez-vous
        self.config._make_request.return_value = {"success": True, "id": "apt_123"}
        
        appointment_result = patient_agent.request_appointment("2025-09-20", "blessure cheville")
        assert "✅" in appointment_result
        
        # 3. Clinicien commence la visite
        clinician_agent = ClinicianAgent(self.config)
        self.config._make_fhir_request.return_value = {"id": "enc_123"}
        
        visit_result = clinician_agent.start_visit(10)
        assert "✅" in visit_result
        
        # 4. Clinicien pose un diagnostic
        self.config._make_fhir_request.return_value = {"id": "cond_123"}
        
        diagnosis_result = clinician_agent.add_diagnosis(10, "Entorse cheville gauche Grade 2")
        assert "✅" in diagnosis_result
        
        # 5. Clinicien crée un plan de soins
        self.config._make_fhir_request.return_value = {"id": "careplan_123"}
        
        careplan_result = clinician_agent.create_careplan(10, "Physiothérapie 2 semaines, repos relatif")
        assert "✅" in careplan_result
        
        # 6. IA génère un résumé
        ai_agent = AIAgent(self.config)
        self.config._make_request.return_value = {
            "success": True,
            "summary": "Résumé de la consultation généré par IA",
            "compression_ratio": 0.3
        }
        
        summary_result = ai_agent.summarize("visit", 10)
        assert "🤖" in summary_result
        
        # 7. IA surveille la récupération
        self.config._make_request.return_value = {
            "success": True,
            "recovery_data": {
                "progress": "50%",
                "alerts": 1,
                "recommendations": "Continuer la physiothérapie"
            }
        }
        
        recovery_result = ai_agent.monitor_recovery(10)
        assert "🔍" in recovery_result
        
        # 8. IA envoie un message de suivi
        self.config._make_request.return_value = {"success": True, "message_id": "msg_123"}
        
        message_result = ai_agent.send_message("player10", "Séance de physiothérapie demain à 10h")
        assert "📤" in message_result

if __name__ == "__main__":
    pytest.main([__file__])
