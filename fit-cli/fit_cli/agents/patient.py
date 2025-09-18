"""
Agent Patient (Joueur) pour FIT CLI
"""

from typing import Optional, Dict, Any
from rich.console import Console

from .base import BaseAgent

console = Console()

class PatientAgent(BaseAgent):
    """Agent Patient (Joueur) pour interagir avec FIT"""
    
    def _get_role(self) -> str:
        return "patient"
    
    def declare_symptoms(self, symptoms: str, severity: str = "moderate", duration: str = "") -> str:
        """Déclarer des symptômes"""
        self._log_action("declare_symptoms", {
            'symptoms': symptoms,
            'severity': severity,
            'duration': duration
        })
        
        if not self.check_permission('create:symptoms'):
            return "❌ [red]Permission refusée: create:symptoms[/red]"
        
        # Préparer les données FHIR
        fhir_data = {
            "resourceType": "Observation",
            "status": "final",
            "category": [{
                "coding": [{
                    "system": "http://terminology.hl7.org/CodeSystem/observation-category",
                    "code": "symptom",
                    "display": "Symptom"
                }]
            }],
            "code": {
                "coding": [{
                    "system": "http://loinc.org",
                    "code": "75322-8",
                    "display": "Symptom"
                }]
            },
            "valueString": symptoms,
            "component": [
                {
                    "code": {
                        "coding": [{
                            "system": "http://loinc.org",
                            "code": "LA6756-3",
                            "display": "Severity"
                        }]
                    },
                    "valueCodeableConcept": {
                        "coding": [{
                            "system": "http://terminology.hl7.org/CodeSystem/v3-ObservationValue",
                            "code": severity,
                            "display": severity.title()
                        }]
                    }
                }
            ]
        }
        
        if duration:
            fhir_data["component"].append({
                "code": {
                    "coding": [{
                        "system": "http://loinc.org",
                        "code": "LA6757-1",
                        "display": "Duration"
                    }]
                },
                "valueString": duration
            })
        
        # Envoyer à l'API FIT existante (utiliser les vraies routes)
        response = self._make_request("POST", "clinical/symptoms", json={
            "symptoms": symptoms,
            "severity": severity,
            "duration": duration
        })
        
        if 'error' in response:
            return self._format_response(response, "Déclaration de symptômes")
        
        return f"✅ [green]Symptômes déclarés avec succès[/green]\n" \
               f"📝 Symptômes: {symptoms}\n" \
               f"⚠️ Sévérité: {severity}\n" \
               f"⏱️ Durée: {duration or 'Non spécifiée'}"
    
    def request_appointment(self, date: str, reason: str) -> str:
        """Demander un rendez-vous"""
        self._log_action("request_appointment", {
            'date': date,
            'reason': reason
        })
        
        if not self.check_permission('request:appointments'):
            return "❌ [red]Permission refusée: request:appointments[/red]"
        
        if not self._validate_date(date):
            return "❌ [red]Date invalide[/red]"
        
        # Utiliser les vraies routes FIT existantes - secretary
        response = self._make_request("POST", "secretary/appointments", json={
            "date": date,
            "reason": reason,
            "status": "requested"
        })
        
        if 'error' in response:
            return self._format_response(response, "Demande de rendez-vous")
        
        return f"✅ [green]Demande de rendez-vous envoyée[/green]\n" \
               f"📅 Date: {date}\n" \
               f"📝 Raison: {reason}\n" \
               f"⏳ Statut: En attente de confirmation"
    
    def list_appointments(self) -> str:
        """Lister les rendez-vous"""
        self._log_action("list_appointments")
        
        if not self.check_permission('read:appointments'):
            return "❌ [red]Permission refusée: read:appointments[/red]"
        
        response = self._make_request("GET", "secretary/dashboard")
        
        if 'error' in response:
            return self._format_response(response, "Liste des rendez-vous")
        
        # Le dashboard secretary retourne les données dans recentAppointments
        appointments = response.get('recentAppointments', [])
        if not appointments:
            return "📅 [yellow]Aucun rendez-vous trouvé[/yellow]"
        
        return self._format_response(appointments, "Mes Rendez-vous")
    
    def cancel_appointment(self, date: str) -> str:
        """Annuler un rendez-vous"""
        self._log_action("cancel_appointment", {'date': date})
        
        if not self.check_permission('request:appointments'):
            return "❌ [red]Permission refusée: request:appointments[/red]"
        
        if not self._validate_date(date):
            return "❌ [red]Date invalide[/red]"
        
        response = self._make_request("DELETE", f"secretary/appointments/{date}")
        
        if 'error' in response:
            return self._format_response(response, "Annulation de rendez-vous")
        
        return f"✅ [green]Rendez-vous annulé[/green]\n" \
               f"📅 Date: {date}"
    
    def get_followup_visits(self) -> str:
        """Consulter le suivi des visites"""
        self._log_action("get_followup_visits")
        
        if not self.check_permission('read:own_data'):
            return "❌ [red]Permission refusée: read:own_data[/red]"
        
        # Utiliser les vraies routes FIT existantes - health_records
        player_id = self.config.user_info.get('player_id', 1) if self.config.user_info else 1
        response = self._make_request("GET", f"players/{player_id}/health-records")
        
        if 'error' in response:
            return self._format_response(response, "Suivi des visites")
        
        visits = response.get('data', [])
        if not visits:
            return "🏥 [yellow]Aucune visite de suivi trouvée[/yellow]"
        
        return self._format_response(visits, "Mes Visites de Suivi")
    
    def share_for_second_opinion(self) -> str:
        """Partager son dossier pour une seconde opinion"""
        self._log_action("share_for_second_opinion")
        
        if not self.check_permission('read:own_data'):
            return "❌ [red]Permission refusée: read:own_data[/red]"
        
        # Récupérer le dossier médical
        response = self._make_request("GET", "healthcare/medical-record")
        
        if 'error' in response:
            return self._format_response(response, "Partage pour seconde opinion")
        
        medical_record = response.get('medical_record', {})
        
        return f"✅ [green]Dossier médical partagé pour seconde opinion[/green]\n" \
               f"📋 Dossier ID: {medical_record.get('id', 'N/A')}\n" \
               f"📅 Dernière mise à jour: {medical_record.get('updated_at', 'N/A')}\n" \
               f"🔗 Lien de partage: {medical_record.get('share_url', 'Généré automatiquement')}"
    
    def request_second_opinion(self) -> str:
        """Demander une seconde opinion"""
        self._log_action("request_second_opinion")
        
        if not self.check_permission('request:appointments'):
            return "❌ [red]Permission refusée: request:appointments[/red]"
        
        # Créer une demande de seconde opinion
        request_data = {
            "type": "second_opinion",
            "status": "pending",
            "priority": "normal"
        }
        
        response = self._make_request("POST", "healthcare/second-opinion-requests", json=request_data)
        
        if 'error' in response:
            return self._format_response(response, "Demande de seconde opinion")
        
        return f"✅ [green]Demande de seconde opinion envoyée[/green]\n" \
               f"📋 ID de la demande: {response.get('request_id', 'N/A')}\n" \
               f"⏳ Statut: En attente d'évaluation\n" \
               f"📞 Vous serez contacté dans les 48h"
    
    def get_medical_summary(self) -> str:
        """Obtenir un résumé médical"""
        self._log_action("get_medical_summary")
        
        if not self.check_permission('read:own_data'):
            return "❌ [red]Permission refusée: read:own_data[/red]"
        
        # Récupérer le résumé médical
        response = self._make_request("GET", "healthcare/medical-summary")
        
        if 'error' in response:
            return self._format_response(response, "Résumé médical")
        
        summary = response.get('summary', {})
        
        return f"📋 [bold]Résumé Médical[/bold]\n" \
               f"👤 Patient: {summary.get('patient_name', 'N/A')}\n" \
               f"📅 Dernière consultation: {summary.get('last_consultation', 'N/A')}\n" \
               f"🏥 Diagnostiques actifs: {summary.get('active_diagnoses', 0)}\n" \
               f"💊 Traitements en cours: {summary.get('active_treatments', 0)}\n" \
               f"⚠️ Allergies: {summary.get('allergies', 'Aucune connue')}"
    
    def get_pcma_status(self) -> str:
        """Obtenir le statut PCMA"""
        self._log_action("get_pcma_status")
        
        if not self.check_permission('read:own_data'):
            return "❌ [red]Permission refusée: read:own_data[/red]"
        
        # Récupérer le statut PCMA
        response = self._make_request("GET", "pcma/status")
        
        if 'error' in response:
            return self._format_response(response, "Statut PCMA")
        
        pcma_status = response.get('pcma_status', {})
        
        status_color = {
            'apte': 'green',
            'inapte': 'red',
            'en_attente': 'yellow',
            'expiré': 'red'
        }.get(pcma_status.get('status', ''), 'white')
        
        return f"🏥 [bold]Statut PCMA[/bold]\n" \
               f"📋 Statut: [{status_color}]{pcma_status.get('status', 'N/A')}[/{status_color}]\n" \
               f"📅 Date d'évaluation: {pcma_status.get('evaluation_date', 'N/A')}\n" \
               f"📅 Date d'expiration: {pcma_status.get('expiry_date', 'N/A')}\n" \
               f"👨‍⚕️ Médecin évaluateur: {pcma_status.get('evaluator', 'N/A')}\n" \
               f"📝 Commentaires: {pcma_status.get('comments', 'Aucun')}"
