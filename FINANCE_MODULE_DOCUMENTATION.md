# Module Comptabilité / Finances - Documentation

## 🎯 Vue d'ensemble

Le module **Comptabilité / Finances** permet aux clubs et associations de gérer leurs données financières avec des fonctionnalités d'édition avancées et des intégrations avec les logiciels comptables professionnels du marché.

## ✨ Fonctionnalités Principales

### 1. 📊 Dashboard Financier
- **Métriques clés** : Revenus, Dépenses, Bénéfice net, Budget restant
- **Graphiques interactifs** : Répartition des revenus et dépenses
- **Aperçu du budget** avec barre de progression
- **Transactions récentes** avec historique détaillé

### 2. ✏️ Édition des Données
- **Formulaire de transaction** complet avec validation
- **Types de transactions** : Revenus, Dépenses, Transferts
- **Catégories prédéfinies** pour clubs et associations
- **Gestion des statuts** : En attente, Terminé, Annulé
- **Notes et commentaires** pour chaque transaction

### 3. 🔗 Intégrations API Professionnelles

#### Logiciels Supportés :
- **Sage** : Comptabilité professionnelle française
- **QuickBooks** : Solution de gestion financière internationale
- **Xero** : Comptabilité cloud moderne
- **Ciel** : Solutions comptables françaises
- **Excel/CSV** : Import/Export de fichiers
- **API Personnalisée** : Intégration sur mesure

#### Fonctionnalités d'Intégration :
- **Synchronisation bidirectionnelle** (Import/Export)
- **Test de connexion** en temps réel
- **Gestion des erreurs** et notifications
- **Historique des synchronisations**
- **Formatage automatique** des données

## 🛠️ Architecture Technique

### Contrôleur Principal
```php
app/Http/Controllers/FinanceController.php
```
- `index()` : Dashboard principal
- `editTransaction()` : Formulaire d'édition
- `updateTransaction()` : Sauvegarde des données
- `integrations()` : Page des intégrations
- `syncWithExternal()` : Synchronisation API
- `testConnection()` : Test de connexion

### Service d'Intégration
```php
app/Services/FinanceIntegrationService.php
```
- Configuration des APIs externes
- Gestion des tokens d'authentification
- Formatage des données par logiciel
- Gestion des erreurs et retry logic

### Vues
- `resources/views/modules/finance/dashboard.blade.php` : Dashboard principal
- `resources/views/modules/finance/edit-transaction.blade.php` : Formulaire d'édition
- `resources/views/modules/finance/integrations.blade.php` : Page des intégrations
- `resources/views/modules/finance/reports.blade.php` : Rapports détaillés
- `resources/views/modules/finance/budgets.blade.php` : Gestion des budgets

## 🔌 Configuration des APIs

### Sage
```php
'sage' => [
    'base_url' => 'https://api.sage.com/v1',
    'auth_type' => 'oauth2',
    'endpoints' => [
        'transactions' => '/transactions',
        'accounts' => '/accounts',
        'contacts' => '/contacts',
        'reports' => '/reports'
    ]
]
```

### QuickBooks
```php
'quickbooks' => [
    'base_url' => 'https://sandbox-quickbooks.api.intuit.com/v3/company',
    'auth_type' => 'oauth2',
    'endpoints' => [
        'accounts' => '/accounts',
        'transactions' => '/transactions',
        'reports' => '/reports',
        'items' => '/items'
    ]
]
```

### Xero
```php
'xero' => [
    'base_url' => 'https://api.xero.com/api.xro/2.0',
    'auth_type' => 'oauth2',
    'endpoints' => [
        'contacts' => '/Contacts',
        'accounts' => '/Accounts',
        'transactions' => '/Transactions',
        'reports' => '/Reports'
    ]
]
```

## 📋 Routes Disponibles

### Routes Authentifiées
- `GET /finance` : Dashboard principal
- `GET /finance/reports` : Rapports financiers
- `GET /finance/budgets` : Gestion des budgets
- `GET /finance/transaction/{id?}` : Édition de transaction
- `PUT /finance/transaction/{id}` : Mise à jour de transaction
- `GET /finance/integrations` : Page des intégrations
- `POST /finance/sync` : Synchronisation avec API externe
- `POST /finance/test-connection` : Test de connexion

### Routes de Test (Sans Authentification)
- `GET /test-finance` : Dashboard de test
- `GET /test-finance-transaction` : Formulaire d'édition de test
- `GET /test-finance-integrations` : Page d'intégrations de test

