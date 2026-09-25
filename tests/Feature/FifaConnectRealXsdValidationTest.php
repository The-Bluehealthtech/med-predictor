<?php

namespace Tests\Feature;

use App\Models\FifaConnect\Address;
use App\Models\FifaConnect\Competition;
use App\Models\FifaConnect\CompetitionTeam;
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
use App\Models\FifaConnect\SupportedDiscipline;
use App\Services\FifaConnect\CompetitionInternationalXmlSerializer;
use App\Services\FifaConnect\DisciplineCaseXmlSerializer;
use App\Services\FifaConnect\EventMessageXmlSerializer;
use App\Services\FifaConnect\FacilityLocalXmlSerializer;
use App\Services\FifaConnect\MatchInternationalXmlSerializer;
use App\Services\FifaConnect\OrganisationLocalXmlSerializer;
use App\Services\FifaConnect\PersonLocalXmlSerializer;
use App\Services\FifaConnect\SchemaCatalog;
use App\Services\FifaConnect\XsdValidator;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Tests\TestCase;

class FifaConnectRealXsdValidationTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $base = config('services.fifa_connect.xsd_path');

        if (!is_file($base . '/scenarios.xsd')) {
            $this->markTestSkipped(
                'FIFA Connect Data 3.3 XSD bundle is not installed.'
            );
        }
    }

    public function test_person_local_validates_against_real_scenarios_xsd(): void
    {
        $person = new Person([
            'person_fifa_id' => 'ABC123A',
            'international_first_name' => 'Jean',
            'international_last_name' => 'Dupont',
            'local_first_name' => 'Jean',
            'local_last_name' => 'Dupont',
            'local_language' => 'fra',
            'local_country' => 'FR',
            'gender' => 'male',
            'nationality' => 'FR',
            'date_of_birth' => Carbon::parse('2000-01-02'),
            'country_of_birth' => 'FR',
            'place_of_birth' => 'Paris',
        ]);

        $registration = new Registration([
            'person_fifa_id' => 'ABC123A',
            'organisation_fifa_id' => 'DEF456B',
            'registration_type' => Registration::TYPE_PLAYER,
            'status' => 'active',
            'registration_valid_from' => Carbon::parse('2026-01-01'),
            'level' => 'pro',
            'discipline' => 'Football',
            'registration_nature' => 'Registration',
        ]);

        $person->setRelation('localNames', new Collection());
        $person->setRelation('nationalIdentifiers', new Collection());
        $person->setRelation(
            'registrations',
            new Collection([$registration])
        );
        $person->setRelation('certifications', new Collection());

        $xml = $this->personSerializer()->serialize($person, true);

        $this->assertStringContainsString('<PersonLocal', $xml);
    }

    public function test_organisation_local_validates_against_real_scenarios_xsd(): void
    {
        $organisation = new Organisation([
            'organisation_fifa_id' => 'ABC123A',
            'status' => 'active',
            'local_name' => 'Club Exemple',
            'local_language' => 'fra',
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
            new Collection([
                new SupportedDiscipline([
                    'discipline' => 'Football',
                    'gender' => 'male',
                ]),
            ])
        );
        $organisation->setRelation(
            'addresses',
            new Collection([$this->address('organisation')])
        );

        $xml = $this->organisationSerializer()->serialize(
            $organisation,
            true
        );

        $this->assertStringContainsString(
            '<OrganisationLocal',
            $xml
        );
    }

    public function test_facility_local_validates_against_real_scenarios_xsd(): void
    {
        $facility = new Facility([
            'facility_fifa_id' => 'ABC123A',
            'status' => 'active',
            'local_name' => 'Stade Exemple',
            'local_language' => 'fra',
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
                    'capacity' => 1000,
                    'ground_nature' => 'grass',
                    'length' => 105,
                    'width' => 68,
                ]),
            ])
        );
        $facility->setRelation(
            'addresses',
            new Collection([$this->address('facility')])
        );

        $xml = $this->facilitySerializer()->serialize(
            $facility,
            true
        );

        $this->assertStringContainsString('<FacilityLocal', $xml);
    }

    public function test_competition_international_validates_against_real_scenarios_xsd(): void
    {
        $competition = $this->competition();

        $team = new CompetitionTeam([
            'organisation_fifa_id' => 'BBB222B',
            'team_fifa_id' => 'CCC333C',
            'international_name' => 'Example FC',
            'international_short_name' => 'EFC',
        ]);
        $team->setRelation('persons', new Collection());

        $competition->setRelation('elements', new Collection());
        $competition->setRelation('teams', new Collection([$team]));

        $xml = $this->competitionSerializer()->serialize(
            $competition,
            true
        );

        $this->assertStringContainsString(
            '<CompetitionInternationalData',
            $xml
        );
    }

    public function test_match_international_validates_against_real_scenarios_xsd(): void
    {
        $competition = $this->competition();
        $competition->setRelation('parent', null);

        $match = new MatchRecord([
            'match_fifa_id' => 'EEE555E',
            'status' => 'Scheduled',
            'competition_fifa_id' => 'AAA111A',
        ]);

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

        $xml = $this->matchSerializer()->serialize($match, true);

        $this->assertStringContainsString(
            '<MatchInternationalData',
            $xml
        );
    }

    public function test_discipline_case_validates_against_real_discipline_xsd(): void
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

        $case->setRelation(
            'sanctions',
            new Collection([
                new Sanction([
                    'status' => 'active',
                    'person_sanction_nature' => 'MatchSuspension',
                    'value' => 2,
                    'measure' => 'Matches',
                ]),
            ])
        );

        $xml = $this->disciplineSerializer()->serialize(
            $case,
            true
        );

        $this->assertStringContainsString('<Case ', $xml);
    }

    public function test_event_message_validates_against_real_scenarios_xsd(): void
    {
        $event = new MatchEvent([
            'event_id' => 'evt-1',
            'match_phase' => '1H',
            'minute' => 15,
            'event_type' => 'Goal',
            'player_fifa_id' => 'CCC333C',
            'match_team' => 'Home',
            'player_shirt_number' => 9,
        ]);

        $xml = $this->eventSerializer()->serialize(
            $event,
            'Insert',
            'EEE555E',
            Carbon::parse('2026-09-25'),
            ['1-0'],
            true
        );

        $this->assertStringContainsString(
            'MessageNature="Insert"',
            $xml
        );
    }

    private function competition(): Competition
    {
        return new Competition([
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

    private function address(string $ownerType): Address
    {
        return new Address([
            'owner_type' => $ownerType,
            'country' => 'FR',
            'region' => 'IDF',
            'postal_code' => '75001',
            'town' => 'Paris',
            'address' => '1 rue Exemple',
        ]);
    }

    private function personSerializer(): PersonLocalXmlSerializer
    {
        return new PersonLocalXmlSerializer(
            new SchemaCatalog(),
            new XsdValidator()
        );
    }

    private function organisationSerializer(): OrganisationLocalXmlSerializer
    {
        return new OrganisationLocalXmlSerializer(
            new SchemaCatalog(),
            new XsdValidator()
        );
    }

    private function facilitySerializer(): FacilityLocalXmlSerializer
    {
        return new FacilityLocalXmlSerializer(
            new SchemaCatalog(),
            new XsdValidator()
        );
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
}
