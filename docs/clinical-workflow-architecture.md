# Architecture Microservices - Workflow Clinique FIT

## Vue d'ensemble

Le workflow clinique FIT implémente un système de santé intelligent basé sur FHIR R4 avec IA générative, suivant le modèle "Generative AI in Clinical Workflow".

## Architecture Microservices

```
┌─────────────────────────────────────────────────────────────────┐
│                        API Gateway (Kong/Ambassador)            │
│                    - Authentication & Authorization             │
│                    - Rate Limiting & Load Balancing              │
│                    - Request Routing                             │
└─────────────────────────────────────────────────────────────────┘
                                │
                                ▼
┌─────────────────────────────────────────────────────────────────┐
│                    Frontend VueJS SPA                           │
│  ┌─────────────┐ ┌─────────────┐ ┌─────────────┐                │
│  │   Patient   │ │  Clinician  │ │   Admin     │                │
│  │   Portal    │ │   Portal    │ │   Portal    │                │
│  └─────────────┘ └─────────────┘ └─────────────┘                │
└─────────────────────────────────────────────────────────────────┘
                                │
                                ▼
┌─────────────────────────────────────────────────────────────────┐
│                    Microservices Backend                        │
│                                                                 │
│  ┌─────────────┐ ┌─────────────┐ ┌─────────────┐              │
│  │   Patient   │ │  Clinical    │ │   AI Agent  │              │
│  │  Service    │ │   Service    │ │   Service   │              │
│  │             │ │              │ │             │              │
│  │ - FHIR      │ │ - FHIR       │ │ - Summarize │              │
│  │   Patient   │ │   Condition  │ │ - Evidence  │              │
│  │ - Symptoms  │ │   CarePlan   │ │ - Education │              │
│  │ - Follow-up │ │   Observation│ │ - Messaging │              │
│  └─────────────┘ └─────────────┘ └─────────────┘              │
│                                                                 │
│  ┌─────────────┐ ┌─────────────┐ ┌─────────────┐              │
│  │ Messaging   │ │ Monitoring  │ │ Clinical    │              │
│  │ Service     │ │ Service     │ │ Trials      │              │
│  │             │ │             │ │ Service     │              │
│  │ - Secure    │ │ - Care Gaps │ │ - Matching  │              │
│  │   Chat      │ │ - Compliance│ │ - Research  │              │
│  │ - Notifications│ │ - Alerts   │ │ - Referrals│              │
│  └─────────────┘ └─────────────┘ └─────────────┘              │
└─────────────────────────────────────────────────────────────────┘
                                │
                                ▼
┌─────────────────────────────────────────────────────────────────┐
│                    Data Layer                                   │
│                                                                 │
│  ┌─────────────┐ ┌─────────────┐ ┌─────────────┐              │
│  │ PostgreSQL  │ │   Redis     │ │   MinIO     │              │
│  │             │ │             │ │             │              │
│  │ - FHIR      │ │ - Sessions  │ │ - Documents │              │
│  │   Server    │ │ - Cache     │ │ - Images    │              │
│  │ - Patient   │ │ - Queues    │ │ - Reports   │              │
│  │   Data      │ │ - Pub/Sub   │ │ - AI Models │              │
│  └─────────────┘ └─────────────┘ └─────────────┘              │
└─────────────────────────────────────────────────────────────────┘
                                │
                                ▼
┌─────────────────────────────────────────────────────────────────┐
│                    External Services                            │
│                                                                 │
│  ┌─────────────┐ ┌─────────────┐ ┌─────────────┐              │
│  │   LLM API   │ │   FHIR      │ │   Clinical  │              │
│  │             │ │   External  │ │   Trials    │              │
│  │ - OpenAI    │ │   Systems   │ │   Database  │              │
│  │ - Claude    │ │ - Hospitals │ │ - Research  │              │
│  │ - Gemini    │ │ - Labs      │ │   Centers   │              │
│  └─────────────┘ └─────────────┘ └─────────────┘              │
└─────────────────────────────────────────────────────────────────┘
```

## Workflow Clinique - Étapes Détaillées

### 1. Patient Lane (Portail Patient)

#### Recherche et Préparation

- **Saisie des symptômes** : Interface intuitive pour décrire les symptômes
- **Recherche d'informations** : IA génère des explications claires
- **Préparation consultation** : Génération de questions pertinentes

#### Post-Consultation

- **Compréhension du diagnostic** : Explications en langage simple
- **Options de traitement** : Présentation vulgarisée des choix
- **Éducation patient** : Documents personnalisés générés par IA

#### Suivi Continu

- **Questions de suivi** : Interface de messagerie sécurisée
- **Seconde opinion** : Préparation des données pour consultation externe

### 2. Clinician Lane (Portail Clinicien)

#### Consultation Initiale

- **Entrée des données** : Interface optimisée pour la saisie rapide
- **Résumé automatique** : IA génère un résumé de la consultation
- **Support décisionnel** : Suggestions basées sur les preuves

#### Revue Clinique et Diagnostic

- **Analyse des symptômes** : Croisement avec bases de données médicales
- **Recherche de preuves** : IA recherche la littérature pertinente
- **Plan de soins** : Génération automatique de CarePlan FHIR

#### Traitement et Essais Cliniques

- **Plan de traitement** : Intégration avec les protocoles standards
- **Matching essais cliniques** : Identification des essais pertinents
- **Référentiels** : Accès aux guidelines et recommandations

#### Référents et Suivi

- **Génération de référents** : Documents automatiques pour spécialistes
- **Suivi continu** : Monitoring des écarts de soins
- **Alertes** : Notifications pour les actions requises

### 3. Agent IA Lane (Service IA)

#### Fonctions Principales

- **Summarization** : Résumés automatiques des consultations
- **Evidence Research** : Recherche dans la littérature médicale
- **Clinical Decision Support** : Suggestions basées sur les preuves
- **Patient Education** : Génération de documents vulgarisés
- **Messaging** : Traduction et clarification des communications
- **Monitoring** : Surveillance des écarts de soins et compliance

## Technologies Utilisées

### Frontend

- **Vue.js 3** avec Composition API
- **Vuetify** pour l'interface utilisateur
- **Vue Router** pour la navigation
- **Pinia** pour la gestion d'état
- **Axios** pour les appels API

### Backend Microservices

- **Node.js** avec Express/Fastify
- **Java Spring Boot** pour les services critiques
- **FHIR R4** pour la standardisation des données
- **PostgreSQL** avec extension FHIR
- **Redis** pour le cache et les sessions

### Infrastructure

- **Kubernetes** pour l'orchestration
- **Docker** pour la containerisation
- **Kong/Ambassador** comme API Gateway
- **Prometheus + Grafana** pour le monitoring
- **ELK Stack** pour les logs

### IA et Intégrations

- **OpenAI GPT-4** pour la génération de texte
- **Claude** pour l'analyse de documents
- **FHIR External Systems** pour l'interopérabilité
- **Clinical Trials APIs** pour la recherche d'essais
