# Audit exhaustif des routes et vues accessibles

Exécution locale le 25 septembre 2026, branche deploy/render. Voir le JSON voisin pour chaque route enregistrée.

- Routes déclarées : 873 ; vues Blade : 637 compilées par `php artisan view:cache`.
- GET testés : 1278 observations, 639 chemins, en visiteur et administrateur.
- Autres verbes testés : 239 requêtes sans données avec identifiant inexistant.
- GET avec 500 ou exception : 147 chemins distincts.
- Requêtes mutantes avec 500 ou exception : 38 méthodes/chemins distincts.
- Actions de contrôleur absentes (inspection statique) : 27 routes.

## Portée et limites

Base SQLite de test sous transaction ; appels Laravel HTTP simulés. Les GET avec paramètres utilisent la valeur 1,
les requêtes de modification la valeur 999999999 sans corps. Un 404/422 ne valide pas un parcours avec des données réelles.
Les routes Dusk sont exclues du balayage GET. Les vues sans route ont été compilées, mais ne peuvent être rendues
avec un contexte métier arbitraire. Les résultats ne prouvent ni le déploiement ni la disponibilité en production.
Plusieurs routes partagent la même URL : le test HTTP observe la route effectivement sélectionnée.

## Résultats GET par identité

| Identité | Statut | Nombre |
|---|---:|---:|
| guest | 200 | 191 |
| guest | 204 | 1 |
| guest | 301 | 1 |
| guest | 302 | 321 |
| guest | 400 | 4 |
| guest | 403 | 3 |
| guest | 404 | 47 |
| guest | 422 | 1 |
| guest | 500 | 68 |
| guest | EXCEPTION | 2 |
| system_admin | 200 | 341 |
| system_admin | 204 | 1 |
| system_admin | 301 | 1 |
| system_admin | 302 | 25 |
| system_admin | 400 | 4 |
| system_admin | 403 | 3 |
| system_admin | 404 | 108 |
| system_admin | 410 | 7 |
| system_admin | 422 | 2 |
| system_admin | 500 | 144 |
| system_admin | EXCEPTION | 3 |

## Résultats POST, PUT, PATCH, DELETE

| Méthode | Statut | Nombre |
|---|---:|---:|
| DELETE | 200 | 1 |
| DELETE | 301 | 1 |
| DELETE | 302 | 2 |
| DELETE | 404 | 20 |
| DELETE | 422 | 1 |
| DELETE | 500 | 3 |
| PATCH | 301 | 1 |
| PATCH | 404 | 6 |
| POST | 200 | 26 |
| POST | 301 | 1 |
| POST | 302 | 23 |
| POST | 400 | 4 |
| POST | 404 | 41 |
| POST | 422 | 45 |
| POST | 500 | 31 |
| POST | 503 | 4 |
| POST | EXCEPTION | 1 |
| PUT | 200 | 1 |
| PUT | 301 | 1 |
| PUT | 302 | 5 |
| PUT | 404 | 18 |
| PUT | 500 | 3 |

## GET avec 500 ou exception

