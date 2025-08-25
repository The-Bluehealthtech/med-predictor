@extends('layouts.app')

@section('title', 'Créer une Visite - Med Predictor')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">🏥 Créer une Nouvelle Visite</h1>
                    <p class="text-gray-600 mt-2">Enregistrer une nouvelle visite médicale</p>
                </div>
                <div class="flex space-x-4">
                    <a href="{{ route('visits.index') }}" 
                       class="bg-gray-600 hover:bg-gray-700 text-white font-semibold py-2 px-4 rounded-lg transition-colors">
                        ← Retour
                    </a>
                </div>
            </div>
        </div>

        <!-- Form -->
        <div class="bg-white rounded-lg shadow-lg p-8">
            <form action="{{ route('visits.store') }}" method="POST">
                @csrf
                
                <!-- Appointment Selection -->
                <div class="mb-8">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">📅 Sélection du Rendez-vous</h2>
                    
                    @if(request('appointment_id'))
                        @php
                            $selectedAppointment = $appointments->where('id', request('appointment_id'))->first();
                        @endphp
                        @if($selectedAppointment)
                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <h3 class="font-semibold text-blue-900">Rendez-vous sélectionné</h3>
                                        <p class="text-blue-700">
                                            {{ $selectedAppointment->athlete->name }} - 
                                            {{ $selectedAppointment->appointment_date->format('d/m/Y H:i') }} - 
                                            {{ $selectedAppointment->doctor->name ?? 'N/A' }}
                                        </p>
                                    </div>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        {{ $selectedAppointment->status }}
                                    </span>
                                </div>
                            </div>
                            <input type="hidden" name="appointment_id" value="{{ $selectedAppointment->id }}">
                            <input type="hidden" name="athlete_id" value="{{ $selectedAppointment->athlete_id }}">
                            <input type="hidden" name="doctor_id" value="{{ $selectedAppointment->doctor_id }}">
                        @endif
                    @else
                        <div class="mb-4">
                            <label for="appointment_id" class="block text-sm font-medium text-gray-700 mb-2">
                                Rendez-vous (optionnel)
                            </label>
                            <select name="appointment_id" id="appointment_id" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">Aucun rendez-vous</option>
                                @foreach($appointments as $appointment)
                                    <option value="{{ $appointment->id }}" 
                                            data-athlete="{{ $appointment->athlete_id }}"
                                            data-doctor="{{ $appointment->doctor_id }}">
                                        {{ $appointment->athlete->name }} - 
                                        {{ $appointment->appointment_date->format('d/m/Y H:i') }} - 
                                        {{ $appointment->doctor->name ?? 'N/A' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @endif
                </div>

                <!-- Patient Information -->
                <div class="mb-8">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">👤 Informations du Patient</h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="athlete_id" class="block text-sm font-medium text-gray-700 mb-2">
                                Patient <span class="text-red-500">*</span>
                            </label>
                            <select name="athlete_id" id="athlete_id" required 
                                    class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    @if(request('appointment_id') && $selectedAppointment) disabled @endif>
                                <option value="">Sélectionner un patient</option>
                                @foreach($athletes as $athlete)
                                    <option value="{{ $athlete->id }}" 
                                            @if(request('appointment_id') && $selectedAppointment && $selectedAppointment->athlete_id == $athlete->id) selected @endif>
                                        {{ $athlete->name }} ({{ $athlete->nationality }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="doctor_id" class="block text-sm font-medium text-gray-700 mb-2">
                                Médecin
                            </label>
                            <select name="doctor_id" id="doctor_id" 
                                    class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    @if(request('appointment_id') && $selectedAppointment) disabled @endif>
                                <option value="">Sélectionner un médecin</option>
                                @foreach($doctors as $doctor)
                                    <option value="{{ $doctor->id }}"
                                            @if(request('appointment_id') && $selectedAppointment && $selectedAppointment->doctor_id == $doctor->id) selected @endif>
                                        {{ $doctor->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Visit Details -->
                <div class="mb-8">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">📋 Détails de la Visite</h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="visit_date" class="block text-sm font-medium text-gray-700 mb-2">
                                Date et Heure <span class="text-red-500">*</span>
                            </label>
                            <input type="datetime-local" name="visit_date" id="visit_date" required
                                   value="{{ now()->format('Y-m-d\TH:i') }}"
                                   class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>

                        <div>
                            <label for="visit_type" class="block text-sm font-medium text-gray-700 mb-2">
                                Type de Visite <span class="text-red-500">*</span>
                            </label>
                            <select name="visit_type" id="visit_type" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">Sélectionner le type de visite</option>
                                <option value="consultation">Consultation</option>
                                <option value="emergency">Urgence</option>
                                <option value="follow_up">Suivi</option>
                                <option value="pre_season">Pré-saison</option>
                                <option value="pcma">PCMA (Évaluation Capacité Physique)</option>
                                <option value="post_match">Post-match</option>
                                <option value="rehabilitation">Rééducation</option>
                            </select>
                        </div>
                    </div>

                    <div class="mt-6">
                        <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">
                            Notes
                        </label>
                        <textarea name="notes" id="notes" rows="4"
                                  class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                  placeholder="Notes additionnelles sur la visite..."></textarea>
                    </div>
                </div>

                <!-- Administrative Data -->
                <div class="mb-8">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">📄 Données Administratives</h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="administrative_data[contact_name]" class="block text-sm font-medium text-gray-700 mb-2">
                                Contact d'urgence
                            </label>
                            <input type="text" name="administrative_data[contact_name]" 
                                   class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                   placeholder="Nom du contact">
                        </div>

                        <div>
                            <label for="administrative_data[contact_phone]" class="block text-sm font-medium text-gray-700 mb-2">
                                Téléphone d'urgence
                            </label>
                            <input type="tel" name="administrative_data[contact_phone]" 
                                   class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                   placeholder="+33 1 23 45 67 89">
                        </div>

                        <div>
                            <label for="administrative_data[insurance_provider]" class="block text-sm font-medium text-gray-700 mb-2">
                                Assurance
                            </label>
                            <input type="text" name="administrative_data[insurance_provider]" 
                                   class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                   placeholder="Nom de l'assurance">
                        </div>

                        <div>
                            <label for="administrative_data[policy_number]" class="block text-sm font-medium text-gray-700 mb-2">
                                Numéro de police
                            </label>
                            <input type="text" name="administrative_data[policy_number]" 
                                   class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                   placeholder="Numéro de police d'assurance">
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="flex justify-end space-x-4 pt-6 border-t border-gray-200">
                    <a href="{{ route('visits.index') }}" 
                       class="bg-gray-600 hover:bg-gray-700 text-white font-semibold py-2 px-6 rounded-lg transition-colors">
                        Annuler
                    </a>
                    <button type="submit" 
                            class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-6 rounded-lg transition-colors">
                        🏥 Créer la Visite
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-fill athlete and doctor when appointment is selected
    const appointmentSelect = document.getElementById('appointment_id');
    const athleteSelect = document.getElementById('athlete_id');
    const doctorSelect = document.getElementById('doctor_id');

    if (appointmentSelect && athleteSelect && doctorSelect) {
        appointmentSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            if (selectedOption.value) {
                const athleteId = selectedOption.dataset.athlete;
                const doctorId = selectedOption.dataset.doctor;
                
                if (athleteId) {
                    athleteSelect.value = athleteId;
                }
                if (doctorId) {
                    doctorSelect.value = doctorId;
                }
            } else {
                athleteSelect.value = '';
                doctorSelect.value = '';
            }
        });
    }
});
</script>
@endsection 