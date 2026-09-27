# -*- coding: utf-8 -*-
import sys
path = 'resources/views/pcma/create.blade.php'
content = open(path, encoding='utf-8').read()

anchor = "console.log(' SCRIPT TAG STARTING...');\n"
if content.count(anchor) != 1:
    print("MISMATCH anchor", content.count(anchor))
    sys.exit(1)

labels_block = anchor + """
// PCMA_LABELS: centralized translated strings for this page's script (see resources/lang/{en,fr}/pcma.php)
const PCMA_LABELS = {
    errGeneric: @json(__('pcma.err_generic_prefix')),
    errHttp: @json(__('pcma.err_http_prefix')),
    errApi: @json(__('pcma.err_api_prefix')),
    errServer: @json(__('pcma.err_server_prefix')),
    errSave: @json(__('pcma.err_save_prefix')),
    errSaveAlert: @json(__('pcma.err_save_alert_prefix')),
    loadingApiKey: @json(__('pcma.loading_api_key')),
    apiKeyLoadedAuto: @json(__('pcma.api_key_loaded_auto')),
    errInvalidResponseFormat: @json(__('pcma.err_invalid_response_format')),
    errLoadingKey: @json(__('pcma.err_loading_key')),
    errMissingApiKey: @json(__('pcma.err_missing_api_key')),
    serviceInitializedSuccess: @json(__('pcma.service_initialized_success')),
    serviceReadyClick: @json(__('pcma.service_ready_click')),
    errInitFailed: @json(__('pcma.err_init_failed')),
    errInitError: @json(__('pcma.err_init_error')),
    testSuccessOperational: @json(__('pcma.test_success_operational')),
    errTestFailed: @json(__('pcma.err_test_failed')),
    errTestFailedAlert: @json(__('pcma.err_test_failed_alert')),
    errStartRecognitionFailed: @json(__('pcma.err_start_recognition_failed')),
    recognitionStopped: @json(__('pcma.recognition_stopped')),
    commandAnalyzedFormsFilled: @json(__('pcma.command_analyzed_forms_filled')),
    dataAppliedToForm: @json(__('pcma.data_applied_to_form')),
    errPlayerNotFoundDb: @json(__('pcma.err_player_not_found_db')),
    playerFoundDataFilled: @json(__('pcma.player_found_data_filled')),
    errAutoSearch: @json(__('pcma.err_auto_search')),
    fieldAge: @json(__('pcma.field_age')),
    vocalPrefix: @json(__('pcma.vocal_prefix')),
    databasePrefix: @json(__('pcma.database_prefix')),
    inconsistencyBadge: @json(__('pcma.inconsistency_badge')),
    errFillConfirmationField: @json(__('pcma.err_fill_confirmation_field')),
    identityConfirmedValidated: @json(__('pcma.identity_confirmed_validated')),
    voiceExtractionNoteHeader: @json(__('pcma.voice_extraction_note_header')),
    ageNotePrefix: @json(__('pcma.age_note_prefix')),
    ageNoteSuffix: @json(__('pcma.age_note_suffix')),
    commandNotePrefix: @json(__('pcma.command_note_prefix')),
    savedAutomaticallySuccess: @json(__('pcma.saved_automatically_success')),
    voiceExtractionSummaryTitle: @json(__('pcma.voice_extraction_summary_title')),
    summaryNameLabel: @json(__('pcma.summary_name_label')),
    summaryAgeLabel: @json(__('pcma.summary_age_label')),
    confidenceLabel: @json(__('pcma.confidence_label')),
    confidenceHigh: @json(__('pcma.confidence_high')),
    confidenceMedium: @json(__('pcma.confidence_medium')),
    confidenceLow: @json(__('pcma.confidence_low')),
    pcmaSavedWithId: @json(__('pcma.pcma_saved_with_id')),
    searchingInProgress: @json(__('pcma.searching_in_progress')),
    foundInDatabase: @json(__('pcma.found_in_database')),
    playerNotFoundTitle: @json(__('pcma.player_not_found_title')),
    searchedNameLabel: @json(__('pcma.searched_name_label')),
    statusColonLabel: @json(__('pcma.status_colon_label')),
    playerNotExistYet: @json(__('pcma.player_not_exist_yet')),
    newPlayerBadge: @json(__('pcma.new_player_badge')),
    transcriptionReceived: @json(__('pcma.transcription_received')),
    serviceGoogleNotInitialized: @json(__('pcma.service_google_not_initialized')),
    errGooglePrefix: @json(__('pcma.err_google_prefix')),
    recordingStoppedGoogle: @json(__('pcma.recording_stopped_google')),
    errStopPrefix: @json(__('pcma.err_stop_prefix')),
    playerDataFilledAuto: @json(__('pcma.player_data_filled_auto')),
    dataExtractedSuccess: @json(__('pcma.data_extracted_success')),
    dataProcessedServiceVocalNlp: @json(__('pcma.data_processed_servicevocal_nlp')),
    recognitionStoppedGeneric: @json(__('pcma.recognition_stopped_generic')),
    dataProcessedDirectIntegration: @json(__('pcma.data_processed_direct_integration')),
    fifaConnectCommandDetected: @json(__('pcma.fifa_connect_command_detected')),
    numberLabelPrefix: @json(__('pcma.number_label_prefix')),
    noStructuredDataDetected: @json(__('pcma.no_structured_data_detected')),
    noFinalTranscript: @json(__('pcma.no_final_transcript')),
    noDataExtracted: @json(__('pcma.no_data_extracted')),
    testAdvancedProgress: @json(__('pcma.test_advanced_progress')),
    speechRecognitionUnsupported: @json(__('pcma.speech_recognition_unsupported')),
    testRecognitionStarted: @json(__('pcma.test_recognition_started')),
    testErrorPrefix: @json(__('pcma.test_error_prefix')),
    testCreationErrorPrefix: @json(__('pcma.test_creation_error_prefix')),
    speechRecognitionUnsupportedBrowser: @json(__('pcma.speech_recognition_unsupported_browser')),
    micPermissionDenied: @json(__('pcma.mic_permission_denied')),
    noMicrophoneDetected: @json(__('pcma.no_microphone_detected')),
    micErrorPrefix: @json(__('pcma.mic_error_prefix')),
    recognitionStoppedPlain: @json(__('pcma.recognition_stopped_plain')),
    listeningPrefix: @json(__('pcma.listening_prefix')),
    listeningSuffix: @json(__('pcma.listening_suffix')),
    googleRecordingInProgress: @json(__('pcma.google_recording_in_progress')),
    errApiKeyNotFound: @json(__('pcma.err_api_key_not_found')),
    modeActiveWord: @json(__('pcma.mode_active_word')),
    transferNotImplemented: @json(__('pcma.transfer_not_implemented')),
    transferSuccessTemplate: @json(__('pcma.transfer_success_template')),
    transferErrorTemplate: @json(__('pcma.transfer_error_template')),
};

"""

content = content.replace(anchor, labels_block, 1)
open(path, 'w', encoding='utf-8').write(content)
print("BATCH19 OK - PCMA_LABELS inserted")
