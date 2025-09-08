<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Logos Simple</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .logo-test { margin: 20px; padding: 20px; border: 1px solid #ccc; }
        .logo-img { width: 100px; height: 100px; border: 2px solid red; }
        .debug-info { background: #f0f0f0; padding: 10px; margin: 10px 0; }
    </style>
</head>
<body>
    <h1>🔍 Test Logos Simple - Diagnostic</h1>
    
    <div class="logo-test">
        <h2>Logo Association 1 (FFF) - Original</h2>
        <img src="{{ asset('images/association_logos/association_1_logo.png') }}" 
             alt="Logo FFF" 
             class="logo-img"
             onload="console.log('✅ Image chargée:', this.src)"
             onerror="console.log('❌ Erreur image:', this.src)">
        
        <div class="debug-info">
            <p><strong>Chemin:</strong> {{ asset('images/association_logos/association_1_logo.png') }}</p>
            <p><strong>URL complète:</strong> http://localhost:8080/images/association_logos/association_1_logo.png</p>
            <p><strong>Fichier existe:</strong> {{ file_exists(public_path('images/association_logos/association_1_logo.png')) ? 'OUI' : 'NON' }}</p>
        </div>
    </div>

    <div class="logo-test">
        <h2>Logo Association 1 (FFF) - Avec fond rouge</h2>
        <img src="{{ asset('images/association_logos/test_visible.png') }}" 
             alt="Logo FFF Test" 
             class="logo-img"
             onload="console.log('✅ Image test chargée:', this.src)"
             onerror="console.log('❌ Erreur image test:', this.src)">
        
        <div class="debug-info">
            <p><strong>Chemin:</strong> {{ asset('images/association_logos/test_visible.png') }}</p>
            <p><strong>Fichier existe:</strong> {{ file_exists(public_path('images/association_logos/test_visible.png')) ? 'OUI' : 'NON' }}</p>
        </div>
    </div>

    <div class="logo-test">
        <h2>Logo Association 3 (Portugal)</h2>
        <img src="{{ asset('images/association_logos/association_3_logo.png') }}" 
             alt="Logo Portugal" 
             class="logo-img"
             onload="console.log('✅ Image chargée:', this.src)"
             onerror="console.log('❌ Erreur image:', this.src)">
        
        <div class="debug-info">
            <p><strong>Chemin:</strong> {{ asset('images/association_logos/association_3_logo.png') }}</p>
            <p><strong>Fichier existe:</strong> {{ file_exists(public_path('images/association_logos/association_3_logo.png')) ? 'OUI' : 'NON' }}</p>
        </div>
    </div>

    <div class="logo-test">
        <h2>Test avec une image de test</h2>
        <img src="data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTAwIiBoZWlnaHQ9IjEwMCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cmVjdCB3aWR0aD0iMTAwIiBoZWlnaHQ9IjEwMCIgZmlsbD0iYmx1ZSIvPjx0ZXh0IHg9IjUwIiB5PSI1MCIgZm9udC1mYW1pbHk9IkFyaWFsIiBmb250LXNpemU9IjE0IiBmaWxsPSJ3aGl0ZSIgdGV4dC1hbmNob3I9Im1pZGRsZSIgZHk9Ii4zZW0iPlRFU1Q8L3RleHQ+PC9zdmc+" 
             alt="Test SVG" 
             class="logo-img">
        <p>Cette image SVG de test devrait s'afficher en bleu avec "TEST" écrit dessus.</p>
    </div>

    <script>
        console.log('🔍 Page de test chargée');
        
        // Vérifier les images après chargement
        window.addEventListener('load', function() {
            const images = document.querySelectorAll('img');
            images.forEach((img, index) => {
                console.log(`Image ${index + 1}:`, img.src);
                if (img.complete) {
                    console.log(`Image ${index + 1} chargée:`, img.naturalWidth, 'x', img.naturalHeight);
                }
            });
        });
    </script>
</body>
</html>


