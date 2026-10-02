<?php

namespace App\Services\Passports;

use App\Models\Player;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Passeport de transfert au format du passeport joueur FIFA (Règlement du
 * statut et du transfert des joueurs, art. 7) : identité d'enregistrement,
 * clubs successifs avec périodes et statut amateur ou professionnel,
 * transferts et certificat international de transfert (ITC).
 * Construit à partir des seules tables locales ; aucune donnée médicale.
 */
final class TransferPassport
{
    public function build(Player $player): array
    {
        $passport = Schema::hasTable('player_passports')
            ? DB::table('player_passports')->where('player_id', $player->id)->orderByDesc('id')->first()
            : null;
        $clubNames = fn (array $ids) => DB::table('clubs')->whereIn('id', array_filter($ids))->pluck('name', 'id');
        $associationOfClub = fn (array $ids) => DB::table('clubs')->leftJoin('associations', 'associations.id', '=', 'clubs.association_id')
            ->whereIn('clubs.id', array_filter($ids))->pluck('associations.name', 'clubs.id');

        // Enregistrements : historique des licences puis licences du joueur.
        $registrations = collect();
        if (Schema::hasTable('player_license_histories')) {
            $history = DB::table('player_license_histories')->where('player_id', $player->id)->get();
            $names = $clubNames($history->pluck('club_id')->all());
            $associations = $associationOfClub($history->pluck('club_id')->all());
            $registrations = $registrations->merge($history->map(fn ($h) => [
                'from' => $h->date_debut, 'to' => $h->date_fin, 'club' => $names[$h->club_id] ?? '—',
                'association' => $associations[$h->club_id] ?? null, 'status' => $this->status($h->type_licence ?? null),
                'season' => null, 'number' => $h->license_number ?? null, 'source' => 'Historique des licences',
            ]));
        }
        if (Schema::hasTable('player_licenses')) {
            $licences = DB::table('player_licenses')->where('player_id', $player->id)->get();
            $names = $clubNames($licences->pluck('club_id')->all());
            $associations = $associationOfClub($licences->pluck('club_id')->all());
            $registrations = $registrations->merge($licences->map(fn ($l) => [
                'from' => $l->issue_date ?? $l->contract_start_date ?? null, 'to' => $l->expiry_date ?? $l->contract_end_date ?? null,
                'club' => $names[$l->club_id] ?? '—', 'association' => $associations[$l->club_id] ?? null,
                'status' => $this->status(($l->contract_type ?? null) ?: ($l->license_category ?? null) ?: ($l->license_type ?? null)),
                'season' => $l->season ?? null, 'number' => $l->license_number ?? null, 'source' => 'Licence',
            ]));
        }
        $registrations = $registrations->sortBy(fn ($r) => (string) $r['from'])->values();

        $transfers = collect();
        if (Schema::hasTable('transfers')) {
            $rows = DB::table('transfers')->where('player_id', $player->id)->orderBy('transfer_date')->get();
            $names = $clubNames($rows->pluck('club_origin_id')->merge($rows->pluck('club_destination_id'))->all());
            $transfers = $rows->map(fn ($t) => [
                'date' => $t->transfer_date, 'from' => $names[$t->club_origin_id] ?? '—', 'to' => $names[$t->club_destination_id] ?? '—',
                'type' => $t->transfer_type, 'status' => $t->transfer_status, 'itc' => $t->itc_status,
                'international' => (bool) $t->is_international, 'fifa_id' => $t->fifa_transfer_id,
            ]);
        }

        return [
            'document' => [
                'standard' => 'Passeport joueur FIFA (RSTJ, art. 7)',
                'generated_at' => now(),
                'number' => $passport->passport_number ?? null,
                'registration_number' => $passport->registration_number ?? null,
                'issuing_authority' => $passport->issuing_authority ?? null,
                'issuing_country' => $passport->issuing_country ?? null,
                'status' => $passport->status ?? null,
                'issue_date' => $passport->issue_date ?? null,
                'expiry_date' => $passport->expiry_date ?? null,
                'itc_status' => $passport->itc_status ?? null,
            ],
            'player' => [
                'id' => $player->id,
                'name' => trim($player->first_name . ' ' . $player->last_name) ?: $player->name,
                'birth_date' => $player->date_of_birth,
                'twelfth_birthday' => $player->date_of_birth ? Carbon::parse($player->date_of_birth)->addYears(12) : null,
                'nationality' => $player->nationality,
                'fifa_connect_id' => $player->fifa_connect_id ?? ($passport->fifa_connect_id ?? null),
                'club' => $player->club?->name,
                'association' => $player->club?->association?->name,
                'position' => $player->position,
            ],
            'registrations' => $registrations->all(),
            'transfers' => $transfers->all(),
        ];
    }

    /** Statut au sens FIFA : amateur ou professionnel, quand l'information est disponible. */
    private function status(?string $raw): ?string
    {
        $raw = mb_strtolower((string) $raw);

        return match (true) {
            $raw === '' => null,
            str_contains($raw, 'pro') => 'Professionnel',
            str_contains($raw, 'amat') => 'Amateur',
            str_contains($raw, 'youth') || str_contains($raw, 'jeune') => 'Jeune (amateur)',
            default => null,
        };
    }
}
