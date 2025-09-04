@extends('layouts.app')

@section('title', 'Détails du Passeport Joueur')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-6">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Détails du Passeport</h1>
                    <p class="mt-1 text-sm text-gray-500">Passeport FIFA de {{ $playerPassport->player->first_name }} {{ $playerPassport->player->last_name }}</p>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('player-passports.edit', $playerPassport) }}" 
                       class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Modifier
                    </a>
                    <a href="{{ route('player-passports.index') }}" 
                       class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Retour
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Player Information -->
                <div class="bg-white shadow rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Informations du Joueur</h3>
                    </div>
                    <div class="px-6 py-4">
                        <div class="flex items-center space-x-4">
                            <div class="flex-shrink-0 h-16 w-16">
                                @if($playerPassport->player->player_picture)
                                    <img class="h-16 w-16 rounded-full object-cover" src="{{ asset('storage/' . $playerPassport->player->player_picture) }}" alt="{{ $playerPassport->player->first_name }}">
                                @else
                                    <div class="h-16 w-16 rounded-full bg-gray-300 flex items-center justify-center">
                                        <span class="text-lg font-medium text-gray-700">
                                            {{ substr($playerPassport->player->first_name, 0, 1) }}{{ substr($playerPassport->player->last_name, 0, 1) }}
                                        </span>
                                    </div>
                                @endif
                            </div>
                            <div>
                                <h4 class="text-xl font-semibold text-gray-900">
                                    {{ $playerPassport->player->first_name }} {{ $playerPassport->player->last_name }}
                                </h4>
                                <p class="text-sm text-gray-500">{{ $playerPassport->player->position }} • {{ $playerPassport->player->nationality }}</p>
                                <p class="text-sm text-gray-500">{{ $playerPassport->player->club->name ?? 'Sans club' }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Passport Details -->
                <div class="bg-white shadow rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Détails du Passeport</h3>
                    </div>
                    <div class="px-6 py-4">
                        <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Numéro de Passeport</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $playerPassport->passport_number }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Type</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        @if($playerPassport->passport_type === 'electronic') bg-blue-100 text-blue-800
                                        @elseif($playerPassport->passport_type === 'physical') bg-green-100 text-green-800
                                        @else bg-yellow-100 text-yellow-800 @endif">
                                        {{ ucfirst($playerPassport->passport_type) }}
                                    </span>
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Statut</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        @if($playerPassport->status === 'active') bg-green-100 text-green-800
                                        @elseif($playerPassport->status === 'suspended') bg-red-100 text-red-800
                                        @elseif($playerPassport->status === 'expired') bg-yellow-100 text-yellow-800
                                        @elseif($playerPassport->status === 'revoked') bg-red-100 text-red-800
                                        @else bg-gray-100 text-gray-800 @endif">
                                        {{ ucfirst(str_replace('_', ' ', $playerPassport->status)) }}
                                    </span>
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Conformité FIFA</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        @if($playerPassport->compliance_status === 'compliant') bg-green-100 text-green-800
                                        @elseif($playerPassport->compliance_status === 'non_compliant') bg-red-100 text-red-800
                                        @else bg-yellow-100 text-yellow-800 @endif">
                                        {{ ucfirst(str_replace('_', ' ', $playerPassport->compliance_status)) }}
                                    </span>
                                </dd>
                            </div>
                        </dl>
                    </div>
                </div>

                <!-- Dates -->
                <div class="bg-white shadow rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Dates</h3>
                    </div>
                    <div class="px-6 py-4">
                        <dl class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Date d'Émission</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $playerPassport->issue_date->format('d/m/Y') }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Date d'Expiration</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    <span class="@if($playerPassport->expiry_date->isPast()) text-red-600 font-medium @endif">
                                        {{ $playerPassport->expiry_date->format('d/m/Y') }}
                                    </span>
                                </dd>
                            </div>
                            @if($playerPassport->renewal_date)
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Date de Renouvellement</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $playerPassport->renewal_date->format('d/m/Y') }}</dd>
                            </div>
                            @endif
                        </dl>
                    </div>
                </div>

                <!-- Issuing Authority -->
                <div class="bg-white shadow rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Autorité Émettrice</h3>
                    </div>
                    <div class="px-6 py-4">
                        <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Autorité</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $playerPassport->issuing_authority }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Pays</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $playerPassport->issuing_country }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>

                @if($playerPassport->notes)
                <!-- Notes -->
                <div class="bg-white shadow rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Notes</h3>
                    </div>
                    <div class="px-6 py-4">
                        <p class="text-sm text-gray-900">{{ $playerPassport->notes }}</p>
                    </div>
                </div>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Status Card -->
                <div class="bg-white shadow rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Statut</h3>
                    </div>
                    <div class="px-6 py-4">
                        <div class="space-y-3">
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-500">Validité</span>
                                <span class="text-sm font-medium @if($playerPassport->expiry_date->isPast()) text-red-600 @else text-green-600 @endif">
                                    @if($playerPassport->expiry_date->isPast())
                                        Expiré
                                    @elseif($playerPassport->expiry_date->diffInDays(now()) <= 30)
                                        Expire bientôt
                                    @else
                                        Valide
                                    @endif
                                </span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-500">Jours restants</span>
                                <span class="text-sm font-medium @if($playerPassport->expiry_date->isPast()) text-red-600 @elseif($playerPassport->expiry_date->diffInDays(now()) <= 30) text-yellow-600 @else text-green-600 @endif">
                                    @if($playerPassport->expiry_date->isPast())
                                        {{ $playerPassport->expiry_date->diffInDays(now()) }} jours en retard
                                    @else
                                        {{ $playerPassport->expiry_date->diffInDays(now()) }} jours
                                    @endif
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="bg-white shadow rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Actions</h3>
                    </div>
                    <div class="px-6 py-4 space-y-3">
                        <button onclick="exportPassport()" class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            Exporter PDF
                        </button>
                        
                        <form method="POST" action="{{ route('player-passports.destroy', $playerPassport) }}" class="inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce passeport ?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-2 border border-red-300 text-sm font-medium rounded-md text-red-700 bg-white hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                                Supprimer
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function exportPassport() {
    // TODO: Implement PDF export functionality
    alert('Fonctionnalité d\'export PDF à implémenter');
}
</script>
@endsection 