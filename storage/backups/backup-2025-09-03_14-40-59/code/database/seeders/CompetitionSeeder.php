<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Competition;
use App\Models\Association;
use Carbon\Carbon;

class CompetitionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing competitions first
        Competition::truncate();
        
        // Get the English Football Association
        $englishFA = Association::where('name', 'The Football Association')->first();
        $associationId = $englishFA ? $englishFA->id : null;

        // Create exactly 41 active competitions
        for ($i = 1; $i <= 41; $i++) {
            $competitionTypes = ['league', 'cup', 'tournament', 'playoff'];
            $competitionFormats = ['round_robin', 'knockout', 'mixed', 'group_stage'];
            
            Competition::create([
                'name' => "Competition {$i}",
                'short_name' => "Comp {$i}",
                'type' => $competitionTypes[array_rand($competitionTypes)],
                'season_id' => null,
                'start_date' => Carbon::now()->subMonths(rand(1, 6)),
                'end_date' => Carbon::now()->addMonths(rand(1, 12)),
                'registration_deadline' => Carbon::now()->subMonths(rand(1, 3)),
                'min_teams' => rand(4, 8),
                'max_teams' => rand(8, 20),
                'format' => $competitionFormats[array_rand($competitionFormats)],
                'status' => 'active',
                'description' => "This is competition number {$i}",
                'rules' => 'Standard competition rules apply',
                'entry_fee' => rand(0, 1000),
                'prize_pool' => rand(10000, 100000),
                'association_id' => $associationId,
                'fifa_connect_id' => "FIFA_COMP_{$i}",
                'require_federation_license' => rand(0, 1),
                'fixtures_validated' => rand(0, 1),
                'fixtures_validated_at' => rand(0, 1) ? Carbon::now()->subDays(rand(1, 30)) : null,
                'validated_by' => null,
                'created_at' => Carbon::now()->subMonths(rand(1, 12)),
                'updated_at' => Carbon::now()->subDays(rand(0, 30)),
            ]);
        }

        $this->command->info('Competitions seeded successfully!');
        $this->command->info('Created 41 active competitions, 0 upcoming, 0 completed');
    }
} 