<?php

namespace App\Http\Controllers\Clinical;

use App\Http\Controllers\Controller;
use App\Models\Player;
use App\Services\Fhir\ClinicalDataQuery;
use App\Services\Fhir\FhirException;
use App\Services\Fhir\ReportIntegration;
use App\Services\MedicalRecordAccess;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Données cliniques des établissements (serveur FHIR de FIT, IHE QEDm) dans le parcours de
 * consultation : réservé aux rôles médicaux ayant accès au joueur.
 */
class ExternalClinicalDataController extends Controller
{
    public function show(Request $request, Player $player, ClinicalDataQuery $query, MedicalRecordAccess $access): View
    {
        $access->authorize($request->user(), $player, null);
        $tab = $request->query('tab', 'lab');
        abort_unless(isset(ClinicalDataQuery::CATEGORIES[$tab]), 404);

        $configured = (bool) config('fhir.base_url');
        $items = null;
        $error = null;
        $linked = $query->patientIds($player);
        $consent = app(\App\Services\Privacy\PlayerConsents::class)->allowsExternalSharing($player);
        if ($configured && $linked !== [] && $consent) {
            try {
                $items = $query->fetch($player, $tab);
            } catch (FhirException $e) {
                $error = $e->getMessage();
            }
        }

        return view('clinical.external-data', ['player' => $player, 'tab' => $tab, 'categories' => ClinicalDataQuery::CATEGORIES,
            'configured' => $configured, 'linked' => $linked, 'items' => $items, 'consent' => $consent, 'error' => $error, 'back' => $request->query('back'),
            'integrated' => $tab === 'reports' ? app(ReportIntegration::class)->integrated($player) : []]);
    }

    /** Compte rendu joint au dossier FIT sur décision du médecin. */
    public function integrate(Request $request, Player $player, ReportIntegration $integration, MedicalRecordAccess $access)
    {
        $access->authorize($request->user(), $player, null);
        $data = $request->validate(['report_id' => 'required|string|regex:/^[A-Za-z0-9\-.]{1,64}$/']);
        try {
            $document = $integration->integrate($player, $data['report_id'], $request->user());
        } catch (FhirException $e) {
            return back()->withErrors(['fhir' => $e->getMessage()]);
        }

        return back()->with('success', 'Compte rendu joint à la visite du ' . $document->visit->visit_date->format('d/m/Y') . ' dans le dossier FIT.');
    }
}
