<?php

namespace App\Services\FifaConnect;

use DOMDocument;
use DOMElement;
use RuntimeException;

class CanonicalXmlImporter
{
    private const NS = 'http://fifa.com/fc';

    public function __construct(
        private readonly XsdValidator $validator
    ) {
    }

    public function import(string $xml): array
    {
        $result = $this->validator->validate($xml);

        if (!$result['valid']) {
            throw new RuntimeException(
                'FIFA Connect XML import rejected: '
                . implode(' | ', $result['errors'])
            );
        }

        $document = new DOMDocument();

        if (!$document->loadXML($xml, LIBXML_NONET)) {
            throw new RuntimeException('Unable to parse validated FIFA XML.');
        }

        $root = $document->documentElement;

        if (!$root) {
            throw new RuntimeException('FIFA XML root is missing.');
        }

        $type = $root->localName;

        if ($type === 'PersonData') {
            $child = $this->singleEnvelopeChild($root, 'PersonLocal');
            return ['type' => 'PersonLocal', 'data' => $this->parsePerson($child)];
        }

        if ($type === 'CompetitionInternationalData') {
            $child = $this->singleEnvelopeChild(
                $root,
                'CompetitionInternational'
            );

            return [
                'type' => 'CompetitionInternational',
                'data' => $this->parseCompetition($child),
            ];
        }

        if ($type === 'MatchInternationalData') {
            $child = $this->singleEnvelopeChild(
                $root,
                'MatchInternational'
            );

            return [
                'type' => 'MatchInternational',
                'data' => $this->parseMatch($child),
            ];
        }

        $data = match ($type) {
            'PersonLocal' => $this->parsePerson($root),
            'OrganisationLocal' => $this->parseOrganisation($root),
            'FacilityLocal' => $this->parseFacility($root),
            'CompetitionInternational' =>
                $this->parseCompetition($root),
            'MatchInternational' =>
                $this->parseMatch($root),
            'Case' => $this->parseCase($root),
            'EventMessage' => $this->parseEventMessage($root),
            default => throw new RuntimeException(
                "Unsupported FIFA Connect root: {$type}."
            ),
        };

        return [
            'type' => $type,
            'data' => $data,
        ];
    }

    public function importMany(string $xml): array
    {
        $result = $this->validator->validate($xml);

        if (!$result['valid']) {
            throw new RuntimeException(
                'FIFA Connect XML import rejected: '
                . implode(' | ', $result['errors'])
            );
        }

        $document = new DOMDocument();

        if (!$document->loadXML($xml, LIBXML_NONET)) {
            throw new RuntimeException(
                'Unable to parse validated FIFA XML.'
            );
        }

        $root = $document->documentElement;

        if (!$root) {
            throw new RuntimeException(
                'FIFA XML root is missing.'
            );
        }

        if ($root->localName === 'PersonData') {
            return array_map(
                fn (DOMElement $child) => [
                    'type' => 'PersonLocal',
                    'data' => $this->parsePerson($child),
                ],
                $this->childrenNamed($root, 'PersonLocal')
            );
        }

        if ($root->localName === 'CompetitionInternationalData') {
            return array_map(
                fn (DOMElement $child) => [
                    'type' => 'CompetitionInternational',
                    'data' => $this->parseCompetition($child),
                ],
                $this->childrenNamed(
                    $root,
                    'CompetitionInternational'
                )
            );
        }

        if ($root->localName === 'MatchInternationalData') {
            return array_map(
                fn (DOMElement $child) => [
                    'type' => 'MatchInternational',
                    'data' => $this->parseMatch($child),
                ],
                $this->childrenNamed(
                    $root,
                    'MatchInternational'
                )
            );
        }

        return [$this->import($xml)];
    }

