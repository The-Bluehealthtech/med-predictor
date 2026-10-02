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
        // Licences : la demande côté club (et fédération), l'approbation côté fédération seulement.
        if (isset($module['audience'])) {
            $licensing = app(\App\Services\Licensing\LicenseWorkflow::class);
            return $authUser !== null && ($module['audience'] === 'federation' ? $licensing->canApprove($authUser) : $licensing->canRequest($authUser));
        }
        // La saisie FIT suit la permission RBAC canonique de sa route de destination.
        $routeRule = match ($module['route'] ?? null) {
            'performances.fit-metrics' => auth()->check()
                && app(\App\Services\RBACService::class)->userHasPermission(auth()->user(), 'record-performance-metrics'),
            default => null,
        };
        if ($routeRule !== null) {
            return $routeRule;
        }
        $group = $module['group'] ?? null;
        return isset($spaceVisible[$group]) ? $spaceVisible[$group] : true;
    };

    // Ce qui reste à faire à chaque étape, pour ce compte (nombres seulement, dans son périmètre).
    $todo = ($authUser && is_array($modules) && isset($modules[0]['name']))
        ? app(\App\Services\Modules\WorkflowCounters::class)->forUser($authUser) : [];
    $visibleModules = (is_array($modules) && isset($modules[0]['name']))
        ? collect($modules)->filter(fn ($m) => $moduleVisible($m))->values()
        : collect($modules);
@endphp
<script>
let activeModuleCategory = null;

function normalizeModuleSearch(value) {
    return (value || '')
        .toString()
        .normalize('NFD')
        .replace(/[\u0300-\u036f]/g, '')
        .toLowerCase()
        .trim();
}

function setActiveCategoryButton(button) {
    document.querySelectorAll('[data-module-category-filter]').forEach(candidate => {
        candidate.classList.remove('bg-blue-600', 'text-white');
        candidate.classList.add('bg-gray-200', 'text-gray-700');
    });

    if (button) {
        button.classList.remove('bg-gray-200', 'text-gray-700');
        button.classList.add('bg-blue-600', 'text-white');
    }
}

function applyModuleFilters() {
    const searchInput = document.getElementById('module-search');
    const query = normalizeModuleSearch(searchInput ? searchInput.value : '');
    let visibleCards = 0;

    document.querySelectorAll('.category-section').forEach(section => {
        const categoryMatches = !activeModuleCategory || section.dataset.category === activeModuleCategory;
        let sectionVisibleCards = 0;

        section.querySelectorAll('.module-card').forEach(card => {
            const haystack = normalizeModuleSearch(card.dataset.search || card.textContent);
            const matches = categoryMatches && (!query || haystack.includes(query));
            card.hidden = !matches;
            if (matches) {
                sectionVisibleCards++;
                visibleCards++;
            }
        });

        section.querySelectorAll('.module-step').forEach(step => {
            const cards = [...step.querySelectorAll('.module-card')];
            if (cards.length > 0) {
                step.hidden = !cards.some(card => !card.hidden);
            } else {
                step.hidden = !categoryMatches || query !== '';
            }
        });

        section.hidden = !categoryMatches || (query !== '' && sectionVisibleCards === 0);
    });

    const status = document.getElementById('module-search-status');
    if (status) {
        status.textContent = query
            ? `${visibleCards} {{ app()->getLocale() === 'en' ? 'module(s) found' : 'module(s) trouvé(s)' }}`
            : '';
    }
}

function showAllCategories(event) {
    activeModuleCategory = null;
    setActiveCategoryButton(event?.currentTarget || null);
    applyModuleFilters();
}

function filterByCategory(category, event) {
    activeModuleCategory = category;
    setActiveCategoryButton(event?.currentTarget || null);
    applyModuleFilters();
}

// Lien depuis le tableau de bord général : /modules?section=clinique pré-filtre la section.
document.addEventListener('DOMContentLoaded', () => {
    const section = new URLSearchParams(window.location.search).get('section');
    const button = section && document.querySelector(`[data-module-category-filter][data-category="${CSS.escape(section)}"]`);
    if (button) {
        filterByCategory(section, { currentTarget: button });
    }
});
</script>

