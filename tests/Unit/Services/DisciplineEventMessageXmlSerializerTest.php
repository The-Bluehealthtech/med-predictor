<?php

namespace Tests\Unit\Services;

use App\Models\FifaConnect\DisciplineCase;
use App\Models\FifaConnect\MatchEvent;
use App\Models\FifaConnect\Sanction;
use App\Services\FifaConnect\DisciplineCaseXmlSerializer;
use App\Services\FifaConnect\EventMessageXmlSerializer;
use App\Services\FifaConnect\SchemaCatalog;
use App\Services\FifaConnect\XsdValidator;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use RuntimeException;
use Tests\TestCase;

class DisciplineEventMessageXmlSerializerTest extends TestCase
{
    private string $dir;

    protected function setUp(): void
    {
        parent::setUp();

        $this->dir = sys_get_temp_dir()
            . '/fc-discipline-xsd-' . bin2hex(random_bytes(6));

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
                'PersonNatureType' => [
                    'Player',
                    'MatchOfficial',
                    'TeamOfficial',
                    'OrganisationOfficial',
                ],
                'OrganisationOfficialRoleType' => [
                    'President',
                    'VicePresident',
                    'GeneralSecretary',
                    'Other',
                ],
                'DisciplineType' => [
                    'Football',
                    'Futsal',
                    'BeachSoccer',
                ],
            ], true)
        );

        file_put_contents(
            $this->dir . '/competition.xsd',
            $this->schema([
                'MatchTeamNatureType' => ['Home', 'Away'],
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

        file_put_contents(
            $this->dir . '/discipline.xsd',
            $this->schema([
                'OffenderNatureType' => [
                    'Person',
                    'Organisation',
                ],
                'PersonSanctionNatureType' => [
                    'Warning',
                    'Fine',
                    'MatchSuspension',
                    'Other',
                ],
                'OrganisationSanctionNatureType' => [
                    'Warning',
                    'Fine',
                    'TransferBan',
                    'Other',
                ],
                'SanctionMeasureType' => [
                    'MonetaryUnit',
                    'Months',
                    'Matches',
                    'Points',
                    'Goals',
                ],
            ])
        );

        file_put_contents(
            $this->dir . '/scenarios.xsd',
            $this->schema([
                'MessageNatureType' => [
                    'Insert',
                    'Update',
                    'Delete',
                ],
            ])
        );

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

    public function test_person_case_serializes_person_sanction(): void
    {
        $case = new DisciplineCase([
            'case_fifa_id' => 'AAA111A',
            'organisation_fifa_id' => 'BBB222B',
            'case_date' => Carbon::parse('2026-09-25'),
            'offender_nature' => 'Person',
            'offender_person_fifa_id' => 'CCC333C',
            'offender_person_nature' => 'Player',
            'description' => 'Serious foul play',
            'status' => 'active',
        ]);

        $sanction = new Sanction([
            'status' => 'active',
            'person_sanction_nature' => 'MatchSuspension',
            'value' => 2,
            'measure' => 'Matches',
        ]);

        $case->setRelation(
            'sanctions',
            new Collection([$sanction])
        );

        $xml = $this->disciplineSerializer()->serialize(
            $case,
            false
        );

        $this->assertStringContainsString('<Case ', $xml);
        $this->assertStringContainsString(
            'OffenderNature="Person"',
            $xml
        );
        $this->assertStringContainsString(
            '<PersonSanctionNature>MatchSuspension</PersonSanctionNature>',
            $xml
        );
        $this->assertStringContainsString(
            'Measure="Matches"',
            $xml
        );
    }

    public function test_person_case_requires_person_fifa_id(): void
    {
        $case = new DisciplineCase([
            'case_fifa_id' => 'AAA111A',
            'organisation_fifa_id' => 'BBB222B',
            'case_date' => Carbon::parse('2026-09-25'),
            'offender_nature' => 'Person',
            'description' => 'Case',
            'status' => 'active',
        ]);
        $case->setRelation('sanctions', new Collection());

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('OffenderPersonFIFAId');

        $this->disciplineSerializer()->serialize($case, false);
    }

    public function test_sanction_rejects_both_person_and_organisation_natures(): void
    {
        $case = new DisciplineCase([
            'case_fifa_id' => 'AAA111A',
            'organisation_fifa_id' => 'BBB222B',
            'case_date' => Carbon::parse('2026-09-25'),
            'offender_nature' => 'Person',
            'offender_person_fifa_id' => 'CCC333C',
            'description' => 'Case',
            'status' => 'active',
        ]);

        $sanction = new Sanction([
            'status' => 'active',
            'person_sanction_nature' => 'Warning',
            'organisation_sanction_nature' => 'Fine',
        ]);

        $case->setRelation(
            'sanctions',
            new Collection([$sanction])
        );

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('exactly one');

        $this->disciplineSerializer()->serialize($case, false);
    }

    public function test_event_message_serializes_insert_and_two_scores(): void
    {
        $event = $this->event();

        $xml = $this->eventSerializer()->serialize(
            $event,
            'Insert',
            'EEE555E',
            Carbon::parse('2026-09-25'),
            [
                ['Team' => 'Home', 'Value' => 1],
                ['Team' => 'Away', 'Value' => 0],
            ],
            false
        );

        $this->assertStringContainsString(
            '<EventMessage ',
            $xml
        );
        $this->assertStringContainsString(
            'MessageNature="Insert"',
            $xml
        );
        $this->assertStringContainsString(
            'MatchFIFAId="EEE555E"',
            $xml
        );
        $this->assertSame(2, substr_count($xml, '<Score '));
    }

    public function test_event_message_rejects_more_than_two_scores(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('one or two Score');

        $this->eventSerializer()->serialize(
            $this->event(),
            'Update',
            'EEE555E',
            null,
            [0, 1, 2],
            false
        );
    }

    public function test_event_message_rejects_unknown_message_nature(): void
    {
        $this->expectException(RuntimeException::class);

        $this->eventSerializer()->serialize(
            $this->event(),
            'Replace',
            'EEE555E',
            null,
            [0],
            false
        );
    }

    private function event(): MatchEvent
    {
        return new MatchEvent([
            'event_id' => 'evt-1',
            'match_phase' => '1H',
            'minute' => 15,
            'event_type' => 'Goal',
            'player_fifa_id' => 'CCC333C',
            'match_team' => 'Home',
            'player_shirt_number' => 9,
        ]);
    }

    private function disciplineSerializer(): DisciplineCaseXmlSerializer
    {
        return new DisciplineCaseXmlSerializer(
            new SchemaCatalog(),
            new XsdValidator()
        );
    }

    private function eventSerializer(): EventMessageXmlSerializer
    {
        return new EventMessageXmlSerializer(
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
