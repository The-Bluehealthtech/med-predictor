<!DOCTYPE html>
<html>
<head>
    <title>Test Authentification</title>
</head>
<body>
    <h1>Test d'Authentification</h1>
    
    <h2>État de l'authentification :</h2>
    <p><strong>Connecté :</strong> {{ $isAuthenticated ? 'OUI' : 'NON' }}</p>
    
    @if($isAuthenticated && $user)
        <h2>Informations utilisateur :</h2>
        <p><strong>ID :</strong> {{ $user->id }}</p>
        <p><strong>Email :</strong> {{ $user->email }}</p>
        <p><strong>Rôle :</strong> {{ $user->role }}</p>
        <p><strong>Nom :</strong> {{ $user->name }}</p>
        
        <h2>Test d'accès au portail :</h2>
        <a href="/portail-joueur/1" style="background: blue; color: white; padding: 10px; text-decoration: none; border-radius: 5px;">
            Accéder au Portail Joueur
        </a>
    @else
        <h2>Non connecté</h2>
        <p>Vous n'êtes pas connecté. <a href="/login">Se connecter</a></p>
    @endif
    
    <hr>
    <h2>Debug Info :</h2>
    <p><strong>Session ID :</strong> {{ session()->getId() }}</p>
    <p><strong>User ID dans session :</strong> {{ session('auth.user_id') ?? 'Non défini' }}</p>
</body>
</html>





