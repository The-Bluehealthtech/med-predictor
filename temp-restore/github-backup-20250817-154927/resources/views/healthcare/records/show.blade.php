@extends('layouts.app')

@section('title', 'Healthcare Record Details')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">📋 Détails du Dossier Médical</h1>
                    <p class="text-gray-600 mt-2">Informations complètes sur le dossier médical</p>
                </div>
                <div class="flex space-x-4">
                    <a href="{{ route('healthcare.index') }}" 
                       class="bg-gray-600 hover:bg-gray-700 text-white font-semibold py-2 px-4 rounded-lg transition-colors">
                        ← Retour
                    </a>
                    <a href="{{ route('healthcare.records.edit', ['record' => $healthRecord->id]) }}" 
                       class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg transition-colors">
                        ✏️ Modifier
                    </a>
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
            <div class="px-4 py-6 sm:px-0">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <h2 class="text-lg font-semibold mb-4">Healthcare Record Details</h2>
                        
                        @if($healthRecord)
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Basic Information -->
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <h3 class="text-md font-semibold mb-3 text-gray-800">Basic Information</h3>
                                    <div class="space-y-2">
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Record ID:</span>
                                            <span class="font-medium">{{ $healthRecord->id }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Status:</span>
                                            <span class="font-medium">{{ ucfirst($healthRecord->status ?? 'N/A') }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Record Date:</span>
                                            <span class="font-medium">{{ $healthRecord->record_date ? $healthRecord->record_date->format('M d, Y') : 'N/A' }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Next Checkup:</span>
                                            <span class="font-medium">{{ $healthRecord->next_checkup_date ? $healthRecord->next_checkup_date->format('M d, Y') : 'N/A' }}</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Player Information -->
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <h3 class="text-md font-semibold mb-3 text-gray-800">Player Information</h3>
                                    <div class="space-y-2">
                                        @if($healthRecord->player)
                                            <div class="flex justify-between">
                                                <span class="text-gray-600">Player:</span>
                                                <span class="font-medium">
                                                    {{ $healthRecord->player ? $healthRecord->player->first_name . ' ' . $healthRecord->player->last_name : 'N/A' }}
                                                </span>
                                            </div>
                                            <div class="flex justify-between">
                                                <span class="text-gray-600">Club:</span>
                                                <span class="font-medium">
                                                    {{ $healthRecord->player && $healthRecord->player->club ? $healthRecord->player->club->name : 'N/A' }}
                                                </span>
                                            </div>
                                        @else
                                            <p class="text-gray-500">No player associated</p>
                                        @endif
                                    </div>
                                </div>

                                <!-- Vital Signs -->
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <h3 class="text-md font-semibold mb-3 text-gray-800">Vital Signs</h3>
                                    <div class="space-y-2">
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Blood Pressure:</span>
                                            <span class="font-medium">{{ $healthRecord->blood_pressure_systolic ?? 'N/A' }}/{{ $healthRecord->blood_pressure_diastolic ?? 'N/A' }} mmHg</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Heart Rate:</span>
                                            <span class="font-medium">{{ $healthRecord->heart_rate ?? 'N/A' }} bpm</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Temperature:</span>
                                            <span class="font-medium">{{ $healthRecord->temperature ?? 'N/A' }}°C</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Weight:</span>
                                            <span class="font-medium">{{ $healthRecord->weight ?? 'N/A' }} kg</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Height:</span>
                                            <span class="font-medium">{{ $healthRecord->height ?? 'N/A' }} cm</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">BMI:</span>
                                            <span class="font-medium">{{ $healthRecord->bmi ?? 'N/A' }}</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Medical Information -->
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <h3 class="text-md font-semibold mb-3 text-gray-800">Medical Information</h3>
                                    <div class="space-y-2">
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Blood Type:</span>
                                            <span class="font-medium">{{ $healthRecord->blood_type ?? 'N/A' }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Risk Score:</span>
                                            <span class="font-medium">{{ $healthRecord->risk_score ?? 'N/A' }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Prediction Confidence:</span>
                                            <span class="font-medium">{{ $healthRecord->prediction_confidence ?? 'N/A' }}%</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Additional Details -->
                            <div class="mt-6 space-y-4">
                                @if(!empty($healthRecord->allergies))
                                    <div class="bg-yellow-50 p-4 rounded-lg">
                                        <h3 class="text-md font-semibold mb-2 text-yellow-800">Allergies</h3>
                                        <p class="text-yellow-700">
                                            @if(is_array($healthRecord->allergies))
                                                {{ implode(', ', $healthRecord->allergies) }}
                                            @elseif(is_string($healthRecord->allergies))
                                                {{ $healthRecord->allergies }}
                                            @else
                                                N/A
                                            @endif
                                        </p>
                                    </div>
                                @endif

                                @if(!empty($healthRecord->medications))
                                    <div class="bg-blue-50 p-4 rounded-lg">
                                        <h3 class="text-md font-semibold mb-2 text-blue-800">Medications</h3>
                                        <p class="text-blue-700">
                                            @if(is_array($healthRecord->medications))
                                                {{ implode(', ', $healthRecord->medications) }}
                                            @elseif(is_string($healthRecord->medications))
                                                {{ $healthRecord->medications }}
                                            @else
                                                N/A
                                            @endif
                                        </p>
                                    </div>
                                @endif

                                @if(!empty($healthRecord->medical_history))
                                    <div class="bg-green-50 p-4 rounded-lg">
                                        <h3 class="text-md font-semibold mb-2 text-green-800">Medical History</h3>
                                        <p class="text-green-700">
                                            @if(is_array($healthRecord->medical_history))
                                                {{ implode(', ', $healthRecord->medical_history) }}
                                            @elseif(is_string($healthRecord->medical_history))
                                                {{ $healthRecord->medical_history }}
                                            @else
                                                N/A
                                            @endif
                                        </p>
                                    </div>
                                @endif

                                @if(!empty($healthRecord->symptoms))
                                    <div class="bg-red-50 p-4 rounded-lg">
                                        <h3 class="text-md font-semibold mb-2 text-red-800">Symptoms</h3>
                                        <p class="text-red-700">
                                            @if(is_array($healthRecord->symptoms))
                                                {{ implode(', ', $healthRecord->symptoms) }}
                                            @elseif(is_string($healthRecord->symptoms))
                                                {{ $healthRecord->symptoms }}
                                            @else
                                                N/A
                                            @endif
                                        </p>
                                    </div>
                                @endif

                                @if($healthRecord->diagnosis)
                                    <div class="bg-purple-50 p-4 rounded-lg">
                                        <h3 class="text-md font-semibold mb-2 text-purple-800">Diagnosis</h3>
                                        <p class="text-purple-700">{{ $healthRecord->diagnosis }}</p>
                                    </div>
                                @endif

                                @if($healthRecord->treatment_plan)
                                    <div class="bg-indigo-50 p-4 rounded-lg">
                                        <h3 class="text-md font-semibold mb-2 text-indigo-800">Treatment Plan</h3>
                                        <p class="text-indigo-700">{{ $healthRecord->treatment_plan }}</p>
                                    </div>
                                @endif
                            </div>
                        @else
                            <div class="text-center py-8">
                                <p class="text-gray-500 text-lg">Dossier médical non trouvé</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection 