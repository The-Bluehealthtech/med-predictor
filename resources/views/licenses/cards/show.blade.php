@extends('layouts.app')

@section('title', 'Carte de licence - FIT Platform')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <x-page-header
        title="Carte de licence"
        :subtitle="'Prévisualisation CR80 recto / verso · ' . ($license->license_number ?: 'FIT-' . $license->id)"
        eyebrow="Licences · fédération"
        :back-href="route('licenses.validation', ['tab' => 'active'])"
        back-label="Retour aux licences approuvées"
    />

    <div class="license-print-toolbar no-print">
        <div>
            <p class="font-semibold text-slate-900">{{ trim(($license->player?->first_name ?? '') . ' ' . ($license->player?->last_name ?? '')) }}</p>
            <p class="text-sm text-slate-500">{{ $license->club?->name }} · {{ $license->season }}</p>
        </div>
        <button type="button" onclick="window.print()" class="rounded-xl bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white hover:bg-slate-800">Imprimer recto / verso</button>
    </div>

    <div class="license-print-area">
        @include('licenses.cards._card', ['license' => $license])
    </div>
    <div class="mt-5 rounded-2xl border border-slate-200 bg-white p-4 text-sm text-slate-600 no-print">
        <strong class="text-slate-900">Format :</strong> CR80 85,6 × 53,98 mm. L’impression produit une page recto puis une page verso ; activez le mode recto-verso de l’imprimante si disponible.
        @unless($license->photo || $license->player?->player_picture_url)
            <p class="mt-2 font-semibold text-amber-700">Photo joueur manquante : la carte reste prévisualisable mais ne devrait pas être imprimée comme carte définitive.</p>
        @endunless
    </div>
</div>

@include('licenses.cards._styles')
@endsection
