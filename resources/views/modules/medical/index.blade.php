@extends('layouts.app')
@section('title', __('medical_module.title'))
@section('content')
<div class="container mx-auto px-4 py-8">
<h1 class="text-3xl font-bold">{{ __('medical_module.title') }}</h1>
<p class="text-gray-600 my-4">{{ __('medical_module.scope') }}</p>
<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
@foreach($stats as $key=>$value)
<div class="bg-white rounded-lg shadow-md p-6"><h2>{{ __('medical_module.'.$key) }}</h2><p class="text-3xl font-bold">{{ $value }}</p></div>
@endforeach
</div>
<div class="bg-white rounded-lg shadow-md p-6 my-6 flex flex-wrap gap-4">
<a href="{{ route('health-records.create') }}">{{ __('medical_module.create') }}</a>
<a href="{{ route('pcma.create') }}">PCMA</a>
<a href="{{ route('health-records.index') }}">{{ __('medical_module.records') }}</a>
<a href="{{ route('medical-predictions.index') }}">{{ __('medical_module.history') }}</a>
</div>
<form method="get" class="my-4 flex gap-4">
<label for="medical-search">{{ __('medical_module.search') }}</label>
<input id="medical-search" name="q" value="{{ $search }}" class="border rounded-lg p-2" maxlength="200">
<button class="bg-blue-600 text-white rounded-lg px-4">{{ __('medical_module.search') }}</button>
</form>
<div class="bg-white rounded-lg shadow-md p-6 overflow-x-auto">
<table class="w-full"><thead><tr><th>{{ __('medical_module.player') }}</th><th>{{ __('medical_module.club') }}</th><th>{{ __('medical_module.actions') }}</th></tr></thead><tbody>
@forelse($players as $player)
<tr class="border-b"><td class="py-4">{{ $player->full_name }}</td><td>{{ $player->club?->name ?? '—' }}</td>
<td><a href="{{ route('modules.medical.athlete',$player->id) }}">{{ __('medical_module.view') }}</a> · <a href="{{ route('modules.medical.athlete.edit',$player->id) }}">{{ __('medical_module.edit') }}</a></td></tr>
@empty
<tr><td colspan="3">{{ __('medical_module.empty') }}</td></tr>
@endforelse
</tbody></table>{{ $players->links() }}
</div>
<div class="bg-white rounded-lg shadow-md p-6 my-6">
<h2 class="text-xl font-bold">{{ __('medical_module.recent') }}</h2>
@forelse($recentRecords as $record)
<p class="py-2"><a href="{{ route('health-records.show',$record->id) }}">{{ $record->player?->full_name }} — {{ $record->record_date?->format('d/m/Y') ?? '—' }}</a></p>
@empty
<p>{{ __('medical_module.empty') }}</p>
@endforelse
</div>
</div>
@endsection
