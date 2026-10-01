<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\{DB, Schema};
return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('pcmas')) return;
        // Retirer la contrainte historique avant de normaliser la valeur.
        if (DB::getDriverName() === 'pgsql') {
            DB::statement("SET LOCAL lock_timeout = '10s'");
            DB::statement("SET LOCAL statement_timeout = '90s'");
            $checks = DB::select("SELECT conname FROM pg_constraint WHERE conrelid = 'pcmas'::regclass AND contype = 'c' AND pg_get_constraintdef(oid) LIKE '%bpma%'");
            foreach ($checks as $check) {
                $name = str_replace('"', '""', $check->conname);
                DB::statement('ALTER TABLE pcmas DROP CONSTRAINT "'.$name.'"');
            }
        }
        if (DB::getDriverName() === 'sqlite') {
            // SQLite conserve le CHECK historique lors d'un simple change().
            // Remplacement littéral de l'énumération, sans recréer les lignes ni leurs liens.
            DB::transaction(function () {
                $schema = DB::selectOne("SELECT sql FROM sqlite_master WHERE type = 'table' AND name = 'pcmas'");
                $sql = str_replace("'bpma'", "'pcma'", $schema->sql);
                if ($sql !== $schema->sql) {
                    $version = (int) DB::selectOne('PRAGMA schema_version')->schema_version;
                    DB::statement('PRAGMA writable_schema = ON');
                    try { DB::update("UPDATE sqlite_master SET sql = ? WHERE type = 'table' AND name = 'pcmas'", [$sql]); }
                    finally { DB::statement('PRAGMA writable_schema = OFF'); }
                    DB::statement('PRAGMA schema_version = '.($version + 1));
                }
                DB::table('pcmas')->where('type', 'bpma')->update(['type' => 'pcma']);
                $integrity = DB::selectOne('PRAGMA integrity_check');
                if (array_values((array) $integrity)[0] !== 'ok') throw new \RuntimeException('Intégrité SQLite non valide après normalisation PCMA.');
            });
            return;
        }
        Schema::table('pcmas', function (Blueprint $table) { $table->string('type')->change(); });
        // Mise à jour de nomenclature uniquement : aucune observation clinique ne change.
        DB::table('pcmas')->where('type', 'bpma')->update(['type' => 'pcma']);
    }
    public function down(): void
    {
        // Ne pas réintroduire une dénomination médicale erronée.
    }
};
