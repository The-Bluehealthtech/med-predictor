<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Motif de visite « pcma » : rendez-vous pris par le secrétariat médical pour
 * une évaluation médicale pré-compétition, réalisée et signée par le médecin
 * pendant la visite (condition d'aptitude des licences).
 */
return new class extends Migration
{
    private const TYPES = ['consultation', 'emergency', 'follow_up', 'pre_season', 'post_match', 'rehabilitation',
        'routine_checkup', 'injury_assessment', 'cardiac_evaluation', 'concussion_assessment'];

    private const COLUMNS = ['appointments' => 'appointment_type', 'visits' => 'visit_type'];

    public function up(): void
    {
        $this->allow([...self::TYPES, 'pcma']);
    }

    public function down(): void
    {
        DB::table('appointments')->where('appointment_type', 'pcma')->update(['appointment_type' => 'routine_checkup']);
        DB::table('visits')->where('visit_type', 'pcma')->update(['visit_type' => 'routine_checkup']);
        $this->allow(self::TYPES);
    }

    private function allow(array $types): void
    {
        foreach (self::COLUMNS as $table => $column) {
            if (!Schema::hasTable($table)) {
                continue;
            }
            if (DB::getDriverName() === 'pgsql') {
                $list = implode(', ', array_map(fn ($t) => "'{$t}'::character varying", $types));
                DB::statement("ALTER TABLE {$table} DROP CONSTRAINT IF EXISTS {$table}_{$column}_check");
                DB::statement("ALTER TABLE {$table} ADD CONSTRAINT {$table}_{$column}_check CHECK ((({$column})::text = ANY ((ARRAY[{$list}])::text[])))");
            } else {
                // SQLite (tests) : la contrainte d'énumération disparaît avec la reconstruction de la colonne.
                Schema::table($table, fn (Blueprint $t) => $t->string($column, 40)->change());
            }
        }
    }
};
