@extends('layouts.app')
@section('title', 'Player License Review')
@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold mb-4">Player License #{{ $license->id }}</h1>
    <div class="mb-4 flex items-center space-x-4">
        {{-- Player Picture --}}
        <div class="flex-shrink-0">
            @if($license->player->player_picture)
                <img src="{{ asset('storage/' . $license->player->player_picture) }}" alt="{{ $license->player->full_name }}" class="w-20 h-20 rounded-full object-cover border-4 border-blue-100">
            @elseif($license->player->player_face_url)
                <img src="{{ $license->player->player_face_url }}" alt="{{ $license->player->full_name }}" class="w-20 h-20 rounded-full object-cover border-4 border-blue-100">
            @else
                <div class="w-20 h-20 bg-blue-100 rounded-full flex items-center justify-center border-4 border-blue-100">
                    <span class="text-blue-600 font-bold text-2xl">{{ $license->player->getInitials() }}</span>
                </div>
            @endif
        </div>
        <div>
            <strong>Player:</strong> {{ $license->player->full_name ?? '-' }}<br>
            <strong>Club:</strong> {{ $license->club->name ?? '-' }}<br>
            <strong>Status:</strong> {{ ucfirst($license->status) }}<br>
            <strong>Requested By:</strong> {{ $license->requested_by ? optional(\App\Models\User::find($license->requested_by))->name : '-' }}<br>
        </div>
    </div>
    @if(isset($analysis))
        <div class="bg-red-50 border-l-4 border-red-400 p-4 mb-4">
            <h3 class="font-bold text-red-700">Fraud Risk Analysis</h3>
            <p>Identity Risk Score: {{ $analysis['identity_score'] ?? 'N/A' }} / 100</p>
            <p>Age Risk Score: {{ $analysis['age_score'] ?? 'N/A' }} / 100</p>
            <ul>
                @foreach($analysis['anomalies'] ?? [] as $anomaly)
                    <li>
                        <strong>{{ ucfirst($anomaly['type']) }}:</strong>
                        {{ $anomaly['description'] }} (Score: {{ $anomaly['score'] }})
                    </li>
                @endforeach
            </ul>
            @if(($analysis['identity_score'] ?? 0) > 80 || ($analysis['age_score'] ?? 0) > 80)
                <div class="mt-2">
                    <button class="bg-yellow-500 text-white px-2 py-1 rounded">Request More Info</button>
                    <button class="bg-gray-700 text-white px-2 py-1 rounded">Quarantine</button>
                </div>
            @endif
            <details class="mt-2">
                <summary class="cursor-pointer text-xs text-gray-500">Show raw AI response (for audit/debug)</summary>
                <pre class="bg-gray-100 p-2 text-xs overflow-x-auto">{{ json_encode($analysis['ai_raw_response'] ?? $analysis, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
            </details>
        </div>
    @endif
    <div class="mb-4">
        <h2 class="font-semibold">Supporting Documents</h2>
        <ul>
            @forelse($license->player->documents as $doc)
                <li class="mb-1">
                    <span class="mr-1">{{ $doc->getDocumentTypeIcon() }}</span>
                    <strong>{{ ucfirst(str_replace('_', ' ', $doc->document_type)) }}:</strong>
                    <a href="{{ asset('storage/' . $doc->file_path) }}" target="_blank" class="text-blue-600 hover:underline">{{ $doc->title ?? $doc->file_name }}</a>
                    <span class="text-xs text-gray-500 ml-1">({{ $doc->file_type ?? $doc->mime_type }})</span>
                </li>
            @empty
                <li class="text-gray-500">No documents uploaded.</li>
            @endforelse
        </ul>
    </div>
    @if($license->status === 'pending' || $license->status === 'justification_requested')
    <div class="mb-4">
        <form action="{{ route('player-licenses.approve', $license->id) }}" method="POST" class="inline">
            @csrf
            <button type="submit" class="bg-green-600 text-white px-2 py-1 rounded">Approve</button>
        </form>
        <form action="{{ route('player-licenses.reject', $license->id) }}" method="POST" class="inline ml-2">
            @csrf
            <input type="text" name="reason" placeholder="Rejection reason" class="border rounded p-1" required>
            <button type="submit" class="bg-red-600 text-white px-2 py-1 rounded">Reject</button>
        </form>
        <form action="{{ route('player-licenses.request-explanation', $license->id) }}" method="POST" class="inline ml-2">
            @csrf
            <input type="text" name="explanation_request" placeholder="What information do you need?" class="border rounded p-1" required>
            <button type="submit" class="bg-yellow-500 text-white px-2 py-1 rounded">Request Explanation</button>
        </form>
        <form action="{{ route('player-licenses.reanalyze', $license->id) }}" method="POST" class="inline ml-2">
            @csrf
            <button type="submit" class="bg-blue-500 text-white px-2 py-1 rounded">Re-run AI Fraud Analysis</button>
        </form>
    </div>
    @endif
    <a href="{{ route('player-licenses.index') }}" class="text-blue-600 hover:underline">Back to list</a>
</div>
@endsection 