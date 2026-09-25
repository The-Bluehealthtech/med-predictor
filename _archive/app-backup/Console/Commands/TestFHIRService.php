<?php

namespace App\Console\Commands;

use App\Models\Athlete;
use App\Services\FHIRService;
use Illuminate\Console\Command;

class TestFHIRService extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fhir:test {--athlete-id= : Test with specific athlete ID}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test FHIR service connectivity and functionality';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Testing FHIR Service...');

        $fhirService = new FHIRService();

        // Test connectivity
        $this->info('1. Testing FHIR server connectivity...');
        $connectivity = $fhirService->checkConnectivity();
        
        if ($connectivity['success']) {
            $this->info('✅ FHIR server is accessible');
            $this->line('Server capabilities: ' . json_encode($connectivity['capabilities'] ?? []));
        } else {
            $this->error('❌ FHIR server is not accessible');
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

        $this->info("2. Testing FHIR push for athlete: {$athlete->name} (ID: {$athlete->id})");
        
        $result = $fhirService->pushPatient($athlete);
        
        if ($result['success']) {
            $this->info('✅ Successfully pushed athlete to FHIR server');
            $this->line('FHIR ID: ' . ($result['fhir_id'] ?? 'N/A'));
        } else {
            $this->error('❌ Failed to push athlete to FHIR server');
            $this->line('Error: ' . ($result['error'] ?? $result['message']));
        }

        $this->info('FHIR service test completed!');
        return 0;
    }
} 