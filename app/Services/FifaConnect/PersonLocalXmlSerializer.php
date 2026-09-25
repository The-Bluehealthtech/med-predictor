<?php

namespace App\Services\FifaConnect;

use App\Models\FifaConnect\Person;
use App\Models\FifaConnect\Registration;
use DOMDocument;
use DOMElement;
use RuntimeException;

class PersonLocalXmlSerializer
{
    private const NS = 'http://fifa.com/fc';
    private const XSI = 'http://www.w3.org/2001/XMLSchema-instance';

    public function __construct(
        private readonly SchemaCatalog $catalog,
        private readonly XsdValidator $validator
    ) {
    }

    public function serialize(Person $person, bool $validate = true): string
    {
        $person->loadMissing([
            'localNames',
            'nationalIdentifiers',
            'registrations',
            'certifications',
        ]);

        $this->assertPerson($person);

        $document = new DOMDocument('1.0', 'UTF-8');
        $document->formatOutput = true;

        $root = $document->createElementNS(self::NS, 'PersonLocal');
        $root->setAttributeNS(
            self::XSI,
            'xsi:schemaLocation',
            self::NS . ' scenarios.xsd'
        );

        $this->setOptional($root, 'InternationalFirstName', $person->international_first_name);
        $this->setOptional($root, 'InternationalLastName', $person->international_last_name);
        $this->setOptional($root, 'PopularName', $person->popular_name);
        $root->setAttribute('Gender', $person->gender);
        $root->setAttribute('Nationality', $person->nationality);
        $this->setOptional($root, 'SecondNationality', $person->second_nationality);
        $root->setAttribute('DateOfBirth', $person->date_of_birth->format('Y-m-d'));
        $root->setAttribute('CountryOfBirth', $person->country_of_birth);
        $this->setOptional($root, 'RegionOfBirth', $person->region_of_birth);
        $root->setAttribute('PlaceOfBirth', $person->place_of_birth);
        $root->setAttribute('PersonFIFAId', $person->person_fifa_id);

        $this->setOptional($root, 'LocalFirstName', $person->local_first_name);
        $root->setAttribute('LocalLastName', $person->local_last_name);
        $this->setOptional($root, 'LocalBirthName', $person->local_birth_name);
        $this->setOptional($root, 'LocalSystemMAId', $person->local_system_ma_id);
        $root->setAttribute('LocalLanguage', $person->local_language);
        $root->setAttribute('LocalCountry', $person->local_country);

        PictureXml::append(
            $document,
            $root,
            $person->picture,
            'Photo'
        );

        foreach ($person->localNames as $localName) {
            $this->catalog->assertEnum(
                'ISO639-2Type',
                $localName->language
            );

            $node = $document->createElementNS(self::NS, 'LocalPersonName');
            $node->setAttribute('Language', $localName->language);
            $this->setOptional($node, 'Title', $localName->title);
            $this->setOptional($node, 'FirstName', $localName->first_name);
            $node->setAttribute('LastName', $localName->last_name);
            $this->setOptional($node, 'BirthName', $localName->birth_name);
            $this->setOptional($node, 'PopularName', $localName->popular_name);
            $root->appendChild($node);
        }

        foreach ($person->nationalIdentifiers as $identifier) {
            $this->catalog->assertEnum(
                'NationalIdentifierNatureType',
                $identifier->nature
            );
            $this->catalog->assertEnum(
                'ISO3166CountryCode',
                $identifier->country
            );

            $node = $document->createElementNS(self::NS, 'NationalIdentifier');
            $node->setAttribute('Identifier', $identifier->identifier);
            $node->setAttribute('NationalIdentifierNature', $identifier->nature);
            $node->setAttribute('Country', $identifier->country);
            $this->setOptional(
                $node,
                'DateFrom',
                $identifier->date_from?->format('Y-m-d')
            );
            $this->setOptional(
                $node,
                'DateTo',
                $identifier->date_to?->format('Y-m-d')
            );
            $this->setOptional($node, 'Description', $identifier->description);
            $root->appendChild($node);
        }

        foreach ([
            'MatchOfficial' => 'MatchOfficialCertificate',
            'TeamOfficial' => 'TeamOfficialCertificate',
        ] as $certificationType => $elementName) {
            foreach (
                $person->certifications
                    ->where('certification_type', $certificationType)
                as $certification
            ) {
                foreach ([
                    'status',
                    'certification_valid_from',
                    'certification_nature',
                ] as $field) {
                    if ($certification->{$field} === null
                        || $certification->{$field} === '') {
                        throw new RuntimeException(
                            "{$elementName} requires {$field}."
                        );
                    }
                }

                $this->catalog->assertEnum(
                    'SimpleStatusType',
                    $certification->status
                );
                $this->catalog->assertEnum(
                    $certificationType === 'MatchOfficial'
                        ? 'MatchOfficialCertificationNatureType'
                        : 'TeamOfficialCertificationNatureType',
                    $certification->certification_nature
                );

                $node = $document->createElementNS(
                    self::NS,
                    $elementName
                );
                $node->setAttribute(
                    'Status',
                    $certification->status
                );
                $node->setAttribute(
                    'CertificationValidFrom',
                    $certification->certification_valid_from
                        ->format('Y-m-d')
                );
                $this->setOptional(
                    $node,
                    'CertificationValidTo',
                    $certification->certification_valid_to
                        ?->format('Y-m-d')
                );
                $this->setOptional(
                    $node,
                    'Description',
                    $certification->description
                );
                $node->setAttribute(
                    'CertificationNature',
                    $certification->certification_nature
                );
                $root->appendChild($node);
            }
        }

        foreach ([
            Registration::TYPE_PLAYER,
            Registration::TYPE_TEAM_OFFICIAL,
            Registration::TYPE_MATCH_OFFICIAL,
            Registration::TYPE_ORGANISATION_OFFICIAL,
        ] as $registrationType) {
            foreach (
                $person->registrations
                    ->where('registration_type', $registrationType)
                as $registration
            ) {
                $root->appendChild(
                    $this->registrationNode(
                        $document,
                        $registration,
                        $person
                    )
                );
            }
        }

        $document->appendChild($root);
        $xml = $document->saveXML();

        if ($validate) {
            $this->validator->assertValid($xml, 'scenarios.xsd');
        }

        return $xml;
    }

