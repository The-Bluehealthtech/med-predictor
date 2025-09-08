<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Logo FTF - Fédération Tunisienne de Football</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            margin: 0;
            padding: 20px;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        
        .container {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            text-align: center;
            max-width: 600px;
            width: 100%;
        }
        
        h1 {
            color: #333;
            margin-bottom: 30px;
            font-size: 2.5em;
        }
        
        .logo-section {
            margin: 30px 0;
            padding: 20px;
            border: 2px dashed #ddd;
            border-radius: 10px;
            background: #f9f9f9;
        }
        
        .logo-ftf {
            max-width: 300px;
            height: auto;
            border: 3px solid #e74c3c;
            border-radius: 10px;
            box-shadow: 0 10px 20px rgba(0,0,0,0.2);
            transition: transform 0.3s ease;
        }
        
        .logo-ftf:hover {
            transform: scale(1.05);
        }
        
        .info-box {
            background: #e8f4fd;
            border-left: 4px solid #3498db;
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
            text-align: left;
        }
        
        .success {
            background: #d4edda;
            border-left-color: #28a745;
            color: #155724;
        }
        
        .file-info {
            background: #fff3cd;
            border-left-color: #ffc107;
            color: #856404;
            font-family: monospace;
            font-size: 0.9em;
        }
        
        .btn {
            background: #e74c3c;
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 25px;
            text-decoration: none;
            display: inline-block;
            margin: 10px;
            font-weight: bold;
            transition: background 0.3s ease;
        }
        
        .btn:hover {
            background: #c0392b;
        }
        
        .btn-secondary {
            background: #95a5a6;
        }
        
        .btn-secondary:hover {
            background: #7f8c8d;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🏆 Test Logo FTF</h1>
        <h2>Fédération Tunisienne de Football</h2>
        
        <div class="info-box success">
            <strong>✅ Succès !</strong> Le logo officiel de la FTF a été téléchargé avec succès.
        </div>
        
        <div class="logo-section">
            <h3>Logo Officiel FTF - Version Haute Qualité</h3>
            <img src="/images/associations/ftf-logo-officiel.png" 
                 alt="Logo officiel de la Fédération Tunisienne de Football" 
                 class="logo-ftf">
            <p><em>Logo officiel de la FTF - Source: [Wikimedia Commons](https://upload.wikimedia.org/wikipedia/fr/thumb/3/33/Logo_federation_tunisienne_de_football.svg/1200px-Logo_federation_tunisienne_de_football.svg.png)</em></p>
        </div>
        
        <div class="logo-section">
            <h3>Logo FTF - Version Précédente</h3>
            <img src="/images/associations/ftf-logo.png" 
                 alt="Logo FTF précédent" 
                 class="logo-ftf" style="max-width: 200px;">
            <p><em>Version précédente (2.1 KB)</em></p>
        </div>
        
        <div class="info-box file-info">
            <strong>📁 Informations du fichier officiel :</strong><br>
            <strong>Nom :</strong> ftf-logo-officiel.png<br>
            <strong>Taille :</strong> 250 KB<br>
            <strong>Chemin :</strong> /public/images/associations/ftf-logo-officiel.png<br>
            <strong>Format :</strong> PNG haute qualité<br>
            <strong>Source :</strong> <a href="https://upload.wikimedia.org/wikipedia/fr/thumb/3/33/Logo_federation_tunisienne_de_football.svg/1200px-Logo_federation_tunisienne_de_football.svg.png" target="_blank">Wikimedia Commons</a>
        </div>
        
        <div class="info-box">
            <strong>🔍 Test d'affichage :</strong><br>
            Si vous voyez le logo ci-dessus, cela signifie que le fichier est correctement accessible et affiché.
        </div>
        
        <div style="margin-top: 30px;">
            <a href="/fifa-portal" class="btn">🏟️ Retour au Portail FIFA</a>
            <a href="/" class="btn btn-secondary">🏠 Accueil</a>
        </div>
        
        <div style="margin-top: 20px; font-size: 0.9em; color: #666;">
            <p><strong>Note :</strong> Ce logo sera maintenant utilisé dans le portail FIFA pour l'association FTF.</p>
        </div>
    </div>
</body>
</html>
