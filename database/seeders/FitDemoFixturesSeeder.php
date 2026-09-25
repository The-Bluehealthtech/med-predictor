<?php

namespace Database\Seeders;

use App\Models\Competition;
use App\Models\GameMatch;
use App\Models\MatchEvent;
use App\Models\MatchSheet;
use App\Models\Player;
use App\Models\PlayerSeasonStat;
use App\Models\Team;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Repeatable demo fixtures using only existing associations, clubs, teams and players.
 * Demo records are never given a FIFA identifier or a referee/doctor attestation.
 */
class FitDemoFixturesSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function (): void {
            Competition::query()->whereNotNull('association_id')->orderBy('id')->each(function (Competition $competition): void {
                $teams = Team::query()->whereHas('club', fn ($q) => $q->where('association_id', $competition->association_id))
                    ->with('club')->orderBy('club_id')->orderBy('id')->get()->unique('club_id')->values();
                if ($teams->count() < 2) {
                    return;
                }

                for ($round = 0; $round < min(3, $teams->count() - 1); $round++) {
                    for ($index = 0; $index + 1 < $teams->count(); $index += 2) {
                        $home = $teams[($index + $round) % $teams->count()];
                        $away = $teams[($index + $round + 1) % $teams->count()];
                        if ($home->id === $away->id) {
                            continue;
                        }
                        $name = "DEMO {$competition->id}-{$round}-{$index}: {$home->club->name} / {$away->club->name}";
                        $date = now()->startOfDay()->subDays(14 - $round * 7)->addHours(15);
                        $homeScore = ($round + $index) % 3;
                        $awayScore = ($round + $index + 1) % 3;
                        $match = GameMatch::query()->firstOrCreate(
                            ['competition_id' => $competition->id, 'name' => $name],
                            ['home_team_id' => $home->id, 'away_team_id' => $away->id,
                                'home_club_id' => $home->club_id, 'away_club_id' => $away->club_id,
                                'match_date' => $date, 'kickoff_time' => $date,
                                'status' => 'completed', 'match_status' => 'completed',
                                'home_score' => $homeScore, 'away_score' => $awayScore]
                        );
                        if (!$match->wasRecentlyCreated) {
                            continue;
                        }
                        $sheet = MatchSheet::query()->firstOrCreate(
                            ['match_id' => $match->id],
                            ['status' => 'draft', 'notes' => 'DEMO — unsigned match sheet']
                        );
                        foreach ([[$home, $homeScore], [$away, $awayScore]] as [$team, $score]) {
                            $squad = Player::query()->where('club_id', $team->club_id)->orderBy('id')->limit(11)->get();
                            if ($squad->isEmpty()) {
                                continue;
                            }
                            foreach ($squad as $player) {
                                $stats = PlayerSeasonStat::query()->firstOrCreate(
                                    ['competition_id' => $competition->id, 'player_id' => $player->id],
                                    ['matches_played' => 0, 'minutes_played' => 0, 'goals' => 0,
                                        'assists' => 0, 'yellow_cards' => 0, 'red_cards' => 0]
                                );
                                $stats->increment('matches_played');
                                $stats->increment('minutes_played', 90);
                            }
                            for ($goal = 0; $goal < $score; $goal++) {
                                $scorer = $squad[$goal % $squad->count()];
                                MatchEvent::query()->create([
                                    'match_id' => $match->id, 'match_sheet_id' => $sheet->id, 'team_id' => $team->id,
                                    'player_id' => $scorer->id, 'event_type' => 'goal', 'type' => 'goal',
                                    'minute' => 15 + $goal * 22, 'description' => 'DEMO goal',
                                ]);
                                PlayerSeasonStat::query()->where('competition_id', $competition->id)
                                    ->where('player_id', $scorer->id)->increment('goals');
                            }
                        }
                    }
                }
            });
        });
    }
}
