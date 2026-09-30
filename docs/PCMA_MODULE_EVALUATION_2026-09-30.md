# Évaluation du module PCMA — 30 septembre 2026

## Verdict

Le formulaire de création s'affiche, mais le module n'est pas validé de bout en bout. Des incohérences bloquent l'enregistrement, l'édition et le PDF. Les fonctions IA présentes sont des aides génératives ou des simulations, pas une chaîne de prédiction médicale dont la fiabilité a été mesurée. L'analyse complète peut annoncer une aptitude sans aucune donnée : défaut prioritaire.

## Périmètre et méthode

- Référence : branche de déploiement deploy/render, commit a099e3e7c83ebc563c4685b4a3d01803a0a761d3, confirmé Live par l'utilisateur.
- Lecture des routes réellement résolues, des contrôleurs web/API, du modèle PCMA, des vues, des services vocaux/FHIR et du service Node MedGemini.
- Rendus Blade de la version de déploiement exécutés avec le runtime Laravel local. Fixtures non nominatives pour les contrôles, réponses HTTP IA simulées ; aucun appel à un fournisseur IA avec des données de santé.
- Analyse du HTML rendu FR/EN et vérification de syntaxe des scripts inline avec node --check.
- Lecture de comptages locaux uniquement. Pas d'accès à la base Render, pas de test de signature sur un vrai dossier, pas de création/suppression de dossier réel.
- La revue visuelle est structurelle : pas de capture authentifiée de production ni de test tactile/mobile. Le service IA Render et son modèle effectivement configuré n'ont pas été vérifiés.
- Aucune modification fonctionnelle ni déploiement effectués dans cette évaluation.

## 1. Workflow

| Étape | État | Preuve et conséquence |
|---|---|---|
| Ouvrir la création | Fonctionne au rendu | Contrôleur create appelé ; variable TeamDoctor présente ; FR/EN rendent. |
| Choisir le joueur | Partiel | Liste issue de players et filtrée pour club/association ; HTML soumet son identifiant sous athlete_id. |
| Enregistrer | Bloqué pour le formulaire actuel | POST /pcma résout PCMAController@store, exigeant player_id. Aucun champ player_id dans le HTML. Payload représentatif : 422, erreur player_id. |
| Brouillon sans signataire | Incomplet | Le sélecteur assessor n'offre que le médecin connecté avec inscription TeamDoctor. Sans inscription il est vide, mais assessor_id reste obligatoire. storeDraft existe côté API mais n'a pas de route PCMA identifiée. |
| Profil doctor/team_doctor | Incomplet | create reconnaît club_medical/association_medical pour les joueurs ; les rôles doctor/team_doctor ne passent pas ces branches de liste. |
| Sauvegarde automatique | Simulée | Endpoint retourne success=true et un identifiant uniqid, sans écriture. Test : variation du nombre de PCMA = 0. |
| Consulter un dossier | Rendu disponible | show rend avec fixture, mais consulte athlete ; le chemin de création principal relie player_id. Risque de nom N/A et de lecture d'un stockage différent. |
| Modifier | 500 reproduit | PCMAController@edit ne transmet pas users ; pcma.edit l'utilise. Undefined variable $users. |
| Types à la modification | Incohérents | Vue propose bpma/dental/orthopedic ; update accepte cardio/neurological/musculoskeletal/general. |
| Signer | Contrôle serveur présent, UI bloquante | store vérifie FIFA ID, TeamDoctor actif, médecin connecté et club. Mais saveSignedPCMA sélectionne le formulaire de langue ; validation client échoue avant sauvegarde. |
| Après signature | Protection incomplète | update/destroy/complete/fail ne contrôlent pas l'immuabilité du dossier signé, la décision finale ni la qualité du signataire. |
| Terminer/échouer | Incohérent | Routes web GET modifient le statut ; API complete/fail visent des méthodes absentes dans la version déployée. |
| Export PDF | Bloqué | generatePdf absent pour POST web et API ; exportPdf existe mais pcma.pdf Blade absent de cette version. |
| Liste des dossiers signés API | Routage incorrect | /api/v1/pcmas/signed résout show avec pcma='signed', avant la route spécifique. |

Sources : routes/web.php ; routes/api.php ; app/Http/Controllers/PCMAController.php ; app/Http/Controllers/Api/V1/PCMAController.php ; resources/views/pcma/create.blade.php et edit.blade.php.

Note de précision : plusieurs closures historiques restent dans web.php, mais les routes déclarées ensuite les remplacent. Le POST web actif est le contrôleur, pas la closure ancienne qui accepte la signature client. Les constats ci-dessus concernent la résolution effective.

## 2. Fonctionnalités et provenance des données

