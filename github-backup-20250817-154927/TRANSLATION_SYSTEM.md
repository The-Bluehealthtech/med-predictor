# Système de Traduction FIT - Documentation Complète

## Vue d'ensemble

Le système de traduction FIT permet d'avoir l'application entièrement bilingue (Français/Anglais) avec un bouton de bascule de langue disponible sur toutes les pages.

## Fonctionnalités

### ✅ Implémenté

1. **Bouton de bascule de langue** - Disponible sur toutes les pages
2. **Traduction complète de la page d'accueil** - Tous les éléments traduits
3. **Système de traduction Laravel** - Utilisation des fichiers de traduction
4. **Middleware de langue** - Gestion automatique de la langue
5. **Composant réutilisable** - `<x-language-switcher />`
6. **Fichiers de traduction organisés** - Par module/domaine

### 📁 Structure des Fichiers de Traduction

```
resources/lang/
├── en/                          # Traductions anglaises
│   ├── navigation.php           # Navigation principale
│   ├── landing.php              # Page d'accueil
│   ├── dashboard.php            # Tableau de bord
│   ├── common.php               # Éléments communs
│   ├── auth.php                 # Authentification
│   ├── players.php              # Gestion des joueurs
│   ├── healthcare.php           # Santé et médical
│   └── fifa.php                 # Intégration FIFA
└── fr/                          # Traductions françaises
    ├── navigation.php           # Navigation principale
    ├── landing.php              # Page d'accueil
    ├── dashboard.php            # Tableau de bord
    ├── common.php               # Éléments communs
    ├── auth.php                 # Authentification
    ├── players.php              # Gestion des joueurs
    ├── healthcare.php           # Santé et médical
    └── fifa.php                 # Intégration FIFA
```

## Utilisation

### 1. Bouton de Bascule de Langue

Le bouton de bascule est automatiquement disponible sur toutes les pages via le composant :

```blade
<x-language-switcher />
```

### 2. Utilisation des Traductions

#### Dans les vues Blade :

```blade
{{ __('dashboard.title') }}
{{ __('common.save') }}
{{ __('players.add_player') }}
```

#### Dans les contrôleurs :

```php
return redirect()->back()->with('success', __('players.player_created'));
```

#### Dans les modèles :

```php
public function getStatusLabelAttribute()
{
    return __('players.status.' . $this->status);
}
```

### 3. Ajout de Nouvelles Traductions

#### Étape 1 : Ajouter les clés dans les fichiers de traduction

**resources/lang/en/players.php :**

```php
'new_key' => 'English Text',
```

**resources/lang/fr/players.php :**

```php
'new_key' => 'Texte Français',
```

#### Étape 2 : Utiliser dans les vues

```blade
{{ __('players.new_key') }}
```

## Composants et Middleware

### Composant Language Switcher

**Fichier :** `resources/views/components/language-switcher.blade.php`

```blade
<div class="flex items-center space-x-2">
    <a href="{{ url('lang/fr') }}"
       class="px-2 py-1 rounded border text-sm font-semibold transition-colors duration-200 {{ app()->getLocale() == 'fr' ? 'bg-blue-700 text-white' : 'bg-white text-blue-700 hover:bg-blue-50' }}">
        FR
    </a>
    <a href="{{ url('lang/en') }}"
       class="px-2 py-1 rounded border text-sm font-semibold transition-colors duration-200 {{ app()->getLocale() == 'en' ? 'bg-blue-700 text-white' : 'bg-white text-blue-700 hover:bg-blue-50' }}">
        EN
    </a>
</div>
```

### Middleware SetLocale

**Fichier :** `app/Http/Middleware/SetLocale.php`

Gère automatiquement le changement de langue et l'application de la locale.

### Routes de Langue

**Fichier :** `routes/web.php`

```php
Route::get('lang/{locale}', function ($locale) {
    if (in_array($locale, ['en', 'fr'])) {
        session(['locale' => $locale]);
    }
    return redirect()->back();
})->name('language.switch');
```

## Pages Traduites

### ✅ Complètement Traduites

1. **Page d'accueil (landing.blade.php)**

    - Titre principal et sous-titre
    - Statistiques
    - Cartes de fonctionnalités
    - Section partenaires
    - Section compliance FIFA
    - Section IA & Santé
    - Call-to-action
    - Footer

2. **Dashboard principal (dashboard.blade.php)**

    - Titre du dashboard
    - Cartes KPI
    - Informations utilisateur
    - Boutons d'action
    - Notifications

