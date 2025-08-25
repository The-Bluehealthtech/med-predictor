@extends('layouts.app')

@section('title', 'Détails du Rendez-vous - Med Predictor')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">📅 Détails du Rendez-vous</h1>
                    <p class="text-gray-600 mt-2">Informations complètes sur le rendez-vous</p>
                </div>
                <div class="flex space-x-4">
                    <a href="{{ route('appointments.index') }}" 
                       class="bg-gray-600 hover:bg-gray-700 text-white font-semibold py-2 px-4 rounded-lg transition-colors">
                        ← Retour
                    </a>
                    <a href="{{ route('appointments.edit', $appointment) }}" 
                       class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg transition-colors">
                        ✏️ Modifier
                    </a>
                </div>
            </div>
        </div>

        <!-- Status Badge -->
        <div class="mb-6">
            <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full 
                @if($appointment->status === 'Confirmé') bg-green-100 text-green-800
                @elseif($appointment->status === 'Planifié') bg-blue-100 text-blue-800
                @elseif($appointment->status === 'Enregistré') bg-yellow-100 text-yellow-800
                @elseif($appointment->status === 'Annulé') bg-red-100 text-red-800
                @else bg-gray-100 text-gray-800 @endif">
                {{ $appointment->status ?? 'N/A' }}
            </span>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Main Information -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow p-6 mb-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Informations du Rendez-vous</h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Date et Heure</label>
                            <p class="text-lg font-semibold text-gray-900">
                                {{ $appointment->appointment_date ? $appointment->appointment_date->format('d/m/Y') : 'N/A' }}
                            </p>
                            <p class="text-sm text-gray-600">
                                {{ $appointment->appointment_date ? $appointment->appointment_date->format('H:i') : 'N/A' }}
                            </p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Durée</label>
                            <p class="text-lg font-semibold text-gray-900">
                                {{ $appointment->duration_minutes ?? 'N/A' }} minutes
                            </p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Type de Rendez-vous</label>
                            <p class="text-lg font-semibold text-gray-900">
                                @php
                                    $typeLabels = [
                                        'consultation' => 'Consultation générale',
                                        'emergency' => 'Urgence',
                                        'follow_up' => 'Suivi',
                                        'pre_season' => 'Bilan pré-saison',
                                        'post_match' => 'Bilan post-match',
                                        'rehabilitation' => 'Rééducation',
                                        'routine_checkup' => 'Contrôle de routine',
                                        'injury_assessment' => 'Évaluation de blessure',
                                        'cardiac_evaluation' => 'Évaluation cardiaque',
                                        'concussion_assessment' => 'Évaluation commotion',
                                    ];
                                    echo $typeLabels[$appointment->appointment_type] ?? $appointment->appointment_type;
                                @endphp
                            </p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Statut</label>
                            <p class="text-lg font-semibold text-gray-900">
                                {{ $appointment->status ?? 'N/A' }}
                            </p>
                        </div>
                    </div>

                    @if($appointment->reason)
                    <div class="mt-6">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Motif</label>
                        <p class="text-gray-900">{{ $appointment->reason }}</p>
                    </div>
                    @endif

                    @if($appointment->notes)
                    <div class="mt-6">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                        <p class="text-gray-900">{{ $appointment->notes }}</p>
                    </div>
                    @endif
                </div>

                <!-- Patient Information -->
                <div class="bg-white rounded-lg shadow p-6 mb-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Informations du Patient</h2>
                    
                    <div class="flex items-center mb-4">
                        <div class="flex-shrink-0 h-16 w-16">
                            <div class="h-16 w-16 rounded-full bg-blue-100 flex items-center justify-center">
                                <span class="text-xl font-medium text-blue-600">
                                    {{ substr($appointment->athlete->name ?? 'N/A', 0, 2) }}
                                </span>
                            </div>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-semibold text-gray-900">
                                {{ $appointment->athlete->name ?? 'N/A' }}
                            </h3>
                            <p class="text-sm text-gray-600">
                                ID FIFA: {{ $appointment->athlete->fifa_connect_id ?? 'N/A' }}
                            </p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Âge</label>
                            <p class="text-gray-900">{{ $appointment->athlete->age ?? 'N/A' }} ans</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Position</label>
                            <p class="text-gray-900">{{ $appointment->athlete->position ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Taille</label>
                            <p class="text-gray-900">{{ $appointment->athlete->height ?? 'N/A' }} cm</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Poids</label>
                            <p class="text-gray-900">{{ $appointment->athlete->weight ?? 'N/A' }} kg</p>
                        </div>
                    </div>
                </div>

                <!-- Doctor Information -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Informations du Médecin</h2>
                    
                    <div class="flex items-center mb-4">
                        <div class="flex-shrink-0 h-16 w-16">
                            <div class="h-16 w-16 rounded-full bg-green-100 flex items-center justify-center">
                                <span class="text-xl font-medium text-green-600">
                                    {{ substr($appointment->doctor->name ?? 'N/A', 0, 2) }}
                                </span>
                            </div>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-semibold text-gray-900">
                                {{ $appointment->doctor->name ?? 'N/A' }}
                            </h3>
                            <p class="text-sm text-gray-600">
                                {{ $appointment->doctor->email ?? 'N/A' }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Quick Actions -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Actions Rapides</h3>
                    
                    <div class="space-y-3">
                        @if($appointment->status === 'Planifié')
                            <button onclick="confirmAppointment({{ $appointment->id }})" 
                                    class="w-full bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded-lg transition-colors">
                                ✅ Confirmer
                            </button>
                        @endif

                        @if($appointment->status === 'Confirmé')
                            <button onclick="checkInAppointment({{ $appointment->id }})" 
                                    class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg transition-colors">
                                🏥 Enregistrer (Check-in)
                            </button>
                        @endif

                        @if($appointment->status !== 'Annulé')
                            <button onclick="cancelAppointment({{ $appointment->id }})" 
                                    class="w-full bg-red-600 hover:bg-red-700 text-white font-semibold py-2 px-4 rounded-lg transition-colors">
                                ❌ Annuler
                            </button>
                        @endif

                        <a href="{{ route('visits.create', ['appointment_id' => $appointment->id]) }}" 
                           class="block w-full bg-purple-600 hover:bg-purple-700 text-white font-semibold py-2 px-4 rounded-lg transition-colors text-center">
                            🏥 Créer une Visite
                        </a>
                    </div>
                </div>

                <!-- Visit Information -->
                @if($appointment->visit)
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Visite Associée</h3>
                    
                    <div class="space-y-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Statut de la Visite</label>
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                @if($appointment->visit->status === 'Terminé') bg-green-100 text-green-800
                                @elseif($appointment->visit->status === 'En cours') bg-yellow-100 text-yellow-800
                                @elseif($appointment->visit->status === 'Enregistré') bg-blue-100 text-blue-800
                                @else bg-gray-100 text-gray-800 @endif">
                                {{ $appointment->visit->status }}
                            </span>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Date de la Visite</label>
                            <p class="text-gray-900">
                                {{ $appointment->visit->visit_date ? $appointment->visit->visit_date->format('d/m/Y H:i') : 'N/A' }}
                            </p>
                        </div>

                        <a href="{{ route('visits.show', $appointment->visit) }}" 
                           class="block w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 px-4 rounded-lg transition-colors text-center">
                            👁️ Voir la Visite
                        </a>
                    </div>
                </div>
                @endif

                <!-- Appointment History -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Historique</h3>
                    
                    <div class="space-y-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Créé le</label>
                            <p class="text-gray-900">
                                {{ $appointment->created_at ? $appointment->created_at->format('d/m/Y H:i') : 'N/A' }}
                            </p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Créé par</label>
                            <p class="text-gray-900">{{ $appointment->createdBy->name ?? 'N/A' }}</p>
                        </div>

                        @if($appointment->updated_at && $appointment->updated_at != $appointment->created_at)
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Modifié le</label>
                            <p class="text-gray-900">
                                {{ $appointment->updated_at->format('d/m/Y H:i') }}
                            </p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function confirmAppointment(appointmentId) {
    if (confirm('Confirmer ce rendez-vous ?')) {
        fetch(`/appointments/${appointmentId}/confirm`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            },
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Erreur: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Erreur lors de la confirmation');
        });
    }
}

function checkInAppointment(appointmentId) {
    if (confirm('Enregistrer le patient (check-in) ?')) {
        fetch(`/appointments/${appointmentId}/check-in`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            },
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Erreur: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Erreur lors du check-in');
        });
    }
}

function cancelAppointment(appointmentId) {
    if (confirm('Annuler ce rendez-vous ?')) {
        fetch(`/appointments/${appointmentId}/cancel`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            },
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Erreur: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Erreur lors de l\'annulation');
        });
    }
}
</script>
@endsection 