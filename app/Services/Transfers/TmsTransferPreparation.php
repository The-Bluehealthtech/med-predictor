<?php

namespace App\Services\Transfers;

use App\Models\PlayerLicense;
use App\Models\Transfer;
use App\Models\User;

final class TmsTransferPreparation
{
    public function readiness(Transfer $transfer): array
    {
        $transfer->loadMissing([
            'player',
            'clubOrigin.association',
            'clubDestination.association',
            'documents',
            'payments.payee',
        ]);

        $requiredDocuments = ['passport', 'contract'];
        if ($transfer->is_minor_transfer) {
            $requiredDocuments[] = 'parental_consent';
        }

        $approved = $transfer->documents
            ->where('validation_status', 'approved')
            ->pluck('document_type')
            ->all();

        $blockers = [];
        $missingDocuments = array_values(array_diff($requiredDocuments, $approved));
        if ($missingDocuments !== []) {
            $blockers[] = [
                'code' => 'documents_missing',
                'label' => 'Pièces obligatoires non approuvées',
                'details' => $missingDocuments,
            ];
        }

        if (!$this->officialFifaId($transfer->player?->fifa_player_id)) {
            $blockers[] = [
                'code' => 'player_fifa_id_missing',
                'label' => 'FIFA ID joueur manquant',
            ];
        }

        if (!$this->officialFifaId($transfer->clubOrigin?->fifa_club_id)) {
            $blockers[] = [
                'code' => 'origin_fifa_id_missing',
                'label' => 'FIFA ID du club libérateur manquant',
            ];
        }

        if (!$this->officialFifaId($transfer->clubDestination?->fifa_club_id)) {
            $blockers[] = [
                'code' => 'destination_fifa_id_missing',
                'label' => 'FIFA ID du club engageant manquant',
            ];
        }

        if ((float) $transfer->transfer_fee > 0 && $transfer->payments->isEmpty()) {
            $blockers[] = [
                'code' => 'payment_schedule_missing',
                'label' => 'Échéancier de paiement manquant pour un transfert avec indemnité',
            ];
        }

        if (!$transfer->is_in_transfer_window) {
            $blockers[] = [
                'code' => 'outside_transfer_window',
                'label' => 'Fenêtre de transfert fermée',
            ];
        }

        return [
            'ready' => $blockers === [],
            'blockers' => $blockers,
            'required_documents' => $requiredDocuments,
            'approved_documents' => $approved,
            'flows' => $this->flows($transfer),
        ];
    }

