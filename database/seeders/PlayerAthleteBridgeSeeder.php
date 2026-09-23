<?php

namespace Database\Seeders;

use App\Models\Player;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class PlayerAthleteBridgeSeeder extends Seeder
{
    public function run(): void
    {
        $invalidPlayers = Player::query()
            ->where(function ($query) {
                $query->whereNull('team_id')
                    ->orWhereNull('date_of_birth')
                    ->orWhereNull('nationality');
            })
            ->count();

        if ($invalidPlayers > 0) {
            throw new RuntimeException(
                "{$invalidPlayers} joueur(s) sans team_id, date_of_birth ou nationality."
            );
        }

        Player::query()
            ->orderBy('id')
            ->chunkById(200, function ($players) {
                $now = now();

                $rows = $players->map(function ($player) use ($now) {
                    $name = trim(
                        $player->name
                        ?: (($player->first_name ?? '') . ' ' . ($player->last_name ?? ''))
                    );

                    return [
                        'player_id' => $player->id,

                        // FIFA externe uniquement s'il existe réellement.
                        // Pour les joueurs synthétiques actuels : NULL.
                        'fifa_id' => $player->fifa_player_id ?: null,

                        'name' => $name,
                        'dob' => $player->date_of_birth,
                        'nationality' => $this->nationalityCode($player->nationality),
                        'team_id' => $player->team_id,
                        'position' => $player->position,
                        'jersey_number' => $player->jersey_number,

                        // Les données synthétiques actuelles n'ont pas
                        // de genre connu : ne pas inventer "male".
                        'gender' => $this->genderValue($player->gender),

                        'active' => true,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                })->all();

                DB::table('athletes')->upsert(
                    $rows,
                    ['player_id'],
                    [
                        'name',
                        'dob',
                        'nationality',
                        'team_id',
                        'position',
                        'jersey_number',
                        'gender',
                        'active',
                        'updated_at',
                    ]
                );
            });

        /*
         * Si un vrai FIFA ID a été synchronisé ultérieurement,
         * on peut le propager sans jamais remplacer une valeur par NULL.
         */
        if (DB::getDriverName() === 'pgsql') {
            DB::statement('
                UPDATE athletes a
                SET fifa_id = p.fifa_player_id,
                    updated_at = NOW()
                FROM players p
                WHERE a.player_id = p.id
                  AND p.fifa_player_id IS NOT NULL
                  AND a.fifa_id IS DISTINCT FROM p.fifa_player_id
            ');
        }

        $this->command?->info(
            'Player/Athlete bridge: '
            . DB::table('athletes')->whereNotNull('player_id')->count()
            . ' athlete(s) liés.'
        );
    }
    private function nationalityCode(?string $nationality): string
    {
        $codes = [
            'Algeria' => 'DZA',
            'Argentina' => 'ARG',
            'Belgium' => 'BEL',
            'Brazil' => 'BRA',
            'Burkina Faso' => 'BFA',
            'Cameroon' => 'CMR',
            'Croatia' => 'HRV',
            'France' => 'FRA',
            'Germany' => 'DEU',
            'Italy' => 'ITA',
            'Ivory Coast' => 'CIV',
            'Mali' => 'MLI',
            'Morocco' => 'MAR',
            'Netherlands' => 'NLD',
            'Poland' => 'POL',
            'Portugal' => 'PRT',
            'Senegal' => 'SEN',
            'Serbia' => 'SRB',
            'Spain' => 'ESP',
            'Tunisia' => 'TUN',
        ];

        if (!$nationality || !isset($codes[$nationality])) {
            throw new \RuntimeException(
                'Nationalité non mappée vers ISO-3: ' . ($nationality ?? 'NULL')
            );
        }

        return $codes[$nationality];
    }


    private function genderValue(?string $gender): ?string
    {
        if ($gender === null || trim($gender) === '') {
            return null;
        }

        $gender = strtolower(trim($gender));

        if (!in_array($gender, ['male', 'female', 'other'], true)) {
            return null;
        }

        return $gender;
    }


}
