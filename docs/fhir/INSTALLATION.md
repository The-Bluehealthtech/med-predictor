# Mise en service du serveur FHIR de FIT

Ce guide installe le serveur HL7 FHIR R4 dédié à FIT (HAPI FHIR) et active la chaîne
d'interopérabilité IHE déjà livrée dans FIT :
- identité (PDQm, PIXm) ;
- IPS (sIPS, MHD) ;
- données des établissements (QEDm, IID) ;
- examens et comptes rendus ;
- sécurité (IUA, BALP) ;
- consentement (PCF).

Rien n'est créé sur Render tant que vous ne validez pas le blueprint (étape 1).

## 1. Créer le serveur sur Render (coût : un service privé + une base)

1. Render → **New → Blueprint** → dépôt `med-predictor`, branche `deploy/render`.
2. Chemin du fichier : **`fhir-server/render.yaml`**. Il décrit :
   - le service privé `fit-fhir` : Docker, plan `1c-2g`, région virginia ;
   - la base `fit-fhir-db` : Postgres 17, plan `0.5c-1g`.

   Les plans sont modifiables avant validation. Le service web de FIT existant n'est pas touché.
3. Valider. Au premier démarrage, HAPI charge ses guides : IPS 1.1.0, sIPS 1.0.0, MHD 4.2.4,
   PDQm 3.2.0, PIXm 3.1.0 et BALP 1.1.4. Ce chargement prend quelques minutes, à suivre dans les logs du service.
4. Relever l'**adresse interne** du service, rubrique *Connect → Internal* du service
   `fit-fhir`, par exemple `fit-fhir:8080`.

## 2. Configurer le service web de FIT (variables Render)

| Variable | Valeur | Obligatoire |
|---|---|---|
| `FIT_FHIR_BASE_URL` | `http://<adresse interne>/fhir`, ex. `http://fit-fhir:8080/fhir` | oui |
| `FIT_FHIR_WEBHOOK_SECRET` | valeur aléatoire d'au moins 32 caractères | oui |
| `APP_URL` | `https://fit.tbhc.uk` (point de notification `/api/fhir/notify`) | oui |
| `FIT_FHIR_SOURCE_OID` | OID de FIT comme source documentaire (registre d'OID, ex. numéro d'entreprise privée IANA) | pour publier les IPS |
| `MEDICAL_PACS_DICOMWEB_URL`, `MEDICAL_PACS_TOKEN` | point DICOMweb HTTPS du PACS (IHE RAD WIA : QIDO-RS, WADO-RS, STOW-RS) | pour lire les images des établissements dans FIT |
| `FIT_IID_VIEWER_URL` | URL HTTPS de la visionneuse du PACS (IHE RAD IID) | pour ouvrir les images des établissements |
| `FIT_FHIR_TOKEN_URL`, `FIT_FHIR_CLIENT_ID`, `FIT_FHIR_CLIENT_SECRET`, `FIT_FHIR_SCOPE` | serveur d'autorisation OAuth 2.0 (IHE IUA) | recommandé (voir §5) |
| `FIT_FHIR_AUDIT` | `true` (par défaut) : AuditEvent BALP pour chaque échange | non |

Le redéploiement du service web prend en compte les variables.

## 3. Vérifier et activer (page d'administration)

Connecté en administrateur système : **/modules → Configuration des API → Mise en service du serveur FHIR**
(adresse `/admin/fhir-setup`).

1. **Vérification** : chaque point est marqué Prêt, À faire ou Bloquant, avec l'action à mener.
2. **Conformité IHE détaillée** : compare le serveur aux déclarations officielles des acteurs IHE.
   L'opération PIXm `$ihe-pix` n'est pas confirmée pour HAPI. Si elle manque, le rapprochement des
   identités passe par la recherche PDQm, qui suffit à FIT.
3. **Installer l'abonnement des comptes rendus** : le serveur préviendra FIT à chaque compte rendu.
   Relancer la vérification : l'abonnement doit être « active ».
4. **Renvoyer les examens en attente** : il s'agit des prescriptions de laboratoire et d'imagerie créées avant l'installation.

Équivalents en ligne de commande, si un shell est disponible :
- `php artisan fhir:readiness`
- `fhir:conformance`
- `fhir:subscriptions:install`
- `fhir:orders:resend`

## 4. Consentement au partage hors du club (IHE PCF)

- Chaque fédération publie sa **politique de confidentialité** (carte « Politique de confidentialité » de /modules).
- **Adobe Sign** doit être configuré et activé (Configuration des API). C'est aujourd'hui le seul fournisseur
  dont la signature est confirmée automatiquement.
- Sans ces deux éléments, FIT refuse le partage hors du club :
  - publication de l'IPS ;
  - consultation des données des établissements.

  C'est voulu.

## 5. Sécurité

- Le serveur est un **service privé** : il n'a pas d'URL publique et n'est joignable que par le réseau privé Render.
- **IHE IUA** : FIT obtient et présente déjà un jeton OAuth 2.0 dès que les variables `FIT_FHIR_TOKEN_*` sont
  renseignées. HAPI ne vérifie pas les jetons par lui-même. Pour l'exiger, il faut installer un serveur
  d'autorisation (par exemple Keycloak) et un contrôle des jetons devant HAPI : passerelle ou intercepteur.
- **Traçabilité** : AuditEvent IHE BALP sur le serveur, en plus du journal d'audit interne de FIT.

## 6. Raccorder les sources (hors FIT)

Les EMR, LIS, RIS et PACS alimentent le serveur par leurs propres connecteurs. C'est le cas, par exemple,
d'un moteur d'intégration qui convertit les messages HL7 v2 en FHIR. Points attendus par FIT :
- Patient avec identifiants : rapprochés par le secrétariat (fiche « Identité clinique ») ;
- DiagnosticReport et Observation : rattachés aux demandes de FIT par `basedOn = ServiceRequest/<id>` ;
- ImagingStudy avec l'UID d'étude (`urn:dicom:uid`) : les images sont lues sur le PACS en DICOMweb et affichées dans la
  visionneuse de FIT, ou ouvertes dans la visionneuse du PACS (IID).