## 🎨 Interface Utilisateur

### Dashboard Principal
- **Cartes de métriques** avec indicateurs visuels
- **Graphiques Chart.js** interactifs
- **Section d'actions rapides** avec 4 boutons :
  - 📊 Rapports
  - 💰 Budgets
  - ✏️ Nouvelle Transaction
  - 🔗 Intégrations

### Formulaire d'Édition
- **Design responsive** avec Tailwind CSS
- **Validation côté client** et serveur
- **Catégories organisées** par groupes (Revenus/Dépenses)
- **Interface intuitive** avec icônes et couleurs

### Page des Intégrations
- **Cartes de logiciels** avec statut de connexion
- **Boutons interactifs** pour tester les connexions
- **Historique des synchronisations** en tableau
- **Notifications en temps réel** avec JavaScript

## 🔧 Fonctionnalités JavaScript

### Test de Connexion
```javascript
async function testConnection(software) {
    // Test de connexion avec feedback visuel
    // Notifications de succès/erreur
    // Réinitialisation automatique des boutons
}
```

### Synchronisation des Données
```javascript
async function syncData(software, type) {
    // Synchronisation Import/Export
    // Indicateurs de progression
    // Gestion des erreurs
}
```

### Notifications
```javascript
function showNotification(message, type) {
    // Notifications toast avec auto-dismiss
    // Types : success, error, info
}
```

## 📊 Types de Données Supportées

### Transactions
- **Type** : revenue, expense, transfer
- **Montant** : numérique avec validation
- **Description** : texte libre
- **Catégorie** : prédéfinie par type d'utilisateur
- **Date** : sélecteur de date
- **Statut** : pending, completed, cancelled
- **Notes** : texte optionnel

### Catégories par Type d'Utilisateur

#### Clubs
**Revenus** :
- Recettes match
- Sponsoring
- Merchandising
- Transferts joueurs
- Prix et primes

**Dépenses** :
- Salaires joueurs
- Salaires staff
- Maintenance installations
- Frais de déplacement
- Équipement

#### Associations
**Revenus** :
- Contributions clubs
- Subventions FIFA
- Frais de compétition
- Sponsoring
- Droits TV

**Dépenses** :
- Organisation compétitions
- Développement football
- Administration
- Marketing
- Formation

## 🚀 Utilisation

### 1. Accès au Module
1. Se connecter au système
2. Aller sur `/modules`
3. Cliquer sur la carte "Comptabilité / Finances" (💰)

### 2. Créer une Transaction
1. Cliquer sur "Nouvelle Transaction" dans les actions rapides
2. Remplir le formulaire avec les données
3. Valider pour sauvegarder

### 3. Configurer une Intégration
1. Aller sur la page "Intégrations"
2. Cliquer sur "Configurer" pour le logiciel souhaité
3. Entrer les credentials API
4. Tester la connexion
5. Activer la synchronisation automatique

### 4. Synchroniser les Données
1. Aller sur la page "Intégrations"
2. Cliquer sur "Tester" pour vérifier la connexion
3. Utiliser les boutons d'import/export
4. Consulter l'historique des synchronisations

## 🔒 Sécurité

- **Validation des données** côté client et serveur
- **Tokens d'authentification** pour les APIs externes
- **Gestion des erreurs** avec logs détaillés
- **Protection CSRF** sur tous les formulaires
- **Sanitisation** des entrées utilisateur

## 📈 Évolutions Futures

- **Synchronisation en temps réel** avec webhooks
- **Rapports automatisés** par email
- **Alertes budgétaires** configurables
- **Intégration avec plus de logiciels** comptables
- **API REST** pour intégrations tierces
- **Export PDF** des rapports financiers

## 🐛 Dépannage

### Problèmes Courants
1. **Erreur de connexion API** : Vérifier les credentials et la connectivité
2. **Données non synchronisées** : Contrôler les logs et les permissions
3. **Formulaire non valide** : Vérifier la validation côté client
4. **Performance lente** : Optimiser les requêtes et la mise en cache

### Logs
- **Laravel logs** : `storage/logs/laravel.log`
- **Erreurs API** : Loggées avec contexte détaillé
- **Synchronisations** : Historique complet disponible

---

**Module développé avec Laravel 10, Tailwind CSS, Chart.js et Alpine.js**
