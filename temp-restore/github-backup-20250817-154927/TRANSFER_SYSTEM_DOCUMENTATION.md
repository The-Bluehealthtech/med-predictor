# Système de Gestion des Transferts FIFA - Documentation Technique

## Vue d'ensemble

Ce système implémente une gestion complète des transferts de joueurs avec intégration automatisée des systèmes FIFA ITMS (International Transfer Matching System) et DTMS (Domestic Transfer Matching System).

## Architecture

### Modèles de données

#### 1. Federation

```php
- id (PK)
- name (string)
- country (string)
- fifa_code (string, unique, 3 caractères)
- fifa_connect_id (string, unique)
- website, email, phone, address (string)
- logo_url (string)
- status (enum: active, inactive, suspended)
- fifa_settings (json)
- timestamps
```

#### 2. Transfer

```php
- id (PK)
- player_id (FK -> players)
- club_origin_id (FK -> clubs)
- club_destination_id (FK -> clubs)
- federation_origin_id (FK -> federations)
- federation_destination_id (FK -> federations)
- transfer_type (enum: permanent, loan, free_agent)
- transfer_status (enum: draft, pending, submitted, under_review, approved, rejected, cancelled)
- itc_status (enum: not_requested, requested, pending, approved, rejected, expired)
- transfer_window_start, transfer_window_end (date)
- transfer_date, contract_start_date, contract_end_date (date)
- itc_request_date, itc_response_date (date)
- transfer_fee (decimal)
- currency (string, 3 caractères)
- payment_status (enum: pending, partial, completed)
- fifa_transfer_id, fifa_itc_id (string)
- fifa_payload, fifa_response (json)
- fifa_error_message (text)
- is_minor_transfer, is_international (boolean)
- special_conditions, notes (text)
- created_by, updated_by (FK -> users)
- timestamps
```

#### 3. Contract

```php
- id (PK)
- player_id (FK -> players)
- club_id (FK -> clubs)
- transfer_id (FK -> transfers)
- contract_type (enum: permanent, loan, trial, amateur)
- start_date, end_date (date)
- is_active (boolean)
- salary, bonus (decimal)
- currency (string, 3 caractères)
- payment_frequency (enum: weekly, monthly, yearly)
- clauses, bonuses (json)
- special_conditions (text)
- fifa_contract_id (string, unique)
- fifa_contract_data (json)
- created_by, updated_by (FK -> users)
- timestamps
```

#### 4. TransferDocument

```php
- id (PK)
- transfer_id (FK -> transfers)
- uploaded_by (FK -> users)
- document_type (string)
- document_name (string)
- file_path, file_name (string)
- mime_type (string)
- file_size (integer)
- validation_status (enum: pending, approved, rejected, expired)
- validation_notes (text)
- validated_by (FK -> users)
- validated_at (timestamp)
- fifa_document_id (string)
- fifa_metadata (json)
- timestamps
```

#### 5. TransferPayment

```php
- id (PK)
- transfer_id (FK -> transfers)
- payer_id, payee_id (FK -> clubs)
- payment_type (enum: transfer_fee, training_compensation, solidarity_contribution, other)
- amount (decimal)
- currency (string, 3 caractères)
- payment_method (enum: bank_transfer, check, cash, other)
- payment_status (enum: pending, processing, completed, failed, cancelled)
- due_date, payment_date (date)
- processed_at (timestamp)
- transaction_id, reference_number (string)
- payment_notes (text)
- fifa_payment_id (string)
- fifa_payment_data (json)
- created_by (FK -> users)
- timestamps
```

### Services

#### FifaTransferService

Service principal pour l'intégration avec les API FIFA.

**Méthodes principales :**

-   `createTransfer(Transfer $transfer)` : Créer un transfert dans FIFA
-   `requestItc(Transfer $transfer)` : Demander un ITC
-   `checkItcStatus(Transfer $transfer)` : Vérifier le statut ITC
-   `updateTransfer(Transfer $transfer)` : Mettre à jour un transfert
-   `processWebhook(array $data)` : Traiter les webhooks FIFA

### Contrôleurs

#### TransferController

Gestion des transferts avec méthodes CRUD et intégration FIFA.

**Méthodes principales :**

-   `index()` : Liste des transferts avec filtres
-   `create()` : Formulaire de création
-   `store()` : Créer un transfert
-   `show()` : Afficher un transfert
-   `edit()` : Formulaire d'édition
-   `update()` : Mettre à jour un transfert
-   `destroy()` : Supprimer un transfert
-   `submitToFifa()` : Soumettre à FIFA
-   `checkItcStatus()` : Vérifier statut ITC
-   `statistics()` : Statistiques des transferts

### Composants Vue.js

#### TransferList.vue

Composant principal pour afficher la liste des transferts.

**Fonctionnalités :**

-   Filtres par statut, type, club, joueur
-   Actions : voir, modifier, soumettre, vérifier ITC
-   Pagination
-   Statistiques en temps réel

#### TransferCreateModal.vue

Modal pour créer de nouveaux transferts.

**Fonctionnalités :**

-   Sélection du joueur et des clubs
-   Validation des données
-   Détection automatique des transferts internationaux
-   Gestion des transferts de mineurs

#### DailyPassport.vue

Composant pour le "Passeport du Jour".

**Onglets :**

-   **Club** : Liste des joueurs éligibles avec statuts
-   **Fédération** : Synthèse des transferts et alertes
-   **Joueur** : Historique complet des transferts

## API Endpoints

### Transferts

