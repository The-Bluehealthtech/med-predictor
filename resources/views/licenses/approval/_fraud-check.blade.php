@php
    $player = $license->player;
    $passport = $player?->passport;
    $photoDoc = $license->documents->where('document_type', 'photo')->sortByDesc('id')->first();
    $identityDoc = $license->documents->where('document_type', 'identity')->sortByDesc('id')->first();
    $contractDoc = $license->documents->where('document_type', 'contract')->sortByDesc('id')->first();
    $licensePhoto = $license->photo?->photo_path ? asset('storage/' . ltrim($license->photo->photo_path, '/')) : null;
    $profilePhoto = $player?->player_picture_url;
    $passportPhoto = $passport?->photo_url;
    $passportSignature = $passport?->signature_url;
    $submittedPhoto = $photoDoc && str_starts_with((string) $photoDoc->mime_type, 'image/') ? route('licenses.document', $photoDoc) : null;
    $identityPhoto = $identityDoc && str_starts_with((string) $identityDoc->mime_type, 'image/') ? route('licenses.document', $identityDoc) : null;
    $registryDob = data_get($license->identity_check, 'registry.date_of_birth');
    $passportDob = $passport?->fifa_date_of_birth;
    $declaredDob = $player?->date_of_birth;
    $declaredAge = $declaredDob ? $declaredDob->age : null;
    $sources = collect([$licensePhoto, $profilePhoto, $passportPhoto, $submittedPhoto, $identityPhoto])->filter()->unique();
    $reviewStatuses = \App\Models\LicenseIntegrityReview::STATUSES;
    $latestReview = $license->integrityReviews->first();
@endphp

