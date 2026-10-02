<!doctype html>
<html lang="fr">
<head>
<meta charset="utf-8">
<title>Passeport médical — {{ $summary['patient']['name'] }}</title>
<style>
    @page { margin: 18mm 15mm 16mm 15mm; }
    body { font-family: DejaVu Sans, sans-serif; font-size: 9.5px; color: #111827; }
    .ips-header { border-bottom: 2px solid #b91c1c; padding-bottom: 3mm; }
    .ips-kicker { font-size: 8px; font-weight: bold; letter-spacing: .08em; text-transform: uppercase; color: #b91c1c; }
    .ips-title { font-size: 16px; font-weight: bold; margin: 1mm 0; }
    .ips-sub { font-size: 9px; color: #4b5563; }
    .ips-meta { margin-top: 2mm; } .ips-meta td { font-size: 8.5px; padding: 0 3mm 0 0; } .ips-meta td:first-child { color: #6b7280; }
    .ips-notice { margin: 3mm 0; padding: 2mm 3mm; background: #fef3c7; color: #78350f; font-size: 8.5px; }
    .ips-section { margin-top: 4mm; page-break-inside: avoid; }
    .ips-section-title { font-size: 11px; font-weight: bold; border-bottom: 1px solid #d1d5db; padding-bottom: 1mm; }
    .ips-code { font-size: 7.5px; font-weight: normal; color: #6b7280; }
    .ips-table { width: 100%; border-collapse: collapse; margin-top: 1.5mm; } .ips-table td { padding: 1.2mm 1mm; border-bottom: 1px solid #f3f4f6; vertical-align: top; }
    .ips-label { font-weight: bold; width: 38%; } .ips-date { color: #6b7280; text-align: right; width: 18mm; }
    .ips-empty { font-size: 8.5px; color: #9ca3af; font-style: italic; margin-top: 1mm; }
    .ips-footer { margin-top: 6mm; font-size: 7.5px; color: #6b7280; }
    .sign { margin-top: 6mm; width: 100%; border-collapse: collapse; } .sign td { border: 1px solid #9ca3af; height: 16mm; padding: 2mm; vertical-align: top; font-size: 8.5px; }
</style>
</head>
<body>
@include('passports.medical._document')
@php $att = $attestation ?? ['state' => 'none']; @endphp
<table class="sign"><tr>
    <td style="width:50%">@if($att['state'] === 'valid')<strong>Attesté électroniquement</strong><br>{{ $att['attestation']->signer_name }}@if($att['attestation']->signer_license) · n° {{ $att['attestation']->signer_license }}@endif<br>le {{ $att['attestation']->signed_at->format('d/m/Y à H:i') }}<br><span style="font-size:7px">SHA-256 {{ $att['attestation']->content_sha256 }}</span>@else Médecin responsable — nom, date et signature @endif</td>
    <td>Accord du joueur pour ce partage — date et signature</td>
</tr></table>
</body>
</html>
