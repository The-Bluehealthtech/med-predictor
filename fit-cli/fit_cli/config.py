"""
Configuration et authentification pour FIT CLI
"""

import os
import json
import configparser
from pathlib import Path
from typing import Optional, Dict, Any
import httpx
from rich.console import Console

console = Console()

class Config:
    """Gestionnaire de configuration pour FIT CLI"""
    
    def __init__(self, config_file: Optional[str] = None):
        self.config_file = config_file or self._get_default_config_path()
        self.config = configparser.ConfigParser()
        self.token: Optional[str] = None
        self.user_info: Optional[Dict[str, Any]] = None
        
        # Charger la configuration
        self._load_config()
        
        # Initialiser le client HTTP
        self.client = httpx.Client(
            base_url=self.base_url,
            timeout=self.timeout,
            headers=self._get_default_headers()
        )
    
    def _get_default_config_path(self) -> str:
        """Obtenir le chemin par défaut du fichier de configuration"""
        home = Path.home()
        config_dir = home / ".fit"
        config_dir.mkdir(exist_ok=True)
        return str(config_dir / ".fitconfig")
    
    def _load_config(self):
        """Charger la configuration depuis le fichier"""
        if os.path.exists(self.config_file):
            self.config.read(self.config_file)
        else:
            # Créer une configuration par défaut
            self._create_default_config()
    
    def _create_default_config(self):
        """Créer une configuration par défaut"""
        self.config['api'] = {
            'base_url': 'http://localhost:8000',
            'api_prefix': '/api',
            'timeout': '30'
        }
        self.config['auth'] = {
            'token_endpoint': '/oauth/token',
            'client_id': 'fit-cli',
            'client_secret': 'fit-cli-secret',
            'scope': 'read write'
        }
        self.config['fhir'] = {
            'base_url': 'http://localhost:8000/api/clinical',
            'version': 'R4'
        }
        self.config['cli'] = {
            'default_role': 'patient',
            'output_format': 'table',
            'verbose': 'false',
            'color': 'true'
        }
        self.config['logging'] = {
            'level': 'INFO',
            'file': 'fit-cli.log'
        }
        
        # Sauvegarder la configuration
        self._save_config()
    
    def _save_config(self):
        """Sauvegarder la configuration"""
        with open(self.config_file, 'w') as f:
            self.config.write(f)
    
    def _get_default_headers(self) -> Dict[str, str]:
        """Obtenir les en-têtes HTTP par défaut"""
        headers = {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'User-Agent': 'FIT-CLI/1.0.0'
        }
        
        if self.token:
            headers['Authorization'] = f'Bearer {self.token}'
        
        return headers
    
    @property
    def base_url(self) -> str:
        """URL de base de l'API FIT"""
        return self.config.get('api', 'base_url', fallback='http://localhost:8000')
    
    @property
    def api_prefix(self) -> str:
        """Préfixe de l'API"""
        return self.config.get('api', 'api_prefix', fallback='/api')
    
    @property
    def timeout(self) -> int:
        """Timeout des requêtes HTTP"""
        return int(self.config.get('api', 'timeout', fallback='30'))
    
    @property
    def fhir_base_url(self) -> str:
        """URL de base pour les APIs FHIR"""
        return self.config.get('fhir', 'base_url', fallback='http://localhost:8000/api/clinical')
    
    @property
    def default_role(self) -> str:
        """Rôle par défaut"""
        return self.config.get('cli', 'default_role', fallback='patient')
    
    @property
    def output_format(self) -> str:
        """Format de sortie par défaut"""
        return self.config.get('cli', 'output_format', fallback='table')
    
    @property
    def verbose(self) -> bool:
        """Mode verbeux"""
        return self.config.getboolean('cli', 'verbose', fallback=False)
    
    @property
    def color(self) -> bool:
        """Couleurs dans la sortie"""
        return self.config.getboolean('cli', 'color', fallback=True)
    
    def authenticate(self, username: str, password: str, role: str = "patient") -> Optional[str]:
        """S'authentifier auprès de FIT"""
        try:
            # Essayer d'abord l'authentification OAuth2
            token = self._oauth_authenticate(username, password)
            if token:
                self.token = token
                self.user_info = {
                    'username': username,
                    'role': role,
                    'authenticated_at': self._get_current_timestamp()
                }
                self._save_auth_info()
                return token
            
            # Fallback: authentification basique
            token = self._basic_authenticate(username, password)
            if token:
                self.token = token
                self.user_info = {
                    'username': username,
                    'role': role,
                    'authenticated_at': self._get_current_timestamp()
                }
                self._save_auth_info()
                return token
            
            return None
            
        except Exception as e:
            console.print(f"❌ Erreur d'authentification: {e}")
            return None
    
    def _oauth_authenticate(self, username: str, password: str) -> Optional[str]:
        """Authentification OAuth2"""
        try:
            token_endpoint = self.config.get('auth', 'token_endpoint', fallback='/oauth/token')
            client_id = self.config.get('auth', 'client_id', fallback='fit-cli')
            client_secret = self.config.get('auth', 'client_secret', fallback='fit-cli-secret')
            
            data = {
                'grant_type': 'password',
                'client_id': client_id,
                'client_secret': client_secret,
                'username': username,
                'password': password,
                'scope': self.config.get('auth', 'scope', fallback='read write')
            }
            
            response = httpx.post(
                f"{self.base_url}{token_endpoint}",
                data=data,
                timeout=self.timeout
            )
            
            if response.status_code == 200:
                token_data = response.json()
                return token_data.get('access_token')
            
            return None
            
        except Exception:
            return None
    
    def _basic_authenticate(self, username: str, password: str) -> Optional[str]:
        """Authentification basique (fallback)"""
        try:
            # Essayer de se connecter avec les credentials
            response = httpx.post(
                f"{self.base_url}/api/auth/login",
                json={'email': username, 'password': password},
                timeout=self.timeout
            )
            
            if response.status_code == 200:
                # Simuler un token pour la démo
                return f"basic_{username}_{self._get_current_timestamp()}"
            
            return None
            
        except Exception:
            return None
    
    def is_authenticated(self) -> bool:
        """Vérifier si l'utilisateur est authentifié"""
        return self.token is not None
    
    def logout(self):
        """Se déconnecter"""
        self.token = None
        self.user_info = None
        self._clear_auth_info()
    
    def _save_auth_info(self):
        """Sauvegarder les informations d'authentification"""
        auth_file = Path(self.config_file).parent / ".fitauth"
        auth_data = {
            'token': self.token,
            'user_info': self.user_info
        }
        
        with open(auth_file, 'w') as f:
            json.dump(auth_data, f)
    
    def _clear_auth_info(self):
        """Effacer les informations d'authentification"""
        auth_file = Path(self.config_file).parent / ".fitauth"
        if auth_file.exists():
            auth_file.unlink()
    
    def _load_auth_info(self):
        """Charger les informations d'authentification"""
        auth_file = Path(self.config_file).parent / ".fitauth"
        if auth_file.exists():
            try:
                with open(auth_file, 'r') as f:
                    auth_data = json.load(f)
                    self.token = auth_data.get('token')
                    self.user_info = auth_data.get('user_info')
            except Exception:
                pass
    
    def _get_current_timestamp(self) -> str:
        """Obtenir le timestamp actuel"""
        from datetime import datetime
        return datetime.now().isoformat()
    
    def get_api_url(self, endpoint: str) -> str:
        """Construire l'URL complète d'un endpoint API"""
        if endpoint.startswith('/'):
            endpoint = endpoint[1:]
        return f"{self.base_url}{self.api_prefix}/{endpoint}"
    
    def get_fhir_url(self, resource: str) -> str:
        """Construire l'URL complète d'une ressource FHIR"""
        if resource.startswith('/'):
            resource = resource[1:]
        return f"{self.fhir_base_url}/{resource}"
    
    def update_config(self, section: str, key: str, value: str):
        """Mettre à jour une valeur de configuration"""
        if not self.config.has_section(section):
            self.config.add_section(section)
        
        self.config.set(section, key, value)
        self._save_config()
    
    def get_config(self, section: str, key: str, fallback: str = None) -> str:
        """Obtenir une valeur de configuration"""
        return self.config.get(section, key, fallback=fallback)
