<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modules FIT - Fond Foncé</title>
    <style>
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
            margin: 0; 
            padding: 20px; 
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
            color: #ffffff;
            min-height: 100vh;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding: 20px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 15px;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .header h1 {
            font-size: 2.5em;
            margin: 0 0 10px 0;
            background: linear-gradient(45deg, #00d4ff, #00ff88);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .header p {
            font-size: 1.2em;
            margin: 0;
            color: #b0b0b0;
        }
        
        .stats {
            display: flex;
            justify-content: center;
            gap: 30px;
            margin: 20px 0;
        }
        
        .stat {
            text-align: center;
            padding: 15px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .stat-number {
            font-size: 2em;
            font-weight: bold;
            color: #00ff88;
        }
        
        .stat-label {
            font-size: 0.9em;
            color: #b0b0b0;
        }
        
        .category {
            margin: 30px 0;
            padding: 20px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 15px;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .category h2 {
            font-size: 1.5em;
            margin: 0 0 20px 0;
            padding: 10px 20px;
            background: linear-gradient(45deg, #ff6b6b, #ffa500);
            border-radius: 10px;
            display: inline-block;
        }
        
        .modules-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 15px;
        }
        
        .module {
            padding: 20px;
            background: rgba(255, 255, 255, 0.08);
            border-radius: 12px;
            border: 1px solid rgba(255, 255, 255, 0.15);
            transition: all 0.3s ease;
            cursor: pointer;
            position: relative;
            overflow: hidden;
        }
        
        .module::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.1), transparent);
            transition: left 0.5s ease;
        }
        
        .module:hover {
            transform: translateY(-5px);
            background: rgba(255, 255, 255, 0.15);
            border-color: rgba(0, 255, 136, 0.5);
            box-shadow: 0 10px 30px rgba(0, 255, 136, 0.2);
        }
        
        .module:hover::before {
            left: 100%;
        }
        
        .module-number {
            display: inline-block;
            width: 30px;
            height: 30px;
            background: linear-gradient(45deg, #00d4ff, #00ff88);
            color: #000;
            border-radius: 50%;
            text-align: center;
            line-height: 30px;
            font-weight: bold;
            font-size: 0.9em;
            margin-right: 15px;
        }
        
        .module-name {
            font-size: 1.2em;
            font-weight: bold;
            margin-bottom: 8px;
            color: #ffffff;
        }
        
        .module-description {
            color: #b0b0b0;
            font-size: 0.95em;
            line-height: 1.4;
            margin-bottom: 10px;
        }
        
        .module-meta {
            font-size: 0.8em;
            color: #888;
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
        }
        
        .module-meta span {
            padding: 2px 8px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 4px;
        }
        
        .status-active {
            color: #00ff88;
        }
        
        .status-inactive {
            color: #ff6b6b;
        }
        
        .color-indicator {
            display: inline-block;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            margin-right: 5px;
        }
        
        .color-red { background: #ff6b6b; }
        .color-green { background: #00ff88; }
        .color-blue { background: #00d4ff; }
        .color-purple { background: #a855f7; }
        .color-orange { background: #ffa500; }
        .color-cyan { background: #06b6d4; }
        .color-emerald { background: #10b981; }
        .color-gray { background: #6b7280; }
        .color-teal { background: #14b8a6; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🚀 Modules FIT Platform</h1>
            <p>Football Intelligence & Tracking - Interface Professionnelle</p>
            
            <div class="stats">
                <div class="stat">
                    <div class="stat-number">{{ $totalModules ?? count($modules) }}</div>
                    <div class="stat-label">Modules Disponibles</div>
                </div>
                <div class="stat">
                    <div class="stat-number">8</div>
                    <div class="stat-label">Catégories</div>
                </div>
                <div class="stat">
                    <div class="stat-number">100%</div>
                    <div class="stat-label">Opérationnel</div>
                </div>
            </div>
        </div>
        
        @if(isset($modules) && is_array($modules))
            @php
                $categories = [
                    'health' => '🏥 Santé & Médecine',
                    'sport' => '⚽ Gestion du Football', 
                    'institutional' => '🏢 Organisations',
                    'documents' => '📋 Licences & Documents',
                    'analytics' => '📊 Analytics & Performance',
                    'technology' => '🤖 IA & Technologie',
                    'portals' => '🌐 Portails & Connectivité',
                    'administration' => '⚙️ Administration'
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
            
            @foreach($categories as $categoryKey => $categoryName)
                @if(isset($groupedModules[$categoryKey]) && count($groupedModules[$categoryKey]) > 0)
                    <div class="category">
                        <h2>{{ $categoryName }}</h2>
                        <div class="modules-grid">
                            @foreach($groupedModules[$categoryKey] as $item)
                                <div class="module" onclick="handleModuleClick('{{ $item['route'] }}', '{{ $item['name'] }}')">
                                    <div style="display: flex; align-items: center; margin-bottom: 10px;">
                                        <span class="module-number">{{ $item['number'] ?? '?' }}</span>
                                        <span class="module-name">{{ $item['name'] }}</span>
                                    </div>
                                    <div class="module-description">{{ $item['description'] }}</div>
                                    <div class="module-meta">
                                        <span class="status-{{ $item['status'] }}">{{ $item['status'] }}</span>
                                        <span><span class="color-indicator color-{{ $item['color'] }}"></span>{{ $item['color'] }}</span>
                                        <span>{{ $item['route'] }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            @endforeach
        @else
            <div class="category">
                <h2>❌ Erreur</h2>
                <p>Aucun module trouvé</p>
            </div>
        @endif
    </div>

    <script>
        function handleModuleClick(route, moduleName) {
            // Effet visuel
            const module = event.currentTarget;
            module.style.transform = 'scale(0.95)';
            
            setTimeout(() => {
                module.style.transform = 'translateY(-5px)';
                
                // Notification
                const notification = document.createElement('div');
                notification.style.cssText = `
                    position: fixed;
                    top: 20px;
                    right: 20px;
                    background: linear-gradient(45deg, #00d4ff, #00ff88);
                    color: #000;
                    padding: 15px 25px;
                    border-radius: 10px;
                    font-weight: bold;
                    z-index: 1000;
                    animation: slideIn 0.3s ease;
                `;
                notification.textContent = `🚀 Accès au module: ${moduleName}`;
                document.body.appendChild(notification);
                
                setTimeout(() => {
                    notification.remove();
                }, 3000);
                
                // Redirection après un délai
                setTimeout(() => {
                    // Mapping des routes Laravel vers les URLs réelles
                    const routeMap = {
                        'modules.medical.index': '/medical',
                        'modules.healthcare.index': '/healthcare',
                        'pcma.index': '/pcma',
                        'modules.players.index': '/players',
                        'modules.referees.index': '/referees',
                        'modules.teams.index': '/teams',
                        'modules.competitions.index': '/competitions',
                        'modules.fixtures.index': '/fixtures',
                        'modules.associations.index': '/associations',
                        'modules.clubs.index': '/clubs',
                        'modules.confederations.index': '/confederations',
                        'modules.licenses.index': '/licenses',
                        'modules.player-passports.index': '/player-passports',
                        'modules.player-registration.index': '/player-registration',
                        'modules.performances.analytics': '/performances/analytics',
                        'dtn.index': '/dtn',
                        'rpm.index': '/rpm',
                        'gemini.index': '/gemini',
                        'fifa.dashboard': '/fifa/dashboard',
                        'player-portal.index': '/player-portal/index',
                        'referee-portal.index': '/referee-portal/index',
                        'team-portal.dashboard': '/team-portal/dashboard',
                        'portal.devices': '/portal/devices',
                        'modules.administration.index': '/administration',
                        'modules.finance.dashboard': '/finance',
                        'admin.content-management.index': '/admin/content-management',
                        'admin.transfer-management.index': '/admin/transfer-management'
                    };
                    
                    const url = routeMap[route] || '/modules';
                    window.location.href = url;
                }, 1500);
            }, 150);
        }
        
        // Animation CSS
        const style = document.createElement('style');
        style.textContent = `
            @keyframes slideIn {
                from { transform: translateX(100%); opacity: 0; }
                to { transform: translateX(0); opacity: 1; }
            }
        `;
        document.head.appendChild(style);
    </script>
</body>
</html>
