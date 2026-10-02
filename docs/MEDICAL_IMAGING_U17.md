# Imagerie médicale et revue U-17

## Parcours utilisateur

Dans `/health-records/{id}`, les modules Imagerie, IRM et Scintigraphie ouvrent le même espace d'examens. La carte Vérification âge U-17 préremplit un examen IRM du poignet. Les anciens formulaires restent accessibles par le lien Historique détaillé (`legacy=1`). Aucune reprise automatique des anciens comptes rendus n'est effectuée.

1. Créer un examen daté et indiquer le centre source.
2. Importer les fichiers et confronter l'identité DICOM au profil FIT. Documenter la vérification.
3. Lire les coupes, régler contraste et zoom, sélectionner les images de référence et annoter des segments. Les mm nécessitent une calibration DICOM ; sa pertinence reste à vérifier.
4. Enregistrer un brouillon, relire puis valider avec un rôle médecin autorisé.
5. Télécharger PDF ou DICOM SR. L'envoi au PACS nécessite une action explicite et une configuration serveur.

Une version validée est immuable. Une correction crée une nouvelle version avec référence au SR précédent. Les révisions concurrentes d'un brouillon sont détectées. La validation conserve l'identité FIT et le nom du validateur ; le SR conserve l'identité source des images pour le rapprochement PACS. La validation est nominative, sans signature cryptographique.

## Limites cliniques

Le logiciel ne diagnostique pas les images. Le compte rendu est rédigé et validé par un professionnel autorisé. Le contrôle U-17 documente le grade de fusion du radius distal, la qualité, la population concernée, une éventuelle seconde lecture et ses désaccords, le règlement et sa date limite, les documents d'identité discordants, ainsi que l'information et le consentement.

Le grade I–VI du protocole masculin n'est pas appliqué à la population féminine ou indéterminée. Une image non interprétable ne peut recevoir de grade. Un grade VI entraîne une demande de revue, sans déduction d'un âge réel, d'une fraude ou d'une éligibilité. Les autres grades ne certifient pas l'âge déclaré. Le règlement applicable doit être documenté ; aucune limite de compétition n'est codée implicitement.

Les antécédents de blessure, les résultats biologiques et les performances ne servent pas à inférer un âge. La vigilance compare les dates de naissance documentées, les métadonnées des images et le profil FIT, avec indication de la source. Les données absentes et les limites de population/qualité sont signalées. Aucun score prédictif d'âge n'est produit.

Références de contexte : [étude IRM du poignet masculin](https://pubmed.ncbi.nlm.nih.gov/17021001/), [limites chez les joueuses](https://pubmed.ncbi.nlm.nih.gov/25880786/). Les critères d'admission relèvent du règlement de la compétition et de sa procédure humaine.

## Formats et interopérabilité

- Images DICOM natives : octets source et UID conservés. Une étude ne peut mélanger identités ou StudyInstanceUID. Le U-17 exige des objets IRM DICOM.
- JPEG/PNG : conversion explicitement marquée en DICOM Secondary Capture, sans invention d'une calibration. Ces fichiers ne constituent pas un examen IRM U-17.
- Limites : 20 Mo/fichier (également après conversion raster), 100 fichiers et 100 Mo/import, 500 objets/examen, 25 millions de pixels/frame et 10 000 frames/objet.
- Le décodeur affiche les formats pris en charge par pydicom/Pillow. Une syntaxe de transfert non prise en charge produit un message explicite et permet le téléchargement source. Ce lecteur n'offre pas de reconstruction 3D, de MPR ni d'analyse automatique.
- Export **Enhanced SR Storage** générique : contenu textuel, identité et observateur, références IMAGE, coordonnées SCOORD et mesures NUM en mm quand disponibles. Les concepts propres au U-17 utilisent le schéma privé déclaré `99FIT`. Aucune conformité TID 1500/TID 2000 n'est revendiquée.
- Envoi DICOMweb **STOW-RS** HTTPS vers `{MEDICAL_PACS_DICOMWEB_URL}/studies`, rapport et objets référencés en `multipart/related`. Une transmission est limitée à 100 Mo. FIT exige un reçu DICOM JSON confirmant chaque SOPInstanceUID. Un succès HTTP seul ne suffit pas. DIMSE/C-STORE n'est pas implémenté.
- La réception et l'affichage du SR, des codes privés et des annotations doivent être testés sur le PACS cible. Le validateur structurel et les reçus simulés ne remplacent pas cette recette.

## Déploiement

Le Dockerfile installe Python dans `/opt/fit-imaging`, avec `pydicom==3.0.1`, `numpy==2.2.6`, `Pillow==11.3.0`. Le service PHP exécute le worker par entrée standard, sans fichier patient temporaire. Les images sont chiffrées en base avec la clé Laravel existante : prévoir la capacité de stockage et conserver `APP_KEY` stable.

La migration `2026_10_02_000001_create_medical_imaging_workspace.php` est incluse dans la liste du déploiement FIT. Les tables préfixées `fit_imaging_` séparent examens, objets et versions des rapports. Le dossier principal demeure disponible si la migration n'a pas été appliquée ; l'espace imagerie indique son indisponibilité.

Variables facultatives :

| Variable | Usage |
| --- | --- |
| `MEDICAL_IMAGING_PYTHON` | Chemin Python alternatif en développement |
| `MEDICAL_PACS_DICOMWEB_URL` | URL de base DICOMweb HTTPS du PACS |
| `MEDICAL_PACS_TOKEN` | Jeton Bearer serveur, jamais fourni au navigateur |

Sans configuration PACS, les exports restent disponibles et aucun envoi n'est réalisé. Les accès utilisent les rôles médicaux et le périmètre club/association du dossier. Les réponses images et rapports interdisent la mise en cache. Aucun dossier ou image n'est envoyé à un service IA externe.

## Vérification

```sh
vendor/bin/phpunit --no-coverage tests/Feature/MedicalImagingWorkflowTest.php tests/Unit/Components/MedicalWorkspaceRenderTest.php
python3 tests/Deployment/medical_imaging_dicom_test.py
# Test Linux (GNU sed)
python3 tests/Deployment/render_start_test.py bin/render-start.sh
```

Les tests de pixels/SR nécessitent les dépendances Python ; utiliser `MEDICAL_IMAGING_TEST_PYTHON` si nécessaire. Les fixtures sont synthétiques. Pour le test d'interaction navigateur : installer Playwright hors du dépôt, capturer le Blade avec `FIT_IMAGING_CAPTURE_UI=1` pendant le test PHP, fournir `FIT_IMAGING_UI_HTML` et `FIT_IMAGING_UI_PNG` (PNG synthétique), puis lancer `tests/Deployment/medical_imaging_ui_test.cjs` avec `NODE_PATH` et éventuellement `FIT_TEST_CHROME`. Le test contrôle références, mesure, limites U-17, absence d'erreurs JavaScript et débordement aux largeurs 1440 et 390 px.
