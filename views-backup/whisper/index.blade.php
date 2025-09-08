@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Speech-to-Text with Whisper</h1>
            <p class="mt-2 text-gray-600">Transcribe audio files using OpenAI Whisper with medical context support</p>
        </div>

        <!-- Connection Status -->
        <div id="connectionStatus" class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-8">
            <div class="flex items-center">
                <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-blue-600 mr-3"></div>
                <span class="text-blue-700">Checking API connection...</span>
            </div>
        </div>

        <!-- Main Transcription Interface -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Upload Section -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Upload Audio File</h3>
                    
                    <!-- File Upload Area -->
                    <div id="uploadArea" class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center hover:border-blue-400 transition-colors">
                        <div class="space-y-4">
                            <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <div>
                                <p class="text-sm text-gray-600">Drag and drop audio file here, or</p>
                                <label for="audioFile" class="mt-2 cursor-pointer inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                                    Choose File
                                </label>
                                <input id="audioFile" type="file" accept="audio/*" class="hidden">
                            </div>
                            <p class="text-xs text-gray-500">Supported formats: MP3, WAV, M4A, FLAC, OGG, WEBM (max 25MB)</p>
                        </div>
                    </div>

                    <!-- File Info -->
                    <div id="fileInfo" class="hidden mt-4 p-4 bg-gray-50 rounded-lg">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="font-medium text-gray-900" id="fileName"></p>
                                <p class="text-sm text-gray-500" id="fileSize"></p>
                            </div>
                            <button id="removeFile" class="text-red-600 hover:text-red-800">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Transcription Options -->
                    <div class="mt-6 space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Language</label>
                            <select id="language" class="w-full border border-gray-300 rounded-lg px-3 py-2">
                                @foreach($supportedLanguages as $code => $name)
                                    <option value="{{ $code }}" {{ $code === 'en' ? 'selected' : '' }}>{{ $name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Transcription Type</label>
                            <select id="transcriptionType" class="w-full border border-gray-300 rounded-lg px-3 py-2">
                                <option value="general">General Transcription</option>
                                <option value="medical_consultation">Medical Consultation</option>
                                <option value="medical_dictation">Medical Dictation</option>
                            </select>
                        </div>

                        <div id="medicalOptions" class="hidden space-y-4">
                            <div id="consultationTypeDiv" class="hidden">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Consultation Type</label>
                                <select id="consultationType" class="w-full border border-gray-300 rounded-lg px-3 py-2">
                                    <option value="general">General Consultation</option>
                                    <option value="symptoms">Symptoms Discussion</option>
                                    <option value="diagnosis">Diagnosis</option>
                                    <option value="treatment">Treatment Plan</option>
                                    <option value="followup">Follow-up</option>
                                </select>
                            </div>

                            <div id="dictationTypeDiv" class="hidden">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Dictation Type</label>
                                <select id="dictationType" class="w-full border border-gray-300 rounded-lg px-3 py-2">
                                    <option value="clinical_notes">Clinical Notes</option>
                                    <option value="medical_report">Medical Report</option>
                                    <option value="prescription">Prescription</option>
                                    <option value="referral">Referral</option>
                                    <option value="discharge">Discharge Instructions</option>
                                </select>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Custom Prompt (Optional)</label>
                            <textarea id="customPrompt" rows="3" class="w-full border border-gray-300 rounded-lg px-3 py-2" placeholder="Add context or specific instructions for transcription..."></textarea>
                        </div>

                        <div class="flex items-center">
                            <input id="medicalContext" type="checkbox" class="h-4 w-4 text-blue-600 border-gray-300 rounded">
                            <label for="medicalContext" class="ml-2 text-sm text-gray-700">Medical Context</label>
                        </div>
                    </div>

                    <!-- Transcribe Button -->
                    <button id="transcribeBtn" class="w-full mt-6 bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                        Transcribe Audio
                    </button>
                </div>
            </div>

            <!-- Results Section -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Transcription Results</h3>
                    
                    <!-- Loading Indicator -->
                    <div id="loadingIndicator" class="hidden">
                        <div class="flex items-center justify-center py-8">
                            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
                            <span class="ml-3 text-gray-600">Transcribing audio...</span>
                        </div>
                    </div>

                    <!-- Results Content -->
                    <div id="resultsContent" class="space-y-4">
                        <div class="text-center text-gray-500 py-8">
                            <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <p>Upload an audio file to start transcription</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Batch Upload Section -->
        <div class="mt-8 bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Batch Transcription</h3>
                <p class="text-sm text-gray-600 mb-4">Upload multiple audio files for batch processing</p>
                
                <div class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center">
                    <input id="batchFiles" type="file" multiple accept="audio/*" class="hidden">
                    <label for="batchFiles" class="cursor-pointer inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700">
                        Choose Multiple Files
                    </label>
                    <p class="text-xs text-gray-500 mt-2">Select up to 10 audio files</p>
                </div>

                <div id="batchFileList" class="hidden mt-4 space-y-2"></div>

                <button id="batchTranscribeBtn" class="hidden mt-4 bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors">
                    Transcribe All Files
                </button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('Whisper page loaded');
    
    // Elements
    const uploadArea = document.getElementById('uploadArea');
    const audioFileInput = document.getElementById('audioFile');
    const fileInfo = document.getElementById('fileInfo');
    const fileName = document.getElementById('fileName');
    const fileSize = document.getElementById('fileSize');
    const removeFile = document.getElementById('removeFile');
    const transcribeBtn = document.getElementById('transcribeBtn');
    const loadingIndicator = document.getElementById('loadingIndicator');
    const resultsContent = document.getElementById('resultsContent');
    const connectionStatus = document.getElementById('connectionStatus');
    const transcriptionType = document.getElementById('transcriptionType');
    const medicalOptions = document.getElementById('medicalOptions');
    const consultationTypeDiv = document.getElementById('consultationTypeDiv');
    const dictationTypeDiv = document.getElementById('dictationTypeDiv');
    const batchFiles = document.getElementById('batchFiles');
    const batchFileList = document.getElementById('batchFileList');
    const batchTranscribeBtn = document.getElementById('batchTranscribeBtn');

    let selectedFile = null;
    let selectedBatchFiles = [];

    // Check API connection
    async function checkConnection() {
        try {
            const response = await fetch('/whisper/test-connection', {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });
            const data = await response.json();
            
            if (data.success) {
                connectionStatus.innerHTML = `
                    <div class="flex items-center text-green-700">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                        <span>Whisper API connected successfully</span>
                    </div>
                `;
            } else {
                connectionStatus.innerHTML = `
                    <div class="flex items-center text-red-700">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                        </svg>
                        <span>Whisper API connection failed: ${data.message}</span>
                    </div>
                `;
            }
        } catch (error) {
            connectionStatus.innerHTML = `
                <div class="flex items-center text-red-700">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                    </svg>
                    <span>Connection test failed: ${error.message}</span>
                </div>
            `;
        }
    }

    // Handle file selection
    function handleFileSelect(file) {
        selectedFile = file;
        fileName.textContent = file.name;
        fileSize.textContent = formatFileSize(file.size);
        fileInfo.classList.remove('hidden');
        transcribeBtn.disabled = false;
    }

    // Format file size
    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }

    // Handle transcription type change
    transcriptionType.addEventListener('change', function() {
        const type = this.value;
        medicalOptions.classList.toggle('hidden', type === 'general');
        consultationTypeDiv.classList.toggle('hidden', type !== 'medical_consultation');
        dictationTypeDiv.classList.toggle('hidden', type !== 'medical_dictation');
    });

    // File upload handlers
    uploadArea.addEventListener('dragover', function(e) {
        e.preventDefault();
        this.classList.add('border-blue-400');
    });

    uploadArea.addEventListener('dragleave', function(e) {
        e.preventDefault();
        this.classList.remove('border-blue-400');
    });

    uploadArea.addEventListener('drop', function(e) {
        e.preventDefault();
        this.classList.remove('border-blue-400');
        
        const files = e.dataTransfer.files;
        if (files.length > 0) {
            handleFileSelect(files[0]);
        }
    });

    audioFileInput.addEventListener('change', function(e) {
        if (e.target.files.length > 0) {
            handleFileSelect(e.target.files[0]);
        }
    });

    removeFile.addEventListener('click', function() {
        selectedFile = null;
        fileInfo.classList.add('hidden');
        transcribeBtn.disabled = true;
        audioFileInput.value = '';
    });

    // Transcribe button handler
    transcribeBtn.addEventListener('click', async function() {
        if (!selectedFile) return;

        const formData = new FormData();
        formData.append('audio_file', selectedFile);
        formData.append('language', document.getElementById('language').value);
        formData.append('prompt', document.getElementById('customPrompt').value);
        formData.append('medical_context', document.getElementById('medicalContext').checked);

        const type = transcriptionType.value;
        if (type === 'medical_consultation') {
            formData.append('consultation_type', document.getElementById('consultationType').value);
        } else if (type === 'medical_dictation') {
            formData.append('dictation_type', document.getElementById('dictationType').value);
        }

        // Show loading
        loadingIndicator.classList.remove('hidden');
        resultsContent.innerHTML = '';

        try {
            let url = '/whisper/transcribe';
            if (type === 'medical_consultation') {
                url = '/whisper/transcribe-medical-consultation';
            } else if (type === 'medical_dictation') {
                url = '/whisper/transcribe-medical-dictation';
            }

            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: formData
            });

            const data = await response.json();

            if (data.success) {
                displayResults(data.data);
            } else {
                displayError(data.message || 'Transcription failed');
            }
        } catch (error) {
            displayError('Transcription failed: ' + error.message);
        } finally {
            loadingIndicator.classList.add('hidden');
        }
    });

    // Display results
    function displayResults(result) {
        resultsContent.innerHTML = `
            <div class="space-y-4">
                <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                    <h4 class="font-medium text-green-800 mb-2">Transcription Successful</h4>
                    <div class="text-sm text-green-700">
                        <p>Language: ${result.language}</p>
                        <p>Duration: ${result.duration}s</p>
                        <p>Confidence: ${(result.confidence * 100).toFixed(1)}%</p>
                        <p>Word Count: ${result.word_count}</p>
                    </div>
                </div>

                <div>
                    <h4 class="font-medium text-gray-900 mb-2">Transcribed Text</h4>
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <p class="text-gray-700 whitespace-pre-wrap">${result.text}</p>
                    </div>
                </div>

                ${result.medical_terms && Object.keys(result.medical_terms).length > 0 ? `
                    <div>
                        <h4 class="font-medium text-gray-900 mb-2">Medical Terms Detected</h4>
                        <div class="bg-blue-50 p-4 rounded-lg">
                            ${Object.entries(result.medical_terms).map(([category, terms]) => `
                                <div class="mb-2">
                                    <span class="font-medium text-blue-800">${category}:</span>
                                    <span class="text-blue-700">${terms.join(', ')}</span>
                                </div>
                            `).join('')}
                        </div>
                    </div>
                ` : ''}

                <div class="flex space-x-2">
                    <button onclick="copyToClipboard('${result.text.replace(/'/g, "\\'")}')" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                        Copy Text
                    </button>
                    <button onclick="downloadTranscription('${result.text.replace(/'/g, "\\'")}', '${selectedFile.name}')" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors">
                        Download
                    </button>
                </div>
            </div>
        `;
    }

    // Display error
    function displayError(message) {
        resultsContent.innerHTML = `
            <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                <h4 class="font-medium text-red-800 mb-2">Transcription Failed</h4>
                <p class="text-red-700">${message}</p>
            </div>
        `;
    }

    // Batch file handling
    batchFiles.addEventListener('change', function(e) {
        selectedBatchFiles = Array.from(e.target.files);
        
        if (selectedBatchFiles.length > 0) {
            batchFileList.classList.remove('hidden');
            batchTranscribeBtn.classList.remove('hidden');
            
            batchFileList.innerHTML = selectedBatchFiles.map((file, index) => `
                <div class="flex items-center justify-between p-2 bg-gray-50 rounded">
                    <div>
                        <p class="font-medium text-gray-900">${file.name}</p>
                        <p class="text-sm text-gray-500">${formatFileSize(file.size)}</p>
                    </div>
                    <span class="text-xs text-gray-400">File ${index + 1}</span>
                </div>
            `).join('');
        } else {
            batchFileList.classList.add('hidden');
            batchTranscribeBtn.classList.add('hidden');
        }
    });

    // Batch transcribe
    batchTranscribeBtn.addEventListener('click', async function() {
        if (selectedBatchFiles.length === 0) return;

        const formData = new FormData();
        selectedBatchFiles.forEach(file => {
            formData.append('audio_files[]', file);
        });
        formData.append('language', document.getElementById('language').value);

        batchTranscribeBtn.disabled = true;
        batchTranscribeBtn.textContent = 'Processing...';

        try {
            const response = await fetch('/whisper/batch-transcribe', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: formData
            });

            const data = await response.json();

            if (data.success) {
                displayBatchResults(data.data);
            } else {
                displayError(data.message || 'Batch transcription failed');
            }
        } catch (error) {
            displayError('Batch transcription failed: ' + error.message);
        } finally {
            batchTranscribeBtn.disabled = false;
            batchTranscribeBtn.textContent = 'Transcribe All Files';
        }
    });

    // Display batch results
    function displayBatchResults(result) {
        resultsContent.innerHTML = `
            <div class="space-y-4">
                <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                    <h4 class="font-medium text-green-800 mb-2">Batch Transcription Complete</h4>
                    <div class="text-sm text-green-700">
                        <p>Total Files: ${result.total_files}</p>
                        <p>Successful: ${result.successful_transcriptions}</p>
                        <p>Failed: ${result.failed_transcriptions}</p>
                    </div>
                </div>

                <div class="space-y-2">
                    ${result.results.map((item, index) => `
                        <div class="border border-gray-200 rounded-lg p-4">
                            <h5 class="font-medium text-gray-900 mb-2">${item.filename}</h5>
                            ${item.result.success ? `
                                <p class="text-sm text-gray-600 mb-2">${item.result.text}</p>
                                <div class="text-xs text-gray-500">
                                    Confidence: ${(item.result.confidence * 100).toFixed(1)}% | 
                                    Words: ${item.result.word_count}
                                </div>
                            ` : `
                                <p class="text-red-600 text-sm">Failed: ${item.result.error}</p>
                            `}
                        </div>
                    `).join('')}
                </div>
            </div>
        `;
    }

    // Utility functions
    window.copyToClipboard = function(text) {
        navigator.clipboard.writeText(text).then(() => {
            alert('Text copied to clipboard!');
        });
    };

    window.downloadTranscription = function(text, filename) {
        const blob = new Blob([text], { type: 'text/plain' });
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = filename.replace(/\.[^/.]+$/, '') + '_transcription.txt';
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        URL.revokeObjectURL(url);
    };

    // Initialize
    checkConnection();
    transcribeBtn.disabled = true;
});
</script>
@endsection 