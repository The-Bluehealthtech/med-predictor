<!DOCTYPE html>
<html>
<head>
    <title>Test Competition Management</title>
</head>
<body>
    <h1>TEST PAGE</h1>
    <p>This is a simple test page</p>
    
    <h2>Debug Information:</h2>
    <p>Total Competitions: {{ $stats['total'] ?? 'N/A' }}</p>
    <p>Competitions Count: {{ $competitions->count() ?? 'N/A' }}</p>
    <p>User Role: {{ auth()->user()->role ?? 'N/A' }}</p>
    
    @if(isset($competitions) && $competitions->count() > 0)
        <h2>Competitions Found:</h2>
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