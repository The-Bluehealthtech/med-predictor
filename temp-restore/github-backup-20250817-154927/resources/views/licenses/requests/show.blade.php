@extends('layouts.app')
@section('title', 'Détail demande de licence')
@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold mb-4">Demande #{{ $request->id }}</h1>
    <div class="mb-4">
        <strong>Type :</strong> {{ $request->type }}<br>
        <strong>Statut :</strong> {{ ucfirst($request->status) }}<br>
        <strong>Club :</strong> {{ $request->club->name ?? '-' }}<br>
        <strong>Joueur :</strong> {{ $request->player->name ?? '-' }}<br>
        <strong>Staff :</strong> {{ $request->staff->name ?? '-' }}<br>
        <strong>Commentaire association :</strong> {{ $request->association_comment ?? '-' }}<br>
    </div>
    <div class="mb-4">
        <h2 class="font-semibold">Pièces jointes</h2>
        <ul>
            @foreach($request->attachments as $att)
                <li>
                    <a href="{{ route('license-requests.download-attachment', $att->id) }}" class="text-blue-600 hover:underline">{{ basename($att->file_path) }}</a> ({{ $att->type }})
                </li>
            @endforeach
        </ul>
        @if(auth()->user()->isClub() && $request->status === 'pending')
        <form action="{{ route('license-requests.upload-attachment', $request->id) }}" method="POST" enctype="multipart/form-data" class="mt-2">
            @csrf
            <input type="file" name="attachment" required accept=".pdf,.jpg,.jpeg,.png">
            <button type="submit" class="bg-blue-600 text-white px-2 py-1 rounded">Ajouter</button>
        </form>
        @endif
    </div>
    <div class="mb-4">
        <h2 class="font-semibold">Historique des statuts</h2>
        <ul>
            @foreach($request->statuses as $status)
                <li>{{ ucfirst($status->status) }} par {{ $status->user->name ?? 'N/A' }} le {{ $status->created_at->format('d/m/Y H:i') }} @if($status->comment) - {{ $status->comment }} @endif</li>
            @endforeach
        </ul>
    </div>
    @if(auth()->user()->isAssociation() && $request->status === 'pending')
    <div class="mb-4">
        <form action="{{ route('license-requests.validate', $request->id) }}" method="POST" class="inline">
            @csrf
            <input type="text" name="comment" placeholder="Commentaire (optionnel)" class="border rounded p-1">
            <button type="submit" class="bg-green-600 text-white px-2 py-1 rounded">Valider</button>
        </form>
        <form action="{{ route('license-requests.refuse', $request->id) }}" method="POST" class="inline ml-2">
            @csrf
            <input type="text" name="comment" placeholder="Motif du refus" class="border rounded p-1" required>
            <button type="submit" class="bg-red-600 text-white px-2 py-1 rounded">Refuser</button>
        </form>
    </div>
    @endif
    <a href="{{ route('license-requests.index') }}" class="text-blue-600 hover:underline">Retour à la liste</a>
</div>
@endsection 