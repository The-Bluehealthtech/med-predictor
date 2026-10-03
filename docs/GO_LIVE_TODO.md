# FIT - Go Live / activation checklist

This checklist tracks production dependencies that must not be simulated in code.

## Biometric integrity

- [ ] Verify Render secret variables from an interactive SSH session on the live service before enabling biometric providers.
- [ ] Confirm `AWS_ACCESS_KEY_ID`, `AWS_SECRET_ACCESS_KEY` and `AWS_DEFAULT_REGION` are present in Render and scoped to the minimum required AWS permissions.
- [ ] Confirm AWS IAM allows `rekognition:CompareFaces` only for the FIT runtime identity where practical.
- [ ] Validate `AWS_REKOGNITION_SIMILARITY_THRESHOLD` with federation policy before operational use.
- [ ] Obtain the licensed signotec Biometrics API SDK/documentation and licence from signotec.
- [ ] Deploy the internal FIT signotec bridge and configure `SIGNOTEC_BRIDGE_URL`, `SIGNOTEC_BRIDGE_TOKEN` and `SIGNOTEC_LICENSE_ID` in Render secrets.
- [ ] Validate dynamic signature capture on approved signotec hardware before enabling automated signature comparison.
- [ ] Confirm retention, access control and audit policy for biometric references/results with the federation/DPO.

## Existing pre-Go-Live items

- [ ] Realign obsolete legacy tests with the canonical FIT workflows without weakening current security.
- [ ] Inventory FIT working copies/worktrees and remove only confirmed unused copies.
- [ ] Provision the licensed FIFA XSD package outside Git and validate `FIFA_CONNECT_XSD_PATH` in CI/Render.
- [ ] Run final FIFA validation in an authorised environment and archive the result.

## FIFA TMS / ITC

- [ ] Attendre la réception des credentials officiels du club pour FIFA TMS/ITC avant toute finalisation de l’intégration.
- [ ] À réception des credentials du club : finaliser et valider l’intégration TMS/ITC de bout en bout dans les environnements FIFA autorisés.
- [ ] Obtenir/valider la documentation d’intégration autorisée correspondant exactement aux credentials et au périmètre du club.
- [ ] Installer/configurer le SDK FIFA Connect ID/TMS officiel dans un bridge dédié et autorisé ; FIT ne doit pas inventer d’endpoint TMS direct.
- [ ] Configurer `FIFA_TMS_CLIENT_ID`, `FIFA_TMS_SECRET_KEY` et `FIFA_TMS_ENVIRONMENT` avec les credentials Azure AD fournis par FIFA, ainsi que `FIFA_TMS_BRIDGE_URL` / `FIFA_TMS_BRIDGE_TOKEN` pour le bridge SDK.
- [ ] Valider le flux métier : FIT prépare le dossier → transfert réalisé dans FIFA TMS → `tmsTransferId` rattaché → FIT récupère statuts/ITC/provenance depuis TMS.
- [ ] Tester les environnements FIFA autorisés dans l’ordre Beta → Preproduction → Production, sans mélange de credentials ou de données.
- [ ] Tester un transfert international de bout en bout dans l’environnement autorisé : préparation FIT, exécution TMS, rattachement de la référence, synchronisation ITC et journal d’audit.
- [ ] Valider l’authenticité et la vérification cryptographique des webhooks FIFA avant de laisser un webhook modifier un statut de transfert.
- [ ] Configurer `TRANSFER_DOCUMENT_DISK` sur un stockage privé durable approuvé et vérifier qu’une pièce de transfert reste téléchargeable après redéploiement/restart Render.

## Signature documentaire

- [ ] Choisir/contractualiser au moins un fournisseur de signature documentaire (signotec signoSign/Universal, Adobe Acrobat Sign ou GlobalSign DSS).
- [ ] Configurer ses secrets Render et l'activer depuis `/modules/api-connectors` après test.
- [ ] Valider les niveaux de signature requis par type de document et juridiction (simple/avancée/qualifiée, certificat, horodatage, LTV).
- [ ] Valider le parcours PCMA : PDF figé, signature numérique médecin, audit et nouvelle version en cas de modification.
- [ ] Provisionner un stockage privé durable dédié aux PDF signés et définir `DOCUMENT_SIGNATURE_DISK=signature_s3` (ou un autre disque privé durable explicitement approuvé).
- [ ] Configurer les secrets `SIGNATURE_AWS_ACCESS_KEY_ID`, `SIGNATURE_AWS_SECRET_ACCESS_KEY`, `SIGNATURE_AWS_DEFAULT_REGION` et `SIGNATURE_AWS_BUCKET` avec des droits limités au bucket/prefix de signatures.
- [ ] Vérifier qu'une signature terminée reste téléchargeable après redéploiement/restart Render avant l'ouverture aux utilisateurs.

## Serveur FHIR de FIT (interopérabilité IHE)

Guide pas à pas : `docs/fhir/INSTALLATION.md` ; vérification : `/admin/fhir-setup` (ou `php artisan fhir:readiness`).

- [ ] Créer le blueprint Render `fhir-server/render.yaml` : service privé `fit-fhir` (HAPI FHIR v8.12.0-2) et base `fit-fhir-db`.
- [ ] Renseigner `FIT_FHIR_BASE_URL` (adresse interne) et `FIT_FHIR_WEBHOOK_SECRET` sur le service web ; vérifier `APP_URL` en HTTPS.
- [ ] Contrôler la conformité IHE depuis `/admin/fhir-setup` ; installer l'abonnement des comptes rendus ; renvoyer les examens en attente.
- [ ] Obtenir un OID pour FIT (source documentaire MHD/XDS) et renseigner `FIT_FHIR_SOURCE_OID` ; aucune valeur inventée.
- [ ] Renseigner `FIT_IID_VIEWER_URL` (visionneuse du PACS, HTTPS).
- [ ] Renseigner `MEDICAL_PACS_DICOMWEB_URL` (HTTPS) et `MEDICAL_PACS_TOKEN` : lecture des images des établissements dans FIT (DICOMweb, IHE RAD WIA) ; tester QIDO-RS et WADO-RS sur un examen réel.
- [ ] Installer un serveur d'autorisation OAuth 2.0 et le contrôle des jetons devant HAPI (IHE IUA) ; renseigner `FIT_FHIR_TOKEN_URL`, `FIT_FHIR_CLIENT_ID`, `FIT_FHIR_CLIENT_SECRET`, `FIT_FHIR_SCOPE`.
- [ ] Publier la politique de confidentialité de chaque fédération ; configurer et activer Adobe Sign pour les consentements (IHE PCF).
- [ ] Raccorder les sources (EMR, LIS, RIS, PACS) au serveur et tester un circuit complet : prescription, compte rendu, notification, intégration au dossier.