    private function parsePerson(DOMElement $root): array
    {
        $data = [
            'person_fifa_id' => $this->attr($root, 'PersonFIFAId'),
            'international_first_name' =>
                $this->attr($root, 'InternationalFirstName'),
            'international_last_name' =>
                $this->attr($root, 'InternationalLastName'),
            'popular_name' => $this->attr($root, 'PopularName'),
            'gender' => $this->attr($root, 'Gender'),
            'nationality' => $this->attr($root, 'Nationality'),
            'second_nationality' =>
                $this->attr($root, 'SecondNationality'),
            'date_of_birth' => $this->attr($root, 'DateOfBirth'),
            'country_of_birth' =>
                $this->attr($root, 'CountryOfBirth'),
            'region_of_birth' =>
                $this->attr($root, 'RegionOfBirth'),
            'place_of_birth' =>
                $this->attr($root, 'PlaceOfBirth'),
            'local_first_name' =>
                $this->attr($root, 'LocalFirstName'),
            'local_last_name' =>
                $this->attr($root, 'LocalLastName'),
            'local_birth_name' =>
                $this->attr($root, 'LocalBirthName'),
            'local_system_ma_id' =>
                $this->attr($root, 'LocalSystemMAId'),
            'local_language' =>
                $this->attr($root, 'LocalLanguage'),
            'local_country' =>
                $this->attr($root, 'LocalCountry'),
            'picture' => null,
            'local_names' => [],
            'national_identifiers' => [],
            'certifications' => [],
            'registrations' => [],
        ];

        foreach ($this->children($root) as $child) {
            switch ($child->localName) {
                case 'Photo':
                    $data['picture'] = $this->parsePicture($child);
                    break;

                case 'LocalPersonName':
                    $data['local_names'][] = [
                        'language' => $this->attr($child, 'Language'),
                        'title' => $this->attr($child, 'Title'),
                        'first_name' => $this->attr($child, 'FirstName'),
                        'last_name' => $this->attr($child, 'LastName'),
                        'birth_name' => $this->attr($child, 'BirthName'),
                        'popular_name' => $this->attr($child, 'PopularName'),
                    ];
                    break;

                case 'NationalIdentifier':
                    $data['national_identifiers'][] =
                        $this->parseNationalIdentifier($child);
                    break;

                case 'MatchOfficialCertificate':
                case 'TeamOfficialCertificate':
                    $data['certifications'][] = [
                        'certification_type' =>
                            $child->localName === 'MatchOfficialCertificate'
                                ? 'MatchOfficial'
                                : 'TeamOfficial',
                        'status' => $this->attr($child, 'Status'),
                        'certification_valid_from' =>
                            $this->attr($child, 'CertificationValidFrom'),
                        'certification_valid_to' =>
                            $this->attr($child, 'CertificationValidTo'),
                        'description' =>
                            $this->attr($child, 'Description'),
                        'certification_nature' =>
                            $this->attr($child, 'CertificationNature'),
                    ];
                    break;

                case 'PlayerRegistration':
                case 'TeamOfficialRegistration':
                case 'MatchOfficialRegistration':
                case 'OrganisationOfficialRegistration':
                    $data['registrations'][] =
                        $this->parseRegistration($child);
                    break;
            }
        }

        return $data;
    }

    private function parseRegistration(DOMElement $node): array
    {
        $type = match ($node->localName) {
            'PlayerRegistration' => 'Player',
            'TeamOfficialRegistration' => 'TeamOfficial',
            'MatchOfficialRegistration' => 'MatchOfficial',
            'OrganisationOfficialRegistration' => 'OrganisationOfficial',
        };

        return [
            'registration_type' => $type,
            'person_fifa_id' =>
                $this->attr($node, 'PersonFIFAId'),
            'status' => $this->attr($node, 'Status'),
            'organisation_fifa_id' =>
                $this->attr($node, 'OrganisationFIFAId'),
            'registration_valid_from' =>
                $this->attr($node, 'RegistrationValidFrom'),
            'registration_valid_to' =>
                $this->attr($node, 'RegistrationValidTo'),
            'level' => $this->attr($node, 'Level'),
            'discipline' => $this->attr($node, 'Discipline'),
            'registration_nature' =>
                $this->attr($node, 'RegistrationNature'),
            'club_training_category' =>
                $this->attr($node, 'ClubTrainingCategory'),
            'match_official_role' =>
                $this->attr($node, 'MatchOfficialRole'),
            'team_official_role' =>
                $this->attr($node, 'TeamOfficialRole'),
            'organisation_official_role' =>
                $this->attr($node, 'OrganisationOfficialRole'),
        ];
    }

