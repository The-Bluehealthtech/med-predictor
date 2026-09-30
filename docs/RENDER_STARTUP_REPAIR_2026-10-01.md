# Render — démarrage bloqué avant ouverture du port — 1 octobre 2026

## Constat
Le journal fourni termine sur la migration AUT/CIM-11 et Render ne détecte aucun port.
Le script attendait fit:deploy complet avant Apache : migrations, import du catalogue, données de démonstration et snapshots.
Cette séquence explique l'absence de port tant que la préparation ne termine pas. Le journal ne prouve pas à lui seul quel verrou ou quelle requête PostgreSQL bloquait en production.

## Correctif
- fit:deploy --schema-only applique les migrations obligatoires, sans import ni calcul.
- Apache démarre après succès du schéma, sur PORT (10000 par défaut).
- fit:deploy --refresh-only effectue ensuite les opérations existantes en arrière-plan ; ses échecs sont explicitement journalisés.
- Migration échouée : le démarrage échoue et Apache ne s'ouvre pas. Aucun démarrage prétendument réussi avec schéma incomplet.
- PostgreSQL : DROP NOT NULL sans conversion des types historiques ; attente de verrou limitée à 10 secondes, requête à 90 secondes, dans la transaction de migration.
- Colonnes et table nouvelles vérifiées avant création : une reprise après interruption ne duplique ni ne détruit les données.
- Commande fit:deploy sans nouveau drapeau : comportement complet existant conservé.
- Aucun accès ni annulation de transaction en base de production effectué pendant le diagnostic.

## Vérification
64 tests PHP / 523 assertions réussis : médical, PCMA, Healthcare, portail, schéma seul et reprise.
PostgreSQL local isolé : verrou exclusif simulé, échec à dix secondes, rollback, migration après libération, types inchangés, données conservées et deuxième exécution réussie.
Script de démarrage testé sous Linux : ouverture TCP avant fin du rafraîchissement ; refus de démarrer en cas de migration échouée ; serveur maintenu et erreur explicite si le rafraîchissement échoue ; PORT invalide refusé.
Ce test utilise une simulation des commandes PHP et du processus Apache ; il ne remplace pas une validation de l'image Render réelle.
Syntaxes PHP, shell et git diff --check réussies.

## Limites
Pas de session Render permettant de lire les verrous de la base réelle ou l'état du déploiement.
L'état Live et le fonctionnement médical authentifié en production restent à confirmer.
