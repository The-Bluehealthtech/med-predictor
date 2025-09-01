<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Test Images Simple</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .test-image { margin: 20px; padding: 20px; border: 2px solid blue; }
        img { width: 100px; height: 100px; border: 2px solid red; }
        .debug { background: #f0f0f0; padding: 10px; margin: 10px 0; }
    </style>
</head>
<body>
    <h1>🔍 Test Images Très Simple</h1>
    
    <div class="test-image">
        <h2>1. Image Rouge de Test (créée localement)</h2>
        <img src="{{ asset('images/association_logos/test_red.png') }}" 
             alt="Test Rouge"
             onload="console.log('✅ Image rouge chargée')"
             onerror="console.log('❌ Erreur image rouge')">
        <div class="debug">
            <p><strong>Chemin:</strong> {{ asset('images/association_logos/test_red.png') }}</p>
            <p><strong>URL complète:</strong> http://localhost:8080/images/association_logos/test_red.png</p>
        </div>
    </div>

    <div class="test-image">
        <h2>2. Logo France FIFA (téléchargé)</h2>
        <img src="{{ asset('images/association_logos/association_1_logo.png') }}" 
             alt="Logo France"
             onload="console.log('✅ Logo France chargé')"
             onerror="console.log('❌ Erreur logo France')">
        <div class="debug">
            <p><strong>Chemin:</strong> {{ asset('images/association_logos/association_1_logo.png') }}</p>
            <p><strong>URL complète:</strong> http://localhost:8080/images/association_logos/association_1_logo.png</p>
        </div>
    </div>

    <div class="test-image">
        <h2>3. Logo France FIFA avec Fond Bleu</h2>
        <img src="{{ asset('images/association_logos/test_france_blue.png') }}" 
             alt="Logo France Bleu"
             onload="console.log('✅ Logo France Bleu chargé')"
             onerror="console.log('❌ Erreur logo France Bleu')">
        <div class="debug">
            <p><strong>Chemin:</strong> {{ asset('images/association_logos/test_france_blue.png') }}</p>
            <p><strong>URL complète:</strong> http://localhost:8080/images/association_logos/test_france_blue.png</p>
        </div>
    </div>

    <div class="test-image">
        <h2>3. Image SVG en Base64 (inline)</h2>
        <img src="data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTAwIiBoZWlnaHQ9IjEwMCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cmVjdCB3aWR0aD0iMTAwIiBoZWlnaHQ9IjEwMCIgZmlsbD0iZ3JlZW4iLz48dGV4dCB4PSI1MCIgeT0iNTAiIGZvbnQtZmFtaWx5PSJBcmlhbCIgZm9udC1zaXplPSIxNCIgZmlsbD0id2hpdGUiIHRleHQtYW5jaG9yPSJtaWRkbGUiIGR5PSIuM2VtIj5URVNUIElNR0U8L3RleHQ+PC9zdmc+" 
             alt="Test SVG"
             onload="console.log('✅ SVG inline chargé')"
             onerror="console.log('❌ Erreur SVG inline')">
        <p>Cette image SVG devrait s'afficher en vert avec "TEST IMAGE" écrit dessus.</p>
    </div>

    <div class="test-image">
        <h2>4. Image Externe (Google)</h2>
        <img src="https://www.google.com/images/branding/googlelogo/1x/googlelogo_color_272x92dp.png" 
             alt="Google Logo"
             onload="console.log('✅ Google logo chargé')"
             onerror="console.log('❌ Erreur Google logo')">
        <p>Cette image externe de Google devrait s'afficher.</p>
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


