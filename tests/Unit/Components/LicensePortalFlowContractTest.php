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
        $controller = file_get_contents(
            $this->projectPath(
                'app/Http/Controllers/PlayerLicenseWorkflowController.php'
            )
        );

        $this->assertStringContainsString(
            'PlayerLicense::create([',
            $controller
        );
        $this->assertStringContainsString(
            "'player_id' => \$player->id",
            $controller
        );
        $this->assertStringContainsString(
            "'status' => 'pending'",
            $controller
        );
        $this->assertStringContainsString(
            "'requested_by' => Auth::id()",
            $controller
        );
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
        $controller = file_get_contents(
            $this->projectPath(
                'app/Http/Controllers/PlayerLicenseWorkflowController.php'
            )
        );

        $this->assertStringContainsString(
            '(int) $player->club_id === (int) $user->club_id',
            $controller
        );
        $this->assertStringContainsString(
            '(int) $player->association_id === (int) $user->association_id',
            $controller
        );
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
