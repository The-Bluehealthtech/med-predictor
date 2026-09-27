@extends('layouts.app')

@section('title', __('competitions.resultats_classements_page.page_title'))

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">
                <i class="fas fa-trophy text-yellow-600 mr-3"></i>
                {{ __('competitions.resultats_classements_page.heading') }}
            </h1>
            <p class="text-gray-600 mt-2">{{ __('competitions.resultats_classements_page.subtitle') }}</p>
        </div>
        <a href="{{ route('modules.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-2 rounded-lg font-medium transition-colors">
            <i class="fas fa-arrow-left mr-2"></i>{{ __('competitions.resultats_classements_page.back_to_modules') }}
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
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('competitions.resultats_classements_page.col_pos') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('competitions.resultats_classements_page.col_team') }}</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('competitions.resultats_classements_page.col_pts') }}</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('competitions.resultats_classements_page.col_played') }}</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('competitions.resultats_classements_page.col_wins') }}</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('competitions.resultats_classements_page.col_draws') }}</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('competitions.resultats_classements_page.col_losses') }}</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('competitions.resultats_classements_page.col_goals_for') }}</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('competitions.resultats_classements_page.col_goals_against') }}</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('competitions.resultats_classements_page.col_diff') }}</th>
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
            <h2 class="text-lg font-semibold text-gray-900 mb-4">{{ __('competitions.resultats_classements_page.rankings_heading') }}</h2>
            <div class="text-center py-8">
                <div class="text-gray-400 text-6xl mb-4">
                    <i class="fas fa-chart-line"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">{{ __('competitions.resultats_classements_page.no_ranking_available') }}</h3>
                <p class="text-gray-500">{{ __('competitions.resultats_classements_page.no_ranking_available_text') }}</p>
            </div>
        </div>
    @endif
</div>
@endsection