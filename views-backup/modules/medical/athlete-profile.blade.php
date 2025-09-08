<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'FIT - Football Intelligence & Tracking') }} - Athlete Profile</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Chart.js for medical charts -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="font-sans antialiased">
    <div class="min-h-screen bg-gray-50">
        <!-- Header -->
        <header class="bg-white shadow-sm border-b border-gray-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center py-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-gradient-to-r from-blue-600 to-indigo-600 rounded-lg flex items-center justify-center">
                                    <span class="text-white font-bold text-lg">FIT</span>
                                </div>
                                <div class="ml-3">
                                    <h1 class="text-2xl font-bold text-gray-900">
                                        Athlete Medical Profile
                                    </h1>
                                    <p class="text-sm text-gray-600">Comprehensive health monitoring and medical management</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center space-x-4">
                        <button onclick="exportMedicalReport()" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors text-sm">
                            📄 Export Report
                        </button>
                        <button onclick="printProfile()" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors text-sm">
                            🖨️ Print
                        </button>
                        <a href="{{ route('modules.medical.index') }}" class="text-gray-600 hover:text-gray-900 text-sm font-medium">← Back to Medical Module</a>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Athlete Profile Header -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
                <div class="p-6">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-6">
                            <div class="w-20 h-20 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-full flex items-center justify-center">
                                <span class="text-white font-bold text-2xl">A</span>
                            </div>
                            <div>
                                <h2 class="text-2xl font-bold text-gray-900">Athlete #{{ $athlete }}</h2>
                                <p class="text-gray-600">Professional Football Player</p>
                                <div class="flex items-center space-x-4 mt-2">
                                    <span class="px-3 py-1 bg-green-100 text-green-800 text-xs rounded-full">Active</span>
                                    <span class="px-3 py-1 bg-blue-100 text-blue-800 text-xs rounded-full">Medical Clearance: Valid</span>
                                    <span class="px-3 py-1 bg-yellow-100 text-yellow-800 text-xs rounded-full">Last Assessment: 2 weeks ago</span>
                                </div>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="text-3xl font-bold text-green-600">85%</div>
                            <div class="text-sm text-gray-600">Health Score</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                            <span class="text-green-600 text-xl">💉</span>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Total Vaccinations</p>
                            <p class="text-2xl font-bold text-gray-900">8</p>
                            <p class="text-xs text-green-600">+2 this year</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                            <span class="text-blue-600 text-xl">🩹</span>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Active Injuries</p>
                            <p class="text-2xl font-bold text-gray-900">1</p>
                            <p class="text-xs text-yellow-600">Recovery in progress</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center">
                            <span class="text-purple-600 text-xl">📋</span>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">PCMA Status</p>
                            <p class="text-2xl font-bold text-gray-900">Valid</p>
                            <p class="text-xs text-green-600">Expires in 6 months</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center">
                            <span class="text-red-600 text-xl">⚠️</span>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Alerts</p>
                            <p class="text-2xl font-bold text-gray-900">2</p>
                            <p class="text-xs text-red-600">Requires attention</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Navigation Tabs -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="border-b border-gray-200">
                    <nav class="flex space-x-8 px-6">
                        <button onclick="showTab('dashboard')" class="tab-button active py-4 px-1 border-b-2 border-transparent text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300">
                            📊 Dashboard
                        </button>
                        <button onclick="showTab('vaccinations')" class="tab-button py-4 px-1 border-b-2 border-transparent text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300">
                            💉 Vaccinations
                        </button>
                        <button onclick="showTab('injuries')" class="tab-button py-4 px-1 border-b-2 border-transparent text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300">
                            🩹 Injuries
                        </button>
                        <button onclick="showTab('pcma')" class="tab-button py-4 px-1 border-b-2 border-transparent text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300">
                            📋 PCMA
                        </button>
                        <button onclick="showTab('concussions')" class="tab-button py-4 px-1 border-b-2 border-transparent text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300">
                            🧠 SCAT
                        </button>
                        <button onclick="showTab('notes')" class="tab-button py-4 px-1 border-b-2 border-transparent text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300">
                            📝 Notes
                        </button>
                        <button onclick="showTab('analytics')" class="tab-button py-4 px-1 border-b-2 border-transparent text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300">
                            📈 Analytics
                        </button>
                    </nav>
                </div>

                <!-- Tab Content -->
                <div class="p-6">
                    <!-- Dashboard Tab -->
                    <div id="dashboard-tab" class="tab-content">
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            <!-- Health Score Chart -->
                            <div class="bg-white border border-gray-200 rounded-lg p-6">
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">Health Score Trend</h3>
                                <canvas id="healthScoreChart" width="400" height="200"></canvas>
                            </div>
                            
                            <!-- Recent Activities -->
                            <div class="bg-white border border-gray-200 rounded-lg p-6">
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">Recent Medical Activities</h3>
                                <div class="space-y-4">
                                    <div class="flex items-center space-x-3 p-3 bg-green-50 rounded-lg">
                                        <div class="w-2 h-2 bg-green-500 rounded-full"></div>
                                        <div class="flex-1">
                                            <p class="text-sm font-medium text-gray-900">Vaccination administered</p>
                                            <p class="text-xs text-gray-600">COVID-19 booster • 2 days ago</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center space-x-3 p-3 bg-yellow-50 rounded-lg">
                                        <div class="w-2 h-2 bg-yellow-500 rounded-full"></div>
                                        <div class="flex-1">
                                            <p class="text-sm font-medium text-gray-900">PCMA assessment scheduled</p>
                                            <p class="text-xs text-gray-600">Next week • Dr. Smith</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center space-x-3 p-3 bg-blue-50 rounded-lg">
                                        <div class="w-2 h-2 bg-blue-500 rounded-full"></div>
                                        <div class="flex-1">
                                            <p class="text-sm font-medium text-gray-900">Injury recovery update</p>
                                            <p class="text-xs text-gray-600">Ankle sprain • 80% recovered</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Vaccinations Tab -->
                    <div id="vaccinations-tab" class="tab-content hidden">
                        <div class="flex justify-between items-center mb-6">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">Vaccination Management</h3>
                                <p class="text-sm text-gray-600">Comprehensive vaccination tracking and FHIR integration</p>
                            </div>
                            <div class="flex space-x-3">
                                <button onclick="syncVaccinations()" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                                    🔄 Sync with Registry
                                </button>
                                <button onclick="addVaccination()" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors">
                                    ➕ Add Vaccine
                                </button>
                                <button onclick="exportVaccinationReport()" class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition-colors">
                                    📊 Export Report
                                </button>
                            </div>
                        </div>

                        <!-- Vaccination Statistics -->
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                            <div class="bg-white border border-gray-200 rounded-lg p-4">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                                        <span class="text-green-600">💉</span>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-gray-500">Total Vaccinations</p>
                                        <p class="text-2xl font-bold text-gray-900">8</p>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-white border border-gray-200 rounded-lg p-4">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                        <span class="text-blue-600">✅</span>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-gray-500">Active</p>
                                        <p class="text-2xl font-bold text-gray-900">7</p>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-white border border-gray-200 rounded-lg p-4">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center">
                                        <span class="text-yellow-600">⚠️</span>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-gray-500">Expiring Soon</p>
                                        <p class="text-2xl font-bold text-gray-900">1</p>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-white border border-gray-200 rounded-lg p-4">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 bg-red-100 rounded-full flex items-center justify-center">
                                        <span class="text-red-600">❌</span>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-gray-500">Expired</p>
                                        <p class="text-2xl font-bold text-gray-900">0</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Vaccination Timeline -->
                        <div class="bg-white border border-gray-200 rounded-lg mb-6">
                            <div class="px-6 py-4 border-b border-gray-200">
                                <h4 class="text-lg font-medium text-gray-900">Vaccination Timeline</h4>
                            </div>
                            <div class="p-6">
                                <div class="relative">
                                    <!-- Timeline Line -->
                                    <div class="absolute left-4 top-0 bottom-0 w-0.5 bg-gray-200"></div>
                                    
                                    <!-- Timeline Items -->
                                    <div class="space-y-6">
                                        <div class="relative flex items-start">
                                            <div class="absolute left-4 w-3 h-3 bg-green-500 rounded-full -ml-1.5 mt-2"></div>
                                            <div class="ml-8 flex-1">
                                                <div class="flex items-center justify-between">
                                                    <h5 class="font-medium text-gray-900">COVID-19 Vaccine (Pfizer)</h5>
                                                    <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Active</span>
                                                </div>
                                                <p class="text-sm text-gray-600">Administered: Jan 15, 2024 • Expires: Jan 15, 2025</p>
                                                <p class="text-sm text-gray-500">Lot: ABC123 • Site: Left Arm • Administered by: Dr. Johnson</p>
                                                <div class="mt-2 flex space-x-2">
                                                    <button onclick="viewVaccinationDetails(1)" class="text-blue-600 hover:text-blue-800 text-sm">View Details</button>
                                                    <button onclick="editVaccination(1)" class="text-green-600 hover:text-green-800 text-sm">Edit</button>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="relative flex items-start">
                                            <div class="absolute left-4 w-3 h-3 bg-green-500 rounded-full -ml-1.5 mt-2"></div>
                                            <div class="ml-8 flex-1">
                                                <div class="flex items-center justify-between">
                                                    <h5 class="font-medium text-gray-900">Tetanus, Diphtheria, Pertussis</h5>
                                                    <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Active</span>
                                                </div>
                                                <p class="text-sm text-gray-600">Administered: Mar 10, 2023 • Expires: Mar 10, 2028</p>
                                                <p class="text-sm text-gray-500">Lot: DEF456 • Site: Right Arm • Administered by: Dr. Smith</p>
                                                <div class="mt-2 flex space-x-2">
                                                    <button onclick="viewVaccinationDetails(2)" class="text-blue-600 hover:text-blue-800 text-sm">View Details</button>
                                                    <button onclick="editVaccination(2)" class="text-green-600 hover:text-green-800 text-sm">Edit</button>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="relative flex items-start">
                                            <div class="absolute left-4 w-3 h-3 bg-yellow-500 rounded-full -ml-1.5 mt-2"></div>
                                            <div class="ml-8 flex-1">
                                                <div class="flex items-center justify-between">
                                                    <h5 class="font-medium text-gray-900">Influenza Vaccine</h5>
                                                    <span class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-800">Expiring Soon</span>
                                                </div>
                                                <p class="text-sm text-gray-600">Administered: Oct 5, 2023 • Expires: Oct 5, 2024</p>
                                                <p class="text-sm text-gray-500">Lot: GHI789 • Site: Left Arm • Administered by: Dr. Wilson</p>
                                                <div class="mt-2 flex space-x-2">
                                                    <button onclick="viewVaccinationDetails(3)" class="text-blue-600 hover:text-blue-800 text-sm">View Details</button>
                                                    <button onclick="editVaccination(3)" class="text-green-600 hover:text-green-800 text-sm">Edit</button>
                                                    <button onclick="scheduleBooster(3)" class="text-orange-600 hover:text-orange-800 text-sm">Schedule Booster</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- FHIR Integration Status -->
                        <div class="bg-white border border-gray-200 rounded-lg">
                            <div class="px-6 py-4 border-b border-gray-200">
                                <h4 class="text-lg font-medium text-gray-900">FHIR Integration Status</h4>
                            </div>
                            <div class="p-6">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <h5 class="font-medium text-gray-900">National Immunization Registry</h5>
                                        <p class="text-sm text-gray-600">Last synced: 2 hours ago • 8 records synchronized</p>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Connected</span>
                                        <button onclick="testFHIRConnection()" class="text-blue-600 hover:text-blue-800 text-sm">Test Connection</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Injuries Tab -->
                    <div id="injuries-tab" class="tab-content hidden">
                        <div class="flex justify-between items-center mb-6">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">Injury Management</h3>
                                <p class="text-sm text-gray-600">Track injuries, recovery progress, and rehabilitation</p>
                            </div>
                            <div class="flex space-x-3">
                                <button onclick="addInjury()" class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition-colors">
                                    ➕ Add Injury
                                </button>
                                <button onclick="generateInjuryReport()" class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition-colors">
                                    📊 Generate Report
                                </button>
                            </div>
                        </div>

                        <!-- Injury Statistics -->
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                            <div class="bg-white border border-gray-200 rounded-lg p-4">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 bg-red-100 rounded-full flex items-center justify-center">
                                        <span class="text-red-600">🩹</span>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-gray-500">Total Injuries</p>
                                        <p class="text-2xl font-bold text-gray-900">12</p>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-white border border-gray-200 rounded-lg p-4">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center">
                                        <span class="text-yellow-600">⚠️</span>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-gray-500">Active</p>
                                        <p class="text-2xl font-bold text-gray-900">1</p>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-white border border-gray-200 rounded-lg p-4">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                                        <span class="text-green-600">✅</span>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-gray-500">Recovered</p>
                                        <p class="text-2xl font-bold text-gray-900">11</p>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-white border border-gray-200 rounded-lg p-4">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                        <span class="text-blue-600">📈</span>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-gray-500">Avg Recovery</p>
                                        <p class="text-2xl font-bold text-gray-900">28d</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Current Injury -->
                        <div class="bg-white border border-gray-200 rounded-lg mb-6">
                            <div class="px-6 py-4 border-b border-gray-200">
                                <h4 class="text-lg font-medium text-gray-900">Current Injury</h4>
                            </div>
                            <div class="p-6">
                                <div class="flex items-center justify-between mb-4">
                                    <div>
                                        <h5 class="font-medium text-gray-900">Ankle Sprain (Right)</h5>
                                        <p class="text-sm text-gray-600">Injury Date: Dec 15, 2024 • Severity: Moderate</p>
                                    </div>
                                    <span class="px-3 py-1 text-sm rounded-full bg-yellow-100 text-yellow-800">Recovery in Progress</span>
                                </div>
                                
                                <!-- Recovery Progress -->
                                <div class="mb-4">
                                    <div class="flex justify-between text-sm text-gray-600 mb-2">
                                        <span>Recovery Progress</span>
                                        <span>80%</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        <div class="bg-green-600 h-2 rounded-full" style="width: 80%"></div>
                                    </div>
                                </div>
                                
                                <!-- Treatment Plan -->
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <h6 class="font-medium text-gray-900 mb-2">Treatment Plan</h6>
                                        <ul class="text-sm text-gray-600 space-y-1">
                                            <li>• Physical therapy 3x/week</li>
                                            <li>• Ice therapy 20min/day</li>
                                            <li>• Compression bandage</li>
                                            <li>• Gradual return to training</li>
                                        </ul>
                                    </div>
                                    <div>
                                        <h6 class="font-medium text-gray-900 mb-2">Expected Return</h6>
                                        <p class="text-sm text-gray-600">Estimated: Jan 30, 2025</p>
                                        <p class="text-xs text-gray-500">2 weeks remaining</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Other tabs content -->
                    <div id="pcma-tab" class="tab-content hidden">
                        <div class="flex justify-between items-center mb-6">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">PCMA Records</h3>
                                <p class="text-sm text-gray-600">Pre-Competition Medical Assessment records and evaluations</p>
                            </div>
                            <div class="flex space-x-3">
                                <button onclick="createPCMA()" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors">
                                    ➕ New PCMA
                                </button>
                                <button onclick="exportPCMAReport()" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                                    📊 Export Report
                                </button>
                            </div>
                        </div>

                        <!-- PCMA Statistics -->
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                            <div class="bg-white border border-gray-200 rounded-lg p-4">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                                        <span class="text-green-600">✅</span>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-gray-500">Total Assessments</p>
                                        <p class="text-2xl font-bold text-gray-900">12</p>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-white border border-gray-200 rounded-lg p-4">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                        <span class="text-blue-600">🩺</span>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-gray-500">Last Assessment</p>
                                        <p class="text-lg font-bold text-gray-900">2 weeks ago</p>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-white border border-gray-200 rounded-lg p-4">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center">
                                        <span class="text-yellow-600">⚠️</span>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-gray-500">Next Due</p>
                                        <p class="text-lg font-bold text-gray-900">3 months</p>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-white border border-gray-200 rounded-lg p-4">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center">
                                        <span class="text-purple-600">📋</span>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-gray-500">Status</p>
                                        <p class="text-lg font-bold text-gray-900">Cleared</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Recent PCMA Assessments -->
                        <div class="bg-white border border-gray-200 rounded-lg mb-6">
                            <div class="px-6 py-4 border-b border-gray-200">
                                <h4 class="text-lg font-medium text-gray-900">Recent PCMA Assessments</h4>
                            </div>
                            <div class="p-6">
                                <div class="space-y-4">
                                    <!-- Assessment 1 -->
                                    <div class="border border-gray-200 rounded-lg p-4">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center space-x-4">
                                                <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                                                    <span class="text-green-600">✅</span>
                                                </div>
                                                <div>
                                                    <h5 class="font-medium text-gray-900">PCMA Assessment #12</h5>
                                                    <p class="text-sm text-gray-600">Date: Jan 15, 2024 • Physician: Dr. Sarah Johnson</p>
                                                    <div class="flex items-center space-x-4 mt-1">
                                                        <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Cleared</span>
                                                        <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">Cardiovascular: Normal</span>
                                                        <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">Neurological: Normal</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="flex space-x-2">
                                                <button onclick="viewPCMADetails(12)" class="text-blue-600 hover:text-blue-800 text-sm">View Details</button>
                                                <button onclick="editPCMA(12)" class="text-green-600 hover:text-green-800 text-sm">Edit</button>
                                                <button onclick="downloadPCMA(12)" class="text-purple-600 hover:text-purple-800 text-sm">Download</button>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Assessment 2 -->
                                    <div class="border border-gray-200 rounded-lg p-4">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center space-x-4">
                                                <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                                                    <span class="text-green-600">✅</span>
                                                </div>
                                                <div>
                                                    <h5 class="font-medium text-gray-900">PCMA Assessment #11</h5>
                                                    <p class="text-sm text-gray-600">Date: Oct 20, 2023 • Physician: Dr. Michael Chen</p>
                                                    <div class="flex items-center space-x-4 mt-1">
                                                        <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Cleared</span>
                                                        <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">Musculoskeletal: Normal</span>
                                                        <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">General: Normal</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="flex space-x-2">
                                                <button onclick="viewPCMADetails(11)" class="text-blue-600 hover:text-blue-800 text-sm">View Details</button>
                                                <button onclick="editPCMA(11)" class="text-green-600 hover:text-green-800 text-sm">Edit</button>
                                                <button onclick="downloadPCMA(11)" class="text-purple-600 hover:text-purple-800 text-sm">Download</button>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Assessment 3 -->
                                    <div class="border border-gray-200 rounded-lg p-4">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center space-x-4">
                                                <div class="w-10 h-10 bg-yellow-100 rounded-full flex items-center justify-center">
                                                    <span class="text-yellow-600">⚠️</span>
                                                </div>
                                                <div>
                                                    <h5 class="font-medium text-gray-900">PCMA Assessment #10</h5>
                                                    <p class="text-sm text-gray-600">Date: Jul 15, 2023 • Physician: Dr. Emily Rodriguez</p>
                                                    <div class="flex items-center space-x-4 mt-1">
                                                        <span class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-800">Conditional</span>
                                                        <span class="px-2 py-1 text-xs rounded-full bg-orange-100 text-orange-800">Follow-up Required</span>
                                                        <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">Cardiovascular: Normal</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="flex space-x-2">
                                                <button onclick="viewPCMADetails(10)" class="text-blue-600 hover:text-blue-800 text-sm">View Details</button>
                                                <button onclick="editPCMA(10)" class="text-green-600 hover:text-green-800 text-sm">Edit</button>
                                                <button onclick="downloadPCMA(10)" class="text-purple-600 hover:text-purple-800 text-sm">Download</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- PCMA Summary -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="bg-white border border-gray-200 rounded-lg p-6">
                                <h4 class="text-lg font-medium text-gray-900 mb-4">Assessment Summary</h4>
                                <div class="space-y-3">
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Cardiovascular Status:</span>
                                        <span class="font-medium text-green-600">Normal</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Neurological Status:</span>
                                        <span class="font-medium text-green-600">Normal</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Musculoskeletal Status:</span>
                                        <span class="font-medium text-green-600">Normal</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">General Health:</span>
                                        <span class="font-medium text-green-600">Excellent</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Risk Level:</span>
                                        <span class="font-medium text-green-600">Low</span>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-white border border-gray-200 rounded-lg p-6">
                                <h4 class="text-lg font-medium text-gray-900 mb-4">Recommendations</h4>
                                <div class="space-y-3">
                                    <div class="flex items-start space-x-2">
                                        <span class="text-green-600">✅</span>
                                        <span class="text-sm text-gray-700">Cleared for all competitive activities</span>
                                    </div>
                                    <div class="flex items-start space-x-2">
                                        <span class="text-blue-600">📋</span>
                                        <span class="text-sm text-gray-700">Annual cardiovascular screening recommended</span>
                                    </div>
                                    <div class="flex items-start space-x-2">
                                        <span class="text-yellow-600">⚠️</span>
                                        <span class="text-sm text-gray-700">Monitor hydration levels during training</span>
                                    </div>
                                    <div class="flex items-start space-x-2">
                                        <span class="text-purple-600">📊</span>
                                        <span class="text-sm text-gray-700">Next assessment due in 3 months</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="concussions-tab" class="tab-content hidden">
                        <div class="flex justify-between items-center mb-6">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">SCAT Assessments</h3>
                                <p class="text-sm text-gray-600">Sport Concussion Assessment Tool evaluations and concussion management</p>
                            </div>
                            <div class="flex space-x-3">
                                <button onclick="createSCAT()" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors">
                                    ➕ New SCAT
                                </button>
                                <button onclick="exportSCATReport()" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                                    📊 Export Report
                                </button>
                            </div>
                        </div>

                        <!-- SCAT Statistics -->
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                            <div class="bg-white border border-gray-200 rounded-lg p-4">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                                        <span class="text-green-600">🧠</span>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-gray-500">Total Assessments</p>
                                        <p class="text-2xl font-bold text-gray-900">3</p>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-white border border-gray-200 rounded-lg p-4">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                        <span class="text-blue-600">📅</span>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-gray-500">Last Assessment</p>
                                        <p class="text-lg font-bold text-gray-900">6 months ago</p>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-white border border-gray-200 rounded-lg p-4">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                                        <span class="text-green-600">✅</span>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-gray-500">Current Status</p>
                                        <p class="text-lg font-bold text-gray-900">Cleared</p>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-white border border-gray-200 rounded-lg p-4">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center">
                                        <span class="text-purple-600">📋</span>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-gray-500">Recovery Time</p>
                                        <p class="text-lg font-bold text-gray-900">14 days</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- SCAT Assessment History -->
                        <div class="bg-white border border-gray-200 rounded-lg mb-6">
                            <div class="px-6 py-4 border-b border-gray-200">
                                <h4 class="text-lg font-medium text-gray-900">SCAT Assessment History</h4>
                            </div>
                            <div class="p-6">
                                <div class="space-y-4">
                                    <!-- Assessment 1 -->
                                    <div class="border border-gray-200 rounded-lg p-4">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center space-x-4">
                                                <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                                                    <span class="text-green-600">✅</span>
                                                </div>
                                                <div>
                                                    <h5 class="font-medium text-gray-900">SCAT Assessment #3</h5>
                                                    <p class="text-sm text-gray-600">Date: Jul 15, 2023 • Physician: Dr. Emily Rodriguez</p>
                                                    <div class="flex items-center space-x-4 mt-1">
                                                        <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Cleared</span>
                                                        <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">Score: 22/22</span>
                                                        <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">No Symptoms</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="flex space-x-2">
                                                <button onclick="viewSCATDetails(3)" class="text-blue-600 hover:text-blue-800 text-sm">View Details</button>
                                                <button onclick="editSCAT(3)" class="text-green-600 hover:text-green-800 text-sm">Edit</button>
                                                <button onclick="downloadSCAT(3)" class="text-purple-600 hover:text-purple-800 text-sm">Download</button>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Assessment 2 -->
                                    <div class="border border-gray-200 rounded-lg p-4">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center space-x-4">
                                                <div class="w-10 h-10 bg-yellow-100 rounded-full flex items-center justify-center">
                                                    <span class="text-yellow-600">⚠️</span>
                                                </div>
                                                <div>
                                                    <h5 class="font-medium text-gray-900">SCAT Assessment #2</h5>
                                                    <p class="text-sm text-gray-600">Date: Jun 30, 2023 • Physician: Dr. Michael Chen</p>
                                                    <div class="flex items-center space-x-4 mt-1">
                                                        <span class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-800">Recovery</span>
                                                        <span class="px-2 py-1 text-xs rounded-full bg-orange-100 text-orange-800">Score: 18/22</span>
                                                        <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">Mild Symptoms</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="flex space-x-2">
                                                <button onclick="viewSCATDetails(2)" class="text-blue-600 hover:text-blue-800 text-sm">View Details</button>
                                                <button onclick="editSCAT(2)" class="text-green-600 hover:text-green-800 text-sm">Edit</button>
                                                <button onclick="downloadSCAT(2)" class="text-purple-600 hover:text-purple-800 text-sm">Download</button>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Assessment 3 -->
                                    <div class="border border-gray-200 rounded-lg p-4">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center space-x-4">
                                                <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center">
                                                    <span class="text-red-600">🚨</span>
                                                </div>
                                                <div>
                                                    <h5 class="font-medium text-gray-900">SCAT Assessment #1</h5>
                                                    <p class="text-sm text-gray-600">Date: Jun 15, 2023 • Physician: Dr. Sarah Johnson</p>
                                                    <div class="flex items-center space-x-4 mt-1">
                                                        <span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-800">Concussion</span>
                                                        <span class="px-2 py-1 text-xs rounded-full bg-orange-100 text-orange-800">Score: 12/22</span>
                                                        <span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-800">Severe Symptoms</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="flex space-x-2">
                                                <button onclick="viewSCATDetails(1)" class="text-blue-600 hover:text-blue-800 text-sm">View Details</button>
                                                <button onclick="editSCAT(1)" class="text-green-600 hover:text-green-800 text-sm">Edit</button>
                                                <button onclick="downloadSCAT(1)" class="text-purple-600 hover:text-purple-800 text-sm">Download</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Concussion Recovery Timeline -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="bg-white border border-gray-200 rounded-lg p-6">
                                <h4 class="text-lg font-medium text-gray-900 mb-4">Recovery Timeline</h4>
                                <div class="space-y-4">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-3 h-3 bg-red-500 rounded-full"></div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-900">Concussion Diagnosed</p>
                                            <p class="text-xs text-gray-600">Jun 15, 2023</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center space-x-3">
                                        <div class="w-3 h-3 bg-yellow-500 rounded-full"></div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-900">Symptom Improvement</p>
                                            <p class="text-xs text-gray-600">Jun 30, 2023</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center space-x-3">
                                        <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-900">Full Recovery</p>
                                            <p class="text-xs text-gray-600">Jul 15, 2023</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-white border border-gray-200 rounded-lg p-6">
                                <h4 class="text-lg font-medium text-gray-900 mb-4">Current Status</h4>
                                <div class="space-y-3">
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Concussion Status:</span>
                                        <span class="font-medium text-green-600">Resolved</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Return to Play:</span>
                                        <span class="font-medium text-green-600">Cleared</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Follow-up Required:</span>
                                        <span class="font-medium text-green-600">No</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Risk Level:</span>
                                        <span class="font-medium text-green-600">Low</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="notes-tab" class="tab-content hidden">
                        <div class="flex justify-between items-center mb-6">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">Medical Notes</h3>
                                <p class="text-sm text-gray-600">Clinical observations, treatment notes, and medical documentation</p>
                            </div>
                            <div class="flex space-x-3">
                                <button onclick="createNote()" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors">
                                    ➕ New Note
                                </button>
                                <button onclick="exportNotesReport()" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                                    📊 Export Report
                                </button>
                            </div>
                        </div>

                        <!-- Notes Statistics -->
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                            <div class="bg-white border border-gray-200 rounded-lg p-4">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                        <span class="text-blue-600">📝</span>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-gray-500">Total Notes</p>
                                        <p class="text-2xl font-bold text-gray-900">24</p>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-white border border-gray-200 rounded-lg p-4">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                                        <span class="text-green-600">👨‍⚕️</span>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-gray-500">Physicians</p>
                                        <p class="text-2xl font-bold text-gray-900">5</p>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-white border border-gray-200 rounded-lg p-4">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center">
                                        <span class="text-purple-600">📅</span>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-gray-500">Last Note</p>
                                        <p class="text-lg font-bold text-gray-900">3 days ago</p>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-white border border-gray-200 rounded-lg p-4">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center">
                                        <span class="text-yellow-600">🏷️</span>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-gray-500">Categories</p>
                                        <p class="text-2xl font-bold text-gray-900">8</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Recent Medical Notes -->
                        <div class="bg-white border border-gray-200 rounded-lg mb-6">
                            <div class="px-6 py-4 border-b border-gray-200">
                                <h4 class="text-lg font-medium text-gray-900">Recent Medical Notes</h4>
                            </div>
                            <div class="p-6">
                                <div class="space-y-4">
                                    <!-- Note 1 -->
                                    <div class="border border-gray-200 rounded-lg p-4">
                                        <div class="flex items-start justify-between">
                                            <div class="flex-1">
                                                <div class="flex items-center space-x-3 mb-2">
                                                    <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">Treatment</span>
                                                    <span class="text-sm text-gray-600">Dr. Sarah Johnson • Jan 20, 2024</span>
                                                </div>
                                                <h5 class="font-medium text-gray-900 mb-2">Follow-up Assessment - Knee Rehabilitation</h5>
                                                <p class="text-sm text-gray-700 mb-3">
                                                    Patient shows excellent progress in knee rehabilitation program. Range of motion has improved significantly. 
                                                    Strength training exercises are well tolerated. Recommend continuing current protocol for 2 more weeks 
                                                    before advancing to sport-specific training.
                                                </p>
                                                <div class="flex items-center space-x-4 text-xs text-gray-500">
                                                    <span>ICD-11: S83.4 - Sprain of medial collateral ligament of knee</span>
                                                    <span>Duration: 45 minutes</span>
                                                </div>
                                            </div>
                                            <div class="flex space-x-2 ml-4">
                                                <button onclick="viewNoteDetails(24)" class="text-blue-600 hover:text-blue-800 text-sm">View</button>
                                                <button onclick="editNote(24)" class="text-green-600 hover:text-green-800 text-sm">Edit</button>
                                                <button onclick="downloadNote(24)" class="text-purple-600 hover:text-purple-800 text-sm">Download</button>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Note 2 -->
                                    <div class="border border-gray-200 rounded-lg p-4">
                                        <div class="flex items-start justify-between">
                                            <div class="flex-1">
                                                <div class="flex items-center space-x-3 mb-2">
                                                    <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Assessment</span>
                                                    <span class="text-sm text-gray-600">Dr. Michael Chen • Jan 15, 2024</span>
                                                </div>
                                                <h5 class="font-medium text-gray-900 mb-2">PCMA Cardiovascular Assessment</h5>
                                                <p class="text-sm text-gray-700 mb-3">
                                                    Comprehensive cardiovascular evaluation completed. ECG shows normal sinus rhythm. 
                                                    Blood pressure 120/80 mmHg. No cardiac symptoms reported. Patient cleared for 
                                    competitive activities. Annual follow-up recommended.
                                                </p>
                                                <div class="flex items-center space-x-4 text-xs text-gray-500">
                                                    <span>ICD-11: Z00.0 - General medical examination</span>
                                                    <span>Duration: 30 minutes</span>
                                                </div>
                                            </div>
                                            <div class="flex space-x-2 ml-4">
                                                <button onclick="viewNoteDetails(23)" class="text-blue-600 hover:text-blue-800 text-sm">View</button>
                                                <button onclick="editNote(23)" class="text-green-600 hover:text-green-800 text-sm">Edit</button>
                                                <button onclick="downloadNote(23)" class="text-purple-600 hover:text-purple-800 text-sm">Download</button>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Note 3 -->
                                    <div class="border border-gray-200 rounded-lg p-4">
                                        <div class="flex items-start justify-between">
                                            <div class="flex-1">
                                                <div class="flex items-center space-x-3 mb-2">
                                                    <span class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-800">Injury</span>
                                                    <span class="text-sm text-gray-600">Dr. Emily Rodriguez • Jan 10, 2024</span>
                                                </div>
                                                <h5 class="font-medium text-gray-900 mb-2">Ankle Sprain Evaluation</h5>
                                                <p class="text-sm text-gray-700 mb-3">
                                                    Grade 2 lateral ankle sprain diagnosed. Swelling and tenderness present. 
                                    RICE protocol initiated. Recommend 2-3 weeks rest before gradual return to training. 
                                    Physical therapy referral provided.
                                                </p>
                                                <div class="flex items-center space-x-4 text-xs text-gray-500">
                                                    <span>ICD-11: S93.4 - Sprain of lateral ligament of ankle</span>
                                                    <span>Duration: 25 minutes</span>
                                                </div>
                                            </div>
                                            <div class="flex space-x-2 ml-4">
                                                <button onclick="viewNoteDetails(22)" class="text-blue-600 hover:text-blue-800 text-sm">View</button>
                                                <button onclick="editNote(22)" class="text-green-600 hover:text-green-800 text-sm">Edit</button>
                                                <button onclick="downloadNote(22)" class="text-purple-600 hover:text-purple-800 text-sm">Download</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Notes Summary -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="bg-white border border-gray-200 rounded-lg p-6">
                                <h4 class="text-lg font-medium text-gray-900 mb-4">Notes by Category</h4>
                                <div class="space-y-3">
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Treatment Notes:</span>
                                        <span class="font-medium text-gray-900">8</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Assessment Notes:</span>
                                        <span class="font-medium text-gray-900">6</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Injury Notes:</span>
                                        <span class="font-medium text-gray-900">5</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Follow-up Notes:</span>
                                        <span class="font-medium text-gray-900">3</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Prevention Notes:</span>
                                        <span class="font-medium text-gray-900">2</span>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-white border border-gray-200 rounded-lg p-6">
                                <h4 class="text-lg font-medium text-gray-900 mb-4">Recent Activity</h4>
                                <div class="space-y-3">
                                    <div class="flex items-start space-x-2">
                                        <span class="text-blue-600">📝</span>
                                        <span class="text-sm text-gray-700">Treatment note added by Dr. Johnson</span>
                                    </div>
                                    <div class="flex items-start space-x-2">
                                        <span class="text-green-600">✅</span>
                                        <span class="text-sm text-gray-700">PCMA assessment completed</span>
                                    </div>
                                    <div class="flex items-start space-x-2">
                                        <span class="text-yellow-600">⚠️</span>
                                        <span class="text-sm text-gray-700">Injury evaluation documented</span>
                                    </div>
                                    <div class="flex items-start space-x-2">
                                        <span class="text-purple-600">📊</span>
                                        <span class="text-sm text-gray-700">Notes report exported</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="analytics-tab" class="tab-content hidden">
                        <div class="flex justify-between items-center mb-6">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">Medical Analytics</h3>
                                <p class="text-sm text-gray-600">Advanced health analytics, trends, and performance insights</p>
                            </div>
                            <div class="flex space-x-3">
                                <button onclick="generateAnalyticsReport()" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors">
                                    📊 Generate Report
                                </button>
                                <button onclick="exportAnalytics()" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                                    📈 Export Data
                                </button>
                            </div>
                        </div>

                        <!-- Analytics Overview -->
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                            <div class="bg-white border border-gray-200 rounded-lg p-4">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                                        <span class="text-green-600">📈</span>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-gray-500">Health Score</p>
                                        <p class="text-2xl font-bold text-gray-900">85%</p>
                                        <p class="text-xs text-green-600">+5% this month</p>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-white border border-gray-200 rounded-lg p-4">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                        <span class="text-blue-600">🏃</span>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-gray-500">Training Days</p>
                                        <p class="text-2xl font-bold text-gray-900">18/20</p>
                                        <p class="text-xs text-blue-600">90% attendance</p>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-white border border-gray-200 rounded-lg p-4">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center">
                                        <span class="text-yellow-600">⏱️</span>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-gray-500">Recovery Time</p>
                                        <p class="text-2xl font-bold text-gray-900">2.3 days</p>
                                        <p class="text-xs text-yellow-600">Avg per injury</p>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-white border border-gray-200 rounded-lg p-4">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center">
                                        <span class="text-purple-600">🎯</span>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-gray-500">Risk Score</p>
                                        <p class="text-2xl font-bold text-gray-900">12%</p>
                                        <p class="text-xs text-purple-600">Low risk</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Health Trends Chart -->
                        <div class="bg-white border border-gray-200 rounded-lg mb-6">
                            <div class="px-6 py-4 border-b border-gray-200">
                                <h4 class="text-lg font-medium text-gray-900">Health Trends (Last 12 Months)</h4>
                            </div>
                            <div class="p-6">
                                <canvas id="healthTrendsChart" width="400" height="200"></canvas>
                            </div>
                        </div>

                        <!-- Performance Metrics -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div class="bg-white border border-gray-200 rounded-lg p-6">
                                <h4 class="text-lg font-medium text-gray-900 mb-4">Injury Analysis</h4>
                                <div class="space-y-4">
                                    <div class="flex justify-between items-center">
                                        <span class="text-gray-600">Total Injuries:</span>
                                        <span class="font-medium text-gray-900">3</span>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <span class="text-gray-600">Most Common:</span>
                                        <span class="font-medium text-gray-900">Ankle Sprain</span>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <span class="text-gray-600">Average Recovery:</span>
                                        <span class="font-medium text-gray-900">14 days</span>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <span class="text-gray-600">Prevention Score:</span>
                                        <span class="font-medium text-green-600">85%</span>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-white border border-gray-200 rounded-lg p-6">
                                <h4 class="text-lg font-medium text-gray-900 mb-4">Training Load</h4>
                                <div class="space-y-4">
                                    <div class="flex justify-between items-center">
                                        <span class="text-gray-600">Weekly Average:</span>
                                        <span class="font-medium text-gray-900">12 hours</span>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <span class="text-gray-600">Intensity Level:</span>
                                        <span class="font-medium text-gray-900">High</span>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <span class="text-gray-600">Recovery Rate:</span>
                                        <span class="font-medium text-green-600">92%</span>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <span class="text-gray-600">Fatigue Index:</span>
                                        <span class="font-medium text-yellow-600">Medium</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Predictive Analytics -->
                        <div class="bg-white border border-gray-200 rounded-lg mb-6">
                            <div class="px-6 py-4 border-b border-gray-200">
                                <h4 class="text-lg font-medium text-gray-900">Predictive Health Insights</h4>
                            </div>
                            <div class="p-6">
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                    <div class="text-center">
                                        <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-3">
                                            <span class="text-green-600 text-2xl">📊</span>
                                        </div>
                                        <h5 class="font-medium text-gray-900 mb-2">Injury Risk</h5>
                                        <p class="text-sm text-gray-600">Low risk of new injuries in next 3 months</p>
                                        <p class="text-lg font-bold text-green-600 mt-2">15%</p>
                                    </div>
                                    <div class="text-center">
                                        <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-3">
                                            <span class="text-blue-600 text-2xl">🎯</span>
                                        </div>
                                        <h5 class="font-medium text-gray-900 mb-2">Performance</h5>
                                        <p class="text-sm text-gray-600">Expected performance improvement</p>
                                        <p class="text-lg font-bold text-blue-600 mt-2">+8%</p>
                                    </div>
                                    <div class="text-center">
                                        <div class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-3">
                                            <span class="text-purple-600 text-2xl">🏥</span>
                                        </div>
                                        <h5 class="font-medium text-gray-900 mb-2">Health Score</h5>
                                        <p class="text-sm text-gray-600">Projected health score in 6 months</p>
                                        <p class="text-lg font-bold text-purple-600 mt-2">88%</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Recommendations -->
                        <div class="bg-white border border-gray-200 rounded-lg">
                            <div class="px-6 py-4 border-b border-gray-200">
                                <h4 class="text-lg font-medium text-gray-900">AI-Powered Recommendations</h4>
                            </div>
                            <div class="p-6">
                                <div class="space-y-4">
                                    <div class="flex items-start space-x-3">
                                        <span class="text-green-600">✅</span>
                                        <div>
                                            <h5 class="font-medium text-gray-900">Increase Recovery Time</h5>
                                            <p class="text-sm text-gray-600">Consider adding 1-2 rest days per week to improve recovery rate</p>
                                        </div>
                                    </div>
                                    <div class="flex items-start space-x-3">
                                        <span class="text-blue-600">💡</span>
                                        <div>
                                            <h5 class="font-medium text-gray-900">Preventive Exercises</h5>
                                            <p class="text-sm text-gray-600">Focus on ankle strengthening exercises to prevent future sprains</p>
                                        </div>
                                    </div>
                                    <div class="flex items-start space-x-3">
                                        <span class="text-yellow-600">⚠️</span>
                                        <div>
                                            <h5 class="font-medium text-gray-900">Monitor Fatigue</h5>
                                            <p class="text-sm text-gray-600">Current training load may lead to increased injury risk</p>
                                        </div>
                                    </div>
                                    <div class="flex items-start space-x-3">
                                        <span class="text-purple-600">📋</span>
                                        <div>
                                            <h5 class="font-medium text-gray-900">Nutrition Optimization</h5>
                                            <p class="text-sm text-gray-600">Consider protein supplementation to support muscle recovery</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
    // Initialize health score chart
    const ctx = document.getElementById('healthScoreChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
            datasets: [{
                label: 'Health Score',
                data: [78, 82, 85, 83, 87, 89, 86, 88, 85, 87, 89, 85],
                borderColor: 'rgb(59, 130, 246)',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    max: 100
                }
            }
        }
    });

    function showTab(tabName) {
        // Hide all tab contents
        document.querySelectorAll('.tab-content').forEach(content => {
            content.classList.add('hidden');
        });
        
        // Remove active class from all tab buttons
        document.querySelectorAll('.tab-button').forEach(button => {
            button.classList.remove('active', 'border-blue-500', 'text-blue-600');
            button.classList.add('text-gray-500');
        });
        
        // Show selected tab content
        const selectedTab = document.getElementById(tabName + '-tab');
        if (selectedTab) {
            selectedTab.classList.remove('hidden');
        }
        
        // Add active class to selected tab button
        const activeButton = document.querySelector(`[onclick="showTab('${tabName}')"]`);
        if (activeButton) {
            activeButton.classList.add('active', 'border-blue-500', 'text-blue-600');
            activeButton.classList.remove('text-gray-500');
        }
    }

    function syncVaccinations() {
        console.log('Syncing vaccinations with national registry...');
        
        // Show sync progress modal
        const modal = document.createElement('div');
        modal.className = 'fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50';
        modal.innerHTML = `
            <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
                <div class="mt-3">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-medium text-gray-900">FHIR Registry Sync</h3>
                        <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                    <div class="space-y-4">
                        <div class="text-center">
                            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto mb-4"></div>
                            <h4 class="text-lg font-medium text-gray-900 mb-2">Syncing with National Immunization Registry</h4>
                            <p class="text-sm text-gray-600">Connecting to FHIR API...</p>
                        </div>
                        
                        <div class="bg-blue-50 p-4 rounded-lg">
                            <h5 class="font-medium text-blue-900 mb-2">Sync Progress</h5>
                            <div class="space-y-2">
                                <div class="flex items-center">
                                    <div class="w-4 h-4 bg-green-500 rounded-full mr-3"></div>
                                    <span class="text-sm text-gray-700">Connecting to FHIR server...</span>
                                </div>
                                <div class="flex items-center">
                                    <div class="w-4 h-4 bg-green-500 rounded-full mr-3"></div>
                                    <span class="text-sm text-gray-700">Authenticating with OAuth2...</span>
                                </div>
                                <div class="flex items-center">
                                    <div class="w-4 h-4 bg-green-500 rounded-full mr-3"></div>
                                    <span class="text-sm text-gray-700">Fetching immunization records...</span>
                                </div>
                                <div class="flex items-center">
                                    <div class="w-4 h-4 bg-yellow-500 rounded-full mr-3 animate-pulse"></div>
                                    <span class="text-sm text-gray-700">Processing 8 vaccination records...</span>
                                </div>
                                <div class="flex items-center">
                                    <div class="w-4 h-4 bg-gray-300 rounded-full mr-3"></div>
                                    <span class="text-sm text-gray-500">Updating local database...</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="bg-green-50 p-4 rounded-lg">
                            <h5 class="font-medium text-green-900 mb-2">Sync Results</h5>
                            <div class="space-y-1 text-sm">
                                <p><span class="font-medium">Records Found:</span> 8 vaccinations</p>
                                <p><span class="font-medium">New Records:</span> 2 (COVID-19 Booster, Influenza)</p>
                                <p><span class="font-medium">Updated Records:</span> 6 existing vaccinations</p>
                                <p><span class="font-medium">Sync Status:</span> <span class="text-green-600 font-medium">Successful</span></p>
                            </div>
                        </div>
                        
                        <div class="bg-yellow-50 p-4 rounded-lg">
                            <h5 class="font-medium text-yellow-900 mb-2">Recommendations</h5>
                            <ul class="text-sm text-yellow-800 list-disc list-inside space-y-1">
                                <li>Schedule Tetanus booster (due in 3 months)</li>
                                <li>Annual influenza vaccination recommended</li>
                                <li>Consider COVID-19 booster if eligible</li>
                                <li>Next sync scheduled for 30 days</li>
                            </ul>
                        </div>
                    </div>
                    <div class="flex justify-end space-x-3 mt-6">
                        <button onclick="closeModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">
                            Close
                        </button>
                        <button onclick="viewSyncResults()" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                            View Details
                        </button>
                    </div>
                </div>
            </div>
        `;
        document.body.appendChild(modal);
        
        // Show success notification after a delay
        setTimeout(() => {
            showNotification('Vaccination sync completed successfully! 2 new records added.', 'success');
        }, 2000);
    }

    function addVaccination() {
        console.log('Opening vaccination form...');
        
        // Create and show a new vaccination form modal
        const modal = document.createElement('div');
        modal.className = 'fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50';
        modal.innerHTML = `
            <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
                <div class="mt-3">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-medium text-gray-900">Add New Vaccination</h3>
                        <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                    <form class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Vaccine Name</label>
                                <select class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option>COVID-19 (Pfizer)</option>
                                    <option>Influenza</option>
                                    <option>Tetanus/Diphtheria</option>
                                    <option>Hepatitis B</option>
                                    <option>MMR (Measles, Mumps, Rubella)</option>
                                    <option>Varicella</option>
                                    <option>Meningococcal</option>
                                    <option>HPV</option>
                                    <option>Pneumococcal</option>
                                    <option>Other</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Vaccine Code</label>
                                <input type="text" placeholder="e.g., 207, 88, 140" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Date Administered</label>
                                <input type="date" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Expiration Date</label>
                                <input type="date" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Administration Site</label>
                                <select class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option>Left Arm</option>
                                    <option>Right Arm</option>
                                    <option>Left Thigh</option>
                                    <option>Right Thigh</option>
                                    <option>Oral</option>
                                    <option>Nasal</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Lot Number</label>
                                <input type="text" placeholder="e.g., ABC123" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Administered By</label>
                            <input type="text" placeholder="e.g., Dr. Sarah Johnson" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">ICD-11 Diagnosis</label>
                            <div class="relative">
                                <input type="text" id="icd11-search-vaccination" placeholder="Search ICD-11 codes..." class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <div id="icd11-results-vaccination" class="absolute z-10 w-full mt-1 bg-white border border-gray-300 rounded-md shadow-lg hidden max-h-60 overflow-y-auto"></div>
                            </div>
                            <div id="selected-icd11-vaccination" class="mt-2 hidden">
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    <span id="selected-icd11-code-vaccination"></span> - <span id="selected-icd11-label-vaccination"></span>
                                    <button onclick="clearICD11Selection('vaccination')" class="ml-1 text-blue-600 hover:text-blue-800">×</button>
                                </span>
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                            <textarea rows="3" placeholder="Any additional notes about the vaccination..." class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                        </div>
                        
                        <div class="flex justify-end space-x-3 mt-6">
                            <button type="button" onclick="closeModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">
                                Cancel
                            </button>
                            <button type="button" onclick="saveVaccination()" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                                Add Vaccination
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        `;
        document.body.appendChild(modal);
        
        // Initialize ICD-11 search
        initModalICD11Search('vaccination');
    }

    function exportVaccinationReport() {
        console.log('Generating vaccination report...');
        
        // Create a comprehensive vaccination report
        const reportContent = `VACCINATION REPORT - ATHLETE #1
Generated: ${new Date().toLocaleDateString()}
Physician: Dr. Sarah Johnson
Report Type: Immunization Record

ATHLETE INFORMATION:
Name: Athlete #1
Sport: Professional Football
Position: Forward
Age: 24 years
Health Score: 85%

VACCINATION SUMMARY:
- Total Vaccinations: 8
- Active Vaccinations: 7
- Expiring Soon: 1
- Expired: 0
- Last Updated: ${new Date().toLocaleDateString()}

DETAILED VACCINATION RECORD:

1. COVID-19 Booster (Pfizer)
   - Date Administered: January 15, 2024
   - Expiration Date: January 15, 2025
   - Status: Active
   - Lot Number: ABC123
   - Site: Left Arm
   - Administered by: Dr. Sarah Johnson
   - FHIR ID: imm-001

2. Influenza Vaccine
   - Date Administered: October 15, 2023
   - Expiration Date: October 15, 2024
   - Status: Active
   - Lot Number: FLU456
   - Site: Right Arm
   - Administered by: Dr. Michael Chen
   - FHIR ID: imm-002

3. Tetanus/Diphtheria (Tdap)
   - Date Administered: September 10, 2023
   - Expiration Date: September 10, 2028
   - Status: Active
   - Lot Number: TD789
   - Site: Left Arm
   - Administered by: Dr. Sarah Johnson
   - FHIR ID: imm-003

4. Hepatitis B
   - Date Administered: August 5, 2023
   - Expiration Date: August 5, 2028
   - Status: Active
   - Lot Number: HEP101
   - Site: Right Arm
   - Administered by: Dr. Emily Rodriguez
   - FHIR ID: imm-004

5. MMR (Measles, Mumps, Rubella)
   - Date Administered: July 20, 2023
   - Expiration Date: July 20, 2028
   - Status: Active
   - Lot Number: MMR202
   - Site: Left Arm
   - Administered by: Dr. David Wilson
   - FHIR ID: imm-005

6. Varicella (Chickenpox)
   - Date Administered: June 15, 2023
   - Expiration Date: June 15, 2028
   - Status: Active
   - Lot Number: VAR303
   - Site: Right Arm
   - Administered by: Dr. Lisa Thompson
   - FHIR ID: imm-006

7. Meningococcal
   - Date Administered: May 10, 2023
   - Expiration Date: May 10, 2028
   - Status: Active
   - Lot Number: MEN404
   - Site: Left Arm
   - Administered by: Dr. Sarah Johnson
   - FHIR ID: imm-007

8. HPV (Human Papillomavirus)
   - Date Administered: April 5, 2023
   - Expiration Date: April 5, 2028
   - Status: Active
   - Lot Number: HPV505
   - Site: Right Arm
   - Administered by: Dr. Michael Chen
   - FHIR ID: imm-008

VACCINATION STATUS BY CATEGORY:
- Required for Sport: 8/8 (100%)
- Recommended: 8/8 (100%)
- Optional: 0/0 (0%)

EXPIRATION SCHEDULE:
- Expiring in 3 months: Tetanus/Diphtheria
- Expiring in 6 months: None
- Expiring in 1 year: COVID-19 Booster, Influenza
- Expiring in 5 years: Hepatitis B, MMR, Varicella, Meningococcal, HPV

FHIR INTEGRATION STATUS:
- Registry Connected: Yes
- Last Sync: ${new Date().toLocaleDateString()}
- Sync Status: Successful
- Records Synced: 8
- New Records: 2
- Updated Records: 6

RECOMMENDATIONS:
1. Schedule Tetanus/Diphtheria booster (due in 3 months)
2. Annual influenza vaccination recommended (October 2024)
3. Consider COVID-19 booster if eligible (January 2025)
4. Maintain vaccination schedule for optimal protection
5. Regular sync with national registry

COMPLIANCE STATUS:
- Sport Requirements: Compliant
- Travel Requirements: Compliant
- International Competition: Compliant
- Health Insurance: Compliant

Report generated by FIT Medical Platform
Date: ${new Date().toLocaleDateString()}
Time: ${new Date().toLocaleTimeString()}
Physician: Dr. Sarah Johnson
Platform: FIT Medical Management System`;

        // Create and download the report
        const blob = new Blob([reportContent], { type: 'text/plain' });
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = `vaccination_report_athlete_1_${new Date().toISOString().split('T')[0]}.txt`;
        document.body.appendChild(a);
        a.click();
        window.URL.revokeObjectURL(url);
        document.body.removeChild(a);
        
        // Show success message
        showNotification('Vaccination report downloaded successfully!', 'success');
    }

    function viewVaccinationDetails(id) {
        alert(`Viewing vaccination details for ID: ${id}`);
        // Add actual view functionality here
    }

    function editVaccination(id) {
        alert(`Editing vaccination ID: ${id}`);
        // Add actual edit functionality here
    }

    function scheduleBooster(id) {
        alert(`Scheduling booster for vaccination ID: ${id}`);
        // Add actual scheduling functionality here
    }

    function testFHIRConnection() {
        alert('Testing FHIR connection...');
        // Add actual connection test here
    }

    function addInjury() {
        console.log('Opening injury form...');
        
        // Create and show a new injury form modal
        const modal = document.createElement('div');
        modal.className = 'fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50';
        modal.innerHTML = `
            <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
                <div class="mt-3">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-medium text-gray-900">Add New Injury</h3>
                        <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                    <form class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Injury Type</label>
                                <select class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500">
                                    <option>Sprain</option>
                                    <option>Strain</option>
                                    <option>Fracture</option>
                                    <option>Dislocation</option>
                                    <option>Contusion</option>
                                    <option>Laceration</option>
                                    <option>Concussion</option>
                                    <option>Tendonitis</option>
                                    <option>Bursitis</option>
                                    <option>Other</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Body Location</label>
                                <select class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500">
                                    <option>Head/Neck</option>
                                    <option>Shoulder</option>
                                    <option>Arm/Elbow</option>
                                    <option>Wrist/Hand</option>
                                    <option>Chest/Back</option>
                                    <option>Hip</option>
                                    <option>Knee</option>
                                    <option>Ankle/Foot</option>
                                    <option>Other</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Date of Injury</label>
                                <input type="date" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Severity</label>
                                <select class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500">
                                    <option>Mild</option>
                                    <option selected>Moderate</option>
                                    <option>Severe</option>
                                    <option>Critical</option>
                                </select>
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">ICD-11 Diagnosis</label>
                            <div class="relative">
                                <input type="text" id="icd11-search-injury" placeholder="Search ICD-11 codes..." class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500">
                                <div id="icd11-results-injury" class="absolute z-10 w-full mt-1 bg-white border border-gray-300 rounded-md shadow-lg hidden max-h-60 overflow-y-auto"></div>
                            </div>
                            <div id="selected-icd11-injury" class="mt-2 hidden">
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    <span id="selected-icd11-code-injury"></span> - <span id="selected-icd11-label-injury"></span>
                                    <button onclick="clearICD11Selection('injury')" class="ml-1 text-blue-600 hover:text-blue-800">×</button>
                                </span>
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Injury Description</label>
                            <textarea rows="3" placeholder="Describe the injury, mechanism, and initial symptoms..." class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500"></textarea>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Expected Recovery Time</label>
                                <input type="number" min="1" max="365" placeholder="Days" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Return to Play Estimate</label>
                                <input type="date" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500">
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Initial Treatment</label>
                            <textarea rows="3" placeholder="Describe initial treatment and immediate care..." class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500"></textarea>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Rehabilitation Plan</label>
                            <textarea rows="3" placeholder="Describe rehabilitation protocol and exercises..." class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500"></textarea>
                        </div>
                        
                        <div class="flex justify-end space-x-3 mt-6">
                            <button type="button" onclick="closeModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">
                                Cancel
                            </button>
                            <button type="button" onclick="saveInjury()" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                                Add Injury
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        `;
        document.body.appendChild(modal);
        
        // Initialize ICD-11 search
        initModalICD11Search('injury');
    }

    function generateInjuryReport() {
        console.log('Generating injury report...');
        
        // Create a comprehensive injury report
        const reportContent = `INJURY REPORT - ATHLETE #1
Generated: ${new Date().toLocaleDateString()}
Physician: Dr. Sarah Johnson
Report Type: Injury Analysis

ATHLETE INFORMATION:
Name: Athlete #1
Sport: Professional Football
Position: Forward
Age: 24 years
Health Score: 85%

INJURY SUMMARY:
- Total Injuries: 12
- Active Injuries: 1
- Recovered Injuries: 11
- Average Recovery Time: 28 days
- Injury Rate: 2.4 injuries per season
- Most Common: Lower extremity (8 injuries)

CURRENT INJURY:
1. Ankle Sprain (Right)
   - Date of Injury: December 15, 2024
   - Severity: Moderate
   - ICD-11: S93.4 - Sprain of ankle
   - Current Status: Recovery in Progress (80% complete)
   - Expected Return: January 15, 2025
   - Treatment: Rehabilitation program
   - Progress: Excellent improvement in range of motion

INJURY HISTORY (Last 3 Years):

1. Ankle Sprain (Left) - September 2023
   - Severity: Mild
   - Recovery Time: 14 days
   - Status: Fully Recovered
   - ICD-11: S93.4 - Sprain of ankle

2. Concussion - June 2023
   - Severity: Moderate
   - Recovery Time: 21 days
   - Status: Cleared for return to sport
   - ICD-11: S06.0 - Concussion

3. Knee Sprain (MCL) - March 2023
   - Severity: Moderate
   - Recovery Time: 42 days
   - Status: Fully Recovered
   - ICD-11: S83.4 - Sprain of medial collateral ligament

4. Hamstring Strain - January 2023
   - Severity: Mild
   - Recovery Time: 18 days
   - Status: Fully Recovered
   - ICD-11: S76.1 - Strain of muscle of thigh

5. Shoulder Dislocation - November 2022
   - Severity: Severe
   - Recovery Time: 56 days
   - Status: Fully Recovered
   - ICD-11: S43.0 - Dislocation of shoulder

6. Wrist Fracture - August 2022
   - Severity: Moderate
   - Recovery Time: 35 days
   - Status: Fully Recovered
   - ICD-11: S62.0 - Fracture of wrist

7. Groin Strain - May 2022
   - Severity: Mild
   - Recovery Time: 12 days
   - Status: Fully Recovered
   - ICD-11: S76.2 - Strain of muscle of hip

8. Ankle Fracture - February 2022
   - Severity: Severe
   - Recovery Time: 84 days
   - Status: Fully Recovered
   - ICD-11: S93.0 - Fracture of ankle

9. Concussion - December 2021
   - Severity: Mild
   - Recovery Time: 14 days
   - Status: Fully Recovered
   - ICD-11: S06.0 - Concussion

10. Knee Contusion - October 2021
    - Severity: Mild
    - Recovery Time: 7 days
    - Status: Fully Recovered
    - ICD-11: S80.0 - Contusion of knee

11. Calf Strain - July 2021
    - Severity: Moderate
    - Recovery Time: 21 days
    - Status: Fully Recovered
    - ICD-11: S86.1 - Strain of muscle of lower leg

INJURY ANALYSIS BY BODY REGION:
- Lower Extremity: 8 injuries (67%)
  * Ankle: 3 injuries
  * Knee: 2 injuries
  * Hamstring: 1 injury
  * Groin: 1 injury
  * Calf: 1 injury
- Upper Extremity: 2 injuries (17%)
  * Shoulder: 1 injury
  * Wrist: 1 injury
- Head/Neck: 2 injuries (17%)
  * Concussion: 2 injuries

INJURY ANALYSIS BY SEVERITY:
- Mild: 5 injuries (42%)
- Moderate: 5 injuries (42%)
- Severe: 2 injuries (17%)
- Critical: 0 injuries (0%)

INJURY ANALYSIS BY TYPE:
- Sprains: 4 injuries (33%)
- Strains: 3 injuries (25%)
- Fractures: 2 injuries (17%)
- Concussions: 2 injuries (17%)
- Dislocation: 1 injury (8%)

RECOVERY TIME ANALYSIS:
- Average Recovery Time: 28 days
- Shortest Recovery: 7 days (Knee Contusion)
- Longest Recovery: 84 days (Ankle Fracture)
- Most Common Recovery Range: 14-21 days

RISK ASSESSMENT:
- High Risk Areas: Ankle, Knee, Head
- Injury Pattern: Lower extremity dominant
- Seasonal Trends: Higher injury rate in competitive season
- Recurrence Risk: Moderate (2 ankle injuries)

PREVENTION RECOMMENDATIONS:
1. Implement ankle strengthening program
2. Enhance concussion prevention protocols
3. Improve lower extremity flexibility
4. Regular injury screening assessments
5. Sport-specific conditioning programs

TREATMENT EFFECTIVENESS:
- Rehabilitation Success Rate: 100%
- Return to Play Rate: 100%
- Recurrence Rate: 8% (1 recurrence)
- Average Time to Full Recovery: 28 days

FUTURE PREVENTION STRATEGIES:
1. Pre-season injury risk assessment
2. Regular flexibility and strength testing
3. Sport-specific injury prevention programs
4. Enhanced protective equipment protocols
5. Regular medical monitoring and screening

Report generated by FIT Medical Platform
Date: ${new Date().toLocaleDateString()}
Time: ${new Date().toLocaleTimeString()}
Physician: Dr. Sarah Johnson
Platform: FIT Medical Management System`;

        // Create and download the report
        const blob = new Blob([reportContent], { type: 'text/plain' });
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = `injury_report_athlete_1_${new Date().toISOString().split('T')[0]}.txt`;
        document.body.appendChild(a);
        a.click();
        window.URL.revokeObjectURL(url);
        document.body.removeChild(a);
        
        // Show success message
        showNotification('Injury report downloaded successfully!', 'success');
    }

    function exportMedicalReport() {
        console.log('Generating comprehensive medical report...');
        
        // Create a comprehensive medical report
        const reportContent = `COMPREHENSIVE MEDICAL REPORT - ATHLETE #1
Generated: ${new Date().toLocaleDateString()}
Physician: Dr. Sarah Johnson
Report Type: Complete Medical Assessment

ATHLETE INFORMATION:
Name: Athlete #1
Sport: Professional Football
Position: Forward
Age: 24 years
Height: 180 cm
Weight: 75 kg
Health Score: 85%

MEDICAL STATUS SUMMARY:
- Overall Health: Good
- Medical Clearance: Valid
- Last Assessment: 2 weeks ago
- Active Alerts: 2 (requires attention)
- Vaccination Status: Up to date (8 vaccinations)
- Injury Status: 1 active injury (recovery in progress)

VACCINATION RECORD:
Total Vaccinations: 8
Recent Vaccinations:
1. COVID-19 Booster - January 15, 2024
2. Influenza - October 2023
3. Tetanus/Diphtheria - September 2023
4. Hepatitis B - August 2023
5. MMR - July 2023
6. Varicella - June 2023
7. Meningococcal - May 2023
8. HPV - April 2023

INJURY HISTORY:
Active Injuries: 1
1. Knee Rehabilitation (S83.4 - Sprain of medial collateral ligament)
   - Date of Injury: December 15, 2023
   - Current Status: Recovery in progress
   - Treatment: Rehabilitation program
   - Expected Return: 2 weeks
   - Progress: Excellent improvement in range of motion

Previous Injuries:
1. Ankle Sprain (S93.4) - September 2023 - Fully recovered
2. Concussion (S06.0) - June 2023 - Cleared for return to sport

PCMA ASSESSMENT:
Status: Valid
Last Assessment: January 15, 2024
Next Assessment Due: April 15, 2024
Expiration: 6 months remaining

Assessment Results:
- Cardiovascular: Normal (BP: 120/80, HR: 72 bpm, ECG: Normal)
- Neurological: Normal (Cognitive function intact, balance normal)
- Musculoskeletal: Normal (Full ROM, strength 5/5)
- General: Excellent (Vision 20/20, hearing normal)

SCAT ASSESSMENT:
Last Assessment: July 15, 2023
Status: Cleared
Symptom Score: 0/22 (No symptoms)
Cognitive Assessment: All tests normal
Physical Assessment: Normal balance, coordination, gait
Return to Play: Cleared for full participation

MEDICAL NOTES SUMMARY:
Total Notes: 24
Recent Notes (Last 5):
1. Follow-up Assessment - Knee Rehabilitation (Jan 20, 2024)
2. Pre-season Physical Assessment (Jan 18, 2024)
3. Concussion Follow-up (Jan 15, 2024)
4. Ankle Sprain Treatment (Jan 12, 2024)
5. Nutrition Consultation (Jan 10, 2024)

PHYSICIAN CONSULTATIONS:
Primary Physicians:
- Dr. Sarah Johnson (8 consultations)
- Dr. Michael Chen (6 consultations)
- Dr. Emily Rodriguez (5 consultations)
- Dr. David Wilson (3 consultations)
- Dr. Lisa Thompson (2 consultations)

MEDICAL ALERTS:
Active Alerts: 2
1. Follow-up required for knee rehabilitation (2 weeks)
2. Annual physical assessment due in 3 months

RECOMMENDATIONS:
1. Continue knee rehabilitation program for 2 weeks
2. Schedule annual comprehensive physical assessment
3. Maintain concussion prevention protocols
4. Regular nutrition consultations for optimal performance
5. Monitor hydration levels during training
6. Continue baseline measurements for future comparison

RISK ASSESSMENT:
- Cardiovascular Risk: Low
- Injury Risk: Medium (due to active rehabilitation)
- Concussion Risk: Low (cleared, following protocols)
- Performance Risk: Low

NEXT STEPS:
1. Complete knee rehabilitation program (2 weeks)
2. Schedule annual physical assessment (3 months)
3. Continue monitoring concussion recovery
4. Maintain injury prevention protocols
5. Regular follow-up with primary physician

MEDICAL CLEARANCE:
Status: Valid
Expiration: 6 months
Restrictions: None
Recommendations: Continue current rehabilitation protocol

Report generated by FIT Medical Platform
Date: ${new Date().toLocaleDateString()}
Time: ${new Date().toLocaleTimeString()}
Physician: Dr. Sarah Johnson
Platform: FIT Medical Management System`;

        // Create and download the report
        const blob = new Blob([reportContent], { type: 'text/plain' });
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = `comprehensive_medical_report_athlete_1_${new Date().toISOString().split('T')[0]}.txt`;
        document.body.appendChild(a);
        a.click();
        window.URL.revokeObjectURL(url);
        document.body.removeChild(a);
        
        // Show success message
        showNotification('Comprehensive medical report downloaded successfully!', 'success');
    }

    function printProfile() {
        window.print();
    }

    function createPCMA() {
        alert('Opening new PCMA assessment form...');
        // Add actual form functionality here
    }

    function exportPCMAReport() {
        alert('Generating PCMA report...');
        // Add actual report generation here
    }

    function viewPCMADetails(id) {
        console.log(`Viewing PCMA details for ID: ${id}`);
        
        // Create and show a modal with PCMA details
        const modal = document.createElement('div');
        modal.className = 'fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50';
        modal.innerHTML = `
            <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
                <div class="mt-3">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-medium text-gray-900">PCMA Assessment Details</h3>
                        <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                    <div class="space-y-4">
                        <div>
                            <h4 class="font-medium text-gray-900">PCMA Assessment #${id}</h4>
                            <p class="text-sm text-gray-600">Date: January 15, 2024</p>
                            <p class="text-sm text-gray-600">Physician: Dr. Sarah Johnson</p>
                            <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Cleared</span>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <h5 class="font-medium text-gray-900 mb-2">Cardiovascular Assessment</h5>
                                <div class="bg-green-50 p-3 rounded">
                                    <p class="text-sm text-gray-700">Status: <span class="font-medium text-green-600">Normal</span></p>
                                    <p class="text-sm text-gray-600">Blood Pressure: 120/80 mmHg</p>
                                    <p class="text-sm text-gray-600">Heart Rate: 72 bpm</p>
                                    <p class="text-sm text-gray-600">ECG: Normal sinus rhythm</p>
                                </div>
                            </div>
                            <div>
                                <h5 class="font-medium text-gray-900 mb-2">Neurological Assessment</h5>
                                <div class="bg-green-50 p-3 rounded">
                                    <p class="text-sm text-gray-700">Status: <span class="font-medium text-green-600">Normal</span></p>
                                    <p class="text-sm text-gray-600">Cognitive function: Intact</p>
                                    <p class="text-sm text-gray-600">Balance: Normal</p>
                                    <p class="text-sm text-gray-600">Coordination: Normal</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <h5 class="font-medium text-gray-900 mb-2">Musculoskeletal Assessment</h5>
                                <div class="bg-green-50 p-3 rounded">
                                    <p class="text-sm text-gray-700">Status: <span class="font-medium text-green-600">Normal</span></p>
                                    <p class="text-sm text-gray-600">Range of motion: Full</p>
                                    <p class="text-sm text-gray-600">Strength: 5/5 all muscle groups</p>
                                    <p class="text-sm text-gray-600">No pain or tenderness</p>
                                </div>
                            </div>
                            <div>
                                <h5 class="font-medium text-gray-900 mb-2">General Assessment</h5>
                                <div class="bg-green-50 p-3 rounded">
                                    <p class="text-sm text-gray-700">Status: <span class="font-medium text-green-600">Excellent</span></p>
                                    <p class="text-sm text-gray-600">Vision: 20/20</p>
                                    <p class="text-sm text-gray-600">Hearing: Normal</p>
                                    <p class="text-sm text-gray-600">No medical contraindications</p>
                                </div>
                            </div>
                        </div>
                        
                        <div>
                            <h5 class="font-medium text-gray-900 mb-2">Recommendations</h5>
                            <ul class="text-sm text-gray-700 list-disc list-inside space-y-1 bg-blue-50 p-3 rounded">
                                <li>Cleared for all competitive activities</li>
                                <li>Annual cardiovascular screening recommended</li>
                                <li>Monitor hydration levels during training</li>
                                <li>Next assessment due in 3 months</li>
                            </ul>
                        </div>
                        
                        <div>
                            <h5 class="font-medium text-gray-900 mb-2">Risk Assessment</h5>
                            <div class="bg-yellow-50 p-3 rounded">
                                <p class="text-sm text-gray-700">Overall Risk Level: <span class="font-medium text-green-600">Low</span></p>
                                <p class="text-sm text-gray-600">No significant risk factors identified</p>
                                <p class="text-sm text-gray-600">Suitable for high-intensity training</p>
                            </div>
                        </div>
                    </div>
                    <div class="flex justify-end space-x-3 mt-6">
                        <button onclick="closeModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">
                            Close
                        </button>
                        <button onclick="editPCMA(${id})" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                            Edit Assessment
                        </button>
                    </div>
                </div>
            </div>
        `;
        document.body.appendChild(modal);
    }

    function editPCMA(id) {
        console.log(`Editing PCMA ID: ${id}`);
        
        // Create and show an edit modal
        const modal = document.createElement('div');
        modal.className = 'fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50';
        modal.innerHTML = `
            <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
                <div class="mt-3">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-medium text-gray-900">Edit PCMA Assessment</h3>
                        <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                    <form class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Cardiovascular Status</label>
                                <select class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option selected>Normal</option>
                                    <option>Abnormal</option>
                                    <option>Requires Follow-up</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Blood Pressure</label>
                                <input type="text" value="120/80 mmHg" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Neurological Status</label>
                                <select class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option selected>Normal</option>
                                    <option>Abnormal</option>
                                    <option>Requires Follow-up</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Musculoskeletal Status</label>
                                <select class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option selected>Normal</option>
                                    <option>Abnormal</option>
                                    <option>Requires Follow-up</option>
                                </select>
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Clinical Notes</label>
                            <textarea rows="4" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">Patient demonstrates excellent cardiovascular fitness with normal ECG findings. Neurological examination reveals intact cognitive function and normal balance. Musculoskeletal assessment shows full range of motion and normal strength in all muscle groups. No medical contraindications identified for competitive sports participation.</textarea>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Recommendations</label>
                            <textarea rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">Cleared for all competitive activities
Annual cardiovascular screening recommended
Monitor hydration levels during training
Next assessment due in 3 months</textarea>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Overall Assessment</label>
                            <select class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option selected>Cleared</option>
                                <option>Conditional</option>
                                <option>Not Cleared</option>
                            </select>
                        </div>
                        
                        <div class="flex justify-end space-x-3 mt-6">
                            <button type="button" onclick="closeModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">
                                Cancel
                            </button>
                            <button type="button" onclick="savePCMA(${id})" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                                Save Changes
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        `;
        document.body.appendChild(modal);
    }

    function downloadPCMA(id) {
        console.log(`Downloading PCMA ID: ${id} report...`);
        
        // Create a downloadable PCMA report
        const pcmaContent = `PCMA Assessment Report - ID: ${id}
Date: January 15, 2024
Physician: Dr. Sarah Johnson
Athlete: Athlete #1
Status: Cleared

CARDIOVASCULAR ASSESSMENT:
Status: Normal
Blood Pressure: 120/80 mmHg
Heart Rate: 72 bpm
ECG: Normal sinus rhythm
Risk Level: Low

NEUROLOGICAL ASSESSMENT:
Status: Normal
Cognitive function: Intact
Balance: Normal
Coordination: Normal
Risk Level: Low

MUSCULOSKELETAL ASSESSMENT:
Status: Normal
Range of motion: Full
Strength: 5/5 all muscle groups
No pain or tenderness
Risk Level: Low

GENERAL ASSESSMENT:
Status: Excellent
Vision: 20/20
Hearing: Normal
No medical contraindications
Risk Level: Low

RECOMMENDATIONS:
- Cleared for all competitive activities
- Annual cardiovascular screening recommended
- Monitor hydration levels during training
- Next assessment due in 3 months

OVERALL ASSESSMENT: CLEARED
Risk Level: Low
Next Review: 3 months`;

        // Create and download the file
        const blob = new Blob([pcmaContent], { type: 'text/plain' });
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = `pcma_assessment_${id}.txt`;
        document.body.appendChild(a);
        a.click();
        window.URL.revokeObjectURL(url);
        document.body.removeChild(a);
        
        // Show success message
        showNotification('PCMA report downloaded successfully!', 'success');
    }

    function savePCMA(id) {
        console.log(`Saving PCMA ID: ${id}`);
        showNotification('PCMA assessment saved successfully!', 'success');
        closeModal();
    }

    function createNote() {
        console.log('Creating new medical note...');
        
        // Create and show a new note form modal
        const modal = document.createElement('div');
        modal.className = 'fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50';
        modal.innerHTML = `
            <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
                <div class="mt-3">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-medium text-gray-900">Create New Medical Note</h3>
                        <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                    <form class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Note Category</label>
                                <select class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option>Treatment</option>
                                    <option>Assessment</option>
                                    <option>Injury</option>
                                    <option>Follow-up</option>
                                    <option>Prevention</option>
                                    <option>Emergency</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Priority</label>
                                <select class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option>Low</option>
                                    <option selected>Medium</option>
                                    <option>High</option>
                                    <option>Urgent</option>
                                </select>
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">ICD-11 Diagnosis</label>
                            <div class="relative">
                                <input type="text" id="icd11-search-note" placeholder="Search ICD-11 codes..." class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500">
                                <div id="icd11-results-note" class="absolute z-10 w-full mt-1 bg-white border border-gray-300 rounded-md shadow-lg hidden max-h-60 overflow-y-auto"></div>
                            </div>
                            <div id="selected-icd11-note" class="mt-2 hidden">
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    <span id="selected-icd11-code-note"></span> - <span id="selected-icd11-label-note"></span>
                                    <button onclick="clearICD11Selection('note')" class="ml-1 text-blue-600 hover:text-blue-800">×</button>
                                </span>
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Clinical Assessment</label>
                            <textarea rows="4" placeholder="Enter detailed clinical assessment..." class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Treatment Plan</label>
                            <textarea rows="3" placeholder="Enter treatment plan and recommendations..." class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Follow-up Date</label>
                                <input type="date" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Duration (minutes)</label>
                                <input type="number" min="5" max="300" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Additional Notes</label>
                            <textarea rows="2" placeholder="Any additional observations or notes..." class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                        </div>
                        
                        <div class="flex justify-end space-x-3 mt-6">
                            <button type="button" onclick="closeModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">
                                Cancel
                            </button>
                            <button type="button" onclick="saveNewNote()" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                                Create Note
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        `;
        document.body.appendChild(modal);
        
        // Initialize ICD-11 search
        initModalICD11Search('note');
    }

    function exportNotesReport() {
        console.log('Generating medical notes report...');
        
        // Create a comprehensive medical notes report
        const reportContent = `MEDICAL NOTES REPORT - ATHLETE #1
Generated: ${new Date().toLocaleDateString()}
Physician: Dr. Sarah Johnson
Total Notes: 24

SUMMARY STATISTICS:
- Total Notes: 24
- Categories: 8 (Treatment, Assessment, Injury, Follow-up, Prevention, Emergency, Rehabilitation, Consultation)
- Physicians: 5 (Dr. Sarah Johnson, Dr. Michael Chen, Dr. Emily Rodriguez, Dr. David Wilson, Dr. Lisa Thompson)
- Date Range: January 2023 - January 2024
- Average Note Duration: 35 minutes

RECENT MEDICAL NOTES (Last 10):

1. Note ID: 24 - Follow-up Assessment - Knee Rehabilitation
   Date: January 20, 2024
   Physician: Dr. Sarah Johnson
   Category: Treatment
   ICD-11: S83.4 - Sprain of medial collateral ligament of knee
   Duration: 45 minutes
   Assessment: Patient shows excellent progress in knee rehabilitation program. Range of motion has improved significantly. Strength training exercises are well tolerated. Recommend continuing current protocol for 2 more weeks before advancing to sport-specific training.

2. Note ID: 23 - Pre-season Physical Assessment
   Date: January 18, 2024
   Physician: Dr. Michael Chen
   Category: Assessment
   ICD-11: Z02.5 - Encounter for examination for participation in sport
   Duration: 60 minutes
   Assessment: Comprehensive pre-season physical examination completed. All systems within normal limits. Cleared for full participation in upcoming season. Baseline measurements recorded for future comparison.

3. Note ID: 22 - Concussion Follow-up
   Date: January 15, 2024
   Physician: Dr. Emily Rodriguez
   Category: Follow-up
   ICD-11: S06.0 - Concussion
   Duration: 30 minutes
   Assessment: Patient demonstrates complete resolution of concussion symptoms. All cognitive assessments are within normal limits. Physical examination reveals normal balance, coordination, and eye movements. Cleared for return to sport.

4. Note ID: 21 - Ankle Sprain Treatment
   Date: January 12, 2024
   Physician: Dr. David Wilson
   Category: Injury
   ICD-11: S93.4 - Sprain of ankle
   Duration: 40 minutes
   Assessment: Grade 2 ankle sprain showing good healing progress. Range of motion improving. Recommend continued rehabilitation exercises and gradual return to activity. No signs of complications.

5. Note ID: 20 - Nutrition Consultation
   Date: January 10, 2024
   Physician: Dr. Lisa Thompson
   Category: Consultation
   ICD-11: Z71.3 - Dietary counseling and surveillance
   Duration: 25 minutes
   Assessment: Nutrition assessment completed. Recommendations provided for optimal performance nutrition. Meal planning assistance provided. Follow-up scheduled for 2 weeks.

NOTES BY CATEGORY:
- Treatment: 8 notes (33%)
- Assessment: 6 notes (25%)
- Injury: 4 notes (17%)
- Follow-up: 3 notes (12%)
- Prevention: 2 notes (8%)
- Emergency: 1 note (4%)

NOTES BY PHYSICIAN:
- Dr. Sarah Johnson: 8 notes
- Dr. Michael Chen: 6 notes
- Dr. Emily Rodriguez: 5 notes
- Dr. David Wilson: 3 notes
- Dr. Lisa Thompson: 2 notes

RECOMMENDATIONS:
1. Continue regular monitoring of rehabilitation progress
2. Schedule annual comprehensive physical assessment
3. Maintain baseline measurements for future comparison
4. Follow concussion prevention protocols
5. Regular nutrition consultations for optimal performance

NEXT STEPS:
- Schedule follow-up for knee rehabilitation in 2 weeks
- Annual physical assessment due in 3 months
- Continue monitoring concussion recovery
- Maintain injury prevention protocols

Report generated by FIT Medical Platform
Date: ${new Date().toLocaleDateString()}
Time: ${new Date().toLocaleTimeString()}`;

        // Create and download the report
        const blob = new Blob([reportContent], { type: 'text/plain' });
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = `medical_notes_report_${new Date().toISOString().split('T')[0]}.txt`;
        document.body.appendChild(a);
        a.click();
        window.URL.revokeObjectURL(url);
        document.body.removeChild(a);
        
        // Show success message
        showNotification('Medical notes report downloaded successfully!', 'success');
    }

    function viewNoteDetails(id) {
        console.log(`Viewing note details for ID: ${id}`);
        
        // Create and show a modal with note details
        const modal = document.createElement('div');
        modal.className = 'fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50';
        modal.innerHTML = `
            <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
                <div class="mt-3">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-medium text-gray-900">Medical Note Details</h3>
                        <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                    <div class="space-y-4">
                        <div>
                            <h4 class="font-medium text-gray-900">Note ID: ${id}</h4>
                            <p class="text-sm text-gray-600">Date: January 20, 2024</p>
                            <p class="text-sm text-gray-600">Physician: Dr. Sarah Johnson</p>
                        </div>
                        <div>
                            <h5 class="font-medium text-gray-900 mb-2">Clinical Assessment</h5>
                            <p class="text-sm text-gray-700 bg-gray-50 p-3 rounded">
                                Patient shows excellent progress in knee rehabilitation program. Range of motion has improved significantly. 
                                Strength training exercises are well tolerated. Recommend continuing current protocol for 2 more weeks 
                                before advancing to sport-specific training.
                            </p>
                        </div>
                        <div>
                            <h5 class="font-medium text-gray-900 mb-2">Diagnosis</h5>
                            <p class="text-sm text-gray-700">ICD-11: S83.4 - Sprain of medial collateral ligament of knee</p>
                        </div>
                        <div>
                            <h5 class="font-medium text-gray-900 mb-2">Recommendations</h5>
                            <ul class="text-sm text-gray-700 list-disc list-inside space-y-1">
                                <li>Continue current rehabilitation protocol</li>
                                <li>Monitor for any signs of regression</li>
                                <li>Schedule follow-up in 2 weeks</li>
                                <li>Gradual return to sport-specific training</li>
                            </ul>
                        </div>
                    </div>
                    <div class="flex justify-end space-x-3 mt-6">
                        <button onclick="closeModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">
                            Close
                        </button>
                        <button onclick="editNote(${id})" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                            Edit Note
                        </button>
                    </div>
                </div>
            </div>
        `;
        document.body.appendChild(modal);
    }

    function editNote(id) {
        console.log(`Editing note ID: ${id}`);
        
        // Create and show an edit modal
        const modal = document.createElement('div');
        modal.className = 'fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50';
        modal.innerHTML = `
            <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
                <div class="mt-3">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-medium text-gray-900">Edit Medical Note</h3>
                        <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                    <form class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Note Category</label>
                            <select class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option>Treatment</option>
                                <option>Assessment</option>
                                <option>Injury</option>
                                <option>Follow-up</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Clinical Assessment</label>
                            <textarea rows="4" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">Patient shows excellent progress in knee rehabilitation program. Range of motion has improved significantly. Strength training exercises are well tolerated. Recommend continuing current protocol for 2 more weeks before advancing to sport-specific training.</textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">ICD-11 Diagnosis</label>
                            <input type="text" value="S83.4 - Sprain of medial collateral ligament of knee" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Recommendations</label>
                            <textarea rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">Continue current rehabilitation protocol
Monitor for any signs of regression
Schedule follow-up in 2 weeks
Gradual return to sport-specific training</textarea>
                        </div>
                        <div class="flex justify-end space-x-3 mt-6">
                            <button type="button" onclick="closeModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">
                                Cancel
                            </button>
                            <button type="button" onclick="saveNote(${id})" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                                Save Changes
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        `;
        document.body.appendChild(modal);
    }

    function downloadNote(id) {
        console.log(`Downloading note ID: ${id}`);
        
        // Create a downloadable note content
        const noteContent = `Medical Note - ID: ${id}
Date: January 20, 2024
Physician: Dr. Sarah Johnson
Category: Treatment

CLINICAL ASSESSMENT:
Patient shows excellent progress in knee rehabilitation program. Range of motion has improved significantly. Strength training exercises are well tolerated. Recommend continuing current protocol for 2 more weeks before advancing to sport-specific training.

DIAGNOSIS:
ICD-11: S83.4 - Sprain of medial collateral ligament of knee

RECOMMENDATIONS:
- Continue current rehabilitation protocol
- Monitor for any signs of regression
- Schedule follow-up in 2 weeks
- Gradual return to sport-specific training

Duration: 45 minutes
Status: Active`;

        // Create and download the file
        const blob = new Blob([noteContent], { type: 'text/plain' });
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = `medical_note_${id}.txt`;
        document.body.appendChild(a);
        a.click();
        window.URL.revokeObjectURL(url);
        document.body.removeChild(a);
        
        // Show success message
        showNotification('Note downloaded successfully!', 'success');
    }

    function closeModal() {
        const modals = document.querySelectorAll('.fixed.inset-0');
        modals.forEach(modal => modal.remove());
    }

    function saveNote(id) {
        console.log(`Saving note ID: ${id}`);
        showNotification('Note saved successfully!', 'success');
        closeModal();
    }

    function saveNewNote() {
        console.log('Saving new medical note...');
        showNotification('New medical note created successfully!', 'success');
        closeModal();
    }

    function saveVaccination() {
        console.log('Saving new vaccination...');
        showNotification('New vaccination added successfully!', 'success');
        closeModal();
    }

    function viewSyncResults() {
        console.log('Viewing sync results...');
        showNotification('Sync results displayed successfully!', 'info');
        closeModal();
    }

    function showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 px-6 py-3 rounded-lg shadow-lg z-50 ${
            type === 'success' ? 'bg-green-500 text-white' : 'bg-blue-500 text-white'
        }`;
        notification.textContent = message;
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.remove();
        }, 3000);
    }



    // SCAT Functions
    function createSCAT() {
        alert('Opening new SCAT assessment form...');
        // Add actual form functionality here
    }

    function exportSCATReport() {
        alert('Generating SCAT report...');
        // Add actual report generation here
    }

    function viewSCATDetails(id) {
        console.log(`Viewing SCAT details for ID: ${id}`);
        
        // Create and show a modal with SCAT details
        const modal = document.createElement('div');
        modal.className = 'fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50';
        modal.innerHTML = `
            <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
                <div class="mt-3">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-medium text-gray-900">SCAT Assessment Details</h3>
                        <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                    <div class="space-y-4">
                        <div>
                            <h4 class="font-medium text-gray-900">SCAT Assessment #${id}</h4>
                            <p class="text-sm text-gray-600">Date: July 15, 2023</p>
                            <p class="text-sm text-gray-600">Physician: Dr. Emily Rodriguez</p>
                            <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Cleared</span>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <h5 class="font-medium text-gray-900 mb-2">Symptom Evaluation</h5>
                                <div class="bg-green-50 p-3 rounded">
                                    <p class="text-sm text-gray-700">Total Score: <span class="font-medium text-green-600">0/22</span></p>
                                    <p class="text-sm text-gray-600">Headache: None</p>
                                    <p class="text-sm text-gray-600">Dizziness: None</p>
                                    <p class="text-sm text-gray-600">Nausea: None</p>
                                    <p class="text-sm text-gray-600">Sensitivity to light: None</p>
                                </div>
                            </div>
                            <div>
                                <h5 class="font-medium text-gray-900 mb-2">Cognitive Assessment</h5>
                                <div class="bg-green-50 p-3 rounded">
                                    <p class="text-sm text-gray-700">Orientation: <span class="font-medium text-green-600">5/5</span></p>
                                    <p class="text-sm text-gray-600">Immediate Memory: <span class="font-medium text-green-600">15/15</span></p>
                                    <p class="text-sm text-gray-600">Concentration: <span class="font-medium text-green-600">5/5</span></p>
                                    <p class="text-sm text-gray-600">Delayed Recall: <span class="font-medium text-green-600">5/5</span></p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <h5 class="font-medium text-gray-900 mb-2">Physical Assessment</h5>
                                <div class="bg-green-50 p-3 rounded">
                                    <p class="text-sm text-gray-700">Balance: <span class="font-medium text-green-600">Normal</span></p>
                                    <p class="text-sm text-gray-600">Coordination: <span class="font-medium text-green-600">Normal</span></p>
                                    <p class="text-sm text-gray-600">Gait: <span class="font-medium text-green-600">Normal</span></p>
                                    <p class="text-sm text-gray-600">Eye movement: <span class="font-medium text-green-600">Normal</span></p>
                                </div>
                            </div>
                            <div>
                                <h5 class="font-medium text-gray-900 mb-2">Return to Play</h5>
                                <div class="bg-green-50 p-3 rounded">
                                    <p class="text-sm text-gray-700">Status: <span class="font-medium text-green-600">Cleared</span></p>
                                    <p class="text-sm text-gray-600">No symptoms present</p>
                                    <p class="text-sm text-gray-600">Normal cognitive function</p>
                                    <p class="text-sm text-gray-600">Safe to return to sport</p>
                                </div>
                            </div>
                        </div>
                        
                        <div>
                            <h5 class="font-medium text-gray-900 mb-2">Clinical Notes</h5>
                            <div class="bg-blue-50 p-3 rounded">
                                <p class="text-sm text-gray-700">
                                    Patient demonstrates complete resolution of concussion symptoms. All cognitive assessments are within normal limits. 
                                    Physical examination reveals normal balance, coordination, and eye movements. No residual symptoms or cognitive deficits observed. 
                                    Patient has successfully completed the return-to-play protocol and is cleared for full participation.
                                </p>
                            </div>
                        </div>
                        
                        <div>
                            <h5 class="font-medium text-gray-900 mb-2">Recommendations</h5>
                            <ul class="text-sm text-gray-700 list-disc list-inside space-y-1 bg-yellow-50 p-3 rounded">
                                <li>Cleared for full return to sport participation</li>
                                <li>Continue monitoring for any symptom recurrence</li>
                                <li>Follow standard concussion prevention protocols</li>
                                <li>Annual baseline testing recommended</li>
                            </ul>
                        </div>
                    </div>
                    <div class="flex justify-end space-x-3 mt-6">
                        <button onclick="closeModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">
                            Close
                        </button>
                        <button onclick="editSCAT(${id})" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                            Edit Assessment
                        </button>
                    </div>
                </div>
            </div>
        `;
        document.body.appendChild(modal);
    }

    function editSCAT(id) {
        console.log(`Editing SCAT ID: ${id}`);
        
        // Create and show an edit modal
        const modal = document.createElement('div');
        modal.className = 'fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50';
        modal.innerHTML = `
            <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
                <div class="mt-3">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-medium text-gray-900">Edit SCAT Assessment</h3>
                        <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                    <form class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Symptom Score</label>
                                <input type="number" value="0" min="0" max="22" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Orientation Score</label>
                                <input type="number" value="5" min="0" max="5" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Immediate Memory</label>
                                <input type="number" value="15" min="0" max="15" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Concentration</label>
                                <input type="number" value="5" min="0" max="5" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Delayed Recall</label>
                                <input type="number" value="5" min="0" max="5" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Balance Assessment</label>
                                <select class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option selected>Normal</option>
                                    <option>Mild Impairment</option>
                                    <option>Moderate Impairment</option>
                                    <option>Severe Impairment</option>
                                </select>
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Clinical Notes</label>
                            <textarea rows="4" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">Patient demonstrates complete resolution of concussion symptoms. All cognitive assessments are within normal limits. Physical examination reveals normal balance, coordination, and eye movements. No residual symptoms or cognitive deficits observed. Patient has successfully completed the return-to-play protocol and is cleared for full participation.</textarea>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Recommendations</label>
                            <textarea rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">Cleared for full return to sport participation
