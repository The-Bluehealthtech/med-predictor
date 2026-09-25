<?php

namespace App\Services\FifaConnect;

use App\Models\FifaConnect\Address;
use App\Models\FifaConnect\CaseMatchEvent;
use App\Models\FifaConnect\Competition;
use App\Models\FifaConnect\CompetitionTeam;
use App\Models\FifaConnect\CompetitionTeamPerson;
use App\Models\FifaConnect\DisciplineCase;
use App\Models\FifaConnect\EventMessage;
use App\Models\FifaConnect\Facility;
use App\Models\FifaConnect\Field;
use App\Models\FifaConnect\MatchCompetitionContext;
use App\Models\FifaConnect\MatchEvent;
use App\Models\FifaConnect\MatchFacilityContext;
use App\Models\FifaConnect\MatchOfficial;
use App\Models\FifaConnect\MatchPhase;
use App\Models\FifaConnect\MatchPlayer;
use App\Models\FifaConnect\MatchRecord;
use App\Models\FifaConnect\MatchTeam;
use App\Models\FifaConnect\Organisation;
use App\Models\FifaConnect\OrganisationLocalName;
use App\Models\FifaConnect\OrganisationNationalIdentifier;
use App\Models\FifaConnect\Person;
use App\Models\FifaConnect\PersonLocalName;
use App\Models\FifaConnect\PersonNationalIdentifier;
use App\Models\FifaConnect\Picture;
use App\Models\FifaConnect\Registration;
use App\Models\FifaConnect\Sanction;
use App\Models\FifaConnect\SupportedDiscipline;
use App\Models\FifaConnect\TeamOfficial;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class CanonicalPersistenceService
{
    public function __construct(
        private readonly CanonicalXmlImporter $importer
    ) {
    }

    public function persistXml(string $xml): Model
    {
        $imported = $this->importer->import($xml);

        return DB::transaction(
            fn () => $this->persistImported(
                $imported,
                $xml
            )
        );
    }

    public function persistXmlBatch(string $xml): array
    {
        $importedItems = $this->importer->importMany($xml);

        return DB::transaction(
            fn () => array_map(
                fn (array $imported) => $this->persistImported(
                    $imported,
                    $xml
                ),
                $importedItems
            )
        );
    }

    private function persistImported(
        array $imported,
        string $xml
    ): Model {
        return match ($imported['type']) {
            'PersonLocal' =>
                $this->persistPerson($imported['data']),
            'OrganisationLocal' =>
                $this->persistOrganisation($imported['data']),
            'FacilityLocal' =>
                $this->persistFacility($imported['data']),
            'CompetitionInternational' =>
                $this->persistCompetition($imported['data']),
            'MatchInternational' =>
                $this->persistMatch($imported['data']),
            'Case' =>
                $this->persistCase($imported['data']),
            'EventMessage' =>
                $this->persistEventMessage(
                    $imported['data'],
                    $xml
                ),
            default => throw new RuntimeException(
                'Unsupported canonical FIFA payload.'
            ),
        };
    }

    private function persistPerson(array $data): Person
    {
        $person = Person::query()->firstOrNew([
            'person_fifa_id' => $data['person_fifa_id'],
        ]);

        $person->fill(Arr::only($data, [
            'international_first_name',
            'international_last_name',
            'popular_name',
            'local_first_name',
            'local_last_name',
            'local_birth_name',
            'local_system_ma_id',
            'local_language',
            'local_country',
            'gender',
            'nationality',
            'second_nationality',
            'date_of_birth',
            'country_of_birth',
            'region_of_birth',
            'place_of_birth',
        ]));
        $person->save();

        $this->syncPicture(
            'person',
            $person->id,
            $data['picture'] ?? null
        );

        PersonLocalName::query()
            ->where('person_id', $person->id)
            ->delete();

        foreach ($data['local_names'] as $name) {
            PersonLocalName::query()->create(
                $name + ['person_id' => $person->id]
            );
        }

        PersonNationalIdentifier::query()
            ->where('person_id', $person->id)
            ->delete();

        foreach ($data['national_identifiers'] as $identifier) {
            PersonNationalIdentifier::query()->create(
                $identifier + ['person_id' => $person->id]
            );
        }

        DB::table('fifa_connect_certifications')
            ->where('person_id', $person->id)
            ->delete();

        foreach ($data['certifications'] as $certification) {
            DB::table('fifa_connect_certifications')->insert(
                $certification + [
                    'person_id' => $person->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }

        $existingLicenseLinks = Registration::query()
            ->where('person_id', $person->id)
            ->get()
            ->mapWithKeys(
                fn (Registration $registration) => [
                    $this->registrationKey(
                        $registration->toArray()
                    ) => $registration->player_license_id,
                ]
            );

        Registration::query()
            ->where('person_id', $person->id)
            ->delete();

        foreach ($data['registrations'] as $registration) {
            $key = $this->registrationKey($registration);

            Registration::query()->create(
                $registration + [
                    'person_id' => $person->id,
                    'player_license_id' =>
                        $existingLicenseLinks->get($key),
                ]
            );
        }

        return $person->refresh();
    }

    private function persistOrganisation(
        array $data
    ): Organisation {
        if ($data['organisation_fifa_id']) {
            $organisation = Organisation::query()->firstOrNew([
                'organisation_fifa_id' =>
                    $data['organisation_fifa_id'],
            ]);
        } elseif ($data['local_system_ma_id']) {
            $organisation = Organisation::query()->firstOrNew([
                'local_system_ma_id' =>
                    $data['local_system_ma_id'],
                'local_country' => $data['local_country'],
            ]);
        } else {
            $organisation = new Organisation();
        }

        $organisation->fill(Arr::only($data, [
            'organisation_fifa_id',
            'status',
            'international_name',
            'international_short_name',
            'local_name',
            'local_short_name',
            'local_system_ma_id',
            'local_language',
            'local_country',
            'organisation_nature',
            'foundation_date',
            'dissolution_date',
            'parent_organisation_fifa_id',
            'web_address',
            'email',
            'phone',
            'fax',
        ]));
        $organisation->save();

        $this->syncPicture(
            'organisation',
            $organisation->id,
            $data['picture'] ?? null
        );

        OrganisationLocalName::query()
            ->where('organisation_id', $organisation->id)
            ->delete();

        foreach ($data['local_names'] as $name) {
            OrganisationLocalName::query()->create(
                $name + [
                    'organisation_id' => $organisation->id,
                ]
            );
        }

        OrganisationNationalIdentifier::query()
            ->where('organisation_id', $organisation->id)
            ->delete();

        foreach ($data['national_identifiers'] as $identifier) {
            OrganisationNationalIdentifier::query()->create(
                $identifier + [
                    'organisation_id' => $organisation->id,
                ]
            );
        }

        SupportedDiscipline::query()
            ->where('organisation_id', $organisation->id)
            ->delete();

        foreach ($data['supported_disciplines'] as $discipline) {
            SupportedDiscipline::query()->create(
                $discipline + [
                    'organisation_id' => $organisation->id,
                ]
            );
        }

        Address::query()
            ->where('owner_type', 'organisation')
            ->where('owner_id', $organisation->id)
            ->delete();

        if ($data['address']) {
            Address::query()->create(
                $data['address'] + [
                    'owner_type' => 'organisation',
                    'owner_id' => $organisation->id,
                ]
            );
        }

        return $organisation->refresh();
    }

    private function persistFacility(array $data): Facility
    {
        if ($data['facility_fifa_id']) {
            $facility = Facility::query()->firstOrNew([
                'facility_fifa_id' =>
                    $data['facility_fifa_id'],
            ]);
        } elseif ($data['local_system_ma_id']) {
            $facility = Facility::query()->firstOrNew([
                'local_system_ma_id' =>
                    $data['local_system_ma_id'],
                'local_country' => $data['local_country'],
            ]);
        } else {
            $facility = new Facility();
        }

        $facility->fill(Arr::only($data, [
            'facility_fifa_id',
            'status',
            'international_name',
            'international_short_name',
            'local_name',
            'local_short_name',
            'local_system_ma_id',
            'local_language',
            'local_country',
            'organisation_fifa_id',
            'parent_facility_fifa_id',
            'web_address',
            'email',
            'phone',
            'fax',
        ]));
        $facility->save();

        DB::table('fifa_connect_facility_local_names')
            ->where('facility_id', $facility->id)
            ->delete();

        foreach ($data['local_names'] as $name) {
            DB::table('fifa_connect_facility_local_names')->insert(
                $name + [
                    'facility_id' => $facility->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }

        Field::query()
            ->where('facility_id', $facility->id)
            ->delete();

        foreach ($data['fields'] as $field) {
            Field::query()->create(
                $field + ['facility_id' => $facility->id]
            );
        }

        Address::query()
            ->where('owner_type', 'facility')
            ->where('owner_id', $facility->id)
            ->delete();

        if ($data['address']) {
            Address::query()->create(
                $data['address'] + [
                    'owner_type' => 'facility',
                    'owner_id' => $facility->id,
                ]
            );
        }

        return $facility->refresh();
    }

    private function persistCompetition(
        array $data,
        ?int $parentId = null
    ): Competition {
        $competition = Competition::query()->firstOrNew([
            'competition_fifa_id' =>
                $data['competition_fifa_id'],
        ]);

        $competition->fill([
            'parent_competition_id' => $parentId,
            ...Arr::only($data, [
                'international_name',
                'international_short_name',
                'organisation_fifa_id',
                'season',
                'status',
                'order_number',
                'date_from',
                'date_to',
                'number_of_participants',
                'system_nature',
                'system_multiplier',
                'nature_fifa_id',
                'nature_international_name',
                'nature_international_short_name',
                'team_character',
                'discipline',
                'age_category',
                'age_category_name',
                'gender',
                'organisation_international_name',
                'organisation_international_short_name',
            ]),
        ]);
        $competition->save();

        $this->syncPicture(
            'competition',
            $competition->id,
            $data['picture'] ?? null
        );

        CompetitionTeam::query()
            ->where('competition_id', $competition->id)
            ->delete();

        foreach ($data['teams'] as $teamData) {
            $ranking = $teamData['ranking'] ?? [];

            $team = CompetitionTeam::query()->create([
                'competition_id' => $competition->id,
                ...Arr::only($teamData, [
                    'organisation_fifa_id',
                    'team_fifa_id',
                    'international_name',
                    'international_short_name',
                    'member_association',
                ]),
                'ranking_position' =>
                    $ranking['position'] ?? null,
                'ranking_matches_played' =>
                    $ranking['matches_played'] ?? null,
                'ranking_wins' =>
                    $ranking['wins'] ?? null,
                'ranking_draws' =>
                    $ranking['draws'] ?? null,
                'ranking_losses' =>
                    $ranking['losses'] ?? null,
                'ranking_goals_for' =>
                    $ranking['goals_for'] ?? null,
                'ranking_goals_against' =>
                    $ranking['goals_against'] ?? null,
                'ranking_goal_difference' =>
                    $ranking['goal_difference'] ?? null,
                'ranking_points' =>
                    $ranking['points'] ?? null,
                'ranking_negative_points' =>
                    $ranking['negative_points'] ?? null,
            ]);

            $this->syncPicture(
                'competition_team',
                $team->id,
                $teamData['picture'] ?? null
            );

            foreach (
                [
                    'players' => 'Player',
                    'team_officials' => 'TeamOfficial',
                ] as $list => $roleType
            ) {
                foreach ($teamData[$list] as $personData) {
                    $picture = $personData['picture'] ?? null;
                    unset($personData['picture']);

                    $person = CompetitionTeamPerson::query()
                        ->create([
                            'competition_team_id' => $team->id,
                            'role_type' => $roleType,
                            ...$personData,
                        ]);

                    $this->syncPicture(
                        'competition_team_person',
                        $person->id,
                        $picture
                    );
                }
            }
        }

        foreach ($data['matches'] as $matchData) {
            $match = MatchRecord::query()->firstOrNew([
                'match_fifa_id' =>
                    $matchData['match_fifa_id'],
            ]);

            $match->fill([
                'competition_fifa_id' =>
                    $competition->competition_fifa_id,
                'status' => $matchData['status'],
                'date_time_local' =>
                    $matchData['date_time_local'],
                'matchday' => $matchData['matchday'],
                'facility_fifa_id' =>
                    $matchData['facility_fifa_id'],
                'facility_international_short_name' =>
                    $matchData[
                        'facility_international_short_name'
                    ],
            ]);
            $match->save();

            $this->upsertSimpleMatchTeam(
                $match,
                'Home',
                $matchData[
                    'home_team_international_short_name'
                ],
                $matchData['home_team_fifa_id'],
                $matchData['home_final_result']
            );

            $this->upsertSimpleMatchTeam(
                $match,
                'Away',
                $matchData[
                    'away_team_international_short_name'
                ],
                $matchData['away_team_fifa_id'],
                $matchData['away_final_result']
            );
        }

        $importedElementIds = [];

        foreach ($data['elements'] as $elementData) {
            $element = $this->persistCompetition(
                $elementData,
                $competition->id
            );
            $importedElementIds[] =
                $element->competition_fifa_id;
        }

        $staleElements = Competition::query()
            ->where(
                'parent_competition_id',
                $competition->id
            );

        if ($importedElementIds) {
            $staleElements->whereNotIn(
                'competition_fifa_id',
                $importedElementIds
            );
        }

        $staleElements->update([
            'parent_competition_id' => null,
        ]);

        return $competition->refresh();
    }

    private function persistMatch(array $data): MatchRecord
    {
        $competition = $data['competition'];

        if (!$competition) {
            throw new RuntimeException(
                'Validated MatchInternational has no Competition.'
            );
        }

        $facility = $data['facility'];

        $match = MatchRecord::query()->firstOrNew([
            'match_fifa_id' => $data['match_fifa_id'],
        ]);

        $match->fill([
            'status' => $data['status'],
            'date_time_local' => $data['date_time_local'],
            'matchday' => $data['matchday'],
            'attendance' => $data['attendance'],
            'competition_fifa_id' =>
                $competition['competition_fifa_id'],
            'facility_fifa_id' =>
                $facility['facility_fifa_id'] ?? null,
        ]);
        $match->save();

        MatchCompetitionContext::query()
            ->where('match_id', $match->id)
            ->delete();

        $this->createMatchCompetitionContext(
            $match,
            $competition,
            0
        );

        MatchFacilityContext::query()
            ->where('match_id', $match->id)
            ->delete();

        if ($facility) {
            MatchFacilityContext::query()->create([
                'match_id' => $match->id,
                ...$facility,
            ]);
        }

        MatchPhase::query()
            ->where('match_id', $match->id)
            ->delete();

        foreach ($data['phases'] as $phase) {
            MatchPhase::query()->create(
                $phase + ['match_id' => $match->id]
            );
        }

        MatchEvent::query()
            ->where('match_id', $match->id)
            ->whereNull('event_id')
            ->delete();

        foreach ($data['events'] as $event) {
            MatchEvent::query()->create(
                $event + ['match_id' => $match->id]
            );
        }

        MatchOfficial::query()
            ->where('match_id', $match->id)
            ->delete();

        foreach ($data['officials'] as $official) {
            $picture = $official['picture'] ?? null;
            unset($official['picture']);

            $model = MatchOfficial::query()->create(
                $official + ['match_id' => $match->id]
            );

            if ($picture) {
                $this->syncPicture(
                    'match_official',
                    $model->id,
                    $picture
                );
            }
        }

        MatchTeam::query()
            ->where('match_id', $match->id)
            ->delete();

        foreach ($data['teams'] as $teamData) {
            $players = $teamData['players'];
            $officials = $teamData['team_officials'];
            $picture = $teamData['picture'] ?? null;

            unset(
                $teamData['players'],
                $teamData['team_officials'],
                $teamData['picture']
            );

            $team = MatchTeam::query()->create(
                $teamData + ['match_id' => $match->id]
            );

            $this->syncPicture(
                'match_team',
                $team->id,
                $picture
            );

            foreach ($players as $playerData) {
                $playerPicture =
                    $playerData['picture'] ?? null;
                unset($playerData['picture']);

                $player = MatchPlayer::query()->create(
                    $playerData + [
                        'match_team_id' => $team->id,
                    ]
                );

                if ($playerPicture) {
                    $this->syncPicture(
                        'match_player',
                        $player->id,
                        $playerPicture
                    );
                }
            }

            foreach ($officials as $officialData) {
                $officialPicture =
                    $officialData['picture'] ?? null;
                unset($officialData['picture']);

                $official = TeamOfficial::query()->create(
                    $officialData + [
                        'match_team_id' => $team->id,
                    ]
                );

                if ($officialPicture) {
                    $this->syncPicture(
                        'team_official',
                        $official->id,
                        $officialPicture
                    );
                }
            }
        }

        return $match->refresh();
    }

    private function persistCase(array $data): DisciplineCase
    {
        $case = DisciplineCase::query()->firstOrNew([
            'case_fifa_id' => $data['case_fifa_id'],
        ]);

        $case->fill(Arr::except($data, [
            'match_event',
            'sanctions',
        ]));
        $case->match_event_id = null;
        $case->save();

        Sanction::query()
            ->where('case_id', $case->id)
            ->delete();

        foreach ($data['sanctions'] as $sanction) {
            Sanction::query()->create(
                $sanction + ['case_id' => $case->id]
            );
        }

        CaseMatchEvent::query()
            ->where('case_id', $case->id)
            ->delete();

        if ($data['match_event']) {
            CaseMatchEvent::query()->create(
                $data['match_event'] + [
                    'case_id' => $case->id,
                ]
            );
        }

        return $case->refresh();
    }

    private function persistEventMessage(
        array $data,
        string $xml
    ): EventMessage {
        $event = $data['event'];

        $message = EventMessage::query()->create([
            'message_nature' => $data['message_nature'],
            'match_fifa_id' => $data['match_fifa_id'],
            'export_date_time' =>
                $data['export_date_time'],
            ...$event,
        ]);

        foreach ($data['scores'] as $index => $score) {
            $message->scores()->create([
                'order_number' => $index + 1,
                'value' => is_array($score) ? $score['value'] : $score,
                'xml_fragment' => is_array($score) ? $score['xml_fragment'] : null,
            ]);
        }

        DB::table('fifa_connect_exchange_messages')
            ->insert([
                'scenario_type' => 'EventMessage',
                'message_nature' =>
                    $data['message_nature'],
                'event_id' => $event['event_id'],
                'subject_fifa_id' =>
                    $data['match_fifa_id'],
                'exported_at' =>
                    $data['export_date_time'],
                'direction' => 'inbound',
                'validation_status' => 'valid',
                'validation_error' => null,
                'payload_hash' => hash('sha256', $xml),
                'payload_xml' => $xml,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

        $match = MatchRecord::query()
            ->where(
                'match_fifa_id',
                $data['match_fifa_id']
            )
            ->first();

        if ($match) {
            $query = MatchEvent::query()
                ->where('match_id', $match->id)
                ->where('event_id', $event['event_id']);

            if ($data['message_nature'] === 'Delete') {
                $query->delete();
            } else {
                MatchEvent::query()->updateOrCreate(
                    [
                        'match_id' => $match->id,
                        'event_id' => $event['event_id'],
                    ],
                    Arr::except($event, ['event_id'])
                );
            }
        }

        return $message->refresh();
    }

    private function createMatchCompetitionContext(
        MatchRecord $match,
        array $data,
        int $depth
    ): MatchCompetitionContext {
        $parent = null;

        if ($data['parent']) {
            $parent = $this->createMatchCompetitionContext(
                $match,
                $data['parent'],
                $depth + 1
            );
        }

        $picture = $data['picture'] ?? null;

        $context = MatchCompetitionContext::query()
            ->create([
                'match_id' => $match->id,
                'parent_context_id' => $parent?->id,
                'depth' => $depth,
                ...Arr::only($data, [
                    'competition_fifa_id',
                    'international_name',
                    'international_short_name',
                    'organisation_international_name',
                    'organisation_international_short_name',
                ]),
            ]);

        $this->syncPicture(
            'match_competition_context',
            $context->id,
            $picture
        );

        return $context;
    }

    private function upsertSimpleMatchTeam(
        MatchRecord $match,
        string $nature,
        ?string $shortName,
        ?string $teamFifaId,
        ?string $finalResult
    ): void {
        MatchTeam::query()->updateOrCreate(
            [
                'match_id' => $match->id,
                'team_nature' => $nature,
            ],
            [
                'team_fifa_id' => $teamFifaId,
                'international_short_name' => $shortName,
                'final_result' => $finalResult,
            ]
        );
    }

    private function syncPicture(
        string $ownerType,
        int $ownerId,
        ?array $picture
    ): void {
        Picture::query()
            ->where('owner_type', $ownerType)
            ->where('owner_id', $ownerId)
            ->delete();

        if (!$picture) {
            return;
        }

        Picture::query()->create(
            $picture + [
                'owner_type' => $ownerType,
                'owner_id' => $ownerId,
            ]
        );
    }

    private function registrationKey(array $registration): string
    {
        return implode('|', [
            $registration['registration_type'] ?? '',
            $registration['organisation_fifa_id'] ?? '',
            $registration['registration_valid_from'] ?? '',
        ]);
    }
}