    private function assertPerson(Person $person): void
    {
        foreach ([
            'person_fifa_id',
            'gender',
            'nationality',
            'date_of_birth',
            'country_of_birth',
            'place_of_birth',
            'local_last_name',
            'local_language',
            'local_country',
        ] as $field) {
            if ($person->{$field} === null || $person->{$field} === '') {
                throw new RuntimeException(
                    "PersonLocal cannot be exported: {$field} is required."
                );
            }
        }

        $this->catalog->assertFifaIdentifier($person->person_fifa_id);
        $this->catalog->assertEnum('GenderType', $person->gender);
        $this->catalog->assertEnum(
            'ISO3166CountryCode',
            $person->nationality
        );
        if ($person->second_nationality !== null) {
            $this->catalog->assertEnum(
                'ISO3166CountryCode',
                $person->second_nationality
            );
        }
        $this->catalog->assertEnum(
            'ISO3166-13CountryCode',
            $person->country_of_birth
        );
        $this->catalog->assertEnum(
            'ISO639-2Type',
            $person->local_language
        );
        $this->catalog->assertEnum(
            'ISO3166CountryCode',
            $person->local_country
        );
    }

    private function registrationNode(
        DOMDocument $document,
        Registration $registration,
        Person $person
    ): DOMElement {
        $element = match ($registration->registration_type) {
            Registration::TYPE_PLAYER => 'PlayerRegistration',
            Registration::TYPE_TEAM_OFFICIAL => 'TeamOfficialRegistration',
            Registration::TYPE_MATCH_OFFICIAL => 'MatchOfficialRegistration',
            Registration::TYPE_ORGANISATION_OFFICIAL => 'OrganisationOfficialRegistration',
            default => throw new RuntimeException(
                "Unsupported registration type: {$registration->registration_type}."
            ),
        };

        foreach ([
            'person_fifa_id',
            'organisation_fifa_id',
            'status',
            'registration_valid_from',
        ] as $field) {
            if ($registration->{$field} === null
                || $registration->{$field} === '') {
                throw new RuntimeException(
                    "Registration requires {$field}."
                );
            }
        }

        $this->catalog->assertFifaIdentifier($registration->person_fifa_id);
        $this->catalog->assertFifaIdentifier($registration->organisation_fifa_id);
        $this->catalog->assertEnum('SimpleStatusType', $registration->status);

        if ($registration->person_fifa_id !== $person->person_fifa_id) {
            throw new RuntimeException(
                'Registration PersonFIFAId does not match its Person.'
            );
        }

        $node = $document->createElementNS(self::NS, $element);
        $node->setAttribute('PersonFIFAId', $registration->person_fifa_id);
        $node->setAttribute('Status', $registration->status);
        $node->setAttribute('OrganisationFIFAId', $registration->organisation_fifa_id);
        $node->setAttribute(
            'RegistrationValidFrom',
            $registration->registration_valid_from->format('Y-m-d')
        );
        $this->setOptional(
            $node,
            'RegistrationValidTo',
            $registration->registration_valid_to?->format('Y-m-d')
        );

        if ($registration->registration_type === Registration::TYPE_PLAYER) {
            $this->catalog->assertEnum('RegistrationLevelType', $registration->level);
            $this->catalog->assertEnum('DisciplineType', $registration->discipline);
            $this->catalog->assertEnum(
                'PlayerRegistrationNatureType',
                $registration->registration_nature
            );

            $node->setAttribute('Level', $registration->level);
            $node->setAttribute('Discipline', $registration->discipline);
            $node->setAttribute('RegistrationNature', $registration->registration_nature);
            if ($registration->club_training_category !== null) {
                $this->catalog->assertEnum(
                    'ClubTrainingCategoryType',
                    $registration->club_training_category
                );
            }
            $this->setOptional(
                $node,
                'ClubTrainingCategory',
                $registration->club_training_category
            );
        } elseif ($registration->registration_type === Registration::TYPE_TEAM_OFFICIAL) {
            $this->catalog->assertEnum('TeamOfficialRoleType', $registration->team_official_role);
            $this->catalog->assertEnum('DisciplineType', $registration->discipline);
            $node->setAttribute('TeamOfficialRole', $registration->team_official_role);
            $node->setAttribute('Discipline', $registration->discipline);
        } elseif ($registration->registration_type === Registration::TYPE_MATCH_OFFICIAL) {
            $this->catalog->assertEnum('MatchOfficialRoleType', $registration->match_official_role);
            $this->catalog->assertEnum('DisciplineType', $registration->discipline);
            $node->setAttribute('MatchOfficialRole', $registration->match_official_role);
            $node->setAttribute('Discipline', $registration->discipline);
        } else {
            $this->catalog->assertEnum(
                'OrganisationOfficialRoleType',
                $registration->organisation_official_role
            );
            $node->setAttribute(
                'OrganisationOfficialRole',
                $registration->organisation_official_role
            );
        }

        return $node;
    }

    private function setOptional(
        DOMElement $element,
        string $name,
        mixed $value
    ): void {
        if ($value !== null && $value !== '') {
            $element->setAttribute($name, (string) $value);
        }
    }
}
