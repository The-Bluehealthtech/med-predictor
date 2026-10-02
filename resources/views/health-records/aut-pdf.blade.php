{{-- Formulaire FIFA de demande d'AUT (politique 2024, annexe 2) rempli à partir de la saisie FIT.
     Les signatures ne sont jamais apposées ni déduites : les cadres restent vides pour signature manuscrite. --}}
@php
    $locale = app()->getLocale() === 'fr' ? 'fr' : 'en';
    $label = fn (array $field) => $field[$locale === 'fr' ? 1 : 2];
    $val = fn (string $key) => trim((string) ($fields[$key] ?? ''));
    $date = function (string $key) use ($fields) {
        $v = $fields[$key] ?? null;
        try { return $v ? \Carbon\Carbon::parse($v)->format('d/m/Y') : ''; } catch (\Throwable) { return (string) $v; }
    };
    $option = fn (array $field, string $key) => $locale === 'fr' ? $field[4][$key] : __('medical_aut.option_' . $key);
    $byKey = collect($sections)->keyBy('key');
    $fieldOf = fn (string $section, string $key) => collect($byKey[$section]['fields'])->firstWhere(0, $key);
    $playerName = trim($val('given_names') . ' ' . $val('surname'));
    // Texte officiel extrait du PDF FIFA : on retire les retours à la ligne de mise en page, on garde les paragraphes.
    $reflow = function (string $text) {
        $text = str_replace(["\u{F0B7}", "\u{F0A7}", "\u{2022}"], '•', $text);
        $paragraphs = preg_split('/\n\s*\n/u', trim($text));
        return implode("\n\n", array_map(fn ($p) => trim(preg_replace('/\s+/u', ' ', $p)), $paragraphs));
    };
    $source = array_map(fn ($t) => is_string($t) ? $reflow($t) : $t, $source);
@endphp
<!doctype html>
<html lang="{{ $locale }}">
<head>
<meta charset="utf-8">
<title>{{ __('medical_aut.pdf_title') }}</title>
<style>
    @page { margin: 22mm 16mm 20mm 16mm; }
    body { font-family: DejaVu Sans, sans-serif; font-size: 9.5px; color: #111827; line-height: 1.35; }
    header { position: fixed; top: -15mm; left: 0; right: 0; font-size: 8px; color: #4b5563; border-bottom: 1px solid #d1d5db; padding-bottom: 2mm; }
    footer { position: fixed; bottom: -13mm; left: 0; right: 0; font-size: 7.5px; color: #6b7280; border-top: 1px solid #e5e7eb; padding-top: 1.5mm; }
    footer .page:after { content: counter(page); }
    h1 { font-size: 15px; margin: 0 0 2mm; color: #0b3b7a; }
    h2 { font-size: 11px; margin: 5mm 0 1.5mm; padding: 1.5mm 2mm; background: #0b3b7a; color: #fff; }
    table { width: 100%; border-collapse: collapse; table-layout: fixed; }
    td, th { border: 1px solid #9ca3af; padding: 1.6mm 2mm; vertical-align: top; }
    th { background: #f3f4f6; text-align: left; font-weight: bold; font-size: 8.5px; }
    td.l { width: 38%; background: #f9fafb; font-weight: bold; font-size: 8.5px; }
    .v { white-space: pre-wrap; }
    .box { border: 1px solid #9ca3af; padding: 2mm; margin: 2mm 0; }
    .note { font-size: 8px; color: #374151; white-space: pre-wrap; }
    .instr { border: 1.5px solid #0b3b7a; padding: 2mm; font-size: 8.5px; white-space: pre-wrap; }
    .sign { page-break-inside: avoid; }
    .sign td { height: 16mm; }
    .opt { margin-right: 4mm; white-space: nowrap; }
    .mark { position: fixed; top: 110mm; left: 0; right: 0; text-align: center; font-size: 40px; color: #dc2626; opacity: .15; transform: rotate(-30deg); }
    .pb { page-break-before: always; }
    .muted { color: #6b7280; }
</style>
</head>
<body>
<header>
    {{ config('medical_aut.source') }}@if($item) · {{ __('medical_aut.pdf_request') }} #{{ $item->id }}@endif
    @if($playerName !== '') · {{ $playerName }}@endif
</header>
<footer>
    {{ __('medical_aut.pdf_generated') }} {{ $generatedAt->format('d/m/Y H:i') }} · {{ config('medical_aut.form_version') }}
    · SHA-256 source {{ substr(config('medical_aut.source_sha256'), 0, 16) }}… · {{ __('medical_aut.pdf_page') }} <span class="page"></span>
</footer>
@if($preview)<div class="mark">{{ __('medical_aut.pdf_preview_mark') }}</div>@endif

<h1>{{ __('medical_aut.pdf_title') }}</h1>
<div class="instr">{{ $source['instructions'] }}</div>
<div class="box note"><b>ADAMS</b> — {{ __('medical_aut.adams_note') }} {{ config('medical_aut.adams_url') }}</div>

@foreach($sections as $section)
    <h2>{{ $section[$locale] }}</h2>

    @if($section['key'] === 'medical')
        <div class="note">{{ $source['medical_note'] }}</div>
    @endif

    @if($section['key'] === 'treatment')
        <table>
            <colgroup><col style="width:5%"><col style="width:31%"><col style="width:14%"><col style="width:16%"><col style="width:20%"><col style="width:14%"></colgroup>
            <tr><th>#</th><th>{{ $locale === 'fr' ? 'Substance interdite (nom générique)' : 'Prohibited substance (generic name)' }}</th><th>{{ $locale === 'fr' ? 'Dose' : 'Dose' }}</th><th>{{ $locale === 'fr' ? 'Voie' : 'Route' }}</th><th>{{ $locale === 'fr' ? 'Fréquence' : 'Frequency' }}</th><th>{{ $locale === 'fr' ? 'Durée' : 'Duration' }}</th></tr>
            @foreach([1, 2, 3] as $n)
                <tr><td>{{ $n }}</td><td class="v">{{ $val('substance_' . $n) }}</td><td class="v">{{ $val('dose_' . $n) }}</td><td class="v">{{ $val('route_' . $n) }}</td><td class="v">{{ $val('frequency_' . $n) }}</td><td class="v">{{ $val('duration_' . $n) }}</td></tr>
            @endforeach
        </table>
        @if($val('treatment_continuation') !== '')
            <table style="margin-top:2mm"><tr><td class="l">{{ $label($fieldOf('treatment', 'treatment_continuation')) }}</td><td class="v">{{ $val('treatment_continuation') }}</td></tr></table>
        @endif
    @else
        <table>
            <colgroup><col style="width:38%"><col style="width:62%"></colgroup>
            @foreach($section['fields'] as $field)
                @php $type = $field[3] ?? 'text'; @endphp
                <tr>
                    <td class="l">{{ $label($field) }}</td>
                    <td class="v">@if($type === 'select')@foreach($field[4] as $key => $unused)<span class="opt">{{ ($fields[$field[0]] ?? null) === $key ? '☒' : '☐' }} {{ $option($field, $key) }}</span> @endforeach
@elseif($type === 'date'){{ $date($field[0]) }}@else{{ $val($field[0]) }}@endif</td>
                </tr>
            @endforeach
        </table>
    @endif

    @if($section['key'] === 'medical' && !empty($healthRecord->icd11_diagnoses))
        <table style="margin-top:2mm">
            <tr><th colspan="2">{{ __('medical_aut.pdf_icd') }}</th></tr>
            @foreach($healthRecord->icd11_diagnoses as $code)
                <tr><td class="l">{{ $code['code'] ?? '—' }}</td><td>{{ $code['label'] ?? '—' }} <span class="muted">({{ $code['release'] ?? '—' }})</span></td></tr>
            @endforeach
        </table>
    @endif

    @if($section['key'] === 'physician')
        <div class="note box">{{ $source['physician'] }}</div>
        <table class="sign"><tr><td class="l">{{ __('medical_aut.pdf_signature') }} — {{ $val('physician_name') }}</td><td></td></tr></table>
    @endif

    @if($section['key'] === 'declaration')
        <div class="note box">{{ preg_replace('/Je soussigné\(e\), ,/u', 'Je soussigné(e), ' . ($val('player_declaration_name') ?: '______________________') . ',', $source['player'], 1) }}</div>
        <table class="sign">
            <tr><td class="l">{{ __('medical_aut.pdf_signature') }} — {{ $val('player_declaration_name') ?: $playerName }}</td><td></td></tr>
            <tr><td class="l">{{ __('medical_aut.pdf_signature') }} — {{ $label($fieldOf('declaration', 'guardian_name')) }}</td><td></td></tr>
        </table>
    @endif
@endforeach

@if($item && !empty($item->supporting_documents))
    <h2>{{ __('medical_aut.pdf_attachments') }}</h2>
    <table>
        @foreach($item->supporting_documents as $i => $file)
            @if(is_array($file))<tr><td class="l">{{ $i + 1 }}</td><td>{{ $file['name'] ?? __('medical_aut.document') }}</td></tr>@endif
        @endforeach
    </table>
@endif

<div class="pb"></div>
<h2>{{ __('medical_aut.pdf_annex') }}</h2>
<div class="note">{{ $source['privacy'] }}</div>
</body>
</html>
