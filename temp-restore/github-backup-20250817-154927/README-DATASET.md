# 🎯 DATASET - 50 JOUEURS LIGUE PROFESSIONNELLE 1 TUNISIE 2024-2025

## 📋 Description

Ce dataset contient les profils complets de **50 joueurs fictifs** pour la saison 2024-2025 du championnat tunisien (Ligue Professionnelle 1). Il est conçu pour permettre le calcul de tous les indicateurs, sous-scores et le **FIT Score final** affichés sur la page "portail-joueur" de la plateforme FIT.

## 🏗️ Structure des Données

### **5 Piliers du FIT Score**

#### **Pillar 1: Health (Santé) 🩺**

-   **Historique des blessures** : 2-4 blessures passées pour 60% des joueurs
-   **Données de Laboratoire** : Biomarqueurs clés (Vitamine D, CK, hs-CRP, etc.)
-   **Tests Fonctionnels** : Y-Balance Test, force isocinétique, agilité, saut vertical
-   **Statut PCMA** : "Clearance OK" pour 90% des joueurs
-   **Outils de collecte** : EMR, Laboratoire, Kinésithérapie

#### **Pillar 2: Performance ⚽**

-   **Statistiques saison précédente** : Minutes jouées, buts, passes, tacles, duels
-   **Données Physiques** : Vitesse max, distance/match, sprints, accélération
-   **Outils de collecte** : Opta/Stats Perform, capteurs GPS (Catapult, STATSports)

#### **Pillar 3: SDOH (Déterminants Sociaux de la Santé) 🌍**

-   **Profils narratifs** : Contextes familiaux, sociaux et culturels
-   **Scores quantifiés** : Support social, stabilité logement/nutrition, adaptation culturelle
-   **Facteurs de risque/protection** : Analyse des vulnérabilités et ressources
-   **Outils de collecte** : Entretiens sociaux, questionnaires bien-être

#### **Pillar 4: Market Value (Valeur Marchande) 💰**

-   **Valeur marchande** : 50 000 € à 3+ millions € selon profil
-   **Durée contrat** : 1-4 ans restants
-   **Salaire et bonus** : Rémunération mensuelle et primes performance
-   **Outils de collecte** : Transfermarkt, CIES Football Observatory

#### **Pillar 5: Adherence/Availability (Adhérence/Disponibilité) ✅**

-   **Présence entraînements** : Taux de participation (85-98%)
-   **Adhérence protocole** : Score de suivi des programmes
-   **Disponibilité générale** : Pourcentage de jours aptes à jouer
-   **Outils de collecte** : Rapports coachs, application mobile, staff médical

## 🌍 Répartition des Nationalités

| Nationalité       | Nombre | Pourcentage |
| ----------------- | ------ | ----------- |
| **Tunisie**       | 28     | 56%         |
| **Maroc**         | 7      | 14%         |
| **Algérie**       | 4      | 8%          |
| **Mali**          | 4      | 8%          |
| **Sénégal**       | 3      | 6%          |
| **Côte d'Ivoire** | 2      | 4%          |
| **Nigeria**       | 2      | 4%          |

## ⚽ Répartition des Postes

| Poste                 | Nombre | Pourcentage |
| --------------------- | ------ | ----------- |
| **Latéral gauche**    | 9      | 18%         |
| **Ailier droit**      | 9      | 18%         |
| **Gardien**           | 7      | 14%         |
| **Milieu central**    | 6      | 12%         |
| **Latéral droit**     | 5      | 10%         |
| **Milieu défensif**   | 4      | 8%          |
| **Milieu offensif**   | 4      | 8%          |
| **JS Kairouan**       | 4      | 8%          |
| **Ailier gauche**     | 3      | 6%          |
| **Défenseur central** | 2      | 4%          |
| **Attaquant**         | 1      | 2%          |

## 🏟️ Répartition des Clubs

