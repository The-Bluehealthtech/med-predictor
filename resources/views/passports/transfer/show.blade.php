@extends('layouts.app')

@section('title', 'Passeport de transfert — ' . $passport['player']['name'])

@section('content')
<style>
    .tp-doc { background:#fff; border-radius:.75rem; box-shadow:0 1px 3px rgba(0,0,0,.08); padding:1.5rem; }
    .tp-header { border-bottom:2px solid #334155; padding-bottom:1rem; }
    .tp-kicker { font-size:.7rem; font-weight:600; letter-spacing:.08em; text-transform:uppercase; color:#334155; }
    .tp-title { font-size:1.5rem; font-weight:700; color:#111827; } .tp-sub { font-size:.875rem; color:#4b5563; }
    .tp-grid { width:100%; margin-top:1rem; font-size:.85rem; } .tp-grid td { padding:.35rem .5rem; border-bottom:1px solid #f3f4f6; } .tp-grid td:nth-child(odd) { color:#6b7280; width:18%; }
    .tp-section-title { margin-top:1.5rem; font-weight:600; color:#111827; border-bottom:1px solid #e5e7eb; padding-bottom:.25rem; }
    .tp-note { font-size:.75rem; color:#6b7280; margin-top:.25rem; } .tp-empty { font-size:.8rem; color:#9ca3af; font-style:italic; margin-top:.4rem; }
    .tp-table { width:100%; margin-top:.5rem; font-size:.85rem; } .tp-table th { text-align:left; font-size:.7rem; text-transform:uppercase; color:#6b7280; padding:.35rem .25rem; border-bottom:1px solid #e5e7eb; } .tp-table td { padding:.35rem .25rem; border-bottom:1px solid #f3f4f6; }
    .tp-footer { margin-top:1.5rem; font-size:.7rem; color:#9ca3af; }
</style>
<div class="max-w-5xl mx-auto px-4 py-8 space-y-4">
    <div class="flex flex-wrap items-center justify-end gap-3 text-sm">
        <a href="{{ route('passports.transfer.pdf', $passport['player']['id']) }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-slate-700 text-white font-semibold hover:bg-slate-800">@include('modules.partials.icon', ['name' => 'file', 'class' => 'w-4 h-4']) Télécharger le PDF</a>
        @unless(auth()->user()->isPlayer())<a href="{{ route('passports.transfer.index') }}" class="text-blue-600 hover:text-blue-800">← Passeports de transfert</a>@endunless
    </div>
    <div class="tp-doc">@include('passports.transfer._document')</div>
    @include('passports._digital-signature', [
        'passportPlayerId' => $passport['player']['id'],
        'canRequestDigitalSignature' => auth()->user()->isClubUser() || auth()->user()->isAssociationUser(),
        'canManageDigitalSignature' => auth()->user()->isClubUser() || auth()->user()->isAssociationUser(),
        'digitalSignatureAction' => route('passports.transfer.digital-signature', $passport['player']['id']),
        'digitalSignatureBlockedMessage' => 'La signature numérique du passeport de transfert est réservée aux acteurs club ou fédération autorisés.',
    ])
</div>
@endsection