    private function parseOrganisation(DOMElement $root): array
    {
        $data = [
            'organisation_fifa_id' =>
                $this->attr($root, 'OrganisationFIFAId'),
            'status' => $this->attr($root, 'Status'),
            'international_name' =>
                $this->attr($root, 'InternationalName'),
            'international_short_name' =>
                $this->attr($root, 'InternationalShortName'),
            'local_name' => $this->attr($root, 'LocalName'),
            'local_short_name' =>
                $this->attr($root, 'LocalShortName'),
            'local_system_ma_id' =>
                $this->attr($root, 'LocalSystemMAId'),
            'local_language' =>
                $this->attr($root, 'LocalLanguage'),
            'local_country' =>
                $this->attr($root, 'LocalCountry'),
            'organisation_nature' =>
                $this->attr($root, 'OrganisationNature'),
            'foundation_date' =>
                $this->attr($root, 'FoundationDate'),
            'dissolution_date' =>
                $this->attr($root, 'DissolutionDate'),
            'parent_organisation_fifa_id' =>
                $this->attr($root, 'ParentOrganisationFIFAId'),
            'picture' => null,
            'local_names' => [],
            'national_identifiers' => [],
            'supported_disciplines' => [],
            'address' => null,
            'web_address' => null,
            'email' => null,
            'phone' => null,
            'fax' => null,
        ];

        foreach ($this->children($root) as $child) {
            switch ($child->localName) {
                case 'Logo':
                    $data['picture'] = $this->parsePicture($child);
                    break;
                case 'NationalIdentifier':
                    $data['national_identifiers'][] =
                        $this->parseNationalIdentifier($child);
                    break;
                case 'LocalOrganisationName':
                    $data['local_names'][] = [
                        'language' => $this->attr($child, 'Language'),
                        'name' => $this->attr($child, 'Name'),
                        'short_name' => $this->attr($child, 'ShortName'),
                    ];
                    break;
                case 'SupportedDiscipline':
                    $data['supported_disciplines'][] = [
                        'discipline' => $this->attr($child, 'Discipline'),
                        'gender' => $this->attr($child, 'Gender'),
                    ];
                    break;
                case 'OfficialAddress':
                    $data['address'] = $this->parseAddress($child);
                    break;
                case 'WebAddress':
                case 'Email':
                case 'Phone':
                case 'Fax':
                    $data[$this->snake($child->localName)] =
                        trim($child->textContent);
                    break;
            }
        }

        return $data;
    }

    private function parseFacility(DOMElement $root): array
    {
        $data = [
            'facility_fifa_id' =>
                $this->attr($root, 'FacilityFIFAId'),
            'status' => $this->attr($root, 'Status'),
            'international_name' =>
                $this->attr($root, 'InternationalName'),
            'international_short_name' =>
                $this->attr($root, 'InternationalShortName'),
            'local_name' => $this->attr($root, 'LocalName'),
            'local_short_name' =>
                $this->attr($root, 'LocalShortName'),
            'local_system_ma_id' =>
                $this->attr($root, 'LocalSystemMAId'),
            'local_language' =>
                $this->attr($root, 'LocalLanguage'),
            'local_country' =>
                $this->attr($root, 'LocalCountry'),
            'organisation_fifa_id' =>
                $this->attr($root, 'OrganisationFIFAId'),
            'parent_facility_fifa_id' =>
                $this->attr($root, 'ParentFacilityFIFAId'),
            'picture' => null,
            'local_names' => [],
            'fields' => [],
            'address' => null,
            'web_address' => null,
            'email' => null,
            'phone' => null,
            'fax' => null,
        ];

        foreach ($this->children($root) as $child) {
            switch ($child->localName) {
                case 'Logo':
                    $data['picture'] = $this->parsePicture($child);
                    break;
                case 'LocalFacilityName':
                    $data['local_names'][] = [
                        'language' => $this->attr($child, 'Language'),
                        'name' => $this->attr($child, 'Name'),
                        'short_name' => $this->attr($child, 'ShortName'),
                    ];
                    break;
                case 'Field':
                    $data['fields'][] = [
                        'order_number' =>
                            $this->attr($child, 'OrderNumber'),
                        'discipline' =>
                            $this->attr($child, 'Discipline'),
                        'capacity' =>
                            $this->attr($child, 'Capacity'),
                        'ground_nature' =>
                            $this->attr($child, 'GroundNature'),
                        'length' => $this->attr($child, 'Length'),
                        'width' => $this->attr($child, 'Width'),
                        'latitude' => $this->attr($child, 'Latitude'),
                        'longitude' => $this->attr($child, 'Longitude'),
                    ];
                    break;
                case 'OfficialAddress':
                    $data['address'] = $this->parseAddress($child);
                    break;
                case 'WebAddress':
                case 'Email':
                case 'Phone':
                case 'Fax':
                    $data[$this->snake($child->localName)] =
                        trim($child->textContent);
                    break;
            }
        }

        return $data;
    }

    private function parseCompetition(DOMElement $root): array
    {
        $data = [
            'competition_fifa_id' =>
                $this->attr($root, 'CompetitionFIFAId'),
            'international_name' =>
                $this->attr($root, 'InternationalName'),
            'international_short_name' =>
                $this->attr($root, 'InternationalShortName'),
            'organisation_fifa_id' =>
                $this->attr($root, 'OrganisationFIFAId'),
            'season' => $this->attr($root, 'Season'),
            'status' => $this->attr($root, 'Status'),
            'order_number' => $this->attr($root, 'OrderNumber'),
            'date_from' => $this->attr($root, 'DateFrom'),
            'date_to' => $this->attr($root, 'DateTo'),
            'number_of_participants' =>
                $this->attr($root, 'NumberOfParticipants'),
            'organisation_international_name' =>
                $this->attr($root, 'OrganisationInternationalName'),
            'organisation_international_short_name' =>
                $this->attr($root, 'OrganisationInternationalShortName'),
            'nature' => null,
            'system' => null,
            'picture' => null,
            'matches' => [],
            'elements' => [],
            'teams' => [],
        ];

        foreach ($this->children($root) as $child) {
            switch ($child->localName) {
                case 'CompetitionNature':
                    $data['nature'] = [
                        'nature_fifa_id' =>
                            $this->attr($child, 'CompetitionFIFAId'),
                        'nature_international_name' =>
                            $this->attr($child, 'InternationalName'),
                        'nature_international_short_name' =>
                            $this->attr($child, 'InternationalShortName'),
                        'team_character' =>
                            $this->attr($child, 'TeamCharacter'),
                        'discipline' =>
                            $this->attr($child, 'Discipline'),
                        'age_category' =>
                            $this->attr($child, 'AgeCategory'),
                        'age_category_name' =>
                            $this->attr($child, 'AgeCategoryName'),
                        'gender' => $this->attr($child, 'Gender'),
                    ];
                    break;
                case 'System':
                    $data['system'] = [
                        'system_nature' =>
                            $this->attr($child, 'Nature'),
                        'system_multiplier' =>
                            $this->attr($child, 'Multiplier'),
                    ];
                    break;
                case 'Logo':
                    $data['picture'] = $this->parsePicture($child);
                    break;
                case 'Match':
                    $data['matches'][] =
                        $this->parseSimpleMatch($child);
                    break;
                case 'CompetitionElement':
                    $data['elements'][] =
                        $this->parseCompetition($child);
                    break;
                case 'CompetitionTeam':
                    $data['teams'][] =
                        $this->parseCompetitionTeam($child);
                    break;
            }
        }

        return array_merge(
            $data,
            $data['nature'] ?? [],
            $data['system'] ?? []
        );
    }

