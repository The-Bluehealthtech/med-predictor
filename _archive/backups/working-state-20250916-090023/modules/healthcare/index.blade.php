@extends('layouts.app')

@section('title', __('healthcare.records_title'))

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">🩺 {{ __('healthcare.records_title') }}</h1>
        <p class="text-gray-600 mt-2">{{ __('healthcare.records_description') }}</p>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-800">{{ __('healthcare.records_list') }}</h2>
        </div>
        
        @if($healthRecords->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                {{ __('healthcare.patient') }}
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                {{ __('healthcare.date') }}
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                {{ __('healthcare.status') }}
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                {{ __('healthcare.risk') }}
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                {{ __('healthcare.predictions') }}
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                {{ __('healthcare.actions') }}
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($healthRecords as $record)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
                                                <span class="text-blue-600 font-semibold">
                                                    {{ $record->player ? substr($record->player->first_name, 0, 1) . substr($record->player->last_name, 0, 1) : __('healthcare.na') }}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ $record->player ? $record->player->full_name : __('healthcare.anonymous_patient') }}
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                {{ $record->user->name }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $record->record_date->format('d/m/Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                        {{ $record->status === 'active' ? 'bg-green-100 text-green-800' : 
                                           ($record->status === 'archived' ? 'bg-gray-100 text-gray-800' : 'bg-yellow-100 text-yellow-800') }}">
                                        {{ __('healthcare.status_' . $record->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($record->risk_score)
                                        <div class="flex items-center">
                                            <div class="w-16 bg-gray-200 rounded-full h-2 mr-2">
                                                <div class="bg-{{ $record->risk_score > 0.7 ? 'red' : ($record->risk_score > 0.4 ? 'yellow' : 'green') }}-500 h-2 rounded-full" 
                                                     style="width: {{ $record->risk_score * 100 }}%"></div>
                                            </div>
                                            <span class="text-sm text-gray-600">{{ number_format($record->risk_score * 100, 0) }}%</span>
                                        </div>
                                    @else
                                        <span class="text-gray-400">{{ __('healthcare.na') }}</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $record->predictions->count() }} {{ __('healthcare.prediction_count') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                        <a href="{{ route('healthcare.records.show', ['record' => $record->id ?? 1]) }}" 
                                           class="text-blue-600 hover:text-blue-900">Voir</a>
                                        <a href="{{ route('healthcare.records.edit', ['record' => $record->id ?? 1]) }}" 
                                           class="text-indigo-600 hover:text-indigo-900">Modifier</a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <div class="px-6 py-4 border-t border-gray-200">
                <!-- Pagination removed - using Collection instead of paginated results -->
            </div>
        @else
            <div class="px-6 py-8 text-center">
                <div class="text-gray-500">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">{{ __('healthcare.no_records') }}</h3>
                    <p class="mt-1 text-sm text-gray-500">{{ __('healthcare.no_records_description') }}</p>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection 