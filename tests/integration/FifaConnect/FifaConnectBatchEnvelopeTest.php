<?php

namespace Tests\Integration\FifaConnect;

use App\Models\FifaConnect\Competition;
use App\Models\FifaConnect\MatchRecord;
use App\Models\FifaConnect\MatchTeam;
use App\Models\FifaConnect\Person;
use App\Services\FifaConnect\CanonicalPersistenceService;
use App\Services\FifaConnect\CanonicalXmlImporter;
use App\Services\FifaConnect\CompetitionInternationalXmlSerializer;
use App\Services\FifaConnect\MatchInternationalXmlSerializer;
use App\Services\FifaConnect\PersonDataXmlSerializer;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class FifaConnectBatchEnvelopeTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();

        foreach ([
            'fifa_connect_competitions',
            'fifa_connect_matches',
            'fifa_connect_persons',
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

    public function test_person_data_envelope_imports_and_persists_multiple_items(): void
    {
        $xml = app(PersonDataXmlSerializer::class)->serialize(
            collect([
                $this->person('ABC123A', 'Dupont'),
                $this->person('DEF456B', 'Martin'),
            ]),
            Carbon::parse('2026-09-25'),
            true
        );
        $items = app(CanonicalXmlImporter::class)->importMany($xml);
        $this->assertSame(
            ['ABC123A', 'DEF456B'],
            array_column(array_column($items, 'data'), 'person_fifa_id')
        );
        $stored = app(CanonicalPersistenceService::class)->persistXmlBatch($xml);
        $this->assertCount(2, $stored);
        $this->assertDatabaseHas('fifa_connect_persons', ['person_fifa_id' => 'ABC123A']);
        $this->assertDatabaseHas('fifa_connect_persons', ['person_fifa_id' => 'DEF456B']);
    }

    public function test_competition_data_envelope_imports_and_persists_multiple_items(): void
    {
        $first = $this->competition(
            'AAA111A',
            'League One'
        );
        $second = $this->competition(
            'BBB222B',
            'League Two',
            'CCC333C',
            'DDD444D'
        );

        $xml = app(
            CompetitionInternationalXmlSerializer::class
        )->serialize(
            collect([$first, $second]),
            true
        );

        $items = app(CanonicalXmlImporter::class)
            ->importMany($xml);

        $this->assertCount(2, $items);
        $this->assertSame(
            ['AAA111A', 'BBB222B'],
            array_column(
                array_column($items, 'data'),
                'competition_fifa_id'
            )
        );

        $stored = app(CanonicalPersistenceService::class)
            ->persistXmlBatch($xml);

        $this->assertCount(2, $stored);
        $this->assertDatabaseHas(
            'fifa_connect_competitions',
            ['competition_fifa_id' => 'AAA111A']
        );
        $this->assertDatabaseHas(
            'fifa_connect_competitions',
            ['competition_fifa_id' => 'BBB222B']
        );
    }

    public function test_match_data_envelope_imports_and_persists_multiple_items(): void
    {
        $competition = $this->competition(
            'AAA111A',
            'League One'
        );

        $first = $this->match(
            'EEE555E',
            $competition,
            'BBB222B',
            'CCC333C'
        );
        $second = $this->match(
            'FFF666F',
            $competition,
            'DDD444D',
            'GGG777G'
        );

        $xml = app(
            MatchInternationalXmlSerializer::class
        )->serialize(
            collect([$first, $second]),
            true
        );

        $items = app(CanonicalXmlImporter::class)
            ->importMany($xml);

        $this->assertCount(2, $items);
        $this->assertSame(
            ['EEE555E', 'FFF666F'],
            array_column(
                array_column($items, 'data'),
                'match_fifa_id'
            )
        );

        $stored = app(CanonicalPersistenceService::class)
            ->persistXmlBatch($xml);

        $this->assertCount(2, $stored);
        $this->assertDatabaseHas(
            'fifa_connect_matches',
            ['match_fifa_id' => 'EEE555E']
        );
        $this->assertDatabaseHas(
            'fifa_connect_matches',
            ['match_fifa_id' => 'FFF666F']
        );
    }

    private function person(string $id, string $name): Person
    {
        $person = new Person([
            'person_fifa_id' => $id,
            'international_first_name' => 'Jean',
            'international_last_name' => $name,
            'local_last_name' => $name,
            'local_language' => 'fre',
            'local_country' => 'FR',
            'gender' => 'male',
            'nationality' => 'FR',
            'date_of_birth' => Carbon::parse('2000-01-02'),
            'country_of_birth' => 'FR',
            'place_of_birth' => 'Paris',
        ]);
        foreach (['picture', 'localNames', 'nationalIdentifiers', 'registrations', 'certifications'] as $relation) {
            $person->setRelation($relation, $relation === 'picture' ? null : new Collection());
        }
        return $person;
    }

    private function competition(
        string $id,
        string $name,
        string $organisationId = 'HHH888H',
        string $natureId = 'JJJ999J'
    ): Competition {
        $competition = new Competition([
            'competition_fifa_id' => $id,
            'international_name' => $name,
            'international_short_name' => substr($name, 0, 8),
            'organisation_fifa_id' => $organisationId,
            'organisation_international_short_name' => 'FA',
            'season' => 2026,
            'status' => 'active',
            'system_nature' => 'Round robin',
            'nature_fifa_id' => $natureId,
            'nature_international_short_name' => 'SEN',
            'team_character' => 'Club',
            'discipline' => 'Football',
            'age_category' => 'Seniors',
        ]);

        $competition->setRelation('picture', null);
        $competition->setRelation('matches', new Collection());
        $competition->setRelation('elements', new Collection());
        $competition->setRelation('teams', new Collection());
        $competition->setRelation('parent', null);

        return $competition;
    }

    private function match(
        string $id,
        Competition $competition,
        string $homeOrganisation,
        string $awayOrganisation
    ): MatchRecord {
        $match = new MatchRecord([
            'match_fifa_id' => $id,
            'status' => 'Scheduled',
            'competition_fifa_id' =>
                $competition->competition_fifa_id,
        ]);

        $match->setRelation('competition', $competition);
        $match->setRelation('competitionContext', null);
        $match->setRelation('facility', null);
        $match->setRelation('facilityContext', null);
        $match->setRelation('phases', new Collection());
        $match->setRelation('events', new Collection());
        $match->setRelation('officials', new Collection());
        $match->setRelation(
            'teams',
            new Collection([
                $this->team(
                    'Home',
                    $homeOrganisation,
                    'Home Team'
                ),
                $this->team(
                    'Away',
                    $awayOrganisation,
                    'Away Team'
                ),
            ])
        );

        return $match;
    }

    private function team(
        string $nature,
        string $organisationId,
        string $name
    ): MatchTeam {
        $team = new MatchTeam([
            'team_nature' => $nature,
            'organisation_fifa_id' => $organisationId,
            'international_name' => $name,
        ]);

        $team->setRelation('picture', null);
        $team->setRelation('players', new Collection());
        $team->setRelation('officials', new Collection());

        return $team;
    }
}
