<!DOCTYPE html>
<html lang="fr"><head><meta charset="utf-8"><title>{{ $title }}</title>@include('medical-documents._style')</head>
<body>
    @include('medical-documents._header')
    <p>Cher(e) Confrère, chère Consœur,</p>
    <p>Je vous adresse ce patient pour un avis en <strong>{{ $specialty }}</strong>@if($urgent) — <strong>demande urgente</strong>@endif.</p>
    <p class="label">Motif et question posée</p>
    <div class="box">{{ $reason }}</div>
    <p>Je vous remercie de bien vouloir me faire part de vos conclusions.</p>
    <p>Bien confraternellement,</p>
    <div class="sign">Dr {{ $doctor->name }}</div>
    <p class="foot">Document établi électroniquement par Dr {{ $doctor->name }}, identifié dans FIT, le {{ $issuedAt->format('d/m/Y à H:i') }}. Données confidentielles, couvertes par le secret médical.</p>
</body></html>
