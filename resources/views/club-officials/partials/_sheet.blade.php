{{-- Fiche FIFA Connect d'un officiel (Person, Registration, Certification). Classes « fc-* » stylées par chaque support. --}}
@php
    $d = fn ($v) => $v ? \Illuminate\Support\Carbon::parse($v)->format('d/m/Y') : '—';
    $genders = config('fifa_connect_roles.genders');
    $statuses = config('fifa_connect_roles.statuses');
    $disciplines = config('fifa_connect_roles.disciplines');
@endphp
<table class="fc-table">
    <tr><th colspan="4">Person</th></tr>
    <tr><td class="fc-k">PersonFIFAId</td><td>{{ $official->person_fifa_id ?: 'non attribué par FIFA Connect' }}</td><td class="fc-k">Gender</td><td>{{ $official->gender }} · {{ $genders[$official->gender] ?? '' }}</td></tr>
    <tr><td class="fc-k">InternationalFirstName</td><td>{{ $official->international_first_name }}</td><td class="fc-k">InternationalLastName</td><td>{{ $official->international_last_name }}</td></tr>
    <tr><td class="fc-k">LocalFirstName</td><td>{{ $official->local_first_name ?: '—' }}</td><td class="fc-k">LocalLastName</td><td>{{ $official->local_last_name ?: '—' }}</td></tr>
    <tr><td class="fc-k">PopularName</td><td>{{ $official->popular_name ?: '—' }}</td><td class="fc-k">DateOfBirth</td><td>{{ $d($official->date_of_birth) }}</td></tr>
    <tr><td class="fc-k">Nationality</td><td>{{ $official->nationality }}@if($official->second_nationality) · {{ $official->second_nationality }}@endif</td><td class="fc-k">CountryOfBirth / PlaceOfBirth</td><td>{{ $official->country_of_birth ?: '—' }}{{ $official->place_of_birth ? ' · ' . $official->place_of_birth : '' }}</td></tr>
    <tr><th colspan="4">Registration</th></tr>
    <tr><td class="fc-k">Type</td><td>{{ $official->registration_type }}</td><td class="fc-k">{{ $official->registration_type === 'TeamOfficial' ? 'TeamOfficialRole' : 'OrganisationOfficialRole' }}</td><td>{{ $official->roleCode() }} · {{ $official->roleLabel() }}{{ $official->is_head_coach ? ' (entraîneur principal)' : '' }}</td></tr>
    <tr><td class="fc-k">Organisation</td><td>{{ str_replace(' (Démo)', '', $club->name) }}{{ $club->fifa_connect_id ? ' · OrganisationFIFAId ' . $club->fifa_connect_id : '' }}</td><td class="fc-k">Discipline</td><td>{{ $official->discipline }} · {{ $disciplines[$official->discipline] ?? '' }}</td></tr>
    <tr><td class="fc-k">Status</td><td>{{ $official->status }} · {{ $statuses[$official->status] ?? '' }}</td><td class="fc-k">ValidFrom / ValidTo</td><td>{{ $d($official->registration_valid_from) }} → {{ $official->registration_valid_to ? $d($official->registration_valid_to) : 'sans fin' }}</td></tr>
    @if($official->role_description)<tr><td class="fc-k">Description du rôle</td><td colspan="3">{{ $official->role_description }}</td></tr>@endif
    <tr><th colspan="4">Certification</th></tr>
    @if($official->certification_name)
        <tr><td class="fc-k">CertificationType</td><td>{{ $official->certification_type }}</td><td class="fc-k">Diplôme</td><td>{{ $official->certification_name }}{{ $official->certification_number ? ' · n° ' . $official->certification_number : '' }}</td></tr>
        <tr><td class="fc-k">ValidFrom / ValidTo</td><td colspan="3">{{ $d($official->certification_valid_from) }} → {{ $official->certification_valid_to ? $d($official->certification_valid_to) : 'sans fin' }}</td></tr>
    @else
        <tr><td colspan="4" class="fc-empty">Aucune certification enregistrée.</td></tr>
    @endif
</table>
