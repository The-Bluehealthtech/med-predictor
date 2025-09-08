@extends('layouts.app')

@section('title', 'Premier League Rankings')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">{{ $competition->name ?? 'Premier League' }} - Final Standings</h1>
                        @if(isset($latestRanking))
                            <p class="text-gray-600 mt-1">Round {{ $latestRanking->round }} - {{ \Carbon\Carbon::parse($latestRanking->created_at)->format('d/m/Y H:i') }}</p>
                        @endif
                    </div>
                    <div class="text-right">
                        <div class="text-sm text-gray-500">Season</div>
                        <div class="text-lg font-semibold">{{ $competition->season ?? '2024/25' }}</div>
                    </div>
                </div>

                <!-- Champions League Qualification -->
                <div class="mb-6 p-4 bg-blue-50 border-l-4 border-blue-400">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-blue-700">
                                <strong>Champions League:</strong> Positions 1-4 | <strong>Europa League:</strong> Position 5 | <strong>Relegation:</strong> Positions 18-20
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Standings Table -->
                @if($rankingsArray && count($rankingsArray) > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pos</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Team</th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">P</th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">W</th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">D</th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">L</th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">GF</th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">GA</th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">GD</th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Pts</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($rankingsArray as $ranking)
                                    <tr class="{{ $ranking['position'] <= 4 ? 'bg-green-50' : ($ranking['position'] == 5 ? 'bg-purple-50' : ($ranking['position'] >= 18 ? 'bg-red-50' : 'hover:bg-gray-50')) }}">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            <div class="flex items-center">
                                                @if($ranking['position'] == 1)
                                                    <span class="text-yellow-600 mr-2">🏆</span>
                                                @elseif($ranking['position'] <= 4)
                                                    <span class="text-blue-600 mr-2">🔵</span>
                                                @elseif($ranking['position'] == 5)
                                                    <span class="text-purple-600 mr-2">🟣</span>
                                                @elseif($ranking['position'] >= 18)
                                                    <span class="text-red-600 mr-2">🔴</span>
                                                @endif
                                                {{ $ranking['position'] }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-10 w-10">
                                                    <div class="h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center">
                                                        <span class="text-sm font-medium text-gray-600">
                                                            {{ substr($ranking['team_name'], 0, 2) }}
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="ml-4">
                                                    <div class="text-sm font-medium text-gray-900">{{ $ranking['team_name'] }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-center">{{ $ranking['played'] }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-center">{{ $ranking['won'] }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-center">{{ $ranking['drawn'] }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-center">{{ $ranking['lost'] }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-center">{{ $ranking['goals_for'] }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-center">{{ $ranking['goals_against'] }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-center">
                                            <span class="{{ $ranking['goal_difference'] > 0 ? 'text-green-600' : ($ranking['goal_difference'] < 0 ? 'text-red-600' : 'text-gray-600') }}">
                                                {{ $ranking['goal_difference'] > 0 ? '+' : '' }}{{ $ranking['goal_difference'] }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900 text-center">{{ $ranking['points'] }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Legend -->
                    <div class="mt-6 grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div class="flex items-center">
                            <div class="w-4 h-4 bg-green-50 border border-green-200 mr-2"></div>
                            <span class="text-sm text-gray-600">Champions League (1-4)</span>
                        </div>
                        <div class="flex items-center">
                            <div class="w-4 h-4 bg-purple-50 border border-purple-200 mr-2"></div>
                            <span class="text-sm text-gray-600">Europa League (5)</span>
                        </div>
                        <div class="flex items-center">
                            <div class="w-4 h-4 bg-red-50 border border-red-200 mr-2"></div>
                            <span class="text-sm text-gray-600">Relegation (18-20)</span>
                        </div>
                        <div class="flex items-center">
                            <div class="w-4 h-4 bg-white border border-gray-200 mr-2"></div>
                            <span class="text-sm text-gray-600">Mid-table (6-17)</span>
                        </div>
                    </div>
                @else
                    <div class="text-center py-8">
                        <div class="mb-4">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Aucun classement disponible</h3>
                        <p class="text-gray-500 mb-4">Les données de classement ne sont pas encore disponibles pour cette compétition.</p>
                        <a href="{{ route('dashboard') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                            </svg>
                            Retour au Tableau de Bord
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection 