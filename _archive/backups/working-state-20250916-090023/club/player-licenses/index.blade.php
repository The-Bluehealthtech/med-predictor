@extends('layouts.app')
@section('title', 'My Player License Requests')
@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold mb-4">My Player License Requests</h1>
    @if(session('success'))
        <div class="bg-green-100 text-green-800 p-2 mb-4">{{ session('success') }}</div>
    @endif
    <table class="min-w-full bg-white border">
        <thead>
            <tr>
                <th>Player</th>
                <th>Status</th>
                <th>Submitted</th>
                <th>Rejection Reason</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        @forelse($licenses as $license)
            <tr>
                <td>
                    <div class="flex items-center">
                        <div class="flex-shrink-0 h-10 w-10">
                            @if($license->player && ($license->player->player_face_url ?? $license->player->player_picture_url))
                                <img class="h-10 w-10 rounded-full object-cover" src="{{ $license->player->player_face_url ?? $license->player->player_picture_url }}" alt="{{ $license->player->full_name }}">
                            @else
                                <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
                                    <span class="text-blue-600 font-semibold text-sm">
                                        {{ $license->player ? substr($license->player->first_name, 0, 1) . substr($license->player->last_name, 0, 1) : '?' }}
                                    </span>
                                </div>
                            @endif
                        </div>
                        <div class="ml-3">
                            {{ $license->player->full_name ?? '-' }}
                        </div>
                    </div>
                </td>
                <td>
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
                </td>
                <td>{{ $license->created_at->format('d/m/Y') }}</td>
                <td>
                    @if($license->status === 'revoked' && $license->rejection_reason)
                        <div class="text-sm text-red-700 max-w-xs truncate" title="{{ $license->rejection_reason }}">
                            {{ Str::limit($license->rejection_reason, 50) }}
                        </div>
                    @else
                        <span class="text-gray-400">-</span>
                    @endif
                </td>
                <td>
                    <a href="{{ route('club.player-licenses.show', $license->id) }}" class="text-blue-600 hover:underline">View</a>
                </td>
            </tr>
        @empty
            <tr><td colspan="5">No license requests found.</td></tr>
        @endforelse
        </tbody>
    </table>
    <div class="mt-4">{{ $licenses->links() }}</div>
</div>
@endsection 