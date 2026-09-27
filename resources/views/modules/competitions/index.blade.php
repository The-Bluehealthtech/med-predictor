@extends('layouts.app')

@section('title', __('Module Compétitions - FIT'))

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- En-tête du Module -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">
            <i class="fas fa-trophy text-yellow-500 mr-3"></i>
            {{ __('competitions.home.module_title') }}
        </h1>
        <p class="text-gray-600">{{ __('competitions.home.module_subtitle') }}</p>
        
        <!-- Statistiques en temps réel -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mt-6">
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-trophy text-blue-600 text-2xl"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-blue-600">{{ __('competitions.supervision_page.active_competitions') }}</p>
                        <p class="text-2xl font-bold text-blue-900">{{ $competitions->count() }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-futbol text-green-600 text-2xl"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-green-600">{{ __('competitions.calendar_page.stat_total_matches') }}</p>
                        <p class="text-2xl font-bold text-green-900">{{ $totalMatches }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-clock text-yellow-600 text-2xl"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-yellow-600">{{ __('À Venir') }}</p>
                        <p class="text-2xl font-bold text-yellow-900">{{ $upcomingMatches }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-purple-50 border border-purple-200 rounded-lg p-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-check-circle text-purple-600 text-2xl"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-purple-600">{{ __('competitions.calendar_page.stat_completed') }}</p>
                        <p class="text-2xl font-bold text-purple-900">{{ $completedMatches }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Prochains Matchs et Résultats Récents -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Prochains Matchs -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">
                <i class="fas fa-clock text-yellow-500 mr-2"></i>
                {{ __('competitions.calendar_page.upcoming_matches') }}
            </h2>
            @if($nextMatches->count() > 0)
                <div class="space-y-3">
                    @foreach($nextMatches as $match)
                        <div class="border border-gray-200 rounded-lg p-3 hover:bg-gray-50">
                            <div class="flex items-center justify-between">
                                <div class="flex-1">
                                    <div class="font-medium text-gray-900">
                                        {{ $match->homeTeam->club->name ?? 'TBD' }} vs {{ $match->awayTeam->club->name ?? 'TBD' }}
                                    </div>
                                    <div class="text-sm text-gray-600">
                                        {{ $match->competition->name ?? __('Compétition') }} • {{ $match->match_date ?? 'N/A' }}
                                    </div>
                                </div>
                                <div class="text-right">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                        {{ $match->match_time ?? 'N/A' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500 text-center py-4">{{ __('competitions.fixtures_page.no_upcoming_match') }}</p>
            @endif
        </div>
        
        <!-- Résultats Récents -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">
                <i class="fas fa-check-circle text-green-500 mr-2"></i>{{ __('Résultats Récents') }}</h2>
            @if($recentResults->count() > 0)
                <div class="space-y-3">
                    @foreach($recentResults as $match)
                        <div class="border border-gray-200 rounded-lg p-3 hover:bg-gray-50">
                            <div class="flex items-center justify-between">
                                <div class="flex-1">
                                    <div class="font-medium text-gray-900">
                                        {{ $match->homeTeam->club->name ?? 'TBD' }} {{ $match->home_score ?? 0 }} - {{ $match->away_score ?? 0 }} {{ $match->awayTeam->club->name ?? 'TBD' }}
                                    </div>
                                    <div class="text-sm text-gray-600">
                                        {{ $match->competition->name ?? __('Compétition') }} • {{ $match->match_date ?? 'N/A' }}
                                    </div>
                                </div>
                                <div class="text-right">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        {{ __('clinical.status_completed') }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500 text-center py-4">{{ __('Aucun résultat récent') }}</p>
            @endif
        </div>
    </div>

    <!-- Navigation par Rôle -->
    <div class="mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">{{ __('competitions.home.access_by_role') }}</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Côté Club -->
                <div class="border border-blue-200 rounded-lg p-6 bg-blue-50">
                    <h3 class="text-lg font-semibold text-blue-900 mb-4">
                        <i class="fas fa-users text-blue-600 mr-2"></i>
                        {{ __('competitions.home.club_side') }}
                    </h3>
                    <div class="space-y-3">
                        <a href="{{ route('competitions.club.engagements') }}" 
                           class="block p-3 bg-white rounded-lg hover:bg-blue-100 transition-colors">
                            <div class="flex items-center">
                                <i class="fas fa-clipboard-list text-blue-600 mr-3"></i>
                                <div>
                                    <div class="font-medium text-gray-900">{{ __('competitions.home.my_engagements') }}</div>
                                    <div class="text-sm text-gray-600">{{ __('competitions.home.club_engagements_desc') }}</div>
                                </div>
                            </div>
                        </a>
                        
                        <a href="{{ route('competitions.club.effectif') }}" 
                           class="block p-3 bg-white rounded-lg hover:bg-blue-100 transition-colors">
                            <div class="flex items-center">
                                <i class="fas fa-user-check text-green-600 mr-3"></i>
                                <div>
                                    <div class="font-medium text-gray-900">{{ __('competitions.home.eligible_squad') }}</div>
                                    <div class="text-sm text-gray-600">{{ __('competitions.home.eligible_squad_desc') }}</div>
                                </div>
                            </div>
                        </a>
                        
                        <a href="{{ route('competitions.club.classement') }}" 
                           class="block p-3 bg-white rounded-lg hover:bg-blue-100 transition-colors">
                            <div class="flex items-center">
                                <i class="fas fa-trophy text-yellow-600 mr-3"></i>
                                <div>
                                    <div class="font-medium text-gray-900">{{ __("Classements") }}</div>
                                    <div class="text-sm text-gray-600">{{ __('Suivi des performances et classements') }}</div>
                                </div>
                            </div>
                        </a>
                        
                        <a href="{{ route('competitions.club.fixtures') }}" 
                           class="block p-3 bg-white rounded-lg hover:bg-blue-100 transition-colors">
                            <div class="flex items-center">
                                <i class="fas fa-calendar-alt text-purple-600 mr-3"></i>
                                <div>
                                    <div class="font-medium text-gray-900">Fixtures</div>
                                    <div class="text-sm text-gray-600">{{ __('Calendrier complet des matchs') }}</div>
                                </div>
                            </div>
                        </a>
                        
                        <a href="{{ route('competitions.club.calendrier') }}" 
                           class="block p-3 bg-white rounded-lg hover:bg-blue-100 transition-colors">
                            <div class="flex items-center">
                                <i class="fas fa-calendar-alt text-purple-600 mr-3"></i>
                                <div>
                                    <div class="font-medium text-gray-900">{{ __('competitions.home.calendar_matches') }}</div>
                                    <div class="text-sm text-gray-600">{{ __('competitions.home.calendar_matches_desc') }}</div>
                                </div>
                            </div>
                        </a>
                        
                        <a href="{{ route('competitions.club.feuilles-match') }}" 
                           class="block p-3 bg-white rounded-lg hover:bg-blue-100 transition-colors">
                            <div class="flex items-center">
                                <i class="fas fa-file-alt text-orange-600 mr-3"></i>
                                <div>
                                    <div class="font-medium text-gray-900">{{ __('competitions.home.match_sheets') }}</div>
                                    <div class="text-sm text-gray-600">{{ __('competitions.home.match_sheets_desc') }}</div>
                                </div>
                            </div>
                        </a>
                        
                        <a href="{{ route('competitions.club.discipline') }}" 
                           class="block p-3 bg-white rounded-lg hover:bg-blue-100 transition-colors">
                            <div class="flex items-center">
                                <i class="fas fa-gavel text-red-600 mr-3"></i>
                                <div>
                                    <div class="font-medium text-gray-900">Discipline & Notifications</div>
                                    <div class="text-sm text-gray-600">{{ __('competitions.home.discipline_notifications_desc') }}</div>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>

                <!-- Côté Association/Ligue -->
                <div class="border border-green-200 rounded-lg p-6 bg-green-50">
                    <h3 class="text-lg font-semibold text-green-900 mb-4">
                        <i class="fas fa-building text-green-600 mr-2"></i>
                        {{ __('competitions.home.association_side') }}
                    </h3>
                    <div class="space-y-3">
                        <a href="{{ route('competitions.association.supervision') }}" 
                           class="block p-3 bg-white rounded-lg hover:bg-green-100 transition-colors">
                            <div class="flex items-center">
                                <i class="fas fa-eye text-green-600 mr-3"></i>
                                <div>
                                    <div class="font-medium text-gray-900">{{ __('competitions.home.supervised_competitions') }}</div>
                                    <div class="text-sm text-gray-600">{{ __('auth.role_summary_associations_desc') }}</div>
                                </div>
                            </div>
                        </a>
                        
                        <a href="{{ route('competitions.association.engagements-clubs') }}" 
                           class="block p-3 bg-white rounded-lg hover:bg-green-100 transition-colors">
                            <div class="flex items-center">
                                <i class="fas fa-clipboard-check text-blue-600 mr-3"></i>
                                <div>
                                    <div class="font-medium text-gray-900">{{ __('Engagements des Clubs') }}</div>
                                    <div class="text-sm text-gray-600">{{ __('competitions.home.club_entries_desc') }}</div>
                                </div>
                            </div>
                        </a>
                        
                        <a href="{{ route('competitions.association.calendrier-global') }}" 
                           class="block p-3 bg-white rounded-lg hover:bg-green-100 transition-colors">
                            <div class="flex items-center">
                                <i class="fas fa-calendar text-purple-600 mr-3"></i>
                                <div>
                                    <div class="font-medium text-gray-900">{{ __('competitions.home.global_calendar') }}</div>
                                    <div class="text-sm text-gray-600">{{ __('competitions.home.global_calendar_desc') }}</div>
                                </div>
                            </div>
                        </a>
                        
                        <a href="{{ route('competitions.association.resultats-classements') }}" 
                           class="block p-3 bg-white rounded-lg hover:bg-green-100 transition-colors">
                            <div class="flex items-center">
                                <i class="fas fa-trophy text-yellow-600 mr-3"></i>
                                <div>
                                    <div class="font-medium text-gray-900">{{ __('Résultats & Classements') }}</div>
                                    <div class="text-sm text-gray-600">{{ __('competitions.home.results_rankings_desc') }}</div>
                                </div>
                            </div>
                        </a>
                        
                        <a href="{{ route('competitions.association.classement') }}" 
                           class="block p-3 bg-white rounded-lg hover:bg-green-100 transition-colors">
                            <div class="flex items-center">
                                <i class="fas fa-chart-bar text-orange-600 mr-3"></i>
                                <div>
                                    <div class="font-medium text-gray-900">{{ __('Classements Détaillés') }}</div>
                                    <div class="text-sm text-gray-600">{{ __('Tableaux professionnels avec statistiques') }}</div>
                                </div>
                            </div>
                        </a>
                        
                        <a href="{{ route('competitions.association.fixtures') }}" 
                           class="block p-3 bg-white rounded-lg hover:bg-green-100 transition-colors">
                            <div class="flex items-center">
                                <i class="fas fa-calendar-alt text-purple-600 mr-3"></i>
                                <div>
                                    <div class="font-medium text-gray-900">Fixtures</div>
                                    <div class="text-sm text-gray-600">{{ __('Calendrier complet des matchs') }}</div>
                                </div>
                            </div>
                        </a>
                        
                        <a href="{{ route('competitions.association.discipline-sanctions') }}" 
                           class="block p-3 bg-white rounded-lg hover:bg-green-100 transition-colors">
                            <div class="flex items-center">
                                <i class="fas fa-balance-scale text-red-600 mr-3"></i>
                                <div>
                                    <div class="font-medium text-gray-900">Discipline & Sanctions</div>
                                    <div class="text-sm text-gray-600">{{ __('competitions.home.discipline_sanctions_desc') }}</div>
                                </div>
                            </div>
                        </a>
                        
                        <a href="{{ route('competitions.association.rapports-statistiques') }}" 
                           class="block p-3 bg-white rounded-lg hover:bg-green-100 transition-colors">
                            <div class="flex items-center">
                                <i class="fas fa-chart-bar text-indigo-600 mr-3"></i>
                                <div>
                                    <div class="font-medium text-gray-900">{{ __('competitions.home.reports_stats') }}</div>
                                    <div class="text-sm text-gray-600">{{ __('competitions.home.reports_stats_desc') }}</div>
                                </div>
                            </div>
                        </a>
                        
                        <a href="{{ route('competitions.association.designation-arbitres') }}" 
                           class="block p-3 bg-white rounded-lg hover:bg-green-100 transition-colors">
                            <div class="flex items-center">
                                <i class="fas fa-whistle text-orange-600 mr-3"></i>
                                <div>
                                    <div class="font-medium text-gray-900">{{ __('competitions.designation_arbitres_page.heading') }}</div>
                                    <div class="text-sm text-gray-600">{{ __('Gestion des arbitres et désignations') }}</div>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Informations sur le Module -->
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-xl font-semibold text-gray-900 mb-4">{{ __('competitions.home.about_module') }}</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="text-center">
                <div class="bg-blue-100 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-3">
                    <i class="fas fa-sync-alt text-blue-600 text-xl"></i>
                </div>
                <h3 class="font-semibold text-gray-900 mb-2">Synchronisation FIFA Connect</h3>
                <p class="text-sm text-gray-600">{{ __('competitions.home.fifa_sync_desc') }}</p>
            </div>
            
            <div class="text-center">
                <div class="bg-green-100 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-3">
                    <i class="fas fa-shield-alt text-green-600 text-xl"></i>
                </div>
                <h3 class="font-semibold text-gray-900 mb-2">{{ __('competitions.home.audit_compliance') }}</h3>
                <p class="text-sm text-gray-600">{{ __('competitions.home.audit_compliance_desc') }}</p>
            </div>
            
            <div class="text-center">
                <div class="bg-purple-100 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-3">
                    <i class="fas fa-mobile-alt text-purple-600 text-xl"></i>
                </div>
                <h3 class="font-semibold text-gray-900 mb-2">{{ __('competitions.home.push_notifications') }}</h3>
                <p class="text-sm text-gray-600">{{ __('competitions.home.push_notifications_desc') }}</p>
            </div>
        </div>
    </div>
</div>

@push('styles')
<link rel="stylesheet" href="{{ asset('css/competitions.css') }}">
@endpush
@endsection