@extends('layouts.app')

@section('title', 'Player Licenses - FIT Platform')

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <x-page-header
            title="Licences joueurs"
            subtitle="Vue d’ensemble des licences enregistrées, leur statut et leur échéance."
            eyebrow="Administration"
            :back-href="route('modules.index')"
            back-label="Retour aux modules"
            :count="$licenses->total()"
            count-label="licence(s)"
        />
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-xl font-semibold text-gray-800">Licences ({{ $licenses->total() }})</h2>
            </div>

            @if($licenses->isEmpty())
            <div class="p-6 text-center text-gray-500">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">{{ __('Aucune licence enregistrée') }}</h3>
            </div>
            @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="p-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('auth.role_player') }}</th>
                            <th class="p-3 text-left text-xs font-medium text-gray-500 uppercase">Club</th>
                            <th class="p-3 text-left text-xs font-medium text-gray-500 uppercase">N° Licence</th>
                            <th class="p-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                            <th class="p-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('clinical.table_status') }}</th>
                            <th class="p-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Expiration') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($licenses as $license)
                        <tr>
                            <td class="p-3">{{ $license->player?->full_name ?? '—' }}</td>
                            <td class="p-3">{{ $license->club?->name ?? '—' }}</td>
                            <td class="p-3">{{ $license->license_number ?? '—' }}</td>
                            <td class="p-3">{{ $license->license_type ?? '—' }}</td>
                            <td class="p-3">
                                <span class="px-2 py-1 text-xs rounded-full {{ $license->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                    {{ $license->status ?? 'inconnu' }}
                                </span>
                            </td>
                            <td class="p-3">{{ $license->expiry_date?->format('d/m/Y') ?? '—' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="p-4 border-t border-gray-200">
                {{ $licenses->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
