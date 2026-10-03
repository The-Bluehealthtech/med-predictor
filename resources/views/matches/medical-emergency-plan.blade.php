@extends('layouts.app')

@section('title','Plan d’urgence médical - FIT')

@section('content')
@php
$roles=$plan->role_assignments ?? [];
$connectRoles=$plan->connect_role_assignments ?? [];
$equipment=$plan->equipment_checklist ?? [];
$timeline=$plan->timeline_checklist ?? [];
$equipmentLabels=[
'evacuation_set_1'=>'Kit évacuation 1','evacuation_set_2'=>'Kit évacuation 2',
'aed_1'=>'DAE 1','aed_2'=>'DAE 2','oxygen_1'=>'Oxygène 1','oxygen_2'=>'Oxygène 2',
'splints'=>'Attelles','emergency_bag'=>'Mallette d’urgence'];
$timelineLabels=[
'h_minus_2_team_present'=>'H-2 · équipe médicale présente',
'h_minus_2_equipment_checked'=>'H-2 · équipement vérifié',
'h_minus_90_simulation'=>'H-1,5 · simulation réalisée',
'h_minus_60_infirmary_ready'=>'H-1 · infirmerie prête',
'halftime_team_present'=>'Mi-temps · équipe toujours présente',
'post_match_until_last_player'=>'Après-match · présence jusqu’au départ du dernier joueur'];
@endphp

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
<x-page-header
    title="Plan d’urgence médical"
    subtitle="{{ $match->homeTeam?->name ?? 'Équipe domicile' }} vs {{ $match->awayTeam?->name ?? 'Équipe extérieure' }}"
    eyebrow="Medical Matchday · FIFA Emergency Care Protocols"
    :back-href="route('competition-management.matches.matchday-preparation',$match)"
    back-label="Retour à Préparation Match Day"
/>

@if(session('success'))
<div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">{{ session('success') }}</div>
@endif
@if(session('info'))
<div class="rounded-xl border border-blue-200 bg-blue-50 px-4 py-3 text-sm text-blue-800">{{ session('info') }}</div>
@endif
@if(session('error'))
<div class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800">{{ session('error') }}</div>
@endif

<section class="grid gap-4 md:grid-cols-4">
<div class="rounded-2xl border border-slate-200 bg-white p-4"><p class="text-xs font-bold uppercase text-slate-500">Statut</p><p class="mt-1 text-lg font-semibold">{{ ucfirst($plan->status) }}</p></div>
<div class="rounded-2xl border border-slate-200 bg-white p-4"><p class="text-xs font-bold uppercase text-slate-500">Stade</p><p class="mt-1 font-semibold">{{ $plan->stadium ?: ($match->stadium ?: $match->venue ?: 'À renseigner') }}</p></div>
<div class="rounded-2xl border border-slate-200 bg-white p-4"><p class="text-xs font-bold uppercase text-slate-500">Protocole</p><p class="mt-1 font-semibold">{{ $plan->protocol_name }}</p><p class="text-xs text-slate-500">{{ $plan->protocol_version }}</p></div>
<div class="rounded-2xl border border-slate-200 bg-white p-4"><p class="text-xs font-bold uppercase text-slate-500">Validation</p><p class="mt-1 font-semibold">{{ $plan->validated_at?->format('d/m/Y H:i') ?? 'Non validé' }}</p></div>
</section>

<form method="POST" action="{{ route('matches.medical-emergency-plan.update',$match) }}" class="space-y-6">
@csrf
@method('PUT')

<section class="rounded-2xl border border-slate-200 bg-white p-5">
<h2 class="font-semibold text-slate-900">1. Contacts & évacuation</h2>
<p class="mt-1 text-sm text-slate-600">Lecture seule depuis la FDM, la compétition et le club recevant. Aucune saisie libre n’est autorisée ici.</p>

<div class="mt-4 grid gap-4 md:grid-cols-2">
<div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
<p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Stade / lieu du match</p>
<p class="mt-1 font-semibold text-slate-900">{{ $plan->stadium ?: 'Non configuré' }}</p>
<p class="mt-1 text-xs text-slate-500">Source : {{ $contactContext['stadium_source'] }}.</p>
<input type="hidden" name="stadium" value="{{ $plan->stadium }}">
</div>

