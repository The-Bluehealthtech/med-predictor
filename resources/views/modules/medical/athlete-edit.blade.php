@extends('layouts.app')

@section('title', 'Edit Athlete Medical Profile - Med Predictor')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-6xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">
                        ✏️ Edit Athlete Medical Profile
                        @if($player)
                            - {{ $player->full_name }}
                            @if(isset($isDemo) && $isDemo)
                                <span class="text-sm bg-yellow-100 text-yellow-800 px-2 py-1 rounded ml-2">Demo Mode</span>
                            @endif
                        @endif
                    </h1>
                    <p class="text-gray-600 mt-2">Update medical information and assessments</p>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('modules.medical.athlete', $player->id ?? 1) }}" 
                       class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg flex items-center space-x-2 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        <span>Back to Profile</span>
                    </a>
                    <a href="{{ route('modules.medical.index') }}" 
                       class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center space-x-2 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                        </svg>
                        <span>Medical Dashboard</span>
                    </a>
                </div>
            </div>
        </div>

        @if($player)
            <!-- Edit Form -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden mb-8">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-xl font-semibold text-gray-800">Edit Medical Information</h2>
                </div>
                <div class="p-6">
                    <form method="POST" action="{{ route('modules.medical.athlete.update', $player->id) }}" class="space-y-6">
                        @csrf
                        @method('PUT')
                        
                        <!-- Personal Information -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">First Name</label>
                                <input type="text" name="first_name" value="{{ $player->first_name ?? '' }}" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Last Name</label>
                                <input type="text" name="last_name" value="{{ $player->last_name ?? '' }}" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Date of Birth</label>
                                <input type="date" name="date_of_birth" value="{{ $player->date_of_birth ?? '' }}" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Position</label>
                                <select name="position" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="">Select Position</option>
                                    <option value="GK" {{ ($player->position ?? '') == 'GK' ? 'selected' : '' }}>Goalkeeper (GK)</option>
                                    <option value="DF" {{ ($player->position ?? '') == 'DF' ? 'selected' : '' }}>Defender (DF)</option>
                                    <option value="MF" {{ ($player->position ?? '') == 'MF' ? 'selected' : '' }}>Midfielder (MF)</option>
                                    <option value="FW" {{ ($player->position ?? '') == 'FW' ? 'selected' : '' }}>Forward (FW)</option>
                                    <option value="ST" {{ ($player->position ?? '') == 'ST' ? 'selected' : '' }}>Striker (ST)</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Nationality</label>
                                <input type="text" name="nationality" value="{{ $player->nationality ?? '' }}" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                        </div>
                        
                        <!-- Physical Information -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Height (cm)</label>
                                <input type="number" name="height" value="{{ $player->height ?? '' }}" min="100" max="250"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Weight (kg)</label>
                                <input type="number" name="weight" value="{{ $player->weight ?? '' }}" min="30" max="150" step="0.1"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                        </div>
                        
                        <!-- Medical Status -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Medical Status</label>
                                <select name="medical_status" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="active" {{ ($player->medical_status ?? 'active') == 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="pending" {{ ($player->medical_status ?? 'active') == 'pending' ? 'selected' : '' }}>Pending Assessment</option>
                                    <option value="suspended" {{ ($player->medical_status ?? 'active') == 'suspended' ? 'selected' : '' }}>Medically Suspended</option>
                                    <option value="injured" {{ ($player->medical_status ?? 'active') == 'injured' ? 'selected' : '' }}>Injured</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Last Medical Check</label>
                                <input type="date" name="last_medical_check" value="{{ $player->last_medical_check ?? '' }}" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                        </div>
                        
                        <!-- Notes -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Medical Notes</label>
                            <textarea name="medical_notes" rows="4" 
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                      placeholder="Enter any medical notes or observations...">{{ $player->medical_notes ?? '' }}</textarea>
                        </div>
                        
                        <!-- Submit Buttons -->
                        <div class="flex justify-end space-x-4 pt-6 border-t border-gray-200">
                            <a href="{{ route('modules.medical.athlete', $player->id) }}" 
                               class="px-6 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 transition-colors">
                                Cancel
                            </a>
                            <button type="submit" 
                                    class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                                Update Profile
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Quick Actions -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden mb-8">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-xl font-semibold text-gray-800">Quick Actions</h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <a href="{{ route('health-records.create') }}?player_id={{ $player->id }}" 
                           class="bg-green-600 hover:bg-green-700 text-white p-4 rounded-lg text-center transition-colors">
                            <div class="text-2xl mb-2">📋</div>
                            <div class="font-medium">New Medical Record</div>
                        </a>
                        <a href="{{ route('medical-predictions.create') }}?player_id={{ $player->id }}" 
                           class="bg-blue-600 hover:bg-blue-700 text-white p-4 rounded-lg text-center transition-colors">
                            <div class="text-2xl mb-2">🔮</div>
                            <div class="font-medium">Medical Prediction</div>
                        </a>
                        <a href="{{ route('health-records.index') }}?player_id={{ $player->id }}" 
                           class="bg-purple-600 hover:bg-purple-700 text-white p-4 rounded-lg text-center transition-colors">
                            <div class="text-2xl mb-2">📊</div>
                            <div class="font-medium">View All Records</div>
                        </a>
                    </div>
                </div>
            </div>
        @else
            <!-- No Player Found -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="p-8 text-center">
                    <div class="text-gray-400 mb-4">
                        <svg class="w-16 h-16 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Player Not Found</h3>
                    <p class="text-gray-600 mb-4">The requested player could not be found in the system.</p>
                    <a href="{{ route('modules.medical.index') }}" 
                       class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors">
                        Back to Medical Dashboard
                    </a>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection




