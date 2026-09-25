<?php

namespace App\Services\FifaConnect;

use App\Models\FifaConnect\Competition;
use App\Models\FifaConnect\Facility;
use App\Models\FifaConnect\MatchCompetitionContext;
use App\Models\FifaConnect\MatchFacilityContext;
use App\Models\FifaConnect\MatchRecord;
use App\Models\FifaConnect\MatchTeam;
use DOMDocument;
use DOMElement;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class MatchInternationalXmlSerializer
{
    private const NS = 'http://fifa.com/fc';
    private const XSI = 'http://www.w3.org/2001/XMLSchema-instance';

    public function __construct(
        private readonly SchemaCatalog $catalog,
        private readonly XsdValidator $validator
    ) {
    }

    public function serialize(
        MatchRecord|Collection|array $matches,
        bool $validate = true
    ): string {
        $items = $matches instanceof MatchRecord
            ? collect([$matches])
            : collect($matches);

        $document = new DOMDocument('1.0', 'UTF-8');
        $document->formatOutput = true;

        $root = $document->createElementNS(
            self::NS,
            'MatchInternationalData'
        );
        $root->setAttributeNS(
            self::XSI,
            'xsi:schemaLocation',
            self::NS . ' scenarios.xsd'
        );
        $root->setAttribute('ExportDate', now()->toIso8601String());

        foreach ($items as $match) {
            if (!$match instanceof MatchRecord) {
                throw new RuntimeException(
                    'MatchInternationalData accepts canonical MatchRecord instances only.'
                );
            }

            $root->appendChild(
                $this->matchNode($document, $match)
            );
        }

        $document->appendChild($root);
        $xml = $document->saveXML();

        if ($validate) {
            $this->validator->assertValid($xml, 'scenarios.xsd');
        }

        return $xml;
    }

    private function matchNode(
        DOMDocument $document,
        MatchRecord $match
    ): DOMElement {
        $match->loadMissing([
            'competition.parent',
            'facility',
            'phases',
            'events',
            'officials',
            'teams.players',
            'teams.officials',
        ]);

        $this->assertMatch($match);

        $node = $document->createElementNS(
            self::NS,
            'MatchInternational'
        );
        $node->setAttribute('MatchFIFAId', $match->match_fifa_id);
        $node->setAttribute('Status', $match->status);
        $this->setOptional(
            $node,
            'DateTimeLocal',
            $match->date_time_local?->toIso8601String()
        );
        $this->setOptional($node, 'Matchday', $match->matchday);
        $this->setOptional($node, 'Attendance', $match->attendance);

        foreach ($match->phases as $phase) {
            $this->catalog->assertEnum(
                'MatchPhaseNatureType',
                $phase->phase
            );

            $phaseNode = $document->createElementNS(
                self::NS,
                'Phase'
            );
            $phaseNode->setAttribute('Phase', $phase->phase);
            $this->setOptional(
                $phaseNode,
                'StartDateTime',
                $phase->start_date_time?->toIso8601String()
            );
            $this->setOptional(
                $phaseNode,
                'EndDateTime',
                $phase->end_date_time?->toIso8601String()
            );
            $this->setOptional(
                $phaseNode,
                'RegularTime',
                $phase->regular_time
            );
            $this->setOptional(
                $phaseNode,
                'StoppageTime',
                $phase->stoppage_time
            );
            $phaseNode->setAttribute(
                'HomeScore',
                (string) $phase->home_score
            );
            $phaseNode->setAttribute(
                'AwayScore',
                (string) $phase->away_score
            );

            $node->appendChild($phaseNode);
        }

        foreach ($match->events as $event) {
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

            foreach ([
                $event->player_fifa_id,
                $event->team_official_fifa_id,
                $event->player_fifa_id_2,
            ] as $id) {
                if ($id) {
                    $this->catalog->assertFifaIdentifier($id);
                }
            }

            if ($event->match_team !== null) {
                $this->catalog->assertEnum(
                    'MatchTeamNatureType',
                    $event->match_team
                );
            }

            $eventNode = $document->createElementNS(
                self::NS,
                'MatchEvent'
            );
            $eventNode->setAttribute(
                'MatchPhase',
                $event->match_phase
            );
            $eventNode->setAttribute(
                'Minute',
                (string) $event->minute
            );
            $this->setOptional(
                $eventNode,
                'StoppageTime',
                $event->stoppage_time
            );
            $eventNode->setAttribute(
                'EventType',
                $event->event_type
            );
            $this->setOptional(
                $eventNode,
                'EventDetailType',
                $event->event_detail_type
            );
            $this->setOptional(
                $eventNode,
                'PlayerFIFAId',
                $event->player_fifa_id
            );
            $this->setOptional(
                $eventNode,
                'TeamOfficialFIFAId',
                $event->team_official_fifa_id
            );
            $this->setOptional(
                $eventNode,
                'PlayerFIFAId2',
                $event->player_fifa_id_2
            );
            $this->setOptional(
                $eventNode,
                'MatchTeam',
                $event->match_team
            );

            $node->appendChild($eventNode);
        }

        if ($match->competitionContext instanceof MatchCompetitionContext) {
            $node->appendChild(
                $this->competitionContextNode(
                    $document,
                    $match->competitionContext
                )
            );
        } else {
            $node->appendChild(
                $this->competitionNode(
                    $document,
                    $match->competition
                )
            );
        }

        if ($match->facility_fifa_id !== null) {
            if ($match->facilityContext instanceof MatchFacilityContext) {
                $node->appendChild(
                    $this->facilityContextNode(
                        $document,
                        $match->facilityContext
                    )
                );
            } elseif ($match->facility instanceof Facility) {
                $node->appendChild(
                    $this->facilityNode(
                        $document,
                        $match->facility
                    )
                );
            } else {
                throw new RuntimeException(
                    'Match facility FIFA ID has no canonical Facility or embedded context.'
                );
            }
        }

        foreach ($match->officials as $official) {
            $this->catalog->assertFifaIdentifier(
                $official->person_fifa_id
            );
            $this->catalog->assertEnum(
                'MatchOfficialRoleNatureType',
                $official->role
            );

            $officialNode = $document->createElementNS(
                self::NS,
                'MatchOfficial'
            );
            $officialNode->setAttribute(
                'PersonFIFAId',
                $official->person_fifa_id
            );
            $officialNode->setAttribute('Role', $official->role);
            $this->setOptional(
                $officialNode,
                'RoleDescription',
                $official->role_description
            );
            $this->setOptional(
                $officialNode,
                'InternationalFirstName',
                $official->international_first_name
            );
            $this->setOptional(
                $officialNode,
                'InternationalLastName',
                $official->international_last_name
            );
            $this->setOptional(
                $officialNode,
                'DateOfBirth',
                $official->date_of_birth?->format('Y-m-d')
            );
            $this->setOptional(
                $officialNode,
                'MemberAssociation',
                $official->member_association
            );

            $node->appendChild($officialNode);
        }

        foreach ($match->teams as $team) {
            $node->appendChild(
                $this->teamNode($document, $team)
            );
        }

        return $node;
    }

    private function competitionContextNode(
        DOMDocument $document,
        MatchCompetitionContext $context,
        string $elementName = 'Competition'
    ): DOMElement {
        $this->catalog->assertFifaIdentifier(
            $context->competition_fifa_id
        );

        foreach ([
            'international_name',
            'organisation_international_short_name',
        ] as $field) {
            if (!$context->{$field}) {
                throw new RuntimeException(
                    "Match competition context requires {$field}."
                );
            }
        }

        $node = $document->createElementNS(
            self::NS,
            $elementName
        );
        $node->setAttribute(
            'CompetitionFIFAId',
            $context->competition_fifa_id
        );
        $node->setAttribute(
            'InternationalName',
            $context->international_name
        );
        $this->setOptional(
            $node,
            'InternationalShortName',
            $context->international_short_name
        );
        $this->setOptional(
            $node,
            'OrganisationInternationalName',
            $context->organisation_international_name
        );
        $node->setAttribute(
            'OrganisationInternationalShortName',
            $context->organisation_international_short_name
        );

        if ($context->parent instanceof MatchCompetitionContext) {
            $node->appendChild(
                $this->competitionContextNode(
                    $document,
                    $context->parent,
                    'ParentCompetition'
                )
            );
        }

        PictureXml::append(
            $document,
            $node,
            $context->picture,
            'Logo'
        );

        return $node;
    }

    private function facilityContextNode(
        DOMDocument $document,
        MatchFacilityContext $context
    ): DOMElement {
        $this->catalog->assertFifaIdentifier(
            $context->facility_fifa_id
        );

        foreach (['town', 'country'] as $field) {
            if (!$context->{$field}) {
                throw new RuntimeException(
                    "Match facility context requires {$field}."
                );
            }
        }

        $this->catalog->assertEnum(
            'ISO3166CountryCode',
            $context->country
        );

        $node = $document->createElementNS(
            self::NS,
            'Facility'
        );
        $node->setAttribute(
            'FacilityFIFAId',
            $context->facility_fifa_id
        );
        $this->setOptional(
            $node,
            'InternationalName',
            $context->international_name
        );
        $this->setOptional(
            $node,
            'InternationalShortName',
            $context->international_short_name
        );
        $this->setOptional(
            $node,
            'FieldOrderNumber',
            $context->field_order_number
        );
        $this->setOptional(
            $node,
            'Capacity',
            $context->capacity
        );
        $node->setAttribute('Town', $context->town);
        $node->setAttribute('Country', $context->country);

        return $node;
    }

    private function competitionNode(
        DOMDocument $document,
        Competition $competition
    ): DOMElement {
        $this->catalog->assertFifaIdentifier(
            $competition->competition_fifa_id
        );

        $node = $document->createElementNS(
            self::NS,
            'Competition'
        );
        $node->setAttribute(
            'CompetitionFIFAId',
            $competition->competition_fifa_id
        );
        $node->setAttribute(
            'InternationalName',
            $competition->international_name
        );
        $this->setOptional(
            $node,
            'InternationalShortName',
            $competition->international_short_name
        );
        $this->setOptional(
            $node,
            'OrganisationInternationalName',
            $competition->organisation_international_name
        );

        if (!$competition->organisation_international_short_name) {
            throw new RuntimeException(
                'Match Competition requires OrganisationInternationalShortName.'
            );
        }

        $node->setAttribute(
            'OrganisationInternationalShortName',
            $competition->organisation_international_short_name
        );

        if ($competition->parent) {
            $parentNode = $this->competitionNode(
                $document,
                $competition->parent
            );
            $parentNode = $document->importNode($parentNode, true);

            // Rebuild with the correct scenario element name.
            $wrapper = $document->createElementNS(
                self::NS,
                'ParentCompetition'
            );

            foreach ($parentNode->attributes ?? [] as $attribute) {
                $wrapper->setAttribute(
                    $attribute->nodeName,
                    $attribute->nodeValue
                );
            }

            foreach (iterator_to_array($parentNode->childNodes) as $child) {
                $wrapper->appendChild($child->cloneNode(true));
            }

            $node->appendChild($wrapper);
        }

        return $node;
    }

    private function facilityNode(
        DOMDocument $document,
        Facility $facility
    ): DOMElement {
        $this->catalog->assertFifaIdentifier(
            $facility->facility_fifa_id
        );

        $facility->loadMissing(['addresses']);

        if ($facility->addresses->count() !== 1) {
            throw new RuntimeException(
                'Match Facility requires exactly one canonical address.'
            );
        }

        $address = $facility->addresses->first();

        $node = $document->createElementNS(
            self::NS,
            'Facility'
        );
        $node->setAttribute(
            'FacilityFIFAId',
            $facility->facility_fifa_id
        );
        $this->setOptional(
            $node,
            'InternationalName',
            $facility->international_name
        );
        $this->setOptional(
            $node,
            'InternationalShortName',
            $facility->international_short_name
        );
        $node->setAttribute('Town', $address->town);
        $node->setAttribute('Country', $address->country);

        return $node;
    }

    private function teamNode(
        DOMDocument $document,
        MatchTeam $team
    ): DOMElement {
        $this->catalog->assertEnum(
            'MatchTeamNatureType',
            $team->team_nature
        );
        $this->catalog->assertFifaIdentifier(
            $team->organisation_fifa_id
        );

        if (!$team->international_name) {
            throw new RuntimeException(
                'MatchTeam requires InternationalName.'
            );
        }

        if ($team->team_fifa_id) {
            $this->catalog->assertFifaIdentifier(
                $team->team_fifa_id
            );
        }

        $node = $document->createElementNS(self::NS, 'Team');
        $node->setAttribute('TeamNature', $team->team_nature);
        $node->setAttribute(
            'OrganisationFIFAId',
            $team->organisation_fifa_id
        );
        $this->setOptional(
            $node,
            'TeamFIFAId',
            $team->team_fifa_id
        );
        $node->setAttribute(
            'InternationalName',
            $team->international_name
        );
        $this->setOptional(
            $node,
            'InternationalShortName',
            $team->international_short_name
        );
        $this->setOptional(
            $node,
            'InternationalCode',
            $team->international_code
        );
        $this->setOptional(
            $node,
            'FinalResult',
            $team->final_result
        );
        $this->setOptional(
            $node,
            'MemberAssociation',
            $team->member_association
        );

        foreach ($team->players as $player) {
            $this->catalog->assertFifaIdentifier(
                $player->person_fifa_id
            );

            $playerNode = $document->createElementNS(
                self::NS,
                'Player'
            );
            $playerNode->setAttribute(
                'PersonFIFAId',
                $player->person_fifa_id
            );
            $playerNode->setAttribute(
                'ShirtNumber',
                $player->shirt_number
            );
            $playerNode->setAttribute(
                'Captain',
                $this->boolValue($player->captain)
            );
            $playerNode->setAttribute(
                'Goalkeeper',
                $this->boolValue($player->goalkeeper)
            );
            $playerNode->setAttribute(
                'StartingLineup',
                $this->boolValue($player->starting_lineup)
            );

            if ($player->played !== null) {
                $playerNode->setAttribute(
                    'Played',
                    $this->boolValue($player->played)
                );
            }

            $this->setOptional(
                $playerNode,
                'InternationalFirstName',
                $player->international_first_name
            );
            $this->setOptional(
                $playerNode,
                'InternationalLastName',
                $player->international_last_name
            );
            $this->setOptional(
                $playerNode,
                'DateOfBirth',
                $player->date_of_birth?->format('Y-m-d')
            );
            $this->setOptional(
                $playerNode,
                'MemberAssociation',
                $player->member_association
            );

            $node->appendChild($playerNode);
        }

        foreach ($team->officials as $official) {
            $this->catalog->assertFifaIdentifier(
                $official->person_fifa_id
            );
            $this->catalog->assertEnum(
                'TeamOfficialRoleNatureType',
                $official->role
            );

            $officialNode = $document->createElementNS(
                self::NS,
                'TeamOfficial'
            );
            $officialNode->setAttribute(
                'PersonFIFAId',
                $official->person_fifa_id
            );
            $officialNode->setAttribute('Role', $official->role);
            $this->setOptional(
                $officialNode,
                'RoleDescription',
                $official->role_description
            );
            $this->setOptional(
                $officialNode,
                'InternationalFirstName',
                $official->international_first_name
            );
            $this->setOptional(
                $officialNode,
                'InternationalLastName',
                $official->international_last_name
            );
            $this->setOptional(
                $officialNode,
                'DateOfBirth',
                $official->date_of_birth?->format('Y-m-d')
            );
            $this->setOptional(
                $officialNode,
                'MemberAssociation',
                $official->member_association
            );

            $node->appendChild($officialNode);
        }

        return $node;
    }

    private function assertMatch(MatchRecord $match): void
    {
        foreach ([
            'match_fifa_id',
            'status',
            'competition_fifa_id',
        ] as $field) {
            if ($match->{$field} === null || $match->{$field} === '') {
                throw new RuntimeException(
                    "MatchInternational cannot be exported: {$field} is required."
                );
            }
        }

        $this->catalog->assertFifaIdentifier(
            $match->match_fifa_id
        );
        $this->catalog->assertFifaIdentifier(
            $match->competition_fifa_id
        );
        $this->catalog->assertEnum(
            'MatchStatusType',
            $match->status
        );

        if (
            !$match->competition instanceof Competition
            && !$match->competitionContext instanceof MatchCompetitionContext
        ) {
            throw new RuntimeException(
                'MatchInternational requires a canonical Competition or embedded competition context.'
            );
        }

        if ($match->teams->count() !== 2) {
            throw new RuntimeException(
                'MatchInternational requires exactly two Team elements.'
            );
        }

        $natures = $match->teams
            ->pluck('team_nature')
            ->sort()
            ->values()
            ->all();

        if (count(array_unique($natures)) !== 2) {
            throw new RuntimeException(
                'MatchInternational requires two distinct team natures.'
            );
        }
    }

    private function boolValue(bool $value): string
    {
        return $value ? 'true' : 'false';
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
