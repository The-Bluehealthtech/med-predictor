<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Report Created Successfully - FIT Platform</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-lg shadow-lg mb-6">
                <div class="px-6 py-8 text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <h1 class="text-3xl font-bold">Rapport Soumis avec Succès!</h1>
                            <p class="text-green-100 mt-2">
                                {{ $report->match->homeTeam->name ?? 'TBD' }} vs {{ $report->match->awayTeam->name ?? 'TBD' }}
                            </p>
                            <p class="text-green-100 text-sm">
                                {{ $report->competition_name ?? 'Competition' }} • {{ $report->match_date ?? 'N/A' }}
                            </p>
                        </div>
                        <div class="text-right">
                            <div class="bg-white bg-opacity-20 rounded-lg p-4">
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Report Summary -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Report Summary</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h3 class="font-medium text-gray-900 mb-2">Match Details</h3>
                            <div class="space-y-2">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Score Final:</span>
                                    <span class="font-medium">{{ $report->final_score ?? 'Non spécifié' }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Score Mi-temps:</span>
                                    <span class="font-medium">{{ $report->half_time_score ?? 'Non spécifié' }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Note du Match:</span>
                                    <span class="font-medium">{{ $report->match_rating ?? 'Non spécifié' }}/10</span>
                                </div>
                            </div>
                        </div>
                        <div>
                            <h3 class="font-medium text-gray-900 mb-2">Incidents</h3>
                            <div class="space-y-2">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Cartons Jaunes:</span>
                                    <span class="font-medium">{{ is_array($report->yellow_cards) ? count($report->yellow_cards) : '0' }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Cartons Rouges:</span>
                                    <span class="font-medium">{{ is_array($report->red_cards) ? count($report->red_cards) : '0' }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Buts:</span>
                                    <span class="font-medium">{{ is_array($report->goals) ? count($report->goals) : '0' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    @if($report->general_comments)
                        <div class="mt-6">
                            <h3 class="font-medium text-gray-900 mb-2">Observations</h3>
                            <div class="bg-gray-50 rounded-lg p-4">
                                <p class="text-gray-700">{{ $report->general_comments }}</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Actions -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Next Steps</h2>
                    <div class="space-y-4">
                        <div class="flex items-center p-4 bg-blue-50 rounded-lg">
                            <svg class="w-5 h-5 text-blue-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            <div>
                                <h3 class="font-medium text-blue-900">Report Submitted</h3>
                                <p class="text-blue-700 text-sm">Your match report has been successfully submitted and will be reviewed by the competition committee.</p>
                            </div>
                        </div>
                        
                        <div class="flex items-center p-4 bg-green-50 rounded-lg">
                            <svg class="w-5 h-5 text-green-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                            </svg>
                            <div>
                                <h3 class="font-medium text-green-900">Performance Tracking</h3>
                                <p class="text-green-700 text-sm">This report will be included in your performance statistics and evaluation.</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-6 flex justify-end space-x-4">
                        <a href="/referee-create-report-working" 
                           class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500">
                            Create Another Report
                        </a>
                        <a href="/referee-dashboard-working" 
                           class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-orange-600 hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500">
                            Back to Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Report Created Successfully - FIT Platform</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-lg shadow-lg mb-6">
                <div class="px-6 py-8 text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <h1 class="text-3xl font-bold">Rapport Soumis avec Succès!</h1>
                            <p class="text-green-100 mt-2">
                                {{ $report->match->homeTeam->name ?? 'TBD' }} vs {{ $report->match->awayTeam->name ?? 'TBD' }}
                            </p>
                            <p class="text-green-100 text-sm">
                                {{ $report->competition_name ?? 'Competition' }} • {{ $report->match_date ?? 'N/A' }}
                            </p>
                        </div>
                        <div class="text-right">
                            <div class="bg-white bg-opacity-20 rounded-lg p-4">
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Report Summary -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Report Summary</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h3 class="font-medium text-gray-900 mb-2">Match Details</h3>
                            <div class="space-y-2">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Score Final:</span>
                                    <span class="font-medium">{{ $report->final_score ?? 'Non spécifié' }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Score Mi-temps:</span>
                                    <span class="font-medium">{{ $report->half_time_score ?? 'Non spécifié' }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Note du Match:</span>
                                    <span class="font-medium">{{ $report->match_rating ?? 'Non spécifié' }}/10</span>
                                </div>
                            </div>
                        </div>
                        <div>
                            <h3 class="font-medium text-gray-900 mb-2">Incidents</h3>
                            <div class="space-y-2">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Cartons Jaunes:</span>
                                    <span class="font-medium">{{ is_array($report->yellow_cards) ? count($report->yellow_cards) : '0' }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Cartons Rouges:</span>
                                    <span class="font-medium">{{ is_array($report->red_cards) ? count($report->red_cards) : '0' }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Buts:</span>
                                    <span class="font-medium">{{ is_array($report->goals) ? count($report->goals) : '0' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    @if($report->general_comments)
                        <div class="mt-6">
                            <h3 class="font-medium text-gray-900 mb-2">Observations</h3>
                            <div class="bg-gray-50 rounded-lg p-4">
                                <p class="text-gray-700">{{ $report->general_comments }}</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Actions -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Next Steps</h2>
                    <div class="space-y-4">
                        <div class="flex items-center p-4 bg-blue-50 rounded-lg">
                            <svg class="w-5 h-5 text-blue-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            <div>
                                <h3 class="font-medium text-blue-900">Report Submitted</h3>
                                <p class="text-blue-700 text-sm">Your match report has been successfully submitted and will be reviewed by the competition committee.</p>
                            </div>
                        </div>
                        
                        <div class="flex items-center p-4 bg-green-50 rounded-lg">
                            <svg class="w-5 h-5 text-green-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                            </svg>
                            <div>
                                <h3 class="font-medium text-green-900">Performance Tracking</h3>
                                <p class="text-green-700 text-sm">This report will be included in your performance statistics and evaluation.</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-6 flex justify-end space-x-4">
                        <a href="/referee-create-report-working" 
                           class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500">
                            Create Another Report
                        </a>
                        <a href="/referee-dashboard-working" 
                           class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-orange-600 hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500">
                            Back to Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>


