@extends('layouts.app')

@php
    $isTeam = old('registration_type', $official->registration_type) === 'TeamOfficial';
    $roles = $isTeam ? $teamRoles : $boardRoles;
    $roleField = $isTeam ? 'team_official_role' : 'organisation_official_role';
    $v = fn ($field) => old($field, $official->{$field} instanceof \Carbon\CarbonInterface ? $official->{$field}->format('Y-m-d') : $official->{$field});
    $input = 'mt-1 block w-full rounded-lg border-gray-300 shadow-sm text-sm';
@endphp

@section('title', ($official->exists ? 'Modifier' : 'Nouvelle fiche') . ' — ' . $club->name)

@section('content')
<div class="max-w-4xl mx-auto px-4 py-8 space-y-6">
    <div>
        <p class="text-xs font-semibold uppercase tracking-wider text-slate-600">{{ str_replace(' (Démo)', '', $club->name) }} · format FIFA Connect</p>
        <h1 class="text-2xl font-bold text-gray-900">{{ $official->exists ? $official->fullName() : ($isTeam ? 'Nouveau membre du staff' : 'Nouveau dirigeant') }}</h1>
    </div>
    @if($errors->any())<div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800"><ul class="list-disc ml-5">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>@endif

    <form method="POST" action="{{ $official->exists ? route('club-officials.update', [$club, $official]) : route('club-officials.store', $club) }}" class="space-y-6">
        @csrf @if($official->exists) @method('PUT') @endif
        <input type="hidden" name="registration_type" value="{{ $isTeam ? 'TeamOfficial' : 'OrganisationOfficial' }}">

        <section class="bg-white rounded-lg shadow p-5 space-y-4">
            <h2 class="font-semibold text-gray-900">Registration <span class="text-xs font-normal text-gray-400 font-mono">{{ $isTeam ? 'TeamOfficial' : 'OrganisationOfficial' }}</span></h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <label class="block text-sm font-medium text-gray-700">{{ $isTeam ? 'TeamOfficialRole' : 'OrganisationOfficialRole' }}
                    <select name="{{ $roleField }}" class="{{ $input }}" required>
                        @foreach($roles as $code => $role)
                            <option value="{{ $code }}" @selected($v($roleField) === $code) @disabled($role['in_xsd'] === false)>{{ $role['label'] }} ({{ $code }}){{ $role['in_xsd'] === null && !$role['confirmed'] ? ' — à confirmer' : '' }}</option>
                        @endforeach
                    </select>
                </label>
                <label class="block text-sm font-medium text-gray-700">Status
                    <select name="status" class="{{ $input }}">@foreach(config('fifa_connect_roles.statuses') as $k => $l)<option value="{{ $k }}" @selected($v('status') === $k)>{{ $l }}</option>@endforeach</select>
                </label>
                <label class="block text-sm font-medium text-gray-700">Discipline
                    <select name="discipline" class="{{ $input }}">@foreach(config('fifa_connect_roles.disciplines') as $k => $l)<option value="{{ $k }}" @selected($v('discipline') === $k)>{{ $l }}</option>@endforeach</select>
                </label>
                <label class="block text-sm font-medium text-gray-700">ValidFrom<input type="date" name="registration_valid_from" value="{{ $v('registration_valid_from') }}" class="{{ $input }}" required></label>
                <label class="block text-sm font-medium text-gray-700">ValidTo<input type="date" name="registration_valid_to" value="{{ $v('registration_valid_to') }}" class="{{ $input }}"></label>
                <label class="block text-sm font-medium text-gray-700">Description du rôle<input name="role_description" value="{{ $v('role_description') }}" maxlength="120" class="{{ $input }}"></label>
            </div>
            @if($isTeam)
                <label class="flex items-center gap-2 text-sm text-gray-700"><input type="hidden" name="is_head_coach" value="0"><input type="checkbox" name="is_head_coach" value="1" @checked(old('is_head_coach', $official->is_head_coach)) class="rounded border-gray-300"> Entraîneur principal du club (rôle « Coach »)</label>
            @endif
            @unless($xsdInstalled)<p class="text-xs text-amber-700">Bundle XSD FIFA Connect non installé : les codes marqués « à confirmer » devront être validés avant un échange avec FIFA Connect.</p>@endunless
        </section>

        <section class="bg-white rounded-lg shadow p-5 space-y-4">
            <h2 class="font-semibold text-gray-900">Person</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <label class="block text-sm font-medium text-gray-700">PersonFIFAId<input name="person_fifa_id" value="{{ $v('person_fifa_id') }}" maxlength="40" class="{{ $input }} font-mono" placeholder="si attribué par FIFA Connect"></label>
                <label class="block text-sm font-medium text-gray-700">InternationalFirstName<input name="international_first_name" value="{{ $v('international_first_name') }}" required maxlength="80" class="{{ $input }}"></label>
                <label class="block text-sm font-medium text-gray-700">InternationalLastName<input name="international_last_name" value="{{ $v('international_last_name') }}" required maxlength="80" class="{{ $input }}"></label>
                <label class="block text-sm font-medium text-gray-700">LocalFirstName<input name="local_first_name" value="{{ $v('local_first_name') }}" maxlength="80" class="{{ $input }}" placeholder="ex. écriture arabe"></label>
                <label class="block text-sm font-medium text-gray-700">LocalLastName<input name="local_last_name" value="{{ $v('local_last_name') }}" maxlength="80" class="{{ $input }}"></label>
                <label class="block text-sm font-medium text-gray-700">PopularName<input name="popular_name" value="{{ $v('popular_name') }}" maxlength="80" class="{{ $input }}"></label>
                <label class="block text-sm font-medium text-gray-700">Gender
                    <select name="gender" class="{{ $input }}" required>@foreach(config('fifa_connect_roles.genders') as $k => $l)<option value="{{ $k }}" @selected($v('gender') === $k)>{{ $l }}</option>@endforeach</select>
                </label>
                <label class="block text-sm font-medium text-gray-700">DateOfBirth<input type="date" name="date_of_birth" value="{{ $v('date_of_birth') }}" required class="{{ $input }}"></label>
                <label class="block text-sm font-medium text-gray-700">Nationality (ISO, 2 lettres)<input name="nationality" value="{{ $v('nationality') }}" required maxlength="2" class="{{ $input }} uppercase" placeholder="TN"></label>
                <label class="block text-sm font-medium text-gray-700">SecondNationality<input name="second_nationality" value="{{ $v('second_nationality') }}" maxlength="2" class="{{ $input }} uppercase"></label>
                <label class="block text-sm font-medium text-gray-700">CountryOfBirth<input name="country_of_birth" value="{{ $v('country_of_birth') }}" maxlength="2" class="{{ $input }} uppercase"></label>
                <label class="block text-sm font-medium text-gray-700">PlaceOfBirth<input name="place_of_birth" value="{{ $v('place_of_birth') }}" maxlength="120" class="{{ $input }}"></label>
            </div>
        </section>

        <section class="bg-white rounded-lg shadow p-5 space-y-4">
            <h2 class="font-semibold text-gray-900">Certification <span class="text-xs font-normal text-gray-400">diplôme d'entraîneur ou qualification</span></h2>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <label class="block text-sm font-medium text-gray-700 md:col-span-2">Diplôme<input name="certification_name" value="{{ $v('certification_name') }}" maxlength="120" class="{{ $input }}" placeholder="ex. Licence CAF A"></label>
                <label class="block text-sm font-medium text-gray-700 md:col-span-2">Numéro<input name="certification_number" value="{{ $v('certification_number') }}" maxlength="60" class="{{ $input }}"></label>
                <label class="block text-sm font-medium text-gray-700 md:col-span-2">ValidFrom<input type="date" name="certification_valid_from" value="{{ $v('certification_valid_from') }}" class="{{ $input }}"></label>
                <label class="block text-sm font-medium text-gray-700 md:col-span-2">ValidTo<input type="date" name="certification_valid_to" value="{{ $v('certification_valid_to') }}" class="{{ $input }}"></label>
            </div>
        </section>

        <section class="bg-white rounded-lg shadow p-5 space-y-4">
            <h2 class="font-semibold text-gray-900">Contact <span class="text-xs font-normal text-gray-400">données locales, non transmises à FIFA Connect</span></h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <label class="block text-sm font-medium text-gray-700">E-mail<input type="email" name="email" value="{{ $v('email') }}" maxlength="120" class="{{ $input }}"></label>
                <label class="block text-sm font-medium text-gray-700">Téléphone<input name="phone" value="{{ $v('phone') }}" maxlength="40" class="{{ $input }}"></label>
            </div>
        </section>

        <div class="flex justify-end gap-3">
            <a href="{{ route('club-officials.club', $club) }}" class="px-4 py-2 rounded-lg border border-gray-300 text-sm text-gray-700 hover:bg-gray-50">Annuler</a>
            <button class="px-4 py-2 rounded-lg bg-slate-700 text-white text-sm font-semibold hover:bg-slate-800">Enregistrer la fiche</button>
        </div>
    </form>
</div>
@endsection