    public function prepare(Transfer $transfer, User $user): Transfer
    {
        $readiness = $this->readiness($transfer);
        abort_unless($readiness['ready'], 409);

        $snapshot = $this->snapshot($transfer);

        $sha256 = hash(
            'sha256',
            json_encode($snapshot, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
        );

        $transfer->forceFill([
            'tms_sync_status' => 'ready',
            'tms_prepared_at' => now(),
            'tms_prepared_by' => $user->id,
            'tms_payload_sha256' => $sha256,
            'tms_snapshot' => $snapshot,
        ])->save();

        return $transfer->fresh();
    }

    public function link(Transfer $transfer, string $tmsTransferId): Transfer
    {
        abort_unless($transfer->tms_sync_status === 'ready' || $transfer->tms_transfer_id, 409);

        $transfer->forceFill([
            'tms_transfer_id' => trim($tmsTransferId),
            'tms_sync_status' => 'linked',
            'tms_last_synced_at' => now(),
        ])->save();

        return $transfer->fresh();
    }

    public function snapshot(Transfer $transfer): array
    {
        $transfer->loadMissing([
            'player',
            'clubOrigin.association',
            'clubDestination.association',
            'documents',
            'payments.payee',
        ]);

        return [
            'message_id' => 'FIT-TRANSFER-'.$transfer->id,
            'fit_transfer_id' => $transfer->id,
            'player' => [
                'fifa_id' => $this->officialFifaId($transfer->player?->fifa_player_id),
                'first_name' => $transfer->player?->first_name,
                'last_name' => $transfer->player?->last_name,
                'date_of_birth' => optional($transfer->player?->date_of_birth)->format('Y-m-d'),
                'nationality' => $transfer->player?->nationality,
                'gender' => $transfer->player?->gender,
                'place_of_birth' => $transfer->player?->place_of_birth,
            ],

            'releasing_club' => $this->club($transfer->clubOrigin),
            'engaging_club' => $this->club($transfer->clubDestination),
            'transfer' => [
                'type' => $transfer->transfer_type,
                'date' => optional($transfer->transfer_date)->format('Y-m-d'),
                'is_international' => (bool) $transfer->is_international,
                'fee' => $transfer->transfer_fee,
                'currency' => $transfer->currency,
                'contract_start_date' => optional($transfer->contract_start_date)->format('Y-m-d'),
                'contract_end_date' => optional($transfer->contract_end_date)->format('Y-m-d'),
            ],
            'flows' => $this->flows($transfer),
            'documents' => $transfer->documents
                ->where('validation_status', 'approved')
                ->map(fn ($document) => [
                    'type' => $document->document_type,
                    'sha256' => $document->sha256,
                    'validated_at' => optional($document->validated_at)->toIso8601String(),
                ])->values()->all(),
            'payments' => $transfer->payments->map(fn ($payment) => [
                'payment_id' => $payment->id,
                'type' => $payment->payment_type,
                'recipient_club_fifa_id' => $this->officialFifaId($payment->payee?->fifa_club_id),
                'amount' => $payment->amount,
                'currency' => $payment->currency,
                'due_date' => optional($payment->due_date)->format('Y-m-d'),
                'payment_date' => optional($payment->payment_date)->format('Y-m-d'),
            ])->values()->all(),
            'prepared_by_fit' => true,
        ];
    }

    private function flows(Transfer $transfer): array
    {
        $firstPro = $this->firstProfessionalRegistration($transfer);

        return [
            'player_sync' => [
                'applicable' => true,
                'label' => 'Synchronisation joueur TMS',
            ],
            'domestic_transfer_declaration' => [
                'applicable' => !(bool) $transfer->is_international,
                'label' => 'Domestic Transfer Declaration',
            ],
            'proof_of_payment' => [
                'applicable' => (float) $transfer->transfer_fee > 0 || $transfer->payments->isNotEmpty(),
                'label' => 'Proof of Payment',
                'payments_count' => $transfer->payments->count(),
            ],
            'first_pro_registration' => $firstPro,
        ];
    }

    private function firstProfessionalRegistration(Transfer $transfer): array
    {
        $license = PlayerLicense::withoutGlobalScopes()
            ->where('player_id', $transfer->player_id)
            ->whereNull('club_official_id')
            ->whereIn('status', ['active', 'expired'])
            ->where(function ($query) {
                $query->where('level', 'pro')
                    ->orWhere('license_type', PlayerLicense::LICENSE_TYPE_PROFESSIONAL);
            })
            ->orderByRaw('COALESCE(issue_date, approved_at, contract_start_date, created_at) asc')
            ->orderBy('id')
            ->first();

        if (!$license) {
            return [
                'applicable' => false,
                'label' => 'Premier enregistrement professionnel',
                'reason' => 'Aucune licence professionnelle délivrée dans FIT.',
            ];
        }

        $effective = $license->issue_date ?: $license->approved_at ?: $license->contract_start_date ?: $license->created_at;
        $periodStart = $transfer->transfer_date ?: $transfer->contract_start_date ?: $transfer->created_at;
        $periodEnd = $transfer->contract_end_date ?: ($periodStart ? $periodStart->copy()->addMonths(18) : null);
        $sameClub = (int) $license->club_id === (int) $transfer->club_destination_id;
        $samePeriod = $effective && $periodStart && $periodEnd
            ? $effective->copy()->startOfDay()->between(
                $periodStart->copy()->subMonths(3)->startOfDay(),
                $periodEnd->copy()->endOfDay()
            )
            : false;

        return [
            'applicable' => $sameClub && $samePeriod,
            'label' => 'Premier enregistrement professionnel',
            'first_license_id' => $license->id,
            'club_id' => $license->club_id,
            'license_number' => $license->license_number,
            'effective_date' => optional($effective)->format('Y-m-d'),
            'matches_this_transfer' => $sameClub && $samePeriod,
            'rule' => $license->level === 'pro' ? 'player_licenses.level=pro' : 'player_licenses.license_type=professional',
        ];
    }

    private function club($club): array
    {
        return [
            'fifa_id' => $this->officialFifaId($club?->fifa_club_id),
            'association_fifa_id' => $this->officialFifaId($club?->association?->fifa_connect_id),
            'name' => $club?->name,
        ];
    }

    private function officialFifaId(?string $value): ?string
    {
        $value = trim((string) $value);
        if ($value === '' || str_starts_with(strtoupper($value), 'FIT-')) {
            return null;
        }

        return $value;
    }
}
