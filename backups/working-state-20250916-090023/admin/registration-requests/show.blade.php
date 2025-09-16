@extends('layouts.app')

@section('title', 'Portal Registration Request Details')

@section('content')
<div class="py-12">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <!-- Header -->
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h2 class="text-2xl font-bold">Portal Registration Request #{{ $registrationRequest->id }}</h2>
                        <p class="text-gray-600">Soumise le {{ $registrationRequest->created_at->format('d/m/Y à H:i') }}</p>
                    </div>
                    <div class="flex space-x-2">
                        <a href="{{ route('admin.registration-requests.index') }}" 
                           class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-md text-sm font-medium">
                            Retour
                        </a>
                        @if($registrationRequest->status === 'pending')
                            <button onclick="approveRequest()" 
                                    class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md text-sm font-medium">
                                Approuver
                            </button>
                            <button onclick="rejectRequest()" 
                                    class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md text-sm font-medium">
                                Rejeter
                            </button>
                        @endif
                    </div>
                </div>

                <!-- Status Badge -->
                <div class="mb-6">
                    @if($registrationRequest->status === 'pending')
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            En attente de validation
                        </span>
                    @elseif($registrationRequest->status === 'approved')
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Approuvée
                        </span>
                    @else
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Rejetée
                        </span>
                    @endif
                </div>

                <!-- Request Details -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Personal Information -->
                    <div class="bg-gray-50 rounded-lg p-4">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Informations personnelles</h3>
                        <div class="space-y-3">
                            <div>
                                <label class="text-sm font-medium text-gray-700">Nom complet</label>
                                <p class="text-sm text-gray-900">{{ $registrationRequest->full_name }}</p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-700">Email</label>
                                <p class="text-sm text-gray-900">{{ $registrationRequest->email }}</p>
                            </div>
                            @if($registrationRequest->phone)
                            <div>
                                <label class="text-sm font-medium text-gray-700">Téléphone</label>
                                <p class="text-sm text-gray-900">{{ $registrationRequest->phone }}</p>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Association & Profile Information -->
                    <div class="bg-gray-50 rounded-lg p-4">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Association et profil</h3>
                        <div class="space-y-3">
                            <div>
                                <label class="text-sm font-medium text-gray-700">Association</label>
                                <p class="text-sm text-gray-900">{{ $registrationRequest->association_name }}</p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-700">Type de profil</label>
                                <p class="text-sm text-gray-900">{{ $registrationRequest->profile_type_name }}</p>
                            </div>
                            @if($registrationRequest->organization)
                            <div>
                                <label class="text-sm font-medium text-gray-700">Organisation/Club</label>
                                <p class="text-sm text-gray-900">{{ $registrationRequest->organization }}</p>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Reason for Access -->
                <div class="mt-6 bg-gray-50 rounded-lg p-4">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Raison de la demande</h3>
                    <div class="bg-white rounded-md p-3 border">
                        <p class="text-sm text-gray-900 whitespace-pre-wrap">{{ $registrationRequest->reason }}</p>
                    </div>
                </div>

                <!-- Review Information -->
                @if($registrationRequest->status !== 'pending')
                <div class="mt-6 bg-gray-50 rounded-lg p-4">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Informations de validation</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="text-sm font-medium text-gray-700">Statut</label>
                            <p class="text-sm text-gray-900">
                                @if($registrationRequest->status === 'approved')
                                    <span class="text-green-600 font-medium">Approuvée</span>
                                @else
                                    <span class="text-red-600 font-medium">Rejetée</span>
                                @endif
                            </p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-700">Date de validation</label>
                            <p class="text-sm text-gray-900">{{ $registrationRequest->reviewed_at->format('d/m/Y à H:i') }}</p>
                        </div>
                        @if($registrationRequest->reviewer)
                        <div>
                            <label class="text-sm font-medium text-gray-700">Validé par</label>
                            <p class="text-sm text-gray-900">{{ $registrationRequest->reviewer->name }}</p>
                        </div>
                        @endif
                        @if($registrationRequest->admin_notes)
                        <div class="md:col-span-2">
                            <label class="text-sm font-medium text-gray-700">Notes administratives</label>
                            <div class="bg-white rounded-md p-3 border mt-1">
                                <p class="text-sm text-gray-900 whitespace-pre-wrap">{{ $registrationRequest->admin_notes }}</p>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
                @endif

                <!-- Action Modal -->
                <div id="actionModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
                    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                        <div class="mt-3">
                            <h3 class="text-lg font-medium text-gray-900 mb-4" id="modalTitle">Action</h3>
                            <div id="modalContent">
                                <!-- Content will be dynamically inserted -->
                            </div>
                            <div class="flex justify-end space-x-3 mt-4">
                                <button onclick="closeModal()" 
                                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 border border-gray-300 rounded-md hover:bg-gray-200">
                                    Annuler
                                </button>
                                <button id="confirmButton" 
                                        class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700">
                                    Confirmer
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function approveRequest() {
        showModal('Approuver la demande', 
                 'Êtes-vous sûr de vouloir approuver cette demande ? Un compte utilisateur sera créé automatiquement.',
                 'Approuver',
                 'bg-green-600 hover:bg-green-700',
                 () => updateRequestStatus('approved'));
    }

    function rejectRequest() {
        const notes = prompt('Veuillez indiquer la raison du rejet (optionnel) :');
        if (notes !== null) {
            showModal('Rejeter la demande', 
                     'Êtes-vous sûr de vouloir rejeter cette demande ?',
                     'Rejeter',
                     'bg-red-600 hover:bg-red-700',
                     () => updateRequestStatus('rejected', notes));
        }
    }

    function showModal(title, content, buttonText, buttonClass, onConfirm) {
        document.getElementById('modalTitle').textContent = title;
        document.getElementById('modalContent').innerHTML = `<p class="text-sm text-gray-600">${content}</p>`;
        document.getElementById('confirmButton').textContent = buttonText;
        document.getElementById('confirmButton').className = `px-4 py-2 text-sm font-medium text-white ${buttonClass} border border-transparent rounded-md`;
        
        document.getElementById('confirmButton').onclick = onConfirm;
        document.getElementById('actionModal').classList.remove('hidden');
    }

    function closeModal() {
        document.getElementById('actionModal').classList.add('hidden');
    }

    function updateRequestStatus(status, notes = '') {
        fetch('{{ route("admin.registration-requests.update", $registrationRequest) }}', {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            },
            body: JSON.stringify({
                status: status,
                admin_notes: notes
            })
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
            alert('Erreur lors de la mise à jour du statut.');
        });
    }
</script>
@endsection 