    private function parseSimpleMatch(DOMElement $node): array
    {
        $fields = [
            'MatchFIFAId', 'Status', 'DateTimeLocal', 'Matchday',
            'FacilityInternationalShortName', 'FacilityFIFAId',
            'HomeTeamInternationalShortName', 'HomeTeamFIFAId',
            'AwayTeamInternationalShortName', 'AwayTeamFIFAId',
            'HomeFinalResult', 'AwayFinalResult',
        ];

        $result = [];

        foreach ($fields as $field) {
            $result[$this->snake($field)] =
                $this->attr($node, $field);
        }

        return $result;
    }

    private function parseCompetitionTeam(DOMElement $node): array
    {
        $data = [
            'organisation_fifa_id' =>
                $this->attr($node, 'OrganisationFIFAId'),
            'team_fifa_id' =>
                $this->attr($node, 'TeamFIFAId'),
            'international_name' =>
                $this->attr($node, 'InternationalName'),
            'international_short_name' =>
                $this->attr($node, 'InternationalShortName'),
            'member_association' =>
                $this->attr($node, 'MemberAssociation'),
            'players' => [],
            'team_officials' => [],
            'ranking' => null,
            'picture' => null,
        ];

        foreach ($this->children($node) as $child) {
            if ($child->localName === 'Player'
                || $child->localName === 'TeamOfficial') {
                $role = [
                    'person_fifa_id' =>
                        $this->attr($child, 'PersonFIFAId'),
                    'international_first_name' =>
                        $this->attr($child, 'InternationalFirstName'),
                    'international_last_name' =>
                        $this->attr($child, 'InternationalLastName'),
                    'date_of_birth' =>
                        $this->attr($child, 'DateOfBirth'),
                    'member_association' =>
                        $this->attr($child, 'MemberAssociation'),
                    'picture' => null,
                ];

                foreach ($this->children($child) as $roleChild) {
                    if ($roleChild->localName === 'Photo') {
                        $role['picture'] =
                            $this->parsePicture($roleChild);
                    }
                }

                $key = $child->localName === 'Player'
                    ? 'players'
                    : 'team_officials';

                $data[$key][] = $role;
            } elseif ($child->localName === 'TeamRanking') {
                $ranking = [];
                foreach ([
                    'Position', 'MatchesPlayed', 'Wins', 'Draws',
                    'Losses', 'GoalsFor', 'GoalsAgainst',
                    'GoalDifference', 'Points', 'NegativePoints',
                ] as $attribute) {
                    $ranking[$this->snake($attribute)] =
                        $this->attr($child, $attribute);
                }
                $data['ranking'] = $ranking;
            } elseif ($child->localName === 'Logo') {
                $data['picture'] = $this->parsePicture($child);
            }
        }

        return $data;
    }

    private function parseMatch(DOMElement $root): array
    {
        $data = [
            'match_fifa_id' => $this->attr($root, 'MatchFIFAId'),
            'status' => $this->attr($root, 'Status'),
            'date_time_local' =>
                $this->attr($root, 'DateTimeLocal'),
            'matchday' => $this->attr($root, 'Matchday'),
            'attendance' => $this->attr($root, 'Attendance'),
            'phases' => [],
            'events' => [],
            'competition' => null,
            'facility' => null,
            'officials' => [],
            'teams' => [],
        ];

        foreach ($this->children($root) as $child) {
            switch ($child->localName) {
                case 'Phase':
                    $phase = [];
                    foreach ([
                        'Phase', 'StartDateTime', 'EndDateTime',
                        'RegularTime', 'StoppageTime',
                        'HomeScore', 'AwayScore',
                    ] as $attribute) {
                        $phase[$this->snake($attribute)] =
                            $this->attr($child, $attribute);
                    }
                    $data['phases'][] = $phase;
                    break;
                case 'MatchEvent':
                    $data['events'][] =
                        $this->parseMatchEvent($child, false);
                    break;
                case 'Competition':
                    $data['competition'] =
                        $this->parseMatchCompetition($child);
                    break;
                case 'Facility':
                    $data['facility'] =
                        $this->parseMatchFacility($child);
                    break;
                case 'MatchOfficial':
                    $data['officials'][] =
                        $this->parseOfficial($child);
                    break;
                case 'Team':
                    $data['teams'][] =
                        $this->parseMatchTeam($child);
                    break;
            }
        }

        return $data;
    }

