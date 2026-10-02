@extends('layouts.app')

@section('title', 'Modules - FIT Platform')

@section('content')
@php
    // Visibilité : la clinique pour les rôles médicaux (plus le secrétariat), chaque espace
    // des sélections pour les comptes qui ont sa permission RBAC. Une section sans carte est masquée.
    $authUser = auth()->user();
    $dtnAccess = app(\App\Services\Dtn\DtnAccess::class);
    $spaceVisible = [
        'dtn' => $authUser && $dtnAccess->isDtnSide($authUser),
        'club' => $authUser && $dtnAccess->isClubSide($authUser),
    ];
    $isMedical = $authUser && $authUser->hasAnyRole(['system_admin', 'association_medical', 'club_medical', 'doctor', 'team_doctor', 'medical_staff']);
    $moduleVisible = function (array $module) use ($spaceVisible, $isMedical, $authUser) {
        if (($module['category'] ?? null) === 'clinique') {
            return $isMedical || (($module['route'] ?? null) === 'secretary.dashboard' && ($authUser?->role === 'secretary'));
        }
        $group = $module['group'] ?? null;
        return isset($spaceVisible[$group]) ? $spaceVisible[$group] : true;
    };

    $visibleModules = (is_array($modules) && isset($modules[0]['name']))
        ? collect($modules)->filter(fn ($m) => $moduleVisible($m))->values()
        : collect($modules);
@endphp
<script>
// Filter functions - defined early to ensure availability
function showAllCategories(event) {
    const sections = document.querySelectorAll('.category-section');
    sections.forEach(section => {
        section.style.display = 'block';
    });
    
    const buttons = document.querySelectorAll('button[onclick*="filterByCategory"], button[onclick*="showAllCategories"]');
    buttons.forEach(button => {
        button.classList.remove('bg-blue-600', 'text-white');
        button.classList.add('bg-gray-200', 'text-gray-700');
    });
    
    if (event && event.target) {
        event.target.classList.remove('bg-gray-200', 'text-gray-700');
        event.target.classList.add('bg-blue-600', 'text-white');
    }
}

function filterByCategory(category, event) {
    const sections = document.querySelectorAll('.category-section');
    sections.forEach(section => {
        section.style.display = 'none';
    });
    
    const targetSections = document.querySelectorAll(`.category-section[data-category="${category}"]`);
    targetSections.forEach(section => {
        section.style.display = 'block';
    });
    
    const buttons = document.querySelectorAll('button[onclick*="filterByCategory"], button[onclick*="showAllCategories"]');
    buttons.forEach(button => {
        button.classList.remove('bg-blue-600', 'text-white');
        button.classList.add('bg-gray-200', 'text-gray-700');
    });
    
    if (event && event.target) {
        event.target.classList.remove('bg-gray-200', 'text-gray-700');
        event.target.classList.add('bg-blue-600', 'text-white');
    }
}
</script>

<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="flex items-center">
                            <img src="{{ asset('images/fit-logo.png') }}" alt="FIT Logo" class="w-10 h-10 mr-3">
                            <div>
                                <h1 class="text-2xl font-bold text-gray-900">
                                    Modules FIT
                                </h1>
                                <p class="text-sm text-gray-600">Football Intelligence & Tracking</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('dashboard') }}" class="text-gray-600 hover:text-gray-900 text-sm font-medium">{{ app()->getLocale() === 'en' ? '← Back to the dashboard' : '← Retour au Dashboard Général' }}</a>
                    @auth
                        <form method="POST" action="{{ route('logout') }}" class="inline-block">
                            @csrf
                            <button type="submit" 
                                    class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-semibold transition duration-300 ease-in-out transform hover:scale-105 shadow-lg flex items-center space-x-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                </svg>
                                <span>{{ app()->getLocale() === 'en' ? 'Log out' : 'Déconnexion' }}</span>
                            </button>
                        </form>
                    @endauth
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Welcome Section -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-8">
            <div class="p-6">
                <div class="text-center">
                    <h2 class="text-3xl font-bold text-gray-900 mb-4">{{ app()->getLocale() === 'en' ? 'Welcome to FIT' : 'Bienvenue sur la plateforme FIT' }}</h2>
                    <p class="text-lg text-gray-600 mb-6">
                        {{ app()->getLocale() === 'en' ? 'Choose a module to access the football management tools' : 'Sélectionnez un module pour accéder aux fonctionnalités de gestion du football' }}
                    </p>
                    <div class="flex justify-center space-x-4">
                        <div class="flex items-center text-sm text-gray-500">
                            <span class="w-2 h-2 bg-green-500 rounded-full mr-2"></span>
                            {{ app()->getLocale() === 'en' ? 'System operational' : 'Système opérationnel' }}
                        </div>
                        <div class="flex items-center text-sm text-gray-500">
                            <span class="w-2 h-2 bg-blue-500 rounded-full mr-2"></span>
                            {{ $visibleModules->count() }} {{ app()->getLocale() === 'en' ? 'modules available' : 'modules disponibles' }}
                        </div>
                        <div class="flex items-center text-sm text-gray-500">
                            <span class="w-2 h-2 bg-purple-500 rounded-full mr-2"></span>
                            {{ $visibleModules->pluck('category')->filter()->unique()->count() }} {{ app()->getLocale() === 'en' ? 'sections' : 'sections' }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modules organisés par catégories -->
        @php
            $isFlat = is_array($modules) && isset($modules[0]) && is_array($modules[0]) && array_key_exists('name', $modules[0]) && !array_key_exists('items', $modules[0]);
        @endphp

        @if($isFlat)
        @php
            // Quatre sections métier ; l'administration est découpée en sous-groupes.
            $categories = [
                'clinique' => ['name' => 'La clinique', 'description' => 'Staff médical : prise en charge, dossiers, bilans et secrétariat', 'icon' => 'pulse', 'tone' => 'bg-red-50 text-red-600 ring-red-100'],
                'performance' => ['name' => 'Le centre de performance', 'description' => 'Staff sportif : cockpit, analyses, charge et métriques FIT', 'icon' => 'chart', 'tone' => 'bg-blue-50 text-blue-600 ring-blue-100'],
                'selections' => ['name' => 'Les sélections nationales', 'description' => 'Relation club ↔ Direction technique nationale', 'icon' => 'flag', 'tone' => 'bg-indigo-50 text-indigo-600 ring-indigo-100'],
                'administration' => ['name' => 'L\'administration', 'description' => 'Organisations, sport, licences, finance et système', 'icon' => 'briefcase', 'tone' => 'bg-slate-100 text-slate-600 ring-slate-200'],
            ];
            // Teinte des pastilles d'icône, par couleur de carte (classes écrites en entier pour Tailwind).
            $iconTone = [
                'red' => 'bg-red-50 text-red-600', 'blue' => 'bg-blue-50 text-blue-600', 'indigo' => 'bg-indigo-50 text-indigo-600',
                'emerald' => 'bg-emerald-50 text-emerald-600', 'gray' => 'bg-slate-100 text-slate-600',
            ];
            $groupLabels = [
                'dtn' => 'Espace DTN — fédération', 'club' => 'Espace club',
                'organisations' => 'Organisations', 'sport' => 'Sport', 'licences' => 'Licences, transferts et FIFA',
                'finance' => 'Finance', 'systeme' => 'Système',
            ];
            $groupTone = ['dtn' => 'text-indigo-700', 'club' => 'text-emerald-700'];

            $groupedModules = [];
            foreach ($modules as $module) {
                if (!$moduleVisible($module)) {
                    continue;
                }
                $groupedModules[$module['category'] ?? 'administration'][] = $module;
            }
        @endphp

        <!-- Filtres par catégorie -->
        <div class="mb-8">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ app()->getLocale() === 'en' ? 'Filter by category' : 'Filtrer par catégorie' }}</h3>
                <div class="flex flex-wrap gap-2">
                    <button onclick="showAllCategories(event)" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        {{ app()->getLocale() === 'en' ? 'All categories' : 'Toutes les catégories' }}
                    </button>
                    @foreach($categories as $key => $category)
                    @continue(empty($groupedModules[$key]))
                    <button onclick="filterByCategory('{{ $key }}', event)" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors category-filter" data-category="{{ $key }}">
                        <span class="inline-flex items-center gap-2">@include('modules.partials.icon', ['name' => $category['icon'] ?? '', 'class' => 'w-4 h-4'])<span>{{ app()->getLocale() === 'en' ? (trans('modules_fit.categories')[$key] ?? $category['name']) : $category['name'] }}</span></span>
                    </button>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Modules par catégories -->
        @foreach($categories as $categoryKey => $categoryInfo)
        @if(isset($groupedModules[$categoryKey]) && count($groupedModules[$categoryKey]) > 0)
        <div class="category-section mb-6" data-category="{{ $categoryKey }}">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <!-- Category Header -->
                <div class="px-4 py-3 border-b border-gray-200 bg-gray-50 rounded-t-lg">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <span class="inline-flex items-center justify-center w-9 h-9 rounded-lg ring-1 mr-3 {{ $categoryInfo['tone'] ?? 'bg-slate-100 text-slate-600 ring-slate-200' }}">@include('modules.partials.icon', ['name' => $categoryInfo['icon'] ?? '', 'class' => 'w-5 h-5'])</span>
                            <h3 class="text-xl font-semibold text-gray-900">{{ app()->getLocale() === 'en' ? (trans('modules_fit.categories')[$categoryKey] ?? $categoryInfo['name']) : $categoryInfo['name'] }}</h3>
                        </div>
                        <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm font-medium">
                            {{ count($groupedModules[$categoryKey]) }} modules
                        </span>
                    </div>
                </div>
                
                <!-- Modules Grid -->
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @php $previousGroup = null; @endphp
                @foreach($groupedModules[$categoryKey] as $item)
                @if(($item['group'] ?? null) && $item['group'] !== $previousGroup)
                    <div class="md:col-span-2 lg:col-span-3 {{ $previousGroup ? 'mt-2 pt-4 border-t border-gray-100' : '' }}">
                        <h4 class="text-xs font-semibold uppercase tracking-wider {{ $groupTone[$item['group']] ?? 'text-gray-500' }}">{{ app()->getLocale() === 'en' ? (trans('modules_fit.groups')[$item['group']] ?? $groupLabels[$item['group']]) : ($groupLabels[$item['group']] ?? $item['group']) }}</h4>
                    </div>
                @endif
                @php $previousGroup = $item['group'] ?? null; @endphp
                @php
                    $routeName = $item['route'] ?? null;
                    $gateMap = [
                        'players.index' => 'access-player-list',
                        'modules.teams.index' => 'access-team-management',
                        'modules.competitions.index' => 'access-competition-management',
                        'modules.referees.index' => 'access-referee-portal',
                        'modules.clubs.index' => 'access-club-management',
                        'modules.associations.index' => 'access-back-office',
                        'modules.licenses.index' => 'access-license-management',
                        'fifa.dashboard' => 'access-fifa-connect',
                        'portal.devices' => 'access-devices-portal',
                        'clinical.patient-portal' => 'access-clinical-portal',
                        'clinical.clinician-portal' => 'access-clinical-portal',
                        'modules.administration.index' => 'access-back-office',
                        'modules.medical.index' => 'access-medical',
                        'modules.healthcare.index' => 'access-healthcare',
                        'pcma.index' => 'access-pcma',
                        'secretary.dashboard' => 'access-secretary',
                        'modules.confederations.index' => 'access-confederations',
                        'fifa.portal.integrated' => 'access-fifa-portal',
                        'player-portal.index' => 'access-player-portal',
                        'referee-portal.index' => 'access-referee-portal',
                        'team-portal.dashboard' => 'access-team-portal',
                        'analytics.dashboard' => 'access-analytics',
                        'analytics.digital-twin' => 'access-digital-twin',
                        'performances.analytics' => 'access-performance-analytics',
                        'rpm.index' => 'access-rpm',
                        'gemini.index' => 'access-gemini',
                        'modules.finance.dashboard' => 'access-finance',
                        'licenses.validation' => 'access-license-validation',
                        'fifa.analytics' => 'access-fifa-analytics',
                        'admin.content-management.index' => 'access-content-management',
                        'admin.transfer-management.index' => 'access-transfer-management'
                    ];
                    $permission = $gateMap[$routeName] ?? 'access-modules';

                    // FIT Metrics follows the canonical RBAC permission used
                    // by its destination route. Legacy module permissions are
                    // audited separately before enforcing them globally here.
                    $canAccess = match ($routeName) {
                        'performances.fit-metrics' => auth()->check()
                            && app(\App\Services\RBACService::class)
                                ->userHasPermission(
                                    auth()->user(),
                                    'record-performance-metrics'
                                ),
                        'dtn.index', 'dtn.players.index', 'dtn.api-access' => $spaceVisible['dtn'],
                        'club.selections.index', 'club.selections.returns', 'club.selections.api-access' => $spaceVisible['club'],
                        default => true,
                    };
                @endphp
                @if($canAccess)
                <div class="module-card bg-white border border-gray-200 rounded-lg p-4 hover:shadow-md hover:border-blue-300 transition-all duration-200 cursor-pointer group" 
                     onclick="handleModuleClick('{{ $item['route'] }}', '{{ app()->getLocale() === 'en' ? (trans('modules_fit.names')[$item['name']] ?? $item['name']) : $item['name'] }}', event)">
                    <div class="flex items-start justify-between mb-3">
                        <div class="flex items-center">
                            <div class="bg-blue-600 text-white w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold mr-3">
                                @php
                                    $cardNumber = 1;
                                    foreach($categories as $catKey => $catInfo) {
                                        if($catKey === $categoryKey) {
                                            break;
                                        }
                                        $cardNumber += count($groupedModules[$catKey] ?? []);
                                    }
                                    $cardNumber += $loop->iteration - 1;
                                @endphp
                                {{ $cardNumber }}
                            </div>
                            <span class="inline-flex items-center justify-center w-9 h-9 rounded-lg {{ $iconTone[$item['color'] ?? 'gray'] ?? 'bg-slate-100 text-slate-600' }}">@include('modules.partials.icon', ['name' => $item['icon'] ?? '', 'class' => 'w-5 h-5'])</span>
                        </div>
                        <div class="w-3 h-3 rounded-full 
                            @if($item['color'] === 'red') bg-red-500
                            @elseif($item['color'] === 'green') bg-green-500
                            @elseif($item['color'] === 'blue') bg-blue-500
                            @elseif($item['color'] === 'purple') bg-purple-500
                            @elseif($item['color'] === 'yellow') bg-yellow-500
                            @elseif($item['color'] === 'indigo') bg-indigo-500
                            @elseif($item['color'] === 'pink') bg-pink-500
                            @elseif($item['color'] === 'teal') bg-teal-500
                            @elseif($item['color'] === 'cyan') bg-cyan-500
                            @elseif($item['color'] === 'emerald') bg-emerald-500
                            @else bg-gray-500 @endif"></div>
                    </div>
                    <h4 class="text-lg font-semibold text-gray-900 mb-2 group-hover:text-blue-600 transition-colors">
                        {{ app()->getLocale() === 'en' ? (trans('modules_fit.names')[$item['name']] ?? $item['name']) : $item['name'] }}
                    </h4>
                    
                    <p class="text-sm text-gray-600 mb-3 line-clamp-2">
                        {{ app()->getLocale() === 'en' ? (trans('modules_fit.descriptions')[$item['description']] ?? $item['description']) : $item['description'] }}
                    </p>
                    
                    <div class="flex items-center justify-between">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            {{ app()->getLocale() === 'en' ? 'Available' : 'Disponible' }}
                        </span>
                        <span class="text-xs text-gray-500 font-mono">{{ $item['route'] }}</span>
                    </div>
                </div>
                @endif
                @endforeach
                </div>
            </div>
        </div>
        @endif
        @endforeach
        @else
        <!-- Fallback pour les modules non plats -->
        <div class="mb-8">
            <div class="flex items-center mb-4">
                <div class="w-10 h-10 rounded-lg flex items-center justify-center mr-4 bg-gray-100 text-gray-600">@include('modules.partials.icon', ['name' => 'layers', 'class' => 'w-6 h-6'])</div>
                <div>
                    <h3 class="text-xl font-semibold text-gray-900">{{ app()->getLocale() === 'en' ? 'Available modules' : 'Modules disponibles' }}</h3>
                    <p class="text-sm text-gray-600">{{ app()->getLocale() === 'en' ? 'Access features' : 'Accès aux fonctionnalités' }}</p>
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                @foreach($modules as $category => $items)
                @if(is_array($items) && isset($items['items']))
                    @foreach($items['items'] as $item)
                    <div class="bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow duration-300 border border-gray-200 p-6 group cursor-pointer" 
                         onclick="window.location.href='{{ route($item['route']) }}'">
                        <div class="flex items-center mb-4">
                            <div class="w-12 h-12 rounded-lg flex items-center justify-center mr-4 bg-gray-100 text-gray-600">
                                @include('modules.partials.icon', ['name' => $item['icon'] ?? '', 'class' => 'w-6 h-6'])
                            </div>
                            <div class="flex-1">
                                <h3 class="text-lg font-semibold text-gray-900 group-hover:text-blue-600 transition-colors">
                                    {{ app()->getLocale() === 'en' ? (trans('modules_fit.names')[$item['name']] ?? $item['name']) : $item['name'] }}
                                </h3>
                                <p class="text-sm text-gray-600 mt-1">{{ app()->getLocale() === 'en' ? (trans('modules_fit.descriptions')[$item['description']] ?? $item['description']) : $item['description'] }}</p>
                            </div>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                {{ app()->getLocale() === 'en' ? 'Available' : 'Disponible' }}
                            </span>
                            <svg class="w-5 h-5 text-gray-400 group-hover:text-blue-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </div>
                    </div>
                    @endforeach
                @endif
                @endforeach
            </div>
        </div>
        @endif
    </div>
