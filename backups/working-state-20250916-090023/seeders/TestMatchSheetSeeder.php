<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Club;
use App\Models\Team;
use App\Models\Competition;
use App\Models\GameMatch;
use App\Models\User;
use App\Models\MatchSheet;
use App\Models\MatchOfficial;
use App\Models\Player;
use App\Models\MatchEvent;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class TestMatchSheetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Creating test match sheet data...');

        // Create test clubs
        $club1 = Club::create([
            'name' => 'Manchester United',
            'short_name' => 'MUFC',
            'city' => 'Manchester',
            'country' => 'England',
            'founded_year' => 1878,
            'logo_url' => null,
            'website' => 'https://www.manutd.com',
            'email' => 'info@manutd.com',
            'phone' => '+44 161 868 8000',
            'address' => 'Old Trafford, Sir Matt Busby Way, Manchester M16 0RA',
            'status' => 'active',
        ]);

        $club2 = Club::create([
            'name' => 'Liverpool FC',
            'short_name' => 'LFC',
            'city' => 'Liverpool',
            'country' => 'England',
            'founded_year' => 1892,
            'logo_url' => null,
            'website' => 'https://www.liverpoolfc.com',
            'email' => 'info@liverpoolfc.com',
            'phone' => '+44 151 260 8600',
            'address' => 'Anfield Road, Liverpool L4 0TH',
            'status' => 'active',
        ]);

        $club3 = Club::create([
            'name' => 'Arsenal FC',
            'short_name' => 'AFC',
            'city' => 'London',
            'country' => 'England',
            'founded_year' => 1886,
            'logo_url' => null,
            'website' => 'https://www.arsenal.com',
            'email' => 'info@arsenal.com',
            'phone' => '+44 20 7619 5000',
            'address' => 'Emirates Stadium, Hornsey Road, London N7 7AJ',
            'status' => 'active',
        ]);

        // Create test teams
        $team1 = Team::create([
            'name' => 'Manchester United First Team',
            'short_name' => 'MUFC',
            'club_id' => $club1->id,
            'type' => 'first_team',
            'season' => '2024-2025',
            'status' => 'active',
            'competition_level' => 'premier_league',
            'formation' => '4-3-3',
            'tactical_style' => 'possession_based',
            'playing_philosophy' => 'attacking',
            'coach_name' => 'Erik ten Hag',
            'assistant_coach' => 'Steve McClaren',
            'fitness_coach' => 'Richard Hawkins',
            'goalkeeper_coach' => 'Richard Hartis',
            'scout' => 'Simon Wells',
            'medical_staff' => 'Dr. Steve McNally',
            'home_ground' => 'Old Trafford',
            'capacity' => 74140,
            'colors' => 'Red, White, Black',
            'website' => 'https://www.manutd.com',
            'description' => 'Manchester United First Team',
        ]);

        $team2 = Team::create([
            'name' => 'Liverpool FC First Team',
            'short_name' => 'LFC',
            'club_id' => $club2->id,
            'type' => 'first_team',
            'season' => '2024-2025',
            'status' => 'active',
            'competition_level' => 'premier_league',
            'formation' => '4-3-3',
            'tactical_style' => 'high_pressing',
            'playing_philosophy' => 'attacking',
            'coach_name' => 'Jürgen Klopp',
            'assistant_coach' => 'Pepijn Lijnders',
            'fitness_coach' => 'Andreas Kornmayer',
            'goalkeeper_coach' => 'John Achterberg',
            'scout' => 'Dave Fallows',
            'medical_staff' => 'Dr. Jim Moxon',
            'home_ground' => 'Anfield',
            'capacity' => 53394,
            'colors' => 'Red, White',
            'website' => 'https://www.liverpoolfc.com',
            'description' => 'Liverpool FC First Team',
        ]);

        $team3 = Team::create([
            'name' => 'Arsenal FC First Team',
            'short_name' => 'AFC',
            'club_id' => $club3->id,
            'type' => 'first_team',
            'season' => '2024-2025',
            'status' => 'active',
            'competition_level' => 'premier_league',
            'formation' => '4-3-3',
            'tactical_style' => 'possession_based',
            'playing_philosophy' => 'attacking',
            'coach_name' => 'Mikel Arteta',
            'assistant_coach' => 'Albert Stuivenberg',
            'fitness_coach' => 'Shad Forsythe',
            'goalkeeper_coach' => 'Inaki Cana',
            'scout' => 'Francis Cagigao',
            'medical_staff' => 'Dr. Gary O\'Driscoll',
            'home_ground' => 'Emirates Stadium',
            'capacity' => 60704,
            'colors' => 'Red, White',
            'website' => 'https://www.arsenal.com',
            'description' => 'Arsenal FC First Team',
        ]);

        // Create test competition
        $competition = Competition::create([
            'name' => 'Premier League 2024-2025',
            'short_name' => 'EPL',
            'type' => 'league',
            'country' => 'England',
            'region' => 'Europe',
            'season' => '2024-2025',
            'description' => 'English Premier League Season 2024-2025',
            'start_date' => '2024-08-10',
            'end_date' => '2025-05-25',
            'registration_deadline' => '2024-07-31',
            'max_teams' => 20,
            'min_teams' => 18,
            'status' => 'active',
            'format' => 'round_robin',
            'rules' => 'Standard Premier League rules apply',
            'prize_pool' => 2500000,
            'entry_fee' => 0,
            'organizer' => 'Premier League',
            'website' => 'https://www.premierleague.com',
            'logo_url' => null,
            'sponsors' => 'Barclays, EA Sports',
            'broadcast_partners' => 'Sky Sports, BT Sport',
            'require_federation_license' => true,
            'football_type' => 'association',
        ]);

        // Get existing test referees
        $referee1 = User::where('email', 'michael.oliver@referee.com')->first();
        $referee2 = User::where('email', 'anthony.taylor@referee.com')->first();
        $referee3 = User::where('email', 'paul.tierney@referee.com')->first();
        $referee4 = User::where('email', 'stuart.attwell@referee.com')->first();
        $referee5 = User::where('email', 'chris.kavanagh@referee.com')->first();

        if (!$referee1 || !$referee2 || !$referee3 || !$referee4 || !$referee5) {
            $this->command->error('Some referee users are missing. Please ensure all referee users exist.');
            return;
        }

        // Get existing players for Manchester United (using existing FIFA IDs)
        $manUtdPlayers = Player::whereIn('fifa_connect_id', ['FIFA001', 'FIFA002', 'FIFA003', 'FIFA004', 'FIFA005', 'FIFA006', 'FIFA007', 'FIFA008', 'FIFA009', 'FIFA010'])->get();
        
        // Get existing players for Liverpool (using existing FIFA IDs)
        $liverpoolPlayers = Player::whereIn('fifa_connect_id', ['FIFA011', 'FIFA012', 'FIFA013', 'FIFA014', 'FIFA015', 'FIFA016', 'FIFA017', 'FIFA018', 'FIFA019', 'FIFA020'])->get();

        // Update team assignments for existing players
        foreach ($manUtdPlayers as $player) {
            $player->update(['team_id' => $team1->id]);
        }
        
        foreach ($liverpoolPlayers as $player) {
            $player->update(['team_id' => $team2->id]);
        }

        // Create test matches
        $match1 = DB::table('matches')->insertGetId([
            'name' => 'Manchester United vs Liverpool',
            'competition_id' => $competition->id,
            'home_team_id' => $team1->id,
            'away_team_id' => $team2->id,
            'home_club_id' => $club1->id,
            'away_club_id' => $club2->id,
            'match_date' => '2024-12-25',
            'kickoff_time' => '2024-12-25 20:00:00',
            'venue' => 'Old Trafford',
            'stadium' => 'Old Trafford',
            'capacity' => 74140,
            'match_status' => 'scheduled',
            'home_score' => null,
            'away_score' => null,
            'matchday' => 1,
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $match2 = DB::table('matches')->insertGetId([
            'name' => 'Arsenal vs Manchester United',
            'competition_id' => $competition->id,
            'home_team_id' => $team3->id,
            'away_team_id' => $team1->id,
            'home_club_id' => $club3->id,
            'away_club_id' => $club1->id,
            'match_date' => '2024-12-28',
            'kickoff_time' => '2024-12-28 15:00:00',
            'venue' => 'Emirates Stadium',
            'stadium' => 'Emirates Stadium',
            'capacity' => 60704,
            'match_status' => 'scheduled',
            'home_score' => null,
            'away_score' => null,
            'matchday' => 1,
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Create match sheet for the first match (using the actual match ID)
        $matchSheet1 = MatchSheet::create([
            'match_id' => $match1,
            'match_number' => 'MS-2024-001',
            'status' => 'draft',
            'stage' => 'pre_match',
            'assigned_referee_id' => $referee1->id,
            'referee_assigned_at' => now(),
            'main_referee_id' => $referee1->id,
            'assistant_referee_1_id' => $referee2->id,
            'assistant_referee_2_id' => $referee3->id,
            'fourth_official_id' => $referee4->id,
            'var_referee_id' => $referee5->id,
            'var_assistant_id' => null,
            'stadium_venue' => 'Old Trafford',
            'kickoff_time' => '2024-12-25 20:00:00',
            'match_status' => 'scheduled',
        ]);

        // Create match sheet for the second match (without referee assignment for testing)
        $matchSheet2 = MatchSheet::create([
            'match_id' => $match2,
            'match_number' => 'MS-2024-002',
            'status' => 'draft',
            'stage' => 'pre_match',
            'assigned_referee_id' => null,
            'referee_assigned_at' => null,
            'main_referee_id' => null,
            'assistant_referee_1_id' => null,
            'assistant_referee_2_id' => null,
            'fourth_official_id' => null,
            'var_referee_id' => null,
            'var_assistant_id' => null,
            'stadium_venue' => 'Emirates Stadium',
            'kickoff_time' => '2024-12-28 15:00:00',
            'match_status' => 'scheduled',
        ]);

        $this->command->info('Test match sheet data created successfully!');
        $this->command->info('Match 1 (with referees): /competition-management/matches/' . $match1 . '/match-sheet');
        $this->command->info('Match 2 (without referees): /competition-management/matches/' . $match2 . '/match-sheet');
        $this->command->info('Test referees available:');
        $this->command->info('- Michael Oliver (michael.oliver@referee.com)');
        $this->command->info('- Anthony Taylor (anthony.taylor@referee.com)');
        $this->command->info('- Paul Tierney (paul.tierney@referee.com)');
        $this->command->info('- Stuart Attwell (stuart.attwell@referee.com)');
        $this->command->info('- Chris Kavanagh (chris.kavanagh@referee.com)');
        $this->command->info('All referee passwords: password');
    }
}
