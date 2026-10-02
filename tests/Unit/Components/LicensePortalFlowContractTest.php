<?php

namespace Tests\Unit\Components;

use PHPUnit\Framework\TestCase;

class LicensePortalFlowContractTest extends TestCase
{
    private function projectPath(string $relative): string
    {
        return dirname(__DIR__, 3) . '/' . $relative;
    }

    public function test_canonical_license_workflow_persists_player_license(): void
    {
        // La demande est créée par le service du circuit (pièces, historique, notifications).
        $controller = file_get_contents($this->projectPath('app/Http/Controllers/PlayerLicenseWorkflowController.php'));
        $workflow = file_get_contents($this->projectPath('app/Services/Licensing/LicenseWorkflow.php'));

        $this->assertStringContainsString('$this->workflow->submitPlayer($player, Auth::user()', $controller);
        $this->assertStringContainsString('PlayerLicense::create([', $workflow);
        $this->assertStringContainsString("'player_id' => \$player->id", $workflow);
        $this->assertStringContainsString("'status' => 'pending'", $workflow);
        $this->assertStringContainsString("'requested_by' => \$by->id", $workflow);
    }

    public function test_player_portal_reads_same_player_licenses_table(): void
    {
        $service = file_get_contents(
            $this->projectPath('app/Services/PlayerPortalDataService.php')
        );

        $this->assertStringContainsString(
            "DB::table('player_licenses')",
            $service
        );
        $this->assertStringContainsString(
            "->where('player_id', \$playerId)",
            $service
        );
        $this->assertStringContainsString(
            "'playerLicenses'",
            $service
        );
    }

    public function test_license_workflow_enforces_player_scope(): void
    {
        $controller = file_get_contents($this->projectPath('app/Http/Controllers/PlayerLicenseWorkflowController.php'));
        $workflow = file_get_contents($this->projectPath('app/Services/Licensing/LicenseWorkflow.php'));

        // Le contrôleur n'accepte qu'un joueur du périmètre du compte…
        $this->assertStringContainsString('requestablePlayers($user)->whereKey($player->getKey())->exists()', $controller);
        // … club : ses joueurs ; fédération : les joueurs des clubs de sa fédération.
        $this->assertStringContainsString("return \$query->where('club_id', \$user->club_id);", $workflow);
        $this->assertStringContainsString("DB::table('clubs')->where('association_id', \$user->association_id)", $workflow);
    }

    public function test_license_aggregator_does_not_fabricate_default_license(): void
    {
        $service = file_get_contents(
            $this->projectPath('app/Services/LicenseHistoryAggregator.php')
        );

        $this->assertStringNotContainsString(
            "'date_debut' => '2020-01-01'",
            $service
        );
        $this->assertStringNotContainsString(
            "'type_licence' => 'Pro',",
            substr(
                $service,
                strpos($service, 'private function fetchFIFALicenseData'),
                5000
            )
        );
        $this->assertStringContainsString(
            '$player[\'license\'] ?? null',
            $service
        );
    }
}
