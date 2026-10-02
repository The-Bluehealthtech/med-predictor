<?php

namespace Tests\Feature;

use App\Http\Controllers\AuditTrailController;
use App\Models\AuditLog;
use App\Models\Club;
use App\Models\User;
use App\Services\Audit\Auditor;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

/**
 * Audit trail opérationnel : authentification, modifications des modèles suivis
 * (sans valeurs pour les données de santé), consultations sensibles, journal en
 * ajout seul, écran d'administration et export tracé.
 */
class AuditTrailTest extends TestCase
{
    use DatabaseTransactions;

    private function lastLog(): ?AuditLog
    {
        return AuditLog::query()->orderByDesc('id')->first();
    }

    private function admin(): User
    {
        return User::factory()->create(['role' => 'system_admin', 'status' => 'active']);
    }

    public function test_logins_failures_and_logouts_are_recorded(): void
    {
        $user = User::factory()->create(['email' => 'audit-login@example.invalid', 'password' => Hash::make('secret-ok'), 'status' => 'active']);

        $this->assertFalse(Auth::attempt(['email' => 'audit-login@example.invalid', 'password' => 'wrong']));
        $failed = $this->lastLog();
        $this->assertSame('login_failed', $failed->action);
        $this->assertSame('warning', $failed->severity);
        $this->assertSame('audit-login@example.invalid', $failed->metadata['attempted_email']);
        $this->assertTrue($failed->metadata['known_account']);

        Auth::login($user);
        $login = $this->lastLog();
        $this->assertSame('login', $login->action);
        $this->assertSame($user->id, (int) $login->user_id);
        $this->assertSame($user->email, $login->user_email);

        Auth::logout();
        $this->assertSame('logout', $this->lastLog()->action, 'le renouvellement du jeton de session ne crée pas de fausse modification');
    }

    public function test_changes_keep_old_and_new_values_but_never_secrets(): void
    {
        $admin = $this->admin();
        $this->actingAs($admin);
        $club = Club::query()->forceCreate(['name' => 'Club Audit Avant']);
        $this->assertSame('created', $this->lastLog()->action);

        $club->update(['name' => 'Club Audit Après']);
        $log = $this->lastLog();
        $this->assertSame('model_change', $log->event_type);
        $this->assertSame('updated', $log->action);
        $this->assertSame(Club::class, $log->model_type);
        $this->assertSame(['name' => 'Club Audit Avant'], $log->old_values);
        $this->assertSame(['name' => 'Club Audit Après'], $log->new_values);
        $this->assertSame($admin->email, $log->user_email);

        $target = User::factory()->create(['status' => 'active']);
        $target->update(['password' => Hash::make('nouveau-secret')]);
        $log = $this->lastLog();
        $this->assertContains('password', $log->changes['fields'], 'le changement de mot de passe est tracé');
        $this->assertArrayNotHasKey('password', $log->new_values ?? [], 'jamais la valeur du mot de passe');
        $this->assertArrayNotHasKey('password', $log->old_values ?? []);
    }

    public function test_health_data_changes_keep_field_names_only(): void
    {
        $this->actingAs($this->admin());
        $club = Club::query()->forceCreate(['name' => 'Dossier']);
        $club->name = 'Diagnostic confidentiel';
        $club->save();

        app(Auditor::class)->modelEvent('updated', $club, ['module' => 'medical', 'sensitive' => true]);
        $log = $this->lastLog();
        $this->assertNull($log->old_values);
        $this->assertNull($log->new_values);
        $this->assertSame(['name'], $log->changes['fields']);
        $this->assertTrue($log->changes['sensitive']);
        $this->assertStringNotContainsString('confidentiel', json_encode($log->toArray()));
    }

    public function test_command_line_writes_are_not_attributed_to_anyone(): void
    {
        $before = AuditLog::query()->count();
        Club::query()->forceCreate(['name' => 'Import en ligne de commande']);
        $this->assertSame($before, AuditLog::query()->count());
    }