| URL | Visiteur | Administrateur | Catégorie |
|---|---:|---:|---|
| `/admin/content-management/create` | 302 | 500 | Method App\Http\Controllers\ContentManagementController::create does not exist. |
| `/admin/content-management/{id}/edit` | 302 | 500 | Method App\Http\Controllers\ContentManagementController::edit does not exist. |
| `/admin/transfer-management/export` | 302 | EXCEPTION | Call to undefined method Symfony\Component\HttpFoundation\StreamedResponse::status() |
| `/administration` | 302 | 500 | Route [admin.rbac.module-permissions] not defined. |
| `/alerts/performance` | 302 | 500 | View [modules.alerts.performance] not found. |
| `/api/club/eligible-players/{competition}` | 302 | 500 | Method App\Http\Controllers\ClubManagementController::getEligiblePlayers does not exist. |
| `/api/clubs/{club}/players/daily-passport` | 302 | 500 | Method App\Http\Controllers\PassportController::clubPassport does not exist. |
| `/api/federations` | 302 | 500 | Other runtime error |
| `/api/federations/{federation}/daily-passport` | 302 | 500 | Method App\Http\Controllers\PassportController::federationPassport does not exist. |
| `/api/formation/barèmes` | 500 | 500 | Method App\Http\Controllers\Controller::index does not exist. |
| `/api/gcs/files` | 302 | 500 |  |
| `/api/gcs/stats` | 302 | 500 |  |
| `/api/joueur/{id}/stats-licences` | 500 | 500 | Method App\Http\Controllers\Controller::index does not exist. |
| `/api/modules/healthcare/{player_id}` | 302 | 500 |  |
| `/api/players/{player}/transfers` | 302 | 500 | Method App\Http\Controllers\PassportController::playerTransfers does not exist. |
| `/api/transfers` | 302 | 500 | Route [transfers.create] not defined. |
| `/api/v1/athletes/{athlete}/pcmas` | 302 | 500 | Method App\Http\Controllers\Api\V1\PCMAController::indexForAthlete does not exist. |
| `/api/v1/athletes/{athlete}/pcmas/statistics` | 302 | 500 | Method App\Http\Controllers\Api\V1\PCMAController::statisticsForAthlete does not exist. |
| `/api/v1/clinical/stats` | 500 | 500 | Other runtime error |
| `/api/v1/debug/auth` | 302 | 500 | Call to undefined method App\Models\User::getAbilities() |
| `/apple-health-kit` | 302 | 500 | View [modules.apple-health-kit.index] not found. |
| `/association/dashboard` | 302 | 500 | Route [association.fraud-detection.index] not defined. |
| `/audit-trail` | 302 | 500 | View [modules.audit-trail.index] not found. |
| `/catapult-connect` | 302 | 500 | View [modules.catapult-connect.index] not found. |
| `/club-management/dashboard` | 302 | 500 | Undefined variable $dashboardData |
| `/club-player-assignments` | 302 | 500 | View [modules.club-player-assignments.index] not found. |
| `/clubs-view/merge-options/{id}` | 500 | 500 |  |
| `/competitions/club/fixtures` | 302 | 500 | syntax error, unexpected end of file |
| `/competitions/create` | 302 | 500 | Route [competition-management.competitions.store] not defined. |
| `/content` | 302 | 500 | View [modules.content.index] not found. |
| `/contracts` | 302 | 500 | View [modules.contracts.index] not found. |
| `/daily-passport` | 302 | 500 | View [modules.daily-passport.index] not found. |
| `/data-sync` | 302 | 500 | View [modules.data-sync.index] not found. |
| `/demo-logos-officiels` | 500 | 500 | Undefined property: stdClass::$association_logo_url |
| `/dental-chart` | 500 | 500 | Route [api.dental.annotations] not defined. |
| `/dental-chart/{patient}` | 500 | 500 | Route [api.dental.annotations] not defined. |
| `/device-connections/oauth2/tokens` | 302 | 500 | View [modules.device-connections.oauth2.tokens] not found. |
| `/documents` | 302 | 500 | View [modules.documents.index] not found. |
| `/federations` | 302 | 500 | View [modules.federations.index] not found. |
| `/fifa-complete` | 500 | 500 | Undefined property: stdClass::$ghs_last_updated |
| `/fifa-debug` | 500 | 500 | Undefined constant "log" |
| `/fifa-test` | 500 | 500 | Method App\Http\Controllers\FIFATestController::test does not exist. |
| `/fifa-test-public` | 500 | 500 | View [player-portal.fifa-ultimate-complete] not found. |
| `/fifa-test-simple` | 500 | 500 | View [fifa-test-simple] not found. |
| `/fifa-test/{id}` | 500 | 500 | Method App\Http\Controllers\FIFATestController::test does not exist. |
| `/fifa-ultimate-complete` | 500 | 500 | View [player-portal.fifa-ultimate-complete] not found. |
| `/fifa-working` | 500 | 500 | Undefined variable $player |
| `/fifa/connectivity` | 302 | 500 | View [modules.fifa.connectivity] not found. |
| `/fifa/contracts` | 302 | 500 | View [modules.fifa.contracts] not found. |
| `/fifa/players/search` | 302 | 500 | View [modules.fifa.players.search] not found. |
| `/fifa/statistics` | 302 | 500 | View [modules.fifa.statistics] not found. |
| `/fifa/sync-dashboard` | 302 | 500 | View [modules.fifa.sync-dashboard] not found. |
| `/garmin-connect` | 302 | 500 | View [modules.garmin-connect.index] not found. |
| `/health-records-simple` | 500 | 500 | Undefined constant "tab" |
| `/healthcare` | 302 | 500 | Undefined variable $healthRecords |
| `/healthcare/export` | 302 | 500 | View [modules.healthcare.export] not found. |
| `/healthcare/predictions` | 302 | 500 | Undefined variable $predictions |
| `/license-requests/create` | 302 | 500 | View [modules.license-requests.show] not found. |
| `/license-requests/export` | 302 | 500 | View [modules.license-requests.show] not found. |
| `/license-requests/{id}` | 302 | 500 | View [modules.license-requests.show] not found. |
| `/license-requests/{licenseRequest}` | 302 | 500 | View [modules.license-requests.show] not found. |
| `/license-types` | 302 | 500 | View [modules.license-types.index] not found. |
| `/logs` | 302 | 500 | View [modules.logs.index] not found. |
| `/medical-predictions` | 302 | 500 | View [modules.medical-predictions.index] not found. |
| `/medical-predictions/dashboard` | 302 | 500 | Undefined variable $stats |
| `/medical-predictions/{prediction}` | 302 | 500 | Other runtime error |
| `/medical-predictions/{prediction}/edit` | 302 | 500 | Other runtime error |
| `/medical-tabs` | 500 | 500 | Undefined variable $visit |
| `/modules/finance/bank-integrations` | 500 | 500 | Route [modules.finance.test-bank-connection] not defined. |
| `/modules/finance/budgets` | 500 | 500 | Undefined variable $userType |
| `/modules/finance/reports` | 500 | 500 | Undefined variable $userType |
| `/modules/finance/transaction/edit/{id}` | 500 | 500 | View [modules.finance.transaction-edit] not found. |
| `/pcma/create` | 302 | 500 | Undefined property: stdClass::$name |
| `/player-licenses` | 302 | 500 | View [modules.player-licenses.index] not found. |
| `/player-portal/documents` | 302 | 500 | Method App\Http\Controllers\PlayerPortalController::documents does not exist. |
| `/player-portal/fifa-light` | 302 | 500 | Method App\Http\Controllers\PlayerPortalController::fifaUltimateDashboard does not exist. |
| `/player-portal/matches` | 302 | 500 | Method App\Http\Controllers\PlayerPortalController::matches does not exist. |
| `/player-portal/performances` | 302 | 500 | Method App\Http\Controllers\PlayerPortalController::performances does not exist. |
| `/player-portal/predictions` | 302 | 500 | Method App\Http\Controllers\PlayerPortalController::predictions does not exist. |
| `/player-portal/profile` | 302 | 500 | Method App\Http\Controllers\PlayerPortalController::profile does not exist. |
| `/player-portal/settings` | 302 | 500 | Method App\Http\Controllers\PlayerPortalController::settings does not exist. |
| `/player-registration` | 302 | 500 | Cannot end a section without first starting one. |
| `/player-registration/create` | 302 | 500 | Other runtime error |
| `/portail-joueur` | 302 | 500 | Other runtime error |
| `/portail-patient` | EXCEPTION | EXCEPTION | Call to undefined method Symfony\Component\HttpFoundation\BinaryFileResponse::status() |
| `/portal/medical-record` | 302 | 500 | View [modules.portal.medical-record] not found. |
| `/portal/wellness` | 302 | 500 | View [modules.portal.wellness] not found. |
| `/registration-requests` | 302 | 500 | View [modules.registration-requests.index] not found. |
| `/seasons` | 302 | 500 | View [modules.seasons.index] not found. |
| `/settings` | 302 | 500 | View [modules.settings.index] not found. |
| `/stakeholder-gallery` | 302 | 500 | View [modules.stakeholder-gallery.index] not found. |
| `/syntax-debugger` | 500 | 500 | syntax error, unexpected token "/" |
| `/system-status` | 302 | 500 | View [modules.system-status.index] not found. |
| `/teams` | 302 | 500 | Undefined variable $teams |
| `/test-association-logo` | 500 | 500 | Call to undefined method stdClass::getLogoUrl() |
| `/test-associations-view` | 500 | 500 | Undefined variable $associations |
| `/test-associations-view/show` | 500 | 500 | Undefined variable $association |
| `/test-blade` | 500 | 500 | View [test-vue-simple] not found. |
| `/test-blade-simple` | 500 | 500 |  |
| `/test-competition-main-route/{id}` | EXCEPTION | EXCEPTION | Other runtime error |
| `/test-competition-simple/{id}` | 500 | 500 | Other runtime error |
| `/test-competitions-auth` | 500 | 500 | Undefined property: stdClass::$logo_url |
| `/test-confederations-view/show` | 500 | 500 | Undefined variable $confederation |
| `/test-dental-chart` | 500 | 500 | Undefined variable $selectedPlayer |
| `/test-dental-simple` | 500 | 500 | Undefined constant "selectedTooth" |
| `/test-health-capture` | 500 | 500 | Call to undefined method Symfony\Component\Routing\CompiledRoute::getRegexPattern() |
| `/test-health-records-create` | 500 | 500 | Undefined variable $selectedPlayer |
| `/test-logos-portail` | 500 | 500 | Undefined property: stdClass::$association_logo_url |
| `/test-medical-tabs` | 500 | 500 | Undefined variable $visit |
| `/test-minimal` | 500 | 500 | Undefined variable $player |
| `/test-minimal/{playerId}` | 500 | 500 |  |
| `/test-modules-cards` | 500 | 500 | View [test-modules-cards] not found. |
| `/test-pcma-create` | 500 | 500 | Undefined variable $athletes |
| `/test-pcma-simple` | 500 | 500 | Undefined variable $athletes |
| `/test-portail-club-logos` | 500 | 500 | Undefined property: stdClass::$player_picture |
| `/test-portail-club-logos-simple` | 500 | 500 | Undefined property: stdClass::$association_logo_url |
| `/test-portail-final` | 500 | 500 | Undefined property: stdClass::$association_logo_url |
| `/test-portail-integre` | 500 | 500 | Undefined property: stdClass::$player_picture |
| `/test-portail-patient` | 500 | 500 | Undefined property: stdClass::$player_picture |
| `/test-portail-simplifie` | 500 | 500 | Undefined property: stdClass::$association_logo_url |
| `/test-portal-context` | 500 | 500 | Call to undefined method stdClass::getLogoUrl() |
| `/test-portal-debug/{playerId}` | 500 | 500 |  |
| `/test-portal-direct/{playerId}` | 500 | 500 |  |
| `/test-portal-exact` | 500 | 500 | syntax error, unexpected token "use" |
| `/test-portal-simple` | 500 | 500 | Call to undefined method stdClass::getLogoUrl() |
| `/test-portal-simulation` | 500 | 500 | Call to undefined method stdClass::getLogoUrl() |
| `/test-portal/{playerId}` | 500 | 500 |  |
| `/test-route-capture` | 500 | 500 | Call to undefined method Illuminate\Routing\RouteCollection::map() |
| `/test-simple-blade` | 500 | 500 |  |
| `/test-view-associations` | 500 | 500 | Undefined variable $associations |
| `/test-view-clubs` | 500 | 500 | Undefined variable $filtered |
| `/test-view-competitions` | 500 | 500 | Undefined variable $competitions |
| `/test-view-fifa` | 500 | 500 | Undefined variable $connectivity |
| `/test-view-healthcare` | 500 | 500 | Undefined variable $healthRecords |
| `/test-view-licenses` | 500 | 500 | Undefined variable $footballType |
| `/test-view-medical` | 500 | 500 | Undefined variable $footballType |
| `/test-view-pcma` | 500 | 500 | Undefined variable $stats |
| `/test-view-players` | 500 | 500 | Undefined variable $players |
| `/test-view-teams` | 500 | 500 | Undefined variable $teams |
| `/test-vue-debug/{playerId}` | 500 | 500 |  |
| `/test-vue-simple/{playerId}` | 500 | 500 |  |
| `/transfers` | 302 | 500 | View [modules.transfers.index] not found. |
| `/visits` | 302 | 500 | View [modules.visits.index] not found. |
| `/whisper` | 302 | 500 | Call to undefined method App\Services\WhisperService::getSupportedLanguages() |
| `/whisper/medical-prompt-types` | 302 | 500 | Call to undefined method App\Services\WhisperService::getMedicalPromptTypes() |
| `/whisper/supported-languages` | 302 | 500 | Call to undefined method App\Services\WhisperService::getSupportedLanguages() |
| `/whisper/test-connection` | 302 | 500 |  |

