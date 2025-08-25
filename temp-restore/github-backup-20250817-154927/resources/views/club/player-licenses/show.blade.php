@extends('layouts.app')
@section('title', 'Player License Request Details')
@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold mb-4">Player License Request #{{ $license->id }}</h1>
    <div class="mb-4 flex items-center">
        <div class="flex-shrink-0 h-16 w-16">
            @if($license->player && ($license->player->player_face_url ?? $license->player->player_picture_url))
                <img class="h-16 w-16 rounded-full object-cover" src="{{ $license->player->player_face_url ?? $license->player->player_picture_url }}" alt="{{ $license->player->full_name }}">
            @else
                <div class="h-16 w-16 rounded-full bg-blue-100 flex items-center justify-center">
                    <span class="text-blue-600 font-bold text-xl">
                        {{ $license->player ? substr($license->player->first_name, 0, 1) . substr($license->player->last_name, 0, 1) : '?' }}
                    </span>
                </div>
            @endif
        </div>
        <div class="ml-4">
            <strong>Player:</strong> {{ $license->player->full_name ?? '-' }}<br>
            <strong>Status:</strong> 
            @if($license->status === 'revoked')
                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                    Rejected
                </span>
            @elseif($license->status === 'approved')
                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                    Approved
                </span>
            @elseif($license->status === 'pending')
                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                    Pending
                </span>
            @elseif($license->status === 'justification_requested')
                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-orange-100 text-orange-800">
                    Explanation Requested
                </span>
            @else
                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">
                    {{ ucfirst($license->status) }}
                </span>
            @endif
            <br>
            <strong>Submitted:</strong> {{ $license->created_at->format('d/m/Y H:i') }}<br>
        </div>
    </div>
    
    @if($license->status === 'revoked' && $license->rejection_reason)
        <div class="bg-red-50 border-l-4 border-red-400 p-4 mb-4">
            <h3 class="font-bold text-red-700 mb-2">Rejection Reason</h3>
            <p class="text-red-800">{{ $license->rejection_reason }}</p>
            @if($license->approved_at)
                <p class="text-sm text-red-600 mt-2">Rejected on: {{ $license->approved_at->format('d/m/Y H:i') }}</p>
            @endif
        </div>
    @endif
    
    @if($license->status === 'justification_requested' && $license->rejection_reason)
        <div class="bg-orange-50 border-l-4 border-orange-400 p-4 mb-4">
            <h3 class="font-bold text-orange-700 mb-2">Explanation Requested</h3>
            <p class="text-orange-800">{{ $license->rejection_reason }}</p>
            @if($license->approved_at)
                <p class="text-sm text-orange-600 mt-2">Requested on: {{ $license->approved_at->format('d/m/Y H:i') }}</p>
            @endif
        </div>
    @endif
    
    @if($license->fraudAnalysis)
        <div class="bg-red-50 border-l-4 border-red-400 p-4 mb-4">
            <h3 class="font-bold text-red-700">Fraud Risk Analysis</h3>
            <p>Identity Risk Score: {{ $license->fraudAnalysis->identity_score ?? 'N/A' }} / 100</p>
            <p>Age Risk Score: {{ $license->fraudAnalysis->age_score ?? 'N/A' }} / 100</p>
            <ul>
                @foreach($license->fraudAnalysis->anomalies ?? [] as $anomaly)
                    <li>
                        <strong>{{ ucfirst($anomaly['type']) }}:</strong>
                        {{ $anomaly['description'] }} (Score: {{ $anomaly['score'] }})
                    </li>
                @endforeach
            </ul>
        </div>
    @endif
    @php $isSystemAdmin = Auth::user() && Auth::user()->role === 'system_admin'; @endphp
    @if($license->status === 'justification_requested' && (Auth::user()->club_id === $license->club_id || $isSystemAdmin))
        <a href="{{ route('player-licenses.request.edit', $license->id) }}" class="inline-block bg-yellow-500 text-white px-4 py-2 rounded font-semibold hover:bg-yellow-600 mb-4">Edit & Resubmit</a>
    @endif
    @if($isSystemAdmin)
        <form action="{{ route('player-licenses.approve', $license->id) }}" method="POST" class="inline-block mr-2">
            @csrf
            <button type="submit" class="bg-green-700 text-white px-4 py-2 rounded font-semibold hover:bg-green-800">Force Approve</button>
        </form>
        <form action="{{ route('player-licenses.reject', $license->id) }}" method="POST" class="inline-block">
            @csrf
            <input type="text" name="reason" placeholder="Rejection reason" class="border rounded p-1" required>
            <button type="submit" class="bg-red-700 text-white px-4 py-2 rounded font-semibold hover:bg-red-800">Force Reject</button>
        </form>
    @endif
    <a href="{{ route('club.player-licenses.index') }}" class="text-blue-600 hover:underline">Back to list</a>
</div>
@endsection 