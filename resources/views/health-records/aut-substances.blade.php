@php($reference=app(\App\Services\AutSubstanceReference::class)->data())
<div class="border rounded p-4 my-4">
<p class="font-semibold">{{ $reference['title'] }}</p>
<p>{{ app()->getLocale()==='fr' ? 'Source fournie : version 2025, pas la liste 2026. La présence dans cette liste ne suffit pas à conclure qu’une AUT est nécessaire : consulter les conditions et exceptions ci-dessous.' : 'Supplied source: 2025 version, not the 2026 list. Inclusion alone does not determine whether a TUE is required: review the conditions and exceptions below.' }}</p>
<p>{{ app()->getLocale()==='fr' ? 'Suggestions : noms énumérés individuellement. Les autres substances, méthodes et exemples regroupés restent consultables dans le texte intégral. Saisie libre possible, sans correspondance automatique.' : 'Suggestions: individually enumerated names. Other substances, methods and grouped examples remain available in the full source text. Free-text entry is supported without automatic matching.' }}</p>
<datalist id="aut-substances">
@foreach($reference['entries'] as $entry)
<option value="{{ $entry['label'] }}">{{ $reference['version'] }} · {{ $reference['sheet'] }} · {{ $entry['row'] }}</option>
@endforeach
</datalist>
@foreach($reference['sections'] as $section)
<details class="my-2"><summary>{{ $section['title'] }}</summary>
@foreach($section['rows'] as $row)
<p class="my-1">{{ $row['text'] }}</p>
@endforeach
</details>
@endforeach
</div>
