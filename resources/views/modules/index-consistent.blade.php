@extends('layouts.app')

@section('title', 'Modules - FIT Platform')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Page Header -->
    <div class="bg-white shadow-sm border-b border-gray-200 mb-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <img src="{{ asset('images/fit-logo.png') }}" alt="FIT Logo" class="w-10 h-10 mr-3">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Modules FIT</h1>
                        <p class="text-sm text-gray-600">Football Intelligence & Tracking</p>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('dashboard') }}" class="text-gray-600 hover:text-gray-900 text-sm font-medium">
                        ← Retour au Dashboard
                    </a>
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
                    <h2 class="text-3xl font-bold text-gray-900 mb-4">Bienvenue sur la plateforme FIT</h2>
                    <p class="text-lg text-gray-600 mb-6">
                        Accédez à tous les modules de gestion du football avec une interface moderne et intuitive
                    </p>
                    
                    <!-- Stats -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                        <div class="bg-blue-50 rounded-lg p-4">
                            <div class="text-2xl font-bold text-blue-600">{{ $totalModules ?? count($modules) }}</div>
                            <div class="text-sm text-blue-800">Modules Disponibles</div>
                        </div>
                        <div class="bg-green-50 rounded-lg p-4">
                            <div class="text-2xl font-bold text-green-600">8</div>
                            <div class="text-sm text-green-800">Catégories</div>
                        </div>
                        <div class="bg-purple-50 rounded-lg p-4">
                            <div class="text-2xl font-bold text-purple-600">100%</div>
                            <div class="text-sm text-purple-800">Opérationnels</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-8">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Filtrer par catégorie</h3>
                <div class="flex flex-wrap gap-2">
                    <button onclick="filterModules('all')" class="filter-btn bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-blue-700 transition duration-200">
                        Tous les modules
                    </button>
                    <button onclick="filterModules('health')" class="filter-btn bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium hover:bg-gray-300 transition duration-200">
                        🏥 Santé
                    </button>
                    <button onclick="filterModules('sport')" class="filter-btn bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium hover:bg-gray-300 transition duration-200">
                        ⚽ Sport
                    </button>
                    <button onclick="filterModules('institutional')" class="filter-btn bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium hover:bg-gray-300 transition duration-200">
                        🏢 Organisations
                    </button>
                    <button onclick="filterModules('documents')" class="filter-btn bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium hover:bg-gray-300 transition duration-200">
                        📋 Documents
                    </button>
                    <button onclick="filterModules('analytics')" class="filter-btn bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium hover:bg-gray-300 transition duration-200">
                        📊 Analytics
                    </button>
                    <button onclick="filterModules('technology')" class="filter-btn bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium hover:bg-gray-300 transition duration-200">
                        🤖 Technologie
                    </button>
                    <button onclick="filterModules('portals')" class="filter-btn bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium hover:bg-gray-300 transition duration-200">
                        🌐 Portails
                    </button>
                    <button onclick="filterModules('administration')" class="filter-btn bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium hover:bg-gray-300 transition duration-200">
                        ⚙️ Administration
                    </button>
                </div>
            </div>
        </div>

        <!-- Categories -->
        <div id="categories">
            @if(isset($modules) && is_array($modules))
                @php
                    $categories = [
                        'health' => ['name' => '🏥 Santé & Médecine', 'icon' => '🏥', 'color' => 'red'],
                        'sport' => ['name' => '⚽ Gestion du Football', 'icon' => '⚽', 'color' => 'green'],
                        'institutional' => ['name' => '🏢 Organisations', 'icon' => '🏢', 'color' => 'blue'],
                        'documents' => ['name' => '📋 Licences & Documents', 'icon' => '📋', 'color' => 'indigo'],
                        'analytics' => ['name' => '📊 Analytics & Performance', 'icon' => '📊', 'color' => 'purple'],
                        'technology' => ['name' => '🤖 IA & Technologie', 'icon' => '🤖', 'color' => 'purple'],
                        'portals' => ['name' => '🌐 Portails & Connectivité', 'icon' => '🌐', 'color' => 'cyan'],
                        'administration' => ['name' => '⚙️ Administration', 'icon' => '⚙️', 'color' => 'gray']
                    ];
                    
                    $groupedModules = [];
                    foreach($modules as $module) {
                        $category = $module['category'] ?? 'administration';
                        if(!isset($groupedModules[$category])) {
                            $groupedModules[$category] = [];
                        }
                        $groupedModules[$category][] = $module;
                    }
                @endphp
                
                @foreach($categories as $categoryKey => $categoryInfo)
                    @if(isset($groupedModules[$categoryKey]) && count($groupedModules[$categoryKey]) > 0)
                        <div class="category-section mb-8" data-category="{{ $categoryKey }}">
                            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                                <!-- Category Header -->
                                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 rounded-t-lg">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center">
                                            <span class="text-2xl mr-3">{{ $categoryInfo['icon'] }}</span>
                                            <h3 class="text-xl font-semibold text-gray-900">{{ $categoryInfo['name'] }}</h3>
                                        </div>
                                        <span class="bg-{{ $categoryInfo['color'] }}-100 text-{{ $categoryInfo['color'] }}-800 px-3 py-1 rounded-full text-sm font-medium">
                                            {{ count($groupedModules[$categoryKey]) }} modules
                                        </span>
                                    </div>
                                </div>
                                
                                <!-- Modules Grid -->
                                <div class="p-6">
                                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                        @foreach($groupedModules[$categoryKey] as $item)
                                            <div class="module-card bg-white border border-gray-200 rounded-lg p-4 hover:shadow-md hover:border-blue-300 transition-all duration-200 cursor-pointer group" 
                                                 onclick="handleModuleClick('{{ $item['route'] }}', '{{ $item['name'] }}')">
                                                <div class="flex items-start justify-between mb-3">
                                                    <div class="flex items-center">
                                                        <div class="bg-blue-600 text-white w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold mr-3">
                                                            {{ $item['number'] ?? '?' }}
                                                        </div>
                                                        <span class="text-2xl mr-2">{{ $item['icon'] }}</span>
                                                    </div>
                                                    <div class="w-3 h-3 rounded-full bg-{{ $item['color'] }}-500"></div>
                                                </div>
                                                
                                                <h4 class="text-lg font-semibold text-gray-900 mb-2 group-hover:text-blue-600 transition-colors">
                                                    {{ $item['name'] }}
                                                </h4>
                                                
                                                <p class="text-sm text-gray-600 mb-3 line-clamp-2">
                                                    {{ $item['description'] }}
                                                </p>
                                                
                                                <div class="flex items-center justify-between">
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                        @if($item['status'] === 'active') ✅ Actif
                                                        @elseif($item['status'] === 'maintenance') 🔧 Maintenance
                                                        @else ❌ Inactif @endif
                                                    </span>
                                                    <span class="text-xs text-gray-500 font-mono">{{ $item['route'] }}</span>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                @endforeach
            @else
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-8 text-center">
                    <div class="text-6xl mb-4">⚠️</div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">Erreur</h3>
                    <p class="text-gray-600">Aucun module trouvé. Vérifiez la configuration.</p>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
