# 🏆 Logos des Ligues Nationales - Documentation FIT

## 📋 Vue d'ensemble

Ce système permet d'afficher automatiquement les logos officiels des ligues nationales de football via l'API API-Football. Les logos sont téléchargés localement et mis à jour automatiquement.

## 🚀 Installation et Configuration

### Prérequis

-   Node.js 16+ installé
-   Clé API API-Football valide
-   Espace disque suffisant (minimum 100MB recommandé)

### Configuration de l'API

1. Obtenir une clé API depuis [API-Football](https://www.api-football.com/)
2. Mettre à jour la clé dans `scripts/update-national-logos.js` :
    ```javascript
    API_KEY: "votre_clé_api_ici";
    ```

## 📁 Structure des Fichiers

```
med-predictor/
├── scripts/
│   ├── update-national-logos.js          # Script principal de téléchargement
│   └── update-national-logos-monthly.sh # Script de mise à jour automatique
├── public/
│   └── associations/                     # Logos téléchargés
│       ├── FR.png                        # Logo France
│       ├── GB-ENG.png                    # Logo Angleterre
│       ├── DE.png                        # Logo Allemagne
│       └── ...
├── resources/js/components/
│   ├── FifaAssociationLogo.vue          # Composant mis à jour
│   └── NationalLeagueLogo.vue           # Nouveau composant
└── docs/
    └── NATIONAL_LEAGUE_LOGOS.md         # Cette documentation
```

## ⚡ Utilisation

### 1. Téléchargement Initial des Logos

```bash
# Lancer le script de téléchargement
node scripts/update-national-logos.js
```

### 2. Mise à Jour Automatique Mensuelle

```bash
# Rendre le script exécutable
chmod +x scripts/update-national-logos-monthly.sh

# Lancer manuellement
./scripts/update-national-logos-monthly.sh

# Programmer avec cron (mensuel)
0 2 1 * * /chemin/vers/med-predictor/scripts/update-national-logos-monthly.sh
```

## 🎨 Composants Vue.js

### FifaAssociationLogo.vue (Mis à jour)

Composant existant mis à jour pour utiliser les logos locaux au lieu des drapeaux.

```vue
<template>
    <FifaAssociationLogo
        :association-code="'FR'"
        :association-name="'France'"
        size="lg"
        :show-fallback="true"
    />
</template>
```

**Props disponibles :**

-   `associationCode` : Code du pays (ex: 'FR', 'GB-ENG')
-   `associationName` : Nom de l'association
-   `size` : Taille du logo ('xs', 'sm', 'md', 'lg', 'xl', '2xl')
-   `showFallback` : Afficher le logo FIFA en cas d'erreur
-   `rounded` : Coins arrondis
-   `shadow` : Ombre portée
-   `style` : Style du composant

### NationalLeagueLogo.vue (Nouveau)

Nouveau composant spécialement conçu pour les logos des ligues nationales.

```vue
<template>
    <NationalLeagueLogo
        :country-code="'FR'"
        :country-name="'France'"
        size="xl"
        :show-tooltip="true"
        :hover="true"
    />
</template>
```

**Props disponibles :**

-   `countryCode` : Code du pays (ex: 'FR', 'GB-ENG')
-   `countryName` : Nom du pays
-   `size` : Taille du logo ('xs', 'sm', 'md', 'lg', 'xl', '2xl')
-   `showTooltip` : Afficher le tooltip au survol
-   `hover` : Effet de survol avec zoom
-   `showFallback` : Afficher le logo FIFA en cas d'erreur
-   `rounded` : Coins arrondis
-   `shadow` : Ombre portée

## 🔧 Fonctionnalités Avancées

### Gestion des Erreurs

-   Fallback automatique vers le logo FIFA générique
-   Gestion des images manquantes
-   Logs détaillés des erreurs

### Performance

-   Cache local des logos
-   Vérification de la fraîcheur des fichiers
-   Téléchargements par lots pour éviter la surcharge

### Sécurité

-   Vérification des types de fichiers
-   Limitation de la taille des fichiers
-   Validation des URLs

## 📊 Métadonnées et Logs

### Fichier de Métadonnées

```json
{
    "lastUpdate": "2025-08-15T09:55:14.079Z",
    "source": "API-Football",
    "totalLeagues": 1188,
    "nationalLeagues": 1022,
    "downloadedLogos": 215,
    "failedLogos": 0,
    "skippedLogos": 807
}
```

### Logs

-   **Principal** : `logs/national-logos-update.log`
-   **Succès** : `logs/national-logos-success-YYYYMMDD.log`
-   **Erreurs** : `logs/national-logos-error-YYYYMMDD.log`

## 🚨 Dépannage

### Problèmes Courants

1. **Clé API invalide**

    ```
    ❌ ERREUR API : 401 Unauthorized
    ```

    **Solution** : Vérifier la clé API dans le script

2. **Espace disque insuffisant**

    ```
    ⚠️  ATTENTION : Espace disque insuffisant
    ```

    **Solution** : Libérer de l'espace ou augmenter la capacité

3. **Erreur de réseau**

    ```
    ❌ ERREUR RÉSEAU : Pas de réponse reçue
    ```

    **Solution** : Vérifier la connectivité internet

4. **Permissions insuffisantes**
    ```
    ❌ ERREUR : Permission denied
    ```
    **Solution** : Vérifier les permissions des répertoires

### Vérifications

```bash
# Vérifier l'espace disque
df -h public/associations/

# Vérifier les permissions
ls -la public/associations/

# Vérifier les logs
tail -f logs/national-logos-update.log

# Tester l'API
curl -H "x-apisports-key: VOTRE_CLE" "https://v3.football.api-sports.io/leagues"
```

## 🔄 Maintenance

### Nettoyage Automatique

-   **Logs** : Supprimés après 30 jours
-   **Rapports** : Supprimés après 90 jours
-   **Fichiers de verrouillage** : Nettoyés automatiquement

### Mise à Jour Manuelle

```bash
# Forcer une mise à jour complète
rm -rf public/associations/*
node scripts/update-national-logos.js
```

### Surveillance

-   Vérifier les logs mensuellement
-   Surveiller l'espace disque
-   Tester périodiquement l'API

## 📈 Statistiques

### Données Actuelles

-   **Total des ligues** : 1188
-   **Ligues nationales** : 1022
-   **Logos disponibles** : 215+
-   **Taille moyenne** : 50-200KB par logo
-   **Espace total** : ~50-100MB

### Performance

-   **Temps de téléchargement** : 2-5 minutes
-   **Taux de succès** : 99%+
-   **Fréquence de mise à jour** : Mensuelle

## 🤝 Support

### Ressources

-   [API-Football Documentation](https://www.api-football.com/documentation)
-   [Vue.js Components](https://vuejs.org/guide/components.html)
-   [Tailwind CSS](https://tailwindcss.com/docs)

### Contact

Pour toute question ou problème :

1. Consulter les logs dans `logs/`
2. Vérifier cette documentation
3. Contacter l'équipe de développement

---

**Dernière mise à jour** : 15 août 2025  
**Version** : 1.0.0  
**Auteur** : Équipe FIT

