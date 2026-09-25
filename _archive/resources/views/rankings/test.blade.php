<!DOCTYPE html>
<html>
<head>
    <title>Rankings Test</title>
</head>
<body>
    <h1>Rankings Test Page</h1>
    
    <h2>Debug Info:</h2>
    <p>Rankings Array Count: {{ count($rankingsArray) }}</p>
    <p>Competition: {{ $competition->name ?? 'Not found' }}</p>
    <p>Latest Ranking Round: {{ $latestRanking->round ?? 'Not found' }}</p>
    
    @if(count($rankingsArray) > 0)
        <p>First Team: {{ $rankingsArray[0]['team_name'] ?? 'No team name' }} - {{ $rankingsArray[0]['points'] ?? 'No points' }} pts</p>
        <p>Last Team: {{ $rankingsArray[count($rankingsArray)-1]['team_name'] ?? 'No team name' }} - {{ $rankingsArray[count($rankingsArray)-1]['points'] ?? 'No points' }} pts</p>
        
        <h2>All Teams:</h2>
        <ul>
        @foreach($rankingsArray as $ranking)
            <li>{{ $ranking['position'] }}. {{ $ranking['team_name'] }} - {{ $ranking['points'] }} pts</li>
        @endforeach
        </ul>
    @else
        <p>No teams in rankings array</p>
    @endif
</body>
</html> 