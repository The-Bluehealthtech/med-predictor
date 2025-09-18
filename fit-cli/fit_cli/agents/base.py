"""
Classe de base pour tous les agents FIT CLI
"""

from abc import ABC, abstractmethod
from typing import Dict, Any, Optional, List
from rich.console import Console
from rich.table import Table
from rich.panel import Panel
from rich.text import Text

from ..config import Config

console = Console()

class BaseAgent(ABC):
    """Classe de base pour tous les agents FIT CLI"""
    
    def __init__(self, config: Optional[Config] = None):
        self.config = config or Config()
        self.role = self._get_role()
    
    @abstractmethod
    def _get_role(self) -> str:
        """Obtenir le rôle de l'agent"""
        pass
    
    def _make_request(self, method: str, endpoint: str, **kwargs) -> Dict[str, Any]:
        """Effectuer une requête HTTP vers l'API FIT"""
        try:
            url = self.config.get_api_url(endpoint)
            
            # Ajouter l'authentification si disponible
            if self.config.is_authenticated():
                kwargs.setdefault('headers', {})['Authorization'] = f'Bearer {self.config.token}'
            
            response = self.config.client.request(method, url, **kwargs)
            
            if response.status_code == 200:
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
    
    def _make_fhir_request(self, method: str, resource: str, **kwargs) -> Dict[str, Any]:
        """Effectuer une requête FHIR"""
        try:
            url = self.config.get_fhir_url(resource)
            
            # Ajouter l'authentification si disponible
            if self.config.is_authenticated():
                kwargs.setdefault('headers', {})['Authorization'] = f'Bearer {self.config.token}'
            
            response = self.config.client.request(method, url, **kwargs)
            
            if response.status_code == 200:
                return response.json()
            elif response.status_code == 201:
                return response.json()
            else:
                return {'error': f'Erreur FHIR {response.status_code}', 'message': response.text}
                
        except Exception as e:
            return {'error': 'Erreur FHIR', 'message': str(e)}
    
    def _format_response(self, data: Dict[str, Any], title: str = "Résultat") -> str:
        """Formater la réponse pour l'affichage"""
        if 'error' in data:
            return f"❌ [red]{data['error']}[/red]: {data.get('message', 'Erreur inconnue')}"
        
        if isinstance(data, dict) and 'success' in data:
            if data['success']:
                return f"✅ [green]{data.get('message', 'Opération réussie')}[/green]"
            else:
                return f"❌ [red]{data.get('message', 'Opération échouée')}[/red]"
        
        # Formater comme un tableau si c'est une liste
        if isinstance(data, list):
            return self._format_table(data, title)
        
        # Formater comme un panneau si c'est un dictionnaire
        if isinstance(data, dict):
            return self._format_panel(data, title)
        
        return str(data)
    
    def _format_table(self, data: List[Dict[str, Any]], title: str) -> str:
        """Formater les données comme un tableau"""
        if not data:
            return f"📋 [yellow]{title}[/yellow]: Aucune donnée disponible"
        
        table = Table(title=title)
        
        # Ajouter les colonnes basées sur les clés du premier élément
        if data:
            for key in data[0].keys():
                table.add_column(key.replace('_', ' ').title(), style="cyan")
            
            # Ajouter les lignes
            for item in data:
                table.add_row(*[str(item.get(key, '')) for key in data[0].keys()])
        
        return table
    
    def _format_panel(self, data: Dict[str, Any], title: str) -> str:
        """Formater les données comme un panneau"""
        content = []
        for key, value in data.items():
            if isinstance(value, (dict, list)):
                content.append(f"[bold]{key.replace('_', ' ').title()}:[/bold] {len(value)} éléments")
            else:
                content.append(f"[bold]{key.replace('_', ' ').title()}:[/bold] {value}")
        
        panel = Panel(
            "\n".join(content),
            title=title,
            border_style="blue"
        )
        
        return panel
    
    def _validate_player_id(self, player_id: Optional[int]) -> bool:
        """Valider l'ID du joueur"""
        if player_id is None:
            console.print("❌ [red]ID du joueur requis[/red]")
            return False
        
        if not isinstance(player_id, int) or player_id <= 0:
            console.print("❌ [red]ID du joueur invalide[/red]")
            return False
        
        return True
    
    def _validate_date(self, date_str: str) -> bool:
        """Valider le format de date"""
        try:
            from datetime import datetime
            datetime.strptime(date_str, '%Y-%m-%d')
            return True
        except ValueError:
            console.print("❌ [red]Format de date invalide. Utilisez YYYY-MM-DD[/red]")
            return False
    
    def _get_current_timestamp(self) -> str:
        """Obtenir le timestamp actuel"""
        from datetime import datetime
        return datetime.now().isoformat()
    
    def _log_action(self, action: str, details: Dict[str, Any] = None):
        """Logger une action"""
        if self.config.verbose:
            log_data = {
                'agent': self.role,
                'action': action,
                'timestamp': self._get_current_timestamp(),
                'details': details or {}
            }
            console.print(f"📝 [dim]Log: {log_data}[/dim]")
    
    def get_permissions(self) -> List[str]:
        """Obtenir les permissions de l'agent"""
        # Permissions de base
        base_permissions = ['read:profile', 'read:appointments']
        
        # Permissions spécifiques au rôle
        role_permissions = {
            'patient': ['read:own_data', 'create:symptoms', 'request:appointments'],
            'clinician': ['read:all_data', 'create:diagnosis', 'create:careplan', 'evaluate:pcma'],
            'secretary': ['manage:appointments', 'read:schedule'],
            'ai': ['read:all_data', 'create:summaries', 'monitor:recovery', 'send:messages']
        }
        
        return base_permissions + role_permissions.get(self.role, [])
    
    def check_permission(self, permission: str) -> bool:
        """Vérifier si l'agent a une permission"""
        return permission in self.get_permissions()
