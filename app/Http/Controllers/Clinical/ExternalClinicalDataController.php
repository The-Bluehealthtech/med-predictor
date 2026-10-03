<?php

namespace App\Http\Controllers\Clinical;

use App\Http\Controllers\Controller;
use App\Models\Player;
use App\Services\Fhir\ClinicalDataQuery;
use App\Services\Fhir\FhirException;
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
        if ($configured && $linked !== []) {
            try {
                $items = $query->fetch($player, $tab);
            } catch (FhirException $e) {
                $error = $e->getMessage();
            }
        }

        return view('clinical.external-data', ['player' => $player, 'tab' => $tab, 'categories' => ClinicalDataQuery::CATEGORIES,
            'configured' => $configured, 'linked' => $linked, 'items' => $items, 'error' => $error, 'back' => $request->query('back')]);
    }
}
