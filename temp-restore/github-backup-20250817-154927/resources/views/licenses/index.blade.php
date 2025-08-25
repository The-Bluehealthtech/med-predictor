@extends('layouts.app')

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