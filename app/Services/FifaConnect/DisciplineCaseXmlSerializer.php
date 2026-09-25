<?php

namespace App\Services\FifaConnect;

use App\Models\FifaConnect\DisciplineCase;
use App\Models\FifaConnect\Sanction;
use DOMDocument;
use DOMElement;
use RuntimeException;

class DisciplineCaseXmlSerializer
{
    private const NS = 'http://fifa.com/fc';
    private const XSI = 'http://www.w3.org/2001/XMLSchema-instance';

    public function __construct(
        private readonly SchemaCatalog $catalog,
        private readonly XsdValidator $validator
    ) {
    }

    public function serialize(
        DisciplineCase $case,
        bool $validate = true
    ): string {
        $this->assertCase($case);

        if ($case->exists) {
        $case->loadMissing([
            'caseMatchEvent',
            'sanctions',
        ]);
        } else {
            $case->loadMissing(['sanctions']);
        }

        $document = new DOMDocument('1.0', 'UTF-8');
        $document->formatOutput = true;

        $root = $document->createElementNS(self::NS, 'Case');
        $root->setAttributeNS(
            self::XSI,
            'xsi:schemaLocation',
            self::NS . ' discipline.xsd'
        );

        $root->setAttribute('CaseFIFAId', $case->case_fifa_id);
        $root->setAttribute(
            'OrganisationFIFAId',
            $case->organisation_fifa_id
        );
        $root->setAttribute(
            'CaseDate',
            $case->case_date->format('Y-m-d')
        );
        $root->setAttribute(
            'OffenderNature',
            $case->offender_nature
        );
        $this->setOptional(
            $root,
            'OffenderOrganisationFIFAId',
            $case->offender_organisation_fifa_id
        );
        $this->setOptional(
            $root,
            'OffenderPersonFIFAId',
            $case->offender_person_fifa_id
        );
        $this->setOptional(
            $root,
            'OffenderPersonNature',
            $case->offender_person_nature
        );
        $root->setAttribute('Description', $case->description);
        $root->setAttribute('Status', $case->status);
        $this->setOptional(
            $root,
            'CompetitionFIFAId',
            $case->competition_fifa_id
        );
        $this->setOptional(
            $root,
            'MatchFIFAId',
            $case->match_fifa_id
        );

        $roles = array_filter([
            'TeamOfficialNature' => $case->team_official_nature,
            'MatchOfficialNature' => $case->match_official_nature,
            'OrganisationOfficialNature' =>
                $case->organisation_official_nature,
        ], fn ($value) => $value !== null && $value !== '');

        if (count($roles) > 1) {
            throw new RuntimeException(
                'Discipline Case permits at most one offender role nature.'
            );
        }

        foreach ($roles as $name => $value) {
            $type = match ($name) {
                'TeamOfficialNature' =>
                    'TeamOfficialRoleNatureType',
                'MatchOfficialNature' =>
                    'MatchOfficialRoleNatureType',
                'OrganisationOfficialNature' =>
                    'OrganisationOfficialRoleType',
            };

            $this->catalog->assertEnum($type, $value);

            $root->appendChild(
                $document->createElementNS(
                    self::NS,
                    $name,
                    htmlspecialchars($value, ENT_XML1)
                )
            );
        }

        $matchEvent = $case->relationLoaded('matchEvent')
            ? $case->getRelation('matchEvent')
            : $case->caseMatchEvent;

        if ($matchEvent) {
            $root->appendChild(
                $this->matchEventNode(
                    $document,
                    $matchEvent
                )
            );
        }

        foreach ($case->sanctions as $sanction) {
            $root->appendChild(
                $this->sanctionNode(
                    $document,
                    $sanction,
                    $case->offender_nature
                )
            );
        }

        $document->appendChild($root);
        $xml = $document->saveXML();

        if ($validate) {
            $this->validator->assertValid(
                $xml,
                'discipline.xsd'
            );
        }

        return $xml;
    }

    private function matchEventNode(
        DOMDocument $document,
        object $event
    ): DOMElement {
        foreach ([
            'match_phase',
            'minute',
            'event_type',
        ] as $field) {
            if ($event->{$field} === null || $event->{$field} === '') {
                throw new RuntimeException(
                    "Discipline MatchEvent requires {$field}."
                );
            }
        }

        $this->catalog->assertEnum(
            'MatchPhaseNatureType',
            $event->match_phase
        );
        $this->catalog->assertEnum(
            'MatchEventNatureType',
            $event->event_type
        );

        if ($event->event_detail_type !== null) {
            $this->catalog->assertEnum(
                'MatchEventDetailNatureType',
                $event->event_detail_type
            );
        }

        if ($event->match_team !== null) {
            $this->catalog->assertEnum(
                'MatchTeamNatureType',
                $event->match_team
            );
        }

        foreach ([
            $event->player_fifa_id,
            $event->team_official_fifa_id,
            $event->player_fifa_id_2,
        ] as $id) {
            if ($id) {
                $this->catalog->assertFifaIdentifier($id);
            }
        }

        $node = $document->createElementNS(
            self::NS,
            'MatchEvent'
        );
        $node->setAttribute(
            'MatchPhase',
            $event->match_phase
        );
        $node->setAttribute(
            'Minute',
            (string) $event->minute
        );
        $this->setOptional(
            $node,
            'StoppageTime',
            $event->stoppage_time
        );
        $node->setAttribute(
            'EventType',
            $event->event_type
        );
        $this->setOptional(
            $node,
            'EventDetailType',
            $event->event_detail_type
        );
        $this->setOptional(
            $node,
            'PlayerFIFAId',
            $event->player_fifa_id
        );
        $this->setOptional(
            $node,
            'TeamOfficialFIFAId',
            $event->team_official_fifa_id
        );
        $this->setOptional(
            $node,
            'PlayerFIFAId2',
            $event->player_fifa_id_2
        );
        $this->setOptional(
            $node,
            'MatchTeam',
            $event->match_team
        );

        return $node;
    }

