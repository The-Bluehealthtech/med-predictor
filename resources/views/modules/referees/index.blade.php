@extends('layouts.app')

@section('title', 'Gestion des arbitres')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-semibold">Gestion des arbitres</h1>
            <p class="text-gray-600">Arbitres enregistrés et affectations réelles.</p>
        </div>
        <a href="{{ route('admin.referee-assignments') }}" class="bg-blue-700 text-white px-4 py-2 rounded">Gérer les affectations</a>
    </div>
    @php
        $referees = \App\Models\User::where('role', 'referee')->orderBy('name')->get();
    @endphp
    <div class="bg-white rounded shadow overflow-x-auto">
        <table class="min-w-full divide-y">
            <thead><tr class="text-left bg-gray-50">
                <th class="p-3">Nom</th><th class="p-3">Email</th><th class="p-3">Statut enregistré</th>
            </tr></thead>
            <tbody class="divide-y">
                @forelse($referees as $referee)
                    <tr>
                        <td class="p-3">{{ $referee->name }}</td>
                        <td class="p-3">{{ $referee->email }}</td>
                        <td class="p-3">{{ $referee->status ?: 'Non renseigné' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="3" class="p-4 text-gray-600">Aucun arbitre enregistré.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
