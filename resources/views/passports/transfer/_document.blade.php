{{-- Contenu du passeport de transfert (format passeport joueur FIFA), commun à l'affichage et au PDF. --}}
@php
    $d = fn ($v) => $v ? \Illuminate\Support\Carbon::parse($v)->format('d/m/Y') : '—';
    $doc = $passport['document'];
    $p = $passport['player'];
@endphp
<div class="tp-header">
    <div class="tp-kicker">Passeport joueur · format FIFA</div>
    <div class="tp-title">{{ $p['name'] }}</div>
    <div class="tp-sub">Né(e) le {{ $d($p['birth_date']) }} · {{ $p['nationality'] ?: 'nationalité non renseignée' }}@if($p['position']) · {{ $p['position'] }}@endif</div>
</div>

<table class="tp-grid">
    <tr><td>Club actuel</td><td>{{ $p['club'] ?: '—' }}</td><td>Fédération</td><td>{{ $p['association'] ?: '—' }}</td></tr>
    <tr><td>Identifiant FIFA Connect</td><td>{{ $p['fifa_connect_id'] ?: 'non attribué' }}</td><td>N° de passeport</td><td>{{ $doc['number'] ?: '—' }}</td></tr>
    <tr><td>N° d'enregistrement</td><td>{{ $doc['registration_number'] ?: '—' }}</td><td>Autorité de délivrance</td><td>{{ $doc['issuing_authority'] ?: '—' }}@if($doc['issuing_country']) ({{ $doc['issuing_country'] }})@endif</td></tr>
    <tr><td>Statut du passeport</td><td>{{ $doc['status'] ?: '—' }}</td><td>Statut ITC</td><td>{{ $doc['itc_status'] ?: '—' }}</td></tr>
</table>

<div class="tp-section-title">Clubs d'enregistrement</div>
<div class="tp-note">Le passeport joueur FIFA recense les clubs auprès desquels le joueur a été enregistré depuis la saison de ses 12 ans{{ $p['twelfth_birthday'] ? ' (le ' . $d($p['twelfth_birthday']) . ')' : '' }}.</div>
@if(empty($passport['registrations']))
    <div class="tp-empty">Aucun enregistrement dans FIT.</div>
@else
    <table class="tp-table">
        <tr><th>Période</th><th>Saison</th><th>Club</th><th>Fédération</th><th>Statut</th><th>N° de licence</th></tr>
        @foreach($passport['registrations'] as $r)
            <tr><td>{{ $d($r['from']) }} → {{ $r['to'] ? $d($r['to']) : 'en cours' }}</td><td>{{ $r['season'] ?: '—' }}</td><td>{{ str_replace(' (Démo)', '', $r['club']) }}</td><td>{{ $r['association'] ?: '—' }}</td><td>{{ $r['status'] ?: 'non précisé' }}</td><td>{{ $r['number'] ?: '—' }}</td></tr>
        @endforeach
    </table>
@endif

<div class="tp-section-title">Transferts</div>
@if(empty($passport['transfers']))
    <div class="tp-empty">Aucun transfert enregistré dans FIT.</div>
@else
    <table class="tp-table">
        <tr><th>Date</th><th>De</th><th>Vers</th><th>Type</th><th>Statut</th><th>ITC</th></tr>
        @foreach($passport['transfers'] as $t)
            <tr><td>{{ $d($t['date']) }}</td><td>{{ $t['from'] }}</td><td>{{ $t['to'] }}</td><td>{{ $t['type'] ?: '—' }}{{ $t['international'] ? ' · international' : '' }}</td><td>{{ $t['status'] ?: '—' }}</td><td>{{ $t['itc'] ?: '—' }}</td></tr>
        @endforeach
    </table>
@endif

<div class="tp-footer">Établi le {{ $doc['generated_at']->format('d/m/Y H:i') }} à partir des données enregistrées dans FIT ({{ $doc['standard'] }}). Ce document ne remplace pas le passeport officiel délivré par la fédération. Il ne contient aucune donnée médicale : celles-ci relèvent du passeport médical.</div>
