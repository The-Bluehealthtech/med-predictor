<!DOCTYPE html>
<html>
<head>
    <title>Test Basique</title>
</head>
<body>
    <h1>Test Images Basique</h1>
    
    <h2>Image Rouge de Test</h2>
    <img src="{{ asset('images/association_logos/test_red.png') }}" width="100" height="100" alt="Test Rouge">
    
    <h2>Logo France FIFA (fichier local)</h2>
    <img src="{{ asset('images/association_logos/association_1_logo.png') }}" width="100" height="100" alt="Logo France Local">
    
    <h2>Logo France FIFA (direct depuis API)</h2>
    <img src="https://api.fifa.com/api/v3/picture/associations-sq-2/FRA" width="100" height="100" alt="Logo France API">
    
    <h2>Logo Portugal FIFA</h2>
    <img src="{{ asset('images/association_logos/association_3_logo.png') }}" width="100" height="100" alt="Logo Portugal">
    
    <h2>Logo Brésil FIFA</h2>
    <img src="{{ asset('images/association_logos/association_7_logo.png') }}" width="100" height="100" alt="Logo Brésil">
    
    <h2>Image SVG Inline</h2>
    <img src="data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTAwIiBoZWlnaHQ9IjEwMCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cmVjdCB3aWR0aD0iMTAwIiBoZWlnaHQ9IjEwMCIgZmlsbD0icmVkIi8+PC9zdmc+" width="100" height="100" alt="SVG Rouge">
</body>
</html>