    public function test_the_journal_is_append_only(): void
    {
        $this->actingAs($this->admin());
        $log = app(Auditor::class)->record(['event_type' => 'system', 'action' => 'probe', 'description' => 'Événement de test']);

        try {
            $log->update(['description' => 'falsifié']);
            $this->fail('la modification doit être refusée');
        } catch (\LogicException) {
        }
        try {
            $log->delete();
            $this->fail('la suppression doit être refusée');
        } catch (\LogicException) {
        }
        $this->assertSame('Événement de test', AuditLog::query()->find($log->id)->description);
    }

    public function test_viewing_a_sensitive_route_is_recorded(): void
    {
        Route::middleware(['web', 'auth'])->get('/_test/audit/dossier/{id}', fn ($id) => 'contenu médical')->name('health-records.show');
        $user = $this->admin();

        $this->actingAs($user)->get('/_test/audit/dossier/42')->assertOk();
        $log = $this->lastLog();
        $this->assertSame('data_access', $log->event_type);
        $this->assertSame('view', $log->action);
        $this->assertSame(['route' => 'health-records.show', 'parameters' => ['id' => '42']], $log->metadata);
        $this->assertStringNotContainsString('contenu médical', json_encode($log->toArray()));
    }

    public function test_admin_screen_lists_and_exports_with_the_export_itself_traced(): void
    {
        Route::middleware(['web', 'auth'])->prefix('admin/audit-trail')->name('admin.audit-trail.')->group(function () {
            Route::get('/', [AuditTrailController::class, 'index'])->name('index');
            Route::get('/export', [AuditTrailController::class, 'export'])->name('export');
            Route::get('/{id}', [AuditTrailController::class, 'show'])->whereNumber('id')->name('show');
        });
        if (!Route::has('modules.administration.index')) {
            Route::get('/_test/administration', fn () => 'ok')->name('modules.administration.index');
        }
        app('router')->getRoutes()->refreshNameLookups();

        $admin = $this->admin();
        $this->actingAs($admin);
        $log = app(Auditor::class)->record(['event_type' => 'security', 'action' => 'probe', 'description' => 'Visible dans l\'écran']);

        $this->get('/admin/audit-trail')->assertOk()->assertSee('Visible dans l')->assertDontSee('Nettoyer les anciens logs');
        $this->get('/admin/audit-trail/' . $log->id)->assertOk()->assertSee('Visible dans l');

        $export = $this->get('/admin/audit-trail/export?format=csv');
        $export->assertOk();
        $this->assertStringContainsString('text/csv', $export->headers->get('content-type'));
        $this->assertSame('export', $this->lastLog()->action, "l'export du journal est tracé");

        $this->actingAs(User::factory()->create(['role' => 'club_admin', 'status' => 'active']))
            ->get('/admin/audit-trail')->assertStatus(302)->assertDontSee('Visible dans l');
    }

    public function test_retention_purge_is_simulated_by_default_and_journaled_when_forced(): void
    {
        $old = DB::table('audit_logs')->insertGetId(['event_type' => 'system', 'model_type' => 'system', 'action' => 'ancien',
            'severity' => 'info', 'created_at' => now()->subYears(config('audit.retention_years') + 1), 'updated_at' => now()]);

        $this->artisan('audit:retention')->assertExitCode(0);
        $this->assertTrue(DB::table('audit_logs')->where('id', $old)->exists(), 'simulation : rien n\'est supprimé');

        $this->artisan('audit:retention', ['--force' => true])->assertExitCode(0);
        $this->assertFalse(DB::table('audit_logs')->where('id', $old)->exists());
        $purge = $this->lastLog();
        $this->assertSame('retention_purge', $purge->action);
        $this->assertGreaterThanOrEqual(1, $purge->metadata['deleted']);
    }
}
