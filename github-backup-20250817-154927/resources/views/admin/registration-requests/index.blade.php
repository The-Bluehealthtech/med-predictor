@extends('layouts.app')

@section('title', 'Portal Registration Requests Management')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-bold">Portal Registration Requests</h2>
                    <div class="flex space-x-2">
                        <select id="statusFilter" class="border border-gray-300 rounded-md px-3 py-2 text-sm">
                            <option value="">Tous les statuts</option>
                            <option value="pending">En attente</option>
                            <option value="approved">Approuvées</option>
                            <option value="rejected">Rejetées</option>
                        </select>
                        <select id="associationFilter" class="border border-gray-300 rounded-md px-3 py-2 text-sm">
                            <option value="">Toutes les associations</option>
                            <option value="france">France - FFF</option>
                            <option value="england">England - FA</option>
                            <option value="spain">Spain - RFEF</option>
                            <option value="germany">Germany - DFB</option>
                            <!-- Ajoute d'autres associations si besoin -->
                        </select>
                    </div>
                </div>

                <!-- Statistiques -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-8 w-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-blue-600">Total</p>
                                <p class="text-2xl font-semibold text-blue-900" id="totalCount">{{ $requests->total() }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-8 w-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-yellow-600">En attente</p>
                                <p class="text-2xl font-semibold text-yellow-900" id="pendingCount">{{ $requests->where('status', 'pending')->count() }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-8 w-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-green-600">Approuvées</p>
                                <p class="text-2xl font-semibold text-green-900" id="approvedCount">{{ $requests->where('status', 'approved')->count() }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-8 w-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-red-600">Rejetées</p>
                                <p class="text-2xl font-semibold text-red-900" id="rejectedCount">{{ $requests->where('status', 'rejected')->count() }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tableau des demandes -->
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nom</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($requests as $request)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $request->id }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $request->first_name }} {{ $request->last_name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $request->email }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $request->status }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $request->created_at ? $request->created_at->format('d/m/Y H:i') : '' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-4 text-center text-gray-500">Aucune demande d'inscription trouvée.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="mt-6">
                    {{ $requests->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 