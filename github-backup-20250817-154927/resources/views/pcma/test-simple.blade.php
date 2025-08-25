<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PCMA Test</title>
</head>
<body>
    <h1>PCMA Test View</h1>
    <p>This is a simple test view to check if the route is working.</p>
    
    <h2>Athletes:</h2>
    <ul>
        @foreach($athletes as $athlete)
            <li>{{ $athlete['name'] }}</li>
        @endforeach
    </ul>
    
    <h2>Users:</h2>
    <ul>
        @foreach($users as $user)
            <li>{{ $user['name'] }}</li>
        @endforeach
    </ul>
</body>
</html> 