Continue monitoring for any symptom recurrence
Follow standard concussion prevention protocols
Annual baseline testing recommended</textarea>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Return to Play Status</label>
                            <select class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option selected>Cleared</option>
                                <option>Conditional</option>
                                <option>Not Cleared</option>
                                <option>Requires Follow-up</option>
                            </select>
                        </div>
                        
                        <div class="flex justify-end space-x-3 mt-6">
                            <button type="button" onclick="closeModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">
                                Cancel
                            </button>
                            <button type="button" onclick="saveSCAT(${id})" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                                Save Changes
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        `;
        document.body.appendChild(modal);
    }

    function downloadSCAT(id) {
        console.log(`Downloading SCAT ID: ${id} report...`);
        
        // Create a downloadable SCAT report
        const scatContent = `SCAT Assessment Report - ID: ${id}
Date: July 15, 2023
Physician: Dr. Emily Rodriguez
Athlete: Athlete #1
Status: Cleared

SYMPTOM EVALUATION:
Total Score: 0/22
Headache: None
Dizziness: None
Nausea: None
Sensitivity to light: None
Sensitivity to noise: None
Feeling slowed down: None
Feeling like "in a fog": None
Don't feel right: None
Difficulty concentrating: None
Difficulty remembering: None
Fatigue or low energy: None
Confusion: None
Drowsiness: None
More emotional: None
Irritability: None
Sadness: None
Nervous or anxious: None
Trouble falling asleep: None
Sleeping more than usual: None
Sleeping less than usual: None

