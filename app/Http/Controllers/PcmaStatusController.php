<?php

namespace App\Http\Controllers;

use App\Models\PCMA;
use App\Services\MedicalRecordAccess;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

final class PcmaStatusController extends Controller
{
    public function complete(Request $request, PCMA $pcma) { return $this->transition($request, $pcma, 'completed'); }
    public function fail(Request $request, PCMA $pcma) { return $this->transition($request, $pcma, 'failed'); }

    private function transition(Request $request, PCMA $pcma, string $status)
    {
        abort_unless($request->user(), 401);
        app(MedicalRecordAccess::class)->authorize($request->user(), $pcma->player, $pcma->athlete);
        $updated = DB::transaction(function () use ($pcma, $status) {
            $record = PCMA::whereKey($pcma->id)->lockForUpdate()->firstOrFail();
            $record->update(['status' => $status, 'completed_at' => now()]);
            return $record->fresh();
        });
        return response()->json(['success' => true, 'data' => ['id' => $updated->id,
            'status' => $updated->status, 'completed_at' => $updated->completed_at]]);
    }
}
