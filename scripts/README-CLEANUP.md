# 🧹 Scripts de Nettoyage du Disque Local

Ce dossier contient une collection complète de scripts pour maintenir votre disque local propre et optimisé.

## 📋 Scripts Disponibles

### 1. 🔍 `analyze-disk-usage.sh` - Analyse de l'Espace Disque
**Usage:** Analyse détaillée de l'utilisation de l'espace disque
```bash
chmod +x scripts/analyze-disk-usage.sh
./scripts/analyze-disk-usage.sh
```

**Fonctionnalités:**
- Analyse des dossiers principaux
- Identification des gros dossiers (>100MB, >50MB, >10MB)
- Détection des fichiers volumineux
- Analyse des dossiers de sauvegarde
- Recommandations de nettoyage

### 2. 🚀 `quick-cleanup.sh` - Nettoyage Rapide Quotidien
**Usage:** Nettoyage rapide et sûr pour la maintenance quotidienne
```bash
chmod +x scripts/quick-cleanup.sh
./scripts/quick-cleanup.sh
```

**Fonctionnalités:**
- Nettoyage des caches Laravel
- Suppression des sessions anciennes
- Nettoyage des logs volumineux
- Suppression des fichiers temporaires
- Nettoyage des composants temporaires
- **SÛR:** Ne supprime que les éléments temporaires

### 3. 🧹 `cleanup-disk-space.sh` - Nettoyage Complet
**Usage:** Nettoyage complet et approfondi (⚠️ ATTENTION: Supprime les sauvegardes)
```bash
chmod +x scripts/cleanup-disk-space.sh
./scripts/cleanup-disk-space.sh
```

**Fonctionnalités:**
- Suppression complète des dossiers de sauvegarde
- Nettoyage de tous les caches
- Suppression des fichiers temporaires
- Nettoyage des composants de sauvegarde
- **ATTENTION:** Demande confirmation avant suppression

### 4. 🤖 `auto-maintenance.sh` - Maintenance Automatique
**Usage:** Script automatisé pour la maintenance via cron
```bash
chmod +x scripts/auto-maintenance.sh
./scripts/auto-maintenance.sh
```

**Fonctionnalités:**
- Nettoyage automatique des logs
- Gestion intelligente des caches
- Nettoyage des sessions
- Suppression des fichiers temporaires
- Logging complet des opérations
- Adaptatif selon l'espace disque disponible

## 🎯 Recommandations d'Usage

### 🔄 Maintenance Quotidienne
```bash
# Exécuter le nettoyage rapide
./scripts/quick-cleanup.sh
```

### 📊 Analyse Hebdomadaire
```bash
# Analyser l'utilisation du disque
./scripts/analyze-disk-usage.sh
```

### 🧹 Nettoyage Mensuel
```bash
# Nettoyage complet (avec précaution)
./scripts/cleanup-disk-space.sh
```

### 🤖 Maintenance Automatique
```bash
# Ajouter au crontab pour une exécution automatique
# Exécution quotidienne à 2h00
0 2 * * * /chemin/vers/med-predictor/scripts/auto-maintenance.sh

# Exécution hebdomadaire le dimanche à 3h00
0 3 * * 0 /chemin/vers/med-predictor/scripts/cleanup-disk-space.sh
```

## ⚠️ Précautions Importantes

### 🚨 Avant d'Exécuter `cleanup-disk-space.sh`
1. **Sauvegardez vos données importantes**
2. **Vérifiez que vous n'avez pas besoin des sauvegardes**
3. **Lisez attentivement la liste des éléments à supprimer**
4. **Confirmez avec "oui" seulement si vous êtes sûr**

### 🔒 Éléments Supprimés par le Nettoyage Complet
- Dossier `backups/` complet
- Dossier `final-cleanup-20250827_091649/`
- Dossier `storage/backups/` complet
- Tous les fichiers `.backup`, `.bak`, `.old`
- Caches et fichiers temporaires
- Sessions anciennes
- Logs volumineux

