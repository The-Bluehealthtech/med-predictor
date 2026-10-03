{{-- Identité FIFA Connect (PersonLocal) : nationalité et pays de naissance du référentiel ISO 3166, lieu de naissance. --}}
@php
    $iso = app(\App\Services\FifaConnect\IsoCountries::class);
    $countries = $iso->options();
    $nationalityCode = $iso->code(old('nationality', $player?->nationality));
    $birthCountry = $iso->code(old('country_of_birth', $player?->country_of_birth));
    $field = 'w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500';
    // Joueur existant en lecture seule : l'identité est figée, seuls les champs de naissance vides restent à compléter.
    $readOnly = $readOnly ?? false;
    $locked = 'opacity-50 cursor-not-allowed bg-gray-100';
@endphp
@if ($readOnly)
    {{-- Un champ désactivé n'est pas transmis : la valeur figée l'est en champ caché. --}}
    <input type="hidden" name="nationality" value="{{ $player?->nationality }}">
    @if ($birthCountry)<input type="hidden" name="country_of_birth" value="{{ $birthCountry }}">@endif
    @if ($player?->place_of_birth)<input type="hidden" name="place_of_birth" value="{{ $player->place_of_birth }}">@endif
@endif
<div>
    <label for="nationality" class="block text-sm font-medium text-gray-700 mb-2">Nationalité *</label>
    <select name="nationality" id="nationality" required class="{{ $field }} {{ $readOnly ? $locked : '' }}" @disabled($readOnly)>
        <option value="">Sélectionner une nationalité</option>
        @foreach ($countries as $code => $name)
            <option value="{{ $name }}" @selected($nationalityCode === $code)>{{ $name }}</option>
        @endforeach
    </select>
    @if ($player?->nationality && !$nationalityCode)
        <p class="mt-1 text-sm text-amber-700">Valeur actuelle « {{ $player->nationality }} » absente du référentiel ISO 3166 : à corriger.</p>
    @endif
    @error('nationality')
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>

<div>
    <label for="country_of_birth" class="block text-sm font-medium text-gray-700 mb-2">Pays de naissance</label>
    <select name="country_of_birth" id="country_of_birth" class="{{ $field }} {{ $readOnly && $birthCountry ? $locked : '' }}" @disabled($readOnly && $birthCountry)>
        <option value="">Non renseigné</option>
        @foreach ($countries as $code => $name)
            <option value="{{ $code }}" @selected($birthCountry === $code)>{{ $name }}</option>
        @endforeach
    </select>
    @error('country_of_birth')
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>

<div>
    <label for="place_of_birth" class="block text-sm font-medium text-gray-700 mb-2">Lieu de naissance</label>
    <input type="text" name="place_of_birth" id="place_of_birth" maxlength="100" value="{{ old('place_of_birth', $player?->place_of_birth) }}"
           class="{{ $field }} {{ $readOnly && $player?->place_of_birth ? $locked : '' }}" @disabled($readOnly && $player?->place_of_birth) placeholder="Ville de naissance">
    @error('place_of_birth')
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>
