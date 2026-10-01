@extends('layouts.app')

@section('title', 'PCMA Dashboard - Med Predictor')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">📋 PCMA Dashboard</h1>
            <p class="text-gray-600 mt-2">{{ __('pcma_extra.label_2a2066cd9351') }}</p>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Total PCMA</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['total_pcmas'] }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">{{ __('competitions.rapports_statistiques_page.pending') }}</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['pending_pcmas'] }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-green-100 text-green-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">{{ __('pcma_extra.label_1d462fba2b2f') }}</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['completed_pcmas'] }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-red-100 text-red-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">{{ __('pcma_extra.label_ff28696a1d2f') }}</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['failed_pcmas'] }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex justify-between items-center mb-8">
            <div class="flex space-x-4">
                <a href="{{ route('pcma.create') }}" 
                   class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200">
                    {{ __('pcma_extra.label_47ad15b4e542') }}
                </a>
                <a href="{{ route('pcma.index') }}" 
                   class="bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200">
                    {{ __('pcma_extra.label_6d0bc308f733') }}
                </a>
                <a href="{{ route('modules.index') }}" 
                   class="bg-gray-600 hover:bg-gray-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200">
                    {{ __('errors.back_to_modules') }}
                </a>
            </div>
        </div>

        <!-- Recent PCMAs -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-xl font-semibold text-gray-800">{{ __('pcma_extra.label_54576ae6b512') }}</h2>
            </div>
            
            <div class="p-6">
                @if($recentPcmas->count() > 0)
                    <div class="space-y-4">
                        @foreach($recentPcmas as $pcma)
                            <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition-colors">
                                <div class="flex justify-between items-start">
                                    <div class="flex-1">
                                        <div class="flex items-center space-x-3">
                                            <h3 class="text-lg font-medium text-gray-900">
                                                {{ ($pcma->player ?? $pcma->athlete)->name ?? __('pcma_extra.unknown_athlete') }}
                                            </h3>
                                            <span class="px-2 py-1 text-xs rounded-full {{ $pcma->status === 'completed' ? 'bg-green-100 text-green-800' : ($pcma->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                                {{ ucfirst($pcma->status ?? 'pending') }}
                                            </span>
                                        </div>
                                        <p class="text-sm text-gray-600 mt-1">
                                            Type: <span class="font-medium">{{ $pcma->type === 'pcma' ? __('pcma_workflow.type_pcma') : ucfirst($pcma->type ?? 'standard') }}</span>
                                            @if($pcma->assessor)
                                                {{ __('• Assesseur:') }} <span class="font-medium">{{ $pcma->assessor->name }}</span>
                                            @endif
                                        </p>
                                        <p class="text-xs text-gray-500 mt-1">
                                            @if($pcma->created_at)
                                                {{ __('Créé le') }} {{ $pcma->created_at->format('d/m/Y H:i') }}
                                            @endif
                                            @if($pcma->completed_at)
                                                {{ __('• Complété le') }} {{ $pcma->completed_at->format('d/m/Y H:i') }}
                                            @endif
                                        </p>
                                    </div>
                                    <div class="flex space-x-2">
                                        <a href="{{ route('pcma.show', $pcma) }}" 
                                           class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                            {{ __('healthcare.view') }}
                                        </a>
                                        <a href="{{ route('pcma.edit', $pcma) }}" 
                                           class="text-gray-600 hover:text-gray-800 text-sm font-medium">
                                            {{ __('healthcare.edit') }}
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <div class="text-gray-400 mb-4">
                            <svg class="w-16 h-16 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">{{ __('pcma_extra.label_08e707b5e154') }}</h3>
                        <p class="text-gray-600 mb-4">{{ __('pcma_extra.label_00f34739a43d') }}</p>
                        <a href="{{ route('pcma.create') }}" 
                           class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200">
                            {{ __('pcma_extra.label_d1ca4cfd1223') }}
                        </a>
                    </div>
                @endif
            </div>
        </div>

        <!-- Signed PCMAs with Vue.js -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden mt-8">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-xl font-semibold text-gray-800">{{ __('pcma_extra.label_d285a49dffad') }}</h2>
                <p class="text-sm text-gray-600 mt-1">{{ __('pcma_extra.label_1295b06f0f38') }}</p>
            </div>
            
            <div id="signed-pcmas-app" class="p-6">
                <!-- Vue.js will render here -->
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
<script>
const { createApp } = Vue;

