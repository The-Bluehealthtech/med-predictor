<?php

namespace App\Http\Controllers;

use App\Models\HealthRecord;
use App\Models\DentalAnnotation;
use App\Services\MedicalRecordAccess;
use Illuminate\Http\Request;

class DentalChartController extends Controller
{
    /**
     * Afficher la page du diagramme dentaire.
     */
    public function index(Request $request)
    {
        $access = app(MedicalRecordAccess::class);
        $access->authorizeRole($request->user());

        $healthRecords = HealthRecord::with('player')
            ->whereHas('player', fn ($query) => $access->scopePlayers($request->user(), $query))
            ->orderByDesc('id')
            ->get();

        return view('health-records.dental-chart', compact('healthRecords'));
    }

    /**
     * Afficher le diagramme dentaire pour un dossier de santé spécifique.
     */
    public function show(Request $request, HealthRecord $healthRecord)
    {
        app(MedicalRecordAccess::class)->authorize($request->user(), $healthRecord->player, null);

        $annotations = DentalAnnotation::where('health_record_id', $healthRecord->id)->get();

        return view('health-records.dental-chart', compact('healthRecord', 'annotations'));
    }
}
