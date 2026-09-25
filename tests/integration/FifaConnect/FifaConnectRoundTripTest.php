<?php

namespace Tests\Integration\FifaConnect;

use App\Models\FifaConnect\Address;
use App\Models\FifaConnect\Certification;
use App\Models\FifaConnect\Competition;
use App\Models\FifaConnect\DisciplineCase;
use App\Models\FifaConnect\Facility;
use App\Models\FifaConnect\Field;
use App\Models\FifaConnect\MatchEvent;
use App\Models\FifaConnect\MatchRecord;
use App\Models\FifaConnect\MatchTeam;
use App\Models\FifaConnect\Organisation;
use App\Models\FifaConnect\Person;
use App\Models\FifaConnect\Registration;
use App\Models\FifaConnect\Sanction;
use App\Services\FifaConnect\CanonicalXmlImporter;
use App\Services\FifaConnect\CompetitionInternationalXmlSerializer;
use App\Services\FifaConnect\DisciplineCaseXmlSerializer;
use App\Services\FifaConnect\EventMessageXmlSerializer;
use App\Services\FifaConnect\FacilityLocalXmlSerializer;
use App\Services\FifaConnect\MatchInternationalXmlSerializer;
use App\Services\FifaConnect\OrganisationLocalXmlSerializer;
use App\Services\FifaConnect\PersonLocalXmlSerializer;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Tests\TestCase;

class FifaConnectRoundTripTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $validationPath = rtrim(
            (string) config(
                'services.fifa_connect.xsd_validation_path'
            ),
            DIRECTORY_SEPARATOR
        );

        if (!is_file($validationPath . '/scenarios.xsd')) {
            $this->markTestSkipped(
                'FIFA validation bundle is not installed.'
            );
        }
    }

    public function test_person_local_round_trip_with_certifications_and_all_registration_types(): void
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

        $person->setRelation('localNames', new Collection());
        $person->setRelation('nationalIdentifiers', new Collection());

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
                new Certification([
                    'certification_type' => 'TeamOfficial',
                    'status' => 'active',
                    'certification_valid_from' =>
                        Carbon::parse('2026-01-01'),
                    'certification_nature' => 'UEFA PRO',
                    'description' => 'Coach certification',
                ]),
            ])
        );

        $base = [
            'person_fifa_id' => 'ABC123A',
            'organisation_fifa_id' => 'DEF456B',
            'status' => 'active',
            'registration_valid_from' =>
                Carbon::parse('2026-01-01'),
        ];

        $person->setRelation(
            'registrations',
            new Collection([
                new Registration($base + [
                    'registration_type' =>
                        Registration::TYPE_ORGANISATION_OFFICIAL,
                    'organisation_official_role' => 'President',
                ]),
                new Registration($base + [
                    'registration_type' =>
                        Registration::TYPE_MATCH_OFFICIAL,
                    'match_official_role' => 'Referee',
                    'discipline' => 'Football',
                ]),
                new Registration($base + [
                    'registration_type' =>
                        Registration::TYPE_TEAM_OFFICIAL,
                    'team_official_role' => 'Coach',
                    'discipline' => 'Football',
                ]),
                new Registration($base + [
                    'registration_type' =>
                        Registration::TYPE_PLAYER,
                    'level' => 'pro',
                    'discipline' => 'Football',
                    'registration_nature' => 'Registration',
                    'club_training_category' => 'Category1',
                ]),
            ])
        );

        $xml = app(PersonLocalXmlSerializer::class)
            ->serialize($person, true);

        $imported = app(CanonicalXmlImporter::class)
            ->import($xml);

        $this->assertSame('PersonLocal', $imported['type']);
        $this->assertSame(
            'ABC123A',
            $imported['data']['person_fifa_id']
        );
        $this->assertCount(
            2,
            $imported['data']['certifications']
        );
        $this->assertCount(
            4,
            $imported['data']['registrations']
        );
        $this->assertSame(
            [
                'Player',
                'TeamOfficial',
                'MatchOfficial',
                'OrganisationOfficial',
            ],
            array_column(
                $imported['data']['registrations'],
                'registration_type'
            )
        );

        $this->assertLessThan(
            strpos($xml, '<TeamOfficialCertificate'),
            strpos($xml, '<MatchOfficialCertificate')
        );
        $this->assertLessThan(
            strpos($xml, '<PlayerRegistration'),
            strpos($xml, '<TeamOfficialCertificate')
        );
    }

    public function test_organisation_and_facility_round_trip(): void
    {
        $organisation = new Organisation([
            'organisation_fifa_id' => 'DEF456B',
            'status' => 'active',
            'local_name' => 'Club Exemple',
            'local_language' => 'fre',
            'local_country' => 'FR',
            'international_name' => 'Example Club',
            'organisation_nature' => 'Club',
        ]);
        $organisation->setRelation('localNames', new Collection());
        $organisation->setRelation(
            'nationalIdentifiers',
            new Collection()
        );
        $organisation->setRelation(
            'supportedDisciplines',
            new Collection()
        );
        $organisation->setRelation(
            'addresses',
            new Collection([
                new Address([
                    'owner_type' => 'organisation',
                    'country' => 'FR',
                    'town' => 'Paris',
                    'address' => '1 rue Exemple',
                ]),
            ])
        );

        $orgXml = app(OrganisationLocalXmlSerializer::class)
            ->serialize($organisation, true);
        $orgImported = app(CanonicalXmlImporter::class)
            ->import($orgXml);

        $this->assertSame(
            'DEF456B',
            $orgImported['data']['organisation_fifa_id']
        );
        $this->assertSame(
            'FR',
            $orgImported['data']['address']['country']
        );

        $facility = new Facility([
            'facility_fifa_id' => 'GHI789C',
            'status' => 'active',
            'local_name' => 'Stade Exemple',
            'local_language' => 'fre',
            'local_country' => 'FR',
            'international_name' => 'Example Stadium',
        ]);
        $facility->setRelation('localNames', new Collection());
        $facility->setRelation(
            'fields',
            new Collection([
                new Field([
                    'order_number' => 1,
                    'discipline' => 'Football',
                    'capacity' => 1200,
                    'ground_nature' => 'grass',
                ]),
            ])
        );
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

        $facilityXml = app(FacilityLocalXmlSerializer::class)
            ->serialize($facility, true);
        $facilityImported = app(CanonicalXmlImporter::class)
            ->import($facilityXml);

        $this->assertSame(
            'GHI789C',
            $facilityImported['data']['facility_fifa_id']
        );
        $this->assertCount(
            1,
            $facilityImported['data']['fields']
        );
    }

    public function test_competition_and_match_round_trip(): void
    {
        $competition = $this->competition();

        $competitionXml =
            app(CompetitionInternationalXmlSerializer::class)
                ->serialize($competition, true);

        $competitionImported =
            app(CanonicalXmlImporter::class)
                ->import($competitionXml);

        $this->assertSame(
            'JKL123D',
            $competitionImported['data']['competition_fifa_id']
        );
        $this->assertSame(
            'Football',
            $competitionImported['data']['discipline']
        );

        $match = new MatchRecord([
            'match_fifa_id' => 'QRS789F',
            'status' => 'Scheduled',
            'competition_fifa_id' => 'JKL123D',
            'matchday' => 1,
        ]);
        $match->setRelation('competition', $competition);
        $match->setRelation('facility', null);
        $match->setRelation('phases', new Collection());
        $match->setRelation('events', new Collection());
        $match->setRelation('officials', new Collection());

        $home = new MatchTeam([
            'team_nature' => 'Home',
            'organisation_fifa_id' => 'TUV123G',
            'international_name' => 'Home Club',
            'international_short_name' => 'HOME',
        ]);
        $home->setRelation('players', new Collection());
        $home->setRelation('teamOfficials', new Collection());

        $away = new MatchTeam([
            'team_nature' => 'Away',
            'organisation_fifa_id' => 'WXY456H',
            'international_name' => 'Away Club',
            'international_short_name' => 'AWAY',
        ]);
        $away->setRelation('players', new Collection());
        $away->setRelation('teamOfficials', new Collection());

        $match->setRelation(
            'teams',
            new Collection([$home, $away])
        );

        $matchXml = app(MatchInternationalXmlSerializer::class)
            ->serialize($match, true);
        $matchImported = app(CanonicalXmlImporter::class)
            ->import($matchXml);

        $this->assertSame(
            'QRS789F',
            $matchImported['data']['match_fifa_id']
        );
        $this->assertCount(
            2,
            $matchImported['data']['teams']
        );
        $this->assertSame(
            ['Home', 'Away'],
            array_column(
                $matchImported['data']['teams'],
                'team_nature'
            )
        );
    }

    public function test_discipline_and_event_message_round_trip(): void
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
        $case->setRelation('matchEvent', null);
        $case->setRelation(
            'sanctions',
            new Collection([
                new Sanction([
                    'status' => 'active',
                    'person_sanction_nature' => 'Warning',
                ]),
            ])
        );

        $caseXml = app(DisciplineCaseXmlSerializer::class)
            ->serialize($case, true);
        $caseImported = app(CanonicalXmlImporter::class)
            ->import($caseXml);

        $this->assertSame(
            'ZAB789J',
            $caseImported['data']['case_fifa_id']
        );
        $this->assertCount(
            1,
            $caseImported['data']['sanctions']
        );

        $event = new MatchEvent([
            'event_id' => 'EVT-1',
            'match_phase' => '1H',
            'minute' => 10,
            'event_type' => 'Goal',
            'match_team' => 'Home',
            'player_fifa_id' => 'ABC123A',
        ]);

        $eventXml = app(EventMessageXmlSerializer::class)
            ->serialize(
                $event,
                'Insert',
                'QRS789F',
                Carbon::parse('2026-09-25'),
                [1, 0],
                true
            );

        $eventImported = app(CanonicalXmlImporter::class)
            ->import($eventXml);

        $this->assertSame(
            'Insert',
            $eventImported['data']['message_nature']
        );
        $this->assertSame(
            ['1', '0'],
            $eventImported['data']['scores']
        );
        $this->assertSame(
            'Goal',
            $eventImported['data']['event']['event_type']
        );
    }

    public function test_complex_event_score_is_preserved_by_import(): void
    {
        $event = new MatchEvent([
            'event_id' => 'EVT-2',
            'match_phase' => '1H',
            'minute' => 10,
            'event_type' => 'Goal',
            'match_team' => 'Home',
        ]);

        $xml = app(EventMessageXmlSerializer::class)->serialize(
            $event, 'Insert', 'QRS789F', Carbon::parse('2026-09-25'), [1]
        );
        $xml = str_replace('<Score>1</Score>', '<Score period="first">1</Score>', $xml);

        $imported = app(CanonicalXmlImporter::class)->import($xml);
        $this->assertSame('1', $imported['data']['scores'][0]['value']);
        $this->assertStringContainsString('period="first"', $imported['data']['scores'][0]['xml_fragment']);
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

        return $competition;
    }
}
