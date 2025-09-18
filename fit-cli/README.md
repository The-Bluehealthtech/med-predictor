# FIT CLI - Assistant en Ligne de Commande

## 🎯 Vue d'ensemble

FIT CLI est un assistant en ligne de commande permettant aux différents rôles (Joueur/Patient, Clinicien, Agent IA) d'interagir avec les modules existants de l'application FIT.

## 🏗️ Architecture

```
fit-cli/
├── fit_cli/                    # Package principal
│   ├── __init__.py
│   ├── main.py                 # Point d'entrée CLI
│   ├── config.py               # Configuration et authentification
│   ├── agents/                 # Agents spécialisés
│   │   ├── __init__.py
│   │   ├── base.py             # Classe de base pour tous les agents
│   │   ├── patient.py          # Agent Patient (Joueur)
│   │   ├── clinician.py        # Agent Clinicien
│   │   └── ai.py               # Agent IA
│   ├── api/                    # Clients API
│   │   ├── __init__.py
│   │   ├── fit_client.py       # Client principal FIT
│   │   ├── fhir_client.py      # Client FHIR
│   │   └── auth_client.py      # Client d'authentification
│   ├── models/                 # Modèles de données
│   │   ├── __init__.py
│   │   ├── fhir.py             # Modèles FHIR
│   │   └── fit.py              # Modèles FIT
│   └── utils/                  # Utilitaires
│       ├── __init__.py
│       ├── validators.py       # Validation des données
│       └── formatters.py       # Formatage des réponses
├── tests/                      # Tests unitaires
│   ├── __init__.py
│   ├── test_agents.py
│   ├── test_api.py
│   └── test_integration.py
├── .fitconfig                  # Configuration par défaut
├── requirements.txt            # Dépendances Python
├── Dockerfile                  # Containerisation
└── docker-compose.yml          # Déploiement local
```

## 🔑 Rôles et Agents

### 👤 Agent Patient (Joueur)

- Déclare symptômes ou blessures
- Demande ou consulte ses rendez-vous
- Accède à son dossier ou demande une seconde opinion

### 🩺 Agent Clinicien (Médecin/Kiné/Secrétaire)

- Crée et gère visites médicales
- Pose diagnostics et crée des plans de soins
- Évalue l'aptitude PCMA et consigne blessures/maladies

### 🤖 Agent IA (Support & Monitoring)

- Génère résumés automatiques de visites ou PCMA
- Détecte les écarts de soins ou retards de reprise
- Envoie des messages contextualisés

## 📡 APIs Intégrées

- **Secrétariat médical** : `/secretary/dashboard`, `/secretary/appointments`
- **Visites médicales** : `/modules/healthcare`, `/modules/medical`
- **PCMA** : `/pcma/dashboard`, `/pcma/create`
- **Workflow clinique** : `/api/clinical/*`
- **FHIR R4** : Patient, Encounter, Condition, CarePlan

## 🚀 Installation

```bash
pip install -r requirements.txt
fit --help
```

## 📖 Utilisation

```bash
# Agent Patient
fit patient symptoms "douleur ischio jambe gauche"
fit patient appointment request --date 2025-09-20 --reason "contrôle PCMA"

# Agent Clinicien
fit clinician visit start --player 10
fit clinician diagnosis add --player 10 --condition "Entorse cheville gauche"

# Agent IA
fit agent summarize visit --player 10
fit agent monitor recovery --player 10
```
