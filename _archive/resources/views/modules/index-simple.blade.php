<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modules FIT</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .module { border: 1px solid #ccc; margin: 10px; padding: 15px; border-radius: 5px; }
        .module:hover { background-color: #f5f5f5; cursor: pointer; }
        .number { font-weight: bold; color: #666; }
        .category { background-color: #e9ecef; padding: 5px; margin: 10px 0; border-radius: 3px; }
    </style>
</head>
<body>
    <h1>Modules FIT - {{ $totalModules ?? count($modules) }} modules disponibles</h1>
    
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
                </div>
                
                @foreach($groupedModules[$categoryKey] as $item)
                    <div class="module">
                        <span class="number">#{{ $item['number'] ?? '?' }}</span>
                        <strong>{{ $item['name'] }}</strong> - {{ $item['description'] }}
                        <br>
                        <small>Route: {{ $item['route'] }} | Statut: {{ $item['status'] }} | Couleur: {{ $item['color'] }}</small>
                    </div>
                @endforeach
            @endif
        @endforeach
    @else
        <p>Erreur: Aucun module trouvé</p>
    @endif
</body>
</html>
