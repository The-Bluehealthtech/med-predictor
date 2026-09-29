# Inventaire des interfaces vers la base principale

Routes actives : 692. Routes de commande ou avec écriture directe repérée : 241.

Audit statique des méthodes réellement enregistrées. Les appels délégués nécessitent une vérification des services et un test de persistance. Une détection directe ne prouve pas que la transaction ou l'autorisation est correcte. Aucun scénario utilisateur n'est exécuté par cet audit.

Connexions nommées explicites repérées dans app : 0.


| Méthodes | URI | Action | Résultat statique |
|---|---|---|---|
| POST | _ignition/execute-solution | Spatie\LaravelIgnition\Http\Controllers\ExecuteSolutionController@__invoke | délégation ou commande à examiner |
| POST | _ignition/update-config | Spatie\LaravelIgnition\Http\Controllers\UpdateConfigController@__invoke | délégation ou commande à examiner |
| POST | account-request | App\Http\Controllers\AccountRequestController@store | délégation ou commande à examiner |
| POST | admin/account-requests/{id}/approve | Closure | écriture directe repérée |
| POST | admin/account-requests/{id}/contact | Closure | écriture directe repérée |
| POST | admin/account-requests/{id}/reject | Closure | écriture directe repérée |
| POST | admin/assign-referees | App\Http\Controllers\AdminRefereeAssignmentController@assignReferees | écriture directe repérée |
| POST | admin/audit-trail/cleanup | App\Http\Controllers\AuditTrailController@cleanup | écriture directe repérée |
| POST | admin/content-management | App\Http\Controllers\ContentManagementController@store | délégation ou commande à examiner |
| DELETE | admin/content-management/{id} | App\Http\Controllers\ContentManagementController@destroy | délégation ou commande à examiner |
| PUT | admin/content-management/{id} | App\Http\Controllers\ContentManagementController@update | délégation ou commande à examiner |
| POST | admin/rbac/initialize-permissions | App\Http\Controllers\RBACController@initializePermissions | écriture directe repérée |
| POST | admin/rbac/module-permissions | App\Http\Controllers\RBACController@updateModulePermissions | délégation ou commande à examiner |
| POST | admin/rbac/permissions | App\Http\Controllers\RBACController@createPermission | écriture directe repérée |
| POST | admin/rbac/roles | App\Http\Controllers\RBACController@createRole | écriture directe repérée |
| DELETE | admin/rbac/roles/{id} | App\Http\Controllers\RBACController@deleteRole | écriture directe repérée |
| PUT | admin/rbac/roles/{id} | App\Http\Controllers\RBACController@updateRole | écriture directe repérée |
| POST | admin/rbac/users/{userId}/assign-role | App\Http\Controllers\RBACController@assignRole | écriture directe repérée |
| POST | admin/system-settings | App\Http\Controllers\SystemSettingsController@store | écriture directe repérée |
| POST | admin/system-settings/bulk-update | App\Http\Controllers\SystemSettingsController@updateBulk | écriture directe repérée |
| POST | admin/system-settings/initialize | App\Http\Controllers\SystemSettingsController@initialize | délégation ou commande à examiner |
| DELETE | admin/system-settings/{id} | App\Http\Controllers\SystemSettingsController@destroy | écriture directe repérée |
| PUT | admin/system-settings/{id} | App\Http\Controllers\SystemSettingsController@update | écriture directe repérée |
| POST | admin/system-settings/{id}/reset | App\Http\Controllers\SystemSettingsController@reset | écriture directe repérée |
| POST | admin/transfer-management/sync-fifa-tms | App\Http\Controllers\TransferManagementController@syncFifaTms | délégation ou commande à examiner |
| POST | admin/transfer-management/{id}/approve | App\Http\Controllers\TransferManagementController@approve | écriture directe repérée |
| POST | admin/transfer-management/{id}/reject | App\Http\Controllers\TransferManagementController@reject | écriture directe repérée |
| POST | ai-testing/injury-prediction | App\Http\Controllers\AITestingController@testInjuryPrediction | délégation ou commande à examiner |
| POST | ai-testing/medical-diagnosis | App\Http\Controllers\AITestingController@testMedicalDiagnosis | délégation ou commande à examiner |
| POST | ai-testing/performance-analysis | App\Http\Controllers\AITestingController@testPerformanceAnalysis | délégation ou commande à examiner |
| POST | ai-testing/run-tests | App\Http\Controllers\AITestingController@runTests | délégation ou commande à examiner |
| POST | ai-testing/test-provider | App\Http\Controllers\AITestingController@testProvider | délégation ou commande à examiner |
| POST | api/apply-predefined-roles | Closure | écriture directe repérée |
| POST | api/calendar/competitions/{competition}/generate-full-schedule | App\Http\Controllers\CalendarManagementController@generateFullSchedule | délégation ou commande à examiner |
| POST | api/calendar/competitions/{competition}/matches/validate-and-create | App\Http\Controllers\CalendarManagementController@validateAndCreateMatch | écriture directe repérée |
| DELETE | api/calendar/competitions/{competition}/schedule | App\Http\Controllers\CalendarManagementController@clearSchedule | écriture directe repérée |
| DELETE | api/calendar/matches/{gameMatch} | App\Http\Controllers\CalendarManagementController@deleteMatch | écriture directe repérée |
| PUT | api/calendar/matches/{gameMatch} | App\Http\Controllers\CalendarManagementController@updateMatch | écriture directe repérée |
| POST | api/clinical/consultations | App\Http\Controllers\ClinicalWorkflowController@initialConsultation | écriture directe repérée |
| POST | api/clinical/decision-support | App\Http\Controllers\ClinicalWorkflowController@clinicalDecisionSupport | délégation ou commande à examiner |
| POST | api/clinical/patients | App\Http\Controllers\ClinicalWorkflowController@createPatient | écriture directe repérée |
| PUT | api/clinical/patients/{id} | Closure | écriture directe repérée |
| POST | api/clinical/summarize | Closure | délégation ou commande à examiner |
| POST | api/clinical/symptoms | App\Http\Controllers\ClinicalWorkflowController@submitSymptoms | écriture directe repérée |
| POST | api/club/teams/{team}/players | App\Http\Controllers\ClubManagementController@addPlayerToTeam | écriture directe repérée |
| POST | api/competitions/{competition}/register-team | App\Http\Controllers\CompetitionManagementController@registerTeam | écriture directe repérée |
| POST | api/dental/annotations | App\Http\Controllers\DentalController@store | écriture directe repérée |
| DELETE | api/dental/annotations/{dentalAnnotation} | App\Http\Controllers\DentalController@destroy | écriture directe repérée |
| PUT | api/dental/annotations/{dentalAnnotation} | App\Http\Controllers\DentalController@update | écriture directe repérée |
| POST | api/dental/reset | App\Http\Controllers\DentalController@reset | écriture directe repérée |
| POST | api/dental/save-all | App\Http\Controllers\DentalController@saveAll | écriture directe repérée |
| POST | api/fit/players/{player}/performance-metrics | App\Http\Controllers\Api\PerformanceMetricRecordingController@__invoke | écriture directe repérée |
| POST | api/fit/players/{player}/performance-metrics/{metric}/verify | App\Http\Controllers\Api\PerformanceMetricVerificationController@__invoke | écriture directe repérée |
| POST | api/gcs/backup | App\Http\Controllers\GcsController@createBackup | délégation ou commande à examiner |
| DELETE | api/gcs/cleanup-backups | App\Http\Controllers\GcsController@cleanupOldBackups | délégation ou commande à examiner |
| DELETE | api/gcs/file | App\Http\Controllers\GcsController@deleteFile | délégation ou commande à examiner |
| POST | api/gcs/upload | App\Http\Controllers\GcsController@uploadFile | délégation ou commande à examiner |
| POST | api/gcs/upload-from-url | App\Http\Controllers\GcsController@uploadFromUrl | délégation ou commande à examiner |
| POST | api/gcs/upload-multiple | App\Http\Controllers\GcsController@uploadFiles | délégation ou commande à examiner |
| POST | api/google-assistant/submit-pcma | App\Http\Controllers\GoogleAssistantController@submitPcmaToFit | délégation ou commande à examiner |
| POST | api/google-assistant/test | App\Http\Controllers\GoogleAssistantController@handleIntent | écriture directe repérée |
| POST | api/google-assistant/webhook | App\Http\Controllers\GoogleAssistantController@handleIntent | écriture directe repérée |
| POST | api/league-championship/competitions/{competition}/generate-schedule | App\Http\Controllers\LeagueChampionshipController@generateSchedule | délégation ou commande à examiner |
| POST | api/league-championship/matches/{gameMatch}/events | App\Http\Controllers\LeagueChampionshipController@addEvent | écriture directe repérée |
| DELETE | api/league-championship/matches/{gameMatch}/events/{eventId} | App\Http\Controllers\LeagueChampionshipController@deleteEvent | écriture directe repérée |
| POST | api/league-championship/matches/{gameMatch}/officials | App\Http\Controllers\LeagueChampionshipController@assignOfficials | écriture directe repérée |
| POST | api/league-championship/matches/{gameMatch}/roster | App\Http\Controllers\LeagueChampionshipController@submitRoster | écriture directe repérée |
| PATCH | api/league-championship/matches/{gameMatch}/statistics | App\Http\Controllers\LeagueChampionshipController@updateStatistics | écriture directe repérée |
| PATCH | api/league-championship/matches/{gameMatch}/status | App\Http\Controllers\LeagueChampionshipController@updateMatchStatus | écriture directe repérée |
| POST | api/matches/{gameMatch}/events | Closure | écriture directe repérée |
| DELETE | api/matches/{gameMatch}/events/{event} | Closure | écriture directe repérée |
| PUT | api/matches/{gameMatch}/status | Closure | écriture directe repérée |
| POST | api/modules/healthcare | Closure | écriture directe repérée |
| POST | api/modules/healthcare/careplans | Closure | écriture directe repérée |
| POST | api/modules/medical | Closure | écriture directe repérée |
| POST | api/modules/medical/injuries | Closure | écriture directe repérée |
| PUT | api/modules/medical/{player_id}/current | Closure | écriture directe repérée |
| POST | api/pcma/auto-save | Closure | délégation ou commande à examiner |
| POST | api/pcma/store | App\Http\Controllers\PCMAController@store | écriture directe repérée |
| POST | api/referee/events/{event}/confirm | App\Http\Controllers\RefereeController@confirmEvent | délégation ou commande à examiner |
| POST | api/referee/events/{event}/contest | App\Http\Controllers\RefereeController@contestEvent | délégation ou commande à examiner |
| POST | api/referee/matches/{gameMatch}/events | App\Http\Controllers\RefereeController@recordEvent | écriture directe repérée |
| PATCH | api/referee/matches/{gameMatch}/status | App\Http\Controllers\RefereeController@updateMatchStatus | écriture directe repérée |
| GET\|HEAD | api/reports/competitions/{competition}/standings/export | App\Http\Controllers\ReportController@exportStandings | écriture directe repérée |
| POST | api/secretary/appointments | Closure | écriture directe repérée |
| DELETE | api/secretary/appointments/{date} | Closure | écriture directe repérée |
| POST | api/transfers | App\Http\Controllers\TransferController@store | écriture directe repérée |
| DELETE | api/transfers/{transfer} | App\Http\Controllers\TransferController@destroy | écriture directe repérée |
| PUT | api/transfers/{transfer} | App\Http\Controllers\TransferController@update | écriture directe repérée |
| POST | api/transfers/{transfer}/check-itc | App\Http\Controllers\TransferController@checkItcStatus | délégation ou commande à examiner |
| POST | api/transfers/{transfer}/payments | App\Http\Controllers\TransferPaymentController@store | délégation ou commande à examiner |
| DELETE | api/transfers/{transfer}/payments/{payment} | App\Http\Controllers\TransferPaymentController@destroy | délégation ou commande à examiner |
| PUT | api/transfers/{transfer}/payments/{payment} | App\Http\Controllers\TransferPaymentController@update | délégation ou commande à examiner |
| POST | api/transfers/{transfer}/submit-fifa | App\Http\Controllers\TransferController@submitToFifa | délégation ou commande à examiner |
| POST | api/users/apply-role | Closure | écriture directe repérée |
| POST | api/v1/association/fraud-detection | App\Http\Controllers\AssociationRegistrationController@fraudDetection | délégation ou commande à examiner |
| POST | api/v1/athletes/{athlete}/immunisations | App\Http\Controllers\ImmunisationController@store | écriture directe repérée |
| POST | api/v1/athletes/{athlete}/immunisations/sync | App\Http\Controllers\ImmunisationController@sync | délégation ou commande à examiner |
| POST | api/v1/clinical/analyze-pcma/{pCMAId} | App\Http\Controllers\ClinicalDataSupportController@analyzePCMA | écriture directe repérée |
| POST | api/v1/clinical/analyze-visit/{visitId} | App\Http\Controllers\ClinicalDataSupportController@analyzeVisit | écriture directe repérée |
| POST | api/v1/clinical/batch-analyze-pcma | App\Http\Controllers\ClinicalDataSupportController@batchAnalyzePCMA | délégation ou commande à examiner |
| POST | api/v1/clinical/batch-analyze-visits | App\Http\Controllers\ClinicalDataSupportController@batchAnalyzeVisits | délégation ou commande à examiner |
| POST | api/v1/clinical/report | App\Http\Controllers\ClinicalDataSupportController@generateClinicalReport | délégation ou commande à examiner |
| POST | api/v1/clinical/test-gemini | App\Http\Controllers\ClinicalDataSupportController@testGeminiConnection | délégation ou commande à examiner |
| DELETE | api/v1/immunisations/{immunisation} | App\Http\Controllers\ImmunisationController@destroy | écriture directe repérée |
| PUT | api/v1/immunisations/{immunisation} | App\Http\Controllers\ImmunisationController@update | écriture directe repérée |
| POST | api/v1/immunisations/{immunisation}/verify | App\Http\Controllers\ImmunisationController@verify | écriture directe repérée |
| POST | api/v1/licenses/fraud-detection/analyze/{licenseId} | App\Http\Controllers\LicenseController@analyzeLicenseFraud | écriture directe repérée |
| POST | api/v1/licenses/fraud-detection/batch | App\Http\Controllers\LicenseController@batchFraudDetection | écriture directe repérée |
| POST | api/v1/licenses/fraud-detection/check-all | App\Http\Controllers\LicenseController@checkAllLicenses | écriture directe repérée |
| POST | api/v1/pcmas | App\Http\Controllers\Api\V1\PCMAController@store | écriture directe repérée |
| POST | api/v1/pcmas/ai-analyze-complete | App\Http\Controllers\Api\V1\PCMAController@aiAnalyzeComplete | délégation ou commande à examiner |
| POST | api/v1/pcmas/ai-analyze-ecg | App\Http\Controllers\Api\V1\PCMAController@aiAnalyzeEcg | délégation ou commande à examiner |
| POST | api/v1/pcmas/ai-analyze-mri | App\Http\Controllers\Api\V1\PCMAController@aiAnalyzeMri | délégation ou commande à examiner |
| POST | api/v1/pcmas/dicom-viewer/process | App\Http\Controllers\Api\V1\PCMAController@processDicomFile | délégation ou commande à examiner |
| POST | api/v1/pcmas/fetch-fhir-data | App\Http\Controllers\Api\V1\PCMAController@fetchFhirData | délégation ou commande à examiner |
| POST | api/v1/pcmas/ocr-extract | App\Http\Controllers\Api\V1\PCMAController@ocrExtract | délégation ou commande à examiner |
| POST | api/v1/pcmas/prefill-from-transcript | App\Http\Controllers\Api\V1\PCMAController@prefillFromTranscript | délégation ou commande à examiner |
| POST | api/v1/pcmas/whisper-transcribe | App\Http\Controllers\Api\V1\PCMAController@whisperTranscribe | délégation ou commande à examiner |
| DELETE | api/v1/pcmas/{pcma} | App\Http\Controllers\Api\V1\PCMAController@destroy | écriture directe repérée |
| PUT | api/v1/pcmas/{pcma} | App\Http\Controllers\Api\V1\PCMAController@update | écriture directe repérée |
| POST | api/v3/analytics/export/schedule | Closure | délégation ou commande à examiner |
| POST | api/v3/medical/wearables/player/{playerId}/sync | Closure | délégation ou commande à examiner |
| POST | api/v3/security/gdpr/data-export/{userId} | Closure | délégation ou commande à examiner |
| POST | association/registration | App\Http\Controllers\AssociationRegistrationController@store | délégation ou commande à examiner |
| PUT | associations-view/update/{id} | Closure | écriture directe repérée |
| POST | associations/logos/update-national | App\Http\Controllers\AssociationLogoController@updateNationalLogos | délégation ou commande à examiner |
| PUT | associations/{association} | App\Http\Controllers\AssociationController@update | écriture directe repérée |
| POST | associations/{association}/logo/reset | App\Http\Controllers\AssociationLogoController@resetToNationalLogo | écriture directe repérée |
| POST | associations/{association}/logo/update | App\Http\Controllers\AssociationLogoController@updateLogo | écriture directe repérée |
| POST | club/{club}/logo/upload | App\Http\Controllers\ClubManagementController@uploadLogo | écriture directe repérée |
| DELETE | clubs-view/delete/{id} | Closure | écriture directe repérée |
| POST | clubs-view/merge | Closure | écriture directe repérée |
| PUT | clubs-view/update/{id} | Closure | écriture directe repérée |
| PUT | competition-management/matches/{match}/match-sheet | App\Http\Controllers\CompetitionManagementController@updateMatchSheet | écriture directe repérée |
| POST\|PUT | competition-management/matches/{match}/match-sheet/submit | App\Http\Controllers\CompetitionManagementController@submitMatchSheet | écriture directe repérée |
| POST | competitions | App\Http\Controllers\CompetitionManagementController@store | écriture directe repérée |
| POST | competitions/association/designation-arbitres/save | App\Http\Controllers\CompetitionController@saveArbitreAssignments | écriture directe repérée |
| POST | competitions/association/engage-club | App\Http\Controllers\CompetitionController@engageClub | délégation ou commande à examiner |
| POST | competitions/association/export-club-data/{clubId} | App\Http\Controllers\CompetitionController@exportClubData | délégation ou commande à examiner |
| POST | competitions/association/export-engagements | App\Http\Controllers\CompetitionController@exportEngagements | délégation ou commande à examiner |
| POST | competitions/association/match/{id}/update | App\Http\Controllers\CompetitionController@updateAssociationMatch | écriture directe repérée |
| POST | competitions/association/validate-all-engagements | App\Http\Controllers\CompetitionController@validateAllEngagements | délégation ou commande à examiner |
| POST | competitions/association/validate-engagement/{clubId} | App\Http\Controllers\CompetitionController@validateEngagement | écriture directe repérée |
| DELETE | competitions/{competition} | App\Http\Controllers\CompetitionManagementController@destroy | écriture directe repérée |
| PUT | competitions/{competition} | App\Http\Controllers\CompetitionManagementController@update | écriture directe repérée |
| POST | competitions/{competition}/register-team | App\Http\Controllers\CompetitionManagementController@registerTeam | écriture directe repérée |
| POST | competitions/{competition}/sync | App\Http\Controllers\CompetitionManagementController@sync | délégation ou commande à examiner |
| POST | confirm-password | Closure | délégation ou commande à examiner |
| POST | email/verification-notification | Closure | délégation ou commande à examiner |
| GET\|HEAD\|POST\|PUT\|PATCH\|DELETE\|OPTIONS | fifa-complete-original.html | \Illuminate\Routing\RedirectController@__invoke | délégation ou commande à examiner |
| POST | forgot-password | Closure | délégation ou commande à examiner |
| POST | gemini/analyze-medical-image | App\Http\Controllers\GoogleGeminiController@analyzeMedicalImage | délégation ou commande à examiner |
| POST | gemini/analyze-performance | App\Http\Controllers\GoogleGeminiController@analyzePerformance | délégation ou commande à examiner |
| POST | gemini/generate-diagnosis | App\Http\Controllers\GoogleGeminiController@generateDiagnosis | délégation ou commande à examiner |
| POST | gemini/generate-rehab-plan | App\Http\Controllers\GoogleGeminiController@generateRehabPlan | délégation ou commande à examiner |
| POST | gemini/generate-treatment | App\Http\Controllers\GoogleGeminiController@generateTreatment | délégation ou commande à examiner |
| POST | gemini/predict-injury-risk | App\Http\Controllers\GoogleGeminiController@predictInjuryRisk | délégation ou commande à examiner |
| POST | health-records | App\Http\Controllers\HealthRecordController@store | écriture directe repérée |
| POST | health-records/generate-hl7-cda | App\Http\Controllers\HealthRecordController@generateHl7Cda | délégation ou commande à examiner |
| DELETE | health-records/{healthRecord} | App\Http\Controllers\HealthRecordController@destroy | écriture directe repérée |
| PUT | health-records/{healthRecord} | App\Http\Controllers\HealthRecordController@update | écriture directe repérée |
| POST | health-records/{healthRecord}/generate-prediction | App\Http\Controllers\HealthRecordController@generatePrediction | délégation ou commande à examiner |
| DELETE | healthcare/records/{record} | Closure | délégation ou commande à examiner |
| PUT | healthcare/records/{record} | Closure | délégation ou commande à examiner |
| POST | joueur/{playerId}/access | App\Http\Controllers\PlayerAccessController@authenticate | écriture directe repérée |
| DELETE | joueur/{playerId}/photo | App\Http\Controllers\PlayerPhotoController@delete | écriture directe repérée |
| PUT | joueur/{playerId}/photo/external | App\Http\Controllers\PlayerPhotoController@updateExternalUrl | écriture directe repérée |
| POST | joueur/{playerId}/photo/generate | App\Http\Controllers\PlayerPhotoController@generateAvatar | écriture directe repérée |
| POST | joueur/{playerId}/photo/upload | App\Http\Controllers\PlayerPhotoController@upload | écriture directe repérée |
| POST | language | Closure | délégation ou commande à examiner |
| DELETE | license-photos/photo/{photo} | App\Http\Controllers\LicensePhotoController@deletePhoto | écriture directe repérée |
| POST | license-photos/upload-photo | App\Http\Controllers\LicensePhotoController@uploadPhoto | écriture directe repérée |
| POST | license-requests | App\Http\Controllers\LicenseRequestController@store | délégation ou commande à examiner |
| POST | license-requests/bulk-actions | App\Http\Controllers\LicenseRequestController@bulkActions | délégation ou commande à examiner |
| DELETE | license-requests/{licenseRequest} | App\Http\Controllers\LicenseRequestController@destroy | délégation ou commande à examiner |
| PUT | license-requests/{licenseRequest} | App\Http\Controllers\LicenseRequestController@update | délégation ou commande à examiner |
| POST | license-requests/{licenseRequest}/approve-by-association | App\Http\Controllers\LicenseRequestController@approveByAssociation | délégation ou commande à examiner |
| POST | license-requests/{licenseRequest}/approve-by-club | App\Http\Controllers\LicenseRequestController@approveByClub | délégation ou commande à examiner |
| POST | license-requests/{licenseRequest}/reject | App\Http\Controllers\LicenseRequestController@reject | délégation ou commande à examiner |
| POST | license-requests/{licenseRequest}/request-additional-info | App\Http\Controllers\LicenseRequestController@requestAdditionalInfo | délégation ou commande à examiner |
| POST | license-requests/{licenseRequest}/submit | App\Http\Controllers\LicenseRequestController@submit | délégation ou commande à examiner |
| POST | licenses | App\Http\Controllers\LicenseController@store | délégation ou commande à examiner |
| PATCH | licenses/{license}/approve | App\Http\Controllers\LicenseController@approve | écriture directe repérée |
| PATCH | licenses/{license}/reject | App\Http\Controllers\LicenseController@reject | écriture directe repérée |
| POST | login | App\Http\Controllers\Auth\LoginController@login | délégation ou commande à examiner |
| POST | logout | App\Http\Controllers\Auth\LoginController@logout | délégation ou commande à examiner |
| PUT | match-sheets/{match} | App\Http\Controllers\CompetitionManagementController@updateMatchSheet | écriture directe repérée |
| POST | medical-predictions | Closure | délégation ou commande à examiner |
| DELETE | medical-predictions/{prediction} | Closure | délégation ou commande à examiner |
| PUT | medical-predictions/{prediction} | Closure | délégation ou commande à examiner |
| POST | modules/finance/sync | App\Http\Controllers\FinanceController@syncWithExternal | délégation ou commande à examiner |
| POST | modules/finance/test-connection | App\Http\Controllers\FinanceController@testConnection | délégation ou commande à examiner |
| POST | modules/licenses/players/{player}/request | App\Http\Controllers\PlayerLicenseWorkflowController@store | écriture directe repérée |
| POST | modules/teams | Closure | écriture directe repérée |
| POST | modules/teams/bulk-store | Closure | écriture directe repérée |
| DELETE | modules/teams/{team} | Closure | écriture directe repérée |
| PUT | modules/teams/{team} | Closure | écriture directe repérée |
| POST | notifications/{id}/mark-as-read | Closure | délégation ou commande à examiner |
| POST | organization-cards/{type} | App\Http\Controllers\OrganizationCardController@store | écriture directe repérée |
| PUT | organization-cards/{type}/{id} | App\Http\Controllers\OrganizationCardController@update | écriture directe repérée |
| PUT | password | Closure | délégation ou commande à examiner |
| POST | pcma | App\Http\Controllers\PCMAController@store | écriture directe repérée |
| POST | pcma/ai-analyze-ct | App\Http\Controllers\PCMAController@aiAnalyzeCt | écriture directe repérée |
| POST | pcma/ai-analyze-ecg | App\Http\Controllers\PCMAController@aiAnalyzeEcg | écriture directe repérée |
| POST | pcma/ai-analyze-mri | App\Http\Controllers\PCMAController@aiAnalyzeMri | écriture directe repérée |
| POST | pcma/ai-analyze-ultrasound | App\Http\Controllers\PCMAController@aiAnalyzeUltrasound | écriture directe repérée |
| POST | pcma/ai-analyze-xray | App\Http\Controllers\PCMAController@aiAnalyzeXray | écriture directe repérée |
| POST | pcma/ai-fitness-assessment | App\Http\Controllers\PCMAController@aiFitnessAssessment | délégation ou commande à examiner |
| POST | pcma/ai/complete | App\Http\Controllers\PCMAController@aiAnalyzeComplete | écriture directe repérée |
| POST | pcma/ai/ct | App\Http\Controllers\PCMAController@aiAnalyzeCt | écriture directe repérée |
| POST | pcma/ai/ecg | App\Http\Controllers\PCMAController@aiAnalyzeEcg | écriture directe repérée |
| POST | pcma/ai/ecg-effort | App\Http\Controllers\PCMAController@aiAnalyzeEcgEffort | écriture directe repérée |
| POST | pcma/ai/fitness | App\Http\Controllers\PCMAController@aiFitnessAssessment | délégation ou commande à examiner |
| POST | pcma/ai/mri | App\Http\Controllers\PCMAController@aiAnalyzeMri | écriture directe repérée |
| POST | pcma/ai/scat | App\Http\Controllers\PCMAController@aiAnalyzeScat | délégation ou commande à examiner |
| POST | pcma/ai/scintigraphy | App\Http\Controllers\PCMAController@aiAnalyzeScintigraphy | écriture directe repérée |
| POST | pcma/ai/ultrasound | App\Http\Controllers\PCMAController@aiAnalyzeUltrasound | écriture directe repérée |
| POST | pcma/ai/xray | App\Http\Controllers\PCMAController@aiAnalyzeXray | écriture directe repérée |
| DELETE | pcma/{pcma} | App\Http\Controllers\PCMAController@destroy | écriture directe repérée |
| PUT | pcma/{pcma} | App\Http\Controllers\PCMAController@update | écriture directe repérée |
| GET\|HEAD | pcma/{pcma}/complete | App\Http\Controllers\PCMAController@complete | écriture directe repérée |
| GET\|HEAD | pcma/{pcma}/fail | App\Http\Controllers\PCMAController@fail | écriture directe repérée |
| POST | player-registration | App\Http\Controllers\PlayerRegistrationController@store | écriture directe repérée |
| POST | players | App\Http\Controllers\PlayerController@store | écriture directe repérée |
| POST | players/bulk-import | App\Http\Controllers\PlayerController@bulkImport | écriture directe repérée |
| DELETE | players/{player} | App\Http\Controllers\PlayerController@destroy | écriture directe repérée |
| PUT\|PATCH | players/{player} | App\Http\Controllers\PlayerController@update | écriture directe repérée |
| PUT | referee/settings/password | Closure | écriture directe repérée |
| PUT | referee/settings/profile | Closure | écriture directe repérée |
| POST | register | Closure | délégation ou commande à examiner |
| POST | reset-password | Closure | délégation ou commande à examiner |
| POST | secretary/appointments | Closure | délégation ou commande à examiner |
| POST | secretary/documents/upload | Closure | délégation ou commande à examiner |
| POST | user-management | Closure | écriture directe repérée |
| DELETE | user-management/{user} | Closure | écriture directe repérée |
| PUT | user-management/{user} | Closure | écriture directe repérée |
| POST | whisper/batch-transcribe | App\Http\Controllers\WhisperController@batchTranscribe | délégation ou commande à examiner |
| POST | whisper/transcribe | App\Http\Controllers\WhisperController@transcribe | délégation ou commande à examiner |
| POST | whisper/transcribe-medical-consultation | App\Http\Controllers\WhisperController@transcribeMedicalConsultation | délégation ou commande à examiner |
| POST | whisper/transcribe-medical-dictation | App\Http\Controllers\WhisperController@transcribeMedicalDictation | délégation ou commande à examiner |

