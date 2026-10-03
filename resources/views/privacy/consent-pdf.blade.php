<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Consentement au partage des données de santé</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #111; line-height: 1.45; }
        h1 { font-size: 16px; margin: 0 0 6px; }
        h2 { font-size: 12px; margin: 16px 0 6px; border-bottom: 1px solid #ccc; padding-bottom: 2px; }
        .box { border: 1px solid #999; padding: 10px; margin-top: 10px; }
        .choice { font-weight: bold; font-size: 12px; }
        .muted { color: #555; font-size: 10px; }
        .policy { white-space: pre-wrap; }
    </style>
</head>
<body>
    <h1>Consentement au partage des données de santé hors du club</h1>
    <div class="muted">Formulaire n° {{ $consent->id }} · établi le {{ $consent->created_at->format('d/m/Y') }} · politique « {{ $policy->title }} », version {{ $policy->version }} du {{ $policy->published_at->format('d/m/Y') }}</div>

    <h2>Joueur concerné</h2>
    <p>{{ trim(($player->first_name ?? '') . ' ' . ($player->last_name ?? '')) ?: $player->name }}
        @if($player->date_of_birth), né(e) le {{ \Illuminate\Support\Carbon::parse($player->date_of_birth)->format('d/m/Y') }}@endif
        @if($player->club) — {{ $player->club->name }}@endif</p>

    <h2>Signataire</h2>
    <p>{{ $consent->performer_name }} —
        @if($consent->performer_type === 'guardian') représentant légal ({{ $relationships[$consent->performer_relationship] ?? $consent->performer_relationship }}) @else le joueur lui-même @endif</p>

    <h2>Objet</h2>
    <p>Le partage hors du club couvre : la publication du résumé médical international (IPS) du passeport médical sur le serveur de données de santé de FIT, et la consultation par le médecin du club des dossiers transmis par les établissements de santé (laboratoires, imagerie, établissements de soins), à des fins de soins.</p>

    <div class="box">
        <p class="choice">{{ $consent->decision === 'permit' ? '☒ J’autorise' : '☒ Je refuse' }} le partage de mes données de santé hors du club, dans les conditions de la politique ci-dessous.</p>
        <p>Durée : {{ $consent->period_end ? 'jusqu’au ' . $consent->period_end->format('d/m/Y') : 'jusqu’à révocation' }}. Ce consentement peut être révoqué à tout moment auprès du secrétariat médical du club.</p>
    </div>

    <h2>Politique de confidentialité de la fédération</h2>
    <div class="policy">{{ $policy->body }}</div>

    <p class="muted">Signature électronique recueillie par le circuit de signature de FIT. Empreinte de la politique (SHA-256) : {{ $policy->body_sha256 }}.</p>
</body>
</html>
