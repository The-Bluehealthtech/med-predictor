<!doctype html>
<html lang="fr">
<head>
<meta charset="utf-8">
<title>Medical Matchday {{ $match->id }}</title>
<style>
body{font-family:DejaVu Sans,sans-serif;font-size:11px;color:#1f2937}
h1{font-size:20px;margin:0 0 4px} h2{font-size:13px;margin:18px 0 6px}
.meta{color:#6b7280}.box{border:1px solid #d1d5db;border-radius:6px;padding:10px;margin-top:8px}
table{width:100%;border-collapse:collapse;margin-top:6px} th,td{border:1px solid #d1d5db;padding:6px;text-align:left;vertical-align:top}
th{background:#f3f4f6}.ok{font-weight:bold}
</style>
</head>
<body>
<h1>Plan d’urgence médical - Medical Matchday</h1>
<div class="meta">{{ $match->homeTeam?->name ?? 'Équipe domicile' }} vs {{ $match->awayTeam?->name ?? 'Équipe extérieure' }}</div>
<div class="meta">Match FIT #{{ $match->id }}@if($plan->connect_match_fifa_id) · Match FIFA {{ $plan->connect_match_fifa_id }}@endif · {{ $plan->protocol_name }} {{ $plan->protocol_version }}</div>

<h2>Contacts & évacuation</h2>
<table>
<tr><th>Stade</th><td>{{ $plan->stadium }}</td><th>Ambulance / régulation</th><td>{{ $plan->ambulance_contact }}</td></tr>
<tr><th>Hôpital</th><td>{{ $plan->nearest_hospital }}</td><th>Téléphone hôpital</th><td>{{ $plan->nearest_hospital_phone }}</td></tr>
<tr><th>Responsable</th><td>{{ $plan->team_leader_name }}</td><th>Téléphone</th><td>{{ $plan->team_leader_phone }}</td></tr>
</table>

<h2>Rôles FIFA</h2>
@php($connectRoles=$plan->connect_role_assignments ?? [])
<table>
<tr><th>Rôle</th><th>Professionnel</th><th>Identité FIFA Connect</th></tr>
@foreach(['black'=>'Noir - Responsable d’équipe','red'=>'Rouge - Évaluation / compressions','orange'=>'Orange - Voies aériennes / rachis cervical','green'=>'Vert - DAE / mallette urgence','blue'=>'Bleu - Oxygène / relais compressions','white'=>'Blanc - Évacuation / attelles'] as $key=>$label)
<tr><td>{{ $label }}</td><td>{{ data_get($plan->role_assignments,$key) }}</td><td>{{ $connectRoles[$key] ?? 'Non liée' }}</td></tr>
@endforeach
</table>

<h2>Équipement critique</h2>
<div class="box">
@foreach(['evacuation_set_1'=>'Kit évacuation 1','evacuation_set_2'=>'Kit évacuation 2','aed_1'=>'DAE 1','aed_2'=>'DAE 2','oxygen_1'=>'Oxygène 1','oxygen_2'=>'Oxygène 2','splints'=>'Attelles','emergency_bag'=>'Mallette d’urgence'] as $key=>$label)
<span>{{ data_get($plan->equipment_checklist,$key) ? '✓' : '☐' }} {{ $label }}</span>@if(!$loop->last) · @endif
@endforeach
</div>

<h2>Timeline médicale</h2>
<table>
@foreach(['h_minus_2_team_present'=>'H-2 · équipe médicale présente','h_minus_2_equipment_checked'=>'H-2 · équipement vérifié','h_minus_90_simulation'=>'H-1,5 · simulation réalisée','h_minus_60_infirmary_ready'=>'H-1 · infirmerie prête','halftime_team_present'=>'Mi-temps · équipe toujours présente','post_match_until_last_player'=>'Après-match · présence jusqu’au départ du dernier joueur'] as $key=>$label)
<tr><td>{{ $label }}</td><td class="ok">{{ data_get($plan->timeline_checklist,$key) ? 'Oui' : 'Non' }}</td></tr>
@endforeach
</table>

@if($plan->notes)
<h2>Notes opérationnelles</h2><div class="box">{{ $plan->notes }}</div>
@endif

<h2>Validation</h2>
<div class="box">
Statut : {{ strtoupper($plan->status) }}<br>
Préparé le : {{ optional($plan->prepared_at)->format('d/m/Y H:i') ?? '—' }}<br>
Validé le : {{ optional($plan->validated_at)->format('d/m/Y H:i') ?? '—' }}<br>
Document figé pour signature numérique FIT.
</div>
</body>
</html>
