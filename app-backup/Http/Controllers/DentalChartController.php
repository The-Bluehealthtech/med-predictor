<?php

namespace App\Http\Controllers;

use App\Models\HealthRecord;
use App\Models\DentalAnnotation;
use Illuminate\Http\Request;

class DentalChartController extends Controller
{
    /**
     * Afficher la page du diagramme dentaire.
     */
    public function index()
    {
        $healthRecords = HealthRecord::with('patient')->get();
        
        return view('health-records.dental-chart', compact('healthRecords'));
    }

    /**
     * Afficher le diagramme dentaire pour un dossier de santé spécifique.
     */
    public function show(HealthRecord $healthRecord)
    {
        $annotations = DentalAnnotation::where('health_record_id', $healthRecord->id)->get();
        
        return view('health-records.dental-chart', compact('healthRecord', 'annotations'));
    }
}
