<div class="space-y-6">
    <section class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-xl font-semibold text-gray-800">{{ __('medical_doping.heading') }}</h2>
        <p class="text-gray-600 my-4">{{ __('medical_doping.notice') }}</p>
        <a href="{{ route('medical-aut.create',$healthRecord->id) }}" class="inline-block bg-blue-600 text-white rounded-lg px-4 py-2">{{ __('medical_doping.create') }}</a>
    </section>
    <section class="bg-white rounded-lg shadow-md p-6">
        <h3 class="font-semibold mb-4">{{ __('medical_doping.tests') }}</h3>
        @php($testRecords=$dopingRecords->filter(fn($r)=>$r->doping_tests || $r->doping_test_status || $r->last_doping_test_date || $r->doping_test_lab || $r->next_doping_test_date))
        @forelse($testRecords as $record)
            <div class="border-b border-gray-200 py-4">
                <p>{{ __('medical_doping.record') }} #{{ $record->id }} · {{ $record->record_date?->format('d/m/Y') ?? '—' }}</p>
                <dl class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-3">
                    @foreach(['doping_test_status','last_doping_test_date','next_doping_test_date','doping_test_lab','loinc_doping_panel'] as $field)
                        <div><dt class="text-gray-600">{{ __('medical_doping.'.$field) }}</dt><dd>{{ $record->$field instanceof \DateTimeInterface ? $record->$field->format('d/m/Y') : ($record->$field ?? '—') }}</dd></div>
                    @endforeach
                </dl>
                @foreach($record->doping_tests ?? [] as $test)
                    <dl class="border rounded-lg p-4 mt-4">
                        @foreach(is_array($test) ? $test : ['details'=>$test] as $key=>$value)
                            <div><dt class="text-gray-600">{{ \Illuminate\Support\Facades\Lang::has('medical_doping.'.$key) ? __('medical_doping.'.$key) : $key }}</dt><dd>{{ is_array($value) ? json_encode($value,JSON_UNESCAPED_UNICODE|JSON_INVALID_UTF8_SUBSTITUTE) : ($value ?? '—') }}</dd></div>
                        @endforeach
                    </dl>
                @endforeach
            </div>
        @empty
            <p class="text-gray-600">{{ __('medical_doping.no_tests') }}</p>
        @endforelse
    </section>
    <section class="bg-white rounded-lg shadow-md p-6">
        <h3 class="font-semibold mb-4">{{ __('medical_doping.aut') }}</h3>
        <p class="text-gray-600 mb-4">{{ __('medical_doping.draft_notice') }}</p>
        @forelse($autRequests as $aut)
            <div class="border-b py-3">
                <p>#{{ $aut->id }} · {{ $aut->request_date?->format('d/m/Y') ?? '—' }} · {{ $aut->medication ?? '—' }}</p>
                <p>{{ __('medical_doping.status') }} : {{ \Illuminate\Support\Facades\Lang::has('medical_doping.status_'.$aut->status) ? __('medical_doping.status_'.$aut->status) : ($aut->status ?? '—') }}</p>
                <p>{{ __('medical_doping.approval') }} : {{ $aut->approved_date?->format('d/m/Y') ?? '—' }} · {{ __('medical_doping.expiry') }} : {{ $aut->expiry_date?->format('d/m/Y') ?? '—' }}</p>
                @if($aut->health_record_id)
                    <a class="text-blue-600 underline" href="{{ route('medical-aut.index',$aut->health_record_id) }}">{{ __('medical_doping.open') }}</a>
                @endif
            </div>
        @empty
            <p class="text-gray-600">{{ __('medical_doping.no_aut') }}</p>
        @endforelse
        @foreach($dopingRecords->filter(fn($r)=>$r->aut_records || $r->aut_status || $r->aut_substance || $r->aut_authorized_substance) as $record)
            <details class="mt-4"><summary>{{ __('medical_doping.legacy') }} #{{ $record->id }}</summary>
                @foreach(['aut_records','aut_status','aut_substance','aut_authorized_substance','aut_approval_date','aut_expiry_date'] as $field)
                    <p>{{ $field }} : {{ $record->$field instanceof \DateTimeInterface ? $record->$field->format('d/m/Y') : (is_array($record->$field) ? json_encode($record->$field,JSON_UNESCAPED_UNICODE|JSON_INVALID_UTF8_SUBSTITUTE) : ($record->$field ?? '—')) }}</p>
                @endforeach
            </details>
        @endforeach
    </section>
    <section class="bg-white rounded-lg shadow-md p-6">
        <h3 class="font-semibold mb-4">{{ __('medical_doping.biological') }}</h3>
        <p class="text-gray-600 mb-4">{{ __('medical_doping.biological_notice') }}</p>
        @forelse($dopingRecords->filter(fn($r)=>!empty($r->biological_profile)) as $record)
            <p>{{ __('medical_doping.record') }} #{{ $record->id }} · {{ $record->record_date?->format('d/m/Y') ?? '—' }}</p>
            @foreach($record->biological_profile as $key=>$value)
                <p>{{ $key }} : {{ is_array($value) ? json_encode($value,JSON_UNESCAPED_UNICODE|JSON_INVALID_UTF8_SUBSTITUTE) : ($value ?? '—') }}</p>
            @endforeach
        @empty
            <p class="text-gray-600">{{ __('medical_doping.no_biological') }}</p>
        @endforelse
    </section>
    <section class="bg-white rounded-lg shadow-md p-6">
        <h3 class="font-semibold mb-4">{{ __('medical_doping.medicines') }}</h3>
        <p class="text-gray-600 mb-4">{{ __('medical_doping.medicine_notice') }}</p>
        @forelse($dopingRecords->filter(fn($r)=>!empty($r->medications)) as $record)
            <div class="mb-4"><p>{{ __('medical_doping.record') }} #{{ $record->id }}</p>
                @foreach($record->medications as $medication)
                    <p>{{ is_array($medication) ? ($medication['name'] ?? $medication['label'] ?? '—') : $medication }}</p>
                    @if(is_array($medication))
                        @include('health-records.medication-antidoping')
                    @else
                        <p class="text-gray-600">{{ __('medical_doping.unchecked') }}</p>
                    @endif
                @endforeach
            </div>
        @empty
            <p class="text-gray-600">{{ __('medical_doping.no_medicines') }}</p>
        @endforelse
    </section>
</div>
