@extends('layouts.app')
@section('content')
<link rel="stylesheet" href="{{ asset('css/medical-imaging.css') }}">
<div class="fi-shell">
    <a class="fi-back" href="{{ route('health-records.show',['healthRecord'=>$healthRecord,'workspace'=>'exams']) }}">← Dossier médical</a>
    <header class="fi-header"><div><div class="fi-eyebrow">Prise en charge médicale</div><h1>Imagerie & vérification U‑17</h1><p>{{ $healthRecord->player->full_name ?? $healthRecord->player->name }} · Dossier #{{ $healthRecord->id }}</p></div><span class="fi-badge">Historique longitudinal</span></header>
    @include('health-records.imaging.messages')
    <p class="fi-hint" style="margin-bottom:18px">Les saisies antérieures restent accessibles : <a href="{{ route('health-records.show',['healthRecord'=>$healthRecord,'legacy'=>1,'tab'=>'imaging']) }}">ouvrir le détail médical historique</a>.</p>
    <div class="fi-index-grid">
        <main>
            <section class="fi-card"><div class="fi-card-head"><h2>Examens du joueur</h2><p>Ouvrez un examen pour lire ses images et rédiger son compte rendu.</p></div>
            @forelse($studies as $study)
                @php $latest=$study->reports->first(); @endphp
                <a class="fi-exam" href="{{ route('medical-imaging.show',[$healthRecord,$study]) }}"><span class="fi-exam-icon">{{ $study->purpose==='age_u17'?'U‑17':$study->modality }}</span><div><strong>{{ $study->purpose==='age_u17'?'IRM du poignet · Vérification U‑17':$study->body_region }}</strong><small>{{ $study->exam_date->format('d/m/Y') }} · {{ $study->source }} · {{ $study->instances_count }} image(s)</small></div><span class="fi-badge {{ $latest?->status==='validated'?'fi-green':'' }}">{{ $latest?->status==='validated'?'Rapport validé':($latest?'Brouillon':'À compléter') }}</span><span aria-hidden="true">→</span></a>
            @empty
                <div class="fi-empty"><h3>Aucun examen dans ce lecteur</h3><p>Créez un examen, ajoutez ses images puis rédigez un compte rendu. Les anciennes entrées médicales restent dans leur historique.</p></div>
            @endforelse
            <div class="fi-padding">{{ $studies->links() }}</div></section>
            <section class="fi-card fi-age-summary"><div class="fi-card-head"><h2>Vigilance sur l’âge déclaré</h2><p>Couverture : {{ $ageVigilance['coverage'] }}. Les alertes demandent une revue humaine.</p></div><div class="fi-padding">
                @foreach($ageVigilance['flags'] as $flag)<div class="fi-notice {{ $flag['severity']==='review'?'fi-amber':'' }}"><strong>{{ $flag['severity']==='review'?'À vérifier':'Données insuffisantes' }}</strong><p>{{ $flag['label'] }}</p><small>{{ $flag['source'] }}</small></div>@endforeach
                @foreach($ageVigilance['confirmed'] as $item)<p>{{ $item }}</p>@endforeach
                <p class="fi-hint">La maturité osseuse ne constitue pas une preuve d’âge réel ou de fraude. Aucun score de probabilité n’est calculé.</p>
            </div></section>
        </main>
        <aside><form class="fi-card" method="POST" action="{{ route('medical-imaging.create',$healthRecord) }}">@csrf
            <div class="fi-card-head"><h2>Nouvel examen</h2><p>Un examen regroupe les images d’une même étude et d’une même personne.</p></div>
            <div class="fi-form">
                <label>Objectif<select name="purpose" id="fi-purpose" required><option value="general" @selected(old('purpose',request('purpose'))!=='age_u17')>Imagerie médicale</option><option value="age_u17" @selected(old('purpose',request('purpose'))==='age_u17')>Vérification U‑17 · IRM du poignet</option></select></label>
                <label>Date de l’examen<input type="date" name="exam_date" required max="{{ now()->format('Y-m-d') }}" value="{{ old('exam_date',now()->format('Y-m-d')) }}"></label>
                <label>Type d’imagerie<select name="modality" id="fi-modality" required>@foreach(config('medical_imaging.modalities') as $value=>$label)<option value="{{ $value }}" @selected(old('modality',request('modality','MR'))===$value)>{{ $label }}</option>@endforeach</select></label>
                <label>Région examinée<input name="body_region" id="fi-body-region" required maxlength="120" value="{{ old('body_region') }}" placeholder="Ex. genou droit"></label>
                <label>Établissement / source<input name="source" required maxlength="180" value="{{ old('source') }}" placeholder="Centre d’imagerie"></label>
                <label>Indication<textarea name="indication" rows="3" maxlength="6000" placeholder="Motif de l’examen">{{ old('indication') }}</textarea></label>
                <button class="fi-button fi-primary">Créer l’examen →</button>
                <p class="fi-hint">Étape suivante : importer les images. Aucun rapport n’est envoyé au PACS à cette étape.</p>
            </div>
        </form></aside>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded',()=>{const purpose=document.getElementById('fi-purpose'),modality=document.getElementById('fi-modality'),region=document.getElementById('fi-body-region');const update=()=>{const age=purpose.value==='age_u17';if(age){modality.value='MR';region.value='Poignet / radius distal';}modality.classList.toggle('fi-disabled',age);region.readOnly=age;};purpose.addEventListener('change',update);update();});
</script>
@endsection
