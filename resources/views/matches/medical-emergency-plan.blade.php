@extends('layouts.app')

@section('title','Plan d’urgence médical - FIT')

@section('content')
@php
$roles=$plan->role_assignments ?? [];
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
    :back-href="route('competition-management.matches.match-sheet',$match)"
    back-label="Retour à la feuille de match"
/>

@if(session('success'))
<div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">{{ session('success') }}</div>
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
<div class="mt-4 grid gap-4 md:grid-cols-2">
<label class="text-sm">Stade<input name="stadium" value="{{ old('stadium',$plan->stadium) }}" class="mt-1 w-full rounded-lg border-slate-300" @disabled(!$canEdit)></label>
<label class="text-sm">Hôpital le plus proche<input name="nearest_hospital" value="{{ old('nearest_hospital',$plan->nearest_hospital) }}" class="mt-1 w-full rounded-lg border-slate-300" @disabled(!$canEdit)></label>
<label class="text-sm">Téléphone hôpital<input name="nearest_hospital_phone" value="{{ old('nearest_hospital_phone',$plan->nearest_hospital_phone) }}" class="mt-1 w-full rounded-lg border-slate-300" @disabled(!$canEdit)></label>
<label class="text-sm">Ambulance / régulation<input name="ambulance_contact" value="{{ old('ambulance_contact',$plan->ambulance_contact) }}" class="mt-1 w-full rounded-lg border-slate-300" @disabled(!$canEdit)></label>
<label class="text-sm">Responsable d’équipe
<select name="team_leader_user_id" class="mt-1 w-full rounded-lg border-slate-300" @disabled(!$canEdit)>
<option value="">Saisie libre</option>
@foreach($eligibleLeaders as $leader)
<option value="{{ $leader->id }}" @selected((string)$plan->team_leader_user_id===(string)$leader->id)>{{ $leader->name }} · {{ $leader->role }}</option>
@endforeach
</select>
</label>
<label class="text-sm">Nom affiché du responsable<input name="team_leader_name" value="{{ old('team_leader_name',$plan->team_leader_name) }}" class="mt-1 w-full rounded-lg border-slate-300" @disabled(!$canEdit)></label>
<label class="text-sm">Téléphone responsable<input name="team_leader_phone" value="{{ old('team_leader_phone',$plan->team_leader_phone) }}" class="mt-1 w-full rounded-lg border-slate-300" @disabled(!$canEdit)></label>
</div>
</section>

<section class="rounded-2xl border border-slate-200 bg-white p-5">
<h2 class="font-semibold text-slate-900">2. Affectation des rôles FIFA</h2>
<p class="mt-1 text-sm text-slate-600">Les couleurs reprennent les responsabilités du plan d’urgence d’avant-match FIFA.</p>
<div class="mt-4 grid gap-3 md:grid-cols-2 xl:grid-cols-3">
@foreach($roleDefinitions as $key=>$label)
<label class="rounded-xl border border-slate-200 p-4">
<span class="text-xs font-bold uppercase tracking-wide">{{ ucfirst($key) }} · {{ $label }}</span>
<input name="role_assignments[{{ $key }}]" value="{{ old('role_assignments.'.$key,$roles[$key]??'') }}" class="mt-3 w-full rounded-lg border-slate-300" placeholder="Nom du professionnel" @disabled(!$canEdit)>
</label>
@endforeach
</div>
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

@if($canDocumentIncident)
<form method="POST" action="{{ route('matches.medical-incidents.store',$match) }}" class="mt-5 space-y-4">
@csrf
<div class="grid gap-4 md:grid-cols-3">
<label class="text-sm">Type
<select name="incident_type" class="mt-1 w-full rounded-lg border-slate-300" required>
<option value="cardiac_arrest">Arrêt cardiaque suspecté</option>
<option value="cervical_spine">Rachis cervical</option>
<option value="fracture">Fracture</option>
<option value="concussion">Commotion</option>
<option value="other">Autre</option>
</select></label>
<label class="text-sm">Minute<input type="number" min="0" max="180" name="match_minute" class="mt-1 w-full rounded-lg border-slate-300"></label>
<label class="text-sm">Joueur
<select name="player_id" class="mt-1 w-full rounded-lg border-slate-300"><option value="">Non identifié / non-joueur</option>
@foreach($matchPlayers as $player)<option value="{{ $player->id }}">{{ $player->name ?: trim($player->first_name.' '.$player->last_name) }}</option>@endforeach
</select></label>
</div>
<div class="grid gap-4 md:grid-cols-2">
<label class="text-sm">Mécanisme<input name="mechanism" class="mt-1 w-full rounded-lg border-slate-300" placeholder="Effondrement, choc, torsion…"></label>
<label class="text-sm">Contact
<select name="contact" class="mt-1 w-full rounded-lg border-slate-300"><option value="">Non déterminé</option><option value="1">Avec contact</option><option value="0">Sans contact</option></select></label>
</div>
<div class="grid gap-3 md:grid-cols-5">
@foreach(['a'=>'A · Airway','b'=>'B · Breathing','c'=>'C · Circulation','d'=>'D · Disability','e'=>'E · Exposure'] as $key=>$label)
<label class="text-xs font-semibold">{{ $label }}<textarea name="abcde_assessment[{{ $key }}]" rows="2" class="mt-1 w-full rounded-lg border-slate-300 text-sm"></textarea></label>
@endforeach
</div>
<div class="grid gap-2 sm:grid-cols-4">
@foreach(['loss_of_consciousness'=>'Perte de connaissance','aed_used'=>'DAE utilisé','oxygen_used'=>'Oxygène administré','evacuated'=>'Évacuation'] as $key=>$label)
<label class="flex items-center gap-2 rounded-lg border border-slate-200 p-3 text-sm"><input type="checkbox" name="{{ $key }}" value="1" class="rounded border-slate-300">{{ $label }}</label>
@endforeach
</div>
<div class="grid gap-4 md:grid-cols-2">
<label class="text-sm">Destination évacuation<input name="evacuation_destination" class="mt-1 w-full rounded-lg border-slate-300"></label>
<label class="text-sm">Médecin / professionnel responsable<input name="doctor_name" required class="mt-1 w-full rounded-lg border-slate-300"></label>
<label class="text-sm md:col-span-2">Diagnostic initial<textarea name="initial_diagnosis" rows="2" class="mt-1 w-full rounded-lg border-slate-300"></textarea></label>
</div>
<div class="flex justify-end"><button class="rounded-xl bg-rose-700 px-5 py-2.5 text-sm font-semibold text-white">Enregistrer l’incident</button></div>
</form>
@endif

@if($incidents->isNotEmpty())
<div class="mt-5 space-y-2">
@foreach($incidents as $incident)
<div class="rounded-xl border border-slate-200 p-4 text-sm">
<div class="flex flex-wrap items-center justify-between gap-2"><strong>{{ str_replace('_',' ',ucfirst($incident->incident_type)) }}</strong><span class="text-slate-500">{{ $incident->match_minute !== null ? $incident->match_minute.'e min' : 'Minute non renseignée' }}</span></div>
<p class="mt-1 text-slate-700">{{ $incident->player?->name ?? 'Personne non identifiée' }} · {{ $incident->doctor_name }}</p>
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
</div>
@endsection
