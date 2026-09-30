# PCMA : suivi des corrections, 30 septembre 2026
Branche : fix/pcma-workflow-20260930.
Base : audit d78b11f, application déployée a099e3e.
## Lot vérifié
- Création et édition utilisent players.id / player_id, sans FIFA ID obligatoire du joueur.
- Scripts de création et édition ciblent le formulaire PCMA, jamais celui de langue.
- Formulaires multipart pour transmettre les fichiers.
- Édition : même liste de joueurs que la création, variable users fournie, types cohérents.
- Liens d'édition vers la route PDF réellement déclarée ; export encore non réparé.
- Champs cliniques validés conservés dans result_json selon les groupes lus par la vue d'édition.
- Valeurs nulles et zéro conservées ; aucune valeur clinique inventée.
- Suppression des logs contenant le corps médical de la requête store.
- Analyse complète sans fichier : HTTP 422, aucun appel au service IA.
- Échecs de validation IA : HTTP 422 plutôt que 500.
- Synthèse vide, en échec, simulée ou sans anomalies structurées : données insuffisantes, attente de validation médicale.
- Contrôle FIFA ID + TeamDoctor du médecin signataire conservé.
## Vérifications
PHPUnit 10.5.53 / PHP 8.4.10 : 9 tests, 55 assertions, tous réussis.
Tests : PcmaCreatePageTest, PcmaAnalysisSafetyTest.
Sources app et vues chargées depuis le worktree de réparation ; vendor du dépôt principal.
Rendu création FR/EN, édition, scope de sélection club, zéro/null, écriture et relecture SQLite isolée.
Aucun appel IA externe ni écriture dans les données opérationnelles.
## Non validé / restant
Export PDF, autosauvegarde effective, contrôle d'accès de chaque dossier et immutabilité serveur des signatures.
Contrat Node/PHP, transcription/OCR réels, FHIR et disponibilité réelle du fournisseur IA.
Workflow complet en navigateur authentifié, ergonomie mobile et rendu visuel de production.
Ce lot ne constitue pas une validation médicale ou prédictive du service IA, ni un déploiement.
