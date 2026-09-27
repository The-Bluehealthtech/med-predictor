@extends('layouts.app')

@section('title', __('medical_predictions.create_page_title'))

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">🔮 {{ __('medical_predictions.create_h1') }}</h1>
                    <p class="text-gray-600 mt-2">{{ __('medical_predictions.create_subtitle') }}</p>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('dashboard') }}" 

                       class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-md transition-colors">
                        <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                        </svg>
                        Back to Dashboard
                    </a>
                    <a href="{{ route('medical-predictions.index') }}" 

                       class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg flex items-center space-x-2 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        <span>{{ __('medical_predictions.shared_back') }}</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Form -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-xl font-semibold text-gray-800">{{ __('medical_predictions.create_form_header') }}</h2>
            </div>
            
            <form action="{{ route('medical-predictions.store') }}" method="POST" class="p-6">
                @csrf
                
                <!-- Player Selection -->
                <div class="mb-6">
                    <label for="player_id" class="block text-sm font-medium text-gray-700 mb-2">
                        {{ __('medical_predictions.shared_player_label') }} <span class="text-red-500">*</span>
                    </label>
                    <select name="player_id" id="player_id" required 

                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">{{ __('medical_predictions.create_select_player') }}</option>
                        @foreach($players as $player)
                            <option value="{{ $player->id }}" {{ $selectedPlayer && $selectedPlayer->id == $player->id ? 'selected' : '' }}>
                                {{ $player->full_name }} - {{ $player->position }} ({{ $player->club->name ?? 'N/A' }})
                            </option>
                        @endforeach
                    </select>
                    @error('player_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Prediction Type -->
                <div class="mb-6">
                    <label for="prediction_type" class="block text-sm font-medium text-gray-700 mb-2">
                        {{ __('medical_predictions.shared_prediction_type_label') }} <span class="text-red-500">*</span>
                    </label>
                    <select name="prediction_type" id="prediction_type" required 

                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">{{ __('medical_predictions.create_select_type') }}</option>
                        @foreach($predictionTypes as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('prediction_type')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Health Record Selection -->
                <div class="mb-6">
                    <label for="health_record_id" class="block text-sm font-medium text-gray-700 mb-2">
                        {{ __('medical_predictions.create_health_record_label') }}
                    </label>
                    <select name="health_record_id" id="health_record_id" 

                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">{{ __('medical_predictions.create_use_most_recent_record') }}</option>
                    </select>
                    <p class="mt-1 text-sm text-gray-500">{{ __('medical_predictions.create_health_record_help') }}</p>
                    @error('health_record_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Manual Factors -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        {{ __('medical_predictions.create_manual_factors_label') }}
                    </label>
                    <div class="space-y-2">
                        <div class="flex items-center space-x-2">
                            <input type="text" name="manual_factors[]" placeholder="{{ __('medical_predictions.create_factor_1_placeholder') }}"

                                   class="flex-1 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <button type="button" onclick="addFactor()" 

                                    class="bg-green-600 hover:bg-green-700 text-white px-3 py-2 rounded-md transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                            </button>
                        </div>
                        <div id="additional-factors" class="space-y-2"></div>
                    </div>
                    <p class="mt-1 text-sm text-gray-500">{{ __('medical_predictions.create_manual_factors_help') }}</p>
                </div>

                <!-- Notes -->
                <div class="mb-6">
                    <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">
                        {{ __('medical_predictions.create_notes_label') }}
                    </label>
                    <textarea name="notes" id="notes" rows="4" 
                              placeholder="{{ __('medical_predictions.create_notes_placeholder') }}"

                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                    @error('notes')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Prediction Type Information -->
                <div class="mb-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <h3 class="text-sm font-medium text-blue-900 mb-2">{{ __('medical_predictions.create_types_info_header') }}</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-blue-800">
                        <div>
                            <strong>{{ __('medical_predictions.create_type_injury_risk_label') }}</strong> {{ __('medical_predictions.create_type_injury_risk_desc') }}
                        </div>
                        <div>
                            <strong>{{ __('medical_predictions.create_type_performance_label') }}</strong> {{ __('medical_predictions.create_type_performance_desc') }}
                        </div>
                        <div>
                            <strong>{{ __('medical_predictions.create_type_health_label') }}</strong> {{ __('medical_predictions.create_type_health_desc') }}
                        </div>
                        <div>
                            <strong>{{ __('medical_predictions.create_type_recovery_label') }}</strong> {{ __('medical_predictions.create_type_recovery_desc') }}
                        </div>
                        <div class="md:col-span-2">
                            <strong>{{ __('medical_predictions.create_type_fitness_label') }}</strong> {{ __('medical_predictions.create_type_fitness_desc') }}
                        </div>
                    </div>
                </div>

                <!-- Submit Buttons -->
                <div class="flex justify-end space-x-3">
                    <a href="{{ route('medical-predictions.index') }}" 

                       class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-2 rounded-lg transition-colors">
                        {{ __('medical_predictions.shared_cancel') }}
                    </a>
                    <button type="submit" 

                            class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg flex items-center space-x-2 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                        <span>{{ __('medical_predictions.create_submit_button') }}</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- AI Information -->
        <div class="mt-6 bg-gray-50 border border-gray-200 rounded-lg p-4">
            <h3 class="text-sm font-medium text-gray-900 mb-2">🤖 {{ __('medical_predictions.create_ai_header') }}</h3>
            <p class="text-sm text-gray-600 mb-3">
                {{ __('medical_predictions.create_ai_intro') }}
            </p>
            <ul class="text-sm text-gray-600 space-y-1">
                <li>• {{ __('medical_predictions.create_ai_bullet_demographics') }}</li>
                <li>• {{ __('medical_predictions.create_ai_bullet_medical_history') }}</li>
                <li>• {{ __('medical_predictions.create_ai_bullet_fifa_stats') }}</li>
                <li>• {{ __('medical_predictions.create_ai_bullet_risk_factors') }}</li>
                <li>• {{ __('medical_predictions.create_ai_bullet_injury_patterns') }}</li>
            </ul>
            <p class="text-sm text-gray-600 mt-3">
                <strong>{{ __('medical_predictions.create_ai_note_label') }}</strong> {{ __('medical_predictions.create_ai_note_text') }}
            </p>
        </div>
    </div>
</div>

<script>
function addFactor() {
    const container = document.getElementById('additional-factors');
    const factorDiv = document.createElement('div');
    factorDiv.className = 'flex items-center space-x-2';
    factorDiv.innerHTML = `
        <input type="text" name="manual_factors[]" placeholder="{{ __('medical_predictions.create_additional_factor_placeholder') }}"
               class="flex-1 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
        <button type="button" onclick="removeFactor(this)" 

                class="bg-red-600 hover:bg-red-700 text-white px-3 py-2 rounded-md transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
    `;
    container.appendChild(factorDiv);
}

function removeFactor(button) {
    button.parentElement.remove();
}

// Load health records when player is selected
document.getElementById('player_id').addEventListener('change', function() {
    const playerId = this.value;
    const healthRecordSelect = document.getElementById('health_record_id');
    
    if (playerId) {
        // Clear existing options except the first one
        healthRecordSelect.innerHTML = '<option value="">{{ __('medical_predictions.create_use_most_recent_record') }}</option>';
        
        // Fetch health records for the selected player
        fetch(`/api/players/${playerId}/health-records`)
            .then(response => response.json())
            .then(data => {
                data.forEach(record => {
                    const option = document.createElement('option');
                    option.value = record.id;
                    option.textContent = `{{ __('medical_predictions.create_record_from_prefix') }} ${record.record_date} - ${record.status}`;
                    healthRecordSelect.appendChild(option);
                });
            })
            .catch(error => {
                console.error('Error loading health records:', error);
            });
    }
});
</script>
@endsection 