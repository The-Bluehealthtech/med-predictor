@php
    $control = $controls[$field] ?? [];
    $controlType = $control['type'] ?? null;
    $options = $control['options'] ?? [];
    $saved = old('section_values.'.$module.'.'.$field, $sectionValues[$field] ?? null);
    $savedArray = is_array($saved) ? $saved : (is_string($saved) && $saved !== '' ? preg_split('/\r?\n|\s*,\s*/', $saved) : []);
    $label = app(\App\Services\HealthRecordSections::class)->label($field);
    $wide = $controlType === 'multiselect' || in_array($type, ['list'], true);
@endphp

@if($type !== 'json')
<div class="{{ $wide ? 'md:col-span-2' : '' }}">
    <label class="block text-sm font-semibold text-slate-700 mb-2">{{ $label }}</label>

    @if($module === 'fmarc' && $field === 'injury_location')
        @include('health-records.partials.injury-body-map', [
            'inputId' => 'module_injury_location',
            'inputName' => 'section_values[fmarc][injury_location]',
            'value' => $saved,
        ])
    @elseif($controlType === 'select')
        <select name="section_values[{{ $module }}][{{ $field }}]" class="w-full rounded-xl border-slate-300 bg-white focus:border-blue-500 focus:ring-blue-500">
            <option value="">Sélectionner…</option>
            @foreach($options as $option)
                <option value="{{ $option }}" @selected((string)$saved === (string)$option)>{{ $option }}</option>
            @endforeach
            @if($saved && !in_array((string)$saved, array_map('strval', $options), true))
                <option value="{{ $saved }}" selected>{{ $saved }} (historique)</option>
            @endif
        </select>
    @elseif($controlType === 'multiselect')
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
            @foreach($options as $option)
                <label class="flex items-start gap-2.5 rounded-xl border border-slate-200 bg-white px-3 py-2.5 hover:border-blue-300 cursor-pointer">
                    <input type="checkbox" name="section_values[{{ $module }}][{{ $field }}][]" value="{{ $option }}"
                           class="mt-0.5 rounded border-slate-300 text-blue-600 focus:ring-blue-500"
                           @checked(in_array((string)$option, array_map('strval', $savedArray), true))>
                    <span class="text-sm text-slate-700">{{ $option }}</span>
                </label>
            @endforeach
        </div>
    @elseif($type === 'boolean')
        <select name="section_values[{{ $module }}][{{ $field }}]" class="w-full rounded-xl border-slate-300 bg-white">
            <option value="">Non évalué</option>
            <option value="1" @selected((string)$saved === '1')>Oui</option>
            <option value="0" @selected((string)$saved === '0')>Non</option>
        </select>
    @elseif($type === 'symptom')
        <select name="section_values[{{ $module }}][{{ $field }}]" class="w-full rounded-xl border-slate-300 bg-white">
            <option value="">Non évalué</option>
            @for($i=0;$i<=6;$i++)<option value="{{ $i }}" @selected((string)$saved === (string)$i)>{{ $i }}</option>@endfor
        </select>
        <p class="text-xs text-slate-400 mt-1">Échelle SCAT 0–6.</p>
    @elseif($type === 'balance')
        <select name="section_values[{{ $module }}][{{ $field }}]" class="w-full rounded-xl border-slate-300 bg-white">
            <option value="">Non évalué</option>
            @for($i=0;$i<=10;$i++)<option value="{{ $i }}" @selected((string)$saved === (string)$i)>{{ $i }}</option>@endfor
        </select>
    @elseif($type === 'date')
        <input type="date" name="section_values[{{ $module }}][{{ $field }}]" value="{{ $saved }}" class="w-full rounded-xl border-slate-300">
    @elseif($type === 'time')
        <input type="time" name="section_values[{{ $module }}][{{ $field }}]" value="{{ $saved }}" class="w-full rounded-xl border-slate-300">
    @elseif(in_array($type, ['number','nonnegative_integer'], true))
        <input type="number" step="{{ $type === 'nonnegative_integer' ? '1' : 'any' }}" min="{{ $type === 'nonnegative_integer' ? '0' : '' }}"
               name="section_values[{{ $module }}][{{ $field }}]" value="{{ $saved }}" class="w-full rounded-xl border-slate-300">
    @elseif($type === 'list')
        <textarea name="section_values[{{ $module }}][{{ $field }}]" rows="2" class="w-full rounded-xl border-slate-300" placeholder="Une valeur par ligne">{{ is_array($saved) ? implode("\n", $saved) : $saved }}</textarea>
    @else
        <input type="text" name="section_values[{{ $module }}][{{ $field }}]" value="{{ is_scalar($saved) ? $saved : '' }}"
               class="w-full rounded-xl border-slate-300" placeholder="Renseigner si nécessaire">
    @endif
</div>
@endif