## Autres verbes avec 500 ou exception

| Méthode | URL | Statut | Catégorie |
|---|---|---:|---|
| DELETE | `/admin/content-management/{id}` | 500 | Method App\Http\Controllers\ContentManagementController::destroy does not exist. |
| DELETE | `/api/gcs/cleanup-backups` | 500 |  |
| DELETE | `/clubs-view/delete/{id}` | 500 |  |
| POST | `/admin/content-management` | 500 | Method App\Http\Controllers\ContentManagementController::store does not exist. |
| POST | `/api/google-assistant/test` | EXCEPTION | Target class [google.assistant.auth] does not exist. |
| POST | `/api/modules/healthcare` | 500 |  |
| POST | `/api/modules/healthcare/careplans` | 500 |  |
| POST | `/api/modules/medical` | 500 |  |
| POST | `/api/modules/medical/injuries` | 500 |  |
| POST | `/api/secretary/appointments` | 500 |  |
| POST | `/api/v1/clinical/analyze-pcma/{pCMAId}` | 500 |  |
| POST | `/api/v1/clinical/analyze-visit/{visitId}` | 500 |  |
| POST | `/api/v1/licenses/fraud-detection/analyze/{licenseId}` | 500 |  |
| POST | `/api/v1/pcmas/ai-analyze-ecg` | 500 |  |
| POST | `/api/v1/pcmas/ai-analyze-mri` | 500 |  |
| POST | `/api/v1/pcmas/dicom-viewer/process` | 500 |  |
| POST | `/api/v1/pcmas/{pcma}/complete` | 500 | Method App\Http\Controllers\Api\V1\PCMAController::complete does not exist. |
| POST | `/api/v1/pcmas/{pcma}/fail` | 500 | Method App\Http\Controllers\Api\V1\PCMAController::fail does not exist. |
| POST | `/competitions/sync-all` | 500 | Method App\Http\Controllers\CompetitionManagementController::syncAll does not exist. |
| POST | `/health-records/generate-hl7-cda` | 500 |  |
| POST | `/pcma/ai-analyze-ecg` | 500 |  |
| POST | `/pcma/ai-analyze-mri` | 500 |  |
| POST | `/pcma/ai-analyze-xray` | 500 |  |
| POST | `/pcma/ai-fitness-assessment` | 500 | Other runtime error |
| POST | `/pcma/ai/ecg` | 500 |  |
| POST | `/pcma/ai/ecg-effort` | 500 |  |
| POST | `/pcma/ai/fitness` | 500 | Other runtime error |
| POST | `/pcma/ai/mri` | 500 |  |
| POST | `/pcma/ai/scat` | 500 |  |
| POST | `/pcma/ai/scintigraphy` | 500 |  |
| POST | `/pcma/ai/xray` | 500 |  |
| POST | `/whisper/batch-transcribe` | 500 | Call to undefined method App\Services\WhisperService::getSupportedLanguages() |
| POST | `/whisper/transcribe` | 500 | Call to undefined method App\Services\WhisperService::getSupportedLanguages() |
| POST | `/whisper/transcribe-medical-consultation` | 500 | Call to undefined method App\Services\WhisperService::getSupportedLanguages() |
| POST | `/whisper/transcribe-medical-dictation` | 500 | Call to undefined method App\Services\WhisperService::getSupportedLanguages() |
| PUT | `/admin/content-management/{id}` | 500 | Method App\Http\Controllers\ContentManagementController::update does not exist. |
| PUT | `/api/modules/medical/{player_id}/current` | 500 |  |
| PUT | `/player-portal/profile` | 500 | Method App\Http\Controllers\PlayerPortalController::updateProfile does not exist. |