### ✅ Éléments Conservés
- Code source de l'application
- Base de données principale
- Configuration essentielle
- Fichiers de production
- Documentation principale

## 📊 Surveillance de l'Espace Disque

### 🔍 Commandes Utiles
```bash
# Espace disque disponible
df -h

# Taille des dossiers
du -sh *

# Taille d'un dossier spécifique
du -sh backups/

# Recherche de gros fichiers
find . -type f -size +100M -exec ls -lh {} \;

# Recherche de dossiers volumineux
find . -type d -exec du -sh {} + | sort -hr | head -20
```

### 📈 Seuils d'Alerte
- **⚠️ Attention:** >80% d'utilisation
- **🚨 Critique:** >90% d'utilisation
- **🧹 Nettoyage recommandé:** >70% d'utilisation

## 🛠️ Configuration Avancée

### ⚙️ Personnalisation des Scripts
Vous pouvez modifier les variables dans les scripts pour adapter le comportement :

```bash
# Dans auto-maintenance.sh
MAX_LOG_SIZE="10M"           # Taille maximale des logs
MAX_SESSION_AGE="7"          # Âge maximal des sessions (jours)
MAX_CACHE_AGE="3"            # Âge maximal des caches (jours)
MAX_BACKUP_AGE="30"          # Âge maximal des sauvegardes (jours)
```

### 🔧 Intégration avec d'Autres Outils
```bash
# Nettoyage après déploiement
./scripts/quick-cleanup.sh && echo "Nettoyage post-déploiement terminé"

# Nettoyage avant sauvegarde
./scripts/cleanup-disk-space.sh && ./backup-script.sh

# Nettoyage conditionnel
if [ $(df -h . | awk 'NR==2 {print $5}' | sed 's/%//') -gt 80 ]; then
    ./scripts/cleanup-disk-space.sh
fi
```

## 📝 Logs et Rapports

### 📋 Fichiers de Log
- **auto-maintenance.sh:** `/tmp/auto-maintenance.log`
- **Tous les scripts:** Affichage en temps réel dans le terminal

### 📊 Rapports de Nettoyage
Chaque script affiche un résumé détaillé des actions effectuées :
- Nombre de fichiers supprimés
- Espace libéré estimé
- Dossiers nettoyés
- Recommandations

## 🆘 Dépannage

### ❌ Erreurs Courantes
1. **Permission refusée:** `chmod +x scripts/*.sh`
2. **Chemin incorrect:** Vérifiez que vous êtes dans le bon répertoire
3. **Espace insuffisant:** Exécutez d'abord `analyze-disk-usage.sh`

### 🔍 Diagnostic
```bash
# Vérifier les permissions
ls -la scripts/

# Tester un script en mode debug
bash -x scripts/quick-cleanup.sh

# Vérifier l'espace disque
df -h .
```

## 📚 Ressources Supplémentaires

### 🔗 Documentation Laravel
- [Cache Management](https://laravel.com/docs/cache)
- [Session Configuration](https://laravel.com/docs/sessions)
- [Log Management](https://laravel.com/docs/logging)

### 🐧 Commandes Linux Utiles
- `du -sh *` - Taille des dossiers
- `find . -size +100M` - Recherche de gros fichiers
- `ncdu` - Analyse interactive de l'espace disque

## 🤝 Contribution

Pour améliorer ces scripts :
1. Testez sur un environnement de développement
2. Documentez les nouvelles fonctionnalités
3. Mettez à jour ce README
4. Testez sur différents systèmes d'exploitation

---

**⚠️ ATTENTION:** Ces scripts suppriment définitivement des fichiers. Utilisez-les avec précaution et assurez-vous d'avoir des sauvegardes avant utilisation.

**📧 Support:** En cas de problème, vérifiez les logs et la documentation avant de demander de l'aide.











