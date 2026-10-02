@extends('layouts.app')

@section('title', 'Passeport médical — ' . $summary['patient']['name'])

@section('content')
<style>
    .ips-doc { background:#fff; border-radius:.75rem; box-shadow:0 1px 3px rgba(0,0,0,.08); padding:1.5rem; }
    .ips-header { display:flex; flex-wrap:wrap; justify-content:space-between; gap:1rem; border-bottom:2px solid #b91c1c; padding-bottom:1rem; }
    .ips-kicker { font-size:.7rem; font-weight:600; letter-spacing:.08em; text-transform:uppercase; color:#b91c1c; }
    .ips-title { font-size:1.5rem; font-weight:700; color:#111827; }
    .ips-sub { font-size:.875rem; color:#4b5563; }
    .ips-meta td { font-size:.8rem; padding:.1rem .5rem; color:#374151; } .ips-meta td:first-child { color:#6b7280; }
    .ips-notice { margin:1rem 0; padding:.75rem 1rem; border-radius:.5rem; background:#fef3c7; color:#78350f; font-size:.8rem; }
    .ips-section { margin-top:1.25rem; }
    .ips-section-title { font-weight:600; color:#111827; border-bottom:1px solid #e5e7eb; padding-bottom:.25rem; }
    .ips-code { font-size:.7rem; font-weight:400; color:#9ca3af; }
    .ips-table { width:100%; margin-top:.5rem; font-size:.85rem; } .ips-table td { padding:.35rem .25rem; border-bottom:1px solid #f3f4f6; vertical-align:top; }
    .ips-label { font-weight:500; color:#111827; width:38%; } .ips-date { color:#6b7280; text-align:right; white-space:nowrap; width:6rem; }
    .ips-empty { font-size:.8rem; color:#9ca3af; font-style:italic; margin-top:.4rem; }
    .ips-footer { margin-top:1.5rem; font-size:.7rem; color:#9ca3af; }
</style>
<div class="max-w-5xl mx-auto px-4 py-8 space-y-4">
    <div class="flex flex-wrap items-end justify-between gap-3">
        <form method="GET" class="flex flex-wrap items-end gap-2">
            <label class="block text-sm font-medium text-gray-700">Motif du partage
                <select name="purpose" class="mt-1 block rounded-lg border-gray-300 shadow-sm text-sm" onchange="this.form.submit()">
                    @foreach($purposes as $key => $label)<option value="{{ $key }}" @selected($summary['document']['purpose'] === $key)>{{ $label }}</option>@endforeach
                </select>
            </label>
        </form>
        <div class="flex items-center gap-3 text-sm">
            <a href="{{ route('passports.medical.pdf', ['player' => $summary['patient']['id'], 'purpose' => $summary['document']['purpose']]) }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-red-700 text-white font-semibold hover:bg-red-800">@include('modules.partials.icon', ['name' => 'file', 'class' => 'w-4 h-4']) Télécharger le PDF</a>
            @unless(auth()->user()->isPlayer())<a href="{{ route('passports.medical.index') }}" class="text-blue-600 hover:text-blue-800">← Passeports médicaux</a>@endunless
        </div>
    </div>
    <div class="ips-doc">@include('passports.medical._document')</div>
</div>
@endsection
