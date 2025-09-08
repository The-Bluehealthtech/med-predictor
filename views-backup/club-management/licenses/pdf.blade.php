<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Player License - {{ $license->player->name ?? 'Unknown' }}</title>
    <style>
        @page {
            margin: 0;
            size: 85.6mm 54mm;
        }
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            font-size: 6px;
            background: #fff;
            color: #222;
        }
        .card-table {
            width: 85.6mm;
            height: 54mm;
            border-collapse: collapse;
            table-layout: fixed;
        }
        .header-row {
            height: 12mm;
            background: #1e3a8a;
            color: #fff;
        }
        .header-left {
            width: 25%;
            padding: 1mm;
            text-align: center;
            vertical-align: middle;
        }
        .header-center {
            width: 50%;
            padding: 1mm;
            text-align: center;
            vertical-align: middle;
        }
        .header-right {
            width: 25%;
            padding: 1mm;
            text-align: center;
            vertical-align: middle;
        }
        .license-title {
            font-size: 7px;
            font-weight: bold;
            margin: 0;
        }
        .license-number {
            font-size: 5px;
            background: #dc2626;
            color: white;
            padding: 0.5mm 1mm;
            border-radius: 1mm;
            display: inline-block;
        }
        .content-row {
            height: 35mm;
        }
        .photo-cell {
            width: 30%;
            padding: 1mm;
            text-align: center;
            vertical-align: top;
        }
        .details-cell {
            width: 45%;
            padding: 1mm;
            vertical-align: top;
        }
        .qr-cell {
            width: 25%;
            padding: 1mm;
            text-align: center;
            vertical-align: top;
        }
        .player-name {
            font-size: 7px;
            font-weight: bold;
            color: #1e3a8a;
            margin-bottom: 1mm;
            text-align: center;
            background: #f3f4f6;
            padding: 1mm;
            border-radius: 1mm;
        }
        .details-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 5px;
        }
        .details-table td {
            padding: 0.5mm;
            border-bottom: 1px solid #e5e7eb;
        }
        .details-table td:first-child {
            font-weight: bold;
            color: #374151;
            width: 40%;
        }
        .details-table td:last-child {
            color: #1f2937;
            width: 60%;
        }
        .logo-container {
            width: 8mm;
            height: 8mm;
            margin: 0 auto;
            border: 1px solid #3b82f6;
            border-radius: 1mm;
            overflow: hidden;
            background: #fff;
        }
        .logo-img {
            width: 100%;
            height: 100%;
            display: block;
        }
        .photo-container {
            width: 12mm;
            height: 15mm;
            margin: 0 auto 1mm auto;
            border: 1px solid #3b82f6;
            border-radius: 1mm;
            overflow: hidden;
            background: #fff;
        }
        .photo-img {
            width: 100%;
            height: 100%;
            display: block;
        }
        .qr-container {
            width: 12mm;
            height: 12mm;
            margin: 0 auto;
            border: 1px solid #3b82f6;
            border-radius: 1mm;
            overflow: hidden;
            background: #fff;
        }
        .qr-img {
            width: 100%;
            height: 100%;
            display: block;
        }
        .footer-row {
            height: 7mm;
            background: #f3f4f6;
            border-top: 1px solid #e5e7eb;
        }
        .footer-left {
            width: 50%;
            padding: 1mm;
            text-align: left;
            vertical-align: middle;
        }
        .footer-right {
            width: 50%;
            padding: 1mm;
            text-align: right;
            vertical-align: middle;
        }
        .association-name {
            font-size: 5px;
            color: #374151;
            margin: 0;
        }
        .valid-until {
            font-size: 5px;
            color: #dc2626;
            font-weight: bold;
            margin: 0;
        }
    </style>
</head>
<body>
    <table class="card-table">
        <!-- Header Row -->
        <tr class="header-row">
            <td class="header-left">
                <div class="logo-container">
                    <img class="logo-img" src="{{ $associationLogo }}" alt="Association Logo">
                </div>
            </td>
            <td class="header-center">
                <h1 class="license-title">PLAYER LICENSE</h1>
            </td>
            <td class="header-right">
                <div class="license-number">{{ $license->license_number ?? 'N/A' }}</div>
            </td>
        </tr>
        
        <!-- Content Row -->
        <tr class="content-row">
            <td class="photo-cell">
                <div class="photo-container">
                    <img class="photo-img" src="{{ $playerPhoto }}" alt="Player Photo">
                </div>
                <div class="logo-container">
                    <img class="logo-img" src="{{ $clubLogo }}" alt="Club Logo">
                </div>
            </td>
            <td class="details-cell">
                <div class="player-name">{{ $license->player->name ?? 'Unknown Player' }}</div>
                <table class="details-table">
                    <tr>
                        <td>Club:</td>
                        <td>{{ $license->club->name ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td>Position:</td>
                        <td>{{ $license->player->position ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td>Age:</td>
                        <td>{{ $license->player->age ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td>Type:</td>
                        <td>{{ ucfirst($license->license_type ?? 'N/A') }}</td>
                    </tr>
                    <tr>
                        <td>Status:</td>
                        <td>{{ ucfirst($license->status ?? 'N/A') }}</td>
                    </tr>
                    <tr>
                        <td>Valid Until:</td>
                        <td>{{ $license->expiry_date ? \Carbon\Carbon::parse($license->expiry_date)->format('d/m/Y') : 'N/A' }}</td>
                    </tr>
                </table>
            </td>
            <td class="qr-cell">
                @if($qrCode)
                    <div class="qr-container">
                        <img class="qr-img" src="{{ $qrCode }}" alt="QR Code">
                    </div>
                @else
                    <div style="width: 12mm; height: 12mm; margin: 0 auto; background: #e5e7eb; border: 1px solid #3b82f6; border-radius: 1mm; display: flex; align-items: center; justify-content: center; font-size: 4px; color: #3b82f6; text-align: center;">
                        QR<br>CODE<br>N/A
                    </div>
                @endif
            </td>
        </tr>
        
        <!-- Footer Row -->
        <tr class="footer-row">
            <td class="footer-left">
                <p class="association-name">{{ $license->club->association->name ?? 'Football Association' }}</p>
            </td>
            <td class="footer-right">
                <p class="valid-until">Valid: {{ $license->issue_date ? \Carbon\Carbon::parse($license->issue_date)->format('d/m/Y') : 'N/A' }}</p>
            </td>
        </tr>
    </table>
</body>
</html> 