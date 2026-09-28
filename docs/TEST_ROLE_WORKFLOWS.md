# Environnement de test des rôles

## Création des comptes

Exécuter :

```bash
DB_CONNECTION=pgsql DATABASE_URL="$DATABASE_URL" php artisan db:seed --class=Database\\Seeders\\RoleTestAccountsSeeder --force
```

Mot de passe commun de test : `TestRole2026!`

Les comptes sont rattachés automatiquement à la première association et au premier club présents dans la base de test.

## Comptes créés

| Rôle | Compte | Responsabilité principale |
|---|---|---|
| System admin | test.system-admin@fit.local | Configuration globale, sécurité, supervision |
| DTN | test.dtn@fit.local | Pilotage technique, performance, suivi des équipes |
| Administrateur ligue | test.ligue-admin@fit.local | Compétitions, licences, validations et discipline |
| Gestionnaire licences | test.registrar@fit.local | Instruction administrative des licences |
| Médecin ligue | test.medical@fit.local | Contrôle médical et certificats |
| Arbitre | test.referee@fit.local | Désignation, feuille de match et rapport |
| Administrateur club | test.club-admin@fit.local | Joueurs, équipes, engagements et feuilles de match |
| Manager club | test.club-manager@fit.local | Effectif et préparation opérationnelle |
| Médecin club | test.club-medical@fit.local | Dossiers médicaux et aptitude |
| Médecin équipe | test.team-doctor@fit.local | Contrôle médical d’équipe et match |

## Workflows principaux

### Directeur technique national (DTN)

- Consulte les performances, scores FIT et analyses techniques.
- Suit les équipes et les joueurs à l’échelle nationale.
- Produit les rapports techniques et identifie les besoins de développement.
- Ne valide pas les licences médicales ni les sanctions administratives.

### Médecin

- Le médecin de club renseigne les dossiers médicaux de son club.
- Le médecin de ligue contrôle les certificats et l’aptitude médicale.
- Une licence ne doit être considérée comme éligible que si son statut administratif est approuvé et que les exigences médicales sont satisfaites.
- Les données médicales restent limitées aux utilisateurs autorisés.

### Arbitre

- Consulte les matches qui lui sont désignés.
- Complète la feuille de match : équipes, joueurs, événements, sanctions, scores et rapport.
- Enregistre les buts, remplacements, cartons J1–J7, cartons R1–R6 et second avertissement.
- Signe la feuille après le match.

### Administrateur de club

- Gère les joueurs, équipes, officiels et engagements du club.
- Soumet les demandes de licence.
- Prépare la feuille de match avec les joueurs éligibles, les capitaines, le coach et le manager.
- Fait signer la feuille par le capitaine du club.
- Ne peut pas valider définitivement une licence ou une feuille au niveau de la ligue.

### Administrateur de ligue

- Crée et gère les compétitions et saisons.
- Contrôle les engagements des clubs et les calendriers.
- Valide les licences après les contrôles administratif et médical.
- Contrôle les feuilles soumises par les clubs et arbitres.
- Valide définitivement la feuille et déclenche les mises à jour statistiques/classements.

## Scénario de test recommandé

1. L’administrateur de club engage une équipe.
2. Le gestionnaire de licences instruit une licence.
3. Le médecin valide l’aptitude médicale.
4. L’administrateur de ligue approuve la licence.
5. L’administrateur de ligue programme le match.
6. L’arbitre est désigné.
7. Le club sélectionne uniquement les joueurs licenciés et éligibles.
8. Les deux capitaines signent la feuille.
9. L’arbitre saisit les événements et signe.
10. L’administrateur de ligue valide et verrouille la feuille.
11. Le classement, les statistiques et les sanctions sont contrôlés.