<div class="rounded-xl border {{ filled($plan->nearest_hospital) ? 'border-slate-200 bg-slate-50' : 'border-amber-200 bg-amber-50' }} p-4">
<p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Hôpital / structure d’évacuation</p>
<p class="mt-1 font-semibold text-slate-900">{{ $plan->nearest_hospital ?: 'Non configuré' }}</p>
<p class="mt-1 text-sm text-slate-600">{{ $plan->nearest_hospital_phone ?: 'Téléphone non configuré' }}</p>
<p class="mt-1 text-xs text-slate-500">Source : {{ $contactContext['hospital_source'] }}.</p>
@if(blank($plan->nearest_hospital))
<p class="mt-2 text-xs text-amber-800">À renseigner dans @if(\Illuminate\Support\Facades\Route::has('clubs-view.edit'))<a class="font-semibold underline" href="{{ route('clubs-view.edit',$match->home_club_id) }}">le club recevant</a>@else le club recevant @endif @if($match->competition) ou @if(\Illuminate\Support\Facades\Route::has('competitions.edit'))<a class="font-semibold underline" href="{{ route('competitions.edit',$match->competition) }}">la compétition</a>@else la compétition @endif @endif.</p>
@endif
<input type="hidden" name="nearest_hospital" value="{{ $plan->nearest_hospital }}">
<input type="hidden" name="nearest_hospital_phone" value="{{ $plan->nearest_hospital_phone }}">
</div>

<div class="rounded-xl border {{ filled($plan->ambulance_contact) ? 'border-slate-200 bg-slate-50' : 'border-amber-200 bg-amber-50' }} p-4 md:col-span-2">
<p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Ambulance / régulation</p>
<p class="mt-1 font-semibold text-slate-900">{{ $plan->ambulance_contact ?: 'Non configuré' }}</p>
<p class="mt-1 text-xs text-slate-500">Source : {{ $contactContext['ambulance_source'] }}.</p>
@if(blank($plan->ambulance_contact))
<p class="mt-2 text-xs text-amber-800">À renseigner dans @if(\Illuminate\Support\Facades\Route::has('clubs-view.edit'))<a class="font-semibold underline" href="{{ route('clubs-view.edit',$match->home_club_id) }}">le club recevant</a>@else le club recevant @endif @if($match->competition) ou @if(\Illuminate\Support\Facades\Route::has('competitions.edit'))<a class="font-semibold underline" href="{{ route('competitions.edit',$match->competition) }}">la compétition</a>@else la compétition @endif @endif.</p>
@endif
<input type="hidden" name="ambulance_contact" value="{{ $plan->ambulance_contact }}">
</div>

<div class="md:col-span-2 rounded-xl border border-slate-200 p-4">
<label class="text-sm font-medium text-slate-700">Responsable médical du club recevant
<select name="team_leader_club_official_id" id="team-leader-person" class="mt-2 w-full rounded-lg border-slate-300" @disabled(!$canEdit || $eligibleLeaders->isEmpty())>
<option value="">— Responsable configuré du club / compétition —</option>
@foreach($eligibleLeaders as $leader)
<option value="{{ $leader['club_official_id'] }}" data-name="{{ $leader['name'] }}" data-phone="{{ $leader['phone'] ?? '' }}" data-fifa="{{ $leader['person_fifa_id'] ?? '' }}" @selected((string)old('team_leader_club_official_id',$plan->team_leader_club_official_id)===(string)$leader['club_official_id'])>{{ $leader['name'] }} · {{ $leader['role'] }} · {{ $leader['team'] }}{{ filled($leader['person_fifa_id'] ?? null) ? ' · FIFA '.$leader['person_fifa_id'] : ' · FIT #'.$leader['club_official_id'] }}{{ filled($leader['phone'] ?? null) ? ' · '.$leader['phone'] : '' }}</option>
@endforeach
</select>
</label>
@if($eligibleLeaders->isEmpty())
<p class="mt-2 text-xs text-amber-800">Aucun responsable actif dans le club recevant. @if(\Illuminate\Support\Facades\Route::has('club-officials.club'))<a class="font-semibold underline" href="{{ route('club-officials.club',$match->home_club_id) }}">Configurer Dirigeants & staff</a>@else Configurer Dirigeants & staff @endif.</p>
@endif
<div class="mt-3 grid gap-3 sm:grid-cols-2">
<div class="rounded-lg bg-slate-50 p-3"><p class="text-xs text-slate-500">Nom</p><p id="team-leader-name-display" class="font-semibold text-slate-900">{{ $plan->team_leader_name ?: 'Non configuré' }}</p></div>
<div class="rounded-lg bg-slate-50 p-3"><p class="text-xs text-slate-500">Téléphone</p><p id="team-leader-phone-display" class="font-semibold text-slate-900">{{ $plan->team_leader_phone ?: 'Non configuré' }}</p></div>
</div>
<p class="mt-2 text-xs text-slate-500">Source : {{ $contactContext['leader_source'] }}.</p>
<input type="hidden" name="team_leader_name" id="team-leader-name" value="{{ $plan->team_leader_name }}">
<input type="hidden" name="team_leader_phone" id="team-leader-phone" value="{{ $plan->team_leader_phone }}">
<input type="hidden" name="team_leader_user_id" value="">
<input type="hidden" name="team_leader_person_fifa_id" id="team-leader-fifa" value="{{ $plan->team_leader_person_fifa_id }}">
</div>
</div>
</section>

