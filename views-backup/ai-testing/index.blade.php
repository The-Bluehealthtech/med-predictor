@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">AI Testing Dashboard</h1>
            <p class="mt-2 text-gray-600">Test and compare different AI providers for medical analysis</p>
        </div>

        <!-- Provider Status -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-8">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">AI Provider Status</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($providers as $provider)
                    <div class="border border-gray-200 rounded-lg p-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <h4 class="font-medium text-gray-900">{{ $provider['name'] }}</h4>
                                <p class="text-sm text-gray-500">{{ $provider['api_key_env'] }}</p>
                            </div>
                            <div class="flex items-center">
                                @if($provider['configured'])
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        Configured
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        Not Configured
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Test Controls -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-8">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Run Tests</h3>
                <div class="space-y-4">
                    <div class="flex flex-wrap gap-4">
                        <button id="runAllTests" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                            Run All Tests
                        </button>
                        <button id="testMedicalDiagnosis" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors">
                            Test Medical Diagnosis
                        </button>
                        <button id="testPerformanceAnalysis" class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition-colors">
                            Test Performance Analysis
                        </button>
                        <button id="testInjuryPrediction" class="bg-orange-600 text-white px-4 py-2 rounded-lg hover:bg-orange-700 transition-colors">
                            Test Injury Prediction
                        </button>
                        <button id="getSummary" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition-colors">
                            Get Summary
                        </button>
                    </div>
                    
                    <!-- Custom Test -->
                    <div class="border-t pt-4">
                        <h4 class="font-medium text-gray-900 mb-2">Custom Test</h4>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <select id="customProvider" class="border border-gray-300 rounded-lg px-3 py-2">
                                @foreach($providers as $provider)
                                    <option value="{{ $provider['key'] }}">{{ $provider['name'] }}</option>
                                @endforeach
                            </select>
                            <input type="text" id="customPrompt" placeholder="Enter your test prompt..." class="border border-gray-300 rounded-lg px-3 py-2 flex-1">
                            <button id="runCustomTest" class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition-colors">
                                Run Test
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Whisper Speech Section -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-8">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">🎤 Whisper Speech - Audio Transcription</h3>
                <p class="text-sm text-gray-600 mb-4">Transcription audio avec OpenAI Whisper pour contexte médical</p>
                
                <!-- Arabic Model Info -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h4 class="text-sm font-medium text-blue-800">Arabic Models Available</h4>
                            <p class="text-sm text-blue-700 mt-1">
                                Whisper Large V2 Arabic 5k Steps model for enhanced Arabic and Tunisian dialect transcription accuracy (WER: 0.4239)
                            </p>
                        </div>
                    </div>
                </div>
                
                <div class="space-y-4">
                    <!-- Audio Upload -->
                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center" id="audioDropZone">
                        <div class="space-y-2">
                            <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <div class="text-gray-600">
                                <label for="audioFile" class="cursor-pointer">
                                    <span class="font-medium text-indigo-600 hover:text-indigo-500">Upload audio file</span>
                                    or drag and drop
                                </label>
                                <input id="audioFile" name="audioFile" type="file" class="sr-only" accept="audio/*">
                            </div>
                            <p class="text-xs text-gray-500">MP3, WAV, M4A up to 25MB</p>
                        </div>
                    </div>

                    <!-- Transcription Options -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <select id="whisperLanguage" class="border border-gray-300 rounded-lg px-3 py-2">
                            <option value="en">English</option>
                            <option value="fr">French</option>
                            <option value="es">Spanish</option>
                            <option value="de">German</option>
                            <option value="ar" class="font-semibold text-blue-600">Arabic (Enhanced Model)</option>
                            <option value="ar-tn" class="font-semibold text-blue-600">Tunisian Arabic (Enhanced Model)</option>
                            <option value="it">Italian</option>
                            <option value="pt">Portuguese</option>
                            <option value="zh">Chinese</option>
                            <option value="ja">Japanese</option>
                            <option value="ko">Korean</option>
                        </select>
                        <select id="whisperContext" class="border border-gray-300 rounded-lg px-3 py-2">
                            <option value="general">General Transcription</option>
                            <option value="medical_consultation">Medical Consultation</option>
                            <option value="medical_dictation">Medical Dictation</option>
                        </select>
                        <button id="transcribeAudio" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors">
                            🎤 Transcribe Audio
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Google Gemini AI Section -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-8">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">🤖 Google Gemini AI - Medical Analysis</h3>
                <p class="text-sm text-gray-600 mb-4">Analyse médicale avancée avec Google Gemini AI</p>
                
                <div class="space-y-4">
                    <!-- Analysis Type Selection -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <button id="geminiDiagnosis" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                            🩺 Medical Diagnosis
                        </button>
                        <button id="geminiTreatment" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors">
                            💊 Treatment Plan
                        </button>
                        <button id="geminiPerformance" class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition-colors">
                            📊 Performance Analysis
                        </button>
                        <button id="geminiInjury" class="bg-orange-600 text-white px-4 py-2 rounded-lg hover:bg-orange-700 transition-colors">
                            ⚠️ Injury Prediction
                        </button>
                        <button id="geminiRehab" class="bg-teal-600 text-white px-4 py-2 rounded-lg hover:bg-teal-700 transition-colors">
                            🏃 Rehabilitation Plan
                        </button>
                        <button id="geminiDocumentation" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition-colors">
                            📝 Medical Documentation
                        </button>
                    </div>

                    <!-- Custom Gemini Analysis -->
                    <div class="border-t pt-4">
                        <h4 class="font-medium text-gray-900 mb-2">Custom Medical Analysis</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <textarea id="geminiPrompt" placeholder="Describe the medical case or symptoms for analysis..." class="border border-gray-300 rounded-lg px-3 py-2 h-24 resize-none"></textarea>
                            <div class="flex flex-col gap-2">
                                <button id="runGeminiAnalysis" class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition-colors">
                                    🤖 Run Gemini Analysis
                                </button>
                                <button id="testGeminiConnection" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition-colors">
                                    🔗 Test Connection
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Loading Indicator -->
        <div id="loadingIndicator" class="hidden bg-white overflow-hidden shadow-sm sm:rounded-lg mb-8">
            <div class="p-6">
                <div class="flex items-center justify-center">
                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
                    <span class="ml-3 text-gray-600">Running AI tests...</span>
                </div>
            </div>
        </div>

        <!-- Error Display -->
        <div id="errorDisplay" class="hidden bg-red-50 border border-red-200 rounded-lg p-4 mb-8">
            <h3 class="text-lg font-semibold text-red-800 mb-2">Error</h3>
            <div id="errorContent" class="text-red-700"></div>
        </div>

        <!-- Results -->
        <div id="resultsContainer" class="space-y-8">
            <!-- Summary Results -->
            <div id="summaryResults" class="hidden bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Test Summary</h3>
                    <div id="summaryContent"></div>
                </div>
            </div>

            <!-- Detailed Results -->
            <div id="detailedResults" class="hidden bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Detailed Results</h3>
                    <div id="detailedContent"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('AI Testing page loaded');
    
    const loadingIndicator = document.getElementById('loadingIndicator');
    const resultsContainer = document.getElementById('resultsContainer');
    const summaryResults = document.getElementById('summaryResults');
    const detailedResults = document.getElementById('detailedResults');
    const summaryContent = document.getElementById('summaryContent');
    const detailedContent = document.getElementById('detailedContent');
    const errorDisplay = document.getElementById('errorDisplay');
    const errorContent = document.getElementById('errorContent');

    function showLoading() {
        console.log('Showing loading indicator');
        loadingIndicator.classList.remove('hidden');
        resultsContainer.classList.add('hidden');
        errorDisplay.classList.add('hidden');
    }

    function hideLoading() {
        console.log('Hiding loading indicator');
        loadingIndicator.classList.add('hidden');
        resultsContainer.classList.remove('hidden');
    }

    function showError(message) {
        console.error('Showing error:', message);
        errorDisplay.classList.remove('hidden');
        errorContent.textContent = message;
        hideLoading();
    }

    function displayResults(data, title = 'Test Results') {
        console.log('Displaying results:', data);
        hideLoading();
        
        if (data.summary) {
            summaryResults.classList.remove('hidden');
            summaryContent.innerHTML = `
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
                    ${Object.entries(data.summary).map(([key, summary]) => `
                        <div class="border border-gray-200 rounded-lg p-4">
                            <h4 class="font-medium text-gray-900 mb-2">${summary.name}</h4>
                            <div class="space-y-2 text-sm">
                                <div class="flex justify-between">
                                    <span>Success Rate:</span>
                                    <span class="font-medium ${summary.success_rate > 80 ? 'text-green-600' : summary.success_rate > 60 ? 'text-yellow-600' : 'text-red-600'}">${summary.success_rate}%</span>
                                </div>
                                <div class="flex justify-between">
                                    <span>Avg Response Time:</span>
                                    <span class="font-medium">${summary.average_response_time}ms</span>
                                </div>
                                <div class="flex justify-between">
                                    <span>Tests:</span>
                                    <span class="font-medium">${summary.successful_tests}/${summary.total_tests}</span>
                                </div>
                            </div>
                        </div>
                    `).join('')}
                </div>
                ${data.recommendations ? `
                    <div class="border-t pt-4">
                        <h4 class="font-medium text-gray-900 mb-2">Recommendations</h4>
                        <ul class="list-disc list-inside space-y-1 text-sm text-gray-600">
                            ${data.recommendations.map(rec => `<li>${rec}</li>`).join('')}
                        </ul>
                    </div>
                ` : ''}
            `;
        }

        if (data.results || data.data) {
            detailedResults.classList.remove('hidden');
            const results = data.results || data.data;
            
            detailedContent.innerHTML = `
                <div class="space-y-6">
                    ${Object.entries(results).map(([providerKey, providerResults]) => `
                        <div class="border border-gray-200 rounded-lg p-4">
                            <h4 class="font-medium text-gray-900 mb-3">${providerResults.provider || providerKey}</h4>
                            ${Object.entries(providerResults).filter(([key, result]) => key !== 'provider').map(([testKey, result]) => `
                                <div class="mb-4 p-3 bg-gray-50 rounded">
                                    <h5 class="font-medium text-gray-700 mb-2">${testKey.replace(/_/g, ' ').toUpperCase()}</h5>
                                    ${result.success ? `
                                        <div class="text-sm text-gray-600 mb-2">
                                            <div class="flex justify-between mb-1">
                                                <span>Response Time:</span>
                                                <span>${result.response_time_ms}ms</span>
                                            </div>
                                            ${result.tokens_used ? `
                                                <div class="flex justify-between mb-1">
                                                    <span>Tokens Used:</span>
                                                    <span>${result.tokens_used}</span>
                                                </div>
                                            ` : ''}
                                        </div>
                                        <div class="bg-white p-3 rounded border text-sm">
                                            <pre class="whitespace-pre-wrap">${result.response}</pre>
                                        </div>
                                    ` : `
                                        <div class="text-red-600 text-sm">
                                            Error: ${result.error}
                                        </div>
                                    `}
                                </div>
                            `).join('')}
                        </div>
                    `).join('')}
                </div>
            `;
        }
    }

    async function makeRequest(url, method = 'POST', body = null) {
        console.log(`Making ${method} request to ${url}`);
        
        const options = {
            method: method,
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        };
        
        if (body) {
            options.body = JSON.stringify(body);
        }
        
        try {
            const response = await fetch(url, options);
            console.log('Response status:', response.status);
            
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }
            
            const data = await response.json();
            console.log('Response data:', data);
            return data;
        } catch (error) {
            console.error('Request failed:', error);
            throw error;
        }
    }

    // Event Listeners
    document.getElementById('runAllTests').addEventListener('click', async function() {
        console.log('Run All Tests clicked');
        showLoading();
        try {
            const data = await makeRequest('/ai-testing/run-tests');
            if (data.success) {
                displayResults(data.data);
            } else {
                showError('Error: ' + data.message);
            }
        } catch (error) {
            showError('Error running tests: ' + error.message);
        }
    });

    document.getElementById('testMedicalDiagnosis').addEventListener('click', async function() {
        console.log('Test Medical Diagnosis clicked');
        showLoading();
        try {
            const data = await makeRequest('/ai-testing/medical-diagnosis');
            if (data.success) {
                displayResults(data.data, 'Medical Diagnosis Test');
            } else {
                showError('Error: ' + data.message);
            }
        } catch (error) {
            showError('Error running medical diagnosis test: ' + error.message);
        }
    });

    document.getElementById('testPerformanceAnalysis').addEventListener('click', async function() {
        console.log('Test Performance Analysis clicked');
        showLoading();
        try {
            const data = await makeRequest('/ai-testing/performance-analysis');
            if (data.success) {
                displayResults(data.data, 'Performance Analysis Test');
            } else {
                showError('Error: ' + data.message);
            }
        } catch (error) {
            showError('Error running performance analysis test: ' + error.message);
        }
    });

    document.getElementById('testInjuryPrediction').addEventListener('click', async function() {
        console.log('Test Injury Prediction clicked');
        showLoading();
        try {
            const data = await makeRequest('/ai-testing/injury-prediction');
            if (data.success) {
                displayResults(data.data, 'Injury Prediction Test');
            } else {
                showError('Error: ' + data.message);
            }
        } catch (error) {
            showError('Error running injury prediction test: ' + error.message);
        }
    });

    document.getElementById('getSummary').addEventListener('click', async function() {
        console.log('Get Summary clicked');
        showLoading();
        try {
            const data = await makeRequest('/ai-testing/summary', 'GET');
            if (data.success) {
                displayResults(data.data, 'Test Summary');
            } else {
                showError('Error: ' + data.message);
            }
        } catch (error) {
            showError('Error getting summary: ' + error.message);
        }
    });

    document.getElementById('runCustomTest').addEventListener('click', async function() {
        const provider = document.getElementById('customProvider').value;
        const prompt = document.getElementById('customPrompt').value;
        
        if (!prompt.trim()) {
            showError('Please enter a test prompt');
            return;
        }
        
        console.log('Run Custom Test clicked', { provider, prompt });
        showLoading();
        try {
            const data = await makeRequest('/ai-testing/test-provider', 'POST', { provider, prompt });
            if (data.success) {
                displayResults(data.data, 'Custom Test Results');
            } else {
                showError('Error: ' + data.message);
            }
        } catch (error) {
            showError('Error running custom test: ' + error.message);
        }
    });

    // Whisper Speech Event Listeners
    
    // Show Arabic model info when Arabic is selected
    document.getElementById('whisperLanguage').addEventListener('change', function() {
        const language = this.value;
        const arabicInfo = document.querySelector('.bg-blue-50');
        
        if (language === 'ar' || language === 'ar-tn') {
            if (arabicInfo) {
                arabicInfo.classList.remove('hidden');
            }
        } else {
            if (arabicInfo) {
                arabicInfo.classList.add('hidden');
            }
        }
    });
    
    document.getElementById('transcribeAudio').addEventListener('click', async function() {
        const audioFile = document.getElementById('audioFile').files[0];
        const language = document.getElementById('whisperLanguage').value;
        const context = document.getElementById('whisperContext').value;
        
        if (!audioFile) {
            showError('Please select an audio file');
            return;
        }
        
        console.log('Transcribe Audio clicked', { language, context });
        showLoading();
        
        const formData = new FormData();
        formData.append('audioFile', audioFile);
        formData.append('language', language);
        formData.append('context', context);
        
        try {
            const response = await fetch('/whisper/transcribe', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: formData
            });
            
            const data = await response.json();
            if (data.success) {
                displayResults({ whisper: data.data }, 'Whisper Transcription Results');
            } else {
                showError('Error: ' + data.message);
            }
        } catch (error) {
            showError('Error transcribing audio: ' + error.message);
        }
    });

    // Google Gemini AI Event Listeners
    document.getElementById('geminiDiagnosis').addEventListener('click', async function() {
        console.log('Gemini Diagnosis clicked');
        showLoading();
        try {
            const data = await makeRequest('/gemini/generate-diagnosis', 'POST', {
                symptoms: 'Patient reports fatigue, muscle weakness, and joint pain. Blood pressure is elevated at 140/90.',
                patient_age: 35,
                medical_history: 'No significant medical history'
            });
            if (data.success) {
                displayResults({ gemini: data.data }, 'Gemini Medical Diagnosis');
            } else {
                showError('Error: ' + data.message);
            }
        } catch (error) {
            showError('Error running Gemini diagnosis: ' + error.message);
        }
    });

    document.getElementById('geminiTreatment').addEventListener('click', async function() {
        console.log('Gemini Treatment clicked');
        showLoading();
        try {
            const data = await makeRequest('/gemini/generate-treatment', 'POST', {
                diagnosis: 'Grade 2 hamstring strain',
                patient_age: 24,
                sport: 'Soccer'
            });
            if (data.success) {
                displayResults({ gemini: data.data }, 'Gemini Treatment Plan');
            } else {
                showError('Error: ' + data.message);
            }
        } catch (error) {
            showError('Error running Gemini treatment: ' + error.message);
        }
    });

    document.getElementById('geminiPerformance').addEventListener('click', async function() {
        console.log('Gemini Performance clicked');
        showLoading();
        try {
            const data = await makeRequest('/gemini/analyze-performance', 'POST', {
                vo2_max: 65,
                heart_rate_recovery: 25,
                sprint_speed: 10.2,
                age: 24,
                sport: 'Soccer'
            });
            if (data.success) {
                displayResults({ gemini: data.data }, 'Gemini Performance Analysis');
            } else {
                showError('Error: ' + data.message);
            }
        } catch (error) {
            showError('Error running Gemini performance analysis: ' + error.message);
        }
    });

    document.getElementById('geminiInjury').addEventListener('click', async function() {
        console.log('Gemini Injury clicked');
        showLoading();
        try {
            const data = await makeRequest('/gemini/predict-injury-risk', 'POST', {
                age: 24,
                previous_injuries: ['ACL injury'],
                training_load: 85,
                fatigue_score: 7
            });
            if (data.success) {
                displayResults({ gemini: data.data }, 'Gemini Injury Prediction');
            } else {
                showError('Error: ' + data.message);
            }
        } catch (error) {
            showError('Error running Gemini injury prediction: ' + error.message);
        }
    });

    document.getElementById('geminiRehab').addEventListener('click', async function() {
        console.log('Gemini Rehab clicked');
        showLoading();
        try {
            const data = await makeRequest('/gemini/generate-rehab-plan', 'POST', {
                injury: 'Grade 2 hamstring strain',
                patient_age: 24,
                sport: 'Soccer',
                injury_date: '2024-01-15'
            });
            if (data.success) {
                displayResults({ gemini: data.data }, 'Gemini Rehabilitation Plan');
            } else {
                showError('Error: ' + data.message);
            }
        } catch (error) {
            showError('Error running Gemini rehab plan: ' + error.message);
        }
    });

    document.getElementById('geminiDocumentation').addEventListener('click', async function() {
        console.log('Gemini Documentation clicked');
        showLoading();
        try {
            const data = await makeRequest('/gemini/analyze-medical-image', 'POST', {
                patient_info: '22-year-old male athlete',
                symptoms: 'Right knee pain after training, no swelling, pain 4/10',
                examination: 'Range of motion slightly limited'
            });
            if (data.success) {
                displayResults({ gemini: data.data }, 'Gemini Medical Documentation');
            } else {
                showError('Error: ' + data.message);
            }
        } catch (error) {
            showError('Error running Gemini documentation: ' + error.message);
        }
    });

    document.getElementById('runGeminiAnalysis').addEventListener('click', async function() {
        const prompt = document.getElementById('geminiPrompt').value;
        
        if (!prompt.trim()) {
            showError('Please enter a medical case description');
            return;
        }
        
        console.log('Run Gemini Analysis clicked', { prompt });
        showLoading();
        try {
            const data = await makeRequest('/gemini/generate-diagnosis', 'POST', {
                symptoms: prompt,
                patient_age: 30,
                medical_history: 'General medical case'
            });
            if (data.success) {
                displayResults({ gemini: data.data }, 'Custom Gemini Analysis');
            } else {
                showError('Error: ' + data.message);
            }
        } catch (error) {
            showError('Error running custom Gemini analysis: ' + error.message);
        }
    });

    document.getElementById('testGeminiConnection').addEventListener('click', async function() {
        console.log('Test Gemini Connection clicked');
        showLoading();
        try {
            const data = await makeRequest('/gemini/test-connection', 'GET');
            if (data.success) {
                displayResults({ gemini: data.data }, 'Gemini Connection Test');
            } else {
                showError('Error: ' + data.message);
            }
        } catch (error) {
            showError('Error testing Gemini connection: ' + error.message);
        }
    });

    // Audio file drag and drop functionality
    const audioDropZone = document.getElementById('audioDropZone');
    const audioFileInput = document.getElementById('audioFile');

    audioDropZone.addEventListener('dragover', function(e) {
        e.preventDefault();
        audioDropZone.classList.add('border-indigo-500', 'bg-indigo-50');
    });

    audioDropZone.addEventListener('dragleave', function(e) {
        e.preventDefault();
        audioDropZone.classList.remove('border-indigo-500', 'bg-indigo-50');
    });

    audioDropZone.addEventListener('drop', function(e) {
        e.preventDefault();
        audioDropZone.classList.remove('border-indigo-500', 'bg-indigo-50');
        
        const files = e.dataTransfer.files;
        if (files.length > 0 && files[0].type.startsWith('audio/')) {
            audioFileInput.files = files;
            console.log('Audio file dropped:', files[0].name);
        }
    });

    audioFileInput.addEventListener('change', function(e) {
        if (e.target.files.length > 0) {
            console.log('Audio file selected:', e.target.files[0].name);
        }
    });

    console.log('Event listeners attached');
});
</script>
@endsection 