<?php

namespace App\Services\ClubOfficials;

use App\Models\Club;
use App\Models\ClubOfficial;
use App\Models\User;
use App\Services\FifaConnect\SchemaCatalog;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;

/**
 * Dirigeants et staff des clubs au format FIFA Connect : droits d'accès,
 * catalogue des rôles (validé contre le XSD officiel quand il est installé),
 * règles de saisie et entraîneur principal du club.
 */
final class ClubOfficials
{
    public function __construct(private readonly SchemaCatalog $catalog)
    {
    }

    /** Lecture : le club (ses comptes), sa fédération, l'admin système. */
    public function canView(User $user, Club $club): bool
    {
        return match (true) {
            $user->isSystemAdmin() => true,
            $user->isClubUser() => (int) $user->club_id === (int) $club->id,
            $user->isAssociationUser() => $user->association_id && (int) $user->association_id === (int) $club->association_id,
            default => false,
        };
    }

    /** Écriture : l'administrateur du club, l'administrateur de sa fédération, l'admin système. */
    public function canManage(User $user, Club $club): bool
    {
        return match (true) {
            $user->isSystemAdmin() => true,
            $user->role === 'club_admin' => (int) $user->club_id === (int) $club->id,
            $user->role === 'association_admin' => $user->association_id && (int) $user->association_id === (int) $club->association_id,
            default => false,
        };
    }

    public function clubsFor(User $user): Builder
    {
        $query = Club::query()->orderBy('name');

        return match (true) {
            $user->isSystemAdmin() => $query,
            $user->isClubUser() && (bool) $user->club_id => $query->where('id', $user->club_id),
            $user->isAssociationUser() && (bool) $user->association_id => $query->where('association_id', $user->association_id),
            default => $query->whereRaw('1 = 0'),
        };
    }

    /** Rôles proposés pour un type d'enregistrement, avec l'état de leur vérification. */
    public function roles(string $registrationType): array
    {
        $group = $registrationType === ClubOfficial::TEAM_OFFICIAL ? 'team_official' : 'organisation_official';
        $roles = config("fifa_connect_roles.{$group}", []);
        $official = $this->officialEnum($registrationType);
        foreach ($roles as $code => &$role) {
            $role['in_xsd'] = $official === null ? null : in_array($code, $official, true);
        }

        return $roles;
    }

    public function xsdInstalled(): bool
    {
        return $this->officialEnum(ClubOfficial::TEAM_OFFICIAL) !== null;
    }

    /** Valeurs officielles du XSD FIFA Connect, ou null si le bundle n'est pas installé. */
    private function officialEnum(string $registrationType): ?array
    {
        try {
            return $this->catalog->enumValues($registrationType === ClubOfficial::TEAM_OFFICIAL ? 'TeamOfficialRoleType' : 'OrganisationOfficialRoleType');
        } catch (\Throwable) {
            return null;
        }
    }

    public function rules(string $registrationType): array
    {
        $roleField = $registrationType === ClubOfficial::TEAM_OFFICIAL ? 'team_official_role' : 'organisation_official_role';
        $allowedRoles = array_keys($this->roles($registrationType));
        if ($official = $this->officialEnum($registrationType)) {
            $allowedRoles = array_values(array_intersect($allowedRoles, $official));
        }
        $country = ['nullable', 'string', 'size:2', 'regex:/^[A-Z]{2}$/'];

        return [
            'registration_type' => ['required', Rule::in([ClubOfficial::TEAM_OFFICIAL, ClubOfficial::ORGANISATION_OFFICIAL])],
            $roleField => ['required', Rule::in($allowedRoles)],
            'role_description' => ['nullable', 'string', 'max:120'],
            'is_head_coach' => ['nullable', 'boolean'],
            'person_fifa_id' => ['nullable', 'string', 'max:40', 'regex:/^[A-Za-z0-9]+$/'],
            'international_first_name' => ['required', 'string', 'max:80'],
            'international_last_name' => ['required', 'string', 'max:80'],
            'local_first_name' => ['nullable', 'string', 'max:80'],
            'local_last_name' => ['nullable', 'string', 'max:80'],
            'popular_name' => ['nullable', 'string', 'max:80'],
            'gender' => ['required', Rule::in(array_keys(config('fifa_connect_roles.genders')))],
            'date_of_birth' => ['required', 'date', 'before:today'],
            'nationality' => ['required', 'string', 'size:2', 'regex:/^[A-Z]{2}$/'],
            'second_nationality' => $country,
            'country_of_birth' => $country,
            'place_of_birth' => ['nullable', 'string', 'max:120'],
            'status' => ['required', Rule::in(array_keys(config('fifa_connect_roles.statuses')))],
            'discipline' => ['required', Rule::in(array_keys(config('fifa_connect_roles.disciplines')))],
            'registration_valid_from' => ['required', 'date'],
            'registration_valid_to' => ['nullable', 'date', 'after_or_equal:registration_valid_from'],
            'certification_name' => ['nullable', 'string', 'max:120'],
            'certification_number' => ['nullable', 'string', 'max:60'],
            'certification_valid_from' => ['nullable', 'date'],
            'certification_valid_to' => ['nullable', 'date', 'after_or_equal:certification_valid_from'],
            'email' => ['nullable', 'email', 'max:120'],
            'phone' => ['nullable', 'string', 'max:40'],
        ];
    }

    /** Entraîneur principal actif du club (rôle FIFA Connect « Coach »), ou null. */
    public function headCoach(Club $club): ?ClubOfficial
    {
        if (!Schema::hasTable('club_officials')) {
            return null;
        }

        return ClubOfficial::query()->where('club_id', $club->id)
            ->where('registration_type', ClubOfficial::TEAM_OFFICIAL)->where('team_official_role', 'Coach')
            ->where('status', 'active')
            ->orderByDesc('is_head_coach')->orderByDesc('registration_valid_from')->first();
    }
}