<section class="rounded-2xl border border-slate-200 bg-white p-5">
<div class="flex flex-wrap items-start justify-between gap-3">
<div>
<h2 class="font-semibold text-slate-900">2. Affectation des rôles FIFA</h2>
<p class="mt-1 text-sm text-slate-600">Les couleurs reprennent les responsabilités du plan d’urgence d’avant-match FIFA. Les responsables sont issus des fiches Dirigeants & staff des clubs.</p>
</div>
@if($connectMatch)
<span class="rounded-full bg-blue-50 px-3 py-1 text-xs font-semibold text-blue-700">Connect · Match FIFA {{ $connectMatch->match_fifa_id }}</span>
@else
<span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600">Aucun match Connect lié</span>
@endif
</div>
@if($connectPeople->isEmpty())
<div class="mt-4 rounded-xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-900">
<strong>Aucune personne identifiée disponible.</strong>
Ajoutez ou synchronisez les membres du staff dans les fiches <span class="font-semibold">Dirigeants & staff</span> des deux clubs du match. Le lien avec un match FIFA Connect n’est pas requis pour affecter ces rôles.
</div>
@else
<div class="mt-4 grid gap-3 md:grid-cols-2 xl:grid-cols-3">
@foreach($roleDefinitions as $key=>$label)
<div class="rounded-xl border border-slate-200 p-4">
<label class="block">
<span class="text-xs font-bold uppercase tracking-wide">{{ ucfirst($key) }} · {{ $label }}</span>
<select name="connect_role_assignments[{{ $key }}]" data-role-connect="{{ $key }}" class="mt-3 w-full rounded-lg border-slate-300 text-sm" @disabled(!$canEdit)>
<option value="">— Sélectionner une personne —</option>
@foreach($connectPeople as $person)
<option value="{{ $person['person_fifa_id'] }}" data-person-name="{{ $person['name'] }}" @selected((string)old('connect_role_assignments.'.$key,$connectRoles[$key]??'')===(string)$person['person_fifa_id'])>
{{ $person['name'] }} · {{ $person['role'] }}{{ $person['team'] ? ' · '.$person['team'] : '' }}{{ $person['person_fifa_id'] ? ' · FIFA '.$person['person_fifa_id'] : '' }}
</option>
@endforeach
</select>
<input type="hidden" name="role_assignments[{{ $key }}]" data-role-name="{{ $key }}" value="{{ old('role_assignments.'.$key,$roles[$key]??'') }}">
</label>
</div>
@endforeach
</div>
<p class="mt-3 text-xs text-slate-500">{{ $connectPeople->count() }} personne(s) identifiée(s) dans les staffs actifs des clubs du match. Une même personne peut être affectée à plusieurs responsabilités si l’organisation médicale le prévoit.</p>
@endif
</section>

<section class="rounded-2xl border border-slate-200 bg-white p-5">
<h2 class="font-semibold text-slate-900">3. Équipement critique</h2>
<div class="mt-4 grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
@foreach($equipmentLabels as $key=>$label)
<label class="flex items-center gap-3 rounded-xl border border-slate-200 p-3 text-sm">
<input type="hidden" name="equipment_checklist[{{ $key }}]" value="0">
<input type="checkbox" name="equipment_checklist[{{ $key }}]" value="1" class="rounded border-slate-300" @checked($equipment[$key]??false) @disabled(!$canEdit)>
<span>{{ $label }}</span>
</label>
@endforeach
</div>
</section>

