# Guide d'Utilisation - Système de Transferts FIFA

## Introduction

Le système de gestion des transferts FIFA permet de gérer complètement les transferts de joueurs avec intégration automatisée des systèmes FIFA ITMS (International Transfer Matching System) et DTMS (Domestic Transfer Matching System).

## Accès au Système

### URL d'accès

-   **Liste des transferts** : `/transfers`
-   **Passeport du Jour** : `/daily-passport`
-   **Création de transfert** : `/transfers/create`

### Permissions requises

-   Utilisateur authentifié
-   Rôle approprié (admin, club_manager, etc.)

## Gestion des Transferts

### 1. Créer un Nouveau Transfert

#### Étape 1 : Accéder au formulaire

1. Aller sur `/transfers`
2. Cliquer sur "Nouveau Transfert"

#### Étape 2 : Remplir les informations

-   **Joueur** : Sélectionner le joueur à transférer
-   **Club d'origine** : Club actuel du joueur
-   **Club de destination** : Nouveau club du joueur
-   **Type de transfert** : Définitif, Prêt, ou Agent libre
-   **Date de transfert** : Date effective du transfert
-   **Date de début de contrat** : Date de début du nouveau contrat
-   **Date de fin de contrat** : Date de fin (optionnel)
-   **Frais de transfert** : Montant du transfert (optionnel)
-   **Devise** : EUR, USD, GBP
-   **Transfert de mineur** : Cocher si le joueur a moins de 18 ans

#### Étape 3 : Validation

-   Le système vérifie automatiquement l'éligibilité du joueur
-   Détection automatique des transferts internationaux
-   Validation des fenêtres de transfert

### 2. Upload des Documents

#### Documents requis

-   **Passeport** : Passeport valide du joueur
-   **Contrat** : Nouveau contrat signé
-   **Certificat médical** : Certificat de bonne santé
-   **Consentement parental** : Pour les mineurs uniquement

#### Procédure d'upload

1. Aller sur la page du transfert
2. Section "Documents"
3. Cliquer sur "Ajouter un document"
4. Sélectionner le type de document
5. Uploader le fichier
6. Attendre la validation par un administrateur

### 3. Soumission à FIFA

#### Prérequis

-   Tous les documents requis sont approuvés
-   Le transfert est dans la fenêtre de transfert
-   Le statut du transfert est "Brouillon"

#### Procédure

1. Sur la page du transfert, cliquer sur "Soumettre à FIFA"
2. Le système envoie automatiquement les données à FIFA
3. Si transfert international, demande automatique d'ITC
4. Suivi en temps réel du statut

### 4. Suivi du Transfert

#### Statuts possibles

-   **Brouillon** : Transfert créé, en cours de préparation
-   **En attente** : Soumis à FIFA, en attente de réponse
-   **Soumis** : Données envoyées à FIFA
-   **Approuvé** : Transfert validé par FIFA
-   **Rejeté** : Transfert refusé par FIFA

#### Actions disponibles

-   **Voir** : Consulter les détails
-   **Modifier** : Modifier (si statut "Brouillon")
-   **Soumettre** : Envoyer à FIFA
-   **Vérifier ITC** : Vérifier le statut ITC

## Passeport du Jour

### Onglet Club

Affiche la liste des joueurs éligibles pour un club donné.

#### Informations affichées

-   Nom et photo du joueur
-   Position et nationalité
-   Statut de la licence FIFA
-   Statut ITC (si applicable)
-   Nombre de suspensions
-   Éligibilité à jouer

#### Filtres

-   Sélection du club
-   Statut de licence
-   Éligibilité

### Onglet Fédération

Synthèse des transferts pour une fédération.

#### Informations affichées

-   Transferts récents avec statuts
-   Alertes réglementaires
-   Statistiques de la fédération

### Onglet Joueur

Historique complet des transferts d'un joueur.

#### Informations affichées

-   Informations personnelles
-   Club actuel et contrat
-   Historique des transferts
-   Documents associés

## Gestion des Paiements

### Types de paiements

-   **Frais de transfert** : Montant principal du transfert
-   **Compensation de formation** : Pour les jeunes joueurs
-   **Contribution de solidarité** : Redistribution aux clubs formateurs

### Procédure

1. Aller sur la page du transfert
2. Section "Paiements"
3. Ajouter un nouveau paiement
4. Remplir les informations (montant, méthode, etc.)
5. Suivre le statut du paiement

## Alertes et Notifications

### Types d'alertes

-   **ITC en retard** : Plus de 7 jours sans réponse
-   **Documents expirés** : Documents périmés
-   **Fenêtre de transfert fermée** : Transfert hors période
-   **Erreurs FIFA** : Problèmes de communication

### Gestion des alertes

-   Affichage dans le tableau de bord
-   Notifications par email
-   Actions correctives suggérées

## Filtres et Recherche

### Filtres disponibles

-   **Statut** : Brouillon, En attente, Approuvé, Rejeté
-   **Type** : Définitif, Prêt, Agent libre
-   **Club** : Club d'origine ou de destination
-   **Joueur** : Nom du joueur
-   **Date** : Période de création

### Recherche avancée

-   Recherche par nom de joueur
-   Recherche par ID FIFA
-   Recherche par montant de transfert

## Export et Rapports

### Types d'export

-   **Liste des transferts** : Excel, PDF
-   **Statistiques** : Graphiques et tableaux
-   **Rapports FIFA** : Format requis par FIFA

### Périodicité

-   Rapports quotidiens automatiques
-   Rapports mensuels de synthèse
-   Rapports annuels complets

## Gestion des Erreurs

### Erreurs courantes

1. **Documents manquants** : Uploader les documents requis
2. **Fenêtre fermée** : Attendre la prochaine fenêtre
3. **Erreur FIFA** : Vérifier la connectivité et les données
4. **Joueur non éligible** : Vérifier les conditions d'éligibilité

### Procédures de résolution

1. Identifier l'erreur dans les logs
2. Suivre les instructions affichées
3. Contacter le support si nécessaire
4. Documenter la résolution

## Support et Aide

### Documentation

-   Guide utilisateur complet
-   FAQ en ligne
-   Tutoriels vidéo

### Support technique

-   Tickets de support
-   Chat en ligne
-   Téléphone support

### Formation

-   Sessions de formation utilisateurs
-   Documentation administrateur
-   Procédures d'urgence

## Bonnes Pratiques

### Avant la création

-   Vérifier l'éligibilité du joueur
-   Préparer tous les documents
-   Vérifier la fenêtre de transfert
-   Contacter les clubs concernés

### Pendant le processus

-   Suivre régulièrement le statut
-   Répondre rapidement aux demandes
-   Maintenir les documents à jour
-   Documenter les communications

### Après validation

-   Archiver les documents
-   Mettre à jour les informations
-   Notifier les parties concernées
-   Préparer les prochaines étapes

## Sécurité

### Protection des données

-   Chiffrement des communications
-   Accès sécurisé aux documents
-   Audit trail complet
-   Sauvegarde régulière

### Conformité

-   Respect du RGPD
-   Conformité FIFA
-   Réglementation locale
-   Politiques internes

## Maintenance

### Tâches quotidiennes

-   Vérification des statuts ITC
-   Nettoyage des transferts expirés
-   Synchronisation avec FIFA
-   Sauvegarde des données

### Tâches hebdomadaires

-   Génération des rapports
-   Vérification des alertes
-   Mise à jour des statistiques
-   Maintenance préventive

### Tâches mensuelles

-   Analyse des performances
-   Mise à jour de la documentation
-   Formation des utilisateurs
-   Optimisation du système
