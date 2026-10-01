<section class="bg-white border border-gray-200 rounded-lg p-6 my-6" data-medical-capture>
    <h2 class="text-xl font-semibold mb-3">{{ __('medical_sections.capture_title') }}</h2>
    <p class="text-gray-600 mb-4">{{ __('medical_sections.capture_help') }}</p>
    @foreach(config('medical_sections.sections',[]) as $section=>$def)
    <details class="border-b py-3">
        <summary class="font-semibold">{{ __('medical_sections.'.$section) }}</summary>
        <label class="block mt-3"><input type="checkbox" name="capture[{{ $section }}]" value="1" @checked(old('capture.'.$section))> {{ __('medical_sections.capture') }}</label>
        <fieldset data-section-capture="{{ $section }}" class="space-y-3 mt-3" @disabled(!old('capture.'.$section))>
            <label class="block">{{ __('medical_sections.exam_date') }}<input class="border rounded px-3 py-2 block" type="date" name="section_dates[{{ $section }}]" value="{{ old('section_dates.'.$section) }}" required></label>
            <label class="block">{{ __('medical_sections.source') }}<input class="border rounded px-3 py-2 w-full" name="section_source[{{ $section }}]" value="{{ old('section_source.'.$section) }}" maxlength="1000" @required(in_array($section,['biological','laboratory'],true))></label>
            @foreach($def['fields'] as $field=>$type)
                @if($type==='json') @continue @endif
                @php($savedValue=old('section_values.'.$section.'.'.$field,$sectionValues[$field] ?? null))
                @php($savedValue=is_array($savedValue)?implode("\n",array_map(fn($v)=>is_scalar($v)?(string)$v:json_encode($v,JSON_UNESCAPED_UNICODE),$savedValue)):$savedValue)
                <label class="block">{{ app(\App\Services\HealthRecordSections::class)->label($field) }}
                @if($section==='fmarc' && $field==='injury_location')
                    @include('health-records.partials.injury-body-map', [
                        'inputId' => 'fmarc_injury_location',
                        'inputName' => 'section_values[fmarc][injury_location]',
                        'value' => $savedValue,
                    ])
                @elseif($type==='boolean')
                    <select class="border rounded px-3 py-2 block" name="section_values[{{ $section }}][{{ $field }}]">
                        <option value="">—</option><option value="1" @selected((string)$savedValue==='1')>{{ __('medical_sections.yes') }}</option><option value="0" @selected((string)$savedValue==='0')>{{ __('medical_sections.no') }}</option>
                    </select>
                @elseif(in_array($type,['list','text'],true))
                    <textarea class="border rounded px-3 py-2 w-full" name="section_values[{{ $section }}][{{ $field }}]" rows="2">{{ $savedValue }}</textarea>
                @else
                    <input class="border rounded px-3 py-2 w-full" type="{{ in_array($type,['date','time'],true)?$type:'number' }}" step="{{ $type==='number'?'any':($type==='time'?'60':'1') }}" name="section_values[{{ $section }}][{{ $field }}]" value="{{ $savedValue }}">
                @endif
                </label>
            @endforeach
            @if(in_array($section,['biological','laboratory'],true))
                <p>{{ __('medical_sections.lab_help') }}</p>
                @for($i=0;$i<3;$i++)
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 border rounded p-3">
                    @foreach(['analyte','value','unit','reference','method','laboratory','report_id','sample_date'] as $field)
                    <label>{{ __('medical_sections.'.($field==='laboratory'?'laboratory_name':$field)) }}<input class="block border rounded px-2 py-1 w-full" type="{{ $field==='sample_date'?'date':'text' }}" name="lab_rows[{{ $section }}][{{ $i }}][{{ $field }}]" value="{{ old('lab_rows.'.$section.'.'.$i.'.'.$field) }}"></label>
                    @endforeach
                </div>
                @endfor
            @endif
            <label class="block">{{ __('medical_sections.files') }}<input type="file" name="medical_files[{{ $section }}][]" multiple accept=".pdf,.jpg,.jpeg,.png,.dcm"></label>
        </fieldset>
    </details>
    @endforeach
</section>
<script type="application/json" id="medical-section-values">@json($sectionValues ?? [])</script>
<script type="application/json" id="medical-section-fields">@json(config('medical_sections.sections',[]))</script>
<script src="{{ asset('js/medical-sections.js') }}" defer></script>