    private function parseMatchCompetition(DOMElement $node): array
    {
        $data = [
            'competition_fifa_id' =>
                $this->attr($node, 'CompetitionFIFAId'),
            'international_name' =>
                $this->attr($node, 'InternationalName'),
            'international_short_name' =>
                $this->attr($node, 'InternationalShortName'),
            'organisation_international_name' =>
                $this->attr($node, 'OrganisationInternationalName'),
            'organisation_international_short_name' =>
                $this->attr(
                    $node,
                    'OrganisationInternationalShortName'
                ),
            'parent' => null,
            'picture' => null,
        ];

        foreach ($this->children($node) as $child) {
            if ($child->localName === 'ParentCompetition') {
                $data['parent'] =
                    $this->parseMatchCompetition($child);
            } elseif ($child->localName === 'Logo') {
                $data['picture'] = $this->parsePicture($child);
            }
        }

        return $data;
    }

    private function parseMatchFacility(DOMElement $node): array
    {
        $result = [];

        foreach ([
            'FacilityFIFAId', 'InternationalName',
            'InternationalShortName', 'FieldOrderNumber',
            'Capacity', 'Town', 'Country',
        ] as $attribute) {
            $result[$this->snake($attribute)] =
                $this->attr($node, $attribute);
        }

        return $result;
    }

    private function parseOfficial(DOMElement $node): array
    {
        $result = [];

        foreach ([
            'PersonFIFAId', 'Role', 'RoleDescription',
            'InternationalFirstName', 'InternationalLastName',
            'DateOfBirth', 'MemberAssociation',
        ] as $attribute) {
            $result[$this->snake($attribute)] =
                $this->attr($node, $attribute);
        }

        $result['picture'] = null;

        foreach ($this->children($node) as $child) {
            if ($child->localName === 'Photo') {
                $result['picture'] =
                    $this->parsePicture($child);
            }
        }

        return $result;
    }

    private function parseMatchTeam(DOMElement $node): array
    {
        $data = [
            'team_nature' => $this->attr($node, 'TeamNature'),
            'organisation_fifa_id' =>
                $this->attr($node, 'OrganisationFIFAId'),
            'team_fifa_id' => $this->attr($node, 'TeamFIFAId'),
            'international_name' =>
                $this->attr($node, 'InternationalName'),
            'international_short_name' =>
                $this->attr($node, 'InternationalShortName'),
            'international_code' =>
                $this->attr($node, 'InternationalCode'),
            'final_result' => $this->attr($node, 'FinalResult'),
            'member_association' =>
                $this->attr($node, 'MemberAssociation'),
            'players' => [],
            'team_officials' => [],
            'picture' => null,
        ];

        foreach ($this->children($node) as $child) {
            if ($child->localName === 'Player') {
                $data['players'][] =
                    $this->parseMatchPlayer($child);
            } elseif ($child->localName === 'TeamOfficial') {
                $data['team_officials'][] =
                    $this->parseOfficial($child);
            } elseif ($child->localName === 'Logo') {
                $data['picture'] = $this->parsePicture($child);
            }
        }

        return $data;
    }

    private function parseMatchPlayer(DOMElement $node): array
    {
        $result = [];

        foreach ([
            'PersonFIFAId', 'ShirtNumber', 'Captain',
            'Goalkeeper', 'StartingLineup', 'Played',
            'InternationalFirstName', 'InternationalLastName',
            'DateOfBirth', 'MemberAssociation',
        ] as $attribute) {
            $result[$this->snake($attribute)] =
                $this->attr($node, $attribute);
        }

        $result['picture'] = null;

        foreach ($this->children($node) as $child) {
            if ($child->localName === 'Photo') {
                $result['picture'] =
                    $this->parsePicture($child);
            }
        }

        return $result;
    }

