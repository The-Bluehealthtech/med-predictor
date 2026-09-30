<?php
namespace App\Http\Controllers;
use App\Models\{PCMA, Player};
use App\Services\{MedicalRecordAccess, PcmaFormData};
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

final class PcmaDocumentController extends Controller
{
    public function generatePdf(Request $request)
    {
        $access = app(MedicalRecordAccess::class);
        $access->authorizeRole($request->user());
        $data = $request->validate(app(PcmaFormData::class)->rules(false));
        $access->input($request->user(), $data);
        // Les déclarations de signature du navigateur ne certifient pas un aperçu.
        foreach (['signature_data', 'signature_image', 'is_signed', 'signed_by', 'signed_at',
            'license_number', 'fifa_compliant'] as $key) unset($data[$key]);
        $pcma = new PCMA(app(PcmaFormData::class)->preserve($data));
        $pcma->setRelation('player', Player::findOrFail($data['player_id']));
        $pcma->setRelation('assessor', $request->user());
        return $this->document($pcma, true)->download('PCMA-draft.pdf');
    }
    public function export(PCMA $pcma)
    {
        app(MedicalRecordAccess::class)->record(request()->user(), $pcma);
        return $this->document($pcma, false)->download('PCMA-'.$pcma->id.'.pdf');
    }
    public function file(Request $request, PCMA $pcma, string $field)
    {
        app(MedicalRecordAccess::class)->record($request->user(), $pcma);
        abort_unless(in_array($field, ['ecg_file', 'mri_file', 'xray_file', 'ct_scan_file',
            'ultrasound_file', 'signature_image'], true), 404);
        $path = $pcma->$field;
        abort_unless(is_string($path) && $path && !str_contains($path, '..'), 404);
        $disk = \Illuminate\Support\Facades\Storage::disk('local')->exists($path) ? 'local' : 'public';
        abort_unless(\Illuminate\Support\Facades\Storage::disk($disk)->exists($path), 404);
        return \Illuminate\Support\Facades\Storage::disk($disk)->response($path);
    }
    private function document(PCMA $pcma, bool $isDraft)
    {
        $pcma->loadMissing(['player', 'athlete', 'assessor']);
        $formData = $pcma->result_json ?? [];
        if (!is_array($formData)) $formData = [];
        unset($formData['draft_token'], $formData['signature_data']);
        foreach (['type', 'status', 'notes', 'medical_history', 'physical_examination',
            'cardiovascular_investigations', 'final_statement', 'scat_assessment',
            'anatomical_annotations'] as $field) {
            if ($pcma->$field !== null) $formData[$field] = $pcma->$field;
        }
        return Pdf::loadView('pcma.pdf', ['pcma' => $pcma, 'formData' => $formData,
            'isDraft' => $isDraft, 'generatedAt' => now()])->setOption('isRemoteEnabled', false);
    }
}
