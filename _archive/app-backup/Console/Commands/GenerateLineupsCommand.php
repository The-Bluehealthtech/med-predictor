<?php

namespace App\Console\Commands;

use App\Services\LineupGenerationService;
use Illuminate\Console\Command;

class GenerateLineupsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'lineups:generate 
                            {--formation= : Formation to use (e.g., 4-4-2, 4-3-3)}
                            {--tactical-style=balanced : Tactical style (balanced, attacking, defensive, possession, counter)}
                            {--max-substitutes=7 : Maximum number of substitutes}
                            {--dry-run : Show what would be generated without creating lineups}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate lineups for all upcoming matches';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting lineup generation for upcoming matches...');

        $options = [
            'formation' => $this->option('formation'),
            'tactical_style' => $this->option('tactical-style'),
            'max_substitutes' => (int) $this->option('max-substitutes'),
            'dry_run' => $this->option('dry-run')
        ];

        if ($options['dry_run']) {
            $this->warn('DRY RUN MODE - No lineups will be created');
        }

        $lineupService = new LineupGenerationService();

        try {
            if ($options['dry_run']) {
                // For dry run, we'll just show what would be generated
                $this->showDryRunInfo($lineupService, $options);
            } else {
                $result = $lineupService->generateLineupsForUpcomingMatches($options);
                
                $this->displayResults($result);
            }

        } catch (\Exception $e) {
            $this->error('Failed to generate lineups: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }

    /**
     * Show dry run information
     */
    private function showDryRunInfo(LineupGenerationService $lineupService, array $options)
    {
        $this->info('Would generate lineups with the following options:');
        $this->line('Formation: ' . ($options['formation'] ?? 'Team default'));
        $this->line('Tactical Style: ' . $options['tactical_style']);
        $this->line('Max Substitutes: ' . $options['max_substitutes']);

        // Get upcoming matches count
        $upcomingMatches = \App\Models\GameMatch::where('match_status', 'scheduled')
            ->where('match_date', '>=', now())
            ->count();

        $totalLineups = $upcomingMatches * 2;
        $this->info("Would generate lineups for {$upcomingMatches} matches ({$totalLineups} total lineups)");
    }

    /**
     * Display generation results
     */
    private function displayResults(array $result)
    {
        $this->info('Lineup generation completed!');
        $this->line('');
        
        $this->table(
            ['Metric', 'Count'],
            [
                ['Total Matches', $result['total_matches']],
                ['Lineups Generated', $result['total_lineups']],
                ['Errors', count($result['errors'])]
            ]
        );

        if (!empty($result['errors'])) {
            $this->error('Errors encountered:');
            foreach ($result['errors'] as $error) {
                $this->line('  - ' . $error);
            }
        }

        if ($result['total_lineups'] > 0) {
            $this->info('Successfully generated ' . $result['total_lineups'] . ' lineups!');
        }
    }
} 