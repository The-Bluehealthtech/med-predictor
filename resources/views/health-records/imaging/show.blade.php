@extends('layouts.app')
@section('content')
<link rel="stylesheet" href="{{ asset('css/medical-imaging.css') }}">
@php
    $validated=$report?->status==='validated' && !$revision;
    $editable=!$validated;
    $age=old('age',$report?->age_review??[]);
    $identity=$study->source_identity??[];
    $images=$study->instances->map(fn($i)=>['id'=>$i->id,'name'=>$i->original_name,'series'=>$i->metadata['series_description']??'Série','metadata'=>$i->metadata,'url'=>route('medical-imaging.frame',[$healthRecord,$study,$i]),'download'=>route('medical-imaging.image',[$healthRecord,$study,$i])])->values();
@endphp
<div class="fi-shell fi-wide" id="fi-workspace">
    <a class="fi-back" href="{{ route('medical-imaging.index',$healthRecord) }}">← Tous les examens du joueur</a>
    <header class="fi-header"><div><div class="fi-eyebrow">{{ $study->purpose==='age_u17'?'Vérification U‑17 · Maturité osseuse':'Lecture et compte rendu' }}</div><h1>{{ $study->purpose==='age_u17'?'IRM du poignet':$study->body_region }}</h1><p>{{ $study->player->full_name??$study->player->name }} · {{ $study->exam_date->format('d/m/Y') }} · {{ $study->source }}</p></div><span class="fi-badge {{ $validated?'fi-green':'' }}">{{ $revision?'Nouvelle version':($validated?'Rapport validé':($report?'Brouillon':'À compléter')) }}</span></header>
    @include('health-records.imaging.messages')
    <ol class="fi-steps" aria-label="Progression de l’examen"><li class="{{ $images->isNotEmpty()?'fi-done':'' }}">1 · Images</li><li class="{{ $study->identity_checked?'fi-done':'' }}">2 · Identité</li><li class="{{ $report?'fi-done':'' }}">3 · Compte rendu</li><li class="{{ $validated?'fi-done':'' }}">4 · Validation & export</li></ol>
    @if(!$study->reports->contains('status','validated'))
    <details class="fi-card fi-import" @if($images->isEmpty()) open @endif><summary>Ajouter les images de l’examen</summary><form class="fi-padding fi-upload" method="POST" action="{{ route('medical-imaging.upload',[$healthRecord,$study]) }}" enctype="multipart/form-data">@csrf
        <label>Fichiers de l’étude<input type="file" name="images[]" multiple required accept=".dcm,.dicom,.jpg,.jpeg,.png"></label><button class="fi-button fi-primary">Importer les images</button><p class="fi-hint">DICOM, JPEG ou PNG · 20 Mo par fichier · 100 Mo par import. Pour le U‑17 : séries IRM DICOM uniquement. Les formats non décodables sont signalés.</p>
    </form></details>
    @endif
    <details class="fi-card fi-identity" @if(!$study->identity_checked && $images->isNotEmpty()) open @endif><summary>Identité des images <span class="fi-badge {{ $study->identity_checked?'fi-green':'fi-amber' }}">{{ $study->identity_checked?'Vérifiée':'À vérifier' }}</span></summary>
        <div class="fi-padding"><div class="fi-identity-grid"><div><small>Profil FIT</small><strong>{{ $study->player->full_name??$study->player->name }}</strong><span>Naissance : {{ $study->player->date_of_birth?->format('d/m/Y')??'Non renseignée' }}</span></div><div><small>Métadonnées des images</small><strong>{{ $identity['name']??'Images non importées' }}</strong><span>Identifiant : {{ $identity['id']??'—' }} · Naissance : {{ $identity['birth_date']??'—' }}</span></div></div>
        @if(!empty($identity['birth_date']) && $study->player->date_of_birth && $identity['birth_date']!==$study->player->date_of_birth->format('Ymd'))<div class="fi-notice fi-amber">La date de naissance des images diffère du profil FIT. Vérifiez la pièce source et expliquez la concordance d’identité ; la différence reste visible dans la vigilance.</div>@endif
        @if(!$study->identity_checked && $images->isNotEmpty())<form class="fi-form" method="POST" action="{{ route('medical-imaging.identity',[$healthRecord,$study]) }}">@csrf<label>Pièce / contrôle effectué et justification<textarea name="identity_note" required minlength="5" maxlength="2000" rows="2">{{ old('identity_note') }}</textarea></label><label class="fi-check"><input type="checkbox" name="identity_checked" value="1" required> J’ai vérifié que ces images concernent ce joueur.</label><button class="fi-button">Enregistrer la vérification</button></form>@else<p class="fi-hint">{{ $study->identity_note }}</p>@endif
        </div>
    </details>
    <div class="fi-reading-grid">
        <section class="fi-card fi-viewer"><div class="fi-card-head"><h2>Images de l’examen</h2><p>Les annotations et images de référence sont associées au compte rendu.</p></div>
            <div class="fi-viewer-tools"><label class="fi-image-select">Série / image<select id="fi-image" aria-label="Choisir une image">@foreach($images as $i=>$image)<option value="{{ $i }}">{{ $image['series'] }} · {{ $image['name'] }}</option>@endforeach</select></label><a id="fi-source-download" class="fi-button" href="#">Fichier DICOM</a></div>
            <div class="fi-frame-controls"><button class="fi-button" type="button" id="fi-prev" aria-label="Coupe précédente">←</button><input type="range" id="fi-frame" min="0" max="0" value="0" aria-label="Coupe de l’image"><span id="fi-frame-label">—</span><button class="fi-button" type="button" id="fi-next" aria-label="Coupe suivante">→</button></div>
            <div class="fi-canvas-wrap" id="fi-canvas-wrap"><canvas id="fi-canvas" aria-label="Image médicale avec annotations"></canvas><div id="fi-viewer-message" role="status">{{ $images->isEmpty()?'Importez les images pour commencer la lecture.':'Chargement de l’image…' }}</div></div>
            <div class="fi-viewer-tools"><button class="fi-button" id="fi-zoom-in" type="button" aria-label="Agrandir">Zoom +</button><button class="fi-button" id="fi-zoom-out" type="button" aria-label="Réduire">Zoom −</button><button class="fi-button" id="fi-reset" type="button">Réinitialiser</button><button class="fi-button" id="fi-invert" type="button">Inverser</button><button class="fi-button" id="fi-measure" type="button" @disabled(!$editable)>Mesurer · 2 points</button></div>
            <div class="fi-window-controls"><label>Centre<input type="number" id="fi-center" step="any" min="-100000" max="100000"></label><label>Largeur<input type="number" id="fi-width" step="any" min="1" max="200000"></label><button class="fi-button" id="fi-window" type="button">Appliquer le contraste</button></div>
            <p class="fi-hint fi-padding" id="fi-calibration">Mesures en mm uniquement si une calibration DICOM est disponible. Vérifiez sa pertinence pour l’examen.</p>
            <div class="fi-reference-head"><strong>Images de référence</strong><button class="fi-button" id="fi-bookmark" type="button" @disabled(!$editable)>+ Ajouter cette coupe</button></div><ul class="fi-reference-list" id="fi-references"></ul>
            <p class="fi-hint fi-padding">Le rendu web aide à la revue. Confirmez sa qualité sur une station adaptée si l’examen l’exige. Une erreur de décodage ne signifie pas que l’image est normale.</p>
        </section>
        <section class="fi-card fi-report"><div class="fi-card-head"><h2>Compte rendu {{ $report?'· v'.$report->version:'' }}</h2><p>{{ $validated?'Version conservée après validation médicale.':'Rédigez, enregistrez puis relisez avant de valider.' }}</p></div>
            <form id="fi-report-form" class="fi-form" method="POST" action="{{ route('medical-imaging.report.save',[$healthRecord,$study]) }}">@csrf
                @if($report && !$revision)<input type="hidden" name="report_id" value="{{ $report->id }}"><input type="hidden" name="edit_revision" value="{{ $report->edit_revision }}">@endif
                <input type="hidden" name="reference_images" id="fi-reference-data" value="{{ old('reference_images',json_encode($report?->reference_images??[])) }}">
                <fieldset @disabled($validated)>
                    <label>Technique<textarea name="technique" rows="2" maxlength="6000" placeholder="Protocole, séquences et conditions de l’examen">{{ old('technique',$report?->technique) }}</textarea></label>
                    <label>Qualité de l’examen<select name="quality"><option value="interpretable" @selected(old('quality',$report?->quality)==='interpretable')>Interprétable</option><option value="limited" @selected(old('quality',$report?->quality)==='limited')>Interprétation limitée</option><option value="uninterpretable" @selected(old('quality',$report?->quality)==='uninterpretable')>Non interprétable</option></select></label>
                    @if($study->purpose==='age_u17') @include('health-records.imaging.u17-fields') @endif
                    <label>Observations<textarea name="findings" rows="5" maxlength="20000" placeholder="Décrivez les constatations et leurs limites">{{ old('findings',$report?->findings) }}</textarea></label>
                    <label>Conclusion<textarea name="conclusion" rows="4" maxlength="12000" placeholder="Conclusion du médecin et suites proposées">{{ old('conclusion',$report?->conclusion) }}</textarea></label>
                </fieldset>
                @if(!$validated)<div class="fi-save-bar"><span id="fi-save-state" class="fi-hint" role="status">Enregistrement manuel du brouillon.</span><button class="fi-button fi-primary" type="submit">Enregistrer le brouillon</button></div>@endif
            </form>
            @if($report && $report->status==='draft')
                <div class="fi-validation"><h3>Validation médicale</h3><p>Validez uniquement les données déjà enregistrées. Les modifications non enregistrées doivent être sauvegardées d’abord.</p>
                @if(auth()->user()->hasAnyRole(['doctor','team_doctor','club_medical','association_medical']))<form method="POST" id="fi-validation-form" action="{{ route('medical-imaging.report.validate',[$healthRecord,$study,$report]) }}">@csrf<input type="hidden" name="edit_revision" value="{{ $report->edit_revision }}"><label class="fi-check"><input type="checkbox" name="confirm" value="1" required> J’ai relu les images, l’identité et le compte rendu ; je valide cette version.</label><button class="fi-button fi-primary" id="fi-validate-button">Valider le compte rendu</button></form>@else<p class="fi-notice">La validation doit être réalisée depuis un compte médecin autorisé.</p>@endif
                </div>
            @endif
            @if($validated)<div class="fi-validation"><h3>Compte rendu validé</h3><p>{{ $report->validator_name??$report->validator?->name }} · {{ $report->validated_at->format('d/m/Y H:i') }}</p><div class="fi-actions"><a class="fi-button fi-primary" href="{{ route('medical-imaging.report.dicom',[$healthRecord,$study,$report]) }}">Télécharger DICOM SR</a><a class="fi-button" href="{{ route('medical-imaging.report.pdf',[$healthRecord,$study,$report]) }}">Télécharger PDF</a><a class="fi-button" href="{{ route('medical-imaging.show',[$healthRecord,$study,'revise'=>1]) }}">Créer une nouvelle version</a></div>
            <details class="fi-pacs"><summary>Transmission au PACS <span class="fi-badge {{ $report->pacs_status==='stored'?'fi-green':'' }}">{{ ['not_sent'=>'Non envoyé','stored'=>'Réception confirmée','failed'=>'À vérifier'][$report->pacs_status]??'À vérifier' }}</span></summary><p>Le rapport et ses images de référence sont transmis au PACS configuré. La compatibilité doit être vérifiée avec ce PACS.</p>
                @if(config('medical_imaging.pacs_url'))<form method="POST" action="{{ route('medical-imaging.report.pacs',[$healthRecord,$study,$report]) }}">@csrf<label class="fi-check"><input name="confirm_pacs" type="checkbox" value="1" required> Envoyer ce rapport et les images référencées au PACS configuré.</label><button class="fi-button">Envoyer au PACS</button></form>@else<p class="fi-notice">Connexion PACS non configurée. Le téléchargement DICOM SR reste disponible.</p>@endif
            </details></div>@endif
            @if($study->reports->count()>1)<details class="fi-history"><summary>Versions précédentes</summary>@foreach($study->reports->skip(1) as $previous)<p>Version {{ $previous->version }} · {{ $previous->status==='validated'?'Validée':'Brouillon' }} @if($previous->status==='validated')<a href="{{ route('medical-imaging.report.pdf',[$healthRecord,$study,$previous]) }}">PDF</a> · <a href="{{ route('medical-imaging.report.dicom',[$healthRecord,$study,$previous]) }}">DICOM SR</a>@endif</p>@endforeach</details>@endif
        </section>
    </div>
</div>
<script type="application/json" id="fi-images-data">{!! json_encode($images,JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS|JSON_HEX_QUOT) !!}</script>
<script src="{{ asset('js/medical-imaging.js') }}" defer></script>
@endsection
