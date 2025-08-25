@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-blue-100 p-4 rounded-lg">
                <h3 class="text-lg font-semibold text-blue-800">Total Competitions</h3>
                <p class="text-2xl font-bold text-blue-600">{{ $stats['total'] ?? 0 }}</p>
            </div>
            <div class="bg-green-100 p-4 rounded-lg">
                <h3 class="text-lg font-semibold text-green-800">Active</h3>
                <p class="text-2xl font-bold text-green-600">{{ $stats['active'] ?? 0 }}</p>
            </div>
            <div class="bg-yellow-100 p-4 rounded-lg">
                <h3 class="text-lg font-semibold text-yellow-800">Upcoming</h3>
                <p class="text-2xl font-bold text-yellow-600">{{ $stats['upcoming'] ?? 0 }}</p>
            </div>
            <div class="bg-red-100 p-4 rounded-lg">
                <h3 class="text-lg font-semibold text-red-800">Completed</h3>
                <p class="text-2xl font-bold text-red-600">{{ $stats['completed'] ?? 0 }}</p>
            </div>
        </div>

        <!-- Competitions Table -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Competitions</h3>
                    <form action="{{ route('competitions.sync-all') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Sync All
                        </button>
                    </form>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Format</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">FIFA ID</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($competitions as $competition)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    @php
                                        $name = $competition['name'] ?? $competition->name ?? 'N/A';
                                        if (is_array($name)) $name = 'N/A';
                                        if (is_object($name)) $name = 'N/A';
                                    @endphp
                                    {{ $name }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    @php
                                        $format = $competition['format'] ?? $competition->format ?? 'N/A';
                                        if (is_array($format)) $format = 'N/A';
                                        if (is_object($format)) $format = 'N/A';
                                    @endphp
                                    {{ $format }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    @php
                                        $fifa_id = $competition['fifa_connect_id'] ?? $competition->fifa_connect_id ?? 'N/A';
                                        if (is_array($fifa_id)) $fifa_id = 'N/A';
                                        if (is_object($fifa_id)) $fifa_id = 'N/A';
                                    @endphp
                                    {{ $fifa_id }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    @php
                                        $type = $competition['type'] ?? $competition->type ?? 'N/A';
                                        if (is_array($type)) $type = 'N/A';
                                        if (is_object($type)) $type = 'N/A';
                                    @endphp
                                    {{ $type }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    @php
                                        $status = $competition['status'] ?? $competition->status ?? 'N/A';
                                        if (is_array($status)) $status = 'N/A';
                                        if (is_object($status)) $status = 'N/A';
                                    @endphp
                                    {{ ucfirst($status) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <a href="{{ route('competitions.show', $competition['id'] ?? $competition->id ?? 1) }}" class="text-indigo-600 hover:text-indigo-900">View</a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 