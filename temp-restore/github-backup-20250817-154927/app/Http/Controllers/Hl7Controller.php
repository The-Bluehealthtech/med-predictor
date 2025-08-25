<?php

namespace App\Http\Controllers;

use App\Models\Performance;
use App\Services\Hl7FhirService;
use Illuminate\Http\Request;

class Hl7Controller extends Controller
{
    protected $hl7Service;

    public function __construct(Hl7FhirService $hl7Service)
    {
        $this->hl7Service = $hl7Service;
    }

    /**
     * Sync performance data to HL7 FHIR.
     */
    public function syncPerformance(Performance $performance)
    {
        $result = $this->hl7Service->createPerformanceObservation($performance);

        return response()->json($result);
    }
} 