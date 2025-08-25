# 🏆 Intégration FIFA TMS (Transfer Matching System)

## Vue d'ensemble

Ce module permet d'intégrer votre application avec l'API officielle FIFA TMS pour récupérer automatiquement les licences et l'historique des transferts des joueurs.

## 🚀 Fonctionnalités

### ✅ Licences FIFA TMS

-   Récupération automatique des licences officielles
-   Historique complet des licences par joueur
-   Statuts des licences (active, expirée, suspendue, etc.)
-   Métadonnées détaillées (catégorie, type de contrat, etc.)

### ✅ Historique des Transferts

-   Suivi complet des mouvements de joueurs
-   Informations sur les clubs de départ et d'arrivée
-   Montants des transferts et devises
-   Fenêtres de transfert et dates de contrat

### ✅ Intégration Automatique

-   Synchronisation avec le système de licences existant
-   Cache intelligent pour optimiser les performances
-   Gestion des erreurs et fallback automatique
-   Mode mock pour le développement

## 🔧 Configuration

### 1. Variables d'environnement

Ajoutez ces variables dans votre fichier `.env` :

```bash
# Configuration FIFA TMS
FIFA_TMS_BASE_URL=https://api.fifa.com/tms/v1
FIFA_TMS_API_KEY=your_fifa_tms_api_key_here
FIFA_TMS_TIMEOUT=15
FIFA_TMS_CACHE_TTL=3600
FIFA_TMS_MOCK_MODE=false
```

### 2. Mode Mock (Développement)

Pour le développement, activez le mode mock :

```bash
FIFA_TMS_MOCK_MODE=true
```

## 📡 API Endpoints

### Test de connectivité

```bash
GET /api/fifa-tms/connectivity
```

### Licences d'un joueur

```bash
GET /api/fifa-tms/player/{playerId}/licenses
```

### Historique des transferts

```bash
GET /api/fifa-tms/player/{playerId}/transfers
```

### Synchronisation complète

```bash
POST /api/fifa-tms/player/{playerId}/sync
```

## 🔄 Intégration avec le système existant

### Service LicenseHistoryAggregator

Le service `LicenseHistoryAggregator` intègre automatiquement les données FIFA TMS :

```php
// Les données FIFA TMS sont automatiquement ajoutées
$licenses = $this->licenseAggregator->getPlayerLicenseHistory($playerId);
```

### Ordre de priorité des sources

1. **Base de données locale** (PlayerLicenseHistory, PlayerLicenses)
2. **API FIFA TMS** (licences officielles)
3. **API FIFA locale** (données de base)
4. **APIs nationales** (futur)

## 🎯 Utilisation

### 1. Vérifier la connectivité

```bash
curl "http://localhost:8001/api/fifa-tms/connectivity"
```

### 2. Récupérer les licences d'un joueur

```bash
curl "http://localhost:8001/api/fifa-tms/player/133/licenses"
```

### 3. Synchroniser toutes les données

```bash
curl -X POST "http://localhost:8001/api/fifa-tms/player/133/sync"
```

## 🔍 Données retournées

### Structure des licences

```json
{
    "date_debut": "2020-07-01",
    "date_fin": null,
    "club": "US Monastir",
    "association": "FTF (Tunisie)",
    "type_licence": "Pro",
    "source_donnee": "FIFA TMS",
    "license_number": "TMS-FIFA133-001",
    "status": "active",
    "metadata": {
        "fifa_tms_id": "MOCK_TMS_001",
        "license_category": "Professional",
        "contract_type": "Permanent"
    }
}
```

### Structure des transferts

```json
{
    "date_transfer": "2020-07-01",
    "club_depart": "Club Africain",
    "club_arrivee": "US Monastir",
    "type_transfer": "Permanent",
    "montant": 500000,
    "devise": "EUR",
    "status": "Completed",
    "source_donnee": "FIFA TMS"
}
```

## 🚨 Gestion des erreurs

### Erreurs courantes

-   **Timeout** : L'API FIFA TMS est lente à répondre
-   **Rate limiting** : Trop de requêtes simultanées
-   **Authentification** : Clé API invalide ou expirée
-   **Joueur sans FIFA ID** : Impossible de récupérer les données

### Fallback automatique

En cas d'erreur, le système :

1. Retourne un tableau vide pour ne pas bloquer l'application
2. Enregistre l'erreur dans les logs
3. Continue avec les autres sources de données

## 📊 Monitoring et logs

### Logs d'information

```php
Log::info("Licences FIFA TMS récupérées pour le joueur {$playerId}", [
    'count' => count($licenses)
]);
```

### Logs d'erreur

```php
Log::error("Erreur lors de la récupération des données FIFA TMS", [
    'player_id' => $playerId,
    'error' => $e->getMessage()
]);
```

## 🔐 Sécurité

### Authentification

-   Utilisation de clés API FIFA officielles
-   Validation des tokens d'authentification
-   Gestion sécurisée des secrets

### Rate Limiting

-   Limitation automatique des requêtes
-   Respect des quotas FIFA
-   Cache intelligent pour réduire les appels

## 🧪 Tests

### Mode Mock

Le mode mock génère des données de test réalistes :

```php
// Licences de test
[
    'date_debut' => '2020-07-01',
    'club' => 'US Monastir',
    'association' => 'FTF (Tunisie)',
    'type_licence' => 'Pro'
]
```

### Tests d'intégration

```bash
# Test de connectivité
php artisan tinker --execute="
    \$service = new \App\Services\FifaTmsLicenseService();
    \$result = \$service->testConnectivity();
    echo 'Connectivité: ' . (\$result['connected'] ? 'OK' : 'ERREUR');
"
```

## 🚀 Déploiement

### 1. Production

```bash
# Désactiver le mode mock
FIFA_TMS_MOCK_MODE=false

# Configurer la vraie API FIFA
FIFA_TMS_BASE_URL=https://api.fifa.com/tms/v1
FIFA_TMS_API_KEY=your_production_api_key
```

### 2. Vérification

```bash
# Tester la connectivité
curl "https://your-domain.com/api/fifa-tms/connectivity"

# Vérifier les logs
tail -f storage/logs/laravel.log | grep "FIFA TMS"
```

## 📞 Support

### Logs utiles

-   **Connectivité** : `/api/fifa-tms/connectivity`
-   **Licences** : `/api/fifa-tms/player/{id}/licenses`
-   **Transferts** : `/api/fifa-tms/player/{id}/transfers`

### Debug

Activez le mode debug dans `.env` :

```bash
APP_DEBUG=true
```

## 🔮 Évolutions futures

-   [ ] Intégration avec d'autres APIs FIFA
-   [ ] Synchronisation bidirectionnelle
-   [ ] Notifications en temps réel
-   [ ] Dashboard de monitoring
-   [ ] Gestion des webhooks FIFA

---

**Note** : Cette intégration nécessite une clé API officielle FIFA TMS. Contactez FIFA pour obtenir vos accès.
