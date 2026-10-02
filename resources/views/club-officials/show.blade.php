@extends('layouts.app')

@section('title', 'Fiche FIFA Connect — ' . $official->fullName())

@section('content')
<style>.fc-table{width:100%;font-size:.875rem;border-collapse:collapse}.fc-table th{text-align:left;font-size:.7rem;text-transform:uppercase;letter-spacing:.06em;color:#475569;background:#f8fafc;padding:.5rem .6rem}.fc-table td{padding:.45rem .6rem;border-bottom:1px solid #f1f5f9;vertical-align:top}.fc-k{color:#64748b;font-family:ui-monospace,monospace;font-size:.75rem;width:18%}.fc-empty{color:#94a3b8;font-style:italic}</style>
<div class="max-w-5xl mx-auto px-4 py-8 space-y-4">
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div>
            <p class="text-xs font-semibold uppercase tracking-wider text-slate-600">Fiche FIFA Connect · {{ str_replace(' (Démo)', '', $club->name) }}</p>
            <h1 class="text-2xl font-bold text-gray-900">{{ $official->fullName() }}</h1>
            <p class="text-sm text-gray-600">{{ $official->roleLabel() }}@if($official->email) · {{ $official->email }}@endif @if($official->phone) · {{ $official->phone }}@endif</p>
        </div>
        <div class="flex flex-wrap items-center gap-3 text-sm">
            <a href="{{ route('club-officials.pdf', [$club, $official]) }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-slate-700 text-white font-semibold hover:bg-slate-800">@include('modules.partials.icon', ['name' => 'file', 'class' => 'w-4 h-4']) PDF</a>
            @if($canManage)<a href="{{ route('club-officials.edit', [$club, $official]) }}" class="px-4 py-2 rounded-lg border border-slate-300 text-slate-700 font-semibold hover:bg-slate-50">Modifier</a>@endif
            <a href="{{ route('club-officials.club', $club) }}" class="text-blue-600 hover:text-blue-800">← Dirigeants et staff</a>
        </div>
    </div>
    @if(session('status'))<div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">{{ session('status') }}</div>@endif
    <div class="bg-white rounded-lg shadow p-5">@include('club-officials.partials._sheet')</div>
    <p class="text-xs text-gray-500">Champs nommés comme dans FIFA Connect (Person, Registration, Certification). L'e-mail et le téléphone sont des données locales, non transmises à FIFA Connect.</p>
</div>
@endsection
