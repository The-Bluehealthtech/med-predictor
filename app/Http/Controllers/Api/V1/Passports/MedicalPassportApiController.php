<?php

namespace App\Http\Controllers\Api\V1\Passports;

use App\Http\Controllers\Controller;
use App\Models\Player;
use App\Services\Dtn\ApiAbilities;
use App\Services\Passports\IpsFhirBundle;
use App\Services\Passports\MedicalSummary;
use App\Services\Passports\PassportAccess;
use App\Services\Passports\PassportAttestations;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * API d'échange du passeport médical : Bundle FHIR IPS pour un transfert ou une sélection.
 * Exige un jeton portant le droit médical et un compte ayant accès au joueur.
 */
class MedicalPassportApiController extends Controller
{
    public function __construct(
        private readonly PassportAccess $access,
        private readonly MedicalSummary $summaries,
        private readonly PassportAttestations $attestations,
        private readonly IpsFhirBundle $fhir,
    ) {
    }

    /** GET /api/v1/passports/medical/{player}?purpose=transfer|selection|general */
    public function show(Request $request, int $player)
    {
        abort_unless($request->user()?->tokenCan(ApiAbilities::MEDICAL), 403, 'Ce jeton n’a pas le droit médical (selections:medical).');
        $model = Player::withoutGlobalScopes()->with('club.association')->findOrFail($player);
        abort_unless($this->access->canViewMedical($request->user(), $model), 403, 'Le compte de ce jeton n’a pas accès au dossier médical de ce joueur.');
        $purpose = $request->validate(['purpose' => ['nullable', 'in:' . implode(',', array_keys(MedicalSummary::PURPOSES))]])['purpose'] ?? 'general';

        $summary = $this->summaries->build($model, $purpose, $request->user()->name);
        Log::info('passeport médical consulté', ['action' => 'api-fhir', 'user_id' => $request->user()->id, 'player_id' => $model->id, 'purpose' => $purpose, 'ip' => $request->ip()]);

        return response()->json($this->fhir->build($summary, $this->attestations->status($model, $summary)), 200,
            ['Content-Type' => 'application/fhir+json', 'Cache-Control' => 'private, no-store'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }
}
