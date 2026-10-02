@php
    $player = $license->player;
    $club = $license->club;
    $association = $club?->association;
    $licensePhoto = $license->photo?->photo_path ? asset('storage/' . ltrim($license->photo->photo_path, '/')) : null;
    $playerPhoto = $licensePhoto ?: $player?->player_picture_url;
    $signature = $player?->passport?->signature_url;
    $holderName = trim(($player?->first_name ?? '') . ' ' . ($player?->last_name ?? '')) ?: ($player?->name ?? 'Joueur');
    $season = $license->season ?: '—';
    $validFrom = $license->issue_date ?? $license->approved_at;
    $validTo = $license->expiry_date;
@endphp

<div class="license-pair">
    <article class="cr80 cr80-front">
        <header class="card-header">
            <div class="brand-lockup">
                @if($association)<img src="{{ $association->getLogoUrl() }}" alt="Logo {{ $association->name }}" class="brand-logo">@endif
                <div><strong>{{ $association?->getDisplayName() ?? 'Fédération' }}</strong><span>Licence de joueur</span></div>
            </div>
            <div class="season">{{ $season }}</div>
        </header>
        <div class="card-body front-body">            <div class="photo-box">
                @if($playerPhoto)
                    <img src="{{ $playerPhoto }}" alt="Photo de {{ $holderName }}">
                @else
                    <div class="photo-missing">Photo manquante</div>
                @endif
            </div>
            <div class="identity">
                <span class="eyebrow">Nom</span><strong>{{ mb_strtoupper($player?->last_name ?? '') }}</strong>
                <span class="eyebrow">Prénom</span><strong>{{ $player?->first_name ?? '—' }}</strong>
                <div class="two-cols">
                    <div><span class="eyebrow">Né(e) le</span><strong>{{ $player?->date_of_birth?->format('d/m/Y') ?? '—' }}</strong></div>
                    <div><span class="eyebrow">Catégorie</span><strong>{{ $license->age_category ?: '—' }}</strong></div>
                </div>
                <span class="eyebrow">Nationalité</span><strong>{{ $player?->nationality ?: '—' }}</strong>
                <span class="eyebrow">Club</span><strong>{{ $club?->name ?? '—' }}</strong>
            </div>
            <div class="club-mark">
                @if($club)<img src="{{ $club->getLogoUrl() }}" alt="Logo {{ $club->name }}">@endif
            </div>
        </div>
        <footer class="card-footer">            <div><span>N° licence</span><strong>{{ $license->license_number ?: 'FIT-' . $license->id }}</strong></div>
            <div><span>Valide du</span><strong>{{ $validFrom?->format('d/m/Y') ?? '—' }}</strong></div>
            <div><span>Au</span><strong>{{ $validTo?->format('d/m/Y') ?? '—' }}</strong></div>
        </footer>
    </article>

    <article class="cr80 cr80-back">
        <header class="card-header light">
            <div class="brand-lockup">
                @if($association)<img src="{{ $association->getLogoUrl() }}" alt="" class="brand-logo">@endif
                <div><strong>{{ $association?->getDisplayName() ?? 'Fédération' }}</strong><span>Licence officielle</span></div>
            </div>
            @if($club)<img src="{{ $club->getLogoUrl() }}" alt="" class="club-logo-small">@endif
        </header>
        <div class="card-body back-body">
            <dl class="details">
                <div><dt>Position</dt><dd>{{ $player?->position ?: '—' }}</dd></div>
                <div><dt>Pied fort</dt><dd>{{ $player?->preferred_foot ?: '—' }}</dd></div>
                <div><dt>ID FIFA</dt><dd>{{ $player?->fifa_connect_id ?: 'Non renseigné' }}</dd></div>
                <div><dt>Type</dt><dd>{{ \App\Services\Licensing\LicenseWorkflow::describe($license) }}</dd></div>
            </dl>            <div class="signature-box">
                @if($signature)
                    <img src="{{ $signature }}" alt="Signature du joueur">
                @else
                    <span>Signature non disponible</span>
                @endif
                <small>Signature du joueur</small>
            </div>
            <div class="verification-block">
                <strong>{{ $license->license_number ?: 'FIT-' . $license->id }}</strong>
                <span>Licence personnelle et non transférable.</span>
                <span>Vérifier dans FIT avant toute utilisation officielle.</span>
            </div>
        </div>
    </article>
</div>
