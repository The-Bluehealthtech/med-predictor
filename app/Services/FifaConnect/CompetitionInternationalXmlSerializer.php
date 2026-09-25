<?php

namespace App\Services\FifaConnect;

use App\Models\FifaConnect\Competition;
use App\Models\FifaConnect\CompetitionTeam;
use App\Models\FifaConnect\MatchRecord;
use DOMDocument;
use DOMElement;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class CompetitionInternationalXmlSerializer
{
    private const NS = 'http://fifa.com/fc';
    private const XSI = 'http://www.w3.org/2001/XMLSchema-instance';

    public function __construct(
        private readonly SchemaCatalog $catalog,
        private readonly XsdValidator $validator
    ) {
    }

    public function serialize(
        Competition|Collection|array $competitions,
        bool $validate = true
    ): string {
        $items = $competitions instanceof Competition
            ? collect([$competitions])
            : collect($competitions);

        $document = new DOMDocument('1.0', 'UTF-8');
        $document->formatOutput = true;

        $root = $document->createElementNS(
            self::NS,
            'CompetitionInternationalData'
        );
        $root->setAttributeNS(
            self::XSI,
            'xsi:schemaLocation',
            self::NS . ' scenarios.xsd'
        );
        $root->setAttribute(
            'ExportDate',
            now()->toIso8601String()
        );

        foreach ($items as $competition) {
            if (!$competition instanceof Competition) {
                throw new RuntimeException(
                    'CompetitionInternationalData accepts canonical Competition models only.'
                );
            }

            $root->appendChild(
                $this->competitionNode($document, $competition)
            );
        }

        $document->appendChild($root);
        $xml = $document->saveXML();

        if ($validate) {
            $this->validator->assertValid(
                $xml,
                'scenarios.xsd'
            );
        }

        return $xml;
    }

    private function competitionNode(
        DOMDocument $document,
        Competition $competition,
        string $elementName = 'CompetitionInternational'
    ): DOMElement {
        $competition->loadMissing([
            'matches.teams',
            'elements',
            'teams',
        ]);

        $this->assertCompetition($competition);

        $node = $document->createElementNS(
            self::NS,
            $elementName
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
        $node->setAttribute(
            'OrganisationFIFAId',
            $competition->organisation_fifa_id
        );
        $node->setAttribute(
            'Season',
            (string) $competition->season
        );
        $node->setAttribute(
            'Status',
            $competition->status
        );
        $this->setOptional(
            $node,
            'OrderNumber',
            $competition->order_number
        );
        $this->setOptional(
            $node,
            'DateFrom',
            $competition->date_from?->format('Y-m-d')
        );
        $this->setOptional(
            $node,
            'DateTo',
            $competition->date_to?->format('Y-m-d')
        );
        $this->setOptional(
            $node,
            'NumberOfParticipants',
            $competition->number_of_participants
        );
        $this->setOptional(
            $node,
            'OrganisationInternationalName',
            $competition->organisation_international_name
        );
        $node->setAttribute(
            'OrganisationInternationalShortName',
            $competition->organisation_international_short_name
        );

        $nature = $document->createElementNS(
            self::NS,
            'CompetitionNature'
        );
        $nature->setAttribute(
            'CompetitionFIFAId',
            $competition->nature_fifa_id
        );
        $this->setOptional(
            $nature,
            'InternationalName',
            $competition->nature_international_name
        );
        $nature->setAttribute(
            'InternationalShortName',
            $competition->nature_international_short_name
        );
        $nature->setAttribute(
            'TeamCharacter',
            $competition->team_character
        );
        $nature->setAttribute(
            'Discipline',
            $competition->discipline
        );
        $nature->setAttribute(
            'AgeCategory',
            $competition->age_category
        );
        $this->setOptional(
            $nature,
            'AgeCategoryName',
            $competition->age_category_name
        );
        $this->setOptional(
            $nature,
            'Gender',
            $competition->gender
        );
        $node->appendChild($nature);

        $system = $document->createElementNS(
            self::NS,
            'System'
        );
        $system->setAttribute(
            'Nature',
            $competition->system_nature
        );
        $this->setOptional(
            $system,
            'Multiplier',
            $competition->system_multiplier
        );
        $node->appendChild($system);

        PictureXml::append(
            $document,
            $node,
            $competition->picture,
            'Logo'
        );

        foreach ($competition->matches as $match) {
            $node->appendChild(
                $this->simpleMatchNode($document, $match)
            );
        }

        foreach ($competition->elements as $element) {
            $node->appendChild(
                $this->competitionNode(
                    $document,
                    $element,
                    'CompetitionElement'
                )
            );
        }

        foreach ($competition->teams as $team) {
            $node->appendChild(
                $this->teamNode($document, $team)
            );
        }

        return $node;
    }

    private function simpleMatchNode(
        DOMDocument $document,
        MatchRecord $match
    ): DOMElement {
        $match->loadMissing('teams');

        $this->catalog->assertFifaIdentifier(
            $match->match_fifa_id
        );
        $this->catalog->assertEnum(
            'MatchStatusType',
            $match->status
        );

        $home = $match->teams->firstWhere(
            'team_nature',
            'Home'
        );
        $away = $match->teams->firstWhere(
            'team_nature',
            'Away'
        );

        if (!$home || !$away || $match->teams->count() !== 2) {
            throw new RuntimeException(
                'SimpleMatchInternational requires exactly one Home and one Away team.'
            );
        }

        foreach ([
            'Home' => $home,
            'Away' => $away,
        ] as $nature => $team) {
            if (!$team->team_fifa_id) {
                throw new RuntimeException(
                    "{$nature} simple match team requires TeamFIFAId."
                );
            }

            if (!$team->international_short_name) {
                throw new RuntimeException(
                    "{$nature} simple match team requires InternationalShortName."
                );
            }

            $this->catalog->assertFifaIdentifier(
                $team->team_fifa_id
            );
        }

        if ($match->facility_fifa_id) {
            $this->catalog->assertFifaIdentifier(
                $match->facility_fifa_id
            );
        }

        $node = $document->createElementNS(
            self::NS,
            'Match'
        );
        $node->setAttribute(
            'MatchFIFAId',
            $match->match_fifa_id
        );
        $node->setAttribute('Status', $match->status);
        $this->setOptional(
            $node,
            'DateTimeLocal',
            $match->date_time_local?->toIso8601String()
        );
        $this->setOptional(
            $node,
            'Matchday',
            $match->matchday
        );
        $this->setOptional(
            $node,
            'FacilityInternationalShortName',
            $match->facility_international_short_name
        );
        $this->setOptional(
            $node,
            'FacilityFIFAId',
            $match->facility_fifa_id
        );

        $node->setAttribute(
            'HomeTeamInternationalShortName',
            $home->international_short_name
        );
        $node->setAttribute(
            'HomeTeamFIFAId',
            $home->team_fifa_id
        );
        $node->setAttribute(
            'AwayTeamInternationalShortName',
            $away->international_short_name
        );
        $node->setAttribute(
            'AwayTeamFIFAId',
            $away->team_fifa_id
        );

        $this->setOptional(
            $node,
            'HomeFinalResult',
            $home->final_result
        );
        $this->setOptional(
            $node,
            'AwayFinalResult',
            $away->final_result
        );

        return $node;
    }

    private function teamNode(
        DOMDocument $document,
        CompetitionTeam $team
    ): DOMElement {
        $this->catalog->assertFifaIdentifier(
            $team->organisation_fifa_id
        );

        if ($team->team_fifa_id) {
            $this->catalog->assertFifaIdentifier(
                $team->team_fifa_id
            );
        }

        $node = $document->createElementNS(
            self::NS,
            'CompetitionTeam'
        );
        $node->setAttribute(
            'OrganisationFIFAId',
            $team->organisation_fifa_id
        );
        $this->setOptional(
            $node,
            'TeamFIFAId',
            $team->team_fifa_id
        );
        $this->setOptional(
            $node,
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
            'MemberAssociation',
            $team->member_association
        );

        $people = $team->relationLoaded('persons')
            ? $team->getRelation('persons')
            : DB::table('fifa_connect_competition_team_persons')
                ->where('competition_team_id', $team->id)
                ->orderBy('id')
                ->get();

        foreach ($people as $person) {
            $name = match ($person->role_type) {
                'Player' => 'Player',
                'TeamOfficial' => 'TeamOfficial',
                default => throw new RuntimeException(
                    "Unsupported competition role type: {$person->role_type}."
                ),
            };

            $this->catalog->assertFifaIdentifier(
                $person->person_fifa_id
            );

            $role = $document->createElementNS(
                self::NS,
                $name
            );
            $role->setAttribute(
                'PersonFIFAId',
                $person->person_fifa_id
            );
            $this->setOptional(
                $role,
                'InternationalFirstName',
                $person->international_first_name
            );
            $this->setOptional(
                $role,
                'InternationalLastName',
                $person->international_last_name
            );
            $dateOfBirth = $person->date_of_birth ?? null;

            if (is_string($dateOfBirth)) {
                $dateOfBirth = \Carbon\Carbon::parse(
                    $dateOfBirth
                );
            }

            $this->setOptional(
                $role,
                'DateOfBirth',
                $dateOfBirth?->format('Y-m-d')
            );
            $this->setOptional(
                $role,
                'MemberAssociation',
                $person->member_association
            );
            $node->appendChild($role);
        }

        $rankingFields = [
            'ranking_position' => 'Position',
            'ranking_matches_played' => 'MatchesPlayed',
            'ranking_wins' => 'Wins',
            'ranking_draws' => 'Draws',
            'ranking_losses' => 'Losses',
            'ranking_goals_for' => 'GoalsFor',
            'ranking_goals_against' => 'GoalsAgainst',
            'ranking_goal_difference' => 'GoalDifference',
            'ranking_points' => 'Points',
            'ranking_negative_points' => 'NegativePoints',
        ];

        if ($team->ranking_position !== null) {
            $ranking = $document->createElementNS(
                self::NS,
                'TeamRanking'
            );

            foreach ($rankingFields as $field => $attribute) {
                $this->setOptional(
                    $ranking,
                    $attribute,
                    $team->{$field}
                );
            }

            $node->appendChild($ranking);
        }

        return $node;
    }

    private function assertCompetition(
        Competition $competition
    ): void {
        foreach ([
            'competition_fifa_id',
            'international_name',
            'organisation_fifa_id',
            'season',
            'status',
            'system_nature',
            'nature_fifa_id',
            'nature_international_short_name',
            'team_character',
            'discipline',
            'age_category',
            'organisation_international_short_name',
        ] as $field) {
            if ($competition->{$field} === null
                || $competition->{$field} === '') {
                throw new RuntimeException(
                    "CompetitionInternational cannot be exported: {$field} is required."
                );
            }
        }

        foreach ([
            $competition->competition_fifa_id,
            $competition->organisation_fifa_id,
            $competition->nature_fifa_id,
        ] as $id) {
            $this->catalog->assertFifaIdentifier($id);
        }

        $this->catalog->assertEnum(
            'SimpleStatusType',
            $competition->status
        );
        $this->catalog->assertEnum(
            'CompetitionSystemNatureType',
            $competition->system_nature
        );
        $this->catalog->assertEnum(
            'CompetitionTeamCharacterType',
            $competition->team_character
        );
        $this->catalog->assertEnum(
            'DisciplineType',
            $competition->discipline
        );
        $this->catalog->assertEnum(
            'AgeCategoryNatureType',
            $competition->age_category
        );

        if ($competition->gender !== null) {
            $this->catalog->assertEnum(
                'GenderType',
                $competition->gender
            );
        }

        if ((int) $competition->season <= 0) {
            throw new RuntimeException(
                'Competition season must be a positive integer.'
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
