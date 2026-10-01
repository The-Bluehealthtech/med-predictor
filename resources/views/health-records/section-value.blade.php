@if(is_array($value))
    <dl class="space-y-2 ml-2">
    @foreach($value as $key=>$child)
        @if(!in_array((string)$key,['_fit_entry','id','recorded_by','version'],true))
        <div class="border-l border-gray-200 pl-3">
            @if(!is_int($key))<dt class="font-medium text-gray-700">{{ app(\App\Services\HealthRecordSections::class)->label((string)$key) }}</dt>@endif
            <dd>@include('health-records.section-value',['value'=>$child])</dd>
        </div>
        @endif
    @endforeach
    </dl>
@else
    <span class="text-gray-700 break-words">{{ is_bool($value)?__('medical_sections.'.($value?'yes':'no')):($value ?? '—') }}</span>
@endif
