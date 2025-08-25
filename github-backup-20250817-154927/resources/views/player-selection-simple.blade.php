<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sélection des Joueurs - FIT Platform</title>
</head>
<body>
    <h1>🏆 Sélection des Joueurs</h1>
    <p>Choisissez un joueur pour accéder à son portail</p>
    
    <h2>📊 Statistiques</h2>
    <p>Total des joueurs: {{ $players->count() }}</p>
    <p>Total des clubs: {{ $clubs->count() }}</p>
    <p>Total des nationalités: {{ $nationalities->count() }}</p>
    
    <h2>⚽ Liste des Joueurs</h2>
    <table border="1">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nom</th>
                <th>Position</th>
                <th>Club</th>
                <th>Nationalité</th>
                <th>Score FIFA</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($players as $player)
            <tr>
                <td>{{ $player->id }}</td>
                <td>{{ $player->first_name }} {{ $player->last_name }}</td>
                <td>{{ $player->position ?? 'N/A' }}</td>
                <td>{{ $player->club->name ?? 'Sans club' }}</td>
                <td>{{ $player->nationality ?? 'N/A' }}</td>
                <td>{{ $player->overall_rating ?? 'N/A' }}</td>
                <td>
                    <a href="{{ route('joueur.portal', $player->id) }}">Accéder au Portail</a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    
    <h2>🔗 Liens de test</h2>
    <ul>
        <li><a href="{{ route('joueur.portal', 32) }}">Portail du joueur 32</a></li>
        <li><a href="{{ route('joueur.portal', 33) }}">Portail du joueur 33</a></li>
        <li><a href="{{ route('joueur.portal', 34) }}">Portail du joueur 34</a></li>
    </ul>
</body>
</html>






