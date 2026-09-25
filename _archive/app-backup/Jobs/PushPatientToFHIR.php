<?php

namespace App\Jobs;

use App\Models\Athlete;
use App\Services\FHIRService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class PushPatientToFHIR implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $backoff = 30;
    public $timeout = 60;

    private Athlete $athlete;

    /**
     * Create a new job instance.
     */
    public function __construct(Athlete $athlete)
    {
        $this->athlete = $athlete;
    }

    /**
     * Execute the job.
     */
    public function handle(FHIRService $fhirService): void
    {
        try {
            Log::info("Starting FHIR push job for athlete {$this->athlete->id}", [
                'athlete_id' => $this->athlete->id,
                'fifa_id' => $this->athlete->fifa_id,
                'job_id' => $this->job->getJobId()
            ]);

            $result = $fhirService->pushPatient($this->athlete);

            if ($result['success']) {
                Log::info("Successfully completed FHIR push job for athlete {$this->athlete->id}", [
                    'athlete_id' => $this->athlete->id,
                    'fhir_id' => $result['fhir_id'] ?? null,
                    'job_id' => $this->job->getJobId()
                ]);
            } else {
                Log::error("Failed to complete FHIR push job for athlete {$this->athlete->id}", [
                    'athlete_id' => $this->athlete->id,
                    'error' => $result['error'] ?? $result['message'],
                    'job_id' => $this->job->getJobId()
                ]);

                // Re-throw to trigger retry
                throw new \Exception($result['error'] ?? $result['message']);
            }
        } catch (\Exception $e) {
            Log::error("Exception in FHIR push job for athlete {$this->athlete->id}", [
                'athlete_id' => $this->athlete->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'job_id' => $this->job->getJobId()
            ]);

            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error("FHIR push job failed permanently for athlete {$this->athlete->id}", [
            'athlete_id' => $this->athlete->id,
            'fifa_id' => $this->athlete->fifa_id,
            'error' => $exception->getMessage(),
            'job_id' => $this->job->getJobId()
        ]);

        // Could send notification to admin about failed FHIR push
        // or create a manual review task
    }
} 