<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head><meta charset="utf-8"><title>PCMA</title>
<style>body{font-family:DejaVu Sans,sans-serif;font-size:11px;color:#172033}h1{color:#326295}table{width:100%;border-collapse:collapse}td{border:1px solid #ddd;padding:6px;vertical-align:top}pre{white-space:pre-wrap}</style></head>
<body>
<h1>PCMA {{ $isDraft ? __('pcma_workflow.draft_preview') : '#'.$pcma->id }}</h1>
<p>{{ $pcma->player?->name ?? $pcma->athlete?->name ?? '—' }} · {{ $pcma->assessment_date?->format('Y-m-d') ?? '—' }}</p>
<p>{{ __('pcma_workflow.assessor') }} : {{ $pcma->assessor?->name ?? '—' }}</p>
@if($isDraft)<p>{{ __('pcma_workflow.preview_notice') }}</p>@endif
@if(!$isDraft && $pcma->is_signed)
<p>{{ __('pcma_workflow.signed_by') }} : {{ $pcma->signed_by ?? '—' }} · {{ $pcma->signed_at?->format('Y-m-d H:i') ?? '—' }}</p>
@endif
<table>
@foreach($formData as $key => $value)
<tr><td>{{ str_replace('_', ' ', $key) }}</td><td><pre>{{ is_array($value) ? json_encode($value, JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT) : ($key === 'type' && $value === 'pcma' ? __('pcma_workflow.type_pcma') : ($value ?? '—')) }}</pre></td></tr>
@endforeach
</table>
<p>{{ $generatedAt->format('Y-m-d H:i') }}</p>
</body></html>
