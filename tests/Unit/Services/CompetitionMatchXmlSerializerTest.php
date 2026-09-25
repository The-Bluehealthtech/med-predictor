<?php

namespace Tests\Unit\Services;

use App\Models\FifaConnect\Competition;
use App\Models\FifaConnect\CompetitionTeam;
use App\Models\FifaConnect\MatchRecord;
use App\Models\FifaConnect\MatchTeam;
use App\Services\FifaConnect\CompetitionInternationalXmlSerializer;
use App\Services\FifaConnect\MatchInternationalXmlSerializer;
use App\Services\FifaConnect\SchemaCatalog;
use App\Services\FifaConnect\XsdValidator;
use Illuminate\Database\Eloquent\Collection;
use RuntimeException;
use Tests\TestCase;

class CompetitionMatchXmlSerializerTest extends TestCase
{
    private string $dir;

    protected function setUp(): void
    {
        parent::setUp();

        $this->dir = sys_get_temp_dir()
            . '/fc-comp-xsd-' . bin2hex(random_bytes(6));

        mkdir($this->dir, 0777, true);

        file_put_contents(
            $this->dir . '/generic.xsd',
            $this->schema([
                'GenderType' => ['male', 'female'],
            ])
        );

        file_put_contents(
            $this->dir . '/registration.xsd',
            $this->schema([
                'SimpleStatusType' => ['active', 'inactive', 'pending'],
                'DisciplineType' => ['Football', 'Futsal', 'BeachSoccer'],
            ], true)
        );

        file_put_contents(
            $this->dir . '/competition.xsd',
            $this->schema([
                'CompetitionSystemNatureType' => [
                    'Elimination',
                    'Round robin',
                    'Combination',
                    'Other',
                ],
                'CompetitionTeamCharacterType' => [
                    'Club',
                    'National',
                    'Other',
                ],
                'AgeCategoryNatureType' => [
                    'Seniors',
                    'U23',
                    'U21',
                    'Other',
                ],
                'MatchTeamNatureType' => ['Home', 'Away'],
                'MatchStatusType' => [
                    'toSchedule',
                    'Scheduled',
                    'Postponed',
                    'Running',
                    'Cancelled',
                    'Played',
                    'Officialised',
                ],
                'MatchPhaseNatureType' => [
                    '1H',
                    '2H',
                    '1ET',
                    '2ET',
                    'PEN',
                ],
                'MatchEventNatureType' => [
                    'Substitution',
                    'Yellow',
                    'Red',
                    'Goal',
                ],
                'MatchEventDetailNatureType' => [
                    'Dissent',
                    'SeriousFoul',
                    'Violence',
                ],
                'MatchOfficialRoleNatureType' => [
                    'Referee',
                    '4. Official',
                    'VAR',
                    'Other',
                ],
                'TeamOfficialRoleNatureType' => [
                    'Coach',
                    'Team Doctor',
                    'Other',
                ],
            ])
        );

        foreach (['discipline.xsd', 'scenarios.xsd'] as $file) {
            file_put_contents(
                $this->dir . '/' . $file,
                '<?xml version="1.0"?><xs:schema xmlns:xs="http://www.w3.org/2001/XMLSchema"/>'
            );
        }

        config(['services.fifa_connect.xsd_path' => $this->dir]);
    }

    protected function tearDown(): void
    {
        foreach (glob($this->dir . '/*') ?: [] as $file) {
            @unlink($file);
        }

        @rmdir($this->dir);

        parent::tearDown();
    }

    public function test_competition_international_serializes_required_xsd_structure(): void
    {
        $competition = $this->competition();

        $team = new CompetitionTeam([
            'organisation_fifa_id' => 'BBB222B',
            'team_fifa_id' => 'CCC333C',
            'international_name' => 'Example FC',
            'international_short_name' => 'EFC',
            'member_association' => 'FRA',
        ]);
        $team->setRelation('persons', new Collection());

        $competition->setRelation('elements', new Collection());
        $competition->setRelation('teams', new Collection([$team]));

        $xml = $this->competitionSerializer()->serialize(
            $competition,
            false
        );

        $this->assertStringContainsString(
            '<CompetitionInternationalData',
            $xml
        );
        $this->assertStringContainsString(
            '<CompetitionInternational CompetitionFIFAId="AAA111A"',
            $xml
        );
        $this->assertStringContainsString(
            '<CompetitionNature CompetitionFIFAId="DDD444D"',
            $xml
        );
        $this->assertStringContainsString(
            '<System Nature="Round robin"',
            $xml
        );
        $this->assertStringContainsString(
            '<CompetitionTeam OrganisationFIFAId="BBB222B"',
            $xml
        );
    }

    public function test_competition_rejects_invalid_fifa_enum(): void
    {
        $competition = $this->competition();
        $competition->system_nature = 'round_robin';

        $competition->setRelation('elements', new Collection());
        $competition->setRelation('teams', new Collection());

        $this->expectException(RuntimeException::class);

        $this->competitionSerializer()->serialize(
            $competition,
            false
        );
    }

