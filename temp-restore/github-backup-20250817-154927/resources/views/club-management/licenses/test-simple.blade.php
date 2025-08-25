<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Test License</title>
    <style>
        @page {
            margin: 0;
            size: 85.6mm 54mm;
        }
        
        body {
            margin: 0;
            padding: 2mm;
            font-family: Arial, sans-serif;
            font-size: 8px;
            background: #1e3a8a;
            color: white;
        }
        
        .test-content {
            text-align: center;
            padding: 10mm;
        }
        
        h1 {
            font-size: 10px;
            margin-bottom: 5mm;
        }
        
        p {
            margin: 2mm 0;
        }
    </style>
</head>
<body>
    <div class="test-content">
        <h1>TEST LICENSE</h1>
        <p>Player: {{ $license->player->name ?? 'Test Player' }}</p>
        <p>Club: {{ $license->club->name ?? 'Test Club' }}</p>
        <p>License: {{ $license->license_number ?? 'TEST-001' }}</p>
        <p>CR80 Format Test</p>
    </div>
</body>
</html> 