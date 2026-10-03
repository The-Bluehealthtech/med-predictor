<?php
namespace App\Http\Controllers;
use App\Models\{PCMA, User};
use App\Services\{MedicalRecordAccess, PcmaFormData};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

final class PcmaDraftController extends Controller
{
    public function save(Request $request)
    {
        $access = app(MedicalRecordAccess::class);
        $access->authorizeRole($request->user());
        $rules = app(PcmaFormData::class)->rules(false);
        $rules['status'] = 'sometimes|in:pending';
        $rules['pcma_id'] = 'nullable|integer';
        $rules['draft_token'] = 'required|uuid';
        // La signature et la décision d'aptitude ne sont jamais produites par l'autosauvegarde.
        $data = app(PcmaFormData::class)->withoutSignature($request->validate($rules));
        $access->input($request->user(), $data);
        if (!empty($data['visit_id'])) {
            app(\App\Services\Medical\PcmaVisit::class)->linkable((int) $data['visit_id'], (int) $data['player_id'], !empty($data['pcma_id']) ? (int) $data['pcma_id'] : null);
        } else {
            unset($data['visit_id']);
        }
        $pcma = DB::transaction(function () use ($data, $request, $access) {
            // Sérialiser les premières sauvegardes du même médecin pour éviter les doublons.
            User::whereKey($request->user()->id)->lockForUpdate()->firstOrFail();
            $pcma = !empty($data['pcma_id']) ? PCMA::lockForUpdate()->findOrFail($data['pcma_id'])
                : PCMA::where('assessor_id', $request->user()->id)
                    ->where('result_json->draft_token', $data['draft_token'])->lockForUpdate()->first();
            if ($pcma) {
                $access->record($request->user(), $pcma, true);
                abort_unless($pcma->status === 'pending', 409, 'Ce dossier n’est plus un brouillon.');
                abort_unless((int) $pcma->player_id === (int) $data['player_id'], 409,
                    'Créez un nouveau brouillon pour changer de joueur.');
            }
            $payload = app(PcmaFormData::class)->preserve($data, $pcma?->result_json);
            $payload['result_json']['draft_token'] = $data['draft_token'];
            unset($payload['pcma_id'], $payload['draft_token'], $payload['final_statement'],
                $payload['signature_data'], $payload['signature_image'], $payload['signed_by'],
                $payload['signed_at'], $payload['is_signed'], $payload['license_number']);
            foreach (['ecg_file', 'mri_file', 'xray_file', 'ct_scan_file', 'ultrasound_file'] as $field) {
                if ($request->hasFile($field)) {
                    $payload[$field] = app(\App\Services\MedicalFileStore::class)->put($request->file($field), 'pcma', $field)->ref();
                }
            }
            $payload['status'] = 'pending';
            $payload['fifa_compliant'] = false;
            $payload['assessor_id'] = $request->user()->id;
            if (!$pcma) $pcma = new PCMA();
            $pcma->fill($payload)->save();
            return $pcma;
        });
        return response()->json(['success' => true, 'pcma_id' => $pcma->id,
            'message' => __('pcma_workflow.draft_saved')]);
    }
}
