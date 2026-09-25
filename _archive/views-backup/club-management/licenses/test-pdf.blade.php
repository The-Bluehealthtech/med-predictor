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
            padding: 0;
            font-family: Arial, sans-serif;
            font-size: 8px;
            background: white;
        }
        
        .test-card {
            width: 85.6mm;
            height: 54mm;
            background: #1e3a8a;
            color: white;
            padding: 2mm;
            box-sizing: border-box;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
        }
        
        .test-title {
            font-size: 12px;
            font-weight: bold;
            margin-bottom: 2mm;
        }
        
        .test-info {
            font-size: 8px;
            margin-bottom: 1mm;
        }
        
        .test-data {
            font-size: 6px;
            background: rgba(255,255,255,0.2);
            padding: 1mm;
            border-radius: 1mm;
            margin-top: 2mm;
        }
    </style>
</head>
<body>
    <div class="test-card">
        <div class="test-title">TEST LICENSE CARD</div>
        <div class="test-info">This is a test to verify PDF generation</div>
        <div class="test-data">
            License ID: {{ $license->id ?? 'N/A' }}<br>
            Player: {{ $license->player->name ?? 'No Player' }}<br>
            Club: {{ $license->club->name ?? 'No Club' }}<br>
            Status: {{ $license->status ?? 'N/A' }}<br>
            Type: {{ $license->license_type ?? 'N/A' }}
        </div>
    </div>
</body>
</html> 