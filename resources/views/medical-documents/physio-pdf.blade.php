<!DOCTYPE html>
<html lang="fr"><head><meta charset="utf-8"><title>{{ $title }}</title>@include('medical-documents._style')</head>
<body>
    @include('medical-documents._header')
    <p class="label">Séances de masso-kinésithérapie</p>
    <p>
        @if($sessions){{ $sessions }} séance(s)@else Nombre de séances laissé à l’appréciation du kinésithérapeute @endif
        @if($frequency) — rythme : {{ $frequency }}@endif
    </p>
    <p class="label">Indication</p>
    <div class="box">{{ $indication }}</div>
    @if($instructions)
        <p class="label">Techniques et consignes</p>
        <div class="box">{{ $instructions }}</div>
    @endif
    <div class="sign">Dr {{ $doctor->name }}</div>
    <p class="foot">Prescription établie électroniquement par Dr {{ $doctor->name }}, identifié dans FIT, le {{ $issuedAt->format('d/m/Y à H:i') }}. Données confidentielles, couvertes par le secret médical.</p>
</body></html>
