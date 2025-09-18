"""
Agent IA pour FIT CLI
"""

from typing import Optional, Dict, Any
from rich.console import Console

from .base import BaseAgent

console = Console()

class AIAgent(BaseAgent):
    """Agent IA pour l'assistance et le monitoring"""
    
    def _get_role(self) -> str:
        return "ai"
    
    def summarize(self, type: str, player_id: Optional[int]) -> str:
        """Générer un résumé automatique"""
        self._log_action("summarize", {'type': type, 'player_id': player_id})
        
        if not self.check_permission('create:summaries'):
            return "❌ [red]Permission refusée: create:summaries[/red]"
        
        if not self._validate_player_id(player_id):
            return "❌ [red]ID du joueur requis[/red]"
        
        if type not in ['visit', 'pcma', 'consultation']:
            return "❌ [red]Type non supporté. Utilisez: visit, pcma, ou consultation[/red]"
        
        # Utiliser les vraies routes FIT existantes
        response = self._make_request("POST", "clinical/summarize", json={
            "type": type,
            "player_id": player_id,
            "timestamp": self._get_current_timestamp()
        })
        
        if 'error' in response:
            return self._format_response(response, "Génération de résumé IA")
        
        summary = response.get('summary', '')
        original_length = response.get('original_length', 0)
        summary_length = response.get('summary_length', 0)
        compression_ratio = response.get('compression_ratio', 0)
        
        return f"🤖 [bold]Résumé IA généré[/bold]\n" \
               f"👤 Joueur ID: {player_id}\n" \
               f"📋 Type: {type.title()}\n" \
               f"📊 Compression: {compression_ratio:.1%}\n" \
               f"📝 Résumé:\n[dim]{summary}[/dim]"
    
    def monitor_recovery(self, player_id: Optional[int]) -> str:
        """Surveiller la récupération d'un joueur"""
        self._log_action("monitor_recovery", {'player_id': player_id})
        
        if not self.check_permission('monitor:recovery'):
            return "❌ [red]Permission refusée: monitor:recovery[/red]"
        
        if not self._validate_player_id(player_id):
            return "❌ [red]ID du joueur requis[/red]"
        
        # Utiliser les vraies routes FIT existantes
        response = self._make_request("GET", f"clinical/monitor-recovery/{player_id}")
        
        if 'error' in response:
            return self._format_response(response, "Monitoring de récupération")
        
        recovery_data = response.get('recovery_data', {})
        
        return f"🔍 [bold]Monitoring de Récupération[/bold]\n" \
               f"👤 Joueur ID: {player_id}\n" \
               f"📅 Dernière évaluation: {recovery_data.get('last_evaluation', 'N/A')}\n" \
               f"📈 Progression: {recovery_data.get('progress', 'N/A')}\n" \
               f"⚠️ Alertes: {recovery_data.get('alerts', 0)}\n" \
               f"📝 Recommandations: {recovery_data.get('recommendations', 'Aucune')}"
    
    def detect_care_gaps(self, player_id: Optional[int]) -> str:
        """Détecter les écarts de soins"""
        self._log_action("detect_care_gaps", {'player_id': player_id})
        
        if not self.check_permission('monitor:recovery'):
            return "❌ [red]Permission refusée: monitor:recovery[/red]"
        
        if not self._validate_player_id(player_id):
            return "❌ [red]ID du joueur requis[/red]"
        
        # Utiliser les vraies routes FIT existantes
        response = self._make_request("GET", f"clinical/care-gaps/{player_id}")
        
        if 'error' in response:
            return self._format_response(response, "Détection d'écarts de soins")
        
        care_gaps = response.get('care_gaps', [])
        total_gaps = response.get('total_gaps', 0)
        high_priority_count = response.get('high_priority_count', 0)
        
        if not care_gaps:
            return f"✅ [green]Aucun écart de soins détecté pour le joueur {player_id}[/green]"
        
        gaps_text = []
        for gap in care_gaps:
            priority_color = {
                'high': 'red',
                'medium': 'yellow',
                'low': 'green'
            }.get(gap.get('priority', ''), 'white')
            
            gaps_text.append(
                f"• [{priority_color}]{gap.get('type', 'N/A')}[/{priority_color}]: "
                f"{gap.get('description', 'N/A')} "
                f"(Échéance: {gap.get('due_date', 'N/A')})"
            )
        
        return f"⚠️ [bold]Écarts de Soins Détectés[/bold]\n" \
               f"👤 Joueur ID: {player_id}\n" \
               f"📊 Total: {total_gaps} écarts\n" \
               f"🔴 Priorité haute: {high_priority_count}\n" \
               f"📋 Détails:\n" + "\n".join(gaps_text)
    
    def send_message(self, to: str, text: str) -> str:
        """Envoyer un message contextualisé"""
        self._log_action("send_message", {'to': to, 'text': text})
        
        if not self.check_permission('send:messages'):
            return "❌ [red]Permission refusée: send:messages[/red]"
        
        if not to or not text:
            return "❌ [red]Destinataire et message requis[/red]"
        
        # Utiliser les vraies routes FIT existantes
        response = self._make_request("POST", "clinical/messages", json={
            "to": to,
            "text": text,
            "type": "ai_generated",
            "timestamp": self._get_current_timestamp(),
            "context": "clinical_workflow"
        })
        
        if 'error' in response:
            return self._format_response(response, "Envoi de message")
        
        return f"📤 [green]Message envoyé[/green]\n" \
               f"👤 Destinataire: {to}\n" \
               f"💬 Message: {text}\n" \
               f"🆔 Message ID: {response.get('message_id', 'N/A')}"
    
    def search_evidence(self, condition: str, player_id: Optional[int]) -> str:
        """Rechercher des preuves médicales"""
        self._log_action("search_evidence", {'condition': condition, 'player_id': player_id})
        
        if not self.check_permission('read:all_data'):
            return "❌ [red]Permission refusée: read:all_data[/red]"
        
        if not condition:
            return "❌ [red]Condition requise[/red]"
        
        # Utiliser les vraies routes FIT existantes
        response = self._make_request("GET", "clinical/clinical-trials", params={
            "condition": condition,
            "patient_id": player_id,
            "search_type": "clinical_trials"
        })
        
        if 'error' in response:
            return self._format_response(response, "Recherche de preuves")
        
        trials = response.get('trials', [])
        total_found = response.get('total_found', 0)
        
        if not trials:
            return f"🔍 [yellow]Aucun essai clinique trouvé pour '{condition}'[/yellow]"
        
        trials_text = []
        for trial in trials:
            trials_text.append(
                f"• [bold]{trial.get('title', 'N/A')}[/bold]\n"
                f"  Phase: {trial.get('phase', 'N/A')}\n"
                f"  Statut: {trial.get('status', 'N/A')}\n"
                f"  Localisation: {trial.get('location', 'N/A')}\n"
                f"  Contact: {trial.get('contact', 'N/A')}"
            )
        
        return f"🔬 [bold]Preuves Médicales Trouvées[/bold]\n" \
               f"🏥 Condition: {condition}\n" \
               f"📊 Total trouvé: {total_found} essais\n" \
               f"📋 Essais cliniques:\n" + "\n\n".join(trials_text)
    
    def check_alerts(self, player_id: Optional[int]) -> str:
        """Vérifier les alertes pour un joueur"""
        self._log_action("check_alerts", {'player_id': player_id})
        
        if not self.check_permission('monitor:recovery'):
            return "❌ [red]Permission refusée: monitor:recovery[/red]"
        
        if not self._validate_player_id(player_id):
            return "❌ [red]ID du joueur requis[/red]"
        
        # Utiliser les vraies routes FIT existantes
        response = self._make_request("GET", f"clinical/alerts/{player_id}")
        
        if 'error' in response:
            return self._format_response(response, "Vérification des alertes")
        
        alerts = response.get('alerts', [])
        critical_count = response.get('critical_count', 0)
        warning_count = response.get('warning_count', 0)
        
        if not alerts:
            return f"✅ [green]Aucune alerte pour le joueur {player_id}[/green]"
        
        alerts_text = []
        for alert in alerts:
            severity_color = {
                'critical': 'red',
                'warning': 'yellow',
                'info': 'blue'
            }.get(alert.get('severity', ''), 'white')
            
            alerts_text.append(
                f"• [{severity_color}]{alert.get('severity', 'N/A').upper()}[/{severity_color}]: "
                f"{alert.get('message', 'N/A')} "
                f"(Date: {alert.get('created_at', 'N/A')})"
            )
        
        return f"🚨 [bold]Alertes Détectées[/bold]\n" \
               f"👤 Joueur ID: {player_id}\n" \
               f"🔴 Critiques: {critical_count}\n" \
               f"🟡 Avertissements: {warning_count}\n" \
               f"📋 Détails:\n" + "\n".join(alerts_text)
    
    def generate_clinical_report(self, player_id: Optional[int]) -> str:
        """Générer un rapport clinique complet"""
        self._log_action("generate_clinical_report", {'player_id': player_id})
        
        if not self.check_permission('read:all_data'):
            return "❌ [red]Permission refusée: read:all_data[/red]"
        
        if not self._validate_player_id(player_id):
            return "❌ [red]ID du joueur requis[/red]"
        
        # Utiliser les vraies routes FIT existantes
        response = self._make_request("POST", "clinical/report", json={
            "player_id": player_id,
            "report_type": "comprehensive",
            "include_ai_insights": True
        })
        
        if 'error' in response:
            return self._format_response(response, "Génération de rapport")
        
        report = response.get('report', {})
        
        return f"📊 [bold]Rapport Clinique Généré[/bold]\n" \
               f"👤 Joueur ID: {player_id}\n" \
               f"📅 Date: {report.get('generated_at', 'N/A')}\n" \
               f"📋 Résumé: {report.get('summary', 'N/A')}\n" \
               f"🔍 Insights IA: {report.get('ai_insights', 'N/A')}\n" \
               f"📄 Rapport complet disponible: {report.get('report_url', 'N/A')}"
    
    def analyze_trends(self, player_id: Optional[int], period: str = "30d") -> str:
        """Analyser les tendances médicales"""
        self._log_action("analyze_trends", {'player_id': player_id, 'period': period})
        
        if not self.check_permission('read:all_data'):
            return "❌ [red]Permission refusée: read:all_data[/red]"
        
        if not self._validate_player_id(player_id):
            return "❌ [red]ID du joueur requis[/red]"
        
        # Utiliser les vraies routes FIT existantes
        response = self._make_request("GET", f"clinical/trends/{player_id}", params={
            "player_id": player_id,
            "period": period,
            "analysis_type": "medical_trends"
        })
        
        if 'error' in response:
            return self._format_response(response, "Analyse des tendances")
        
        trends = response.get('trends', {})
        
        return f"📈 [bold]Analyse des Tendances[/bold]\n" \
               f"👤 Joueur ID: {player_id}\n" \
               f"📅 Période: {period}\n" \
               f"📊 Tendances détectées: {trends.get('trends_count', 0)}\n" \
               f"🔍 Détails: {trends.get('analysis', 'N/A')}"
