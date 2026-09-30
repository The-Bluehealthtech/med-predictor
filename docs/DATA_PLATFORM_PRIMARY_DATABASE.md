# Base principale FIT et alimentation future du data lake

## Décision

La base relationnelle choisie par `config/database.php` (`database.default`, variable `DB_CONNECTION`) est la base opérationnelle principale de FIT. Les formulaires web, API métier, imports et intégrations doivent y enregistrer leurs données avant d'annoncer un succès. Le futur data lake consommera les données persistées de cette base ; il ne constitue pas une deuxième destination d'écriture des formulaires. Un même serveur applicatif ne doit pas basculer vers une autre base à l'occasion d'une panne.

Cette règle vaut pour tous les domaines : identités, clubs, équipes, compétitions, feuilles de match, arbitres, santé, PCMA, DTN/RPM, performance et FIFA Connect. Chaque domaine garde ses tables et relations. Une base principale ne signifie pas une table unique. Les fichiers (images, PDF, enregistrements) peuvent être stockés dans le stockage de fichiers existant ; leur identité, rattachement, chemin et statut restent enregistrés dans la base opérationnelle.

## Identité et provenance

`players.id` est la clé interne du joueur. Le FIFA ID est un identifiant externe facultatif, utile pour l'interopérabilité ; le calcul de performance n'en dépend pas. `athletes.player_id`, les mesures et les dossiers associés utilisent la liaison interne existante. La table historique `joueurs` ne doit pas recevoir de nouvelles observations utilisées par le nouveau calcul : ses identifiants ne peuvent pas être confondus avec ceux de `players`.

Les données observées, non vérifiées et synthétiques doivent rester distinguables. Les lignes synthétiques illustrent les interfaces ; elles ne contribuent ni aux groupes de référence ni à l'entraînement ou à la validation des modèles. Pour les sources du nouveau score, le champ `score_origin` accepte le statut métier `observed` seulement après contrôle de la source. Le défaut est `unverified`. Les interfaces existantes de saisie/vérification dans `performance_metrics` continuent d'écrire dans la base principale avec auteur et vérificateur ; leur catalogue FIT et leur score existant ne sont pas confondus avec le nouveau score de match.

## Écritures et disponibilité

Les écritures utilisent la connexion Laravel principale, les validations et droits existants, et une transaction pour les ensembles indissociables. Les interfaces ne présentent pas un succès avant la persistance. Le middleware global `DatabaseFallback` vérifie la connexion : si elle échoue, il répond 503 sans injecter de chiffres fictifs. Une exception du contrôleur n'entraîne jamais une deuxième invocation de l'action.

Pour un import réel : résoudre le joueur par sa clé interne autorisée, valider les unités et la présence des valeurs, garder les manquants à null, enregistrer la provenance et le contexte de match ou d'agrégat, puis calculer après la persistance. Une simple case fournie par le navigateur ne doit pas suffire à certifier la provenance.

## Contrat du futur data lake

L'alimentation sera incrémentale et traçable à partir de la base principale : données sources, identifiant interne, contexte organisationnel/tenant, provenance, dates de mesure et de modification, version de schéma, corrections et suppressions. Les scores et prédictions sont des résultats dérivés versionnés. L'export conserve les règles d'accès aux données médicales et personnelles. Le choix entre journal transactionnel et capture des changements de la base sera fait selon le moteur réellement hébergé ; aucune infrastructure de data lake n'est déployée dans cette phase.

## Vérification de la branche

L'audit réutilisable `scripts/audit-primary-database.php` inspecte les routes actives et produit `docs/PRIMARY_DATABASE_ROUTE_AUDIT.md`. Il a relevé 692 routes, dont 241 routes de commande ou comportant une écriture directe repérée. Aucune connexion nommée imposée n'a été trouvée dans `app`. Ce constat statique ne vaut pas test de persistance de toutes les interfaces.

18 actions de route ne se résolvent pas à une méthode existante ; elles sont listées dans l'inventaire. Les routes concernent notamment le portail joueur, des exports PCMA, des transitions PCMA, la synchronisation de compétitions et les passeports. Elles doivent être réparées selon leurs contrats actuels avant de déclarer l'ensemble des interfaces vérifié. Les délégations aux services et les fichiers joints nécessitent également des tests par scénario métier. La branche n'a pas été déployée et la connexion réellement hébergée en production n'a pas été inspectée.

Vérification ciblée : 13 tests, 54 assertions réussies, comprenant calcul de score, provenance, absence de FIFA ID, indisponibilité de la base et absence de réexécution d'une écriture. Aucune donnée de démonstration n'a été reclassée comme donnée réelle.

## Réparation des actions actives — 30 septembre 2026

Les 18 actions auparavant introuvables sont reliées à des méthodes disponibles. L'audit statique des 692 routes ne relève plus d'action introuvable. Cela ne constitue pas une validation fonctionnelle exhaustive de toutes les pages.

Les contacts du portail sont écrits uniquement pour players.id lié au compte, sans FIFA ID obligatoire. Les transitions PCMA utilisent la base principale et contrôlent le périmètre médical. Les passeports et transferts lisent les données persistées, sans inventer d'alertes réglementaires. Le PDF non enregistré est identifié comme brouillon, sans signature certifiée.

Les barèmes de formation n'ont aucune source officielle configurée : la route répond explicitement 503, sans tarif fictif. Les calculs financiers correspondants restent à configurer. La synchronisation externe FIFA n'a pas été déclenchée lors des tests.

Vérifications ciblées : résolution des actions, isolation du contact joueur, accès médical interclub, transitions PCMA en base de test, génération PDF et échappement des données, barèmes absents. Les tests ne couvrent pas encore tous les parcours navigateur ni les intégrations externes. Aucun déploiement de production effectué.