<section class="rounded-2xl border border-slate-200 bg-white p-5">
<h2 class="font-semibold text-slate-900">4. Timeline médicale du match</h2>
<div class="mt-4 space-y-2">
@foreach($timelineLabels as $key=>$label)
<label class="flex items-center gap-3 rounded-xl border border-slate-200 p-3 text-sm">
<input type="hidden" name="timeline_checklist[{{ $key }}]" value="0">
<input type="checkbox" name="timeline_checklist[{{ $key }}]" value="1" class="rounded border-slate-300" @checked($timeline[$key]??false) @disabled(!$canEdit)>
<span>{{ $label }}</span>
</label>
@endforeach
</div>
</section>

<section class="rounded-2xl border border-slate-200 bg-white p-5">
<label class="text-sm font-medium">Notes opérationnelles<textarea name="notes" rows="4" class="mt-2 w-full rounded-lg border-slate-300" @disabled(!$canEdit)>{{ old('notes',$plan->notes) }}</textarea></label>
</section>

@if($canEdit)
<div class="flex justify-end"><button class="rounded-xl bg-slate-900 px-5 py-2.5 text-sm font-semibold text-white">Enregistrer le plan</button></div>
@endif
</form>

<section class="rounded-2xl border border-rose-200 bg-white p-5">
<div class="flex items-start justify-between gap-4">
<div><h2 class="font-semibold text-slate-900">5. Incidents médicaux terrain</h2>
<p class="mt-1 text-sm text-slate-600">Traçabilité clinique du match. Le protocole guide l’équipe médicale ; FIT n’autorise aucune décision autonome.</p></div>
<span class="rounded-full bg-rose-50 px-3 py-1 text-xs font-semibold text-rose-700">{{ $incidents->count() }} incident(s)</span>
</div>

<div class="mt-5 grid gap-3 md:grid-cols-2">
<button type="button" data-protocol-choice="cardiac_arrest" class="group rounded-2xl border-2 border-red-200 bg-red-50 p-4 text-left hover:border-red-400 hover:bg-red-100">
<div class="flex items-center justify-between gap-3">
<div><p class="text-xs font-bold uppercase tracking-wide text-red-700">Urgence vitale</p><h3 class="mt-1 text-lg font-bold text-red-950">SCA · Arrêt cardiaque</h3></div>
<span class="rounded-full bg-white px-3 py-1 text-xs font-bold text-red-700">ACTIVER</span>
</div>
<p class="mt-2 text-sm text-red-800">Ouvrir immédiatement le protocole FIFA SCA et documenter l’intervention.</p>
</button>
<button type="button" data-protocol-choice="cervical_spine" class="group rounded-2xl border-2 border-amber-200 bg-amber-50 p-4 text-left hover:border-amber-400 hover:bg-amber-100">
<div class="flex items-center justify-between gap-3">
<div><p class="text-xs font-bold uppercase tracking-wide text-amber-700">Traumatisme</p><h3 class="mt-1 text-lg font-bold text-amber-950">Traumatisme crânien / cervical</h3></div>
<span class="rounded-full bg-white px-3 py-1 text-xs font-bold text-amber-700">ACTIVER</span>
</div>
<p class="mt-2 text-sm text-amber-800">Ouvrir le protocole FIFA crânio-cervical et l’évaluation structurée.</p>
</button>
</div>

