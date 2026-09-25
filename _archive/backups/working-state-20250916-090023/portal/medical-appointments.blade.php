@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-2xl font-bold text-gray-900">
                        <i class="fas fa-stethoscope text-purple-600 mr-2"></i>
                        Rendez-vous Médicaux
                    </h2>
                    <button id="new-appointment-btn" class="bg-purple-600 text-white px-4 py-2 rounded-md hover:bg-purple-700 transition-colors">
                        <i class="fas fa-plus mr-2"></i>
                        Demander un rendez-vous
                    </button>
                </div>

                <!-- Statistiques -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                    <div class="bg-blue-50 p-4 rounded-lg">
                        <div class="flex items-center">
                            <i class="fas fa-calendar-check text-blue-600 text-2xl mr-3"></i>
                            <div>
                                <p class="text-sm text-blue-600">Total</p>
                                <p id="total-appointments" class="text-2xl font-bold text-blue-900">0</p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-yellow-50 p-4 rounded-lg">
                        <div class="flex items-center">
                            <i class="fas fa-clock text-yellow-600 text-2xl mr-3"></i>
                            <div>
                                <p class="text-sm text-yellow-600">En attente</p>
                                <p id="pending-appointments" class="text-2xl font-bold text-yellow-900">0</p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-green-50 p-4 rounded-lg">
                        <div class="flex items-center">
                            <i class="fas fa-check-circle text-green-600 text-2xl mr-3"></i>
                            <div>
                                <p class="text-sm text-green-600">Confirmés</p>
                                <p id="confirmed-appointments" class="text-2xl font-bold text-green-900">0</p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-purple-50 p-4 rounded-lg">
                        <div class="flex items-center">
                            <i class="fas fa-video text-purple-600 text-2xl mr-3"></i>
                            <div>
                                <p class="text-sm text-purple-600">Télé médecine</p>
                                <p id="telemedicine-appointments" class="text-2xl font-bold text-purple-900">0</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Liste des rendez-vous -->
                <div id="appointments-list" class="space-y-4">
                    <!-- Les rendez-vous seront chargés ici -->
                </div>

                <!-- État de chargement -->
                <div id="loading" class="text-center py-8">
                    <i class="fas fa-spinner fa-spin text-2xl text-gray-400"></i>
                    <p class="mt-2 text-gray-500">Chargement des rendez-vous...</p>
                </div>

                <!-- État vide -->
                <div id="empty-state" class="text-center py-8 hidden">
                    <i class="fas fa-calendar-times text-4xl text-gray-300 mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Aucun rendez-vous</h3>
                    <p class="text-gray-500">Vous n'avez pas encore de rendez-vous médicaux.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Nouveau Rendez-vous -->
