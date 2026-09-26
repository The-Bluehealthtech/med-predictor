@extends('layouts.secretary')

@section('title', __('secretary.dashboard_title'))

@section('content')
<div id="secretary-dashboard">
    <!-- En-tête -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">{{ __('secretary.dashboard_heading') }}</h1>
        <p class="text-gray-600 mt-2">{{ __('secretary.dashboard_subtitle') }}</p>
    </div>

    <!-- Statistiques -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                    <i class="fas fa-calendar-alt text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">{{ __('secretary.stat_appointments') }}</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $stats['total_appointments'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 text-green-600">
                    <i class="fas fa-clock text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">{{ __('secretary.stat_upcoming') }}</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $stats['upcoming_appointments'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                    <i class="fas fa-file-medical text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">{{ __('secretary.stat_documents') }}</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $stats['total_documents'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                    <i class="fas fa-hourglass-half text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">{{ __('secretary.stat_pending') }}</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $stats['pending_documents'] }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Actions Rapides -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">
                <i class="fas fa-plus-circle text-blue-600 mr-2"></i>
                {{ __('secretary.quick_actions') }}
            </h3>
            <div class="space-y-3">
                <button @click="showAppointmentModal = true" 
                        class="w-full flex items-center justify-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700">
                    <i class="fas fa-calendar-plus mr-2"></i>
                    {{ __('secretary.new_appointment') }}
                </button>
                <button @click="showDocumentModal = true" 
                        class="w-full flex items-center justify-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700">
                    <i class="fas fa-upload mr-2"></i>
                    {{ __('secretary.upload_document') }}
                </button>
                <button @click="showAthleteSearch = true" 
                        class="w-full flex items-center justify-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-purple-600 hover:bg-purple-700">
                    <i class="fas fa-search mr-2"></i>
                    {{ __('secretary.search_athlete') }}
                </button>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">
                <i class="fas fa-chart-line text-green-600 mr-2"></i>
                {{ __('secretary.today') }}
            </h3>
            <div class="space-y-2">
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">{{ __('secretary.appointments_today') }}</span>
                    <span class="text-sm font-medium text-gray-900">{{ $stats['upcoming_appointments'] }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">{{ __('secretary.documents_pending') }}</span>
                    <span class="text-sm font-medium text-gray-900">{{ $stats['pending_documents'] }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">{{ __('secretary.ai_analyses') }}</span>
                    <span class="text-sm font-medium text-gray-900">0</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Rendez-vous Récents -->
    <div class="bg-white rounded-lg shadow mb-8">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">
                <i class="fas fa-calendar-alt text-blue-600 mr-2"></i>
                {{ __('secretary.recent_appointments') }}
            </h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('secretary.col_athlete') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('secretary.col_date') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('secretary.col_type') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('secretary.col_status') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('secretary.col_actions') }}</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($recentAppointments as $appointment)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    <div class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center">
                                        <i class="fas fa-user text-gray-600"></i>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $appointment->athlete->name ?? __('secretary.unknown_player') }}</div>
                                    <div class="text-sm text-gray-500">{{ $appointment->athlete->fifa_id ?? 'N/A' }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $appointment->appointment_date ? $appointment->appointment_date->format('d/m/Y H:i') : __('secretary.date_undefined') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                {{ $appointment->type === 'consultation' ? 'bg-blue-100 text-blue-800' : 
                                   ($appointment->type === 'examination' ? 'bg-green-100 text-green-800' : 
                                   ($appointment->type === 'emergency' ? 'bg-red-100 text-red-800' : 
                                   ($appointment->type === 'follow_up' ? 'bg-purple-100 text-purple-800' : 'bg-gray-100 text-gray-800'))) }}">
                                {{ ucfirst($appointment->type ?? __('secretary.undefined')) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                {{ $appointment->status === 'confirmed' ? 'bg-green-100 text-green-800' : 
                                   ($appointment->status === 'scheduled' ? 'bg-yellow-100 text-yellow-800' : 
                                   ($appointment->status === 'cancelled' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800')) }}">
                                {{ ucfirst($appointment->status ?? __('secretary.undefined')) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <button class="text-blue-600 hover:text-blue-900 mr-2">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="text-red-600 hover:text-red-900">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                            {{ __('secretary.no_upcoming_appointments') }}
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Documents Récents -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">
                <i class="fas fa-file-medical text-green-600 mr-2"></i>
                {{ __('secretary.recent_documents') }}
            </h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('secretary.col_document') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('secretary.col_athlete') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('secretary.col_type') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('secretary.col_status') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('secretary.col_actions') }}</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($recentDocuments as $document)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    <div class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center">
                                        <i class="fas fa-file text-gray-600"></i>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $document->file_name ?? __('secretary.unnamed_document') }}</div>
                                    <div class="text-sm text-gray-500">{{ $document->file_size ?? __('secretary.unknown_size') }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $document->player->name ?? 'N/A' }}</div>
                            <div class="text-sm text-gray-500">{{ $document->player->fifa_connect_id ?? 'N/A' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                {{ ucfirst($document->medical_record_type ?? __('secretary.unknown_type')) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                {{ $document->status === 'analyzed' ? 'bg-green-100 text-green-800' : 
                                   ($document->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800') }}">
                                {{ ucfirst($document->status ?? __('secretary.undefined')) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <button class="text-blue-600 hover:text-blue-900 mr-2">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="text-green-600 hover:text-green-900 mr-2">
                                <i class="fas fa-brain"></i>
                            </button>
                            <button class="text-red-600 hover:text-red-900">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                            {{ __('secretary.no_recent_documents') }}
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modals -->
@include('secretary.partials.appointment-modal')
@include('secretary.partials.document-modal')
@include('secretary.partials.athlete-search-modal')

@push('scripts')
<script>
const { createApp, ref } = Vue;

createApp({
    setup() {
        const showAppointmentModal = ref(false);
        const showDocumentModal = ref(false);
        const showAthleteSearch = ref(false);

        return {
            showAppointmentModal,
            showDocumentModal,
            showAthleteSearch
        };
    }
}).mount('#secretary-dashboard');
</script>
@endpush
@endsection 