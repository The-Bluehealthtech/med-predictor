# Évaluation posturale V1

## Objectif

La V1 sépare trois concepts :

- `postural_assessments` : épisode clinique postural ;
- `postural_findings` : observations structurées ;
- `postural_measurements` : mesures manuelles ou graphiques.

Elle ne génère aucun diagnostic automatique, aucun score global de posture et aucun seuil de sévérité automatique.

## Compatibilité

La livraison est additive. Les champs historiques sont conservés :

- `health_records.postural_assessment`
- `health_records.postural_alignment`
- `health_records.postural_corrections`
- `postural_assessments.view`
- `postural_assessments.annotations`
- `postural_assessments.markers`
- `postural_assessments.angles`

## Catalogue

`config/postural_assessment.php` est la source de vérité pour :

- vues ;
- côtés ;
- sévérités ;
- findings ;
- protocoles de mesure.

Les coordonnées graphiques doivent être normalisées entre 0 et 1.

Les distances physiques ne sont pas exprimées en millimètres sans calibration. Les protocoles non calibrés utilisent l’unité `normalized`.

## Cycle de vie

`draft -> completed -> validated -> archived`

Une évaluation `validated` est en lecture seule dans le service V1.

## Routes

Les routes du module sont isolées dans `routes/postural.php` et chargées par `RouteServiceProvider`.

Les routes directes par identifiant d’évaluation revérifient la visibilité du `HealthRecord` afin de conserver le cloisonnement tenant.

## Migration legacy

La commande :

```bash
php artisan postural:migrate-legacy
```

est un dry-run.

Pour appliquer :

```bash
php artisan postural:migrate-legacy --apply
```

Filtres disponibles :

```bash
php artisan postural:migrate-legacy --assessment=123
php artisan postural:migrate-legacy --player=45
```

La commande :

- conserve les colonnes historiques ;
- transforme les coordonnées 600x800 en coordonnées normalisées ;
- ne donne aucun sens anatomique aux anciens angles libres ;
- marque les mesures converties avec `metadata.legacy_migrated=true` ;
- ignore les évaluations déjà converties.

## Déploiement

Ordre recommandé :

1. exécuter les tests ;
2. appliquer les migrations additives sur une copie PostgreSQL ;
3. vérifier `migrate -> rollback -> migrate` ;
4. déployer le code ;
5. laisser les données historiques inchangées ;
6. exécuter `postural:migrate-legacy` en dry-run sur la copie ;
7. examiner le rapport ;
8. seulement ensuite envisager `--apply`.

Aucune migration ni commande legacy ne doit être exécutée automatiquement sur Render par cette livraison.

## Tests ajoutés

- cohérence du catalogue ;
- finding valide ;
- rejet d’une vue incompatible ;
- validation du nombre de points d’une mesure graphique.

Des tests d’intégration PostgreSQL restent requis avant une migration de production.
