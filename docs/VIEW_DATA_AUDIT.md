# Audit des données affichées — 25 septembre 2026

L'inventaire [view-data-audit-2026-09-25.json](view-data-audit-2026-09-25.json) couvre les **634 fichiers Blade présents** après retrait de quatre portails de démonstration. Un balayage statique détecte des signaux dans **133 vues** ; **501** autres n'ont aucun signal selon ces règles, ce qui ne valide ni leurs données ni leur modèle métier. Les références de vues directes sont indicatives : les composants et routes dynamiques peuvent y échapper.

## Corrections de cette passe

- Accueil public : suppression des chiffres et graphiques fictifs ; page d'accueil institutionnelle.
- Portails FIFA publics remplis de clubs et joueurs fictifs : routes retirées (410), vues concernées supprimées ; `/player-portal` redirige vers le portail authentifié.
- Liste des arbitres : données persistées, statut absent explicite et lien vers les affectations réelles.
- Portail joueur actif : courbes de match et radar aléatoires masqués avec mention de données indisponibles.
- Demande de licence club : aucun FIFA ID généré par horodatage ; utilisation exclusive de l'identifiant déjà stocké chez le joueur.
- Signature PCMA : suppression des numéros MED aléatoires, de l'IP fictive, des identifiants d'évaluation temporaires et des valeurs médicales injectées par défaut. Confirmation désactivée faute de numéro professionnel vérifié et de décision médicale prouvée.

## Modèle et limites vérifiés

Les types canoniques du projet couvrent Person, Registration, Organisation, Competition et Discipline. Sept sérialisations XML locales passent la validation contre les XSD réels présents dans le dépôt. Cette validation de schéma ne prouve pas l'origine des fiches affichées ni une synchronisation avec le service FIFA Connect : les clés de production ne sont pas disponibles.

Références normatives : https://data.fifaconnect.org/scenarios/ ; https://data.fifaconnect.org/registration/ ; https://data.fifaconnect.org/competition/ ; https://data.fifaconnect.org/discipline/ ; https://data.fifaconnect.org/content/documentation/generic.html.

## À vérifier avant certification exhaustive

1. Examiner les 133 vues signalées, puis les 501 autres avec leur contrôleur, leur requête et leurs composants : un balayage lexical ne permet pas de déclarer toutes les cartes conformes.
2. Relier chaque champ affiché aux enregistrements persistés et à son type FIFA Connect applicable. Un champ local sans équivalent FIFA ne doit pas être qualifié de donnée FIFA.
3. Déterminer la source autoritative du numéro professionnel du médecin et de la décision PCMA ; la signature reste bloquée jusqu'à preuve.
4. Rejouer les routes en environnement authentifié et vérifier les jeux de données réels, les rôles et les erreurs 500. Le registre `ROUTE_500_AUDIT.md` garde l'instantané initial et les corrections vérifiées séparés.
