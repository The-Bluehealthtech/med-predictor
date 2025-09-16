<!DOCTYPE html>
<html>
<head>
    <title>Test Competitions</title>
</head>
<body>
    <h1>Test Competitions Page</h1>
    
    <h2>Statistics:</h2>
    <ul>
        <li>Total Competitions: {{ $stats['total_competitions'] }}</li>
        <li>Active Competitions: {{ $stats['active_competitions'] }}</li>
        <li>Total Players: {{ $stats['total_players'] }}</li>
        <li>Total Clubs: {{ $stats['total_clubs'] }}</li>
    </ul>
    
    <h2>Competitions:</h2>
    @if($competitions->count() > 0)
        <ul>
        @foreach($competitions as $competition)
            <li>{{ $competition->name }} ({{ $competition->status }})</li>
        @endforeach
        </ul>
    @else
        <p>No competitions found</p>
    @endif
</body>
</html> 