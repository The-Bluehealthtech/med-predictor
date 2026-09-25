<?php

namespace Tests\Integration\FifaConnect;

use App\Models\FifaConnect\Address;
use App\Models\FifaConnect\Certification;
use App\Models\FifaConnect\Competition;
use App\Models\FifaConnect\DisciplineCase;
use App\Models\FifaConnect\EventMessage;
use App\Models\FifaConnect\Facility;
use App\Models\FifaConnect\Field;
use App\Models\FifaConnect\MatchEvent;
use App\Models\FifaConnect\MatchRecord;
use App\Models\FifaConnect\MatchTeam;
use App\Models\FifaConnect\Person;
use App\Models\FifaConnect\Picture;
use App\Models\FifaConnect\Registration;
use App\Models\FifaConnect\Sanction;
use App\Services\FifaConnect\CanonicalPersistenceService;
use App\Services\FifaConnect\CanonicalXmlImporter;
use App\Services\FifaConnect\CompetitionInternationalXmlSerializer;
use App\Services\FifaConnect\DisciplineCaseXmlSerializer;
use App\Services\FifaConnect\EventMessageXmlSerializer;
use App\Services\FifaConnect\MatchInternationalXmlSerializer;
use App\Services\FifaConnect\PersonLocalXmlSerializer;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class FifaConnectPersistenceRoundTripTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();

        foreach ([
            'fifa_connect_persons',
            'fifa_connect_competitions',
            'fifa_connect_matches',
            'fifa_connect_event_messages',
        ] as $table) {
            if (!Schema::hasTable($table)) {
                $this->markTestSkipped(
                    'Canonical FIFA migrations are not installed.'
                );
            }
        }

        $path = rtrim(
            (string) config(
                'services.fifa_connect.xsd_validation_path'
            ),
            DIRECTORY_SEPARATOR
        );

        if (!is_file($path . '/scenarios.xsd')) {
            $this->markTestSkipped(
                'FIFA validation bundle is not installed.'
            );
        }
    }

    public function test_person_xml_db_xml_round_trip_is_lossless(): void
    {
        $person = new Person([
            'person_fifa_id' => 'ABC123A',
            'international_first_name' => 'Jean',
            'international_last_name' => 'Dupont',
            'local_first_name' => 'Jean',
            'local_last_name' => 'Dupont',
            'local_language' => 'fre',
            'local_country' => 'FR',
            'gender' => 'male',
            'nationality' => 'FR',
            'date_of_birth' => Carbon::parse('2000-01-02'),
            'country_of_birth' => 'FR',
            'place_of_birth' => 'Paris',
        ]);

        $person->setRelation(
            'picture',
            new Picture([
                'storage_mode' => 'link',
                'picture_link' => 'https://example.test/p.jpg',
                'mime_type' => 'image/jpeg',
            ])
        );
        $person->setRelation('localNames', new Collection());
        $person->setRelation(
            'nationalIdentifiers',
            new Collection()
        );
        $person->setRelation(
            'certifications',
            new Collection([
                new Certification([
                    'certification_type' => 'MatchOfficial',
                    'status' => 'active',
                    'certification_valid_from' =>
                        Carbon::parse('2026-01-01'),
                    'certification_nature' => 'FIFA Referee',
                    'description' => 'Referee certification',
                ]),
            ])
        );
        $person->setRelation(
            'registrations',
            new Collection([
                new Registration([
                    'registration_type' =>
                        Registration::TYPE_PLAYER,
                    'person_fifa_id' => 'ABC123A',
                    'organisation_fifa_id' => 'DEF456B',
                    'status' => 'active',
                    'registration_valid_from' =>
                        Carbon::parse('2026-01-01'),
                    'level' => 'pro',
                    'discipline' => 'Football',
                    'registration_nature' => 'Registration',
                    'club_training_category' => 'Category1',
                ]),
            ])
        );

        $serializer = app(PersonLocalXmlSerializer::class);
        $importer = app(CanonicalXmlImporter::class);

        $xmlBefore = $serializer->serialize($person, true);

        $stored = app(CanonicalPersistenceService::class)
            ->persistXml($xmlBefore);

        $stored->load([
            'picture',
            'localNames',
            'nationalIdentifiers',
            'certifications',
            'registrations',
        ]);

        $xmlAfter = $serializer->serialize(
            $stored,
            true
        );

        $this->assertSame(
            $importer->import($xmlBefore),
            $importer->import($xmlAfter)
        );

        $this->assertDatabaseHas(
            'fifa_connect_pictures',
            [
                'owner_type' => 'person',
                'owner_id' => $stored->id,
                'storage_mode' => 'link',
            ]
        );
    }

    public function test_competition_simple_match_round_trip_uses_team_fifa_ids(): void
    {
        $competition = $this->competition();

        $match = new MatchRecord([
            'match_fifa_id' => 'QRS789F',
            'status' => 'Scheduled',
            'competition_fifa_id' => 'JKL123D',
            'matchday' => 1,
            'facility_international_short_name' => 'STAD',
        ]);

        $home = new MatchTeam([
            'team_nature' => 'Home',
            'team_fifa_id' => 'TUV123G',
            'international_short_name' => 'HOME',
            'final_result' => 2,
        ]);
        $away = new MatchTeam([
            'team_nature' => 'Away',
            'team_fifa_id' => 'WXY456H',
            'international_short_name' => 'AWAY',
            'final_result' => 1,
        ]);

        $match->setRelation(
            'teams',
            new Collection([$home, $away])
        );

        $competition->setRelation(
            'matches',
            new Collection([$match])
        );

        $serializer =
            app(CompetitionInternationalXmlSerializer::class);
        $importer = app(CanonicalXmlImporter::class);

        $xmlBefore = $serializer->serialize(
            $competition,
            true
        );

        $this->assertStringContainsString(
            'HomeTeamFIFAId="TUV123G"',
            $xmlBefore
        );
        $this->assertStringContainsString(
            'AwayTeamFIFAId="WXY456H"',
            $xmlBefore
        );

        $stored = app(CanonicalPersistenceService::class)
            ->persistXml($xmlBefore);

        $stored->load([
            'matches.teams',
            'elements',
            'teams.persons',
            'picture',
        ]);

        $xmlAfter = $serializer->serialize(
            $stored,
            true
        );

        $this->assertSame(
            $importer->import($xmlBefore),
            $importer->import($xmlAfter)
        );

        $storedMatch = MatchRecord::query()
            ->where('match_fifa_id', 'QRS789F')
            ->firstOrFail();

        $this->assertDatabaseHas(
            'fifa_connect_match_teams',
            [
                'match_id' => $storedMatch->id,
                'team_nature' => 'Home',
                'team_fifa_id' => 'TUV123G',
                'organisation_fifa_id' => null,
            ]
        );
    }

    public function test_match_xml_db_xml_round_trip_uses_embedded_contexts(): void
    {
        $competition = $this->competition();

        $facility = new Facility([
            'facility_fifa_id' => 'GHI789C',
            'status' => 'active',
            'local_name' => 'Stade Exemple',
            'local_language' => 'fre',
            'local_country' => 'FR',
            'international_name' => 'Example Stadium',
            'international_short_name' => 'STAD',
        ]);
        $facility->setRelation(
            'addresses',
            new Collection([
                new Address([
                    'owner_type' => 'facility',
                    'country' => 'FR',
                    'town' => 'Paris',
                    'address' => '1 stade',
                ]),
            ])
        );

        $match = new MatchRecord([
            'match_fifa_id' => 'QRS789F',
            'status' => 'Scheduled',
            'competition_fifa_id' => 'JKL123D',
            'facility_fifa_id' => 'GHI789C',
            'matchday' => 1,
        ]);
        $match->setRelation('competition', $competition);
        $match->setRelation('competitionContext', null);
        $match->setRelation('facility', $facility);
        $match->setRelation('facilityContext', null);
        $match->setRelation('phases', new Collection());
        $match->setRelation('events', new Collection());
        $match->setRelation('officials', new Collection());

        $home = $this->fullMatchTeam(
            'Home',
            'TUV123G',
            'Home Club'
        );
        $away = $this->fullMatchTeam(
            'Away',
            'WXY456H',
            'Away Club'
        );

        $match->setRelation(
            'teams',
            new Collection([$home, $away])
        );

        $serializer =
            app(MatchInternationalXmlSerializer::class);
        $importer = app(CanonicalXmlImporter::class);

        $xmlBefore = $serializer->serialize($match, true);

        $stored = app(CanonicalPersistenceService::class)
            ->persistXml($xmlBefore);

        $stored->load([
            'competitionContext.parent.picture',
            'competitionContext.picture',
            'facilityContext',
            'phases',
            'events',
            'officials',
            'teams.picture',
            'teams.players',
            'teams.teamOfficials',
        ]);

        $xmlAfter = $serializer->serialize(
            $stored,
            true
        );

        $this->assertSame(
            $importer->import($xmlBefore),
            $importer->import($xmlAfter)
        );

        $this->assertDatabaseHas(
            'fifa_connect_match_competition_contexts',
            [
                'match_id' => $stored->id,
                'depth' => 0,
                'competition_fifa_id' => 'JKL123D',
            ]
        );
        $this->assertDatabaseHas(
            'fifa_connect_match_facility_contexts',
            [
                'match_id' => $stored->id,
                'facility_fifa_id' => 'GHI789C',
                'town' => 'Paris',
                'country' => 'FR',
            ]
        );
    }

    public function test_discipline_embedded_event_survives_db_round_trip(): void
    {
        $case = new DisciplineCase([
            'case_fifa_id' => 'ZAB789J',
            'organisation_fifa_id' => 'DEF456B',
            'case_date' => Carbon::parse('2026-09-25'),
            'offender_nature' => 'Person',
            'offender_person_fifa_id' => 'ABC123A',
            'offender_person_nature' => 'Player',
            'description' => 'Test case',
            'status' => 'active',
        ]);

        $case->setRelation(
            'matchEvent',
            new MatchEvent([
                'match_phase' => '1H',
                'minute' => 22,
                'event_type' => 'Yellow',
                'player_fifa_id' => 'ABC123A',
                'match_team' => 'Home',
            ])
        );
        $case->setRelation('caseMatchEvent', null);
        $case->setRelation(
            'sanctions',
            new Collection([
                new Sanction([
                    'status' => 'active',
                    'person_sanction_nature' => 'Warning',
                ]),
            ])
        );

        $serializer =
            app(DisciplineCaseXmlSerializer::class);
        $importer = app(CanonicalXmlImporter::class);

        $xmlBefore = $serializer->serialize($case, true);

        $stored = app(CanonicalPersistenceService::class)
            ->persistXml($xmlBefore);

        $stored->load([
            'caseMatchEvent',
            'matchEvent',
            'sanctions',
        ]);

        $xmlAfter = $serializer->serialize(
            $stored,
            true
        );

        $this->assertSame(
            $importer->import($xmlBefore),
            $importer->import($xmlAfter)
        );

        $this->assertDatabaseHas(
            'fifa_connect_case_match_events',
            [
                'case_id' => $stored->id,
                'event_type' => 'Yellow',
                'minute' => 22,
            ]
        );
    }

    public function test_event_message_insert_update_delete_is_applied_and_journaled(): void
    {
        $match = MatchRecord::query()->create([
            'match_fifa_id' => 'QRS789F',
            'status' => 'Scheduled',
            'competition_fifa_id' => 'JKL123D',
        ]);

        $serializer =
            app(EventMessageXmlSerializer::class);
        $persistence =
            app(CanonicalPersistenceService::class);
        $importer = app(CanonicalXmlImporter::class);

        $insertEvent = new MatchEvent([
            'event_id' => 'EVT-1',
            'match_phase' => '1H',
            'minute' => 10,
            'event_type' => 'Goal',
            'match_team' => 'Home',
            'player_fifa_id' => 'ABC123A',
        ]);

        $insertXml = $serializer->serialize(
            $insertEvent,
            'Insert',
            'QRS789F',
            Carbon::parse('2026-09-25'),
            [1, 0],
            true
        );

        $storedMessage = $persistence->persistXml(
            $insertXml
        );

        $this->assertInstanceOf(
            EventMessage::class,
            $storedMessage
        );
        $this->assertDatabaseHas(
            'fifa_connect_match_events',
            [
                'match_id' => $match->id,
                'event_id' => 'EVT-1',
                'minute' => 10,
                'event_type' => 'Goal',
            ]
        );

        $messageXml = $serializer->serializeModel(
            $storedMessage,
            true
        );

        $this->assertSame(
            $importer->import($insertXml),
            $importer->import($messageXml)
        );

        $updateEvent = new MatchEvent([
            'event_id' => 'EVT-1',
            'match_phase' => '1H',
            'minute' => 11,
            'event_type' => 'Goal',
            'match_team' => 'Home',
            'player_fifa_id' => 'ABC123A',
        ]);

        $persistence->persistXml(
            $serializer->serialize(
                $updateEvent,
                'Update',
                'QRS789F',
                Carbon::parse('2026-09-25'),
                [1, 0],
                true
            )
        );

        $this->assertDatabaseHas(
            'fifa_connect_match_events',
            [
                'match_id' => $match->id,
                'event_id' => 'EVT-1',
                'minute' => 11,
            ]
        );

        $persistence->persistXml(
            $serializer->serialize(
                $updateEvent,
                'Delete',
                'QRS789F',
                Carbon::parse('2026-09-25'),
                [1, 0],
                true
            )
        );

        $this->assertDatabaseMissing(
            'fifa_connect_match_events',
            [
                'match_id' => $match->id,
                'event_id' => 'EVT-1',
            ]
        );

        $this->assertSame(
            3,
            DB::table('fifa_connect_exchange_messages')
                ->where('scenario_type', 'EventMessage')
                ->where('subject_fifa_id', 'QRS789F')
                ->count()
        );
    }

    public function test_complex_event_score_survives_database_round_trip(): void
    {
        $event = new MatchEvent([
            'event_id' => 'EVT-COMPLEX',
            'match_phase' => '1H',
            'minute' => 10,
            'event_type' => 'Goal',
            'match_team' => 'Home',
        ]);
        $serializer = app(EventMessageXmlSerializer::class);
        $xml = $serializer->serialize(
            $event, 'Insert', 'QRS789F', Carbon::parse('2026-09-25'), [1]
        );
        $xml = str_replace(
            '<EventMessage xmlns="http://fifa.com/fc"',
            '<EventMessage xmlns="http://fifa.com/fc" xmlns:ext="urn:score-extension"',
            $xml
        );
        $xml = str_replace(
            '<Score>1</Score>',
            '<Score period="first"><ext:Home>1</ext:Home><Away>0</Away></Score>',
            $xml
        );

        $message = app(CanonicalPersistenceService::class)->persistXml($xml);
        $exported = $serializer->serializeModel($message);
        $imported = app(CanonicalXmlImporter::class)->import($exported);

        $this->assertStringContainsString('period="first"', $exported);
        $this->assertStringContainsString('<ext:Home>1</ext:Home>', $exported);
        $this->assertStringContainsString('<Away>0</Away>', $exported);
        $this->assertSame('10', $imported['data']['scores'][0]['value']);
    }

    private function competition(): Competition
    {
        $competition = new Competition([
            'competition_fifa_id' => 'JKL123D',
            'international_name' => 'Example League',
            'international_short_name' => 'EL',
            'organisation_fifa_id' => 'DEF456B',
            'organisation_international_short_name' => 'EXFA',
            'season' => 2026,
            'status' => 'active',
            'system_nature' => 'Round robin',
            'nature_fifa_id' => 'MNP456E',
            'nature_international_name' => 'League',
            'nature_international_short_name' => 'LG',
            'team_character' => 'Club',
            'discipline' => 'Football',
            'age_category' => 'Seniors',
        ]);

        $competition->setRelation('matches', new Collection());
        $competition->setRelation('elements', new Collection());
        $competition->setRelation('teams', new Collection());
        $competition->setRelation('parent', null);
        $competition->setRelation('picture', null);

        return $competition;
    }

    private function fullMatchTeam(
        string $nature,
        string $organisationFifaId,
        string $name
    ): MatchTeam {
        $team = new MatchTeam([
            'team_nature' => $nature,
            'organisation_fifa_id' =>
                $organisationFifaId,
            'international_name' => $name,
            'international_short_name' =>
                strtoupper(substr($name, 0, 4)),
        ]);

        $team->setRelation('players', new Collection());
        $team->setRelation(
            'teamOfficials',
            new Collection()
        );
        $team->setRelation('picture', null);

        return $team;
    }
}