    private function sanctionNode(
        DOMDocument $document,
        Sanction $sanction,
        string $offenderNature
    ): DOMElement {
        $this->catalog->assertEnum(
            'SimpleStatusType',
            $sanction->status
        );

        $hasPerson = $sanction->person_sanction_nature !== null
            && $sanction->person_sanction_nature !== '';
        $hasOrganisation =
            $sanction->organisation_sanction_nature !== null
            && $sanction->organisation_sanction_nature !== '';

        if ($hasPerson === $hasOrganisation) {
            throw new RuntimeException(
                'Sanction must define exactly one person or organisation sanction nature.'
            );
        }

        if ($offenderNature === 'Person' && !$hasPerson) {
            throw new RuntimeException(
                'Person offender requires PersonSanctionNature.'
            );
        }

        if ($offenderNature === 'Organisation' && !$hasOrganisation) {
            throw new RuntimeException(
                'Organisation offender requires OrganisationSanctionNature.'
            );
        }

        $node = $document->createElementNS(
            self::NS,
            'Sanction'
        );
        $node->setAttribute('Status', $sanction->status);
        $this->setOptional($node, 'Value', $sanction->value);
        $this->setOptional($node, 'Measure', $sanction->measure);
        $this->setOptional($node, 'Currency', $sanction->currency);
        $this->setOptional(
            $node,
            'ValueServed',
            $sanction->value_served
        );
        $this->setOptional(
            $node,
            'DateFrom',
            $sanction->date_from?->format('Y-m-d')
        );
        $this->setOptional(
            $node,
            'DateTo',
            $sanction->date_to?->format('Y-m-d')
        );

        if ($sanction->measure !== null) {
            $this->catalog->assertEnum(
                'SanctionMeasureType',
                $sanction->measure
            );
        }

        if ($hasPerson) {
            $this->catalog->assertEnum(
                'PersonSanctionNatureType',
                $sanction->person_sanction_nature
            );
            $node->appendChild(
                $document->createElementNS(
                    self::NS,
                    'PersonSanctionNature',
                    htmlspecialchars(
                        $sanction->person_sanction_nature,
                        ENT_XML1
                    )
                )
            );
        } else {
            $this->catalog->assertEnum(
                'OrganisationSanctionNatureType',
                $sanction->organisation_sanction_nature
            );
            $node->appendChild(
                $document->createElementNS(
                    self::NS,
                    'OrganisationSanctionNature',
                    htmlspecialchars(
                        $sanction->organisation_sanction_nature,
                        ENT_XML1
                    )
                )
            );
        }

        return $node;
    }

    private function assertCase(DisciplineCase $case): void
    {
        foreach ([
            'case_fifa_id',
            'organisation_fifa_id',
            'case_date',
            'offender_nature',
            'description',
            'status',
        ] as $field) {
            if ($case->{$field} === null || $case->{$field} === '') {
                throw new RuntimeException(
                    "Discipline Case cannot be exported: {$field} is required."
                );
            }
        }

        $this->catalog->assertFifaIdentifier($case->case_fifa_id);
        $this->catalog->assertFifaIdentifier(
            $case->organisation_fifa_id
        );
        $this->catalog->assertEnum(
            'OffenderNatureType',
            $case->offender_nature
        );
        $this->catalog->assertEnum(
            'SimpleStatusType',
            $case->status
        );

        foreach ([
            $case->offender_organisation_fifa_id,
            $case->offender_person_fifa_id,
            $case->competition_fifa_id,
            $case->match_fifa_id,
        ] as $id) {
            if ($id) {
                $this->catalog->assertFifaIdentifier($id);
            }
        }

        if ($case->offender_nature === 'Person'
            && !$case->offender_person_fifa_id) {
            throw new RuntimeException(
                'Person offender requires OffenderPersonFIFAId.'
            );
        }

        if ($case->offender_nature === 'Organisation'
            && !$case->offender_organisation_fifa_id) {
            throw new RuntimeException(
                'Organisation offender requires OffenderOrganisationFIFAId.'
            );
        }
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