| Fonction | Donnée utilisée | État |
|---|---|---|
| Liste joueurs | players de la base principale | Réelle au sens « persistée », sans preuve de provenance clinique ; identifiant soumis incohérent. |
| Tableaux de bord/liste | pcmas, athlete, assessor | Requêtes présentes ; périmètre médical non filtré dans index/dashboard. |
| Constantes vitales et antécédents | Formulaire | Plusieurs champs validés ne sont pas fillable dans PCMA ; result_json créé par défaut ne contient que type/statut/date. Round-trip clinique non garanti. |
| Antécédents cardiovasculaires | Champ cardiovascular_history | La validation store attend medical_history ; mapping incohérent. |
| Pièces jointes | Uploads vers disque public | store gère certains champs singuliers, mais le formulaire n'a pas enctype multipart/form-data ; les tableaux mri_files/ct_files de l'analyse ne sont pas le même contrat que le stockage. |
| Recherche vocale FIFA | API de recherche joueurs | Le chemin searchPlayerByFifaConnect cherche un nom de démonstration fixe ; l'ID FIFA demandé n'est pas utilisé. |
| Voix dans le navigateur | Service Google Speech + extraction par règles | Configuration et microphone non testés. La « confiance » de l'extraction repose sur le nombre de champs, pas une calibration médicale. |
| Voix API Whisper | Service Node | transcribeAudio retourne un texte clinique constant, en mode mock et dans la branche censée être réelle. |
| OCR API | Service Node | extractTextFromImage retourne un rapport constant dans les deux branches. |
| FHIR | Requêtes HTTP vers serveur fourni | Lecture externe implémentée ; transfert vers les champs du formulaire non implémenté, authentification/connecteur métier non démontrés. |
| Transfert voix/OCR/FHIR | Gestionnaire des modes | Trois boutons appellent des méthodes signalant « non implémenté ». D'autres chemins d'extraction coexistent, sans workflow unique. |
| Prédictions persistées | Modèle MedicalPrediction | Modèle existant, mais les actions PCMA examinées ne constituent pas une chaîne de prédiction persistée/versionnée. |

Comptages de la base locale lors de l'audit : 825 players, 0 athletes, 0 pcmas, 0 medical_predictions. Ces nombres ne décrivent PAS Render. Ils ne permettent pas de valider un historique médical longitudinal ni un modèle prédictif sur données réelles. L'utilisateur a indiqué que les données de personnes sont fictives ; des lignes en base ne deviennent pas des observations cliniques réelles pour cette raison.

L'identité doit être players.id, avec rattachement explicite éventuel à athlete. Le FIFA ID du joueur ne doit pas être nécessaire pour alimenter un dossier. La vérification FIFA ID + TeamDoctor du médecin signataire reste une exigence distincte.

## 3. Interface, lisibilité et utilisation

