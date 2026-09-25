<?php

namespace App\Console\Commands;

use App\Models\Athlete;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class TestPCMAAI extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pcma:test-ai {--athlete-id= : Test with specific athlete ID}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test PCMA AI extraction functionality';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Testing PCMA AI Extraction...');

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

        $this->info("Testing with athlete: {$athlete->name} (ID: {$athlete->id})");

        // Test cardiovascular transcript
        $cardioTranscript = "Patient reports blood pressure is 120 over 80, heart rate is 65 bpm, resting heart rate is 58. No chest pain or shortness of breath. Family history includes heart disease in father. Currently taking no medications.";

        $this->info('1. Testing cardiovascular PCMA extraction...');
        $this->testExtraction($athleteId, 'cardio', $cardioTranscript);

        // Test neurological transcript
        $neuroTranscript = "Patient is alert and oriented. Glasgow Coma Scale score is 15. Pupils are equal and reactive. Normal motor function. No neurological symptoms reported. No headache or memory loss.";

        $this->info('2. Testing neurological PCMA extraction...');
        $this->testExtraction($athleteId, 'neurological', $neuroTranscript);

        // Test musculoskeletal transcript
        $mskTranscript = "Patient has full range of motion in all joints. Strength grade is 5/5. Pain level is 0/10. Joint stability is normal. No specific injury reported.";

        $this->info('3. Testing musculoskeletal PCMA extraction...');
        $this->testExtraction($athleteId, 'musculoskeletal', $mskTranscript);

        $this->info('PCMA AI extraction test completed!');
        return 0;
    }

    private function testExtraction($athleteId, $pcmaType, $transcript)
    {
        try {
            // Create a mock request
            $request = new \Illuminate\Http\Request();
            $request->merge([
                'transcript' => $transcript,
                'athlete_id' => $athleteId,
                'pcma_type' => $pcmaType
            ]);

            // Call the controller method directly
            $controller = new \App\Http\Controllers\Api\V1\PCMAController();
            $response = $controller->prefillFromTranscript($request);
            $data = $response->getData(true);
            
            if (isset($data['success']) && $data['success']) {
                $this->info("✅ Successfully extracted {$pcmaType} PCMA data");
                
                if (isset($data['confidence_score'])) {
                    $this->line("Confidence: " . round($data['confidence_score'] * 100, 1) . "%");
                }
                
                if (isset($data['extracted_fields']) && is_array($data['extracted_fields'])) {
                    $this->line("Extracted fields: " . implode(', ', $data['extracted_fields']));
                }
                
                // Show some extracted values
                if (isset($data['data']) && is_array($data['data'])) {
                    $this->line("Sample extracted data:");
                    foreach (array_slice($data['data'], 0, 5) as $key => $value) {
                        $displayValue = is_array($value) ? implode(', ', $value) : (string) $value;
                        $this->line("  {$key}: {$displayValue}");
                    }
                }
                
                if (isset($data['mock_data']) && $data['mock_data']) {
                    $this->line("ℹ️  Mock data generated (AI service unavailable)");
                }
            } else {
                $this->error("❌ Failed to extract {$pcmaType} PCMA data");
                $this->line("Error: " . ($data['message'] ?? 'Unknown error'));
            }
        } catch (\Exception $e) {
            $this->error("❌ Exception during {$pcmaType} extraction: " . $e->getMessage());
        }

        $this->line('');
    }
} 