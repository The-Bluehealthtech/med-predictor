# Healthcare — corrections et contrôles du 30 septembre 2026

## Comportement
- /modules/healthcare et /healthcare utilisent le même contrôleur et les mêmes
  dossiers persistés, avec pagination et périmètre médical par club/association.
- Les liens Voir/Modifier utilisent le dossier demandé, sans Patient Example,
  date fixe ni risque de démonstration. Modification et suppression écrivent en base.
- Création et édition utilisent players.id ; aucun FIFA ID n'est requis.
- Dossier existant : les listes vides et valeurs nulles explicites sont préservées.
  Les champs cliniques réellement présents dans le schéma sont validés selon les
  casts du modèle. Propriétaire, joueur, scores calculés et chemins sont protégés.
- Quatre liens IA cassés de l'édition pointent vers les routes PCMA existantes.
- Création, lecture, édition, mutation et exports vérifient le rôle médical et
  le joueur autorisé. Le patient d'un dossier ne peut pas être remplacé.
- Export CSV réel, par lots, limité aux dossiers autorisés ; champs cliniques
  disponibles, hors scores non validés et chemins de fichiers. Formules neutralisées.
- La génération des pronostics fixes est désactivée (503 explicite), sans supprimer
  l'historique. Cet historique est présenté comme non validé, sans pronostic clinique.
- Le portail exclut les modèles non approuvés. config/healthcare.php contient une
  liste vide de modèles validés ; elle ne doit être renseignée qu'après validation.
- Répartition des dossiers dans la carte Healthcare issue des dossiers autorisés.
  Alertes indisponibles : tiret, pas faux zéro. Liens et libellés de la carte corrigés.
- Nouveaux CDA stockés sur le disque privé ; téléchargement/lecture protégés.
  Les anciens fichiers publics ne sont pas migrés dans ce chantier.
- Journaux CDA retirés lorsqu'ils contenaient dossiers, analyses ou aperçu XML.
- L'événement de création reste interne ; diffusion publique du dossier désactivée.
- HealthRecord, PCMA et portail utilisent la connexion principale avec player_id.
  Les PCMA sont liés au dossier, sans recopier ni inventer les conclusions médicales.

## Vérification
51 tests réussis, 397 assertions, couvrant PCMA/CIM-11, Healthcare et droits du
portail. Contrôles Healthcare : listes et détails réels, refus interclubs et rôles
non médicaux, mutation/suppression persistées, patient immuable, création sans
FIFA ID, aucune prédiction artificielle, export filtré, listes JSON et champs
AUT/dentaires, dates nulles, rendu français/anglais, rapports privés et refus
d'accès à leurs fichiers. Le test d'export a aussi été répété après élargissement
aux champs cliniques disponibles. PHP et diff vérifiés.

## Limites
Tests sur SQLite isolée et données de test ; aucune mutation clinique de production.
Session de production derrière 2Key non disponible pour l'assistant. Les vrais
providers IA, la validation clinique de modèles et la migration des anciens
rapports publics ne sont pas vérifiés. Les pièces jointes des nombreux sousmodules
du formulaire complet ne font pas l'objet d'une validation fonctionnelle globale.
Le CSV Healthcare ne comprend pas les PCMA : leur source reste pcmas.

## Ajustement visuel après signalement de la fiche 237
- CSS de production vérifié : app.css et fifa-design-system.css en HTTP 200 ; utilitaires Tailwind présents dans app.css.
- La fiche du module Healthcare utilisait une vue simplifiée. Elle utilise maintenant la fiche complète health-records.show, comme /health-records/{id}. La vue simplifiée retirée ne peut plus diverger.
- Styles d'onglets corrigés : les directives @apply du CSS inline, non compilées, sont remplacées par du CSS explicite limité à .health-record-page, avec les variables FIFA existantes. Styles insérés dans la pile styles du layout.
- 52 tests, 403 assertions réussis ; test de rendu sur les deux adresses avec données structurées. Test ciblé répété après insertion des styles dans le head.
- Pas de validation visuelle dans la session authentifiée de production ; aucune modification des données du dossier 237.