## Actions de contrôleur absentes

| Méthode | URL | Action |
|---|---|---|
| POST | `/api/v1/pcmas/{pcma}/complete` | `App\Http\Controllers\Api\V1\PCMAController@complete` |
| POST | `/api/v1/pcmas/{pcma}/fail` | `App\Http\Controllers\Api\V1\PCMAController@fail` |
| GET,HEAD | `/api/v1/athletes/{athlete}/pcmas` | `App\Http\Controllers\Api\V1\PCMAController@indexForAthlete` |
| GET,HEAD | `/api/v1/athletes/{athlete}/pcmas/statistics` | `App\Http\Controllers\Api\V1\PCMAController@statisticsForAthlete` |
| GET,HEAD | `/api/club/eligible-players/{competition}` | `App\Http\Controllers\ClubManagementController@getEligiblePlayers` |
| GET,HEAD | `/api/clubs/{club}/players/daily-passport` | `App\Http\Controllers\PassportController@clubPassport` |
| GET,HEAD | `/api/federations/{federation}/daily-passport` | `App\Http\Controllers\PassportController@federationPassport` |
| GET,HEAD | `/api/players/{player}/transfers` | `App\Http\Controllers\PassportController@playerTransfers` |
| GET,HEAD | `/api/joueur/{id}/historique-licences` | `App\Http\Controllers\Controller@index` |
| GET,HEAD | `/api/joueur/{id}/stats-licences` | `App\Http\Controllers\Controller@index` |
| GET,HEAD | `/api/formation/barèmes` | `App\Http\Controllers\Controller@index` |
| GET,HEAD | `/admin/content-management/create` | `App\Http\Controllers\ContentManagementController@create` |
| POST | `/admin/content-management` | `App\Http\Controllers\ContentManagementController@store` |
| GET,HEAD | `/admin/content-management/{id}/edit` | `App\Http\Controllers\ContentManagementController@edit` |
| PUT | `/admin/content-management/{id}` | `App\Http\Controllers\ContentManagementController@update` |
| DELETE | `/admin/content-management/{id}` | `App\Http\Controllers\ContentManagementController@destroy` |
| POST | `/competitions/sync-all` | `App\Http\Controllers\CompetitionManagementController@syncAll` |
| GET,HEAD | `/player-portal/profile` | `App\Http\Controllers\PlayerPortalController@profile` |
| PUT | `/player-portal/profile` | `App\Http\Controllers\PlayerPortalController@updateProfile` |
| GET,HEAD | `/player-portal/predictions` | `App\Http\Controllers\PlayerPortalController@predictions` |
| GET,HEAD | `/player-portal/performances` | `App\Http\Controllers\PlayerPortalController@performances` |
| GET,HEAD | `/player-portal/matches` | `App\Http\Controllers\PlayerPortalController@matches` |
| GET,HEAD | `/player-portal/documents` | `App\Http\Controllers\PlayerPortalController@documents` |
| GET,HEAD | `/player-portal/settings` | `App\Http\Controllers\PlayerPortalController@settings` |
| GET,HEAD | `/player-portal/fifa-light` | `App\Http\Controllers\PlayerPortalController@fifaUltimateDashboard` |
| GET,HEAD | `/fifa-test` | `App\Http\Controllers\FIFATestController@test` |
| GET,HEAD | `/fifa-test/{id}` | `App\Http\Controllers\FIFATestController@test` |