    private function parseCase(DOMElement $root): array
    {
        $data = [
            'case_fifa_id' => $this->attr($root, 'CaseFIFAId'),
            'organisation_fifa_id' =>
                $this->attr($root, 'OrganisationFIFAId'),
            'case_date' => $this->attr($root, 'CaseDate'),
            'offender_nature' =>
                $this->attr($root, 'OffenderNature'),
            'offender_organisation_fifa_id' =>
                $this->attr($root, 'OffenderOrganisationFIFAId'),
            'offender_person_fifa_id' =>
                $this->attr($root, 'OffenderPersonFIFAId'),
            'offender_person_nature' =>
                $this->attr($root, 'OffenderPersonNature'),
            'description' => $this->attr($root, 'Description'),
            'status' => $this->attr($root, 'Status'),
            'competition_fifa_id' =>
                $this->attr($root, 'CompetitionFIFAId'),
            'match_fifa_id' =>
                $this->attr($root, 'MatchFIFAId'),
            'team_official_nature' => null,
            'match_official_nature' => null,
            'organisation_official_nature' => null,
            'match_event' => null,
            'sanctions' => [],
        ];

        foreach ($this->children($root) as $child) {
            switch ($child->localName) {
                case 'TeamOfficialNature':
                    $data['team_official_nature'] =
                        trim($child->textContent);
                    break;
                case 'MatchOfficialNature':
                    $data['match_official_nature'] =
                        trim($child->textContent);
                    break;
                case 'OrganisationOfficialNature':
                    $data['organisation_official_nature'] =
                        trim($child->textContent);
                    break;
                case 'MatchEvent':
                    $data['match_event'] =
                        $this->parseMatchEvent($child, false);
                    break;
                case 'Sanction':
                    $data['sanctions'][] =
                        $this->parseSanction($child);
                    break;
            }
        }

        return $data;
    }

    private function parseSanction(DOMElement $node): array
    {
        $data = [
            'status' => $this->attr($node, 'Status'),
            'value' => $this->attr($node, 'Value'),
            'measure' => $this->attr($node, 'Measure'),
            'currency' => $this->attr($node, 'Currency'),
            'value_served' => $this->attr($node, 'ValueServed'),
            'date_from' => $this->attr($node, 'DateFrom'),
            'date_to' => $this->attr($node, 'DateTo'),
            'person_sanction_nature' => null,
            'organisation_sanction_nature' => null,
        ];

        foreach ($this->children($node) as $child) {
            if ($child->localName === 'PersonSanctionNature') {
                $data['person_sanction_nature'] =
                    trim($child->textContent);
            } elseif (
                $child->localName === 'OrganisationSanctionNature'
            ) {
                $data['organisation_sanction_nature'] =
                    trim($child->textContent);
            }
        }

        return $data;
    }

    private function parseEventMessage(DOMElement $root): array
    {
        return [
            'message_nature' =>
                $this->attr($root, 'MessageNature'),
            'match_fifa_id' =>
                $this->attr($root, 'MatchFIFAId'),
            'export_date_time' =>
                $this->attr($root, 'ExportDateTime'),
            'event' => $this->parseMatchEvent($root, true),
            'scores' => array_map(
                fn (DOMElement $node) => $this->parseScore($node),
                $this->childrenNamed($root, 'Score')
            ),
        ];
    }

    private function parseScore(DOMElement $node): string|array
    {
        if ($this->children($node) || $node->hasAttributes()) {
            return [
                'value' => trim($node->textContent),
                'xml_fragment' => $node->C14N(),
            ];
        }

        return trim($node->textContent);
    }

