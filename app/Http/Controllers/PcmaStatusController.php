<?php
namespace App\Http\Controllers;
use App\Models\PCMA;
use App\Services\MedicalRecordAccess;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

final class PcmaStatusController extends Controller
{
    public function complete(Request $request, PCMA $pcma)
    {
        return $this->transition($request, $pcma, 'completed');
    }
    public function fail(Request $request, PCMA $pcma)
    {
        return $this->transition($request, $pcma, 'failed');
    }
    private function transition(Request $request, PCMA $pcma, string $status)
    {
        $access = app(MedicalRecordAccess::class);
        $access->authorizeRole($request->user());
        DB::transaction(function () use ($pcma, $status, $request, $access) {
            $record = PCMA::lockForUpdate()->findOrFail($pcma->id);
            $access->record($request->user(), $record, true);
            if ($status === 'completed') {
                $final = $record->final_statement ?? [];
                abort_unless(in_array($final['overall_decision'] ?? null, ['FIT', 'NOT_FIT', 'CONDITIONAL'], true)
                    || !empty($final['cleared_for_competition']) || !empty($final['cleared_with_restrictions'])
                    || !empty($final['not_cleared']), 422, 'La conclusion du médecin est requise.');
            }
            $record->update(['status' => $status, 'completed_at' => now()]);
        });
        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json(['success' => true, 'data' => $pcma->fresh()]);
        }
        return redirect()->route('pcma.show', $pcma)->with('success', __('pcma_workflow.status_saved'));
    }
}
