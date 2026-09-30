@extends('layouts.app')
@section('title', __('medical_module.profile'))
@section('content')
<div class="container mx-auto px-4 py-8">
<a href="{{ route('modules.medical.index') }}">{{ __('medical_module.back') }}</a>
<div class="bg-white rounded-lg shadow-md p-6 my-6">
<h1 class="text-3xl font-bold">{{ $player->full_name }}</h1>
<p>{{ __('medical_module.club') }} : {{ $player->club?->name ?? '—' }}</p>
<p>{{ __('medical_module.position') }} : {{ $player->position ?? '—' }}</p>
<p>{{ __('medical_module.records') }} : {{ $records->count() }}</p>
</div>
<div class="flex flex-wrap gap-4 my-6">
<a href="{{ route('health-records.create',['player_id'=>$player->id]) }}">{{ __('medical_module.create') }}</a>
<a href="{{ route('pcma.create',['player_id'=>$player->id]) }}">PCMA</a>
<a href="{{ route('health-records.index',['player_id'=>$player->id]) }}">{{ __('medical_module.records') }}</a>
</div>
@forelse($records as $record)
<div class="bg-white rounded-lg shadow-md p-6 my-4">
<h2 class="font-bold">{{ $record->record_date?->format('d/m/Y') ?? '—' }}</h2>
<p>{{ $record->diagnosis ?? '—' }}</p>
<p class="whitespace-pre-line">{{ $record->notes ?? '—' }}</p>
<a href="{{ route('health-records.show',$record->id) }}">{{ __('medical_module.view') }}</a>
</div>
@empty
<p>{{ __('medical_module.empty') }}</p>
@endforelse
</div>
@endsection