@if($canDocumentIncident)
<form id="medical-incident-form" method="POST" action="{{ route('matches.medical-incidents.store',$match) }}" class="mt-5 space-y-4">
@csrf
<div class="grid gap-4 md:grid-cols-3">
<label class="text-sm">Type
<select name="incident_type" id="incident_type" class="mt-1 w-full rounded-lg border-slate-300" required>
<option value="cardiac_arrest">Arrêt cardiaque suspecté</option>
<option value="cervical_spine">Rachis cervical</option>
<option value="fracture">Fracture</option>
<option value="concussion">Commotion</option>
<option value="other">Autre</option>
</select></label>
<label class="text-sm">Minute<input type="number" min="0" max="180" name="match_minute" class="mt-1 w-full rounded-lg border-slate-300"></label>
<label class="text-sm">Joueur de la FDM
@if($matchPlayers->isNotEmpty())
<select name="player_id" class="mt-1 w-full rounded-lg border-slate-300">
<option value="">— Incident sans joueur identifié —</option>
@foreach($matchPlayers as $player)
<option value="{{ $player['id'] }}">#{{ $player['jersey_number'] ?? '—' }} · {{ $player['name'] }} · {{ $player['club_name'] ?? 'Club non renseigné' }}</option>
@endforeach
</select>
@else
<select class="mt-1 w-full rounded-lg border-slate-300 bg-slate-100" disabled><option>FDM sans joueurs enregistrés</option></select>
<span class="mt-1 block text-xs text-amber-700">La FDM ne contient aucune composition. <a class="font-semibold underline" href="{{ route('competition-management.matches.match-sheet.edit',$match) }}">Compléter la FDM</a> avant d’identifier un joueur dans un incident.</span>
@endif
</label>
</div>
<div class="grid gap-4 md:grid-cols-2">
<label class="text-sm">Mécanisme
<select name="mechanism" class="mt-1 w-full rounded-lg border-slate-300">
<option value="">— Sélectionner —</option>
@foreach($mechanismOptions as $value=>$label)
<option value="{{ $value }}">{{ $label }}</option>
@endforeach
</select>
</label>
<label class="text-sm">Contact
<select name="contact" class="mt-1 w-full rounded-lg border-slate-300"><option value="">Non déterminé</option><option value="1">Avec contact</option><option value="0">Sans contact</option></select></label>
</div>
<div id="fifa-sca-protocol" class="hidden rounded-xl border border-red-300 bg-red-50 p-4">
<div class="flex flex-wrap items-center justify-between gap-2"><div><h3 class="font-semibold text-red-900">Protocole FIFA · Arrêt cardiaque / SCA</h3><p class="text-xs text-red-700">FIFA Emergency Care Protocols · v3 - March 2025 · aide-mémoire, décision clinique humaine.</p></div><span class="rounded-full bg-white px-3 py-1 text-xs font-semibold text-red-700">URGENCE TERRAIN</span></div>
<div class="mt-3 grid gap-2 md:grid-cols-2">
@foreach(['sca_responsiveness_breathing'=>'Conscience et respiration vérifiées','sca_cpr'=>'Compressions / RCP débutées','sca_aed'=>'DAE apporté / utilisé selon indication','sca_oxygen'=>'Oxygène disponible / administré selon indication','sca_ambulance'=>'Ambulance / régulation activée','sca_evacuation'=>'Évacuation organisée'] as $key=>$label)
<label class="flex items-center gap-2 rounded-lg border border-red-200 bg-white p-3 text-sm"><input type="checkbox" name="protocol_actions[{{ $key }}]" value="1" class="rounded border-slate-300">{{ $label }}</label>
@endforeach
</div></div>

<div id="fifa-head-protocol" class="hidden rounded-xl border border-amber-300 bg-amber-50 p-4">
<div><h3 class="font-semibold text-amber-900">Protocole FIFA · Traumatisme crânien / rachis cervical</h3><p class="text-xs text-amber-700">FIFA Emergency Care Protocols · v3 - March 2025 · immobilisation et évaluation structurée par l’équipe médicale.</p></div>
<div class="mt-3 grid gap-2 md:grid-cols-2">
@foreach(['head_cervical_control'=>'Contrôle / immobilisation cervicale','head_abcde'=>'Évaluation ABCDE réalisée','head_neuro'=>'Examen neurologique documenté','head_collar'=>'Minerve envisagée / utilisée selon indication','head_transfer'=>'Transfert scoop / planche et sangles selon indication','head_evacuation'=>'Évacuation organisée si requise'] as $key=>$label)
<label class="flex items-center gap-2 rounded-lg border border-amber-200 bg-white p-3 text-sm"><input type="checkbox" name="protocol_actions[{{ $key }}]" value="1" class="rounded border-slate-300">{{ $label }}</label>
@endforeach
</div></div>

