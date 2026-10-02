<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Audit trail en ajout seul, au niveau de la base (PostgreSQL) : toute
 * modification, suppression ou vidage de audit_logs est refusé, même hors de
 * l'application. Seule exception : la purge de rétention (commande
 * audit:retention), qui s'annonce par le réglage de transaction
 * fit.audit_retention_purge et est elle-même journalisée.
 *
 * Limite : le propriétaire de la base peut toujours retirer ces triggers ;
 * la protection vise l'application, les scripts et les erreurs de manipulation.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() !== 'pgsql' || !Schema::hasTable('audit_logs')) {
            return;
        }
        DB::unprepared(<<<'SQL'
CREATE OR REPLACE FUNCTION fit_audit_logs_append_only() RETURNS trigger AS $$
BEGIN
    IF TG_OP = 'DELETE' AND current_setting('fit.audit_retention_purge', true) = 'on' THEN
        RETURN OLD;
    END IF;
    RAISE EXCEPTION 'audit_logs est en ajout seul : % refusé', TG_OP USING ERRCODE = 'insufficient_privilege';
END;
$$ LANGUAGE plpgsql;

DROP TRIGGER IF EXISTS audit_logs_append_only ON audit_logs;
CREATE TRIGGER audit_logs_append_only BEFORE UPDATE OR DELETE ON audit_logs
    FOR EACH ROW EXECUTE FUNCTION fit_audit_logs_append_only();

DROP TRIGGER IF EXISTS audit_logs_no_truncate ON audit_logs;
CREATE TRIGGER audit_logs_no_truncate BEFORE TRUNCATE ON audit_logs
    FOR EACH STATEMENT EXECUTE FUNCTION fit_audit_logs_append_only();
SQL);
    }

    public function down(): void
    {
        if (DB::getDriverName() !== 'pgsql') {
            return;
        }
        DB::unprepared(<<<'SQL'
DROP TRIGGER IF EXISTS audit_logs_append_only ON audit_logs;
DROP TRIGGER IF EXISTS audit_logs_no_truncate ON audit_logs;
DROP FUNCTION IF EXISTS fit_audit_logs_append_only();
SQL);
    }
};
