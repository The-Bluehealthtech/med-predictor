@extends('layouts.app')

@section('title', 'Résultats & Classements - Association')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">
                <i class="fas fa-trophy text-yellow-600 mr-3"></i>
                Résultats & Classements
            </h1>
            <p class="text-gray-600 mt-2">Compilation automatique des résultats</p>
        </div>
        <a href="{{ route('modules.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-2 rounded-lg font-medium transition-colors">
            <i class="fas fa-arrow-left mr-2"></i>Retour aux Modules
        </a>
    </div>

    @if(count($classements) > 0)
        @foreach($classements as $classement)
            <div class="bg-white rounded-lg shadow p-6 mb-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-6">{{ $classement['competition'] }}</h2>
                
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pos</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Équipe</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Pts</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">M</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">V</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">N</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">D</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">BP</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">BC</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Diff</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($classement['equipes'] as $equipe)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        @if($equipe['position'] <= 3)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                @if($equipe['position'] == 1) bg-yellow-100 text-yellow-800
                                                @elseif($equipe['position'] == 2) bg-gray-100 text-gray-800
                                                @else bg-orange-100 text-orange-800
                                                @endif">
                                                {{ $equipe['position'] }}
                                            </span>
                                        @else
                                            {{ $equipe['position'] }}
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ $equipe['nom'] }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-center font-bold text-gray-900">
                                        {{ $equipe['points'] }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-500">
                                        {{ $equipe['matchs'] }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-center text-green-600 font-medium">
                                        {{ $equipe['victoires'] }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-center text-yellow-600 font-medium">
                                        {{ $equipe['nuls'] }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-center text-red-600 font-medium">
                                        {{ $equipe['defaites'] }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-500">
                                        {{ $equipe['buts_pour'] }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-500">
                                        {{ $equipe['buts_contre'] }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-center font-medium
                                        @if($equipe['difference'] > 0) text-green-600
                                        @elseif($equipe['difference'] < 0) text-red-600
                                        @else text-gray-500
                                        @endif">
                                        {{ $equipe['difference'] > 0 ? '+' : '' }}{{ $equipe['difference'] }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endforeach
    @else
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Classements</h2>
            <div class="text-center py-8">
                <div class="text-gray-400 text-6xl mb-4">
                    <i class="fas fa-chart-line"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Aucun classement disponible</h3>
                <p class="text-gray-500">Les classements apparaîtront ici une fois les matchs joués.</p>
            </div>
        </div>
    @endif
</div>
@endsection