## Actions non résolues

- `POST — api/pcma/pdf — App\Http\Controllers\PCMAController@generatePdf`
- `POST — api/v1/pcmas/{pcma}/complete — App\Http\Controllers\Api\V1\PCMAController@complete`
- `POST — api/v1/pcmas/{pcma}/fail — App\Http\Controllers\Api\V1\PCMAController@fail`
- `GET|HEAD — api/club/eligible-players/{competition} — App\Http\Controllers\ClubManagementController@getEligiblePlayers`
- `GET|HEAD — api/clubs/{club}/players/daily-passport — App\Http\Controllers\PassportController@clubPassport`
- `GET|HEAD — api/federations/{federation}/daily-passport — App\Http\Controllers\PassportController@federationPassport`
- `GET|HEAD — api/players/{player}/transfers — App\Http\Controllers\PassportController@playerTransfers`
- `GET|HEAD — api/formation/barèmes — App\Http\Controllers\Controller@index`
- `POST — competitions/sync-all — App\Http\Controllers\CompetitionManagementController@syncAll`
- `POST — pcma/pdf — App\Http\Controllers\PCMAController@generatePdf`
- `GET|HEAD — player-portal/profile — App\Http\Controllers\PlayerPortalController@profile`
- `PUT — player-portal/profile — App\Http\Controllers\PlayerPortalController@updateProfile`
- `GET|HEAD — player-portal/predictions — App\Http\Controllers\PlayerPortalController@predictions`
- `GET|HEAD — player-portal/performances — App\Http\Controllers\PlayerPortalController@performances`
- `GET|HEAD — player-portal/matches — App\Http\Controllers\PlayerPortalController@matches`
- `GET|HEAD — player-portal/documents — App\Http\Controllers\PlayerPortalController@documents`
- `GET|HEAD — player-portal/settings — App\Http\Controllers\PlayerPortalController@settings`
- `GET|HEAD — player-portal/fifa-light — App\Http\Controllers\PlayerPortalController@fifaUltimateDashboard`
