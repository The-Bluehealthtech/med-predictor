<?php

namespace App\Http\Controllers;

use App\Models\Athlete;
use App\Models\PCMA;
use App\Models\User;
use App\Services\MedicalRecordAccess;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

final class PcmaDocumentController extends Controller
{
    public function generatePdf(Request $request)
    {
        abort_unless($request->user(), 401);
        $rules = (new \App\Http\Requests\StoreFifaCompliantPCMARequest())->rules();
        $rules['status'] = 'sometimes|in:pending,completed,failed,cleared,not_cleared';
        $data = Validator::make($request->all(), $rules)->validate();
        $athlete = Athlete::findOrFail($data['athlete_id']);
        app(MedicalRecordAccess::class)->authorize($request->user(), $athlete->player, $athlete);
        abort_if(isset($data['player_id']) && (int) $data['player_id'] !== (int) $athlete->player_id, 422);
        $assessor = User::findOrFail($data['assessor_id']);
        // Un aperçu ne certifie aucune signature transmise par le navigateur.
        $pcma = new PCMA($data);
        $pcma->setRelation('athlete', $athlete);
        $pcma->setRelation('assessor', $assessor);
        $formData = $data;
        $generatedAt = now();
        $isDraft = true;
        return Pdf::loadView('pcma.pdf', compact('pcma', 'formData', 'athlete', 'generatedAt', 'isDraft'))
            ->download('PCMA-draft.pdf');
    }
}
