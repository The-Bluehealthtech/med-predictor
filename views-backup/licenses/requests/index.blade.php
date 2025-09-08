@extends('layouts.app')
@section('title', 'Demandes de licence')
@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold mb-4">Demandes de licence</h1>
    @if(session('success'))
        <div class="bg-green-100 text-green-800 p-2 mb-4">{{ session('success') }}</div>
    @endif
    <a href="{{ route('license-requests.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded mb-4 inline-block">Nouvelle demande</a>
    <table class="min-w-full bg-white border">
        <thead>
            <tr>
                <th>ID</th>
                <th>Type</th>
                <th>Statut</th>
                <th>Club</th>
                <th>Dernier changement</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        @forelse($requests as $req)
            <tr>
                <td>{{ $req->id }}</td>
                <td>{{ $req->type }}</td>
                <td>{{ ucfirst($req->status) }}</td>
                <td>{{ $req->club->name ?? '-' }}</td>
                <td>{{ $req->statuses->last()?->created_at?->format('d/m/Y H:i') }}</td>
                <td>
                    <a href="{{ route('license-requests.show', $req->id) }}" class="text-blue-600 hover:underline">Voir</a>
                    @if(auth()->user()->isClub() && $req->status === 'pending')
                        | <a href="{{ route('license-requests.edit', $req->id) }}" class="text-yellow-600 hover:underline">Modifier</a>
                        | <form action="{{ route('license-requests.destroy', $req->id) }}" method="POST" class="inline">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-red-600 hover:underline" onclick="return confirm('Supprimer ?')">Supprimer</button>
                        </form>
                    @endif
                </td>
            </tr>
        @empty
            <tr><td colspan="6">Aucune demande.</td></tr>
        @endforelse
        </tbody>
    </table>
    <div class="mt-4">{{ $requests->links() }}</div>
</div>
@endsection 