@php($abcdeOptions=[
'a'=>['label'=>'A · Airway','options'=>['patent'=>'Voies aériennes libres','at_risk'=>'Voies aériennes à risque / menace','obstructed'=>'Obstruction suspectée / constatée','adjunct'=>'Dispositif de maintien des voies aériennes en place']],
'b'=>['label'=>'B · Breathing','options'=>['normal'=>'Respiration spontanée sans anomalie évidente','abnormal'=>'Respiration anormale / détresse suspectée','absent'=>'Respiration absente','assisted'=>'Ventilation / assistance respiratoire en cours']],
'c'=>['label'=>'C · Circulation','options'=>['stable'=>'Circulation sans anomalie évidente','compromised'=>'Circulation compromise / choc suspecté','major_bleeding'=>'Hémorragie majeure constatée','cpr'=>'RCP / compressions en cours']],
'd'=>['label'=>'D · Disability','options'=>['alert'=>'Alerte / répond normalement','voice'=>'Répond à la voix','pain'=>'Répond à la douleur','unresponsive'=>'Sans réponse','neuro_abnormal'=>'Anomalie neurologique constatée']],
'e'=>['label'=>'E · Exposure','options'=>['no_finding'=>'Pas de lésion évidente à l’exposition','injury_found'=>'Lésion / traumatisme visible','temperature_risk'=>'Risque thermique / environnemental','multiple_findings'=>'Lésions multiples constatées']],
])
<div class="grid gap-3 md:grid-cols-5">
@foreach($abcdeOptions as $key=>$section)
<label class="text-xs font-semibold">{{ $section['label'] }}
<select name="abcde_assessment[{{ $key }}]" class="mt-1 w-full rounded-lg border-slate-300 text-sm">
<option value="">— Non évalué —</option>
@foreach($section['options'] as $value=>$label)<option value="{{ $value }}">{{ $label }}</option>@endforeach
</select>
</label>
@endforeach
</div>
<p class="-mt-2 text-xs text-slate-500">ABCDE structuré : FIT enregistre les constatations du professionnel ; il ne produit pas de diagnostic autonome.</p>
<div class="grid gap-2 sm:grid-cols-4">
@foreach(['loss_of_consciousness'=>'Perte de connaissance','aed_used'=>'DAE utilisé','oxygen_used'=>'Oxygène administré','evacuated'=>'Évacuation'] as $key=>$label)
<label class="flex items-center gap-2 rounded-lg border border-slate-200 p-3 text-sm"><input type="checkbox" name="{{ $key }}" value="1" class="rounded border-slate-300">{{ $label }}</label>
@endforeach
</div>
<div class="grid gap-4 md:grid-cols-2">
<label class="text-sm">Destination évacuation
<select name="evacuation_destination" class="mt-1 w-full rounded-lg border-slate-300" @disabled($incidentEvacuationDestinations->isEmpty())>
<option value="">{{ $incidentEvacuationDestinations->isEmpty() ? 'Aucune structure configurée' : '— Sélectionner la structure configurée —' }}</option>
@foreach($incidentEvacuationDestinations as $destination)
<option value="{{ $destination['value'] }}">{{ $destination['label'] }} · {{ $destination['source'] }}</option>
@endforeach
</select>
@if($incidentEvacuationDestinations->isEmpty())<span class="mt-1 block text-xs text-amber-700">Configurez la structure d’évacuation dans le club recevant ou la compétition.</span>@endif
</label>
<label class="text-sm">Médecin / professionnel responsable
<select name="doctor_club_official_id" id="incident-doctor" required class="mt-1 w-full rounded-lg border-slate-300" @disabled($incidentMedicalProfessionals->isEmpty())>
<option value="">{{ $incidentMedicalProfessionals->isEmpty() ? 'Aucun professionnel médical identifié' : '— Sélectionner dans le staff médical —' }}</option>
@foreach($incidentMedicalProfessionals as $professional)
<option value="{{ $professional['id'] }}" data-name="{{ $professional['name'] }}">{{ $professional['name'] }} · {{ $professional['role'] }} · {{ $professional['club'] }}{{ $professional['fifa_id'] ? ' · FIFA '.$professional['fifa_id'] : ' · FIT #'.$professional['id'] }}</option>
@endforeach
</select>
<input type="hidden" name="doctor_name" id="incident-doctor-name" value="">
@if($incidentMedicalProfessionals->isEmpty())<span class="mt-1 block text-xs text-amber-700">Ajoutez le médecin/kinésithérapeute dans Dirigeants & staff du club.</span>@endif
</label>
<label class="text-sm md:col-span-2">Diagnostic initial
<select name="initial_diagnosis" class="mt-1 w-full rounded-lg border-slate-300">
<option value="">— Sélectionner après évaluation clinique —</option>
@foreach($initialDiagnosisOptions as $code=>$label)
<option value="{{ $code }}">{{ $label }}</option>
@endforeach
</select>
<span class="mt-1 block text-xs text-slate-500">Nomenclature structurée ; le diagnostic reste sous validation du professionnel de santé.</span>
</label>
</div>
<div class="flex justify-end"><button class="rounded-xl bg-rose-700 px-5 py-2.5 text-sm font-semibold text-white">Enregistrer l’incident</button></div>
</form>
@endif