## Correction vérifiée après l'instantané

- `/administration` : 500 dans l'inventaire initial ; 200 en administrateur après suppression du lien vers une page de permissions non implémentée. Le JSON reste l'instantané initial.
- `/association/dashboard` : 500 dans l'inventaire initial ; 200 en administrateur après désactivation de trois raccourcis vers des pages de détection de fraude inexistantes. L'API de détection n'est pas modifiée.
- `/competitions/create`, `/player-registration`, `/teams`, `/healthcare`, `/club-management/dashboard` : 200 en administrateur dans le test de rendu après correction des noms de routes, de la section Blade dupliquée et de l'alimentation des vues par des données persistées. Le GET invité de `/healthcare` redirige vers la connexion. Ces vérifications ne couvrent pas les formulaires soumis ni les accès avec des enregistrements réels.
- `/fifa/connectivity` et `/fifa/statistics` : 200 en administrateur via le tableau de bord FIFA existant, avec son état de connectivité et les statistiques persistées ; l'appel HTTP externe est simulé pendant le test.
- `/api/v1/athletes/{athlete}/pcmas` et `/statistics` : correction des actions vers les méthodes existantes ; tests des réponses authentifiées, des données vides et du refus d'un rôle non médical. Les mutations PCMA restent à traiter.
- `/api/federations` et `/api/federations/{federation}` : routage vers les méthodes JSON existantes du contrôleur au lieu des vues HTML ; liste testée sous token administrateur.