function filterModules(category) {
    // Update active filter button
    document.querySelectorAll('.filter-btn').forEach(btn => {
        btn.classList.remove('bg-blue-600', 'text-white');
        btn.classList.add('bg-gray-200', 'text-gray-700');
    });
    event.target.classList.remove('bg-gray-200', 'text-gray-700');
    event.target.classList.add('bg-blue-600', 'text-white');
    
    // Show/hide categories
    const categories = document.querySelectorAll('.category-section');
    categories.forEach(cat => {
        if (category === 'all' || cat.dataset.category === category) {
            cat.style.display = 'block';
        } else {
            cat.style.display = 'none';
        }
    });
}

function handleModuleClick(route, moduleName) {
    // Créer des URLs fonctionnelles basées sur les routes
    const routeMap = {
        'modules.medical.index': '/modules/medical',
        'modules.healthcare.index': '/modules/healthcare',
        'pcma.index': '/pcma',
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
        'dtn.index': '/dtn',
        'rpm.index': '/rpm',
        'gemini.index': '/gemini',
        'fifa.dashboard': '/fifa/dashboard',
        'player-portal.index': '/player-portal',
        'players.list': '/players/list',
        'referee-portal.index': '/referee-portal',
        'team-portal.dashboard': '/team-portal',
        'portal.devices': '/portal/devices',
        'modules.administration.index': '/modules/administration',
        'modules.finance.dashboard': '/modules/finance',
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
            <div class="text-4xl mb-4">🚀</div>
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
</script>
@endsection