```
GET    /api/transfers                    # Liste des transferts
POST   /api/transfers                    # Créer un transfert
GET    /api/transfers/{id}               # Détails d'un transfert
PUT    /api/transfers/{id}               # Mettre à jour un transfert
DELETE /api/transfers/{id}               # Supprimer un transfert
POST   /api/transfers/{id}/submit-fifa   # Soumettre à FIFA
POST   /api/transfers/{id}/check-itc     # Vérifier ITC
GET    /api/transfers/statistics         # Statistiques
```

### Documents

```
GET    /api/transfers/{id}/documents           # Liste des documents
POST   /api/transfers/{id}/documents           # Upload document
GET    /api/transfers/{id}/documents/{doc}     # Détails document
PUT    /api/transfers/{id}/documents/{doc}     # Mettre à jour document
DELETE /api/transfers/{id}/documents/{doc}     # Supprimer document
POST   /api/transfers/{id}/documents/{doc}/approve  # Approuver document
POST   /api/transfers/{id}/documents/{doc}/reject   # Rejeter document
```

### Paiements

```
GET    /api/transfers/{id}/payments      # Liste des paiements
POST   /api/transfers/{id}/payments      # Créer un paiement
GET    /api/transfers/{id}/payments/{pay} # Détails paiement
PUT    /api/transfers/{id}/payments/{pay} # Mettre à jour paiement
DELETE /api/transfers/{id}/payments/{pay} # Supprimer paiement
```

### Passeport du Jour

```
GET    /api/clubs/{id}/players/daily-passport    # Passeport club
GET    /api/federations/{id}/daily-passport      # Passeport fédération
GET    /api/players/{id}/transfers               # Historique joueur
```

### Webhooks FIFA

```
POST   /webhooks/fifa                   # Webhook FIFA
```

## Workflow des Transferts

### 1. Création du Transfert

1. Utilisateur remplit le formulaire de transfert
2. Validation des données côté client et serveur
3. Création du transfert en statut "draft"
4. Création automatique du contrat associé

### 2. Upload des Documents

1. Upload des documents requis (passeport, contrat, etc.)
2. Validation des documents par les administrateurs
3. Statut des documents mis à jour

### 3. Soumission à FIFA

1. Vérification que tous les documents sont approuvés
2. Vérification de la fenêtre de transfert
3. Envoi des données à FIFA via API
4. Mise à jour du statut du transfert
5. Si transfert international : demande automatique d'ITC

### 4. Suivi et Validation

1. Suivi en temps réel des réponses FIFA
2. Mise à jour automatique des statuts
3. Notifications en cas d'erreur ou rejet
4. Archivage complet des communications

## Configuration FIFA

### Variables d'environnement

```env
FIFA_API_URL=https://api.fifa.com
FIFA_API_KEY=your_api_key
FIFA_API_SECRET=your_api_secret
FIFA_TIMEOUT=30
FIFA_WEBHOOK_SECRET=your_webhook_secret
FIFA_LOGGING_ENABLED=true
FIFA_LOG_LEVEL=info
FIFA_TESTING_ENABLED=false
FIFA_MOCK_RESPONSES=false
```

### Configuration des fenêtres de transfert

```php
'transfer_windows' => [
    'default' => [
        'summer' => [
            'start' => '07-01', // 1er juillet
            'end' => '08-31',   // 31 août
        ],
        'winter' => [
            'start' => '01-01', // 1er janvier
            'end' => '01-31',   // 31 janvier
        ],
    ],
],
```

## Sécurité

### Authentification

-   Toutes les routes API nécessitent une authentification Sanctum
-   Vérification des permissions utilisateur
-   Validation CSRF pour les formulaires

### Validation des données

-   Validation stricte côté serveur
-   Sanitisation des entrées utilisateur
-   Validation des types de fichiers uploadés

### Audit Trail

-   Logging complet de toutes les actions
-   Traçabilité des modifications
-   Archivage des communications FIFA

## Tests

### Tests unitaires

```bash
php artisan test --filter=TransferTest
php artisan test --filter=FifaTransferServiceTest
```

### Tests d'intégration

```bash
php artisan test --filter=TransferApiTest
php artisan test --filter=FifaIntegrationTest
```

### Tests de bout en bout

```bash
npm run test:e2e
```

## Déploiement

### Prérequis

-   Laravel 11+
-   PHP 8.2+
-   MySQL/PostgreSQL
-   Redis (pour le cache)
-   Node.js 18+ (pour les assets)

### Installation

```bash
# Installation des dépendances
composer install
npm install

# Configuration de la base de données
php artisan migrate

# Compilation des assets
npm run build

# Configuration des tâches cron
php artisan schedule:work
```

### Tâches cron

```bash
# Vérification quotidienne des ITC
0 9 * * * php artisan transfers:check-itc-status

# Nettoyage des transferts expirés
0 2 * * * php artisan transfers:cleanup-expired

# Synchronisation avec FIFA
*/30 * * * * php artisan fifa:sync-transfers
```

## Maintenance

### Monitoring

-   Logs d'erreur FIFA
-   Métriques de performance API
-   Alertes en cas d'échec de synchronisation

### Sauvegarde

-   Sauvegarde quotidienne de la base de données
-   Archivage des documents uploadés
-   Sauvegarde des logs FIFA

### Mise à jour

-   Vérification des changements d'API FIFA
-   Tests de régression avant déploiement
-   Rollback plan en cas de problème

## Support

### Documentation utilisateur

-   Guide d'utilisation des transferts
-   FAQ sur les problèmes courants
-   Tutoriels vidéo

### Support technique

-   Tickets de support
-   Documentation API
-   Exemples d'intégration

### Formation

-   Sessions de formation utilisateurs
-   Documentation administrateur
-   Procédures d'urgence
