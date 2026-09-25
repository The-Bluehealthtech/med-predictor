@extends('layouts.app')

@section('title', 'Nouveau Rendez-vous - Med Predictor')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">📅 Nouveau Rendez-vous</h1>
                    <p class="text-gray-600 mt-2">Planifier un nouveau rendez-vous médical</p>
                </div>
                <a href="{{ route('appointments.index') }}" 
                   class="bg-gray-600 hover:bg-gray-700 text-white font-semibold py-2 px-4 rounded-lg transition-colors">
                    ← Retour
                </a>
            </div>
        </div>

        <!-- Form -->
        <div class="bg-white rounded-lg shadow p-6">
            <form action="{{ route('appointments.store') }}" method="POST">
                @csrf
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Athlete -->
                    <div>
                        <label for="athlete_id" class="block text-sm font-medium text-gray-700 mb-2">
                            Patient *
                        </label>
                        <select name="athlete_id" id="athlete_id" required 
                                class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Sélectionner un patient</option>
                            @foreach($athletes as $athlete)
                                <option value="{{ $athlete->id }}" {{ old('athlete_id') == $athlete->id ? 'selected' : '' }}>
                                    {{ $athlete->name }} ({{ $athlete->fifa_connect_id }})
                                </option>
                            @endforeach
                        </select>
                        @error('athlete_id')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Doctor -->
                    <div>
                        <label for="doctor_id" class="block text-sm font-medium text-gray-700 mb-2">
                            Médecin *
                        </label>
                        <select name="doctor_id" id="doctor_id" required 
                                class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Sélectionner un médecin</option>
                            @foreach($doctors as $doctor)
                                <option value="{{ $doctor->id }}" {{ old('doctor_id') == $doctor->id ? 'selected' : '' }}>
                                    {{ $doctor->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('doctor_id')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Date -->
                    <div>
                        <label for="appointment_date" class="block text-sm font-medium text-gray-700 mb-2">
                            Date et Heure *
                        </label>
                        <input type="datetime-local" name="appointment_date" id="appointment_date" required
                               value="{{ old('appointment_date') }}"
                               class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @error('appointment_date')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Duration -->
                    <div>
                        <label for="duration_minutes" class="block text-sm font-medium text-gray-700 mb-2">
                            Durée (minutes) *
                        </label>
                        <select name="duration_minutes" id="duration_minutes" required 
                                class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="15" {{ old('duration_minutes') == 15 ? 'selected' : '' }}>15 minutes</option>
                            <option value="30" {{ old('duration_minutes') == 30 ? 'selected' : '' }}>30 minutes</option>
                            <option value="45" {{ old('duration_minutes') == 45 ? 'selected' : '' }}>45 minutes</option>
                            <option value="60" {{ old('duration_minutes') == 60 ? 'selected' : '' }}>1 heure</option>
                            <option value="90" {{ old('duration_minutes') == 90 ? 'selected' : '' }}>1h30</option>
                            <option value="120" {{ old('duration_minutes') == 120 ? 'selected' : '' }}>2 heures</option>
                        </select>
                        @error('duration_minutes')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Type -->
                    <div class="md:col-span-2">
                        <label for="appointment_type" class="block text-sm font-medium text-gray-700 mb-2">
                            Type de Rendez-vous *
                        </label>
                        <select name="appointment_type" id="appointment_type" required 
                                class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Sélectionner un type</option>
                            <option value="consultation" {{ old('appointment_type') == 'consultation' ? 'selected' : '' }}>Consultation générale</option>
                            <option value="emergency" {{ old('appointment_type') == 'emergency' ? 'selected' : '' }}>Urgence</option>
                            <option value="follow_up" {{ old('appointment_type') == 'follow_up' ? 'selected' : '' }}>Suivi</option>
                            <option value="pre_season" {{ old('appointment_type') == 'pre_season' ? 'selected' : '' }}>Bilan pré-saison</option>
                            <option value="post_match" {{ old('appointment_type') == 'post_match' ? 'selected' : '' }}>Bilan post-match</option>
                            <option value="rehabilitation" {{ old('appointment_type') == 'rehabilitation' ? 'selected' : '' }}>Rééducation</option>
                            <option value="routine_checkup" {{ old('appointment_type') == 'routine_checkup' ? 'selected' : '' }}>Contrôle de routine</option>
                            <option value="injury_assessment" {{ old('appointment_type') == 'injury_assessment' ? 'selected' : '' }}>Évaluation de blessure</option>
                            <option value="cardiac_evaluation" {{ old('appointment_type') == 'cardiac_evaluation' ? 'selected' : '' }}>Évaluation cardiaque</option>
                            <option value="concussion_assessment" {{ old('appointment_type') == 'concussion_assessment' ? 'selected' : '' }}>Évaluation commotion</option>
                        </select>
                        @error('appointment_type')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Reason -->
                    <div class="md:col-span-2">
                        <label for="reason" class="block text-sm font-medium text-gray-700 mb-2">
                            Motif
                        </label>
                        <textarea name="reason" id="reason" rows="3" 
                                  class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                  placeholder="Décrivez le motif de la consultation...">{{ old('reason') }}</textarea>
                        @error('reason')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Notes -->
                    <div class="md:col-span-2">
                        <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">
                            Notes
                        </label>
                        <textarea name="notes" id="notes" rows="3" 
                                  class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                  placeholder="Notes additionnelles...">{{ old('notes') }}</textarea>
                        @error('notes')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="mt-8 flex justify-end space-x-4">
                    <a href="{{ route('appointments.index') }}" 
                       class="bg-gray-600 hover:bg-gray-700 text-white font-semibold py-2 px-6 rounded-lg transition-colors">
                        Annuler
                    </a>
                    <button type="submit" 
                            class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-6 rounded-lg transition-colors">
                        Créer le Rendez-vous
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Set default datetime to current time + 1 hour
document.addEventListener('DOMContentLoaded', function() {
    const now = new Date();
    now.setHours(now.getHours() + 1);
    now.setMinutes(0);
    now.setSeconds(0);
    now.setMilliseconds(0);
    
    const datetimeLocal = now.toISOString().slice(0, 16);
    document.getElementById('appointment_date').value = datetimeLocal;
});
</script>
@endsection 