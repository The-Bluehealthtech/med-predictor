<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des Licences - Système de Licences</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-8">
    <div class="max-w-7xl mx-auto">
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-bold text-gray-800">🏆 Liste des Licences</h1>
            <a href="{{ route('license.upload.photo.form') }}" 
               class="bg-blue-600 text-white px-6 py-3 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                📤 Nouvelle Licence + Photo
            </a>
        </div>

        <!-- Filtres -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <h2 class="text-xl font-semibold text-gray-700 mb-4">🔍 Filtres</h2>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label for="club_filter" class="block text-sm font-medium text-gray-700 mb-2">Club</label>
                    <select id="club_filter" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                        <option value="">Tous les clubs</option>
                        @foreach($clubs ?? [] as $club)
                            <option value="{{ $club->id }}">{{ $club->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="type_filter" class="block text-sm font-medium text-gray-700 mb-2">Type</label>
                    <select id="type_filter" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                        <option value="">Tous les types</option>
                        <option value="amateur">Amateur</option>
                        <option value="semi_pro">Semi-Professionnel</option>
                        <option value="professional">Professionnel</option>
                        <option value="international">International</option>
                    </select>
                </div>
                <div>
                    <label for="status_filter" class="block text-sm font-medium text-gray-700 mb-2">Statut</label>
                    <select id="status_filter" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                        <option value="">Tous les statuts</option>
                        <option value="active">Active</option>
                        <option value="expired">Expirée</option>
                        <option value="suspended">Suspendue</option>
                        <option value="revoked">Révoquée</option>
                    </select>
                </div>
                <div>
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-2">Recherche</label>
                    <input type="text" id="search" placeholder="Nom du joueur..." 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md">
                </div>
            </div>
        </div>

        <!-- Liste des Licences -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Photo
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Joueur
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Club
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Type
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Statut
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Dates
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($licenses as $license)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($license->photo)
                                    <img src="{{ asset('storage/' . $license->photo->photo_path) }}" 
                                         alt="Photo {{ $license->player->first_name }}" 
                                         class="w-16 h-16 object-cover rounded-lg">
                                @else
                                    <div class="w-16 h-16 bg-gray-200 rounded-lg flex items-center justify-center">
                                        <span class="text-gray-500 text-xs">Pas de photo</span>
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">
                                    {{ $license->player->first_name }} {{ $license->player->last_name }}
                                </div>
                                <div class="text-sm text-gray-500">
                                    #{{ $license->license_number }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $license->club->name }}</div>
                                <div class="text-sm text-gray-500">{{ $license->club->city }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    @if($license->license_type === 'professional') bg-blue-100 text-blue-800
                                    @elseif($license->license_type === 'international') bg-purple-100 text-purple-800
                                    @elseif($license->license_type === 'semi_pro') bg-yellow-100 text-yellow-800
                                    @else bg-green-100 text-green-800
                                    @endif">
                                    {{ $license->license_type_text }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    @if($license->status === 'active') bg-green-100 text-green-800
                                    @elseif($license->status === 'expired') bg-red-100 text-red-800
                                    @elseif($license->status === 'suspended') bg-yellow-100 text-yellow-800
                                    @else bg-gray-100 text-gray-800
                                    @endif">
                                    {{ $license->status_text }}
                                </span>
                                @if($license->is_expired)
                                    <div class="text-xs text-red-600 mt-1">Expirée</div>
                                @elseif($license->days_remaining <= 30)
                                    <div class="text-xs text-yellow-600 mt-1">{{ $license->days_remaining }} jours restants</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <div>Début: {{ $license->start_date->format('d/m/Y') }}</div>
                                <div>Fin: {{ $license->end_date->format('d/m/Y') }}</div>
                                @if($license->issued_at)
                                    <div class="text-xs">Émise: {{ $license->issued_at->format('d/m/Y') }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex space-x-2">
                                    <a href="{{ route('license.upload.photo.form') }}?player_id={{ $license->player_id }}&club_id={{ $license->club_id }}" 
                                       class="text-blue-600 hover:text-blue-900">Modifier</a>
                                    @if($license->photo)
                                        <form action="{{ route('license.photo.delete', $license->photo->id) }}" 
                                              method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="text-red-600 hover:text-red-900"
                                                    onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette photo ?')">
                                                Supprimer
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                                Aucune licence trouvée. 
                                <a href="{{ route('license.upload.photo.form') }}" class="text-blue-600 hover:text-blue-800">
                                    Créer la première licence
                                </a>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination -->
        @if($licenses->hasPages())
        <div class="mt-8">
            {{ $licenses->links() }}
        </div>
        @endif
    </div>

    <script>
        // Filtres dynamiques
        document.addEventListener('DOMContentLoaded', function() {
            const filters = ['club_filter', 'type_filter', 'status_filter', 'search'];
            
            filters.forEach(filterId => {
                const filter = document.getElementById(filterId);
                if (filter) {
                    filter.addEventListener('change', applyFilters);
                    filter.addEventListener('input', applyFilters);
                }
            });
        });

        function applyFilters() {
            const clubFilter = document.getElementById('club_filter')?.value || '';
            const typeFilter = document.getElementById('type_filter')?.value || '';
            const statusFilter = document.getElementById('status_filter')?.value || '';
            const searchFilter = document.getElementById('search')?.value || '';

            // Construire l'URL avec les filtres
            const params = new URLSearchParams();
            if (clubFilter) params.append('club', clubFilter);
            if (typeFilter) params.append('type', typeFilter);
            if (statusFilter) params.append('status', statusFilter);
            if (searchFilter) params.append('search', searchFilter);

            // Rediriger avec les filtres
            const url = new URL(window.location);
            url.search = params.toString();
            window.location.href = url.toString();
        }
    </script>
</body>
</html> 
@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex items-center justify-between mb-4">
        <h1 class="text-2xl font-bold">Licences</h1>
        <a href="{{ route('licenses.create') }}" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">Créer une licence</a>
    </div>
    <form method="GET" class="mb-4 flex flex-wrap gap-4 items-center">
        <div>
            <label for="type" class="text-sm font-medium">Type :</label>
            <select name="type" id="type" class="border border-gray-300 rounded px-2 py-1">
                <option value="">Tous</option>
                <option value="Joueur" @if(request('type')=='Joueur') selected @endif>Joueur</option>
                <option value="Staff" @if(request('type')=='Staff') selected @endif>Staff</option>
                <option value="Médical" @if(request('type')=='Médical') selected @endif>Médical</option>
            </select>
        </div>
        <div>
            <label for="status" class="text-sm font-medium">Statut :</label>
            <select name="status" id="status" class="border border-gray-300 rounded px-2 py-1">
                <option value="">Tous</option>
                <option value="Active" @if(request('status')=='Active') selected @endif>Active</option>
                <option value="Inactive" @if(request('status')=='Inactive') selected @endif>Inactive</option>
            </select>
        </div>
        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Filtrer</button>
    </form>
    <div>
        Total licences : {{ $licenses->total() }}
    </div>
    <table class="min-w-full divide-y divide-gray-200 mt-4">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nom</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($licenses as $license)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap">{{ $license->id }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">{{ $license->name ?? $license->full_name ?? '-' }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">{{ $license->type ?? '-' }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">{{ $license->status ?? '-' }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <a href="{{ route('licenses.edit', $license) }}" class="text-blue-600 hover:underline mr-2">Éditer</a>
                        <form action="{{ route('licenses.destroy', $license) }}" method="POST" style="display:inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:underline" onclick="return confirm('Supprimer cette licence ?')">Supprimer</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="px-6 py-4 text-center text-gray-500">Aucune licence trouvée.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
    <div class="mt-6">
        {{ $licenses->appends(request()->query())->links() }}
    </div>
</div>
@endsection 