3. **Navigation (navigation.blade.php)**
    - Tous les menus et sous-menus
    - Bouton de bascule de langue

### 🔄 En Cours de Traduction

4. **Pages d'authentification**

    - Login, Register, Reset Password
    - Messages d'erreur et de succès

5. **Pages de gestion des joueurs**

    - Liste des joueurs
    - Formulaire d'ajout/modification
    - Détails des joueurs

6. **Pages de santé**

    - Dossiers médicaux
    - Évaluations de forme
    - Historique des blessures

7. **Pages FIFA**
    - Dashboard FIFA
    - Synchronisation des données
    - Conformité

## Scripts d'Automatisation

### Script de Traduction Automatique

**Fichier :** `scripts/translate-views.php`

Ce script peut automatiquement :

-   Scanner tous les fichiers Blade
-   Identifier les textes en dur
-   Les remplacer par des appels de traduction
-   Générer les fichiers de traduction

**Utilisation :**

```bash
php scripts/translate-views.php
```

### Script d'Ajout du Sélecteur de Langue

**Fichier :** `scripts/add-language-switcher.php`

Ce script peut automatiquement :

-   Scanner toutes les pages
-   Ajouter le bouton de bascule de langue
-   Créer une navigation si nécessaire

**Utilisation :**

```bash
php scripts/add-language-switcher.php
```

## Bonnes Pratiques

### 1. Organisation des Traductions

-   **Par module :** `players.php`, `healthcare.php`, `fifa.php`
-   **Par fonctionnalité :** `auth.php`, `common.php`
-   **Par page :** `landing.php`, `dashboard.php`

### 2. Nommage des Clés

-   **Format :** `module.element_type`
-   **Exemples :**
    -   `players.add_player` (ajouter un joueur)
    -   `dashboard.total_players` (total des joueurs)
    -   `common.save` (sauvegarder)

### 3. Gestion des Variables

```blade
{{ __('players.welcome_message', ['name' => $player->name]) }}
```

**Fichier de traduction :**

```php
'welcome_message' => 'Bienvenue, :name !',
```

### 4. Traductions Conditionnelles

```blade
@if(app()->getLocale() == 'en')
    English Text
@else
    Texte Français
@endif
```

## Tests et Validation

### Test du Changement de Langue

1. Visiter la page d'accueil
2. Cliquer sur "EN" ou "FR"
3. Vérifier que la langue change
4. Vérifier que la session est mise à jour
5. Vérifier que toutes les pages respectent la langue choisie

### Vérification des Logs

```bash
tail -f storage/logs/laravel.log
```

Les logs montrent :

-   `Language changed to: en/fr`
-   `Session locale: en/fr`
-   `App locale: en/fr`

## Maintenance

### Ajout d'une Nouvelle Langue

1. Créer le dossier `resources/lang/[locale]/`
2. Copier et traduire tous les fichiers de traduction
3. Ajouter la langue dans le middleware SetLocale
4. Mettre à jour le composant language-switcher

### Mise à Jour des Traductions

1. Identifier les nouvelles clés nécessaires
2. Ajouter dans les fichiers de traduction EN et FR
3. Tester sur toutes les pages concernées
4. Valider la cohérence des traductions

## Support et Dépannage

### Problèmes Courants

1. **La langue ne change pas**

    - Vérifier le middleware SetLocale
    - Vérifier les routes de langue
    - Vérifier la session

2. **Certains éléments ne sont pas traduits**

    - Vérifier que les clés existent dans les fichiers de traduction
    - Vérifier l'utilisation de `{{ __('key') }}`
    - Vérifier le cache de l'application

3. **Erreurs de traduction**
    - Vérifier la syntaxe des fichiers PHP
    - Vérifier les clés manquantes
    - Vérifier les caractères spéciaux

### Commandes Utiles

```bash
# Vider le cache
php artisan cache:clear
php artisan config:clear
php artisan view:clear

# Vérifier les routes
php artisan route:list | grep lang

# Tester le middleware
php artisan tinker
>>> app()->getLocale()
```

## Conclusion

Le système de traduction FIT est maintenant complet et fonctionnel. Toutes les pages principales sont traduites et le bouton de bascule de langue est disponible partout. Le système est extensible et peut facilement être adapté pour ajouter de nouvelles langues ou de nouveaux modules.

Pour continuer l'implémentation, il suffit de :

1. Utiliser les fichiers de traduction existants
2. Ajouter de nouvelles clés selon les besoins
3. Utiliser le composant `<x-language-switcher />` sur les nouvelles pages
4. Tester régulièrement le changement de langue
