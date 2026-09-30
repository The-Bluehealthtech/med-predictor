# PCMA : corrections vérifiées, 30 septembre 2026
Branche : fix/pcma-workflow-20260930. Base déployée : a099e3e ; audit : d78b11f.
## Corrections
- Identité du joueur : players.id / player_id ; FIFA ID facultatif pour le joueur.
- Médecin signataire : contrôles FIFA ID et inscription active TeamDoctor conservés.
- Création/édition : formulaire PCMA ciblé, multipart, listes de joueurs limitées au périmètre autorisé.
- Données cliniques conservées dans la base opérationnelle, avec zéro et null distincts.
- Autosauvegarde persistante et idempotente ; finalisation du même brouillon, sans doublon.
- Accès médical contrôlé sur listes, dossiers, PDF, fichiers et API ; dossiers signés immuables via Eloquent.
- Champs de signature/certification fournis par le navigateur neutralisés hors parcours vérifié.
- Clôture d'un dossier : conclusion médicale requise, y compris sur mise à jour directe.
- PDF réel : aperçu sans sauvegarde et export du dossier enregistré ; textes échappés, aucune absence clinique inventée.
- Nouvelles pièces médicales privées ; téléchargement soumis aux droits du dossier.
- Routes complete/fail en POST ; actions absentes remplacées ; route signed non masquée par un identifiant.
- Contrat PHP/Node unifié : résultat structuré requis, simulations et replis fictifs refusés.
- Validation invalide : 422 ; service IA indisponible : 503 ; aucune autorisation médicale automatique.
- Transcription et OCR : octets du document transmis au fournisseur ; confiance non mesurée = null.
- Clé interne IA obligatoire côté Node ; aucune clé fournisseur exposée au navigateur.
- Double chargement du script de reconnaissance vocale supprimé.
## Tests exécutés
- PHP 8.4.10 / PHPUnit 10.5.53 : 26 tests, 179 assertions, tous réussis.
- Node 24.4.1 : 6 tests de contrat, tous réussis, après npm ci depuis le verrou existant.
- Sources, routes et vues du worktree ; dépendances PHP du dépôt principal ; SQLite isolée.
- Tests HTTP de droits, sauvegarde, PDF, fichiers privés, routes IA et signature falsifiée.
- Rendu serveur FR/EN et édition vérifiés ; aucun appel fournisseur réel ni écriture opérationnelle.
## Limites et vérifications restantes
- Pas de déploiement de ce lot, ni de recette complète dans le navigateur de production authentifié.
- Modes OCR/FHIR : doublons de contrôles et gestionnaires historiques identifiés ; recette visuelle/interactions encore requise.
- Fournisseurs réels et qualité clinique/prédictive non validés ; les tests simulent uniquement les contrats réseau.
- Configuration nécessaire sans secrets dans Git : AI_SERVICE_URL (ou AI_BASE_URL), AI_API_KEY partagé, GEMINI_API_KEY, MED_GEMINI_MODEL ; OPENAI_API_KEY pour transcription.
- DICOM : conservation possible ; analyse directe non prise en charge par le contrat image/PDF, renvoie une validation explicite.
- FHIR : URL limitée au serveur configuré ; correspondance patient/joueur et transfert clinique complet encore à valider.
- Les anciens fichiers publics ne sont pas migrés par ce lot ; le viewer DICOM historique nécessite une revue propre.
- Immutabilité Eloquent testée ; concurrence signature/édition simultanée sur tous les anciens écrivains non vérifiée.
- Aucune dépendance installée dans node_modules n'est incluse dans le commit ; installation standard npm ci nécessaire.
## Suivi des erreurs HTTP 500
- /pcma/create : correctif a099e3e déjà déployé précédemment ; utilisateur avait confirmé l'ouverture.
- Branche : inventaire des routes PCMA sans action de contrôleur manquante ; 7 endpoints IA à entrée vide renvoient 422, sans 500.
- PDF et autosauvegarde : réponses testées sur le code de branche ; état de production après ce lot non vérifié.
- Les autres routes historiquement signalées (/dtn, /rpm, /test-referee-assignments) ne sont pas retestées dans ce lot PCMA.

## Alignement portail / dossier opérationnel
- PlayerPortalDataService lit toujours pcmas sur la connexion Laravel principale, filtrée par player_id.
- Projection commune PlayerPcmaData depuis la ligne enregistrée : final_statement.overall_decision, sans conclusion déduite du seul statut completed/approved.
- FIT / NOT_FIT / CONDITIONAL : décision médicale affichée explicitement ; statut non signé conservé et aucune certification automatique.
- Anciens drapeaux explicites de décision reconnus seulement si un seul est actif ; contradictions = valeur manquante.
- Prochaine date issue uniquement de result_json.next_assessment_date ; suppression du repli automatique assessment_date + 1 an.
- Carte PCMA et résumé de conformité utilisent la même projection ; labels de conclusion FR/EN.
- Dernier dossier : tri assessment_date puis id pour une sélection déterministe.
- Vérification : 28 tests Laravel, 203 assertions, réussis ; compilation de la vue portail et syntaxe PHP réussies.
- Test intégré : écriture via contrôleur PCMA puis lecture SQL par player_id et projection portail, même PDO, valeurs zéro/null, absence de fuite vers un autre joueur.
- Le rendu complet en navigateur authentifié de production n'est toujours pas vérifié.
