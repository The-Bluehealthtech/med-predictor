<?php

namespace App\Services;

use App\Models\Athlete;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DICOMwebService
{
    private string $baseUrl;
    private PendingRequest $httpClient;

    public function __construct()
    {
        $this->baseUrl = config('services.pacs.base_url');
        $this->httpClient = Http::withHeaders([
            'Accept' => 'application/dicom+json',
            'Content-Type' => 'application/dicom+json',
        ])->timeout(60);
    }

    /**
     * Get imaging studies for a patient.
     */
    public function getStudiesForPatient(Athlete $athlete): array
    {
        try {
            $response = $this->httpClient->get(
                "{$this->baseUrl}/studies",
                [
                    'PatientID' => $athlete->fifa_id,
                    'limit' => 50,
                    'offset' => 0
                ]
            );

            if ($response->successful()) {
                $studies = $response->json();
                
                Log::info("Successfully retrieved studies for athlete {$athlete->id}", [
                    'athlete_id' => $athlete->id,
                    'fifa_id' => $athlete->fifa_id,
                    'study_count' => count($studies)
                ]);

                return [
                    'success' => true,
                    'studies' => $this->transformStudies($studies),
                    'count' => count($studies)
                ];
            } else {
                Log::error("Failed to retrieve studies for athlete {$athlete->id}", [
                    'athlete_id' => $athlete->id,
                    'fifa_id' => $athlete->fifa_id,
                    'status_code' => $response->status(),
                    'response' => $response->body()
                ]);

                return [
                    'success' => false,
                    'message' => 'Failed to retrieve studies from PACS',
                    'status_code' => $response->status(),
                    'error' => $response->body(),
                    'studies' => []
                ];
            }
        } catch (\Exception $e) {
            Log::error("Exception while retrieving studies for athlete {$athlete->id}", [
                'athlete_id' => $athlete->id,
                'fifa_id' => $athlete->fifa_id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'message' => 'Exception occurred while retrieving studies',
                'error' => $e->getMessage(),
                'studies' => []
            ];
        }
    }

    /**
     * Get specific study details.
     */
    public function getStudyDetails(string $studyInstanceUID): array
    {
        try {
            $response = $this->httpClient->get(
                "{$this->baseUrl}/studies/{$studyInstanceUID}"
            );

            if ($response->successful()) {
                $study = $response->json();
                
                Log::info("Successfully retrieved study details", [
                    'study_instance_uid' => $studyInstanceUID,
                    'study_data' => $study
                ]);

                return [
                    'success' => true,
                    'study' => $this->transformStudy($study)
                ];
            } else {
                Log::error("Failed to retrieve study details", [
                    'study_instance_uid' => $studyInstanceUID,
                    'status_code' => $response->status(),
                    'response' => $response->body()
                ]);

                return [
                    'success' => false,
                    'message' => 'Failed to retrieve study details',
                    'status_code' => $response->status(),
                    'error' => $response->body()
                ];
            }
        } catch (\Exception $e) {
            Log::error("Exception while retrieving study details", [
                'study_instance_uid' => $studyInstanceUID,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Exception occurred while retrieving study details',
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Get series for a specific study.
     */
    public function getSeriesForStudy(string $studyInstanceUID): array
    {
        try {
            $response = $this->httpClient->get(
                "{$this->baseUrl}/studies/{$studyInstanceUID}/series"
            );

            if ($response->successful()) {
                $series = $response->json();
                
                Log::info("Successfully retrieved series for study", [
                    'study_instance_uid' => $studyInstanceUID,
                    'series_count' => count($series)
                ]);

                return [
                    'success' => true,
                    'series' => $this->transformSeries($series),
                    'count' => count($series)
                ];
            } else {
                Log::error("Failed to retrieve series for study", [
                    'study_instance_uid' => $studyInstanceUID,
                    'status_code' => $response->status(),
                    'response' => $response->body()
                ]);

                return [
                    'success' => false,
                    'message' => 'Failed to retrieve series from PACS',
                    'status_code' => $response->status(),
                    'error' => $response->body(),
                    'series' => []
                ];
            }
        } catch (\Exception $e) {
            Log::error("Exception while retrieving series for study", [
                'study_instance_uid' => $studyInstanceUID,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Exception occurred while retrieving series',
                'error' => $e->getMessage(),
                'series' => []
            ];
        }
    }

    /**
     * Get instances for a specific series.
     */
    public function getInstancesForSeries(string $studyInstanceUID, string $seriesInstanceUID): array
    {
        try {
            $response = $this->httpClient->get(
                "{$this->baseUrl}/studies/{$studyInstanceUID}/series/{$seriesInstanceUID}/instances"
            );

            if ($response->successful()) {
                $instances = $response->json();
                
                Log::info("Successfully retrieved instances for series", [
                    'study_instance_uid' => $studyInstanceUID,
                    'series_instance_uid' => $seriesInstanceUID,
                    'instance_count' => count($instances)
                ]);

                return [
                    'success' => true,
                    'instances' => $this->transformInstances($instances),
                    'count' => count($instances)
                ];
            } else {
                Log::error("Failed to retrieve instances for series", [
                    'study_instance_uid' => $studyInstanceUID,
                    'series_instance_uid' => $seriesInstanceUID,
                    'status_code' => $response->status(),
                    'response' => $response->body()
                ]);

                return [
                    'success' => false,
                    'message' => 'Failed to retrieve instances from PACS',
                    'status_code' => $response->status(),
                    'error' => $response->body(),
                    'instances' => []
                ];
            }
        } catch (\Exception $e) {
            Log::error("Exception while retrieving instances for series", [
                'study_instance_uid' => $studyInstanceUID,
                'series_instance_uid' => $seriesInstanceUID,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Exception occurred while retrieving instances',
                'error' => $e->getMessage(),
                'instances' => []
            ];
        }
    }

    /**
     * Get DICOMweb URL for viewing an image.
     */
    public function getImageUrl(string $studyInstanceUID, string $seriesInstanceUID, string $sopInstanceUID): string
    {
        return "{$this->baseUrl}/studies/{$studyInstanceUID}/series/{$seriesInstanceUID}/instances/{$sopInstanceUID}/rendered";
    }

    /**
     * Transform studies array to standardized format.
     */
    private function transformStudies(array $studies): array
    {
        return array_map(function ($study) {
            return [
                'study_instance_uid' => $study['0020000D']['Value'][0] ?? null,
                'study_date' => $study['00080020']['Value'][0] ?? null,
                'study_time' => $study['00080030']['Value'][0] ?? null,
                'study_description' => $study['00081030']['Value'][0] ?? 'No description',
                'modalities_in_study' => $study['00080061']['Value'] ?? [],
                'number_of_series' => $study['00201209']['Value'][0] ?? 0,
                'number_of_instances' => $study['00201208']['Value'][0] ?? 0,
                'patient_name' => $study['00100010']['Value'][0]['Alphabetic'] ?? 'Unknown',
                'patient_id' => $study['00100020']['Value'][0] ?? null,
                'patient_birth_date' => $study['00100030']['Value'][0] ?? null,
                'patient_sex' => $study['00100040']['Value'][0] ?? null,
                'accession_number' => $study['00080050']['Value'][0] ?? null,
                'referring_physician_name' => $study['00080090']['Value'][0]['Alphabetic'] ?? null,
                'performing_physician_name' => $study['00081010']['Value'][0]['Alphabetic'] ?? null,
                'institution_name' => $study['00080080']['Value'][0] ?? null,
                'study_comments' => $study['00081040']['Value'][0] ?? null,
                'formatted_date' => $this->formatStudyDate($study['00080020']['Value'][0] ?? null),
                'formatted_time' => $this->formatStudyTime($study['00080030']['Value'][0] ?? null),
            ];
        }, $studies);
    }

    /**
     * Transform single study to standardized format.
     */
    private function transformStudy(array $study): array
    {
        return [
            'study_instance_uid' => $study['0020000D']['Value'][0] ?? null,
            'study_date' => $study['00080020']['Value'][0] ?? null,
            'study_time' => $study['00080030']['Value'][0] ?? null,
            'study_description' => $study['00081030']['Value'][0] ?? 'No description',
            'modalities_in_study' => $study['00080061']['Value'] ?? [],
            'number_of_series' => $study['00201209']['Value'][0] ?? 0,
            'number_of_instances' => $study['00201208']['Value'][0] ?? 0,
            'patient_name' => $study['00100010']['Value'][0]['Alphabetic'] ?? 'Unknown',
            'patient_id' => $study['00100020']['Value'][0] ?? null,
            'patient_birth_date' => $study['00100030']['Value'][0] ?? null,
            'patient_sex' => $study['00100040']['Value'][0] ?? null,
            'accession_number' => $study['00080050']['Value'][0] ?? null,
            'referring_physician_name' => $study['00080090']['Value'][0]['Alphabetic'] ?? null,
            'performing_physician_name' => $study['00081010']['Value'][0]['Alphabetic'] ?? null,
            'institution_name' => $study['00080080']['Value'][0] ?? null,
            'study_comments' => $study['00081040']['Value'][0] ?? null,
            'formatted_date' => $this->formatStudyDate($study['00080020']['Value'][0] ?? null),
            'formatted_time' => $this->formatStudyTime($study['00080030']['Value'][0] ?? null),
        ];
    }

    /**
     * Transform series array to standardized format.
     */
    private function transformSeries(array $series): array
    {
        return array_map(function ($seriesItem) {
            return [
                'series_instance_uid' => $seriesItem['0020000E']['Value'][0] ?? null,
                'series_number' => $seriesItem['00200011']['Value'][0] ?? null,
                'series_date' => $seriesItem['00080021']['Value'][0] ?? null,
                'series_time' => $seriesItem['00080031']['Value'][0] ?? null,
                'series_description' => $seriesItem['0008103E']['Value'][0] ?? 'No description',
                'modality' => $seriesItem['00080060']['Value'][0] ?? null,
                'number_of_instances' => $seriesItem['00201209']['Value'][0] ?? 0,
                'body_part_examined' => $seriesItem['00180015']['Value'][0] ?? null,
                'formatted_date' => $this->formatStudyDate($seriesItem['00080021']['Value'][0] ?? null),
                'formatted_time' => $this->formatStudyTime($seriesItem['00080031']['Value'][0] ?? null),
            ];
        }, $series);
    }

    /**
     * Transform instances array to standardized format.
     */
    private function transformInstances(array $instances): array
    {
        return array_map(function ($instance) {
            return [
                'sop_instance_uid' => $instance['00080018']['Value'][0] ?? null,
                'instance_number' => $instance['00200013']['Value'][0] ?? null,
                'image_type' => $instance['00080008']['Value'] ?? [],
                'image_comments' => $instance['00081040']['Value'][0] ?? null,
                'formatted_date' => $this->formatStudyDate($instance['00080012']['Value'][0] ?? null),
                'formatted_time' => $this->formatStudyTime($instance['00080013']['Value'][0] ?? null),
            ];
        }, $instances);
    }

    /**
     * Format study date from DICOM format (YYYYMMDD) to readable format.
     */
    private function formatStudyDate(?string $dicomDate): ?string
    {
        if (!$dicomDate || strlen($dicomDate) !== 8) {
            return null;
        }

        $year = substr($dicomDate, 0, 4);
        $month = substr($dicomDate, 4, 2);
        $day = substr($dicomDate, 6, 2);

        return "{$year}-{$month}-{$day}";
    }

    /**
     * Format study time from DICOM format (HHMMSS.FFFFFF) to readable format.
     */
    private function formatStudyTime(?string $dicomTime): ?string
    {
        if (!$dicomTime) {
            return null;
        }

        // Extract hours, minutes, seconds
        $hours = substr($dicomTime, 0, 2);
        $minutes = substr($dicomTime, 2, 2);
        $seconds = substr($dicomTime, 4, 2);

        return "{$hours}:{$minutes}:{$seconds}";
    }

    /**
     * Check PACS server connectivity.
     */
    public function checkConnectivity(): array
    {
        try {
            $response = $this->httpClient->get("{$this->baseUrl}/");

            if ($response->successful()) {
                return [
                    'success' => true,
                    'message' => 'PACS server is accessible',
                    'response' => $response->body()
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'PACS server is not accessible',
                    'status_code' => $response->status(),
                    'error' => $response->body()
                ];
            }
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Exception occurred while checking PACS server connectivity',
                'error' => $e->getMessage()
            ];
        }
    }
} 