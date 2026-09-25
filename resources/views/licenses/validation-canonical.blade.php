@extends('layouts.app')

@section('title', 'Validation des licences')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Validation des licences</h1>
            <p class="text-sm text-gray-600">Licences joueurs enregistrées dans player_licenses.</p>
        </div>
        <a href="{{ route('modules.licenses.index') }}" class="text-blue-600 hover:text-blue-800">
            ← Gestion des licences
        </a>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-4">
            <div class="text-sm text-gray-500">Total</div>
            <div class="text-2xl font-bold">{{ $totalCount }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <div class="text-sm text-gray-500">En attente</div>
            <div class="text-2xl font-bold">{{ $pendingCount }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <div class="text-sm text-gray-500">Actives</div>
            <div class="text-2xl font-bold">{{ $approvedCount }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <div class="text-sm text-gray-500">Rejetées / révoquées</div>
            <div class="text-2xl font-bold">{{ $rejectedCount }}</div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Joueur</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Club</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Expiration</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Statut</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($licenses as $license)
                        <tr>
                            <td class="px-4 py-3">
                                {{ trim(($license->player?->first_name ?? '') . ' ' . ($license->player?->last_name ?? '')) ?: 'N/A' }}
                            </td>
                            <td class="px-4 py-3">{{ $license->club?->name ?? 'N/A' }}</td>
                            <td class="px-4 py-3">{{ $license->license_type_text }}</td>
                            <td class="px-4 py-3">{{ $license->expiry_date?->format('d/m/Y') ?? 'N/A' }}</td>
                            <td class="px-4 py-3">{{ $license->status_text }}</td>
                            <td class="px-4 py-3">
                                @if($license->isPending())
                                    <div class="flex gap-2">
                                        <button type="button" onclick="approveLicense({{ $license->id }})" class="text-green-700 hover:underline">
                                            Approuver
                                        </button>
                                        <button type="button" onclick="rejectLicense({{ $license->id }})" class="text-red-700 hover:underline">
                                            Rejeter
                                        </button>
                                    </div>
                                @else
                                    <span class="text-gray-400">—</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-gray-500">
                                Aucune licence dans le périmètre autorisé.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4">{{ $licenses->links() }}</div>
    </div>
</div>

<script>
async function licenseAction(url, options) {
    const response = await fetch(url, {
        ...options,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        }
    });

    const data = await response.json();

    if (!response.ok || !data.success) {
        throw new Error(data.message || 'Action impossible');
    }

    window.location.reload();
}

function approveLicense(id) {
    if (!confirm('Approuver cette licence ?')) return;

    licenseAction('/licenses/' + id + '/approve', {
        method: 'PATCH'
    }).catch(function (error) {
        alert(error.message);
    });
}

function rejectLicense(id) {
    const reason = prompt('Motif du rejet :');
    if (!reason) return;

    licenseAction('/licenses/' + id + '/reject', {
        method: 'PATCH',
        body: JSON.stringify({ rejection_reason: reason })
    }).catch(function (error) {
        alert(error.message);
    });
}
</script>
@endsection
