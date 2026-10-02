@extends('layouts.app')

@section('title', 'Impression des licences - FIT Platform')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <x-page-header
        title="Impression en batch"
        :subtitle="$licenses->count() . ' carte(s) sélectionnée(s) · CR80 recto / verso'"
        eyebrow="Licences · fédération"
        :back-href="route('licenses.validation', ['tab' => 'active'])"
        back-label="Retour aux licences approuvées"
    />

    <div class="license-print-toolbar no-print">
        <p class="text-sm text-slate-600">Vérifiez les photos avant impression. Chaque licence génère un recto puis un verso.</p>
        <button type="button" onclick="window.print()" class="rounded-xl bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white hover:bg-slate-800">Imprimer {{ $licenses->count() }} carte(s)</button>
    </div>

    <div class="license-print-area space-y-6">
        @foreach($licenses as $license)
            <section class="batch-license">
                @include('licenses.cards._card', ['license' => $license])
            </section>
        @endforeach
    </div>
</div>

@include('licenses.cards._styles')
<style>
    .batch-license{margin-bottom:1.5rem}
    @media print{.batch-license{margin:0}.batch-license .cr80{page-break-after:always!important}}
</style>
@endsection
