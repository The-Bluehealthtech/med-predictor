@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Google Gemini AI Medical Assistant</h1>
            <p class="mt-2 text-gray-600">Advanced AI-powered medical analysis and recommendations</p>
        </div>

        <!-- Connection Status -->
        <div id="connectionStatus" class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-8">
            <div class="flex items-center">
                <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-blue-600 mr-3"></div>
                <span class="text-blue-700">Testing Gemini API connection...</span>
            </div>
        </div>

        <!-- Configuration Info -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-8">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Service Configuration</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <div class="border border-gray-200 rounded-lg p-4">
                        <h4 class="font-medium text-gray-900 mb-2">API Status</h4>
                        <p class="text-sm text-gray-600" id="apiStatus">
                            @if($configuration['api_key_configured'])
                                <span class="text-green-600">✓ Configured</span>
                            @else
                                <span class="text-red-600">✗ Not Configured</span>
                            @endif
                        </p>
                    </div>
                    <div class="border border-gray-200 rounded-lg p-4">
                        <h4 class="font-medium text-gray-900 mb-2">Model</h4>
                        <p class="text-sm text-gray-600">{{ $configuration['model'] ?? 'gemini-pro' }}</p>
                    </div>
                    <div class="border border-gray-200 rounded-lg p-4">
                        <h4 class="font-medium text-gray-900 mb-2">Timeout</h4>
                        <p class="text-sm text-gray-600">{{ $configuration['timeout'] ?? 60 }}s</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Interface -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Input Section -->
            <div class="space-y-6">
                <!-- Analysis Type Selection -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Select Analysis Type</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <button onclick="selectAnalysisType('diagnosis')" class="analysis-type-btn p-4 border-2 border-gray-200 rounded-lg hover:border-blue-400 transition-colors text-left">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center text-blue-600 mr-3">
                                        🏥
                                    </div>
                                    <div>
                                        <h4 class="font-medium text-gray-900">Medical Diagnosis</h4>
                                        <p class="text-sm text-gray-600">Analyze symptoms and suggest diagnoses</p>
                                    </div>
                                </div>
                            </button>

                            <button onclick="selectAnalysisType('treatment')" class="analysis-type-btn p-4 border-2 border-gray-200 rounded-lg hover:border-blue-400 transition-colors text-left">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center text-green-600 mr-3">
                                        💊
                                    </div>
                                    <div>
                                        <h4 class="font-medium text-gray-900">Treatment Plan</h4>
                                        <p class="text-sm text-gray-600">Generate treatment recommendations</p>
                                    </div>
                                </div>
                            </button>

                            <button onclick="selectAnalysisType('performance')" class="analysis-type-btn p-4 border-2 border-gray-200 rounded-lg hover:border-blue-400 transition-colors text-left">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center text-purple-600 mr-3">
                                        📊
                                    </div>
                                    <div>
                                        <h4 class="font-medium text-gray-900">Performance Analysis</h4>
                                        <p class="text-sm text-gray-600">Analyze athletic performance data</p>
                                    </div>
                                </div>
                            </button>

                            <button onclick="selectAnalysisType('injury_prediction')" class="analysis-type-btn p-4 border-2 border-gray-200 rounded-lg hover:border-blue-400 transition-colors text-left">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center text-orange-600 mr-3">
                                        ⚠️
                                    </div>
                                    <div>
                                        <h4 class="font-medium text-gray-900">Injury Prediction</h4>
                                        <p class="text-sm text-gray-600">Assess injury risk factors</p>
                                    </div>
                                </div>
                            </button>

                            <button onclick="selectAnalysisType('rehabilitation')" class="analysis-type-btn p-4 border-2 border-gray-200 rounded-lg hover:border-blue-400 transition-colors text-left">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center text-indigo-600 mr-3">
                                        🏃‍♂️
                                    </div>
                                    <div>
                                        <h4 class="font-medium text-gray-900">Rehabilitation Plan</h4>
                                        <p class="text-sm text-gray-600">Create recovery protocols</p>
                                    </div>
                                </div>
                            </button>

                            <button onclick="selectAnalysisType('image_analysis')" class="analysis-type-btn p-4 border-2 border-gray-200 rounded-lg hover:border-blue-400 transition-colors text-left">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-teal-100 rounded-lg flex items-center justify-center text-teal-600 mr-3">
                                        🔬
                                    </div>
                                    <div>
                                        <h4 class="font-medium text-gray-900">Image Analysis</h4>
                                        <p class="text-sm text-gray-600">Analyze medical image descriptions</p>
                                    </div>
                                </div>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Input Forms -->
                <div id="inputForms" class="space-y-6">
                    <!-- Diagnosis Form -->
                    <div id="diagnosisForm" class="analysis-form hidden bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Medical Diagnosis Analysis</h3>
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Symptoms</label>
                                    <div id="symptomsContainer" class="space-y-2">
                                        <div class="flex">
                                            <input type="text" class="symptom-input flex-1 border border-gray-300 rounded-lg px-3 py-2" placeholder="Enter symptom">
                                            <button onclick="addSymptom()" class="ml-2 bg-blue-600 text-white px-3 py-2 rounded-lg hover:bg-blue-700">Add</button>
                                        </div>
                                    </div>
                                    <div id="symptomsList" class="mt-2 space-y-1"></div>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Patient Data (Optional)</label>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <input type="number" id="patientAge" placeholder="Age" class="border border-gray-300 rounded-lg px-3 py-2">
                                        <select id="patientGender" class="border border-gray-300 rounded-lg px-3 py-2">
                                            <option value="">Select Gender</option>
                                            <option value="male">Male</option>
                                            <option value="female">Female</option>
                                            <option value="other">Other</option>
                                        </select>
                                    </div>
                                </div>

                                <button onclick="generateDiagnosis()" class="w-full bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                                    Generate Diagnosis
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Treatment Form -->
                    <div id="treatmentForm" class="analysis-form hidden bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Treatment Recommendations</h3>
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Diagnosis</label>
                                    <textarea id="diagnosisInput" rows="3" class="w-full border border-gray-300 rounded-lg px-3 py-2" placeholder="Enter the diagnosis..."></textarea>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Patient Data (Optional)</label>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <input type="number" id="treatmentAge" placeholder="Age" class="border border-gray-300 rounded-lg px-3 py-2">
                                        <select id="treatmentGender" class="border border-gray-300 rounded-lg px-3 py-2">
                                            <option value="">Select Gender</option>
                                            <option value="male">Male</option>
                                            <option value="female">Female</option>
                                            <option value="other">Other</option>
                                        </select>
                                    </div>
                                </div>

                                <button onclick="generateTreatment()" class="w-full bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors">
                                    Generate Treatment Plan
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Performance Form -->
                    <div id="performanceForm" class="analysis-form hidden bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Performance Analysis</h3>
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Performance Data (JSON)</label>
                                    <textarea id="performanceData" rows="6" class="w-full border border-gray-300 rounded-lg px-3 py-2 font-mono text-sm" placeholder='{"metrics": {"speed": 10.5, "endurance": 85}, "history": [...], "goals": [...]}'></textarea>
                                </div>

                                <button onclick="analyzePerformance()" class="w-full bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition-colors">
                                    Analyze Performance
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Injury Prediction Form -->
                    <div id="injuryPredictionForm" class="analysis-form hidden bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Injury Risk Assessment</h3>
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Player Data (JSON)</label>
                                    <textarea id="playerData" rows="6" class="w-full border border-gray-300 rounded-lg px-3 py-2 font-mono text-sm" placeholder='{"age": 25, "position": "forward", "injury_history": [...], "performance_metrics": {...}}'></textarea>
                                </div>

                                <button onclick="predictInjuryRisk()" class="w-full bg-orange-600 text-white px-4 py-2 rounded-lg hover:bg-orange-700 transition-colors">
                                    Assess Injury Risk
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Rehabilitation Form -->
                    <div id="rehabilitationForm" class="analysis-form hidden bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Rehabilitation Plan</h3>
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Injury Description</label>
                                    <textarea id="injuryDescription" rows="3" class="w-full border border-gray-300 rounded-lg px-3 py-2" placeholder="Describe the injury..."></textarea>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Patient Data (Optional)</label>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <select id="injurySeverity" class="border border-gray-300 rounded-lg px-3 py-2">
                                            <option value="">Select Severity</option>
                                            <option value="mild">Mild</option>
                                            <option value="moderate">Moderate</option>
                                            <option value="severe">Severe</option>
                                        </select>
                                        <input type="text" id="injuryDuration" placeholder="Duration (e.g., 2 weeks)" class="border border-gray-300 rounded-lg px-3 py-2">
                                    </div>
                                </div>

                                <button onclick="generateRehabPlan()" class="w-full bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition-colors">
                                    Generate Rehabilitation Plan
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Image Analysis Form -->
                    <div id="imageAnalysisForm" class="analysis-form hidden bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Medical Image Analysis</h3>
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Image Description</label>
                                    <textarea id="imageDescription" rows="4" class="w-full border border-gray-300 rounded-lg px-3 py-2" placeholder="Describe the medical image in detail..."></textarea>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Context (Optional)</label>
                                    <textarea id="imageContext" rows="2" class="w-full border border-gray-300 rounded-lg px-3 py-2" placeholder="Additional context or patient information..."></textarea>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Image Type</label>
                                    <select id="imageType" class="w-full border border-gray-300 rounded-lg px-3 py-2">
                                        <option value="xray">X-Ray</option>
                                        <option value="mri">MRI</option>
                                        <option value="ct">CT Scan</option>
                                        <option value="ultrasound">Ultrasound</option>
                                        <option value="other">Other</option>
                                    </select>
                                </div>

                                <button onclick="analyzeMedicalImage()" class="w-full bg-teal-600 text-white px-4 py-2 rounded-lg hover:bg-teal-700 transition-colors">
                                    Analyze Image
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Results Section -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">AI Analysis Results</h3>
                    
                    <!-- Loading Indicator -->
                    <div id="loadingIndicator" class="hidden">
                        <div class="flex items-center justify-center py-8">
                            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
                            <span class="ml-3 text-gray-600">Analyzing with Gemini AI...</span>
                        </div>
                    </div>

                    <!-- Results Content -->
                    <div id="resultsContent" class="space-y-4">
                        <div class="text-center text-gray-500 py-8">
                            <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                            </svg>
                            <p>Select an analysis type and provide data to get started</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('Gemini page loaded');
    
    let selectedAnalysisType = null;
    let symptoms = [];

    // Test connection on load
    testConnection();

    // Analysis type selection
    window.selectAnalysisType = function(type) {
        selectedAnalysisType = type;
        
        // Update button styles
        document.querySelectorAll('.analysis-type-btn').forEach(btn => {
            btn.classList.remove('border-blue-400', 'bg-blue-50');
        });
        event.target.classList.add('border-blue-400', 'bg-blue-50');
        
        // Show/hide forms
        document.querySelectorAll('.analysis-form').forEach(form => {
            form.classList.add('hidden');
        });
        document.getElementById(type + 'Form').classList.remove('hidden');
    };

    // Symptom management
    window.addSymptom = function() {
        const input = document.querySelector('.symptom-input');
        const symptom = input.value.trim();
        
        if (symptom && !symptoms.includes(symptom)) {
            symptoms.push(symptom);
            updateSymptomsList();
            input.value = '';
        }
    };

    function updateSymptomsList() {
        const container = document.getElementById('symptomsList');
        container.innerHTML = symptoms.map((symptom, index) => `
            <div class="flex items-center justify-between bg-gray-50 px-3 py-2 rounded">
                <span class="text-sm">${symptom}</span>
                <button onclick="removeSymptom(${index})" class="text-red-600 hover:text-red-800">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        `).join('');
    }

    window.removeSymptom = function(index) {
        symptoms.splice(index, 1);
        updateSymptomsList();
    };

    // API functions
    async function testConnection() {
        try {
            const response = await fetch('/gemini/test-connection', {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });
            const data = await response.json();
            
            const statusDiv = document.getElementById('connectionStatus');
            if (data.success) {
                statusDiv.innerHTML = `
                    <div class="flex items-center text-green-700">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                        <span>Gemini API connected successfully</span>
                    </div>
                `;
            } else {
                statusDiv.innerHTML = `
                    <div class="flex items-center text-red-700">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                        </svg>
                        <span>Gemini API connection failed: ${data.message}</span>
                    </div>
                `;
            }
        } catch (error) {
            console.error('Connection test failed:', error);
        }
    }

    async function makeRequest(url, data) {
        const response = await fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify(data)
        });
        return response.json();
    }

    function showLoading() {
        document.getElementById('loadingIndicator').classList.remove('hidden');
        document.getElementById('resultsContent').innerHTML = '';
    }

    function hideLoading() {
        document.getElementById('loadingIndicator').classList.add('hidden');
    }

    function displayResults(data) {
        hideLoading();
        
        const resultsContent = document.getElementById('resultsContent');
        if (data.success) {
            resultsContent.innerHTML = `
                <div class="space-y-4">
                    <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                        <h4 class="font-medium text-green-800 mb-2">Analysis Complete</h4>
                        <div class="text-sm text-green-700">
                            <p>Confidence: ${(data.confidence * 100).toFixed(1)}%</p>
                            <p>Tokens Used: ${data.tokens_used}</p>
                        </div>
                    </div>

                    <div>
                        <h4 class="font-medium text-gray-900 mb-2">AI Analysis</h4>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <div class="prose max-w-none text-sm">
                                ${data.analysis.replace(/\n/g, '<br>')}
                            </div>
                        </div>
                    </div>

                    <div class="flex space-x-2">
                        <button onclick="copyToClipboard('${data.analysis.replace(/'/g, "\\'")}')" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                            Copy Analysis
                        </button>
                        <button onclick="downloadAnalysis('${data.analysis.replace(/'/g, "\\'")}', '${selectedAnalysisType}')" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors">
                            Download
                        </button>
                    </div>
                </div>
            `;
        } else {
            resultsContent.innerHTML = `
                <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                    <h4 class="font-medium text-red-800 mb-2">Analysis Failed</h4>
                    <p class="text-red-700">${data.error}</p>
                </div>
            `;
        }
    }

    // Analysis functions
    window.generateDiagnosis = async function() {
        if (symptoms.length === 0) {
            alert('Please add at least one symptom');
            return;
        }

        showLoading();
        
        const patientData = {};
        const age = document.getElementById('patientAge').value;
        const gender = document.getElementById('patientGender').value;
        
        if (age) patientData.age = parseInt(age);
        if (gender) patientData.gender = gender;

        try {
            const data = await makeRequest('/gemini/generate-diagnosis', {
                symptoms: symptoms,
                patient_data: patientData
            });
            displayResults(data);
        } catch (error) {
            displayResults({ success: false, error: 'Request failed: ' + error.message });
        }
    };

    window.generateTreatment = async function() {
        const diagnosis = document.getElementById('diagnosisInput').value.trim();
        if (!diagnosis) {
            alert('Please enter a diagnosis');
            return;
        }

        showLoading();
        
        const patientData = {};
        const age = document.getElementById('treatmentAge').value;
        const gender = document.getElementById('treatmentGender').value;
        
        if (age) patientData.age = parseInt(age);
        if (gender) patientData.gender = gender;

        try {
            const data = await makeRequest('/gemini/generate-treatment', {
                diagnosis: diagnosis,
                patient_data: patientData
            });
            displayResults(data);
        } catch (error) {
            displayResults({ success: false, error: 'Request failed: ' + error.message });
        }
    };

    window.analyzePerformance = async function() {
        const performanceData = document.getElementById('performanceData').value.trim();
        if (!performanceData) {
            alert('Please enter performance data');
            return;
        }

        try {
            const data = JSON.parse(performanceData);
        } catch (error) {
            alert('Invalid JSON format for performance data');
            return;
        }

        showLoading();

        try {
            const data = await makeRequest('/gemini/analyze-performance', {
                performance_data: JSON.parse(performanceData)
            });
            displayResults(data);
        } catch (error) {
            displayResults({ success: false, error: 'Request failed: ' + error.message });
        }
    };

    window.predictInjuryRisk = async function() {
        const playerData = document.getElementById('playerData').value.trim();
        if (!playerData) {
            alert('Please enter player data');
            return;
        }

        try {
            const data = JSON.parse(playerData);
        } catch (error) {
            alert('Invalid JSON format for player data');
            return;
        }

        showLoading();

        try {
            const data = await makeRequest('/gemini/predict-injury-risk', {
                player_data: JSON.parse(playerData),
                performance_history: []
            });
            displayResults(data);
        } catch (error) {
            displayResults({ success: false, error: 'Request failed: ' + error.message });
        }
    };

    window.generateRehabPlan = async function() {
        const injury = document.getElementById('injuryDescription').value.trim();
        if (!injury) {
            alert('Please describe the injury');
            return;
        }

        showLoading();
        
        const patientData = {};
        const severity = document.getElementById('injurySeverity').value;
        const duration = document.getElementById('injuryDuration').value;
        
        if (severity) patientData.severity = severity;
        if (duration) patientData.duration = duration;

        try {
            const data = await makeRequest('/gemini/generate-rehab-plan', {
                injury: injury,
                patient_data: patientData
            });
            displayResults(data);
        } catch (error) {
            displayResults({ success: false, error: 'Request failed: ' + error.message });
        }
    };

    window.analyzeMedicalImage = async function() {
        const imageDescription = document.getElementById('imageDescription').value.trim();
        if (!imageDescription) {
            alert('Please describe the medical image');
            return;
        }

        showLoading();
        
        const context = document.getElementById('imageContext').value.trim();
        const imageType = document.getElementById('imageType').value;

        try {
            const data = await makeRequest('/gemini/analyze-medical-image', {
                image_description: imageDescription,
                context: context,
                image_type: imageType
            });
            displayResults(data);
        } catch (error) {
            displayResults({ success: false, error: 'Request failed: ' + error.message });
        }
    };

    // Utility functions
    window.copyToClipboard = function(text) {
        navigator.clipboard.writeText(text).then(() => {
            alert('Analysis copied to clipboard!');
        });
    };

    window.downloadAnalysis = function(text, type) {
        const blob = new Blob([text], { type: 'text/plain' });
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = `gemini_${type}_analysis_${new Date().toISOString().split('T')[0]}.txt`;
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        URL.revokeObjectURL(url);
    };
});
</script>
@endsection 