createApp({
    data() {
        return {
            signedPcmas: [],
            loading: true,
            error: null
        }
    },
    methods: {
        async loadSignedPcmas() {
            try {
                this.loading = true;
                const response = await fetch('/api/signed-pcmas');
                
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                const data = await response.json();
                
                if (data.success) {
                    this.signedPcmas = data.pcmas;
                } else {
                    this.error = data.message || @json(__('Erreur lors du chargement'));
                }
            } catch (error) {
                console.error('Error loading signed PCMAs:', error);
                this.error = @json(__('Erreur de connexion'));
            } finally {
                this.loading = false;
            }
        },
        formatDate(dateString) {
            return new Date(dateString).toLocaleDateString('fr-FR', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
        },
        getStatusBadge(status) {
            const statusMap = {
                'completed': { class: 'bg-green-100 text-green-800', text: @json(__('Complété')) },
                'pending': { class: 'bg-yellow-100 text-yellow-800', text: 'En attente' },
                'failed': { class: 'bg-red-100 text-red-800', text: @json(__('Échoué')) }
            };
            return statusMap[status] || { class: 'bg-gray-100 text-gray-800', text: @json(__('Inconnu')) };
        },
        viewPcma(pcmaId) {
            window.location.href = `/pcma/${pcmaId}`;
        },
        printPcma(pcmaId) {
            window.open(`/pcma/${pcmaId}/pdf`, '_blank');
        }
    },
    mounted() {
        this.loadSignedPcmas();
    },
    template: `
        <div>
            <!-- Loading State -->
            <div v-if="loading" class="text-center py-8">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mx-auto"></div>
                <p class="text-gray-600 mt-2">{{ __('Chargement des PCMAs signés...') }}</p>
            </div>

            <!-- Error State -->
            <div v-else-if="error" class="text-center py-8">
                <div class="text-red-500 mb-4">
                    <svg class="w-12 h-12 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">{{ __('pcma.loading_error') }}</h3>
                <p class="text-gray-600 mb-4">@{{ error }}</p>
                <button @click="loadSignedPcmas" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200">
                    Réessayer
                </button>
            </div>

            <!-- Signed PCMAs List -->
            <div v-else-if="signedPcmas.length > 0" class="space-y-4">
                <div v-for="pcma in signedPcmas" :key="pcma.id" class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition-colors">
                    <div class="flex justify-between items-start">
                        <div class="flex-1">
                            <div class="flex items-center space-x-3 mb-2">
                                <h3 class="text-lg font-medium text-gray-900">
                                    <span v-if="pcma.athlete?.name">@{{ pcma.athlete.name }}</span><span v-else>{{ __('pcma_extra.unknown_athlete') }}</span>
                                </h3>
                                <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">
                                    {{ __('✅ Signé') }}
                                </span>
                                <span :class="'px-2 py-1 text-xs rounded-full ' + getStatusBadge(pcma.status).class">
                                    @{{ getStatusBadge(pcma.status).text }}
                                </span>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-gray-600">
                                <div>
                                    <p><strong>Type:</strong> @{{ pcma.type === 'pcma' ? 'PCMA' : (pcma.type ? pcma.type.charAt(0).toUpperCase() + pcma.type.slice(1) : 'Standard') }}</p>
                                    <p v-if="pcma.assessor"><strong>{{ __('pcma_extra.assessor_short') }}</strong> @{{ pcma.assessor.name }}</p>
                                    <p><strong>{{ __('pcma.report_assessment_date_label') }}</strong> @{{ pcma.assessment_date ? formatDate(pcma.assessment_date) : 'Non spécifiée' }}</p>
                                </div>
                                <div>
                                    <p><strong>{{ __('pcma.report_signed_by_label') }}</strong> @{{ pcma.signed_by || 'Non spécifié' }}</p>
                                    <p><strong>{{ __('pcma_extra.label_38aec7f0fe5a') }}</strong> @{{ pcma.license_number || 'Non spécifiée' }}</p>
                                    <p><strong>{{ __('pcma.report_signature_date_label') }}</strong> @{{ pcma.signed_at ? formatDate(pcma.signed_at) : 'Non spécifiée' }}</p>
                                </div>
                            </div>
                            
                            <p class="text-xs text-gray-500 mt-2">
                                {{ __('Créé le') }} @{{ formatDate(pcma.created_at) }}
                                <span v-if="pcma.completed_at">{{ __('• Complété le') }} @{{ formatDate(pcma.completed_at) }}</span>
                            </p>
                        </div>
                        
                        <div class="flex space-x-2">
                            <button @click="viewPcma(pcma.id)" 
                                    class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                {{ __('👁️ Voir') }}
                            </button>
                            <button @click="printPcma(pcma.id)" 
                                    class="text-green-600 hover:text-green-800 text-sm font-medium">
                                {{ __('pcma_extra.label_26d5eac2bb68') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Empty State -->
            <div v-else class="text-center py-8">
                <div class="text-gray-400 mb-4">
                    <svg class="w-16 h-16 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">{{ __('Aucun PCMA signé trouvé') }}</h3>
                <p class="text-gray-600 mb-4">{{ __('Aucun document médical signé n’est disponible pour le moment. Les PCMAs signés apparaîtront ici une fois qu’ils auront été complétés et signés.') }}</p>
                <div class="flex justify-center">
                    <button @click="loadSignedPcmas" 
                            class="bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200">
                        {{ __('🔄 Recharger la liste') }}
                    </button>
                </div>
            </div>
        </div>
    `
}).mount('#signed-pcmas-app');
</script>
@endpush
@endsection
