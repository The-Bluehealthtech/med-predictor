@extends('layouts.app')

@section('title', 'Module Compétitions - FIT')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- En-tête du Module -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">
            <i class="fas fa-trophy text-yellow-500 mr-3"></i>
            Module Compétitions
        </h1>
        <p class="text-gray-600">Gestion complète des compétitions avec intégration FIFA Connect</p>
    </div>

    <!-- Navigation par Rôle -->
    <div class="mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Accès par Rôle</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Côté Club -->
                <div class="border border-blue-200 rounded-lg p-6 bg-blue-50">
                    <h3 class="text-lg font-semibold text-blue-900 mb-4">
                        <i class="fas fa-users text-blue-600 mr-2"></i>
                        Côté Club
                    </h3>
                    <div class="space-y-3">
                        <a href="{{ route('competitions.club.engagements') }}" 
                           class="block p-3 bg-white rounded-lg hover:bg-blue-100 transition-colors">
                            <div class="flex items-center">
                                <i class="fas fa-clipboard-list text-blue-600 mr-3"></i>
                                <div>
                                    <div class="font-medium text-gray-900">Mes Engagements</div>
                                    <div class="text-sm text-gray-600">Compétitions où le club est inscrit</div>
                                </div>
                            </div>
                        </a>
                        
                        <a href="{{ route('competitions.club.effectif') }}" 
                           class="block p-3 bg-white rounded-lg hover:bg-blue-100 transition-colors">
                            <div class="flex items-center">
                                <i class="fas fa-user-check text-green-600 mr-3"></i>
                                <div>
                                    <div class="font-medium text-gray-900">Effectif Éligible</div>
                                    <div class="text-sm text-gray-600">Joueurs autorisés avec vérifications</div>
                                </div>
                            </div>
                        </a>
                        
                        <a href="{{ route('competitions.club.calendrier') }}" 
                           class="block p-3 bg-white rounded-lg hover:bg-blue-100 transition-colors">
                            <div class="flex items-center">
                                <i class="fas fa-calendar-alt text-purple-600 mr-3"></i>
                                <div>
                                    <div class="font-medium text-gray-900">Calendrier & Matchs</div>
                                    <div class="text-sm text-gray-600">Planning des rencontres</div>
                                </div>
                            </div>
                        </a>
                        
                        <a href="{{ route('competitions.club.feuilles-match') }}" 
                           class="block p-3 bg-white rounded-lg hover:bg-blue-100 transition-colors">
                            <div class="flex items-center">
                                <i class="fas fa-file-alt text-orange-600 mr-3"></i>
                                <div>
                                    <div class="font-medium text-gray-900">Feuilles de Match</div>
                                    <div class="text-sm text-gray-600">Préparation et soumission</div>
                                </div>
                            </div>
                        </a>
                        
                        <a href="{{ route('competitions.club.discipline') }}" 
                           class="block p-3 bg-white rounded-lg hover:bg-blue-100 transition-colors">
                            <div class="flex items-center">
                                <i class="fas fa-gavel text-red-600 mr-3"></i>
                                <div>
                                    <div class="font-medium text-gray-900">Discipline & Notifications</div>
                                    <div class="text-sm text-gray-600">Suivi sanctions et alertes</div>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>

                <!-- Côté Association/Ligue -->
                <div class="border border-green-200 rounded-lg p-6 bg-green-50">
                    <h3 class="text-lg font-semibold text-green-900 mb-4">
                        <i class="fas fa-building text-green-600 mr-2"></i>
                        Côté Association/Ligue
                    </h3>
                    <div class="space-y-3">
                        <a href="{{ route('competitions.association.supervision') }}" 
                           class="block p-3 bg-white rounded-lg hover:bg-green-100 transition-colors">
                            <div class="flex items-center">
                                <i class="fas fa-eye text-green-600 mr-3"></i>
                                <div>
                                    <div class="font-medium text-gray-900">Compétitions Supervisées</div>
                                    <div class="text-sm text-gray-600">Gestion des compétitions</div>
                                </div>
                            </div>
                        </a>
                        
                        <a href="{{ route('competitions.association.engagements-clubs') }}" 
                           class="block p-3 bg-white rounded-lg hover:bg-green-100 transition-colors">
                            <div class="flex items-center">
                                <i class="fas fa-clipboard-check text-blue-600 mr-3"></i>
                                <div>
                                    <div class="font-medium text-gray-900">Engagements des Clubs</div>
                                    <div class="text-sm text-gray-600">Validation des inscriptions</div>
                                </div>
                            </div>
                        </a>
                        
                        <a href="{{ route('competitions.association.calendrier-global') }}" 
                           class="block p-3 bg-white rounded-lg hover:bg-green-100 transition-colors">
                            <div class="flex items-center">
                                <i class="fas fa-calendar text-purple-600 mr-3"></i>
                                <div>
                                    <div class="font-medium text-gray-900">Calendrier Global</div>
                                    <div class="text-sm text-gray-600">Planning centralisé</div>
                                </div>
                            </div>
                        </a>
                        
                        <a href="{{ route('competitions.association.resultats-classements') }}" 
                           class="block p-3 bg-white rounded-lg hover:bg-green-100 transition-colors">
                            <div class="flex items-center">
                                <i class="fas fa-trophy text-yellow-600 mr-3"></i>
                                <div>
                                    <div class="font-medium text-gray-900">Résultats & Classements</div>
                                    <div class="text-sm text-gray-600">Compilation automatique</div>
                                </div>
                            </div>
                        </a>
                        
                        <a href="{{ route('competitions.association.discipline-sanctions') }}" 
                           class="block p-3 bg-white rounded-lg hover:bg-green-100 transition-colors">
                            <div class="flex items-center">
                                <i class="fas fa-balance-scale text-red-600 mr-3"></i>
                                <div>
                                    <div class="font-medium text-gray-900">Discipline & Sanctions</div>
                                    <div class="text-sm text-gray-600">Validation des sanctions</div>
                                </div>
                            </div>
                        </a>
                        
                        <a href="{{ route('competitions.association.rapports-statistiques') }}" 
                           class="block p-3 bg-white rounded-lg hover:bg-green-100 transition-colors">
                            <div class="flex items-center">
                                <i class="fas fa-chart-bar text-indigo-600 mr-3"></i>
                                <div>
                                    <div class="font-medium text-gray-900">Rapports & Statistiques</div>
                                    <div class="text-sm text-gray-600">Export PDF/Excel</div>
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
        <h2 class="text-xl font-semibold text-gray-900 mb-4">À propos du Module Compétitions</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="text-center">
                <div class="bg-blue-100 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-3">
                    <i class="fas fa-sync-alt text-blue-600 text-xl"></i>
                </div>
                <h3 class="font-semibold text-gray-900 mb-2">Synchronisation FIFA Connect</h3>
                <p class="text-sm text-gray-600">Intégration automatique avec les données FIFA Connect pour une gestion conforme</p>
            </div>
            
            <div class="text-center">
                <div class="bg-green-100 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-3">
                    <i class="fas fa-shield-alt text-green-600 text-xl"></i>
                </div>
                <h3 class="font-semibold text-gray-900 mb-2">Audit & Conformité</h3>
                <p class="text-sm text-gray-600">Toutes les actions sont horodatées et auditées pour la conformité</p>
            </div>
            
            <div class="text-center">
                <div class="bg-purple-100 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-3">
                    <i class="fas fa-mobile-alt text-purple-600 text-xl"></i>
                </div>
                <h3 class="font-semibold text-gray-900 mb-2">Notifications Push</h3>
                <p class="text-sm text-gray-600">Alertes automatiques pour les suspensions, matchs, etc.</p>
            </div>
        </div>
    </div>
</div>

@push('styles')
<link rel="stylesheet" href="{{ asset('css/competitions.css') }}">
@endpush
@endsection