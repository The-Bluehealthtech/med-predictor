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

    public function test_dtn_routes_use_the_selection_controller_behind_auth(): void
    {
        $routes = file_get_contents($this->projectPath('routes/web.php'));

        $this->assertStringContainsString("DtnController::class, 'index']\n)->middleware(['auth'])->name('dtn.index');", $routes);
        $this->assertStringContainsString("Route::middleware(['auth'])->group(function () {\n    Route::get('/dtn/selections/create'", $routes);
        foreach (['dtn.selections.store', 'dtn.selections.show', 'dtn.selections.departure', 'dtn.selections.return', 'dtn.selections.acknowledge'] as $name) {
            $this->assertStringContainsString("->name('{$name}')", $routes);
        }
    }

    public function test_every_controller_action_goes_through_dtn_access(): void
    {
        $controller = file_get_contents($this->projectPath('app/Http/Controllers/DtnController.php'));

        foreach (['canUseTool', 'canConvoke', 'canView', 'canEditDeparture', 'canEditReturn', 'canAcknowledge', 'canCancel', 'canSeeMedical', 'canEditMedical'] as $check) {
            $this->assertStringContainsString("access->{$check}(", $controller);
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
