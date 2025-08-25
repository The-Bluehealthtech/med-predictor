# Correction de Traduction - "11-a-side Football"

## Problème Identifié

La traduction anglaise pour "Football 11 à 11" était incorrecte dans certains fichiers, utilisant "11-a-side Men's Football" au lieu de "11-a-side Football".

## Corrections Apportées

### 1. Fichiers de Traduction (i18n)

**Fichiers modifiés :**

-   `resources/js/i18n/en.json`
-   `resources/js/i18n/fr.json`

**Changement :**

```json
// Avant
"common.key167": "11-a-side Men's Football"

// Après
"common.key167": "11-a-side Football"
```

### 2. Composants Vue.js

**Fichiers modifiés :**

-   `resources/js/components/FootballTypeSelector.vue`
-   `resources/js/components/FootballTypeSelector.vue.bak`

**Changements :**

-   Correction des commentaires HTML
-   Correction des titres affichés

### 3. Store JavaScript

**Fichier modifié :**

-   `resources/js/stores/footballTypeStore.js`

**Changement :**

```javascript
// Avant
description: "Women's football with the same rules as men's 11-a-side.";

// Après
description: "Women's football with the same rules as 11-a-side.";
```

### 4. Vue Blade

**Fichier modifié :**

-   `resources/views/profile-selector.blade.php`

**Changement :**

```javascript
// Avant
'11aside': '11-a-side Men\'s'

// Après
'11aside': '11-a-side'
```

### 5. Fichiers de Scripts

**Fichiers modifiés :**

-   `scripts/rename-keys-suggestions.txt`
-   `scripts/i18n-validation-report.json`

**Changements :**

-   Mise à jour des suggestions de renommage
-   Correction du rapport de validation

## Traductions Correctes

### Français

-   **Football 11 à 11** ✅ (déjà correct dans `AccountRequest.php`)

### Anglais

-   **11-a-side Football** ✅ (maintenant corrigé partout)

## Vérification

Toutes les références à "11-a-side Men's Football" ont été remplacées par "11-a-side Football" pour une traduction anglaise correcte et cohérente.

## Impact

Cette correction assure :

1. **Cohérence** dans toutes les traductions anglaises
2. **Précision** de la terminologie footballistique
3. **Uniformité** dans l'interface utilisateur
4. **Conformité** aux standards internationaux

La traduction est maintenant correcte et cohérente dans tout le système.
