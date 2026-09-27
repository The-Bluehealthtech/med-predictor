<!doctype html>
<html lang="fr">
<head><meta charset="utf-8"><title>{{ __('Créer une compétition') }}</title><script src="https://cdn.tailwindcss.com"></script></head>
<body class="bg-slate-100 min-h-screen"><main class="max-w-4xl mx-auto p-6">
<div class="bg-white rounded-lg shadow p-6"><h1 class="text-2xl font-bold mb-6">{{ __('Créer une compétition') }}</h1>
@if($errors->any())<div class="bg-red-100 text-red-800 p-4 mb-4 rounded"><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
<form method="POST" action="{{ route('competitions.store') }}" class="grid grid-cols-1 md:grid-cols-2 gap-4">@csrf
<label>{{ __('Nom') }}<input name="name" required class="w-full border rounded p-2"></label>
<label>{{ __('Nom court') }}<input name="short_name" class="w-full border rounded p-2"></label>
<label>{{ __('Type') }}<select name="type" required class="w-full border rounded p-2"><option value="league">{{ __('Championnat') }}</option><option value="cup">{{ __('Coupe') }}</option><option value="tournament">{{ __('Tournoi') }}</option><option value="friendly">{{ __('Amical') }}</option></select></label>
<label>{{ __('Saison') }}<input name="season" required value="2026" class="w-full border rounded p-2"></label>
<label>{{ __('Date de début') }}<input type="date" name="start_date" required class="w-full border rounded p-2"></label>
<label>{{ __('Date de fin') }}<input type="date" name="end_date" required class="w-full border rounded p-2"></label>
<label>{{ __('Format') }}<select name="format" required class="w-full border rounded p-2"><option value="round_robin">{{ __('Toutes rondes') }}</option><option value="knockout">{{ __('Élimination directe') }}</option><option value="group_stage">{{ __('Phase de groupes') }}</option></select></label>
<label>{{ __('Statut') }}<select name="status" required class="w-full border rounded p-2"><option value="upcoming">{{ __('À venir') }}</option><option value="active">{{ __('Active') }}</option></select></label>
<label>{{ __('Minimum d’équipes') }}<input type="number" name="min_teams" min="2" value="2" class="w-full border rounded p-2"></label>
<label>{{ __('Maximum d’équipes') }}<input type="number" name="max_teams" min="2" value="20" class="w-full border rounded p-2"></label>
<label class="md:col-span-2">{{ __('Description') }}<textarea name="description" rows="3" class="w-full border rounded p-2"></textarea></label>
<div class="md:col-span-2 flex gap-3"><a href="{{ url()->previous() }}" class="px-4 py-2 bg-gray-200 rounded">{{ __('Annuler') }}</a><button class="px-4 py-2 bg-green-600 text-white rounded" type="submit">{{ __('Créer la compétition') }}</button></div>
</form></div></main></body></html>
