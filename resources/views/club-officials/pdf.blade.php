<!doctype html>
<html lang="fr">
<head>
<meta charset="utf-8">
<title>Fiche FIFA Connect — {{ $official->fullName() }}</title>
<style>
    @page { margin: 18mm 15mm; }
    body { font-family: DejaVu Sans, sans-serif; font-size: 9.5px; color: #0f172a; }
    h1 { font-size: 16px; margin: 0 0 1mm; } .k { font-size: 8px; font-weight: bold; letter-spacing: .08em; text-transform: uppercase; color: #475569; }
    .head { border-bottom: 2px solid #334155; padding-bottom: 3mm; margin-bottom: 4mm; }
    .fc-table { width: 100%; border-collapse: collapse; } .fc-table th { text-align: left; font-size: 7.5px; text-transform: uppercase; letter-spacing: .06em; color: #475569; background: #f1f5f9; padding: 1.5mm; }
    .fc-table td { padding: 1.3mm 1.5mm; border-bottom: 1px solid #e2e8f0; vertical-align: top; } .fc-k { color: #64748b; font-size: 8px; width: 22%; } .fc-empty { color: #94a3b8; font-style: italic; }
    .foot { margin-top: 5mm; font-size: 7.5px; color: #64748b; }
</style>
</head>
<body>
<div class="head"><div class="k">Fiche FIFA Connect · {{ str_replace(' (Démo)', '', $club->name) }}</div><h1>{{ $official->fullName() }}</h1><div>{{ $official->roleLabel() }}</div></div>
@include('club-officials.partials._sheet')
<div class="foot">Établie le {{ now()->format('d/m/Y H:i') }} à partir des données enregistrées dans FIT, avec les noms de champs FIFA Connect (Person, Registration, Certification). Ne remplace pas l'enregistrement officiel dans FIFA Connect.</div>
</body>
</html>
