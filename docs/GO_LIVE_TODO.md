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

- [ ] Obtenir les accès officiels FIFA TMS/ITC et la documentation d’intégration autorisée avant toute activation.
- [ ] Configurer `FIFA_API_URL`, `FIFA_API_KEY` et `FIFA_API_SECRET` dans les secrets Render ; aucune valeur factice ou par défaut n’est acceptée par FIT.
- [ ] Vérifier les endpoints transfert/ITC et le mécanisme d’authentification avec la documentation fournie à l’organisation avant le premier appel réel.
- [ ] Tester un transfert international de bout en bout dans l’environnement autorisé : soumission, référence externe, demande ITC, statut ITC et journal d’audit.
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
