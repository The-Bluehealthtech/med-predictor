# Guide d'Utilisation FIT CLI

## 🚀 Installation

### Installation Locale

```bash
# Cloner le repository
git clone <repository-url>
cd fit-cli

# Installer les dépendances
pip install -r requirements.txt

# Rendre le script exécutable
chmod +x fit

# Tester l'installation
./fit --help
```

### Installation avec Docker

```bash
# Construire l'image
docker build -t fit-cli .

# Exécuter le CLI
docker run -it --rm fit-cli --help
```

### Installation avec Docker Compose

```bash
# Démarrer tous les services
docker-compose up -d

# Utiliser le CLI
docker-compose exec fit-cli --help
```

## 🔐 Authentification

### Connexion

```bash
# Se connecter avec nom d'utilisateur et mot de passe
fit login --username "patient@example.com" --password "password" --role "patient"

# Se connecter en tant que clinicien
fit login --username "doctor@example.com" --password "password" --role "clinician"

# Se connecter en tant qu'agent IA
fit login --username "ai@example.com" --password "password" --role "ai"
```

### Vérification du statut

```bash
# Vérifier la connexion
fit status

# Se déconnecter
fit logout
```

## 👤 Agent Patient (Joueur)

### Déclarer des symptômes

```bash
# Symptômes simples
fit patient symptoms "douleur ischio jambe gauche"

# Symptômes avec sévérité
fit patient symptoms "douleur ischio jambe gauche" --severity "severe"

# Symptômes avec durée
fit patient symptoms "douleur ischio jambe gauche" --severity "moderate" --duration "depuis 3 jours"
```

### Gérer les rendez-vous

```bash
# Demander un rendez-vous
fit patient appointment request --date "2025-09-20" --reason "contrôle PCMA"

# Lister les rendez-vous
fit patient appointment list

# Annuler un rendez-vous
fit patient appointment cancel --date "2025-09-20"
```

### Suivi médical

```bash
# Consulter le suivi des visites
fit patient followup

# Partager son dossier pour seconde opinion
fit patient second-opinion share

# Demander une seconde opinion
fit patient second-opinion request
```

## 🩺 Agent Clinicien

### Gérer les visites médicales

```bash
# Commencer une visite
fit clinician visit start --player 10

# Terminer une visite
fit clinician visit end --player 10

# Lister les visites
fit clinician visit list
```

### Diagnostics et plans de soins

```bash
# Ajouter un diagnostic
fit clinician diagnosis add --player 10 --condition "Entorse cheville gauche"

# Lister les diagnostics
fit clinician diagnosis list --player 10

# Créer un plan de soins
fit clinician careplan create --player 10 --treatment "Physiothérapie 2 semaines"
```

### Évaluations PCMA

```bash
# Évaluer l'aptitude PCMA
fit clinician pcma evaluate --player 10

# Lister les évaluations PCMA
fit clinician pcma list --player 10
```

### Gestion des blessures

```bash
# Signaler une blessure
fit clinician injury report --player 10 --type "Ischio" --severity "Grade 2"

# Lister les blessures
fit clinician injury list --player 10
```

### Secrétariat médical

```bash
# Créer un rendez-vous
fit clinician secretary create --player 10 --date "2025-09-21" --reason "Bilan blessure"

# Lister les rendez-vous
fit clinician secretary list
```

## 🤖 Agent IA

### Génération de résumés

```bash
# Résumer une visite
fit agent summarize visit --player 10

# Résumer une évaluation PCMA
fit agent summarize pcma --player 10

# Résumer une consultation
fit agent summarize consultation --player 10
```

### Monitoring et surveillance

```bash
# Surveiller la récupération
fit agent monitor recovery --player 10

# Détecter les écarts de soins
fit agent monitor caregap --player 10

# Vérifier les alertes
fit agent monitor alerts --player 10
```

### Messages et communication

```bash
# Envoyer un message
fit agent message --to "player10" --text "Reprise progressive demain, 20 min vélo"

# Envoyer un message au clinicien
fit agent message --to "clinician" --text "Patient prêt pour la rééducation"
```

### Recherche de preuves

```bash
# Rechercher des preuves médicales
fit agent evidence --condition "entorse cheville" --player 10

# Rechercher des essais cliniques
fit agent evidence --condition "syndrome coronarien aigu"
```

## 📋 Exemples de Workflows Complets

### Workflow : Blessure → Consultation → Plan de soins → Suivi IA

```bash
# 1. Patient déclare des symptômes
fit patient symptoms "douleur cheville gauche" --severity "severe" --duration "1 jour"

# 2. Patient demande un rendez-vous
fit patient appointment request --date "2025-09-20" --reason "blessure cheville"

# 3. Clinicien commence la visite
fit clinician visit start --player 10

# 4. Clinicien pose un diagnostic
fit clinician diagnosis add --player 10 --condition "Entorse cheville gauche Grade 2"

# 5. Clinicien crée un plan de soins
fit clinician careplan create --player 10 --treatment "Physiothérapie 2 semaines, repos relatif"

# 6. IA génère un résumé
fit agent summarize visit --player 10

# 7. IA surveille la récupération
fit agent monitor recovery --player 10

# 8. IA envoie un message de suivi
fit agent message --to "player10" --text "Séance de physiothérapie demain à 10h"
```

### Workflow : Évaluation PCMA → Suivi IA

```bash
# 1. Clinicien évalue l'aptitude PCMA
fit clinician pcma evaluate --player 10

# 2. IA génère un résumé de l'évaluation
fit agent summarize pcma --player 10

# 3. IA détecte les écarts de soins
fit agent monitor caregap --player 10

# 4. IA envoie des recommandations
fit agent message --to "clinician" --text "PCMA expiré dans 30 jours pour le joueur 10"
```

## ⚙️ Configuration

### Fichier de configuration (.fitconfig)

```ini
[api]
base_url = "http://localhost:8000"
api_prefix = "/api"
timeout = 30

[auth]
token_endpoint = "/oauth/token"
client_id = "fit-cli"
client_secret = "fit-cli-secret"
scope = "read write"

[fhir]
base_url = "http://localhost:8000/api/clinical"
version = "R4"

[cli]
default_role = "patient"
output_format = "table"
verbose = false
color = true

[logging]
level = "INFO"
file = "fit-cli.log"
```

### Variables d'environnement

```bash
export FIT_API_URL="http://localhost:8000"
export FIT_FHIR_URL="http://localhost:8000/api/clinical"
export FIT_AUTH_URL="http://localhost:8000/oauth/token"
export FIT_CLI_VERBOSE="false"
export FIT_CLI_COLOR="true"
```

## 🐛 Dépannage

### Problèmes de connexion

```bash
# Vérifier le statut de la connexion
fit status

# Vérifier les logs
tail -f fit-cli.log

# Tester la connectivité
curl -I http://localhost:8000/health
```

### Problèmes d'authentification

```bash
# Se déconnecter et se reconnecter
fit logout
fit login --username "user@example.com" --password "password" --role "patient"

# Vérifier les permissions
fit status
```

### Problèmes de permissions

```bash
# Vérifier le rôle
fit status

# Changer de rôle si nécessaire
fit logout
fit login --username "user@example.com" --password "password" --role "clinician"
```

## 📚 Ressources

- [Documentation API FIT](http://localhost:8000/api/docs)
- [Spécification FHIR R4](https://hl7.org/fhir/R4/)
- [Guide des permissions](http://localhost:8000/docs/permissions)
- [Exemples de workflows](http://localhost:8000/docs/workflows)
