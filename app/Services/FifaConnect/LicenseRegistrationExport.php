<?php

namespace App\Services\FifaConnect;

use App\Models\FifaConnect\Person;
use App\Models\FifaConnect\Registration;
use App\Models\PlayerLicense;
use App\Services\Licensing\LicenseWorkflow;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Traduction d'une licence FIT en message FIFA Connect Data Standard 3.3
 * « PersonLocal » (une personne et son enregistrement), et liste des écarts de
 * données qui empêchent l'export. Les codes sont contrôlés contre les
 * énumérations officielles du paquet XSD (SchemaCatalog) ; un pays saisi en
 * toutes lettres (français ou anglais) est converti par la référence ISO 3166
 * (IsoCountries), sinon signalé.
 */
final class LicenseRegistrationExport
{
    public function __construct(private readonly SchemaCatalog $catalog, private readonly PersonLocalXmlSerializer $serializer, private readonly IsoCountries $iso)
    {
    }

    /** @return array{person:Person, issues:array<string,string>} */
    public function build(PlayerLicense $license): array
    {
        $issues = [];
        $official = $license->club_official_id ? $license->clubOfficial : null;
        $player = $official ? null : $license->player;
        $holder = $official ?? $player;
        if (!$holder) {
            return ['person' => new Person, 'issues' => ['Person' => 'titulaire introuvable']];
        }

        $first = $official ? $official->international_first_name : $player->first_name;
        $last = $official ? $official->international_last_name : $player->last_name;
        $club = DB::table('clubs')->where('id', $license->club_id)->first(['fifa_connect_id', 'country', 'country_code', 'association_id']);
        $federationLanguage = $club?->association_id ? DB::table('license_scale_settings')->where('association_id', $club->association_id)->value('local_language') : null;
        $federationCountry = $club?->association_id ? DB::table('associations')->where('id', $club->association_id)->value('country') : null;

        $person = new Person([
            'person_fifa_id' => $this->fifaId($official ? $official->person_fifa_id : $player->fifa_connect_id, 'PersonFIFAId', $issues),
            'international_first_name' => $first,
            'international_last_name' => $last,
            'local_first_name' => $official?->local_first_name ?: $first,
            'local_last_name' => ($official?->local_last_name ?: $last) ?: null,
            // Langue des noms locaux : celle de la personne, à défaut celle de la fédération.
            'local_language' => $this->code('ISO639-2Type', ($holder->local_language ?? null) ?: ($federationLanguage ?: config('licensing.fifa_export.local_language')), 'LocalLanguage', $issues),
            'local_country' => $this->country(($club->country_code ?? null) ?: (($club->country ?? null) ?: $federationCountry), 'LocalCountry (pays du club)', $issues),
            'gender' => $this->code('GenderType', $holder->gender, 'Gender', $issues),
            'nationality' => $this->country($holder->nationality, 'Nationality', $issues),
            'date_of_birth' => $holder->date_of_birth ? Carbon::parse($holder->date_of_birth) : null,
            'country_of_birth' => $this->country($holder->country_of_birth ?? null, 'CountryOfBirth', $issues, 'ISO3166-13CountryCode'),
            'place_of_birth' => ($holder->place_of_birth ?? null) ?: null,
        ]);
        if (!$person->date_of_birth) {
            $issues['DateOfBirth'] = 'absente';
        }
        if (!$person->place_of_birth) {
            $issues['PlaceOfBirth'] = 'absent';
        }
        if (!$person->local_last_name) {
            $issues['LocalLastName'] = 'absent';
        }

        $registration = new Registration([
            'person_fifa_id' => $person->person_fifa_id,
            'organisation_fifa_id' => $this->fifaId($club->fifa_connect_id ?? null, 'OrganisationFIFAId (club)', $issues),
            'registration_type' => $license->registration_type ?: Registration::TYPE_PLAYER,
            'status' => LicenseWorkflow::fifaStatus($license),
            'registration_valid_from' => $license->contract_start_date,
            'registration_valid_to' => $license->expiry_date,
            'level' => $license->level,
            'discipline' => $license->discipline,
            'registration_nature' => $license->registration_nature,
            'team_official_role' => $license->team_official_role,
            'organisation_official_role' => $license->organisation_official_role,
        ]);
        if (!$license->contract_start_date) {
            $issues['RegistrationValidFrom'] = 'absente';
        }
        if (!$license->registration_type) {
            $issues['Registration'] = 'licence antérieure au format FIFA Connect';
        } elseif ($license->registration_type === Registration::TYPE_PLAYER) {
            $this->code('RegistrationLevelType', $license->level, 'Level', $issues);
            $this->code('DisciplineType', $license->discipline, 'Discipline', $issues);
            $this->code('PlayerRegistrationNatureType', $license->registration_nature, 'RegistrationNature', $issues);
        } elseif ($license->registration_type === Registration::TYPE_TEAM_OFFICIAL) {
            $this->code('TeamOfficialRoleType', $license->team_official_role, 'TeamOfficialRole', $issues);
            $this->code('DisciplineType', $license->discipline, 'Discipline', $issues);
        } else {
            $this->code('OrganisationOfficialRoleType', $license->organisation_official_role, 'OrganisationOfficialRole', $issues);
        }

        $person->setRelation('localNames', new Collection);
        $person->setRelation('nationalIdentifiers', new Collection);
        $person->setRelation('certifications', new Collection);
        $person->setRelation('registrations', new Collection([$registration]));

        return ['person' => $person, 'issues' => $issues];
    }

    /** XML PersonLocal validé contre le XSD officiel ; null et écarts si les données sont incomplètes. */
    public function export(PlayerLicense $license): array
    {
        ['person' => $person, 'issues' => $issues] = $this->build($license);
        if ($issues !== []) {
            return ['xml' => null, 'issues' => $issues];
        }
        try {
            return ['xml' => $this->serializer->serialize($person, true), 'issues' => []];
        } catch (\Throwable $e) {
            return ['xml' => null, 'issues' => ['XSD' => mb_substr($e->getMessage(), 0, 300)]];
        }
    }

    private function fifaId(?string $value, string $field, array &$issues): ?string
    {
        $value = $value === null ? null : strtoupper(trim($value));
        if (!$value) {
            $issues[$field] = 'absent';

            return null;
        }
        $pattern = $this->catalog->pattern('FIFAIdentifier');
        if ($pattern && !preg_match('/^' . trim($pattern, '^$') . '$/', $value)) {
            $issues[$field] = "format invalide ({$value})";

            return null;
        }

        return $value;
    }

    private function code(string $type, ?string $value, string $field, array &$issues): ?string
    {
        if ($value === null || $value === '') {
            $issues[$field] = 'absent';

            return null;
        }
        if (!in_array($value, $this->catalog->enumValues($type), true)) {
            $issues[$field] = "« {$value} » hors de l'énumération officielle {$type}";

            return null;
        }

        return $value;
    }

    /** Code ISO 3166 : code officiel, ou nom (français ou anglais) de la référence config/iso_countries.php. */
    private function country(?string $value, string $field, array &$issues, string $type = 'ISO3166CountryCode'): ?string
    {
        if ($value === null || trim($value) === '') {
            $issues[$field] = 'absent';

            return null;
        }
        $code = $this->iso->code($value);
        if ($code && in_array($code, $this->catalog->enumValues($type), true)) {
            return $code;
        }
        $issues[$field] = "« {$value} » : pays absent du référentiel ISO 3166 officiel";

        return null;
    }
}
