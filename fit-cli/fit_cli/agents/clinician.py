"""
Agent Clinicien pour FIT CLI
"""

from typing import Optional, Dict, Any
from rich.console import Console

from .base import BaseAgent

console = Console()

class ClinicianAgent(BaseAgent):
    """Agent Clinicien pour interagir avec FIT"""
    
    def _get_role(self) -> str:
        return "clinician"
    
    def start_visit(self, player_id: Optional[int]) -> str:
        """Commencer une visite médicale"""
        self._log_action("start_visit", {'player_id': player_id})
        
        if not self.check_permission('read:all_data'):
            return "❌ [red]Permission refusée: read:all_data[/red]"
        
        if not self._validate_player_id(player_id):
            return "❌ [red]ID du joueur requis[/red]"
        
        # Utiliser les vraies routes FIT existantes - modules.medical
        response = self._make_request("POST", "modules/medical", json={
            "player_id": player_id,
            "visit_type": "consultation",
            "status": "in-progress",
            "start_time": self._get_current_timestamp()
        })
        
        if 'error' in response:
            return self._format_response(response, "Début de visite")
        
        return f"✅ [green]Visite médicale commencée[/green]\n" \
               f"👤 Joueur ID: {player_id}\n" \
               f"🆔 Visite ID: {response.get('id', 'N/A')}\n" \
               f"⏰ Heure de début: {self._get_current_timestamp()}"
    
    def end_visit(self, player_id: Optional[int]) -> str:
        """Terminer une visite médicale"""
        self._log_action("end_visit", {'player_id': player_id})
        
        if not self.check_permission('read:all_data'):
            return "❌ [red]Permission refusée: read:all_data[/red]"
        
        if not self._validate_player_id(player_id):
            return "❌ [red]ID du joueur requis[/red]"
        
        # Utiliser les vraies routes FIT existantes - modules.medical
        response = self._make_request("PUT", f"modules/medical/{player_id}/current", json={
            "status": "finished",
            "end_time": self._get_current_timestamp()
        })
        
        if 'error' in response:
            return self._format_response(response, "Fin de visite")
        
        return f"✅ [green]Visite médicale terminée[/green]\n" \
               f"👤 Joueur ID: {player_id}\n" \
               f"⏰ Heure de fin: {self._get_current_timestamp()}"
    
    def list_visits(self) -> str:
        """Lister les visites médicales"""
        self._log_action("list_visits")
        
        if not self.check_permission('read:all_data'):
            return "❌ [red]Permission refusée: read:all_data[/red]"
        
        response = self._make_request("GET", "modules/medical")
        
        if 'error' in response:
            return self._format_response(response, "Liste des visites")
        
        visits = response.get('data', [])
        if not visits:
            return "🏥 [yellow]Aucune visite trouvée[/yellow]"
        
        # Formater les visites pour l'affichage
        result = f"🏥 [green]Visites Médicales ({len(visits)} trouvées)[/green]\n\n"
        for visit in visits:
            player_name = visit.get('player', {}).get('first_name', 'N/A') + ' ' + visit.get('player', {}).get('last_name', 'N/A')
            result += f"📋 [blue]Visite #{visit.get('id', 'N/A')}[/blue]\n"
            result += f"👤 Joueur: {player_name} (ID: {visit.get('player_id', 'N/A')})\n"
            result += f"📅 Date: {visit.get('record_date', 'N/A')}\n"
            result += f"🏥 Type: {visit.get('visit_type', 'N/A')}\n"
            result += f"📊 Statut: {visit.get('status', 'N/A')}\n"
            if visit.get('diagnosis'):
                result += f"🔍 Diagnostic: {visit.get('diagnosis')}\n"
            result += "---\n"
        
        return result
    
    def add_diagnosis(self, player_id: Optional[int], condition: str) -> str:
        """Ajouter un diagnostic"""
        self._log_action("add_diagnosis", {'player_id': player_id, 'condition': condition})
        
        if not self.check_permission('create:diagnosis'):
            return "❌ [red]Permission refusée: create:diagnosis[/red]"
        
        if not self._validate_player_id(player_id):
            return "❌ [red]ID du joueur requis[/red]"
        
        if not condition:
            return "❌ [red]Condition requise[/red]"
        
        # Utiliser les vraies routes FIT existantes - modules.healthcare
        response = self._make_request("POST", "modules/healthcare", json={
            "player_id": player_id,
            "condition": condition,
            "status": "active",
            "diagnosis_date": self._get_current_timestamp()
        })
        
        if 'error' in response:
            return self._format_response(response, "Ajout de diagnostic")
        
        return f"✅ [green]Diagnostic ajouté[/green]\n" \
               f"👤 Joueur ID: {player_id}\n" \
               f"🏥 Condition: {condition}\n" \
               f"🆔 Diagnostic ID: {response.get('id', 'N/A')}"
    
    def list_diagnoses(self, player_id: Optional[int]) -> str:
        """Lister les diagnostics d'un joueur"""
        self._log_action("list_diagnoses", {'player_id': player_id})
        
        if not self.check_permission('read:all_data'):
            return "❌ [red]Permission refusée: read:all_data[/red]"
        
        if not self._validate_player_id(player_id):
            return "❌ [red]ID du joueur requis[/red]"
        
        response = self._make_request("GET", f"modules/healthcare/{player_id}")
        
        if 'error' in response:
            return self._format_response(response, "Liste des diagnostics")
        
        conditions = response.get('entry', [])
        if not conditions:
            return f"🏥 [yellow]Aucun diagnostic trouvé pour le joueur {player_id}[/yellow]"
        
        # Extraire les diagnostics
        diagnoses = []
        for entry in conditions:
            condition = entry.get('resource', {})
            diagnoses.append({
                'id': condition.get('id', 'N/A'),
                'condition': condition.get('code', {}).get('coding', [{}])[0].get('display', 'N/A'),
                'status': condition.get('clinicalStatus', {}).get('coding', [{}])[0].get('display', 'N/A'),
                'date': condition.get('onsetDateTime', 'N/A')
            })
        
        return self._format_response(diagnoses, f"Diagnostics - Joueur {player_id}")
    
    def create_careplan(self, player_id: Optional[int], treatment: str) -> str:
        """Créer un plan de soins"""
        self._log_action("create_careplan", {'player_id': player_id, 'treatment': treatment})
        
        if not self.check_permission('create:careplan'):
            return "❌ [red]Permission refusée: create:careplan[/red]"
        
        if not self._validate_player_id(player_id):
            return "❌ [red]ID du joueur requis[/red]"
        
        if not treatment:
            return "❌ [red]Plan de traitement requis[/red]"
        
        # Utiliser les vraies routes FIT existantes - modules.healthcare
        response = self._make_request("POST", "modules/healthcare/careplans", json={
            "player_id": player_id,
            "treatment": treatment,
            "status": "active",
            "created_date": self._get_current_timestamp()
        })
        
        if 'error' in response:
            return self._format_response(response, "Création de plan de soins")
        
        return f"✅ [green]Plan de soins créé[/green]\n" \
               f"👤 Joueur ID: {player_id}\n" \
               f"💊 Traitement: {treatment}\n" \
               f"🆔 Plan ID: {response.get('id', 'N/A')}"
    
    def evaluate_pcma(self, player_id: Optional[int]) -> str:
        """Évaluer l'aptitude PCMA"""
        self._log_action("evaluate_pcma", {'player_id': player_id})
        
        if not self.check_permission('evaluate:pcma'):
            return "❌ [red]Permission refusée: evaluate:pcma[/red]"
        
        if not self._validate_player_id(player_id):
            return "❌ [red]ID du joueur requis[/red]"
        
        # Utiliser les vraies routes FIT existantes - PCMA
        response = self._make_request("POST", "pcma", json={
            "player_id": player_id,
            "evaluation_date": self._get_current_timestamp(),
            "status": "in_progress"
        })
        
        if 'error' in response:
            return self._format_response(response, "Évaluation PCMA")
        
        return f"✅ [green]Évaluation PCMA démarrée[/green]\n" \
               f"👤 Joueur ID: {player_id}\n" \
               f"🆔 Évaluation ID: {response.get('id', 'N/A')}\n" \
               f"📅 Date: {self._get_current_timestamp()}\n" \
               f"⏳ Statut: En cours"
    
    def report_injury(self, player_id: Optional[int], injury_type: str, severity: str) -> str:
        """Signaler une blessure"""
        self._log_action("report_injury", {
            'player_id': player_id,
            'injury_type': injury_type,
            'severity': severity
        })
        
        if not self.check_permission('create:diagnosis'):
            return "❌ [red]Permission refusée: create:diagnosis[/red]"
        
        if not self._validate_player_id(player_id):
            return "❌ [red]ID du joueur requis[/red]"
        
        if not injury_type:
            return "❌ [red]Type de blessure requis[/red]"
        
        # Utiliser les vraies routes FIT existantes - modules.medical
        response = self._make_request("POST", "modules/medical/injuries", json={
            "player_id": player_id,
            "injury_type": injury_type,
            "severity": severity,
            "injury_date": self._get_current_timestamp()
        })
        
        if 'error' in response:
            return self._format_response(response, "Signalement de blessure")
        
        return f"✅ [green]Blessure signalée[/green]\n" \
               f"👤 Joueur ID: {player_id}\n" \
               f"🏥 Type: {injury_type}\n" \
               f"⚠️ Sévérité: Grade {severity}\n" \
               f"🆔 Diagnostic ID: {response.get('id', 'N/A')}"
    
    def create_appointment(self, player_id: Optional[int], date: str, reason: str) -> str:
        """Créer un rendez-vous (Secrétaire)"""
        self._log_action("create_appointment", {
            'player_id': player_id,
            'date': date,
            'reason': reason
        })
        
        if not self.check_permission('manage:appointments'):
            return "❌ [red]Permission refusée: manage:appointments[/red]"
        
        if not self._validate_player_id(player_id):
            return "❌ [red]ID du joueur requis[/red]"
        
        if not self._validate_date(date):
            return "❌ [red]Date invalide[/red]"
        
        # Utiliser les vraies routes FIT existantes - secretary
        response = self._make_request("POST", "secretary/appointments", json={
            "player_id": player_id,
            "date": date,
            "reason": reason,
            "status": "scheduled"
        })
        
        if 'error' in response:
            return self._format_response(response, "Création de rendez-vous")
        
        return f"✅ [green]Rendez-vous créé[/green]\n" \
               f"👤 Joueur ID: {player_id}\n" \
               f"📅 Date: {date}\n" \
               f"📝 Raison: {reason}\n" \
               f"🆔 Rendez-vous ID: {response.get('id', 'N/A')}"
