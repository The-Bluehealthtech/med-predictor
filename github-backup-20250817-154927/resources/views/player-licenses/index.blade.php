@extends('layouts.app')
@section('title', 'Player License Requests')
@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold mb-4">Player License Requests (Pending & Explanation Requested)</h1>
    @if(session('success'))
        <div class="bg-green-100 text-green-800 p-2 mb-4">{{ session('success') }}</div>
    @endif
    <table class="min-w-full bg-white border">
        <thead>
            <tr>
                <th>ID</th>
                <th>Player</th>
                <th>Club</th>
                <th>Status</th>
                <th>Requested By</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        @forelse($licenses as $license)
            <tr>
                <td>{{ $license->id }}</td>
                <td>{{ $license->player->full_name ?? '-' }}</td>
                <td>{{ $license->club->name ?? '-' }}</td>
                <td>
                    @if($license->status === 'pending')
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
                <td>{{ $license->requested_by ? optional(\App\Models\User::find($license->requested_by))->name : '-' }}</td>
                <td>
                    <a href="{{ route('player-licenses.show', $license->id) }}" class="text-blue-600 hover:underline">Review</a>
                </td>
            </tr>
        @empty
            <tr><td colspan="6">No pending license requests.</td></tr>
        @endforelse
        </tbody>
    </table>
    <div class="mt-4">{{ $licenses->links() }}</div>
</div>
@endsection 