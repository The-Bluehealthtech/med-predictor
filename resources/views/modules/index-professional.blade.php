<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modules FIT - Plateforme Football Intelligence & Tracking</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            color: #333;
            line-height: 1.6;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 2rem;
        }

        .header {
            text-align: center;
            margin-bottom: 3rem;
            color: white;
        }

        .header h1 {
            font-size: 3rem;
            font-weight: 700;
            margin-bottom: 1rem;
            text-shadow: 0 2px 4px rgba(0,0,0,0.3);
        }

        .header .subtitle {
            font-size: 1.2rem;
            opacity: 0.9;
            margin-bottom: 1rem;
        }

        .stats {
            display: flex;
            justify-content: center;
            gap: 2rem;
            margin-top: 1.5rem;
        }

        .stat-item {
            background: rgba(255,255,255,0.1);
            backdrop-filter: blur(10px);
            padding: 1rem 1.5rem;
            border-radius: 15px;
            border: 1px solid rgba(255,255,255,0.2);
        }

        .stat-number {
            font-size: 2rem;
            font-weight: 700;
            color: #fff;
        }

        .stat-label {
            font-size: 0.9rem;
            opacity: 0.8;
        }

        .filters {
            display: flex;
            justify-content: center;
            gap: 1rem;
            margin-bottom: 2rem;
            flex-wrap: wrap;
        }

        .filter-btn {
            background: rgba(255,255,255,0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.2);
            color: white;
            padding: 0.75rem 1.5rem;
            border-radius: 25px;
            cursor: pointer;
            transition: all 0.3s ease;
            font-weight: 500;
        }

        .filter-btn:hover, .filter-btn.active {
            background: rgba(255,255,255,0.2);
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        }

        .categories {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 2rem;
            margin-bottom: 2rem;
        }

        .category {
            background: rgba(255,255,255,0.95);
            backdrop-filter: blur(20px);
            border-radius: 20px;
            padding: 1.5rem;
            box-shadow: 0 8px 32px rgba(0,0,0,0.1);
            border: 1px solid rgba(255,255,255,0.2);
            transition: all 0.3s ease;
        }

        .category:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(0,0,0,0.15);
        }

        .category-header {
            display: flex;
            align-items: center;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid #f0f0f0;
        }

        .category-icon {
            font-size: 2rem;
            margin-right: 1rem;
        }

        .category-title {
            font-size: 1.4rem;
            font-weight: 600;
            color: #2d3748;
        }

        .category-count {
            background: #667eea;
            color: white;
            padding: 0.25rem 0.75rem;
            border-radius: 15px;
            font-size: 0.8rem;
            font-weight: 600;
            margin-left: auto;
        }

        .modules-grid {
            display: grid;
            gap: 1rem;
        }

        .module-card {
            background: white;
            border-radius: 15px;
            padding: 1.25rem;
            border: 1px solid #e2e8f0;
            transition: all 0.3s ease;
            cursor: pointer;
            position: relative;
            overflow: hidden;
        }

        .module-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #667eea, #764ba2);
            transform: scaleX(0);
            transition: transform 0.3s ease;
        }

        .module-card:hover::before {
            transform: scaleX(1);
        }

        .module-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            border-color: #667eea;
        }

        .module-header {
            display: flex;
            align-items: center;
            margin-bottom: 0.75rem;
        }

        .module-number {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 0.9rem;
            margin-right: 1rem;
            box-shadow: 0 2px 8px rgba(102, 126, 234, 0.3);
        }

        .module-icon {
            font-size: 1.5rem;
            margin-right: 0.75rem;
        }

        .module-name {
            font-size: 1.1rem;
            font-weight: 600;
            color: #2d3748;
            flex: 1;
        }

        .module-status {
            background: #10b981;
            color: white;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .module-description {
            color: #64748b;
            font-size: 0.9rem;
            margin-bottom: 0.75rem;
            line-height: 1.5;
        }

        .module-route {
            color: #667eea;
            font-size: 0.8rem;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .color-indicator {
            position: absolute;
            top: 1rem;
            right: 1rem;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            border: 2px solid white;
            box-shadow: 0 0 0 1px rgba(0,0,0,0.1);
        }

        .color-red { background: #ef4444; }
        .color-green { background: #10b981; }
        .color-blue { background: #3b82f6; }
        .color-purple { background: #8b5cf6; }
        .color-indigo { background: #6366f1; }
        .color-cyan { background: #06b6d4; }
        .color-emerald { background: #10b981; }
        .color-teal { background: #14b8a6; }
        .color-gray { background: #6b7280; }

        .footer {
            text-align: center;
            margin-top: 3rem;
            color: rgba(255,255,255,0.8);
        }

        .footer a {
            color: white;
            text-decoration: none;
            font-weight: 500;
        }

        .footer a:hover {
            text-decoration: underline;
        }

        @media (max-width: 768px) {
            .container {
                padding: 1rem;
            }
            
            .header h1 {
                font-size: 2rem;
            }
            
            .stats {
                flex-direction: column;
                gap: 1rem;
            }
            
            .categories {
                grid-template-columns: 1fr;
            }
            
            .filters {
                flex-direction: column;
                align-items: center;
            }
        }

        .fade-in {
            animation: fadeIn 0.6s ease-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .pulse {
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% {
                opacity: 1;
            }
            50% {
                opacity: 0.7;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header fade-in">
            <h1>🏆 Modules FIT</h1>
            <div class="subtitle">Football Intelligence & Tracking Platform</div>
            <div class="stats">
                <div class="stat-item">
                    <div class="stat-number">{{ $totalModules ?? count($modules) }}</div>
                    <div class="stat-label">Modules Disponibles</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">8</div>
                    <div class="stat-label">Catégories</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">100%</div>
                    <div class="stat-label">Opérationnels</div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="filters fade-in">
            <button class="filter-btn active" onclick="filterModules('all')">Tous les modules</button>
            <button class="filter-btn" onclick="filterModules('health')">🏥 Santé</button>
            <button class="filter-btn" onclick="filterModules('sport')">⚽ Sport</button>
            <button class="filter-btn" onclick="filterModules('institutional')">🏢 Organisations</button>
            <button class="filter-btn" onclick="filterModules('documents')">📋 Documents</button>
            <button class="filter-btn" onclick="filterModules('analytics')">📊 Analytics</button>
            <button class="filter-btn" onclick="filterModules('technology')">🤖 Technologie</button>
            <button class="filter-btn" onclick="filterModules('portals')">🌐 Portails</button>
            <button class="filter-btn" onclick="filterModules('administration')">⚙️ Administration</button>
        </div>

        <!-- Categories -->
        <div class="categories" id="categories">
            @if(isset($modules) && is_array($modules))
                @php
                    $categories = [
                        'health' => ['name' => '🏥 Santé & Médecine', 'icon' => '🏥'],
                        'sport' => ['name' => '⚽ Gestion du Football', 'icon' => '⚽'],
                        'institutional' => ['name' => '🏢 Organisations', 'icon' => '🏢'],
                        'documents' => ['name' => '📋 Licences & Documents', 'icon' => '📋'],
                        'analytics' => ['name' => '📊 Analytics & Performance', 'icon' => '📊'],
                        'technology' => ['name' => '🤖 IA & Technologie', 'icon' => '🤖'],
                        'portals' => ['name' => '🌐 Portails & Connectivité', 'icon' => '🌐'],
                        'administration' => ['name' => '⚙️ Administration', 'icon' => '⚙️']
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
                        <div class="category fade-in" data-category="{{ $categoryKey }}">
                            <div class="category-header">
                                <div class="category-icon">{{ $categoryInfo['icon'] }}</div>
                                <div class="category-title">{{ $categoryInfo['name'] }}</div>
                                <div class="category-count">{{ count($groupedModules[$categoryKey]) }}</div>
                            </div>
                            
                            <div class="modules-grid">
                                @foreach($groupedModules[$categoryKey] as $item)
                                    <div class="module-card" onclick="handleModuleClick('{{ $item['route'] }}', '{{ $item['name'] }}')">
                                        <div class="color-indicator color-{{ $item['color'] }}"></div>
                                        <div class="module-header">
                                            <div class="module-number">{{ $item['number'] ?? '?' }}</div>
                                            <div class="module-icon">{{ $item['icon'] }}</div>
                                            <div class="module-name">{{ $item['name'] }}</div>
                                            <div class="module-status">
                                                @if($item['status'] === 'active') ✅ Actif
                                                @elseif($item['status'] === 'maintenance') 🔧 Maintenance
                                                @else ❌ Inactif @endif
                                            </div>
                                        </div>
                                        <div class="module-description">{{ $item['description'] }}</div>
                                        <div class="module-route">{{ $item['route'] }}</div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                @endforeach
            @else
                <div class="category fade-in">
                    <div class="category-header">
                        <div class="category-icon">⚠️</div>
                        <div class="category-title">Erreur</div>
                    </div>
                    <div class="modules-grid">
                        <div class="module-card">
                            <div class="module-description">Aucun module trouvé. Vérifiez la configuration.</div>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Footer -->
        <div class="footer fade-in">
            <p>© 2024 FIT Platform - Football Intelligence & Tracking</p>
            <p><a href="{{ route('dashboard') }}">← Retour au Dashboard</a></p>
        </div>
    </div>

    <script>
        function filterModules(category) {
            // Update active filter button
            document.querySelectorAll('.filter-btn').forEach(btn => btn.classList.remove('active'));
            event.target.classList.add('active');
            
            // Show/hide categories
            const categories = document.querySelectorAll('.category');
            categories.forEach(cat => {
                if (category === 'all' || cat.dataset.category === category) {
                    cat.style.display = 'block';
                    cat.classList.add('fade-in');
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
            card.style.transform = 'scale(0.95)';
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
            notification.style.cssText = `
                position: fixed;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%);
                background: rgba(255, 255, 255, 0.95);
                backdrop-filter: blur(20px);
                padding: 2rem;
                border-radius: 20px;
                box-shadow: 0 20px 40px rgba(0,0,0,0.2);
                border: 1px solid rgba(255,255,255,0.3);
                z-index: 1000;
                text-align: center;
                animation: fadeIn 0.3s ease;
            `;
            
            notification.innerHTML = `
                <div style="font-size: 3rem; margin-bottom: 1rem;">🚀</div>
                <h3 style="color: #2d3748; margin-bottom: 0.5rem; font-weight: 600;">Accès au module</h3>
                <p style="color: #64748b; margin-bottom: 1rem;">${moduleName}</p>
                <div style="background: #667eea; color: white; padding: 0.5rem 1rem; border-radius: 10px; font-size: 0.9rem; margin-bottom: 1rem;">
                    ${url}
                </div>
                <p style="color: #10b981; font-size: 0.9rem;">Redirection en cours...</p>
            `;
            
            document.body.appendChild(notification);
            
            // Supprimer la notification après 1.5 secondes
            setTimeout(() => {
                notification.style.animation = 'fadeOut 0.3s ease';
                setTimeout(() => {
                    document.body.removeChild(notification);
                }, 300);
            }, 1500);
        }

        // Add smooth scrolling and animations
        document.addEventListener('DOMContentLoaded', function() {
            // Add staggered animation to cards
            const cards = document.querySelectorAll('.module-card');
            cards.forEach((card, index) => {
                card.style.animationDelay = `${index * 0.1}s`;
            });

            // Ajouter des styles CSS pour les animations
            const style = document.createElement('style');
            style.textContent = `
                @keyframes fadeOut {
                    from { opacity: 1; transform: translate(-50%, -50%) scale(1); }
                    to { opacity: 0; transform: translate(-50%, -50%) scale(0.8); }
                }
            `;
            document.head.appendChild(style);
        });
    </script>
</body>
</html>
