@extends('layouts.app')

@section('title', __('competitions.club_entries_page.page_title'))

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold text-gray-900 mb-6">{{ __('competitions.club_entries_page.heading') }}</h1>
    
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-xl font-semibold mb-4">{{ __('competitions.club_entries_page.registered_competitions') }}</h2>
        
        @if($engagements->count() > 0)
            <div class="space-y-4">
                @foreach($engagements as $engagement)
                <div class="border rounded-lg p-4 hover:shadow-md transition-shadow">
                    <div class="flex justify-between items-start mb-3">
                        <div>
                            <h3 class="font-semibold text-lg">{{ $engagement['nom'] }}</h3>
                            <div class="flex items-center space-x-4 text-sm text-gray-600 mt-1">
                                <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded-full text-xs">
                                    {{ $engagement['type'] }}
                                </span>
                                <span class="bg-green-100 text-green-800 px-2 py-1 rounded-full text-xs">
                                    {{ $engagement['categorie'] }}
                                </span>
                                <span class="bg-purple-100 text-purple-800 px-2 py-1 rounded-full text-xs">
                                    {{ $engagement['discipline'] }}
                                </span>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                {{ $engagement['statut_raw'] === \App\Models\Competition::STATUS_PUBLISHED ? 'bg-green-100 text-green-800' :
                                   ($engagement['statut_raw'] === \App\Models\Competition::STATUS_VALIDATED ? 'bg-blue-100 text-blue-800' : 'bg-yellow-100 text-yellow-800') }}">
                                {{ $engagement['statut'] }}
                            </span>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                        <div>
                            <span class="text-gray-500">{{ __('competitions.club_entries_page.season_label') }}</span>
                            <span class="font-medium">{{ $engagement['saison'] }}</span>
                        </div>
                        <div>
                            <span class="text-gray-500">{{ __('competitions.club_entries_page.ranking_label') }}</span>
                            <span class="font-medium">{{ $engagement['classement'] }}{{ __('competitions.club_entries_page.ranking_suffix') }}</span>
                        </div>
                        <div>
                            <span class="text-gray-500">{{ __('competitions.club_entries_page.points_label') }}</span>
                            <span class="font-medium">{{ $engagement['points'] }}</span>
                        </div>
                        <div>
                            <span class="text-gray-500">{{ __('competitions.club_entries_page.matches_label') }}</span>
                            <span class="font-medium">{{ $engagement['matchs_joues'] }}/{{ $engagement['nb_matchs'] }}</span>
                        </div>
                    </div>
                    
                    <div class="mt-3 pt-3 border-t border-gray-200">
                        <div class="flex justify-between items-center text-sm text-gray-600">
                            <div>
                                <span class="text-gray-500">{{ __('competitions.club_entries_page.association_label') }}</span>
                                <span class="font-medium">{{ $engagement['association'] }}</span>
                            </div>
                            <div>
                                <span class="text-gray-500">{{ __('competitions.club_entries_page.confederation_label') }}</span>
                                <span class="font-medium">{{ $engagement['confederation'] }}</span>
                            </div>
                        </div>
                        @if($engagement['fifa_connect_id'])
                        <div class="mt-2 flex items-center justify-between">
                            <div class="flex items-center text-xs text-gray-500">
                                <i class="fas fa-link mr-1"></i>
                                <span>FIFA Connect ID: {{ $engagement['fifa_connect_id'] }}</span>
                            </div>
                            @if($engagement['fifa_sync_enabled'])
                            <span class="inline-flex items-center px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">
                                <i class="fas fa-sync-alt mr-1"></i>
                                {{ __('competitions.club_entries_page.sync_enabled') }}
                            </span>
                            @endif
                        </div>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        @else
            <p class="text-gray-600">{{ __('competitions.club_entries_page.no_competition_found') }}</p>
        @endif
    </div>
</div>
@endsection