@if($incidents->isNotEmpty())
<div class="mt-5 space-y-2">
<h3 class="text-sm font-semibold text-slate-900">Historique des incidents ({{ $incidents->count() }})</h3>
@foreach($incidents as $incident)
<div class="rounded-xl border border-slate-200 p-4 text-sm">
<div class="flex flex-wrap items-center justify-between gap-2"><strong>{{ str_replace('_',' ',ucfirst($incident->incident_type)) }}</strong><span class="text-slate-500">{{ $incident->match_minute !== null ? $incident->match_minute.'e min' : 'Minute non renseignée' }}</span></div>
@php($incidentIdentity=$matchPlayers->firstWhere('id',$incident->player_id))
<p class="mt-1 text-slate-700">
@if($incidentIdentity)#{{ $incidentIdentity['jersey_number'] ?? '—' }} · {{ $incidentIdentity['name'] }} · {{ $incidentIdentity['club_name'] ?? 'Club non renseigné' }}@else{{ $incident->player?->name ?? 'Personne non identifiée' }}@endif
@if($incident->doctor_name) · {{ $incident->doctor_name }}@endif
</p>
@if($incident->initial_diagnosis)<p class="mt-1 text-slate-600">{{ $incident->initial_diagnosis }}</p>@endif
</div>
@endforeach
</div>
@endif
</section>

@if($canValidate)
<section class="rounded-2xl border {{ $plan->status==='ready' ? 'border-emerald-200 bg-emerald-50' : 'border-amber-200 bg-amber-50' }} p-5">
<h2 class="font-semibold text-slate-900">Validation médicale fédération</h2>
<p class="mt-1 text-sm text-slate-700">La validation confirme que les contacts, rôles, équipements et étapes pré-match requis ont été renseignés.</p>
<form method="POST" action="{{ route('matches.medical-emergency-plan.validate',$match) }}" class="mt-3">@csrf
<button class="rounded-xl bg-emerald-700 px-4 py-2 text-sm font-semibold text-white" @disabled($plan->status!=='ready')>Valider le plan médical</button>
</form>
</section>
@endif

<section class="rounded-2xl border border-slate-200 bg-white p-5">
<div class="flex flex-wrap items-start justify-between gap-3">
<div>
<h2 class="font-semibold text-slate-900">Signature numérique du plan Medical Matchday</h2>
<p class="mt-1 text-sm text-slate-600">Utilise le moteur de signature documentaire FIT déjà commun aux PCMA, imagerie et autres documents certifiés.</p>
</div>
<span class="rounded-full px-3 py-1 text-xs font-semibold {{ $plan->status==='validated' ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-700' }}">
{{ $plan->status==='validated' ? 'Document éligible à la signature' : 'Validation médicale requise' }}
</span>
</div>
<div class="mt-4 rounded-xl border {{ $federationMedicalValidator ? 'border-emerald-200 bg-emerald-50' : 'border-amber-200 bg-amber-50' }} p-4">
<p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Signataire de validation fédérale</p>
@if($federationMedicalValidator)
<p class="mt-1 font-semibold text-slate-900">{{ $federationMedicalValidator->name }}</p>
<p class="text-sm text-slate-600">{{ $federationMedicalValidator->email }} · Responsable médical de {{ $match->competition?->association?->name }}{{ $federationMedicalValidator->fifa_connect_id ? ' · FIFA '.$federationMedicalValidator->fifa_connect_id : ' · FIT #'.$federationMedicalValidator->id }}</p>
@else
<p class="mt-1 font-semibold text-amber-900">Responsable médical fédéral non configuré</p>
<p class="mt-1 text-sm text-amber-800">La signature est bloquée. Configurez le responsable médical dans la fiche de l’association.</p>
@if($match->competition?->association && \Illuminate\Support\Facades\Route::has('associations-view.edit'))
<a class="mt-2 inline-block text-sm font-semibold text-amber-900 underline" href="{{ route('associations-view.edit',$match->competition->association->id) }}">Configurer l’association</a>
@endif
@endif
</div>
@php($readySignatureProviders=$signatureProviders->where('status','ready'))
@if($canRequestSignature)
<form method="POST" action="{{ route('matches.medical-emergency-plan.signatures.store',$match) }}" class="mt-4 flex flex-col gap-3 sm:flex-row sm:items-end">
@csrf
<label class="flex-1 text-sm font-medium text-slate-700">Fournisseur
<select name="provider" class="mt-1 w-full rounded-lg border-slate-300" @disabled($readySignatureProviders->isEmpty()) required>
@forelse($readySignatureProviders as $provider)
<option value="{{ $provider['slug'] }}">{{ $provider['name'] }}</option>
@empty
<option value="">Aucun fournisseur activé</option>
@endforelse
</select>
</label>
<button type="submit" @disabled($readySignatureProviders->isEmpty()) class="rounded-lg px-4 py-2.5 text-sm font-semibold {{ $readySignatureProviders->isEmpty() ? 'cursor-not-allowed bg-slate-200 text-slate-500' : 'bg-slate-900 text-white hover:bg-slate-800' }}">Demander la signature numérique</button>
</form>
@endif
@if($readySignatureProviders->isEmpty())
<p class="mt-3 text-sm text-amber-700">Aucun fournisseur de signature n’est actuellement activé dans Configuration des API.</p>
@endif