    private function parseMatchEvent(
        DOMElement $node,
        bool $detail
    ): array {
        $result = [];

        foreach ([
            'MatchPhase', 'Minute', 'StoppageTime',
            'EventType', 'EventDetailType', 'PlayerFIFAId',
            'TeamOfficialFIFAId', 'PlayerFIFAId2', 'MatchTeam',
        ] as $attribute) {
            $result[$this->snake($attribute)] =
                $this->attr($node, $attribute);
        }

        if ($detail) {
            foreach ([
                'EventId',
                'PlayerShirtNumber',
                'PlayerShirtNumber2',
            ] as $attribute) {
                $result[$this->snake($attribute)] =
                    $this->attr($node, $attribute);
            }
        }

        return $result;
    }

    private function parseNationalIdentifier(
        DOMElement $node
    ): array {
        return [
            'identifier' => $this->attr($node, 'Identifier'),
            'nature' =>
                $this->attr($node, 'NationalIdentifierNature'),
            'country' => $this->attr($node, 'Country'),
            'date_from' => $this->attr($node, 'DateFrom'),
            'date_to' => $this->attr($node, 'DateTo'),
            'description' => $this->attr($node, 'Description'),
        ];
    }

    private function parseAddress(DOMElement $node): array
    {
        return [
            'country' => $this->attr($node, 'Country'),
            'region' => $this->attr($node, 'Region'),
            'postal_code' => $this->attr($node, 'PostalCode'),
            'town' => $this->attr($node, 'Town'),
            'address' => $this->attr($node, 'Address'),
        ];
    }

    private function parsePicture(DOMElement $wrapper): ?array
    {
        foreach ($this->children($wrapper) as $child) {
            if ($child->localName === 'PictureEmbedded') {
                return [
                    'storage_mode' => 'embedded',
                    'embedded_base64' => trim($child->textContent),
                    'picture_link' => null,
                    'mime_type' => $this->attributeByLocalName(
                        $child,
                        'contentType'
                    ),
                ];
            }

            if ($child->localName === 'PictureLink') {
                return [
                    'storage_mode' => 'link',
                    'embedded_base64' => null,
                    'picture_link' =>
                        $this->attr($child, 'PictureLink'),
                    'mime_type' =>
                        $this->attr($child, 'MimeType'),
                ];
            }
        }

        return null;
    }

    private function singleEnvelopeChild(
        DOMElement $root,
        string $expectedName
    ): DOMElement {
        $children = $this->childrenNamed(
            $root,
            $expectedName
        );

        if (count($children) !== 1) {
            throw new RuntimeException(
                "{$root->localName} import currently requires exactly one {$expectedName} element."
            );
        }

        return $children[0];
    }

    private function attr(
        DOMElement $node,
        string $name
    ): ?string {
        if (!$node->hasAttribute($name)) {
            return null;
        }

        $value = $node->getAttribute($name);

        return $value === '' ? null : $value;
    }

    private function attributeByLocalName(
        DOMElement $node,
        string $name
    ): ?string {
        foreach ($node->attributes as $attribute) {
            if ($attribute->localName === $name) {
                return $attribute->nodeValue;
            }
        }

        return null;
    }

    private function children(DOMElement $node): array
    {
        $children = [];

        foreach ($node->childNodes as $child) {
            if ($child instanceof DOMElement
                && $child->namespaceURI === self::NS) {
                $children[] = $child;
            }
        }

        return $children;
    }

    private function childrenNamed(
        DOMElement $node,
        string $name
    ): array {
        return array_values(array_filter(
            $this->children($node),
            fn (DOMElement $child) =>
                $child->localName === $name
        ));
    }

    private function snake(string $value): string
    {
        // Preserve FIFA as one semantic token rather than F_I_F_A and
        // separate numeric suffixes used by attributes such as FIFAId2.
        $value = str_replace('FIFA', 'Fifa', $value);
        $value = preg_replace(
            '/([A-Za-z])([0-9]+)/',
            '$1_$2',
            $value
        );

        return strtolower(
            preg_replace('/(?<!^)[A-Z]/', '_$0', $value)
        );
    }
}
