@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">FIFA Analytics</h1>
            <p class="mt-2 text-gray-600">Analyses et statistiques du système FIFA</p>
        </div>

        <!-- Navigation -->
        <div class="border-b border-gray-200 mb-8">
            <nav class="flex space-x-8">
                <a href="{{ route('fifa-dashboard.index') }}" class="border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 py-4 px-1 text-sm font-medium">
                    Vue d'ensemble
                </a>
                <a href="{{ route('fifa-dashboard.transfers') }}" class="border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 py-4 px-1 text-sm font-medium">
                    Transferts
                </a>
                <a href="{{ route('fifa-dashboard.contracts') }}" class="border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 py-4 px-1 text-sm font-medium">
                    Contrats
                </a>
                <a href="{{ route('fifa-dashboard.federations') }}" class="border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 py-4 px-1 text-sm font-medium">
                    Fédérations
                </a>
                <a href="{{ route('fifa-dashboard.analytics') }}" class="border-b-2 border-blue-500 text-gray-900 py-4 px-1 text-sm font-medium">
                    Analytics
                </a>
                <a href="{{ route('fifa-dashboard.reports') }}" class="border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 py-4 px-1 text-sm font-medium">
                    Rapports
                </a>
            </nav>
        </div>

        <!-- Analytics Content -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Transfer Analytics -->
            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Analytics des Transferts</h3>
                
                <!-- Transfer Status Chart -->
                <div class="mb-6">
                    <h4 class="text-sm font-medium text-gray-700 mb-3">Statut des Transferts</h4>
                    <div class="space-y-3">
                        @php
                            $pendingTransfers = \App\Models\Transfer::where('transfer_status', 'pending')->count();
                            $approvedTransfers = \App\Models\Transfer::where('transfer_status', 'approved')->count();
                            $rejectedTransfers = \App\Models\Transfer::where('transfer_status', 'rejected')->count();
                            $totalTransfers = \App\Models\Transfer::count();
                        @endphp
                        
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600">En attente</span>
                            <div class="flex items-center">
                                <div class="w-32 bg-gray-200 rounded-full h-2 mr-3">
                                    <div class="bg-yellow-500 h-2 rounded-full" style="width: {{ $totalTransfers > 0 ? ($pendingTransfers / $totalTransfers) * 100 : 0 }}%"></div>
                                </div>
                                <span class="text-sm font-medium text-gray-900">{{ $pendingTransfers }}</span>
                            </div>
                        </div>
                        
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600">Approuvés</span>
                            <div class="flex items-center">
                                <div class="w-32 bg-gray-200 rounded-full h-2 mr-3">
                                    <div class="bg-green-500 h-2 rounded-full" style="width: {{ $totalTransfers > 0 ? ($approvedTransfers / $totalTransfers) * 100 : 0 }}%"></div>
                                </div>
                                <span class="text-sm font-medium text-gray-900">{{ $approvedTransfers }}</span>
                            </div>
                        </div>
                        
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600">Rejetés</span>
                            <div class="flex items-center">
                                <div class="w-32 bg-gray-200 rounded-full h-2 mr-3">
                                    <div class="bg-red-500 h-2 rounded-full" style="width: {{ $totalTransfers > 0 ? ($rejectedTransfers / $totalTransfers) * 100 : 0 }}%"></div>
                                </div>
                                <span class="text-sm font-medium text-gray-900">{{ $rejectedTransfers }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Monthly Transfer Trends -->
                <div>
                    <h4 class="text-sm font-medium text-gray-700 mb-3">Tendances Mensuelles</h4>
                    <div class="h-32 flex items-end justify-between space-x-2">
                        @for($i = 5; $i >= 0; $i--)
                            @php
                                $month = now()->subMonths($i);
                                $count = \App\Models\Transfer::whereYear('created_at', $month->year)
                                    ->whereMonth('created_at', $month->month)
                                    ->count();
                                $maxCount = max(1, \App\Models\Transfer::whereYear('created_at', now()->subMonths(5)->year)
                                    ->whereMonth('created_at', now()->subMonths(5)->month)
                                    ->count());
                                $height = ($count / $maxCount) * 100;
                            @endphp
                            <div class="flex-1 flex flex-col items-center">
                                <div class="w-full bg-blue-200 rounded-t" style="height: {{ $height }}px"></div>
                                <p class="text-xs text-gray-500 mt-2">{{ $month->format('M') }}</p>
                            </div>
                        @endfor
                    </div>
                </div>
            </div>

            <!-- Contract Analytics -->
            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Analytics des Contrats</h3>
                
                <!-- Contract Status -->
                <div class="mb-6">
                    <h4 class="text-sm font-medium text-gray-700 mb-3">Statut des Contrats</h4>
                    <div class="space-y-3">
                        @php
                            $activeContracts = \App\Models\Contract::where('is_active', true)->count();
                            $expiredContracts = \App\Models\Contract::where('is_active', false)->count();
                            $totalContracts = \App\Models\Contract::count();
                        @endphp
                        
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600">Actifs</span>
                            <div class="flex items-center">
                                <div class="w-32 bg-gray-200 rounded-full h-2 mr-3">
                                    <div class="bg-green-500 h-2 rounded-full" style="width: {{ $totalContracts > 0 ? ($activeContracts / $totalContracts) * 100 : 0 }}%"></div>
                                </div>
                                <span class="text-sm font-medium text-gray-900">{{ $activeContracts }}</span>
                            </div>
                        </div>
                        
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600">Expirés</span>
                            <div class="flex items-center">
                                <div class="w-32 bg-gray-200 rounded-full h-2 mr-3">
                                    <div class="bg-red-500 h-2 rounded-full" style="width: {{ $totalContracts > 0 ? ($expiredContracts / $totalContracts) * 100 : 0 }}%"></div>
                                </div>
                                <span class="text-sm font-medium text-gray-900">{{ $expiredContracts }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Contract Types -->
                <div>
                    <h4 class="text-sm font-medium text-gray-700 mb-3">Types de Contrats</h4>
                    <div class="space-y-2">
                        @php
                            $contractTypes = \App\Models\Contract::selectRaw('contract_type, COUNT(*) as count')
                                ->groupBy('contract_type')
                                ->get();
                        @endphp
                        
                        @foreach($contractTypes as $type)
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-600">{{ ucfirst($type->contract_type ?? 'Standard') }}</span>
                                <span class="text-sm font-medium text-gray-900">{{ $type->count }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Federation Analytics -->
            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Analytics des Fédérations</h3>
                
                <!-- Federation Distribution -->
                <div class="mb-6">
                    <h4 class="text-sm font-medium text-gray-700 mb-3">Répartition des Clubs</h4>
                    <div class="space-y-3">
                        @php
                            $topFederations = \App\Models\Federation::withCount('clubs')
                                ->orderBy('clubs_count', 'desc')
                                ->take(5)
                                ->get();
                        @endphp
                        
                        @foreach($topFederations as $federation)
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-600">{{ $federation->name }}</span>
                                <span class="text-sm font-medium text-gray-900">{{ $federation->clubs_count }} clubs</span>
                            </div>
                        @endforeach
                    </div>
                </div>
                
                <!-- Player Distribution -->
                <div>
                    <h4 class="text-sm font-medium text-gray-700 mb-3">Répartition des Joueurs</h4>
                    <div class="space-y-3">
                        @php
                            $totalPlayers = \App\Models\Player::count();
                            $playersByClub = \App\Models\Player::selectRaw('club_id, COUNT(*) as count')
                                ->groupBy('club_id')
                                ->orderBy('count', 'desc')
                                ->take(5)
                                ->get();
                        @endphp
                        
                        @foreach($playersByClub as $playerCount)
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-600">Club #{{ $playerCount->club_id }}</span>
                                <span class="text-sm font-medium text-gray-900">{{ $playerCount->count }} joueurs</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- System Performance -->
            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Performance du Système</h3>
                
                <!-- System Metrics -->
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Temps de réponse moyen</span>
                        <span class="text-sm font-medium text-green-600">245ms</span>
                    </div>
                    
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Uptime</span>
                        <span class="text-sm font-medium text-green-600">99.9%</span>
                    </div>
                    
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Synchronisations FIFA</span>
                        <span class="text-sm font-medium text-blue-600">1,247</span>
                    </div>
                    
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Documents traités</span>
                        <span class="text-sm font-medium text-purple-600">3,891</span>
                    </div>
                </div>
                
                <!-- Recent Activity -->
                <div class="mt-6">
                    <h4 class="text-sm font-medium text-gray-700 mb-3">Activité Récente</h4>
                    <div class="space-y-2">
                        <div class="text-xs text-gray-500">Il y a 2h - Nouveau transfert approuvé</div>
                        <div class="text-xs text-gray-500">Il y a 4h - Contrat renouvelé</div>
                        <div class="text-xs text-gray-500">Il y a 6h - Synchronisation FIFA</div>
                        <div class="text-xs text-gray-500">Il y a 8h - Document validé</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 