| Club                   | Nombre | Pourcentage |
| ---------------------- | ------ | ----------- |
| **Club Africain**      | 11     | 22%         |
| **Espérance de Tunis** | 6      | 12%         |
| **CS Sfaxien**         | 6      | 12%         |
| **AS Gabès**           | 6      | 12%         |
| **Stade Tunisien**     | 5      | 10%         |
| **US Monastir**        | 4      | 8%          |
| **JS Kairouan**        | 4      | 8%          |
| **Olympique Béja**     | 4      | 8%          |
| **Étoile du Sahel**    | 3      | 6%          |
| **CA Bizertin**        | 1      | 2%          |

## 📊 Statistiques des FIT Scores

### **Distribution des Catégories**

-   **Très bon** : 30 joueurs (60%)
-   **Bon** : 20 joueurs (40%)

### **Scores Moyens par Pilier**

-   **Health** : 80.74
-   **Performance** : 82.70
-   **SDOH** : 72.46
-   **Market Value** : 77.12
-   **Adherence** : 88.86

### **FIT Score Global**

-   **Moyen** : 80.38
-   **Minimum** : 74.0
-   **Maximum** : 88.0

## 🚀 Utilisation

### **1. Génération du Dataset**

```bash
php generate-dataset-simple.php
```

### **2. Validation des Données**

```bash
php validate-dataset.php
```

### **3. Import dans la Base de Données**

```bash
php import-dataset.php
```

## 📁 Fichiers

-   **`dataset-50-joueurs-tunisie-2024-2025.json`** : Dataset complet (164 KB)
-   **`generate-dataset-simple.php`** : Script de génération
-   **`validate-dataset.php`** : Script de validation
-   **`import-dataset.php`** : Script d'import en base

## 🔍 Validation et Qualité

### **Cohérence des Données**

-   ✅ Aucune erreur de cohérence détectée
-   ✅ Âges cohérents avec dates de naissance
-   ✅ FIT Scores calculés correctement
-   ✅ Répartition réaliste des nationalités et postes

### **Données de Santé**

-   **67 blessures totales** (1.34 par joueur en moyenne)
-   **94% des joueurs** avec PCMA "Clearance OK"
-   **Types de blessures** : Musculaires, ligamentaires, contusions, entorses

### **Valeurs Marchandes**

-   **Moyenne** : 1 862 696 €
-   **Plage** : 438 533 € - 3 590 911 €
-   **Facteurs** : Âge, poste, club, potentiel

### **Adhérence**

-   **Présence entraînements** : 89.7% en moyenne
-   **Disponibilité générale** : 89.9% en moyenne
-   **Scores** : Excellent, Très bon, Bon, Moyen

## 🎯 Cas d'Usage

### **Calcul du FIT Score**

Le dataset permet de tester et valider l'algorithme de calcul du FIT Score avec des données réalistes et variées.

### **Développement Frontend**

Les 50 profils permettent de tester l'interface utilisateur avec différents types de joueurs et scores.

### **Tests de Performance**

Validation des performances de la plateforme avec un volume de données significatif.

### **Formation et Démonstration**

Dataset idéal pour former les utilisateurs et démontrer les fonctionnalités de la plateforme.

## 🔧 Personnalisation

### **Modifier la Répartition**

-   Ajuster les pourcentages dans `$nationalites`
-   Modifier la liste des clubs et postes
-   Ajuster les plages de scores FIT

### **Ajouter des Données**

-   Étendre les types de blessures
-   Ajouter de nouveaux biomarqueurs
-   Créer des profils SDOH plus détaillés

### **Générer Plus de Joueurs**

-   Modifier la boucle dans `generateJoueur()`
-   Ajuster les algorithmes de génération

## 📞 Support

Pour toute question ou modification du dataset, consultez la documentation de la plateforme FIT ou contactez l'équipe de développement.

---

**🎉 Dataset prêt pour la production ! Tous les 50 joueurs sont validés et cohérents pour le calcul du FIT Score.**




