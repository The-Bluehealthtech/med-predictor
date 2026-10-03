<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $record ? __('Modifier') : __('Créer') }} {{ $type }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen p-6">
    <main class="max-w-2xl mx-auto bg-white rounded-lg shadow p-8">
        <div class="flex items-center justify-between gap-3">
            <a href="{{ $type === 'confederations' ? route('modules.confederations.index') : ($type === 'associations' ? route('associations-view') : route('clubs-view')) }}" class="text-blue-700 underline">{{ __('health_records_edit.back_to_list') }}</a>
            @include('partials.logout-button', ['variant' => 'chip'])
        </div>
        <h1 class="text-2xl font-bold my-6">{{ $record ? __('Modifier') : __('Créer') }} {{ ['confederations' => __('une confédération'), 'associations' => __('une association'), 'clubs' => __('un club')][$type] }}</h1>
        @if(session('success')) <p class="bg-green-100 text-green-800 p-3 mb-4">{{ session('success') }}</p> @endif
        @if($errors->any())
            <ul class="bg-red-100 text-red-800 p-3 mb-4">
                @foreach($errors->all() as $error) <li>{{ $error }}</li> @endforeach
            </ul>
        @endif
        <form method="POST" action="{{ $record ? route('organization-cards.update', [$type, $record->id]) : route('organization-cards.store', $type) }}" class="space-y-5">
            @csrf
            @if($record) @method('PUT') @endif
            <label class="block">{{ __('Nom *') }}
                <input name="name" required maxlength="255" value="{{ old('name', $record?->name) }}" class="mt-1 block w-full border rounded p-2">
            </label>
            <label class="block">{{ __('Nom abrégé') }}<input name="short_name" maxlength="50" value="{{ old('short_name', $record?->short_name) }}" class="mt-1 block w-full border rounded p-2">
            </label>
            @if($type === 'confederations' || $type === 'associations')
                <label class="block">{{ $type === 'confederations' ? 'Continent / pays' : 'Pays' }} {{ $type === 'associations' ? '*' : '' }}
                    <input name="country" {{ $type === 'associations' ? 'required' : '' }} value="{{ old('country', $record?->country) }}" class="mt-1 block w-full border rounded p-2">
                </label>
            @endif
            @if($type === 'confederations')
                <label class="block">{{ __('Année de fondation') }}
                    <input type="number" name="founded_year" min="1800" max="{{ date('Y') }}" value="{{ old('founded_year', $record?->founded_year) }}" class="mt-1 block w-full border rounded p-2">
                </label>
                <label class="block">{{ __('clinical.table_status') }}
                    <select name="status" class="mt-1 block w-full border rounded p-2">
                        @foreach(['active' => 'Active', 'inactive' => 'Inactive', 'suspended' => 'Suspendue'] as $value => $label)
                            <option value="{{ $value }}" @selected(old('status', $record?->status ?? 'active') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </label>
            @elseif($type === 'associations')
                <label class="block">{{ __('Confédération *') }}<select name="confederation_id" required class="mt-1 block w-full border rounded p-2">
                        <option value="">Choisir</option>
                        @foreach($confederations as $confederation)
                            <option value="{{ $confederation->id }}" @selected((int) old('confederation_id', $record?->confederation_id) === (int) $confederation->id)>{{ $confederation->name }}</option>
                        @endforeach
                    </select>
                </label>
            @else
                <label class="block">Association *
                    <select name="association_id" required class="mt-1 block w-full border rounded p-2">
                        <option value="">Choisir</option>
                        @foreach($associations as $association)
                            @if(auth()->user()->isSystemAdmin() || (int) auth()->user()->association_id === (int) $association->id)
                                <option value="{{ $association->id }}" @selected((int) old('association_id', $record?->association_id) === (int) $association->id)>{{ $association->name }}</option>
                            @endif
                        @endforeach
                    </select>
                </label>
                @php($iso = app(\App\Services\FifaConnect\IsoCountries::class))
                <label class="block">Pays (ISO 3166, FIFA Connect)
                    <select name="country_code" class="mt-1 block w-full border rounded p-2">
                        <option value="">Pays de l'association</option>
                        @foreach($iso->options() as $code => $name)
                            <option value="{{ $code }}" @selected(old('country_code', $record?->country_code) === $code)>{{ $name }}</option>
                        @endforeach
                    </select>
                </label>
            @endif
            <p class="text-sm text-gray-600">{{ __('Les identifiants et statuts de synchronisation FIFA sont renseignés uniquement par l\'intégration FIFA.') }}</p>
            <button type="submit" class="bg-blue-700 text-white rounded px-5 py-2">{{ __('clinical.save') }}</button>
        </form>
    </main>
</body>
</html>
