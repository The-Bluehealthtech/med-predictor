<!doctype html>
<html lang="fr">
<head>
<meta charset="utf-8">
<title>Passeport de transfert — {{ $passport['player']['name'] }}</title>
<style>
    @page { margin: 18mm 15mm 16mm 15mm; }
    body { font-family: DejaVu Sans, sans-serif; font-size: 9.5px; color: #111827; }
    .tp-header { border-bottom: 2px solid #334155; padding-bottom: 3mm; }
    .tp-kicker { font-size: 8px; font-weight: bold; letter-spacing: .08em; text-transform: uppercase; color: #334155; }
    .tp-title { font-size: 16px; font-weight: bold; margin: 1mm 0; } .tp-sub { font-size: 9px; color: #4b5563; }
    .tp-grid { width: 100%; border-collapse: collapse; margin-top: 3mm; } .tp-grid td { padding: 1.2mm 1.5mm; border-bottom: 1px solid #f3f4f6; } .tp-grid td:nth-child(odd) { color: #6b7280; width: 20%; }
    .tp-section-title { margin-top: 5mm; font-size: 11px; font-weight: bold; border-bottom: 1px solid #d1d5db; padding-bottom: 1mm; }
    .tp-note { font-size: 8px; color: #6b7280; margin-top: 1mm; } .tp-empty { font-size: 8.5px; color: #9ca3af; font-style: italic; margin-top: 1mm; }
    .tp-table { width: 100%; border-collapse: collapse; margin-top: 1.5mm; } .tp-table th { text-align: left; font-size: 7.5px; text-transform: uppercase; color: #6b7280; border-bottom: 1px solid #d1d5db; padding: 1mm; } .tp-table td { padding: 1.2mm 1mm; border-bottom: 1px solid #f3f4f6; }
    .tp-footer { margin-top: 6mm; font-size: 7.5px; color: #6b7280; }
</style>
</head>
<body>
@include('passports.transfer._document')
</body>
</html>
