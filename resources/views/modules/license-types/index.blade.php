@extends('layouts.app')

@section('title', 'Types de Licence - FIT Platform')

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-6">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">📄 Types de Licence</h1>
                    <p class="text-sm text-gray-600">Catalogue des types de licences FIFA Connect</p>
                </div>
                <a href="{{ route('modules.index') }}" class="text-gray-600 hover:text-gray-900 text-sm font-medium">← Retour aux Modules</a>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-xl font-semibold text-gray-800">Types de licence ({{ $licenseTypes->count() }})</h2>
            </div>

            @if($licenseTypes->isEmpty())
            <div class="p-6 text-center text-gray-500">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">Aucun type de licence configuré</h3>
            </div>
            @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="p-3 text-left text-xs font-medium text-gray-500 uppercase">Nom</th>
                            <th class="p-3 text-left text-xs font-medium text-gray-500 uppercase">Code</th>
                            <th class="p-3 text-left text-xs font-medium text-gray-500 uppercase">Durée de validité</th>
                            <th class="p-3 text-left text-xs font-medium text-gray-500 uppercase">Frais</th>
                            <th class="p-3 text-left text-xs font-medium text-gray-500 uppercase">Statut</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($licenseTypes as $type)
                        <tr>
                            <td class="p-3">{{ $type->name }}</td>
                            <td class="p-3">{{ $type->code }}</td>
                            <td class="p-3">{{ $type->validity_period_months ? $type->getValidityPeriodFormatted() : '—' }}</td>
                            <td class="p-3">{{ $type->fee_amount !== null ? $type->getFeeFormatted() : '—' }}</td>
                            <td class="p-3">
                                <span class="px-2 py-1 text-xs rounded-full {{ $type->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                    {{ $type->getStatusText() }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