<section class="mt-4 rounded-2xl border border-slate-200 bg-white p-5" aria-labelledby="h-fraud-check">
    <div class="flex flex-wrap items-start justify-between gap-3">
        <div>
            <p class="text-xs font-bold uppercase tracking-wide text-slate-500">Contrôle fédération</p>
            <h2 id="h-fraud-check" class="mt-1 text-lg font-semibold text-slate-900">Anti-fraude · identité et âge</h2>
            <p class="mt-1 text-sm text-slate-600">Comparaison des sources disponibles. Aucun score biométrique n’est généré tant qu’un moteur validé n’est pas connecté.</p>
        </div>
        <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-700">Revue humaine requise</span>
    </div>

    <div class="mt-4 grid gap-4 xl:grid-cols-2">
        <div class="rounded-xl border border-slate-200 p-4">
            <h3 class="font-semibold text-slate-900">Comparaison des photos</h3>            <div class="mt-3 grid grid-cols-2 gap-3 sm:grid-cols-3">
                @foreach([
                    ['label' => 'Photo licence', 'url' => $licensePhoto],
                    ['label' => 'Profil joueur', 'url' => $profilePhoto],
                    ['label' => 'Passeport joueur', 'url' => $passportPhoto],
                    ['label' => 'Photo déposée', 'url' => $submittedPhoto],
                    ['label' => 'Pièce d’identité', 'url' => $identityPhoto],
                ] as $item)
                    <div class="rounded-xl border border-slate-200 bg-slate-50 p-2">
                        <p class="mb-2 text-xs font-semibold text-slate-600">{{ $item['label'] }}</p>
                        @if($item['url'])
                            <img src="{{ $item['url'] }}" alt="{{ $item['label'] }}" class="h-28 w-full rounded-lg object-cover">
                        @else
                            <div class="flex h-28 items-center justify-center rounded-lg bg-slate-100 px-2 text-center text-xs text-slate-500">Non disponible</div>
                        @endif
                    </div>
                @endforeach
            </div>
            @if($photoDoc)
                <p class="mt-3 text-xs text-slate-600">Photo fournie dans le dossier :
                    <a href="{{ route('licenses.document', $photoDoc) }}" target="_blank" rel="noopener" class="font-semibold text-blue-700 hover:underline">{{ $photoDoc->original_name }}</a>
                </p>
            @endif
            <p class="mt-3 rounded-lg bg-blue-50 px-3 py-2 text-xs text-blue-800">{{ $sources->count() >= 2 ? 'Plusieurs sources photo sont disponibles pour comparaison visuelle.' : 'Une seule source photo est disponible : comparaison insuffisante.' }}</p>
        </div>

        <div class="rounded-xl border border-slate-200 p-4">
            <h3 class="font-semibold text-slate-900">Signatures</h3>            <div class="mt-3 grid gap-3 sm:grid-cols-2">
                <div class="rounded-xl border border-slate-200 bg-slate-50 p-3">
                    <p class="text-xs font-semibold text-slate-600">Signature de référence</p>
                    @if($passportSignature)
                        <img src="{{ $passportSignature }}" alt="Signature de référence" class="mt-2 h-20 w-full object-contain">
                    @else
                        <div class="mt-2 flex h-20 items-center justify-center text-xs text-slate-500">Aucune signature joueur structurée</div>
                    @endif
                </div>
                <div class="rounded-xl border border-slate-200 bg-slate-50 p-3">
                    <p class="text-xs font-semibold text-slate-600">Pièces à contrôler</p>
                    <div class="mt-2 space-y-2 text-sm">
                        @foreach([$identityDoc, $contractDoc] as $doc)
                            @if($doc)<a href="{{ route('licenses.document', $doc) }}" target="_blank" rel="noopener" class="block text-blue-700 hover:underline">{{ $doc->label() }}</a>@endif
                        @endforeach
                        @if(!$identityDoc && !$contractDoc)<span class="text-xs text-slate-500">Aucune pièce exploitable pour une seconde signature.</span>@endif
                    </div>
                </div>
            </div>
            <p class="mt-3 rounded-lg bg-amber-50 px-3 py-2 text-xs text-amber-800">La comparaison automatique de signature n’est pas activée. La fédération conserve la décision finale.</p>
        </div>
    </div>

    <div class="mt-4 grid gap-4 xl:grid-cols-2">
        <div class="rounded-xl border border-slate-200 p-4">
            <h3 class="font-semibold text-slate-900">Cohérence d’identité</h3>
            <dl class="mt-3 grid grid-cols-[minmax(0,1fr)_auto] gap-x-4 gap-y-2 text-sm">
                <dt class="text-slate-500">Nom FIT</dt><dd class="font-medium text-slate-900">{{ trim(($player?->first_name ?? '') . ' ' . ($player?->last_name ?? '')) ?: '—' }}</dd>
                <dt class="text-slate-500">ID FIFA</dt><dd class="font-medium text-slate-900">{{ $player?->fifa_connect_id ?: 'Absent' }}</dd>
                <dt class="text-slate-500">Dernière vérification registre</dt><dd class="font-medium text-slate-900">{{ $license->identity_check_status ? (\App\Services\Licensing\FifaIdRegistry::STATUSES[$license->identity_check_status] ?? $license->identity_check_status) : 'Non effectuée' }}</dd>
                <dt class="text-slate-500">Pièce d’identité</dt><dd class="font-medium text-slate-900">{{ $identityDoc ? 'Fournie' : 'Absente' }}</dd>
            </dl>
        </div>

        <div class="rounded-xl border border-slate-200 p-4">
            <h3 class="font-semibold text-slate-900">Cohérence de l’âge</h3>
            <dl class="mt-3 grid grid-cols-[minmax(0,1fr)_auto] gap-x-4 gap-y-2 text-sm">
                <dt class="text-slate-500">Date de naissance déclarée</dt><dd class="font-medium text-slate-900">{{ $declaredDob?->format('d/m/Y') ?? '—' }}</dd>
                <dt class="text-slate-500">Âge calculé aujourd’hui</dt><dd class="font-medium text-slate-900">{{ $declaredAge !== null ? $declaredAge . ' ans' : '—' }}</dd>
                <dt class="text-slate-500">Catégorie licence</dt><dd class="font-medium text-slate-900">{{ $license->age_category ?: '—' }}</dd>
                <dt class="text-slate-500">Date passeport joueur</dt><dd class="font-medium {{ $passportDob && $declaredDob && $passportDob->toDateString() !== $declaredDob->toDateString() ? 'text-red-700' : 'text-slate-900' }}">{{ $passportDob?->format('d/m/Y') ?? 'Non disponible' }}</dd>
                <dt class="text-slate-500">Date registre FIFA ID</dt><dd class="font-medium {{ $registryDob && $declaredDob && $registryDob !== $declaredDob->toDateString() ? 'text-red-700' : 'text-slate-900' }}">{{ $registryDob ? \Illuminate\Support\Carbon::parse($registryDob)->format('d/m/Y') : 'Non disponible' }}</dd>
                <dt class="text-slate-500">Âge estimé biométrie / IRM</dt><dd class="font-medium text-slate-500">Moteur non connecté</dd>
            </dl>
            <p class="mt-3 rounded-lg bg-slate-50 px-3 py-2 text-xs text-slate-600">Toute estimation future devra afficher la méthode, la date, la source et l’incertitude ; elle ne modifiera jamais automatiquement la date de naissance déclarée.</p>
        </div>
    </div>

    <form method="POST" action="{{ route('licenses.integrity-review', $license) }}" class="mt-4 rounded-xl border border-slate-200 bg-slate-50 p-4">
        @csrf
        <div class="flex flex-wrap items-center justify-between gap-2">
            <div>
                <h3 class="font-semibold text-slate-900">Conclusion de la revue fédération</h3>
                <p class="text-xs text-slate-500">Enregistrement ajouté à l’historique ; il ne décide pas automatiquement de la licence.</p>
            </div>
            @if($latestReview)
                <span class="text-xs text-slate-500">Dernière revue : {{ $latestReview->reviewed_at?->format('d/m/Y H:i') }}</span>
            @endif
        </div>
        <div class="mt-3 grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
            @foreach(['photo_status' => 'Photos', 'signature_status' => 'Signatures', 'identity_status' => 'Identité', 'age_status' => 'Âge'] as $field => $label)
                <label class="text-sm">
                    <span class="font-semibold text-slate-700">{{ $label }}</span>
                    <select name="{{ $field }}" required class="mt-1 w-full rounded-xl border border-slate-300 px-3 py-2 text-sm">
                        @foreach($reviewStatuses as $value => $statusLabel)
                            <option value="{{ $value }}" @selected(old($field, $latestReview?->{$field} ?? 'insufficient') === $value)>{{ $statusLabel }}</option>
                        @endforeach
                    </select>
                </label>
            @endforeach
        </div>
        <label class="mt-3 block text-sm">
            <span class="font-semibold text-slate-700">Notes de contrôle <span class="font-normal text-slate-500">(facultatif)</span></span>
            <textarea name="notes" rows="2" maxlength="2000" class="mt-1 w-full rounded-xl border border-slate-300 px-3 py-2 text-sm">{{ old('notes') }}</textarea>
        </label>
        <button type="submit" class="mt-3 rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800">Enregistrer la revue</button>
    </form>

    @if($license->integrityReviews->isNotEmpty())
        <div class="mt-4">
            <h3 class="text-sm font-semibold text-slate-900">Historique des revues</h3>
            <ul class="mt-2 divide-y divide-slate-100 rounded-xl border border-slate-200 bg-white px-4">
                @foreach($license->integrityReviews->take(5) as $review)
                    <li class="py-3 text-sm">
                        <div class="flex flex-wrap items-center justify-between gap-2">
                            <span class="font-medium text-slate-900">{{ $review->reviewer?->name ?? 'Fédération' }} · {{ $review->reviewed_at?->format('d/m/Y H:i') }}</span>
                            <span class="text-xs text-slate-500">Photo {{ $reviewStatuses[$review->photo_status] ?? $review->photo_status }} · Signature {{ $reviewStatuses[$review->signature_status] ?? $review->signature_status }} · Identité {{ $reviewStatuses[$review->identity_status] ?? $review->identity_status }} · Âge {{ $reviewStatuses[$review->age_status] ?? $review->age_status }}</span>
                        </div>
                        @if($review->notes)<p class="mt-1 text-slate-600">{{ $review->notes }}</p>@endif
                    </li>
                @endforeach
            </ul>
        </div>
    @endif
</section>