<div class="mt-5 border-t border-slate-100 pt-4">
<h3 class="text-sm font-semibold text-slate-900">Historique de signature</h3>
@forelse($signatureRequests as $signatureRequest)
<div class="mt-2 rounded-xl bg-slate-50 px-4 py-3 text-sm">
<div class="flex flex-wrap items-center justify-between gap-2">
<span>{{ $signatureRequest->metadata['provider_name'] ?? $signatureRequest->provider }} · {{ $signatureRequest->signer_name ?? 'Signataire' }}</span>
<span class="font-medium text-slate-700">{{ ucfirst($signatureRequest->status) }} · {{ optional($signatureRequest->requested_at)->format('d/m/Y H:i') }}</span>
</div>
<div class="mt-2 flex flex-wrap gap-2">
@if($signatureRequest->provider==='adobe_sign' && $signatureRequest->external_reference && in_array($signatureRequest->status,['sent','pending'],true) && $canRequestSignature)
<form method="POST" action="{{ route('matches.medical-emergency-plan.signatures.sync',[$match,$signatureRequest]) }}">@csrf
<button type="submit" class="rounded-lg border border-slate-300 bg-white px-3 py-1.5 text-xs font-semibold text-slate-700">Synchroniser</button>
</form>
@endif
@if(data_get($signatureRequest->metadata,'signed_path'))
<a href="{{ route('matches.medical-emergency-plan.signatures.download',[$match,$signatureRequest]) }}" class="rounded-lg bg-emerald-600 px-3 py-1.5 text-xs font-semibold text-white">Télécharger le PDF signé</a>
<span class="self-center text-xs text-slate-500">SHA-256 : {{ \Illuminate\Support\Str::limit((string)data_get($signatureRequest->metadata,'signed_sha256'),20,'…') }}</span>
@endif
</div>
</div>
@empty
<p class="mt-2 text-sm text-slate-500">Aucune demande de signature pour ce plan.</p>
@endforelse
</div>
</section>
</div>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const type = document.getElementById('incident_type');
    const sca = document.getElementById('fifa-sca-protocol');
    const head = document.getElementById('fifa-head-protocol');
    if (!type || !sca || !head) return;
    const refreshProtocol = () => {
        sca.classList.toggle('hidden', type.value !== 'cardiac_arrest');
        head.classList.toggle('hidden', !['cervical_spine','concussion'].includes(type.value));
    };
    document.querySelectorAll('[data-role-connect]').forEach((select) => {
        select.addEventListener('change', () => {
            const selected = select.options[select.selectedIndex];
            const input = document.querySelector(`[data-role-name="${select.dataset.roleConnect}"]`);
            if (input && selected?.dataset.personName) input.value = selected.dataset.personName;
        });
    });

    document.querySelectorAll('[data-protocol-choice]').forEach((button) => {
        button.addEventListener('click', () => {
            type.value = button.dataset.protocolChoice;
            refreshProtocol();
            document.getElementById('medical-incident-form')?.scrollIntoView({behavior: 'smooth', block: 'start'});
        });
    });
    type.addEventListener('change', refreshProtocol);
    refreshProtocol();
});
</script>
@endsection

<script>document.addEventListener('DOMContentLoaded',()=>{
 const leader=document.getElementById('team-leader-person');
 if(leader){ leader.addEventListener('change',()=>{ const option=leader.options[leader.selectedIndex]; const name=document.getElementById('team-leader-name'); const phone=document.getElementById('team-leader-phone'); const nameDisplay=document.getElementById('team-leader-name-display'); const phoneDisplay=document.getElementById('team-leader-phone-display'); const selectedName=option?.dataset?.name||''; const selectedPhone=option?.dataset?.phone||''; if(name) name.value=selectedName; if(phone) phone.value=selectedPhone; if(nameDisplay) nameDisplay.textContent=selectedName||'Non configuré'; if(phoneDisplay) phoneDisplay.textContent=selectedPhone||'Non configuré'; const fifa=document.getElementById('team-leader-fifa'); if(fifa) fifa.value=option?.dataset?.fifa||''; }); }
});
</script>
