<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PlayerPassportTestFixtureSeeder extends Seeder
{
    public function run(): void
    {
        if (DB::getDriverName() !== 'pgsql') {
            throw new \RuntimeException('Ce complément groupé nécessite PostgreSQL.');
        }

        $creatorId = DB::table('users')->min('id');
        if ($creatorId === null) {
            throw new \RuntimeException('Aucun utilisateur disponible pour created_by.');
        }

        $updated = DB::affectingStatement(<<<'SQL'
            UPDATE player_passports p
            SET passport_number = COALESCE(NULLIF(p.passport_number, ''), 'SYNTH-PASSPORT-' || p.player_id::text),
                status = COALESCE(p.status, 'pending_validation'),
                issuing_authority = COALESCE(NULLIF(p.issuing_authority, ''), 'Autorité fictive de test'),
                updated_at = CURRENT_TIMESTAMP
            WHERE EXISTS (
                SELECT 1 FROM player_real_time_health rt
                WHERE rt.player_id = p.player_id AND rt.notes LIKE 'synthetic_demo%'
            ) AND (
                p.passport_number IS NULL OR p.passport_number = ''
                OR p.status IS NULL OR p.issuing_authority IS NULL OR p.issuing_authority = ''
            )
        SQL);

        $inserted = DB::affectingStatement(<<<'SQL'
            INSERT INTO player_passports (
                player_id, passport_number, passport_type, status,
                issue_date, expiry_date, issuing_authority, issuing_country,
                created_by, notes, created_at, updated_at
            )
            SELECT DISTINCT rt.player_id,
                'SYNTH-PASSPORT-' || rt.player_id::text,
                'temporary', 'pending_validation',
                DATE '2026-09-01', DATE '2027-09-01',
                'Autorité fictive de test', 'TEST', ?,
                '{"source":"synthetic_demo","official":false}'::json,
                CURRENT_TIMESTAMP, CURRENT_TIMESTAMP
            FROM player_real_time_health rt
            WHERE rt.notes LIKE 'synthetic_demo%'
              AND NOT EXISTS (
                  SELECT 1 FROM player_passports p WHERE p.player_id = rt.player_id
              )
        SQL, [(int) $creatorId]);

        $this->command?->info("Passeports synthétiques non validés : {$inserted} créés, {$updated} complétés.");
    }
}