</div>

<style>
/* Ensure all category sections are visible by default */
.category-section {
    display: block !important;
}
</style>

<script>
function handleModuleClick(route, moduleName, event) {
    console.log('handleModuleClick called with:', route, moduleName, event);
    // Créer des URLs fonctionnelles basées sur les routes
    const routeMap = {
        'modules.medical.index': '/modules/medical',
        'modules.healthcare.index': '/modules/healthcare',
        'pcma.index': '/pcma',
        'secretary.dashboard': '/secretary/dashboard',
        'modules.players.index': '/modules/players',
        'modules.teams.index': '/modules/teams',
        'modules.competitions.index': '/modules/competitions',
        'modules.referees.index': '/modules/referees',
        'modules.clubs.index': '/modules/clubs',
        'modules.associations.index': '/modules/associations',
        'modules.confederations.index': '/modules/confederations',
        'modules.licenses.index': '/modules/licenses',
        'licenses.validation': '/licenses/validation',
        'analytics.dashboard': '/analytics/dashboard',
        'fifa.analytics': '/fifa/analytics',
        'analytics.digital-twin': '/analytics/digital-twin',
        'performances.analytics': '/performances/analytics',
        'performances.fit-metrics': '/performances/fit-metrics',
        'modules.coach-cockpit': '/modules/coach-cockpit',
        'dtn.index': '/dtn',
        'dtn.players.index': '/dtn/players',
        'dtn.api-access': '/dtn/api-access',
        'club.selections.index': '/club/selections',
        'club.selections.returns': '/club/selections/returns',
        'club.selections.api-access': '/club/selections/api-access',
        'rpm.index': '/rpm',
        'gemini.index': '/gemini',
        'fifa.dashboard': '/fifa/dashboard',
        'player-portal.dashboard': '/player-portal',
        'player-portal.index': '/player-portal',
        'players.list': '/players/list',
        'test.referees': '/test-referees',
        'referee-dashboard-test': '/referee-dashboard-test',
        'referee-portal.index': '/referee-portal',
        'team-portal.dashboard': '/team-portal',
        'portal.devices': '/portal/devices',
        'clinical.patient-portal': '/clinical/patient-portal',
        'clinical.clinician-portal': '/clinical/clinician-portal',
        'modules.administration.index': '/modules/administration',
        'modules.finance.dashboard': '/modules/finance/',
        'admin.content-management.index': '/admin/content-management',
        'admin.transfer-management.index': '/admin/transfer-management'
    };

    const url = routeMap[route] || '/modules';
    
    // Ajouter un effet visuel de clic
    const card = event.currentTarget;
    card.style.transform = 'scale(0.98)';
    card.style.transition = 'transform 0.1s ease';
    
    setTimeout(() => {
        card.style.transform = '';
        
        // Afficher un message informatif
        showModuleInfo(moduleName, url);
        
        // Rediriger après un court délai
        setTimeout(() => {
            window.location.href = url;
        }, 1500);
    }, 100);
}

