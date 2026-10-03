<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $policy->title }} — v{{ $policy->version }}</title>
    <style>
        body { font-family: system-ui, -apple-system, 'Segoe UI', sans-serif; max-width: 48rem; margin: 2rem auto; padding: 0 1rem; color: #111827; line-height: 1.55; background: #fff; }
        .meta { color: #4b5563; font-size: .9rem; }
        .body { white-space: pre-wrap; margin-top: 1.5rem; }
    </style>
</head>
<body>
    <h1>{{ $policy->title }}</h1>
    <p class="meta">{{ $association?->name }} · version {{ $policy->version }} publiée le {{ $policy->published_at->format('d/m/Y') }} · empreinte SHA-256 {{ $policy->body_sha256 }}</p>
    <div class="body">{{ $policy->body }}</div>
</body>
</html>