    public function test_match_requires_exactly_two_teams(): void
    {
        $match = $this->match();
        $match->setRelation('competition', $this->competition());
        $match->setRelation('facility', null);
        $match->setRelation('phases', new Collection());
        $match->setRelation('events', new Collection());
        $match->setRelation('officials', new Collection());

        $home = $this->matchTeam('Home', 'BBB222B');
        $match->setRelation('teams', new Collection([$home]));

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('exactly two Team');

        $this->matchSerializer()->serialize($match, false);
    }

    public function test_match_requires_distinct_home_and_away_team_natures(): void
    {
        $match = $this->match();
        $match->setRelation('competition', $this->competition());
        $match->setRelation('facility', null);
        $match->setRelation('phases', new Collection());
        $match->setRelation('events', new Collection());
        $match->setRelation('officials', new Collection());

        $match->setRelation(
            'teams',
            new Collection([
                $this->matchTeam('Home', 'BBB222B'),
                $this->matchTeam('Home', 'CCC333C'),
            ])
        );

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('distinct team natures');

        $this->matchSerializer()->serialize($match, false);
    }

    public function test_match_serializes_canonical_competition_and_two_teams(): void
    {
        $match = $this->match();

        $competition = $this->competition();
        $competition->setRelation('parent', null);

        $match->setRelation('competition', $competition);
        $match->setRelation('facility', null);
        $match->setRelation('phases', new Collection());
        $match->setRelation('events', new Collection());
        $match->setRelation('officials', new Collection());
        $match->setRelation(
            'teams',
            new Collection([
                $this->matchTeam('Home', 'BBB222B'),
                $this->matchTeam('Away', 'CCC333C'),
            ])
        );

        $xml = $this->matchSerializer()->serialize($match, false);

        $this->assertStringContainsString(
            '<MatchInternationalData',
            $xml
        );
        $this->assertStringContainsString(
            '<MatchInternational MatchFIFAId="EEE555E" Status="Scheduled"',
            $xml
        );
        $this->assertStringContainsString(
            '<Competition CompetitionFIFAId="AAA111A"',
            $xml
        );
        $this->assertSame(2, substr_count($xml, '<Team '));
        $this->assertStringContainsString(
            'TeamNature="Home"',
            $xml
        );
        $this->assertStringContainsString(
            'TeamNature="Away"',
            $xml
        );
    }

    private function competition(): Competition
    {
        $competition = new Competition([
            'competition_fifa_id' => 'AAA111A',
            'international_name' => 'National League',
            'international_short_name' => 'NL',
            'organisation_fifa_id' => 'BBB222B',
            'organisation_international_short_name' => 'FA',
            'season' => 2026,
            'status' => 'active',
            'system_nature' => 'Round robin',
            'nature_fifa_id' => 'DDD444D',
            'nature_international_short_name' => 'SEN',
            'team_character' => 'Club',
            'discipline' => 'Football',
            'age_category' => 'Seniors',
        ]);

        $competition->setRelation('parent', null);
        $competition->setRelation('matches', new Collection());

        return $competition;
    }

    private function match(): MatchRecord
    {
        return new MatchRecord([
            'match_fifa_id' => 'EEE555E',
            'status' => 'Scheduled',
            'competition_fifa_id' => 'AAA111A',
        ]);
    }

    private function matchTeam(
        string $nature,
        string $organisationId
    ): MatchTeam {
        $team = new MatchTeam([
            'team_nature' => $nature,
            'organisation_fifa_id' => $organisationId,
            'international_name' => $nature . ' Team',
        ]);

        $team->setRelation('players', new Collection());
        $team->setRelation('officials', new Collection());

        return $team;
    }

    private function competitionSerializer(): CompetitionInternationalXmlSerializer
    {
        return new CompetitionInternationalXmlSerializer(
            new SchemaCatalog(),
            new XsdValidator()
        );
    }

    private function matchSerializer(): MatchInternationalXmlSerializer
    {
        return new MatchInternationalXmlSerializer(
            new SchemaCatalog(),
            new XsdValidator()
        );
    }

    private function schema(
        array $enums,
        bool $includeFifaIdentifier = false
    ): string {
        $parts = [
            '<?xml version="1.0"?>',
            '<xs:schema xmlns:xs="http://www.w3.org/2001/XMLSchema">',
        ];

        if ($includeFifaIdentifier) {
            $parts[] = '<xs:simpleType name="FIFAIdentifier"><xs:restriction base="xs:string"><xs:length value="7"/><xs:pattern value="^[0123456789ABCDEFGHIJKLMNPQRSTUVWXYZ]{6}[0123456789ABCDEFGHIJKLMNPQRSTU]$"/></xs:restriction></xs:simpleType>';
        }

        foreach ($enums as $name => $values) {
            $parts[] = '<xs:simpleType name="' . $name . '"><xs:restriction base="xs:string">';

            foreach ($values as $value) {
                $parts[] = '<xs:enumeration value="' . htmlspecialchars($value, ENT_XML1) . '"/>';
            }

            $parts[] = '</xs:restriction></xs:simpleType>';
        }

        $parts[] = '</xs:schema>';

        return implode('', $parts);
    }
}
