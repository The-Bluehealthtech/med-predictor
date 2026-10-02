<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\Analytics\PlayerFormAnalytics;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * Analyse des performances : chiffres issus des feuilles de match, alertes
 * cohérentes avec leurs raisons, classements avec seuil, périmètre par compte.
 */
class PlayerFormAnalyticsTest extends TestCase
{
    use DatabaseTransactions;

    private int $clubId;
    private int $otherClubId;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('role-eval:generate-demo', ['--seed' => 11, '--clubs' => 4])->assertExitCode(0);
        $competitionId = DB::table('matches')->max('competition_id');
        $clubs = DB::table('matches')->where('competition_id', $competitionId)->pluck('home_club_id')->unique()->sort()->values();
        $this->clubId = (int) $clubs[0];
        $this->otherClubId = (int) $clubs[1];
    }

    private function user(string $role, array $attributes = []): User
    {
        return User::factory()->create(array_merge(['role' => $role, 'tenant_id' => $role === 'system_admin' ? null : 1,
            'club_id' => null, 'association_id' => null, 'status' => 'active'], $attributes));
    }

    public function test_figures_come_from_match_sheets(): void
    {
        $data = app(PlayerFormAnalytics::class)->forClub($this->clubId, 'season');
        $this->assertNotNull($data);
        $this->assertSame($data['club_matches'], $data['window_matches']);
        $this->assertCount($data['club_matches'], $data['team_trend']['values'], 'une note d\'équipe par match joué');

        $player = collect($data['players'])->sortByDesc('matches')->first();
        $teamIds = DB::table('teams')->where('club_id', $this->clubId)->pluck('id');
        $apps = DB::table('match_participations')->where('player_id', $player['id'])->whereIn('team_id', $teamIds)->get();
        $this->assertSame($apps->count(), $player['matches']);
        $this->assertSame((int) $apps->sum(fn ($a) => max(0, $a->minute_out - $a->minute_in)), $player['minutes']);

        $record = $data['record'];
        $this->assertSame($data['club_matches'], $record['won'] + $record['drawn'] + $record['lost']);

        $five = app(PlayerFormAnalytics::class)->forClub($this->clubId, '5');
        $this->assertSame(5, $five['window_matches']);
        $this->assertLessThanOrEqual(5, max(array_column($five['players'], 'matches')));
    }

    public function test_alerts_are_consistent_with_their_reasons_and_rankings_respect_thresholds(): void
    {
        $data = app(PlayerFormAnalytics::class)->forClub($this->clubId, 'season', null, 5);
        $byId = collect($data['players'])->keyBy('id');
        foreach ($data['alerts'] as $alert) {
            $this->assertNotEmpty($alert['reason']);
            $p = $byId[$alert['player_id']];
            match ($alert['type']) {
                'form_drop' => $this->assertLessThanOrEqual(-PlayerFormAnalytics::FORM_DELTA, $p['form_delta']),
                'form_rise' => $this->assertGreaterThanOrEqual(PlayerFormAnalytics::FORM_DELTA, $p['form_delta']),
                'high_load' => $this->assertTrue($p['load_last3_pct'] >= PlayerFormAnalytics::HIGH_LOAD_PCT && $p['last3_span_days'] <= PlayerFormAnalytics::CONGESTED_DAYS),
                'minutes_drop' => $this->assertSame(0, $p['minutes_last3']),
                'suspension_risk' => $this->assertSame(4, $p['yellow_season'] % 5),
            };
        }
        foreach ($data['leaders']['rating'] as $p) {
            $this->assertGreaterThanOrEqual(5, $p['matches']);
        }
        foreach ($data['leaders']['ga_per90'] as $p) {
            $this->assertGreaterThanOrEqual(270, $p['minutes']);
        }
        $gk = app(PlayerFormAnalytics::class)->forClub($this->clubId, 'season', 'GK');
        $this->assertNotEmpty($gk['players']);
        $this->assertSame(['GK'], array_values(array_unique(array_column($gk['players'], 'position'))));
    }

    public function test_high_load_only_when_the_calendar_is_congested(): void
    {
        $analytics = app(PlayerFormAnalytics::class);
        $weekly = $analytics->forClub($this->clubId, 'season');
        $this->assertEmpty(array_filter($weekly['alerts'], fn ($a) => $a['type'] === 'high_load'), 'un match par semaine n\'est pas une surcharge');

        // Les 3 derniers matchs du club ramenés sur 6 jours.
        $last3 = DB::table('matches')->where(fn ($q) => $q->where('home_club_id', $this->clubId)->orWhere('away_club_id', $this->clubId))
            ->whereNotNull('home_score')->orderByDesc('match_date')->orderByDesc('id')->limit(3)->get(['id', 'match_date']);
        $end = \Carbon\Carbon::parse($last3->first()->match_date);
        foreach ($last3->values() as $i => $match) {
            DB::table('matches')->where('id', $match->id)->update(['match_date' => $end->copy()->subDays(3 * $i)]);
        }

        $congested = $analytics->forClub($this->clubId, 'season');
        $fullMinutes = collect($congested['players'])->filter(fn ($p) => $p['load_last3_pct'] >= PlayerFormAnalytics::HIGH_LOAD_PCT);
        $this->assertNotEmpty($fullMinutes, 'au moins un joueur a tout joué');
        $flagged = collect($congested['alerts'])->where('type', 'high_load')->pluck('player_id')->sort()->values()->all();
        $this->assertSame($fullMinutes->pluck('id')->sort()->values()->all(), $flagged);
        $this->assertStringContainsString('joués en 6 jours', collect($congested['alerts'])->firstWhere('type', 'high_load')['reason']);
    }

    public function test_page_renders_and_scope_follows_the_account(): void
    {
        $admin = $this->user('system_admin');
        $page = $this->actingAs($admin)->get(route('performances.analytics', ['club_id' => $this->clubId, 'window' => '10']))->assertOk()
            ->assertSee('Analyse des performances')->assertSee('Alertes et signaux')->assertSee('Note moyenne de l\'équipe, match par match', false);
        $data = $page->viewData('data');
        $this->assertSame(min(10, $data['club_matches']), $data['window_matches'], 'au plus les 10 derniers matchs');

        $coach = $this->user('club_admin', ['club_id' => $this->clubId]);
        $own = $this->actingAs($coach)->get(route('performances.analytics'))->assertOk();
        $this->assertSame($this->clubId, $own->viewData('clubId'));
        $this->assertCount(1, $own->viewData('clubs'));
        $this->actingAs($coach)->get(route('performances.analytics', ['club_id' => $this->otherClubId]))->assertNotFound();

        $this->actingAs($this->user('player'))->get(route('performances.analytics'))->assertForbidden();
        $this->actingAs($admin)->get(route('performances.analytics', ['window' => 'tout']))->assertSessionHasErrors('window');
    }
}
