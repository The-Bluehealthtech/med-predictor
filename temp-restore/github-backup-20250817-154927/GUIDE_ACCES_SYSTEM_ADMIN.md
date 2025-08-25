# Guide d'Accès System Admin - Gestion des Demandes de Compte

## 🎯 Accès à l'Interface d'Administration

### Identifiants de Connexion

-   **URL de connexion**: http://localhost:8000/login
-   **Email**: system.admin@fit.com
-   **Mot de passe**: admin123

### Étapes d'Accès

1. Ouvrez votre navigateur et allez sur http://localhost:8000/login
2. Entrez les identifiants ci-dessus
3. Une fois connecté, cliquez sur **"Admin"** dans le menu de navigation
4. Cliquez sur **"Demandes de compte"** (Account Requests)
5. Vous accédez à l'interface de gestion des demandes

## 🔧 Fonctionnalités Disponibles

### Interface d'Administration

-   **Vue d'ensemble**: Liste de toutes les demandes de compte
-   **Filtres avancés**: Par statut, type d'organisation, type de football
-   **Recherche**: Par nom, email, organisation
-   **Pagination**: Navigation facile entre les pages

### Actions sur les Demandes

-   **Voir les détails**: Cliquez sur "Voir" pour afficher toutes les informations
-   **Marquer comme contacté**: Pour les demandes en attente
-   **Approuver**: Crée automatiquement un compte utilisateur et envoie les identifiants
-   **Rejeter**: Avec une raison obligatoire

### Workflow d'Approbation

1. **Demande soumise** → Statut: "En attente"
2. **Contact (optionnel)** → Statut: "Contacté"
3. **Approbation** → Statut: "Approuvé" + Compte créé + Email envoyé
4. **Rejet** → Statut: "Rejeté" + Email avec raison

## 📊 Statuts des Demandes

### En Attente (Pending)

-   Demande nouvellement soumise
-   Actions disponibles: Contacter, Approuver, Rejeter

### Contacté (Contacted)

-   Demande marquée comme contactée par l'admin
-   Actions disponibles: Approuver, Rejeter

### Approuvé (Approved)

-   Demande approuvée
-   Compte utilisateur créé automatiquement
-   Identifiants envoyés par email
-   Actions disponibles: Aucune (finalisé)

### Rejeté (Rejected)

-   Demande rejetée avec raison
-   Email de rejet envoyé
-   Actions disponibles: Aucune (finalisé)

## 🎨 Interface Utilisateur

### Filtres Disponibles

-   **Statut**: Tous, En attente, Contacté, Approuvé, Rejeté
-   **Type d'organisation**: Club, Association, Fédération, Ligue, Académie, Autre
-   **Type de football**: 11 à 11, Futsal, Football Féminin, Beach Soccer
-   **Recherche**: Texte libre pour nom, email, organisation

### Informations Affichées

-   **Demandeur**: Nom, email, téléphone
-   **Organisation**: Nom, type
-   **Type de football**: Badge coloré
-   **Statut**: Badge avec couleur correspondante
-   **Date**: Date de soumission
-   **Actions**: Boutons selon le statut

## 📧 Notifications Email

### Pour les Admins

-   **Nouvelle demande**: Notification immédiate quand une demande est soumise
-   **Détails complets**: Informations sur le demandeur et l'organisation
-   **Lien direct**: Accès direct à la demande pour traitement

### Pour les Utilisateurs

-   **Approbation**: Email avec identifiants de connexion
-   **Rejet**: Email avec raison et prochaines étapes

## 🔐 Sécurité

### Contrôle d'Accès

-   **Rôles autorisés**: system_admin, association_admin, association_registrar
-   **Protection CSRF**: Toutes les actions protégées
-   **Validation**: Vérification des données d'entrée
-   **Audit trail**: Toutes les actions enregistrées

### Données Sensibles

-   **Mots de passe**: Générés automatiquement et sécurisés
-   **Identifiants**: Stockés temporairement pour envoi email
-   **Historique**: Toutes les actions tracées avec timestamps

## 🚀 Utilisation Rapide

### Approuver une Demande

1. Cliquez sur "Voir" pour examiner les détails
2. Cliquez sur "Approuver"
3. Ajoutez des notes (optionnel)
4. Confirmez l'approbation
5. Le système crée automatiquement le compte et envoie l'email

### Rejeter une Demande

1. Cliquez sur "Rejeter"
2. Entrez une raison obligatoire
3. Confirmez le rejet
4. L'email de rejet est envoyé automatiquement

### Marquer comme Contacté

1. Cliquez sur "Contacter"
2. Confirmez l'action
3. Le statut passe à "Contacté"

## 📈 Statistiques

### Métriques Disponibles

-   **Total des demandes**: Nombre total de demandes
-   **En attente**: Demandes non traitées
-   **Contactées**: Demandes marquées comme contactées
-   **Approuvées**: Demandes approuvées avec comptes créés
-   **Rejetées**: Demandes rejetées
-   **Avec comptes**: Demandes ayant généré un compte utilisateur

## 🔧 Dépannage

### Problèmes Courants

-   **Accès refusé**: Vérifiez que vous êtes connecté avec un rôle autorisé
-   **Page non trouvée**: Vérifiez que le serveur Laravel est démarré
-   **Erreur de base de données**: Vérifiez la connexion à la base de données

### Support

-   **Logs**: Vérifiez les logs Laravel pour les erreurs
-   **Base de données**: Vérifiez l'intégrité des tables
-   **Email**: Vérifiez la configuration SMTP pour les notifications

## ✅ Checklist de Vérification

### Avant de Commencer

-   [ ] Serveur Laravel démarré (php artisan serve)
-   [ ] Base de données accessible
-   [ ] Configuration email correcte
-   [ ] Utilisateur System Admin créé

### Fonctionnalités à Tester

-   [ ] Connexion avec les identifiants System Admin
-   [ ] Accès au menu Admin
-   [ ] Accès à l'interface des demandes de compte
-   [ ] Filtrage et recherche
-   [ ] Approbation d'une demande
-   [ ] Rejet d'une demande
-   [ ] Réception des emails de notification

## 🎉 Conclusion

L'interface d'administration des demandes de compte est maintenant entièrement opérationnelle. Les System Admin peuvent efficacement gérer les demandes, approuver les comptes et maintenir un workflow organisé pour l'intégration de nouveaux utilisateurs dans la plateforme FIT.
