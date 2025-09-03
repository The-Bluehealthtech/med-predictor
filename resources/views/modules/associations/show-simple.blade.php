<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $association->name }} - Test</title>
</head>
<body>
    <h1>{{ $association->name }}</h1>
    <p>Pays: {{ $association->country }}</p>
    <p>Statut: {{ $association->status }}</p>
    <p>Clubs: {{ $association->clubs->count() }}</p>
    <p>Joueurs: {{ $association->players->count() }}</p>
    <a href="/associations-view">Retour</a>
</body>
</html>



