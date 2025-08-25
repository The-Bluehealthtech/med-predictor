# 🔍 Commande d'Audit du Projet Laravel

## 📋 Description

La commande `project:db:audit` est un outil d'audit complet qui analyse votre projet Laravel pour identifier :

- **Tables de base de données** et leurs colonnes
- **Modèles Eloquent** et leur utilisation
- **Vues Blade** et leur utilisation
- **Colonnes orphelines** (jamais utilisées dans le code)

## 🚀 Utilisation

### Commande de base
```bash
php artisan project:db:audit
```

### Exécution en arrière-plan
```bash
php artisan project:db:audit > /dev/null 2>&1 &
```

## 📊 Fonctionnalités

### 1. **Analyse des Tables** 📊
- Liste toutes les tables de la base de données
- Compte le nombre de colonnes et de lignes
- Supporte MySQL, PostgreSQL et SQLite

### 2. **Analyse des Modèles** 🏗️
- Détecte tous les modèles Eloquent dans `app/Models/`
- Identifie les relations définies
- Vérifie l'utilisation dans le code

### 3. **Analyse des Vues** 👁️
- Parcourt `resources/views/` pour les vues Blade
- Détecte les appels `view()` et `View::make()`
- Identifie les vues orphelines

### 4. **Analyse des Colonnes** 🔍
- Vérifie chaque colonne de chaque table
- Ignore les colonnes système (`id`, `created_at`, etc.)
- Recherche l'utilisation dans tout le code

### 5. **Score de Propreté** 📈
- Calcule un score global de 0 à 100%
- Basé sur :
  - % de tables avec modèle associé
  - % de modèles utilisés
  - % de vues utilisées
  - % de colonnes utilisées

## 📁 Fichiers Analysés

La commande parcourt automatiquement :

- `app/Http/Controllers/`
- `app/Http/Middleware/`
- `app/Services/`
- `app/Providers/`
- `routes/`
- `app/Console/Commands/`
- `resources/views/`

## 📝 Rapport Généré

### Format
Le rapport est généré au format JSON dans :
```
storage/logs/db-audit-YYYYMMDD_HHMMSS.log
```

### Structure du Rapport
```json
{
  "audit_summary": {
    "timestamp": "2025-08-24 21:40:07",
    "cleanliness_score": "85.3%",
    "total_tables": 100,
    "total_models": 87,
    "total_views": 0,
    "total_columns": 2298
  },
  "tables_found": [...],
  "orphan_tables": [...],
  "unused_models": [...],
  "views_found": [...],
  "orphan_views": [...],
  "orphan_columns": {...},
  "detailed_tables": [...],
  "detailed_models": [...]
}
```

## 🎯 Cas d'Usage

### 1. **Audit de Maintenance**
```bash
# Exécuter avant une refactorisation
php artisan project:db:audit
```

### 2. **Nettoyage de Code**
```bash
# Identifier les éléments inutilisés
php artisan project:db:audit
# Puis consulter le rapport pour nettoyer
```

### 3. **Vérification de Cohérence**
```bash
# S'assurer que toutes les tables ont des modèles
php artisan project:db:audit
```

### 4. **Intégration CI/CD**
```bash
# Dans un pipeline de déploiement
php artisan project:db:audit
# Vérifier le score de propreté
```

## ⚠️ Limitations

### 1. **Analyse Statique**
- La commande analyse le code source, pas l'exécution
- Les modèles chargés dynamiquement peuvent ne pas être détectés

### 2. **Contexte des Colonnes**
- L'analyse des colonnes est basée sur des patterns simples
- Certaines utilisations complexes peuvent ne pas être détectées

### 3. **Performance**
- L'analyse peut prendre du temps sur de gros projets
- Considérer l'exécution en arrière-plan pour les projets volumineux

## 🔧 Personnalisation

### Modifier les Chemins de Recherche
Éditez `app/Console/Commands/ProjectDbAudit.php` :

```php
private function findUsedModels(): array
{
    $searchPaths = [
        app_path('Http/Controllers'),
        app_path('Http/Middleware'),
        app_path('Services'),
        app_path('Providers'),
        base_path('routes'),
        app_path('Console/Commands'),
        // Ajouter vos chemins personnalisés
        app_path('Custom/Path')
    ];
    // ...
}
```

### Ajouter des Colonnes Système
Modifiez la méthode `analyzeColumns()` :

```php
// Ignorer les colonnes système
if (in_array($columnName, [
    'id', 'created_at', 'updated_at', 'deleted_at',
    'uuid', 'slug' // Ajouter vos colonnes
])) {
    continue;
}
```

## 📊 Interprétation des Résultats

### Score de Propreté
- **90-100%** : Excellent, projet très bien structuré
- **80-89%** : Bon, quelques améliorations possibles
- **70-79%** : Moyen, refactorisation recommandée
- **< 70%** : Critique, nettoyage urgent nécessaire

### Tables Orphelines
- Tables sans modèle Eloquent associé
- Considérer la création de modèles ou la suppression

### Modèles Non Utilisés
- Modèles jamais référencés dans le code
- Vérifier s'ils sont nécessaires

### Vues Orphelines
- Vues jamais appelées par `view()` ou `View::make()`
- Considérer la suppression si obsolètes

### Colonnes Orphelines
- Colonnes jamais utilisées dans le code
- Vérifier l'utilité avant suppression

## 🚨 Bonnes Pratiques

### 1. **Exécution Régulière**
```bash
# Ajouter au cron pour un audit automatique
0 2 * * 0 cd /path/to/project && php artisan project:db:audit
```

### 2. **Avant Refactorisation**
```bash
# Toujours auditer avant de modifier
php artisan project:db:audit
```

### 3. **Documentation**
- Conserver les rapports d'audit
- Suivre l'évolution du score de propreté
- Documenter les actions de nettoyage

## 🔍 Dépannage

### Erreur de Connexion Base
```bash
# Vérifier la configuration database
php artisan config:cache
php artisan project:db:audit
```

### Erreur de Permissions
```bash
# Vérifier les permissions sur storage/logs
chmod -R 755 storage/logs
```

### Commande Non Trouvée
```bash
# Vérifier l'enregistrement dans Kernel.php
php artisan list | grep project
```

## 📚 Ressources

- [Documentation Laravel Commands](https://laravel.com/docs/artisan)
- [Eloquent ORM](https://laravel.com/docs/eloquent)
- [Database Schema](https://laravel.com/docs/migrations)

---

**Note** : Cette commande est un outil d'aide au développement. Les résultats doivent être interprétés avec discernement et validés manuellement avant toute action de suppression.