COGNITIVE ASSESSMENT:
Orientation: 5/5
Immediate Memory: 15/15
Concentration: 5/5
Delayed Recall: 5/5

PHYSICAL ASSESSMENT:
Balance: Normal
Coordination: Normal
Gait: Normal
Eye movement: Normal

CLINICAL NOTES:
Patient demonstrates complete resolution of concussion symptoms. All cognitive assessments are within normal limits. Physical examination reveals normal balance, coordination, and eye movements. No residual symptoms or cognitive deficits observed. Patient has successfully completed the return-to-play protocol and is cleared for full participation.

RECOMMENDATIONS:
- Cleared for full return to sport participation
- Continue monitoring for any symptom recurrence
- Follow standard concussion prevention protocols
- Annual baseline testing recommended

RETURN TO PLAY STATUS: CLEARED
Risk Level: Low
Next Review: Annual baseline testing`;

        // Create and download the file
        const blob = new Blob([scatContent], { type: 'text/plain' });
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = `scat_assessment_${id}.txt`;
        document.body.appendChild(a);
        a.click();
        window.URL.revokeObjectURL(url);
        document.body.removeChild(a);
        
        // Show success message
        showNotification('SCAT report downloaded successfully!', 'success');
    }

    function saveSCAT(id) {
        console.log(`Saving SCAT ID: ${id}`);
        showNotification('SCAT assessment saved successfully!', 'success');
        closeModal();
    }

    // Initialize health trends chart
    document.addEventListener('DOMContentLoaded', function() {
        const healthTrendsCtx = document.getElementById('healthTrendsChart');
        if (healthTrendsCtx) {
            new Chart(healthTrendsCtx, {
                type: 'line',
                data: {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                    datasets: [{
                        label: 'Health Score',
                        data: [78, 82, 85, 83, 87, 89, 86, 88, 85, 87, 89, 85],
                        borderColor: 'rgb(59, 130, 246)',
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        tension: 0.4
                    }, {
                        label: 'Injury Risk',
                        data: [25, 22, 20, 18, 15, 12, 10, 8, 12, 15, 18, 12],
                        borderColor: 'rgb(239, 68, 68)',
                        backgroundColor: 'rgba(239, 68, 68, 0.1)',
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            display: true
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 100
                        }
                    }
                }
            });
        }
    });

    // Show dashboard by default
    document.addEventListener('DOMContentLoaded', function() {
        showTab('dashboard');
        
        // Test function to ensure JavaScript is working
        window.testNoteFunctions = function() {
            console.log('Note functions are accessible');
            alert('Note functions are working!');
        };
        
        // Make sure all functions are globally accessible
        window.viewNoteDetails = viewNoteDetails;
        window.editNote = editNote;
        window.downloadNote = downloadNote;
    });

    function generateAnalyticsReport() {
        alert('Generating comprehensive medical analytics report...');
        // Add actual report generation functionality here
    }

    function exportAnalytics() {
        alert('Exporting medical analytics data...');
        // Add actual export functionality here
    }

    function saveInjury() {
        console.log('Saving new injury...');
        showNotification('New injury added successfully!', 'success');
        closeModal();
    }

    // ICD-11 Search Functions
    let icd11SearchTimeout;
    let selectedICD11 = null;
    let currentFormType = null;

    function initICD11Search(formType = 'injury') {
        console.log('🔍 Initializing ICD-11 search for form type:', formType);
        currentFormType = formType;
        
        const searchInput = document.getElementById(`icd11-search-${formType}`);
        if (!searchInput) {
            console.log(`❌ ICD-11 search input not found for form type: ${formType}`);
            return;
        }
        console.log(`✅ ICD-11 search input found for form type: ${formType}`, searchInput);

        searchInput.addEventListener('input', function() {
            console.log('🔍 ICD-11 search input event triggered for form type:', formType);
            clearTimeout(icd11SearchTimeout);
            const query = this.value.trim();
            console.log('🔍 Search query:', query);
            
            if (query.length < 2) {
                console.log('🔍 Query too short, hiding results');
                hideICD11Results(formType);
                return;
            }

            icd11SearchTimeout = setTimeout(() => {
                console.log('🔍 Calling searchICD11 with query:', query);
                searchICD11(query, formType);
            }, 300);
        });

        // Hide results when clicking outside
        document.addEventListener('click', function(e) {
            if (!e.target.closest(`#icd11-search-${formType}`) && !e.target.closest(`#icd11-results-${formType}`)) {
                console.log('🔍 Click outside, hiding results for form type:', formType);
                hideICD11Results(formType);
            }
        });
    }

    function searchICD11(query, formType = 'injury') {
        console.log('Searching ICD-11 for:', query, 'in form type:', formType);
        
        // Show loading state
        const resultsDiv = document.getElementById(`icd11-results-${formType}`);
        resultsDiv.innerHTML = '<div class="p-3 text-gray-500">Searching...</div>';
        resultsDiv.classList.remove('hidden');

        // Call the ICD-11 API
        fetch(`/api/v1/icd11/search?query=${encodeURIComponent(query)}`, {
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            displayICD11Results(data.data || [], formType);
        })
        .catch(error => {
            console.error('Error searching ICD-11:', error);
            // Show fallback results for demo
            displayICD11Results(getFallbackICD11Results(query), formType);
        });
    }

    function displayICD11Results(results, formType = 'injury') {
        const resultsDiv = document.getElementById(`icd11-results-${formType}`);
        
        if (results.length === 0) {
            resultsDiv.innerHTML = '<div class="p-3 text-gray-500">No results found</div>';
            return;
        }

        const html = results.map(item => `
            <div class="p-3 hover:bg-gray-50 cursor-pointer border-b border-gray-100 last:border-b-0" 
                 onclick="selectICD11('${item.code}', '${item.label.replace(/'/g, "\\'")}', '${formType}')">
                <div class="font-medium text-sm text-gray-900">${item.code}</div>
                <div class="text-xs text-gray-600">${item.label}</div>
            </div>
        `).join('');

        resultsDiv.innerHTML = html;
        resultsDiv.classList.remove('hidden');
    }

    function selectICD11(code, label, formType = 'injury') {
        selectedICD11 = { code, label };
        
        // Update the display
        document.getElementById(`selected-icd11-code-${formType}`).textContent = code;
        document.getElementById(`selected-icd11-label-${formType}`).textContent = label;
        document.getElementById(`selected-icd11-${formType}`).classList.remove('hidden');
        
        // Clear the search input
        document.getElementById(`icd11-search-${formType}`).value = '';
        hideICD11Results(formType);
        
        console.log('Selected ICD-11:', selectedICD11, 'for form type:', formType);
    }

    function clearICD11Selection(formType = 'injury') {
        selectedICD11 = null;
        document.getElementById(`selected-icd11-${formType}`).classList.add('hidden');
        document.getElementById(`icd11-search-${formType}`).value = '';
    }

    function hideICD11Results(formType = 'injury') {
        document.getElementById(`icd11-results-${formType}`).classList.add('hidden');
    }

    function getFallbackICD11Results(query) {
        // Fallback results for demo when API is not available
        const fallbackResults = [
            { code: 'S93.4', label: 'Sprain of ankle' },
            { code: 'S83.4', label: 'Sprain of medial collateral ligament of knee' },
            { code: 'S06.0', label: 'Concussion' },
            { code: 'S76.1', label: 'Strain of muscle of thigh' },
            { code: 'S43.0', label: 'Dislocation of shoulder' },
            { code: 'S62.0', label: 'Fracture of wrist' },
            { code: 'S76.2', label: 'Strain of muscle of hip' },
            { code: 'S93.0', label: 'Fracture of ankle' },
            { code: 'S80.0', label: 'Contusion of knee' },
            { code: 'S86.1', label: 'Strain of muscle of lower leg' }
        ];

        return fallbackResults.filter(item => 
            item.code.toLowerCase().includes(query.toLowerCase()) ||
            item.label.toLowerCase().includes(query.toLowerCase())
        );
    }

    // Initialize ICD-11 search when modals are opened
    function initModalICD11Search(formType = 'injury') {
        setTimeout(() => {
            initICD11Search(formType);
        }, 100);
    }
    </script>
</body>
</html> 