function showModuleInfo(moduleName, url) {
    // Créer une notification élégante
    const notification = document.createElement('div');
    notification.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50';
    notification.innerHTML = `
        <div class="bg-white rounded-lg p-8 max-w-md mx-4 text-center shadow-xl">
            <div class="mb-4 flex justify-center text-gray-400">@include('modules.partials.icon', ['name' => 'layers', 'class' => 'w-10 h-10'])</div>
            <h3 class="text-xl font-semibold text-gray-900 mb-2">Accès au module</h3>
            <p class="text-gray-600 mb-4">${moduleName}</p>
            <div class="bg-blue-100 text-blue-800 px-4 py-2 rounded-lg text-sm font-mono mb-4">
                ${url}
            </div>
            <p class="text-green-600 text-sm">Redirection en cours...</p>
        </div>
    `;
    
    document.body.appendChild(notification);
    
    // Supprimer la notification après 1.5 secondes
    setTimeout(() => {
        document.body.removeChild(notification);
    }, 1500);
}


// Ensure all sections are visible on page load
document.addEventListener('DOMContentLoaded', function() {
    console.log('Page loaded, ensuring all sections are visible');
    const pageSections = document.querySelectorAll('.category-section');
    pageSections.forEach(section => {
        section.style.display = 'block';
    });
    console.log('Made', pageSections.length, 'sections visible');
});
</script>
@endsection