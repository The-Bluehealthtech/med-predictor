<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head><meta charset="utf-8"><title>PCMA</title>
<style>body{font-family:DejaVu Sans,sans-serif;font-size:11px;color:#172033}h1{color:#326295}table{width:100%;border-collapse:collapse}td{border:1px solid #ddd;padding:6px;vertical-align:top}pre{white-space:pre-wrap}</style></head>
<body>
<h1>PCMA {{ $isDraft ?? false ? (app()->getLocale() === 'en' ? '— Draft preview' : '— Aperçu brouillon') : '#'.$pcma->id }}</h1>
<p>{{ $athlete?->name ?? '—' }} · {{ $pcma->assessment_date?->format('Y-m-d') ?? ($formData['assessment_date'] ?? '—') }}</p>
<p>{{ app()->getLocale() === 'en' ? 'Assessor' : 'Médecin évaluateur' }} : {{ $pcma->assessor?->name ?? '—' }}</p>
@if($isDraft ?? false)<p>{{ app()->getLocale() === 'en' ? 'Unsaved preview. No certified signature.' : 'Aperçu non enregistré. Aucune signature certifiée.' }}</p>@endif
<table>
@foreach($formData as $key => $value)
    @if(!in_array($key, ['_token','signature_data','signature_image','is_signed','signed_by','signed_at']))
    <tr><td>{{ str_replace('_', ' ', $key) }}</td><td>{{ is_array($value) ? json_encode($value, JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT) : $value }}</td></tr>
    @endif
@endforeach
</table>
<p>{{ $generatedAt->format('Y-m-d H:i') }}</p>
</body></html>
