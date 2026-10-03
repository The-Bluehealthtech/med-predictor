<?php

namespace App\Services\Medical;

use App\Models\PCMA;
use App\Models\Visit;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * PCMA réalisé dans le cadre d'une visite médicale : le secrétariat prend un
 * rendez-vous de motif « pcma », le médecin reçoit le joueur, remplit et signe
 * le PCMA rattaché à la visite ; la signature clôt la visite et le rendez-vous.
 * Le PCMA signé est celui que lit la condition d'aptitude des licences.
 */
final class PcmaVisit
{
    public const TYPE = 'pcma';

    /** Visite ouverte, de motif PCMA, concernant ce joueur, sans PCMA déjà signé. */
    public function linkable(int $visitId, int $playerId, ?int $pcmaId = null): Visit
    {
        $visit = Visit::with('athlete')->find($visitId);
        $fail = fn (string $message) => throw ValidationException::withMessages(['visit_id' => $message]);

        if (!$visit || $visit->visit_type !== self::TYPE) {
            $fail('La visite indiquée n’est pas une visite PCMA.');
        }
        if ((int) $visit->athlete?->player_id !== $playerId) {
            $fail('Le PCMA doit concerner le joueur reçu pendant la visite.');
        }
        if (in_array($visit->status, ['Terminé', 'Annulé'], true)) {
            $fail('Cette visite est clôturée.');
        }
        $signed = PCMA::query()->where('visit_id', $visit->id)->where('is_signed', true)
            ->when($pcmaId, fn ($q) => $q->whereKeyNot($pcmaId))->exists();
        if ($signed) {
            $fail('Un PCMA signé existe déjà pour cette visite.');
        }

        return $visit;
    }

    /** Signature du médecin : la visite et son rendez-vous sont terminés, le PCMA y est référencé. */
    public function close(PCMA $pcma): void
    {
        if (!$pcma->is_signed || !$pcma->visit_id) {
            return;
        }
        DB::transaction(function () use ($pcma) {
            $visit = Visit::query()->lockForUpdate()->find($pcma->visit_id);
            if (!$visit) {
                return;
            }
            $admin = $visit->administrative_data ?? [];
            $admin['pcma_id'] = $pcma->id;
            $admin['pcma_signed_at'] = optional($pcma->signed_at)->toIso8601String() ?? now()->toIso8601String();
            $visit->update(['status' => 'Terminé', 'administrative_data' => $admin]);
            if ($visit->appointment_id) {
                DB::table('appointments')->where('id', $visit->appointment_id)->update(['status' => 'Terminé', 'updated_at' => now()]);
            }
        });
    }
}
