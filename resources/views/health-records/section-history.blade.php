@foreach($sectionHistory as $section=>$entries)
    @if(isset($onlySection) && $onlySection!==$section) @continue @endif
    @if(isset($onlySections) && !in_array($section,$onlySections,true)) @continue @endif
    <section class="bg-white rounded-lg shadow-md p-6 mb-6" id="medical-section-{{ $section }}">
        <h3 class="text-xl font-semibold text-gray-800 mb-4">{{ __('medical_sections.'.$section) }}</h3>
        @forelse($entries as $item)
            <article class="border-b border-gray-200 py-4">
                <p class="font-semibold">{{ __('medical_sections.exam_date') }} : {{ $item['date'] ?? __('medical_sections.unknown_date') }}</p>
                <p class="text-sm text-gray-500">{{ __('medical_sections.record') }} #{{ $item['record_id'] }} · {{ __('medical_sections.record_date') }} : {{ $item['record_date'] instanceof \DateTimeInterface ? $item['record_date']->format('d/m/Y') : ($item['record_date'] ?? '—') }}</p>
                @if(empty($item['entry']['_fit_entry']))<p class="text-sm text-gray-500">{{ __('medical_sections.legacy_notice') }}</p>@endif
                @if(!empty($item['entry']['source']))<p>{{ __('medical_sections.source') }} : {{ $item['entry']['source'] }}</p>@endif
                @if(!empty($item['entry']['recorded_at']))<p class="text-sm text-gray-500">{{ __('medical_sections.saved_at') }} : {{ $item['entry']['recorded_at'] }}</p>@endif
                @if($section==='dental' && is_array($item['entry']['values']['dental_data'] ?? null) && !empty($item['entry']['values']['dental_data']))
                    @include('health-records.odontogram',['odontogramReadonly'=>true,'odontogramData'=>$item['entry']['values']['dental_data']])
                @endif
                @include('health-records.section-value',['value'=>$item['entry']['values'] ?? ($item['entry']['legacy'] ?? $item['entry'])])
                @foreach(($sectionDocuments ?? collect())->where('entry_id',$item['entry']['id'] ?? '') as $file)
                    <p class="mt-3"><a class="text-blue-600 underline" href="{{ route('player-medical.document',[$file->health_record_id,$file->id]) }}">{{ $file->original_name }}</a></p>
                @endforeach
            </article>
        @empty
            <p class="text-gray-600">{{ __('medical_sections.empty') }}</p>
        @endforelse
    </section>
@endforeach