Constats du HTML FR/EN rendu :
- Deux formulaires : changement de langue en premier, PCMA en second. querySelector('form') sélectionne le mauvais formulaire pour signature, PDF et évaluation d'aptitude IA.
- Huit IDs dupliqués : image-upload, fhir_server_url, fhir_patient_id, fhir_resource_type, fetch-fhir-data, clear-fhir, fhir-results, fhir-content. Les sélecteurs peuvent cibler le mauvais panneau.
- SpeechRecognitionService-laravel.js est chargé deux fois.
- Les règles CSS !important imposent l'affichage des sections vocales même lorsque le gestionnaire de modes les masque. Plusieurs gestionnaires et initialisations des mêmes boutons coexistent.
- Les cartes et grilles adaptatives sont présentes. Leur lisibilité réelle sur mobile et le focus clavier/modales restent à tester visuellement.
- Les résultats d'aptitude IA utilisent innerHTML avec des champs provenant du service ; absence d'échappement systématique. Les scores utilisent || 'N/A', donc une valeur 0 disparaît.
- Des textes français/anglais restent littéraux dans les vues et scripts (exemples : champs d'édition et messages des analyses). Rendu EN disponible ne signifie pas traduction exhaustive.
- Les scripts inline rendus passent node --check : absence d'erreur de syntaxe détectée, mais pas preuve d'absence d'erreur runtime.

## 4. IA et prédictions

### Défauts reproduits

1. aiAnalyzeComplete sans fichier : HTTP 200, success=true, medical_status=Normal, sports_eligibility=Cleared for sports. Aucun fournisseur appelé. generateOverallAssessment([]) produit cette conclusion par défaut.
2. aiAnalyzeEcg sans fichier : HTTP 500 au lieu d'une erreur de validation 422.
3. Aptitude et SCAT : Laravel appelle /api/v1/med-gemini/analyze avec analysis_type et prompt, sans file_content. Le routeur Node du dépôt exige aussi file_content. Simulation de sa réponse 400 : aptitude Laravel retourne 503.
4. La synthèse médicale lit abnormalities au premier niveau, alors que la réponse Node enveloppe les résultats et que MRI/CT sont regroupés. Les mots « none » et « Aucune » sont utilisés comme tests : aucune validation de schéma homogène.
5. CT est traité via des types d'analyse xray ; le type MIME est construit en image/<extension>, y compris pour PDF/DICOM. Compatibilité fournisseur non démontrée.

### Ce que le code permet de conclure

- Une intégration Google Generative AI existe ; le modèle par défaut dans ce dépôt est gemini-1.5-flash. L'étiquette Med-Gemini du module ne prouve pas un modèle spécialisé validé.
- Le service contient un mode mock. En production sans clé il s'arrête ; en cas d'échec d'initialisation il peut repasser en mock. L'état de Render n'est pas connu.
- calculateConfidence donne des points pour la longueur du texte, les accolades et la présence de termes médicaux. Ce pourcentage n'est pas une précision mesurée, une probabilité clinique ou une fiabilité calibrée.
- Aucun entraînement, validation temporelle, calibration, référence comparative ou intervalle prédictif documenté n'a été identifié dans le chemin PCMA examiné.
- CardioPCMASubmitted diffuse un événement, mais EventServiceProvider ne lui associe aucun listener de prédiction et désactive la découverte automatique. Un consommateur externe éventuel n'est pas vérifié.
- Les analyses doivent rester des propositions à examiner par le médecin ; aucune donnée manquante ne doit produire une conclusion « normale » ou « apte ».

Sources : PCMAController aiAnalyzeComplete/callMedGeminiAI/generateOverallAssessment ; src/ai-service/routes/medGemini.js ; src/ai-service/services/medGeminiService.js ; app/Events/CardioPCMASubmitted.php ; app/Providers/EventServiceProvider.php.

## 5. Accès et confidentialité

- PCMA n'utilise pas le scope tenant du modèle Player. Les routes CRUD authentifiées et leurs méthodes ne vérifient pas systématiquement le rôle médical ni le club/association du dossier.
- Des contrôles médicaux existent sur getAthletePCMAs/getAthletePCMAStats, mais pas sur l'ensemble du CRUD.
- Le GET /api/signed-pcmas retourne les dossiers signés avec relations pour tout compte authentifié, sans périmètre métier dans cette closure.
- Le endpoint auto-save ne porte que le middleware web dans l'inventaire.
- Les données de formulaire et les analyses sont journalisées côté serveur et console navigateur. Des résultats externes sont insérés en HTML. Les pièces médicales/signatures sont stockées sur le disque public.
- Le connecteur FHIR accepte une URL libre et la contacte côté serveur sans liste de destinations autorisées dans la méthode examinée : risque de requêtes vers des destinations internes, non testé par exploitation.
- Le endpoint de clé Google Speech retourne la valeur de configuration au navigateur. Sa restriction au service prévu et ses quotas doivent être vérifiés ; aucune clé n'a été lue pendant cet audit.

## 6. Priorités et critères de validation

| Priorité | Chantier | Critère de sortie |
|---|---|---|
| P0 | Analyse vide et faux résultats | Aucun message Normal/FIT ni succès de sauvegarde sans données/écriture ; états indisponibles explicites. |
| P0 | Accès aux dossiers | Tous les chemins vérifient rôle médical et périmètre ; un autre club ne peut lire ni modifier le dossier. |
| P0 | Identité, stockage, édition | player_id canonique ; création puis réouverture conservent chaque valeur et chaque pièce ; édition sans 500. |
| P1 | Signature et cycle de vie | TeamDoctor vérifié côté serveur ; dossier signé figé/versionné ; décisions distinctes du statut ; transitions en POST/PUT. |
| P1 | PDF | Brouillon et dossier signé rendus depuis les mêmes données stockées ; aucun défaut clinique inventé. |
| P1 | UI | Formulaire ciblé par ID ; IDs uniques ; un seul gestionnaire de modes ; FR/EN et mobile vérifiés. |
| P1 | Contrats IA | Schéma unique, erreurs 422/503 cohérentes, type de fichier correct, modèle et provenance enregistrés. |
| P2 | Voix/OCR/FHIR | Services réels, transfert relu puis validé ; rattachement au joueur et source documentés. |
| P2 | Prédiction mesurée | Historique observé suffisant, séparation synthétique, validation et fiabilité mesurées avant exposition. |

Ordre recommandé : P0 avant de considérer le module opérationnel, puis parcours manuel complet + signature/PDF, puis multimodal et prédictions.

## 7. Résultats de contrôle

59 routes PCMA effectives inventoriées ; 4 actions vers des méthodes absentes. Création FR/EN : rendu OK. Index, dashboard, show avec fixture, voice-fallback : rendu OK. Edit avec fixture : erreur users. POST représentatif : 422 player_id. Autosave : succès sans écriture. IA vide : 200 avec aptitude positive. ECG vide : 500. Contrat aptitude Node : 503. Deux scripts inline compilables, huit IDs dupliqués.

Ces contrôles sont locaux et reproductibles sur le code référencé. Ils ne valident ni le layout pixel par pixel en production, ni les dossiers réels, ni un fournisseur IA opérationnel. L'ouverture /pcma/create en production a été confirmée par l'utilisateur avant cet audit.
