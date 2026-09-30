# PCMA : vérification HTTP après déploiement

Date : 30 septembre 2026. Version déployée : d23a1b6.

20 requêtes de production sans session ni données médicales : aucune réponse 500 et aucune erreur réseau.
Les réponses 401/419 confirment les protections ; elles ne valident pas les contrôleurs après authentification.

| Méthode | Route | HTTP | Résultat |
|---|---|---:|---|
| GET | `/pcma` | 401 | Unauthenticated. |
| GET | `/pcma/create` | 401 | Unauthenticated. |
| GET | `/pcma/dashboard` | 401 | Unauthenticated. |
| GET | `/pcma/0` | 401 | Unauthenticated. |
| GET | `/pcma/0/edit` | 401 | Unauthenticated. |
| GET | `/pcma/0/pdf` | 401 | Unauthenticated. |
| GET | `/pcma/0/files/ecg_file` | 401 | Unauthenticated. |
| GET | `/api/signed-pcmas` | 401 | Unauthenticated. |
| GET | `/api/v1/pcmas` | 401 | Unauthenticated. |
| GET | `/api/v1/pcmas/signed` | 401 | Unauthenticated. |
| POST | `/api/pcma/auto-save` | 419 | CSRF token mismatch. |
| POST | `/pcma/pdf` | 419 | CSRF token mismatch. |
| POST | `/api/pcma/pdf` | 401 | Unauthenticated. |
| POST | `/api/v1/pcmas/ai-analyze-ecg` | 401 | Unauthenticated. |
| POST | `/api/v1/pcmas/ai-analyze-mri` | 401 | Unauthenticated. |
| POST | `/api/v1/pcmas/ai-analyze-complete` | 401 | Unauthenticated. |
| POST | `/api/v1/pcmas/whisper-transcribe` | 401 | Unauthenticated. |
| POST | `/api/v1/pcmas/ocr-extract` | 401 | Unauthenticated. |
| POST | `/api/v1/pcmas/prefill-from-transcript` | 401 | Unauthenticated. |
| POST | `/api/v1/pcmas/fetch-fhir-data` | 401 | Unauthenticated. |

GET /pcma/create sans Accept JSON renvoie 302 vers la connexion.

## Non vérifié en production
- Création, édition, autosauvegarde et PDF après connexion.
- Signature médicale avec inscription TeamDoctor active.
- Interactions OCR/FHIR, ergonomie et affichage mobile.
- Fournisseurs IA réels et qualité clinique.
- Accès navigateur cloud bloqué par ERR_BLOCKED_BY_CLIENT ; protection 2Key signalée par l’utilisateur.

Les 26 tests Laravel (179 assertions) et 6 tests Node précédents valident le code avec données isolées et contrats réseau simulés, pas une recette clinique en production.
