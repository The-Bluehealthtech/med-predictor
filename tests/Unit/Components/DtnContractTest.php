<?php

namespace Tests\Unit\Components;

use PHPUnit\Framework\TestCase;

/**
 * Contrat de l'outil DTN (sélections nationales, partage club ↔ Direction
 * technique nationale) : routes réelles protégées, règles d'accès
 * centralisées, partie médicale cloisonnée, pas de données fictives.
 */
class DtnContractTest extends TestCase
{
    private function projectPath(string $relative): string
    {
        return dirname(__DIR__, 3) . '/' . $relative;
    }

    public function test_the_two_spaces_are_separate_route_groups_behind_rbac(): void
    {
        $routes = file_get_contents($this->projectPath('routes/web.php'));

        $this->assertStringContainsString("FederationController::class, 'index']\n    )->middleware(['auth'])->name('dtn.index');", $routes);
        $this->assertStringContainsString("Route::middleware(['auth', 'auth.unified', 'permission.unified:dtn-federation-space'])->group(", $routes);
        $this->assertStringContainsString("Route::middleware(['auth', 'auth.unified', 'permission.unified:club-selections-space'])->group(", $routes);
        foreach (['dtn.players.index', 'dtn.players.show', 'dtn.selections.store', 'dtn.selections.show', 'dtn.selections.return', 'dtn.api-access',
            'club.selections.index', 'club.selections.show', 'club.selections.departure', 'club.selections.acknowledge', 'club.selections.api-access'] as $name) {
            $this->assertStringContainsString("->name('{$name}')", $routes);
        }
        $this->assertFileDoesNotExist($this->projectPath('app/Http/Controllers/DtnController.php'));
    }

    public function test_every_action_goes_through_dtn_access_and_the_api_checks_token_and_rbac(): void
    {
        $workflow = file_get_contents($this->projectPath('app/Services/Dtn/SelectionWorkflow.php'));
        foreach (['canConvoke', 'canEditDeparture', 'canEditReturn', 'canAcknowledge', 'canCancel', 'canEditMedical'] as $check) {
            $this->assertStringContainsString("{$check}(", $workflow);
        }
        $this->assertStringContainsString('canViewAsFederation(', file_get_contents($this->projectPath('app/Http/Controllers/Dtn/FederationController.php')));
        $this->assertStringContainsString('canViewAsClub(', file_get_contents($this->projectPath('app/Http/Controllers/Dtn/ClubSelectionController.php')));

        foreach (['FederationApiController' => 'FEDERATION_PERMISSION', 'ClubSelectionApiController' => 'CLUB_PERMISSION'] as $class => $permission) {
            $api = file_get_contents($this->projectPath("app/Http/Controllers/Api/V1/Selections/{$class}.php"));
            $this->assertSame(substr_count($api, '): JsonResponse'), substr_count($api, '$this->requireAbility('), "{$class} : droit du jeton sur chaque action");
            $this->assertSame(substr_count($api, '$this->requireAbility('), substr_count($api, "DtnAccess::{$permission})"), "{$class} : permission RBAC sur chaque action");
        }
    }

    public function test_medical_data_is_restricted_to_medical_roles_and_never_serialized(): void
    {
        $access = file_get_contents($this->projectPath('app/Services/Dtn/DtnAccess.php'));
        $report = file_get_contents($this->projectPath('app/Models/NationalSelectionReport.php'));

        $this->assertStringContainsString("CLUB_MEDICAL = ['club_medical', 'team_doctor']", $access);
        $this->assertStringContainsString("DTN_MEDICAL = ['association_medical']", $access);
        $this->assertStringContainsString("protected \$hidden = ['medical'];", $report);
        $this->assertStringNotContainsString('isSystemAdmin', $this->methodBody($access, 'canSeeMedical'),
            "l'administrateur système n'a pas accès au médical");
    }

    public function test_snapshot_reads_no_medical_table(): void
    {
        $snapshot = file_get_contents($this->projectPath('app/Services/Dtn/SelectionSnapshot.php'));

        foreach (['health_records', 'pcmas', 'injuries', 'player_real_time_health', 'medical_records'] as $table) {
            $this->assertStringNotContainsString("'{$table}'", $snapshot);
        }
    }

    private function methodBody(string $source, string $method): string
    {
        $start = strpos($source, "function {$method}(");
        $this->assertNotFalse($start, $method);

        return substr($source, $start, strpos($source, "\n    }\n", $start) - $start);
    }
}
