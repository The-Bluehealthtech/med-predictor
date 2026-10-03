<?php

namespace App\Services\ClubOfficials;

use App\Models\Club;
use App\Models\ClubOfficial;
use App\Models\FifaConnect\Registration;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

final class SyncFifaConnectOfficials
{
    public function sync(Club $club, User $actor): array
    {
        if (!Schema::hasTable('fifa_connect_registrations') || !Schema::hasTable('fifa_connect_persons')) {
            return ['created'=>0,'updated'=>0,'skipped'=>0,'available'=>false];
        }

        $organisationIds = collect([$club->fifa_connect_id ?? null, $club->fifa_club_id ?? null])
            ->filter()->map(fn ($id) => (string) $id)->unique()->values();
        if ($organisationIds->isEmpty()) {
            return ['created'=>0,'updated'=>0,'skipped'=>0,'available'=>true,'missing_club_fifa_id'=>true];
        }

        $registrations = Registration::query()
            ->with('person')
            ->whereIn('organisation_fifa_id', $organisationIds)
            ->whereIn('registration_type', [Registration::TYPE_TEAM_OFFICIAL, Registration::TYPE_ORGANISATION_OFFICIAL])
            ->where('status', 'active')
            ->get();

        $created = $updated = $skipped = 0;

        DB::transaction(function () use ($registrations, $club, $actor, &$created, &$updated, &$skipped) {
            foreach ($registrations as $registration) {
                $person = $registration->person;
                $role = $registration->registration_type === Registration::TYPE_TEAM_OFFICIAL
                    ? $registration->team_official_role
                    : $registration->organisation_official_role;

                if (!$person || blank($person->person_fifa_id) || blank($role)
                    || blank($person->international_first_name) || blank($person->international_last_name)
                    || blank($person->gender) || blank($registration->registration_valid_from)) {
                    $skipped++;
                    continue;
                }

                $official = ClubOfficial::query()
                    ->where('club_id', $club->id)
                    ->where('person_fifa_id', $person->person_fifa_id)
                    ->where('registration_type', $registration->registration_type)
                    ->first();
                $exists = (bool) $official;
                $official ??= new ClubOfficial;
                $official->club_id = $club->id;

                $official->fill([
                    'registration_type' => $registration->registration_type,
                    'person_fifa_id' => $person->person_fifa_id,
                    'international_first_name' => $person->international_first_name,
                    'international_last_name' => $person->international_last_name,
                    'local_first_name' => $person->local_first_name,
                    'local_last_name' => $person->local_last_name,
                    'popular_name' => $person->popular_name,
                    'gender' => strtolower((string) $person->gender),
                    'date_of_birth' => $person->date_of_birth,
                    'nationality' => $person->nationality ? strtoupper((string) $person->nationality) : null,
                    'second_nationality' => $person->second_nationality ? strtoupper((string) $person->second_nationality) : null,
                    'country_of_birth' => $person->country_of_birth ? strtoupper((string) $person->country_of_birth) : null,
                    'place_of_birth' => $person->place_of_birth,
                    'team_official_role' => $registration->registration_type === Registration::TYPE_TEAM_OFFICIAL ? $role : null,
                    'organisation_official_role' => $registration->registration_type === Registration::TYPE_ORGANISATION_OFFICIAL ? $role : null,
                    'role_description' => $role,
                    'status' => 'active',
                    'discipline' => $registration->discipline ?: 'Football',
                    'registration_valid_from' => $registration->registration_valid_from,
                    'registration_valid_to' => $registration->registration_valid_to,
                    'source' => 'FIFAConnect',
                    'source_url' => null,
                    'retrieved_at' => now(),
                ]);
                if (!$exists) $official->created_by = $actor->id;
                $official->save();

                $exists ? $updated++ : $created++;
            }
        });

        return compact('created','updated','skipped') + ['available'=>true];
    }
}
