<?php

namespace App\Services\FifaConnect;

use App\Models\Association;
use App\Models\FifaConnect\DataHolder;
use App\Models\Player;
use Illuminate\Support\Collection;
use InvalidArgumentException;
use RuntimeException;

class DataHolderService
{
    public function __construct(private readonly SchemaCatalog $catalog)
    {
    }

    public function recordStoredFifaId(
        Association $association,
        string $fifaId,
        bool $connectMutationSent
    ): DataHolder {
        $fifaId = trim($fifaId);

        try {
            $this->catalog->assertFifaIdentifier($fifaId);
        } catch (RuntimeException $exception) {
            throw new InvalidArgumentException('Invalid FIFA_ID.', 0, $exception);
        }

        return DataHolder::query()->updateOrCreate(
            [
                'association_id' => $association->id,
                'person_fifa_id' => $fifaId,
            ],
            [
                'claim_status' => $connectMutationSent
                    ? 'covered_by_mutation'
                    : 'claim_required',
                'claim_reason' => $connectMutationSent
                    ? 'connect_id_mutation_sent'
                    : 'existing_fifa_id_stored_without_mutation',
                'last_error' => null,
            ]
        );
    }

    public function markClaimAccepted(DataHolder $holder): DataHolder
    {
        $holder->forceFill([
            'claim_status' => 'registered',
            'registered_as_holder_at' => now(),
            'last_error' => null,
        ])->save();

        return $holder->refresh();
    }

    public function markVerified(
        DataHolder $holder,
        string $remoteStatus = 'confirmed'
    ): DataHolder {
        $holder->forceFill([
            'claim_status' => 'verified',
            'last_verified_at' => now(),
            'last_remote_status' => $remoteStatus,
            'last_error' => null,
        ])->save();

        return $holder->refresh();
    }

    public function markMerged(
        DataHolder $holder,
        string $primaryFifaId
    ): DataHolder {
        $holder->forceFill([
            'claim_status' => 'merged',
            'merged_into_fifa_id' => trim($primaryFifaId),
        ])->save();

        return $holder->refresh();
    }

    public function productionIdsForBulkClaim(
        Association $association
    ): Collection {
        $excluded = DataHolder::query()
            ->where('association_id', $association->id)
            ->where(function ($query) {
                $query->whereNotNull('merged_into_fifa_id')
                    ->orWhere('remote_deleted', true)
                    ->orWhereIn('claim_status', [
                        'covered_by_mutation', 'registered', 'verified',
                    ]);
            })
            ->pluck('person_fifa_id');

        return Player::query()
            ->where('association_id', $association->id)
            ->whereNotNull('fifa_connect_id')
            ->where('fifa_connect_id', '<>', '')
            ->whereNotIn('fifa_connect_id', $excluded)
            ->pluck('fifa_connect_id')
            ->map(fn ($id) => trim((string) $id))
            ->filter(function ($id) {
                try {
                    $this->catalog->assertFifaIdentifier($id);
                    return true;
                } catch (RuntimeException) {
                    return false;
                }
            })
            ->unique()
            ->sort()
            ->values();
    }

    public function bulkClaimCsv(Association $association): string
    {
        return $this->productionIdsForBulkClaim($association)
            ->implode("\n");
    }
}
