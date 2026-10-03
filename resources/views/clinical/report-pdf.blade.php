<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>{{ $label }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #111; }
        h1 { font-size: 16px; margin: 0 0 4px; }
        .muted { color: #555; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border-bottom: 1px solid #ddd; padding: 5px 4px; text-align: left; vertical-align: top; }
        th { background: #f3f4f6; font-size: 10px; text-transform: uppercase; }
        .box { margin-top: 10px; padding: 8px; border: 1px solid #ddd; }
        .foot { margin-top: 16px; font-size: 9px; color: #666; }
    </style>
</head>
<body>
    <h1>{{ $label }}</h1>
    <div class="muted">
        {{ trim(($player->first_name ?? '') . ' ' . ($player->last_name ?? '')) ?: $player->name }}
        @if($player->date_of_birth) · né(e) le {{ \Illuminate\Support\Carbon::parse($player->date_of_birth)->format('d/m/Y') }}@endif
    </div>
    <div class="muted">
        Établissement : {{ $source ?? 'non précisé' }} · statut : {{ $report['status'] ?? '—' }}
        @if(!empty($report['issued'])) · émis le {{ \Illuminate\Support\Carbon::parse($report['issued'])->format('d/m/Y H:i') }}@endif
    </div>

    @if(!empty($report['conclusion']))
        <div class="box"><strong>Conclusion :</strong> {{ $report['conclusion'] }}</div>
    @endif

    @if($results)
        <table>
            <thead><tr><th>Examen</th><th>Résultat</th><th>Référence</th><th>Interprétation</th></tr></thead>
            <tbody>
                @foreach($results as $result)
                    <tr>
                        <td>{{ $result['label'] }}@if($result['codes'])<br><span class="muted">{{ $result['codes'] }}</span>@endif</td>
                        <td>{{ $result['value'] ?? '—' }}</td>
                        <td>{{ $result['range'] ?? '—' }}</td>
                        <td>{{ $result['interpretation'] ?? '—' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <p class="foot">Récapitulatif généré par FIT le {{ now()->format('d/m/Y H:i') }} à partir du compte rendu DiagnosticReport/{{ $report['id'] ?? '?' }} du serveur FHIR de FIT, transmis par l’établissement. Aucun contenu n’a été ajouté ni interprété par FIT.</p>
</body>
</html>
