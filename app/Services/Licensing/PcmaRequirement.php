<?php

namespace App\Services\Licensing;

use App\Models\Player;
use App\Models\PlayerLicense;
use App\Services\PlayerPcmaData;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Condition d'aptitude médicale (PCMA) pour une licence de joueur : règle de la
 * catégorie d'âge dans le barème de la fédération (jamais, joueurs pro, tous). Seuls le statut,
 * la date et la conclusion sont exposés, jamais le contenu médical du bilan.
 */
final class PcmaRequirement
{
    public const STATES = [
        'valid' => 'Apte — PCMA signé',
        'conditional' => 'Apte avec restrictions — PCMA signé',
        'not_fit' => 'Inapte selon le dernier PCMA',
        'expired' => 'PCMA trop ancien',
        'unsigned' => 'PCMA non signé par un médecin',
        'missing' => 'Aucun PCMA enregistré',
    ];

    public function __construct(private readonly PlayerPcmaData $pcma)
    {
    }

    /** Exigence de PCMA d'un joueur selon le barème (catégorie d'âge, niveau). @return array{required:bool, reason:string} */
    public function requirement(Player $player, string $discipline, string $level): array
    {
        $rules = app(LicenseScale::class)->playerRules($player, $discipline, $level);

        return ['required' => $rules['pcma_required'], 'reason' => $rules['pcma_reason']];
    }

    /** État du PCMA le plus récent du joueur (bilans synthétiques de démonstration exclus). */
    public function status(Player $player): array
    {
        $records = DB::table('pcmas')->where('player_id', $player->id)
            ->orderByDesc('assessment_date')->orderByDesc('id')->limit(20)->get();
        foreach ($records as $record) {
            $data = $this->pcma->fromRecord($record);
            if ($data->synthetic_test) {
                continue;
            }
            $date = $record->assessment_date ? Carbon::parse($record->assessment_date) : null;
            $state = match (true) {
                !$data->is_signed || $data->medical_decision === null => 'unsigned',
                $data->medical_decision === 'NOT_FIT' => 'not_fit',
                !$date || $date->lt(now()->subMonths((int) config('licensing.pcma.validity_months', 12))) => 'expired',
                $data->medical_decision === 'CONDITIONAL' => 'conditional',
                default => 'valid',
            };

            return ['state' => $state, 'label' => self::STATES[$state], 'date' => $date, 'valid' => in_array($state, ['valid', 'conditional'], true)];
        }

        return ['state' => 'missing', 'label' => self::STATES['missing'], 'date' => null, 'valid' => false];
    }

    /** Condition et état réunis pour une demande ; « blocking » : l'approbation est impossible. */
    public function check(PlayerLicense $license): array
    {
        $player = $license->player;
        if ($license->club_official_id || !$player) {
            return ['required' => false, 'reason' => 'Licence d\'officiel : PCMA non exigé.', 'status' => ['state' => 'missing', 'label' => self::STATES['missing'], 'date' => null, 'valid' => false], 'blocking' => false];
        }
        // Licence antérieure à l'alignement FIFA Connect : niveau déduit de l'ancien type.
        $level = $license->level ?: ($license->license_type === 'professional' ? 'pro' : 'amateur');
        $requirement = $this->requirement($player, $license->discipline ?: 'Football', $level);
        $status = $this->status($player);

        return $requirement + ['status' => $status, 'blocking' => $requirement['required'] && !$status['valid']];
    }
}