<div id="appointment-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg max-w-md w-full">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Demander un rendez-vous médical</h3>
                
                <form id="appointment-form">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Type de rendez-vous
                        </label>
                        <div class="space-y-2">
                            <label class="flex items-center">
                                <input type="radio" name="appointment_type" value="onsite" class="mr-2" checked>
                                <span>Sur site</span>
                            </label>
                            <label class="flex items-center">
                                <input type="radio" name="appointment_type" value="telemedicine" class="mr-2">
                                <span>Télé médecine (vidéo)</span>
                            </label>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="appointment-date" class="block text-sm font-medium text-gray-700 mb-2">
                            Date et heure souhaitées
                        </label>
                        <input type="datetime-local" id="appointment-date" required
                               class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-purple-500">
                    </div>

                    <div class="mb-4">
                        <label for="appointment-duration" class="block text-sm font-medium text-gray-700 mb-2">
                            Durée (minutes)
                        </label>
                        <select id="appointment-duration" required
                                class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-purple-500">
                            <option value="30">30 minutes</option>
                            <option value="45">45 minutes</option>
                            <option value="60">1 heure</option>
                            <option value="90">1 heure 30</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label for="appointment-reason" class="block text-sm font-medium text-gray-700 mb-2">
                            Motif de la consultation
                        </label>
                        <textarea id="appointment-reason" rows="3" required
                                  class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-purple-500"
                                  placeholder="Décrivez brièvement le motif de votre consultation..."></textarea>
                    </div>

                    <div class="mb-4">
                        <label for="appointment-notes" class="block text-sm font-medium text-gray-700 mb-2">
                            Notes additionnelles (optionnel)
                        </label>
                        <textarea id="appointment-notes" rows="2"
                                  class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-purple-500"
                                  placeholder="Informations supplémentaires..."></textarea>
                    </div>

                    <div class="flex justify-end space-x-2">
                        <button type="button" id="cancel-appointment" 
                                class="px-4 py-2 text-gray-600 hover:text-gray-800">
                            Annuler
                        </button>
                        <button type="submit" 
                                class="px-4 py-2 bg-purple-600 text-white rounded-md hover:bg-purple-700">
                            Demander le rendez-vous
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Détails Rendez-vous -->
<div id="appointment-details-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg max-w-lg w-full">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Détails du rendez-vous</h3>
                
                <div id="appointment-details" class="space-y-4">
                    <!-- Les détails seront chargés ici -->
                </div>

                <div class="flex justify-end space-x-2 mt-6">
                    <button id="close-details" class="px-4 py-2 text-gray-600 hover:text-gray-800">
                        Fermer
                    </button>
                    <button id="join-video" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 hidden">
                        <i class="fas fa-video mr-2"></i>
                        Rejoindre la session
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    let appointments = [];
    let currentAppointment = null;

    // Charger les rendez-vous
    function loadAppointments() {
        fetch('/portal/medical-appointments')
            .then(response => response.json())
            .then(data => {
                appointments = data.appointments.data;
                updateAppointmentsList();
                updateStatistics();
                hideLoading();
            })
            .catch(error => {
                console.error('Erreur:', error);
                hideLoading();
                showEmptyState();
            });
    }

    // Mettre à jour la liste des rendez-vous
    function updateAppointmentsList() {
        const container = document.getElementById('appointments-list');
        
        if (appointments.length === 0) {
            showEmptyState();
            return;
        }

        container.innerHTML = appointments.map(appointment => `
            <div class="appointment-item bg-gray-50 rounded-lg p-4 border-l-4 ${getStatusColor(appointment.status)}">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="flex-shrink-0">
                            <i class="${appointment.appointment_type === 'telemedicine' ? 'fas fa-video' : 'fas fa-user-md'} text-lg ${getStatusColor(appointment.status)}"></i>
                        </div>
                        <div class="flex-1">
                            <h4 class="text-sm font-medium text-gray-900">
                                Rendez-vous ${appointment.type_label}
                            </h4>
                            <p class="text-sm text-gray-600">
                                ${new Date(appointment.appointment_date).toLocaleString('fr-FR')}
                            </p>
                            <p class="text-xs text-gray-500">
                                Durée: ${appointment.duration_minutes} minutes
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-2">
                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full ${appointment.status_color}">
                            ${appointment.status_label}
                        </span>
                        <button onclick="viewAppointment(${appointment.id})" class="text-blue-600 hover:text-blue-800">
                            <i class="fas fa-eye"></i>
                        </button>
                        ${appointment.status === 'pending' ? `
                            <button onclick="cancelAppointment(${appointment.id})" class="text-red-600 hover:text-red-800">
                                <i class="fas fa-times"></i>
                            </button>
                        ` : ''}
                    </div>
                </div>
                <div class="mt-2">
                    <p class="text-sm text-gray-700">
                        <strong>Motif:</strong> ${appointment.reason}
                    </p>
                </div>
            </div>
        `).join('');
    }

    // Mettre à jour les statistiques
    function updateStatistics() {
        const total = appointments.length;
        const pending = appointments.filter(a => a.status === 'pending').length;
        const confirmed = appointments.filter(a => a.status === 'confirmed').length;
        const telemedicine = appointments.filter(a => a.appointment_type === 'telemedicine').length;

        document.getElementById('total-appointments').textContent = total;
        document.getElementById('pending-appointments').textContent = pending;
        document.getElementById('confirmed-appointments').textContent = confirmed;
        document.getElementById('telemedicine-appointments').textContent = telemedicine;
    }

    // Obtenir la couleur du statut
    function getStatusColor(status) {
        return {
            'pending': 'border-yellow-500',
            'confirmed': 'border-blue-500',
            'completed': 'border-green-500',
            'cancelled': 'border-red-500'
        }[status] || 'border-gray-500';
    }

    // Voir les détails d'un rendez-vous
    window.viewAppointment = function(appointmentId) {
        fetch(`/portal/medical-appointments/${appointmentId}`)
            .then(response => response.json())
            .then(data => {
                currentAppointment = data.appointment;
                displayAppointmentDetails(data.appointment);
            })
            .catch(error => console.error('Erreur:', error));
    };

    // Afficher les détails d'un rendez-vous
    function displayAppointmentDetails(appointment) {
        const container = document.getElementById('appointment-details');
        const joinVideoBtn = document.getElementById('join-video');
        
        container.innerHTML = `
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <p class="text-sm font-medium text-gray-700">Type</p>
                    <p class="text-sm text-gray-900">${appointment.type_label}</p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-700">Statut</p>
                    <p class="text-sm text-gray-900">${appointment.status_label}</p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-700">Date</p>
                    <p class="text-sm text-gray-900">${new Date(appointment.appointment_date).toLocaleString('fr-FR')}</p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-700">Durée</p>
                    <p class="text-sm text-gray-900">${appointment.duration_minutes} minutes</p>
                </div>
            </div>
            <div class="mt-4">
                <p class="text-sm font-medium text-gray-700">Motif</p>
                <p class="text-sm text-gray-900">${appointment.reason}</p>
            </div>
            ${appointment.notes ? `
                <div class="mt-4">
                    <p class="text-sm font-medium text-gray-700">Notes</p>
                    <p class="text-sm text-gray-900">${appointment.notes}</p>
                </div>
            ` : ''}
            ${appointment.doctor ? `
                <div class="mt-4">
                    <p class="text-sm font-medium text-gray-700">Médecin</p>
                    <p class="text-sm text-gray-900">${appointment.doctor.name}</p>
                </div>
            ` : ''}
        `;

        // Afficher le bouton vidéo si c'est une télé médecine confirmée
        if (appointment.appointment_type === 'telemedicine' && appointment.status === 'confirmed') {
            joinVideoBtn.classList.remove('hidden');
        } else {
            joinVideoBtn.classList.add('hidden');
        }

        document.getElementById('appointment-details-modal').classList.remove('hidden');
    }

    // Annuler un rendez-vous
    window.cancelAppointment = function(appointmentId) {
        if (!confirm('Êtes-vous sûr de vouloir annuler ce rendez-vous ?')) {
            return;
        }

        fetch(`/portal/medical-appointments/${appointmentId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            loadAppointments();
        })
        .catch(error => console.error('Erreur:', error));
    };

    // Nouveau rendez-vous
    document.getElementById('new-appointment-btn').addEventListener('click', function() {
        document.getElementById('appointment-modal').classList.remove('hidden');
    });

    document.getElementById('cancel-appointment').addEventListener('click', function() {
        document.getElementById('appointment-modal').classList.add('hidden');
        document.getElementById('appointment-form').reset();
    });

    document.getElementById('appointment-form').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = {
            appointment_type: document.querySelector('input[name="appointment_type"]:checked').value,
            appointment_date: document.getElementById('appointment-date').value,
            duration_minutes: document.getElementById('appointment-duration').value,
            reason: document.getElementById('appointment-reason').value,
            notes: document.getElementById('appointment-notes').value
        };

        fetch('/portal/medical-appointments', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify(formData)
        })
        .then(response => response.json())
        .then(data => {
            document.getElementById('appointment-modal').classList.add('hidden');
            document.getElementById('appointment-form').reset();
            loadAppointments();
        })
        .catch(error => console.error('Erreur:', error));
    });

    // Fermer les modals
    document.getElementById('close-details').addEventListener('click', function() {
        document.getElementById('appointment-details-modal').classList.add('hidden');
    });

    // Rejoindre la session vidéo
    document.getElementById('join-video').addEventListener('click', function() {
        if (!currentAppointment) return;

        fetch(`/portal/medical-appointments/${currentAppointment.id}/join-video`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.meeting_url) {
                window.open(data.meeting_url, '_blank');
            }
        })
        .catch(error => console.error('Erreur:', error));
    });

    // Fonctions utilitaires
    function hideLoading() {
        document.getElementById('loading').style.display = 'none';
    }

    function showEmptyState() {
        document.getElementById('empty-state').classList.remove('hidden');
    }

    // Initialisation
    loadAppointments();
});
</script>
@endpush
@endsection 