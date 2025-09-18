"""
Client API principal pour FIT
"""

import httpx
from typing import Dict, Any, Optional
from rich.console import Console

from ..config import Config

console = Console()

class FitClient:
    """Client pour interagir avec l'API FIT"""
    
    def __init__(self, config: Optional[Config] = None):
        self.config = config or Config()
        self.client = httpx.Client(
            base_url=self.config.base_url,
            timeout=self.config.timeout,
            headers=self._get_headers()
        )
    
    def _get_headers(self) -> Dict[str, str]:
        """Obtenir les en-têtes HTTP"""
        headers = {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'User-Agent': 'FIT-CLI/1.0.0'
        }
        
        if self.config.is_authenticated():
            headers['Authorization'] = f'Bearer {self.config.token}'
        
        return headers
    
    def get(self, endpoint: str, **kwargs) -> Dict[str, Any]:
        """Effectuer une requête GET"""
        return self._make_request('GET', endpoint, **kwargs)
    
    def post(self, endpoint: str, **kwargs) -> Dict[str, Any]:
        """Effectuer une requête POST"""
        return self._make_request('POST', endpoint, **kwargs)
    
    def put(self, endpoint: str, **kwargs) -> Dict[str, Any]:
        """Effectuer une requête PUT"""
        return self._make_request('PUT', endpoint, **kwargs)
    
    def delete(self, endpoint: str, **kwargs) -> Dict[str, Any]:
        """Effectuer une requête DELETE"""
        return self._make_request('DELETE', endpoint, **kwargs)
    
    def _make_request(self, method: str, endpoint: str, **kwargs) -> Dict[str, Any]:
        """Effectuer une requête HTTP"""
        try:
            url = self.config.get_api_url(endpoint)
            
            response = self.client.request(method, url, **kwargs)
            
            if response.status_code in [200, 201]:
                return response.json()
            elif response.status_code == 401:
                return {'error': 'Non autorisé', 'message': 'Veuillez vous connecter'}
            elif response.status_code == 403:
                return {'error': 'Accès refusé', 'message': 'Permissions insuffisantes'}
            elif response.status_code == 404:
                return {'error': 'Non trouvé', 'message': 'Ressource introuvable'}
            else:
                return {'error': f'Erreur HTTP {response.status_code}', 'message': response.text}
                
        except Exception as e:
            return {'error': 'Erreur de connexion', 'message': str(e)}
    
    # Méthodes spécifiques aux modules FIT
    
    def get_health_status(self) -> Dict[str, Any]:
        """Vérifier le statut de santé de l'API"""
        return self.get('health')
    
    def get_player_info(self, player_id: int) -> Dict[str, Any]:
        """Obtenir les informations d'un joueur"""
        return self.get(f'players/{player_id}')
    
    def get_medical_records(self, player_id: int) -> Dict[str, Any]:
        """Obtenir les dossiers médicaux d'un joueur"""
        return self.get(f'healthcare/medical-records/{player_id}')
    
    def create_appointment(self, appointment_data: Dict[str, Any]) -> Dict[str, Any]:
        """Créer un rendez-vous"""
        return self.post('secretary/appointments', json=appointment_data)
    
    def get_appointments(self, player_id: Optional[int] = None) -> Dict[str, Any]:
        """Obtenir les rendez-vous"""
        params = {}
        if player_id:
            params['player_id'] = player_id
        return self.get('secretary/appointments', params=params)
    
    def create_pcma_evaluation(self, pcma_data: Dict[str, Any]) -> Dict[str, Any]:
        """Créer une évaluation PCMA"""
        return self.post('pcma', json=pcma_data)
    
    def get_pcma_status(self, player_id: int) -> Dict[str, Any]:
        """Obtenir le statut PCMA d'un joueur"""
        return self.get(f'pcma/status/{player_id}')
    
    def create_visit(self, visit_data: Dict[str, Any]) -> Dict[str, Any]:
        """Créer une visite médicale"""
        return self.post('healthcare/visits', json=visit_data)
    
    def get_visits(self, player_id: Optional[int] = None) -> Dict[str, Any]:
        """Obtenir les visites médicales"""
        params = {}
        if player_id:
            params['player_id'] = player_id
        return self.get('healthcare/visits', params=params)
    
    def create_diagnosis(self, diagnosis_data: Dict[str, Any]) -> Dict[str, Any]:
        """Créer un diagnostic"""
        return self.post('healthcare/diagnoses', json=diagnosis_data)
    
    def get_diagnoses(self, player_id: int) -> Dict[str, Any]:
        """Obtenir les diagnostics d'un joueur"""
        return self.get(f'healthcare/diagnoses/{player_id}')
    
    def create_careplan(self, careplan_data: Dict[str, Any]) -> Dict[str, Any]:
        """Créer un plan de soins"""
        return self.post('healthcare/careplans', json=careplan_data)
    
    def get_careplans(self, player_id: int) -> Dict[str, Any]:
        """Obtenir les plans de soins d'un joueur"""
        return self.get(f'healthcare/careplans/{player_id}')
    
    def report_injury(self, injury_data: Dict[str, Any]) -> Dict[str, Any]:
        """Signaler une blessure"""
        return self.post('healthcare/injuries', json=injury_data)
    
    def get_injuries(self, player_id: int) -> Dict[str, Any]:
        """Obtenir les blessures d'un joueur"""
        return self.get(f'healthcare/injuries/{player_id}')
    
    def generate_summary(self, summary_data: Dict[str, Any]) -> Dict[str, Any]:
        """Générer un résumé IA"""
        return self.post('clinical/summarize', json=summary_data)
    
    def get_clinical_decision_support(self, decision_data: Dict[str, Any]) -> Dict[str, Any]:
        """Obtenir un support décisionnel clinique"""
        return self.post('clinical/decision-support', json=decision_data)
    
    def monitor_recovery(self, player_id: int) -> Dict[str, Any]:
        """Surveiller la récupération d'un joueur"""
        return self.get(f'clinical/monitor-recovery/{player_id}')
    
    def detect_care_gaps(self, player_id: int) -> Dict[str, Any]:
        """Détecter les écarts de soins"""
        return self.get(f'clinical/care-gaps/{player_id}')
    
    def search_clinical_trials(self, search_params: Dict[str, Any]) -> Dict[str, Any]:
        """Rechercher des essais cliniques"""
        return self.get('clinical/clinical-trials', params=search_params)
    
    def send_message(self, message_data: Dict[str, Any]) -> Dict[str, Any]:
        """Envoyer un message"""
        return self.post('clinical/messages', json=message_data)
    
    def get_alerts(self, player_id: int) -> Dict[str, Any]:
        """Obtenir les alertes d'un joueur"""
        return self.get(f'clinical/alerts/{player_id}')
    
    def generate_report(self, report_data: Dict[str, Any]) -> Dict[str, Any]:
        """Générer un rapport clinique"""
        return self.post('clinical/report', json=report_data)
    
    def analyze_trends(self, player_id: int, period: str = "30d") -> Dict[str, Any]:
        """Analyser les tendances médicales"""
        return self.get(f'clinical/trends/{player_id}', params={'period': period})
    
    def close(self):
        """Fermer le client HTTP"""
        self.client.close()
