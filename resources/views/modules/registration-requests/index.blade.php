@extends('layouts.app')

@section('title', 'Demandes d\'inscription - FIT Platform')

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-6">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">📝 Demandes d'inscription / de licence</h1>
                    <p class="text-sm text-gray-600">Suivi des demandes de licence FIFA Connect</p>
                </div>
                <a href="{{ route('modules.index') }}" class="text-gray-600 hover:text-gray-900 text-sm font-medium">← Retour aux Modules</a>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-xl font-semibold text-gray-800">Demandes ({{ $requests->total() }})</h2>
            </div>

            @if($requests->isEmpty())
            <div class="p-6 text-center text-gray-500">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">Aucune demande enregistrée</h3>
            </div>
            @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="p-3 text-left text-xs font-medium text-gray-500 uppercase">Demandeur</th>
                            <th class="p-3 text-left text-xs font-medium text-gray-500 uppercase">Club</th>
                            <th class="p-3 text-left text-xs font-medium text-gray-500 uppercase">Type de demande</th>
                            <th class="p-3 text-left text-xs font-medium text-gray-500 uppercase">Type de licence</th>
                            <th class="p-3 text-left text-xs font-medium text-gray-500 uppercase">Statut</th>
                            <th class="p-3 text-left text-xs font-medium text-gray-500 uppercase">Soumise le</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($requests as $request)
                        <tr>
                            <td class="p-3">{{ $request->full_name ?? trim(($request->first_name ?? '').' '.($request->last_name ?? '')) ?: '—' }}</td>
                            <td class="p-3">{{ $request->currentClub?->name ?? '—' }}</td>
                            <td class="p-3">{{ $request->request_type ?? '—' }}</td>
                            <td class="p-3">{{ $request->license_type ?? '—' }}</td>
                            <td class="p-3">
                                <span class="px-2 py-1 text-xs rounded-full {{ $request->request_status === 'approved' ? 'bg-green-100 text-green-800' : ($request->request_status === 'rejected' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                                    {{ $request->request_status ?? 'draft' }}
                                </span>
                            </td>
                            <td class="p-3">{{ $request->created_at?->format('d/m/Y') ?? '—' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="p-4 border-t border-gray-200">
                {{ $requests->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