<div class="min-h-screen bg-gray-50">
    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <x-page-header
            title="{{ app()->getLocale() === 'en' ? 'FIT modules' : 'Modules FIT' }}"
            subtitle="{{ app()->getLocale() === 'en' ? 'Access your clinical, performance, selection and administration workflows.' : 'Accédez aux parcours clinique, performance, sélections et administration.' }}"
            eyebrow="FIT"
            :back-href="route('dashboard')"
            :back-label="app()->getLocale() === 'en' ? 'Back to dashboard' : 'Retour au tableau de bord'"
            :count="$visibleModules->count()"
            :count-label="app()->getLocale() === 'en' ? 'available modules' : 'modules disponibles'"
        >
            <x-slot:meta>
                <span>{{ $visibleModules->pluck('category')->filter()->unique()->count() }} {{ app()->getLocale() === 'en' ? 'sections' : 'sections' }}</span>
                <span aria-hidden="true">•</span>
                <span>{{ app()->getLocale() === 'en' ? 'System operational' : 'Système opérationnel' }}</span>
            </x-slot:meta>
        </x-page-header>

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
            // Parcours de chaque section : étapes ordonnées, rôle et production de chaque étape.
            $workflows = [
                'clinique' => ['type' => 'flow', 'tone' => 'bg-red-600', 'steps' => [
                    ['label' => 'Accueil', 'todo' => 'clinic_today', 'role' => 'Secrétariat médical', 'output' => 'Rendez-vous pris, dossier ouvert', 'routes' => ['secretary.dashboard']],
                    ['label' => 'Prise en charge', 'todo' => 'clinic_waiting', 'role' => 'Médecin', 'output' => 'Consultation et orientation', 'routes' => ['modules.medical.index']],
                    ['label' => 'Dossier médical', 'todo' => 'clinic_aut', 'role' => 'Médecin', 'output' => 'Diagnostic, traitement, AUT, imagerie', 'routes' => ['modules.healthcare.index']],
                    ['label' => 'Aptitude', 'todo' => 'clinic_pcma', 'role' => 'Médecin', 'output' => 'Aptitude à jouer transmise au staff', 'routes' => ['pcma.index']],
                    ['label' => 'Partager', 'role' => 'Médecin, joueur', 'output' => 'Résumé IPS pour un transfert, une sélection ou un club', 'routes' => ['passports.medical.index']],
                ]],
                'performance' => ['type' => 'flow', 'tone' => 'bg-blue-600', 'steps' => [
                    ['label' => 'Collecter', 'role' => 'Préparateur physique', 'output' => 'Métriques et données des capteurs', 'routes' => ['performances.fit-metrics', 'portal.devices']],
                    ['label' => 'Surveiller', 'role' => 'Préparateur physique', 'output' => 'Charge et état de forme', 'routes' => ['rpm.index']],
                    ['label' => 'Évaluer', 'todo' => 'perf_alerts', 'role' => 'Analyste, entraîneur adjoint', 'output' => 'Forme, temps de jeu, alertes par joueur', 'routes' => ['performances.analytics']],
                    ['label' => 'Décider', 'role' => 'Entraîneur', 'output' => 'Composition et plan du prochain match', 'routes' => ['modules.coach-cockpit']],
                ], 'tools' => ['analytics.digital-twin'], 'tools_label' => 'Pour aller plus loin'],
                'selections' => ['type' => 'lanes', 'lanes' => [
                    'dtn' => ['label' => 'Direction technique nationale', 'tone' => 'bg-indigo-600', 'text' => 'text-indigo-700'],
                    'club' => ['label' => 'Club', 'tone' => 'bg-emerald-600', 'text' => 'text-emerald-700'],
                ], 'steps' => [
                    ['lane' => 'dtn', 'label' => 'Observer', 'role' => 'DTN', 'output' => 'Joueurs ciblés', 'routes' => ['dtn.players.index']],
                    ['lane' => 'dtn', 'label' => 'Convoquer', 'todo' => 'dtn_waiting_club', 'role' => 'DTN', 'output' => 'Convocation envoyée au club', 'routes' => ['dtn.index']],
                    ['lane' => 'club', 'label' => 'État de départ', 'todo' => 'club_departures', 'role' => 'Staff et médecin du club', 'output' => 'Données du joueur envoyées à la DTN', 'routes' => ['club.selections.index']],
                    ['lane' => 'dtn', 'label' => 'État de retour', 'todo' => 'dtn_returns', 'role' => 'DTN, après le rassemblement', 'output' => 'Incidents, performances et risques', 'routes' => ['dtn.index']],
                    ['lane' => 'club', 'label' => 'Accusé de réception', 'todo' => 'club_returns', 'role' => 'Club', 'output' => 'Sélection clôturée', 'routes' => ['club.selections.returns']],
                ], 'tools' => ['dtn.api-access', 'club.selections.api-access'], 'tools_label' => 'Connexion des logiciels (API)'],
                'administration' => ['type' => 'flow', 'tone' => 'bg-slate-600', 'steps' => [
                    ['label' => 'Structurer', 'role' => 'Administration', 'output' => 'Clubs et fédérations en place', 'routes' => ['modules.clubs.index', 'club-officials.index', 'modules.associations.index', 'modules.confederations.index']],
                    ['label' => 'Enregistrer', 'role' => 'Secrétariat du club', 'output' => 'Joueurs et équipes inscrits', 'routes' => ['modules.players.index', 'modules.teams.index']],
                    ['label' => 'Demander une licence', 'todo' => 'licences_info_requested', 'role' => 'Club', 'output' => 'Demande envoyée à la fédération', 'routes' => ['modules.licenses.index']],
                    ['label' => 'Approuver', 'todo' => 'licences_pending', 'role' => 'Fédération · identité FIFA ID facultative', 'output' => 'Licence active, complément demandé ou refus motivé', 'routes' => ['licenses.validation']],
                    ['label' => 'Transférer', 'todo' => 'transfers_pending', 'role' => 'Club et fédération', 'output' => 'Mutations enregistrées', 'routes' => ['admin.transfer-management.index', 'passports.transfer.index', 'fifa.dashboard']],
                    ['label' => 'Organiser', 'role' => 'Ligue ou fédération', 'output' => 'Compétitions et arbitres désignés', 'routes' => ['modules.competitions.index', 'referee-portal.index']],
                ], 'tools' => ['modules.finance.dashboard', 'modules.administration.index', 'admin.content-management.index', 'gemini.index'], 'tools_label' => 'Outils transverses'],
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

        <!-- Recherche et filtres -->
        <div class="mb-8">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="mb-5">
                    <label for="module-search" class="block text-sm font-semibold text-gray-900 mb-2">
                        {{ app()->getLocale() === 'en' ? 'Search modules' : 'Rechercher un module' }}
                    </label>
                    <div class="relative">
                        <input id="module-search"
                               type="search"
                               autocomplete="off"
                               placeholder="{{ app()->getLocale() === 'en' ? 'Name, feature, workflow…' : 'Nom, fonctionnalité, parcours…' }}"
                               class="w-full rounded-lg border border-gray-300 px-4 py-3 pr-10 focus:border-blue-500 focus:ring-blue-500">
                        <span class="pointer-events-none absolute inset-y-0 right-3 flex items-center text-gray-400">⌕</span>
                    </div>
                    <p id="module-search-status" class="mt-2 text-sm text-gray-500" aria-live="polite"></p>
                </div>

                <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ app()->getLocale() === 'en' ? 'Filter by category' : 'Filtrer par catégorie' }}</h3>
                <div class="flex flex-wrap gap-2">
                    <button type="button" data-module-category-filter onclick="showAllCategories(event)" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        {{ app()->getLocale() === 'en' ? 'All categories' : 'Toutes les catégories' }}
                    </button>
                    @foreach($categories as $key => $category)
                    @continue(empty($groupedModules[$key]))
                    <button type="button" data-module-category-filter onclick="filterByCategory('{{ $key }}', event)" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors category-filter" data-category="{{ $key }}">
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
                
                <!-- Parcours de la section -->
                <div class="p-6">
                    @include('modules.partials.workflow', ['workflow' => $workflows[$categoryKey], 'items' => $groupedModules[$categoryKey], 'todo' => $todo])
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
        'passports.medical.index': '/passports/medical',
        'club-officials.index': '/club-officials',
        'passports.transfer.index': '/passports/transfer',
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


document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('module-search');
    if (searchInput) {
        searchInput.addEventListener('input', applyModuleFilters);
        searchInput.addEventListener('search', applyModuleFilters);
    }

    applyModuleFilters();
});
</script>
@endsection