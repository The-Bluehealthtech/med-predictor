# Documentation i18n - FIT Application

## 📋 Vue d'ensemble

Cette documentation décrit le système d'internationalisation (i18n) de l'application FIT, qui supporte le français et l'anglais.

## 📁 Structure des fichiers

- `resources/js/i18n/fr.json` - Traductions françaises
- `resources/js/i18n/en.json` - Traductions anglaises
- `resources/js/i18n/index.js` - Configuration Vue i18n

## 🔑 Organisation des clés

Les clés sont organisées par catégories fonctionnelles :

### 1. Landing Page (`landing`)
Interface d'accueil et présentation de l'application.

### 2. Menu (`menu`)
Navigation principale de l'application.

### 3. Dashboard (`dashboard`)
Tableau de bord principal avec KPIs et actions rapides.

### 4. RPM - Recovery Performance Management (`rpm`)
Module de gestion de la performance et récupération des joueurs.

### 5. DTN - Direction Technique Nationale (`dtn`)
Module de gestion des équipes nationales et sélections.

### 6. Referee (`referee`)
Module de gestion des arbitres et matchs.

### 7. Player (`player`)
Module de gestion des joueurs et profils.

### 8. Common (`common`)
Éléments communs utilisés dans plusieurs modules.

## 📊 Statistiques

- **Total des clés FR:** 609
- **Total des clés EN:** 609
- **Langues supportées:** 2 (FR, EN)
- **Modules couverts:** 8

## 🚀 Utilisation

### Dans les composants Vue.js

```vue
<template>
  <div>
    <h1>{{ $t('dashboard.title') }}</h1>
    <p>{{ $t('dashboard.welcome') }}</p>
  </div>
</template>

<script>
export default {
  name: 'MyComponent',
  methods: {
    showMessage() {
      this.$t('common.loading')
    }
  }
}
</script>
```

### Changement de langue

```javascript
// Changer vers l'anglais
this.$i18n.locale = 'en'

// Changer vers le français
this.$i18n.locale = 'fr'
```

## 🔧 Maintenance

### Ajouter une nouvelle traduction

1. Ajouter la clé dans `fr.json` :
```json
{
  "newModule": {
    "newKey": "Nouvelle valeur"
  }
}
```

2. Ajouter la traduction dans `en.json` :
```json
{
  "newModule": {
    "newKey": "New value"
  }
}
```

### Scripts de maintenance

- `scripts/test-i18n.cjs` - Validation des traductions
- `scripts/fix-translations.cjs` - Correction automatique
- `scripts/cleanup-i18n.cjs` - Nettoyage des fichiers

## 📝 Conventions

1. **Nommage des clés :** Utiliser des noms descriptifs en camelCase
2. **Organisation :** Grouper par module fonctionnel
3. **Cohérence :** Maintenir la même structure dans les deux langues
4. **Commentaires :** Ajouter des commentaires pour les clés complexes

## 🎯 Bonnes pratiques

1. **Toujours utiliser des clés descriptives**
   - ✅ `dashboard.total_matches`
   - ❌ `dashboard.key1`

2. **Grouper les clés logiquement**
   - ✅ `player.profile.name`
   - ❌ `player_name`

3. **Maintenir la cohérence**
   - Vérifier que toutes les clés existent dans les deux langues
   - Utiliser le script de validation régulièrement

4. **Éviter les traductions littérales**
   - Adapter le contenu au contexte culturel
   - Tester avec des locuteurs natifs

## 🔍 Validation

Exécuter régulièrement la validation :

```bash
node scripts/test-i18n.cjs
```

## 📈 Évolution

Le système i18n a été conçu pour être extensible :

- Ajout facile de nouvelles langues
- Support des pluriels et interpolations
- Intégration avec les outils de traduction automatique

## 🤝 Contribution

Pour contribuer aux traductions :

1. Identifier les clés manquantes ou incorrectes
2. Proposer les corrections via les scripts appropriés
3. Tester les modifications
4. Documenter les changements

---

*Dernière mise à jour : 23/07/2025*
