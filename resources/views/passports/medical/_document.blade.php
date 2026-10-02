{{-- Contenu du passeport médical (IPS), commun à l'affichage et au PDF. Classes « ips-* » stylées par chaque support. --}}
@php
    $d = fn ($v) => $v ? \Illuminate\Support\Carbon::parse($v)->format('d/m/Y') : '—';
    $doc = $summary['document'];
    $p = $summary['patient'];
@endphp
<div class="ips-header">
    <div>
        <div class="ips-kicker">Passeport médical · International Patient Summary</div>
        <div class="ips-title">{{ $p['name'] }}</div>
        <div class="ips-sub">Né(e) le {{ $d($p['birth_date']) }} · {{ $p['nationality'] ?: 'nationalité non renseignée' }} · {{ $p['club'] ?: 'club non renseigné' }}@if($p['fifa_connect_id']) · FIFA Connect {{ $p['fifa_connect_id'] }}@endif</div>
    </div>
    <table class="ips-meta">
        <tr><td>Motif</td><td><strong>{{ $doc['purpose_label'] }}</strong></td></tr>
        <tr><td>Établi le</td><td>{{ $doc['generated_at']->format('d/m/Y H:i') }}</td></tr>
        <tr><td>Par</td><td>{{ $doc['author'] ?: '—' }}</td></tr>
        <tr><td>Groupe sanguin</td><td>{{ $p['blood_type'] ?: 'non renseigné' }}</td></tr>
    </table>
</div>

<div class="ips-notice">
    Résumé établi à partir des {{ $doc['records_count'] }} dossier(s) médical(aux) enregistré(s) dans FIT{{ $doc['last_record_date'] ? ' (dernier le ' . $d($doc['last_record_date']) . ')' : '' }}.
    Une section vide signifie qu'aucune information n'est enregistrée, et non l'absence de problème.
    <strong>Document non attesté</strong> : à signer par le médecin responsable avant tout usage officiel.
</div>

@foreach($sections as $key => $meta)
    @php $items = $summary['sections'][$key] ?? []; @endphp
    <div class="ips-section">
        <div class="ips-section-title">{{ $meta['title'] }} <span class="ips-code">{{ $meta['ips'] }} · LOINC {{ $meta['loinc'] }} · {{ $meta['level'] }}</span></div>
        @if(empty($items))
            <div class="ips-empty">Aucune information enregistrée.</div>
        @else
            <table class="ips-table">
                @foreach($items as $item)
                    <tr>
                        <td class="ips-label">{{ $item['label'] }}@if(!empty($item['code'])) <span class="ips-code">{{ $item['code'] }}</span>@endif</td>
                        <td>{{ $item['detail'] ?: '' }}@if(!empty($item['status']) && $item['status'] !== 'enregistré') <span class="ips-code">({{ $item['status'] }})</span>@endif</td>
                        <td class="ips-date">{{ $d($item['date'] ?? null) }}</td>
                    </tr>
                @endforeach
            </table>
        @endif
    </div>
@endforeach

<div class="ips-footer">
    Structure : {{ $doc['standard'] }} · {{ $doc['type_code'] }} · document {{ $doc['id'] }}.
    Données de santé confidentielles : à ne transmettre qu'au destinataire concerné, avec l'accord du joueur.
</div>
