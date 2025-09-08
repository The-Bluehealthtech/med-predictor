<?php

namespace App\Console\Commands;

use App\Models\Athlete;
use App\Services\DICOMwebService;
use Illuminate\Console\Command;

class TestDICOMwebService extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dicom:test {--athlete-id= : Test with specific athlete ID}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test DICOMweb service connectivity and functionality';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Testing DICOMweb Service...');

        $dicomService = new DICOMwebService();

        // Test connectivity
        $this->info('1. Testing PACS server connectivity...');
        $connectivity = $dicomService->checkConnectivity();
        
        if ($connectivity['success']) {
            $this->info('✅ PACS server is accessible');
            $this->line('Server response: ' . ($connectivity['response'] ?? 'OK'));
        } else {
            $this->error('❌ PACS server is not accessible');
            $this->line('Error: ' . ($connectivity['error'] ?? $connectivity['message']));
            return 1;
        }

        // Test with athlete
        $athleteId = $this->option('athlete-id');
        if (!$athleteId) {
            $athlete = Athlete::first();
            if (!$athlete) {
                $this->error('No athletes found in database');
                return 1;
            }
            $athleteId = $athlete->id;
        }

        $athlete = Athlete::find($athleteId);
        if (!$athlete) {
            $this->error("Athlete with ID {$athleteId} not found");
            return 1;
        }

        $this->info("2. Testing DICOMweb studies retrieval for athlete: {$athlete->name} (ID: {$athlete->id})");
        $this->line("FIFA ID: {$athlete->fifa_id}");
        
        $result = $dicomService->getStudiesForPatient($athlete);
        
        if ($result['success']) {
            $this->info('✅ Successfully retrieved studies from PACS');
            $this->line("Found {$result['count']} studies");
            
            if ($result['count'] > 0) {
                $this->info('Sample studies:');
                foreach (array_slice($result['studies'], 0, 3) as $study) {
                    $this->line("- {$study['study_description']} ({$study['formatted_date']})");
                    $this->line("  Modalities: " . implode(', ', $study['modalities_in_study']));
                    $this->line("  Series: {$study['number_of_series']}, Images: {$study['number_of_instances']}");
                }
            }
        } else {
            $this->error('❌ Failed to retrieve studies from PACS');
            $this->line('Error: ' . ($result['error'] ?? $result['message']));
        }

        $this->info('DICOMweb service test completed!');
        return 0;
    }
} 