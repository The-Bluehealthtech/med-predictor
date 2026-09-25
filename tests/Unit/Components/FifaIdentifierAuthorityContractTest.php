<?php

namespace Tests\Unit\Components;

use PHPUnit\Framework\TestCase;

class FifaIdentifierAuthorityContractTest extends TestCase
{
    private function projectPath(string $relative): string
    {
        return dirname(__DIR__, 3) . '/' . $relative;
    }

    public function test_player_workflows_do_not_fabricate_fifa_ids(): void
    {
        foreach ([
            'app/Http/Controllers/PlayerController.php',
            'app/Http/Controllers/PlayerRegistrationController.php',
            'app/Http/Controllers/ClubManagementController.php',
        ] as $file) {
            $source = file_get_contents($this->projectPath($file));

            $this->assertStringNotContainsString(
                'Player::generateFifaConnectId()',
                $source,
                $file
            );
            $this->assertStringNotContainsString(
                "'FIFA' . str_pad",
                $source,
                $file
            );
        }
    }

    public function test_competitions_do_not_fabricate_fifa_ids(): void
    {
        $model = file_get_contents(
            $this->projectPath('app/Models/Competition.php')
        );
        $controller = file_get_contents(
            $this->projectPath(
                'app/Http/Controllers/CompetitionManagementController.php'
            )
        );

        $this->assertStringNotContainsString("'COMP_'", $model);
        $this->assertStringNotContainsString(
            'generateCompetitionId()',
            $controller
        );
    }

    public function test_legacy_fifa_id_generators_fail_closed(): void
    {
        foreach ([
            'app/Models/Player.php',
            'app/Models/LicenseRequest.php',
            'app/Models/AccountRequest.php',
            'app/Services/FifaConnectService.php',
        ] as $file) {
            $source = file_get_contents($this->projectPath($file));

            $this->assertStringNotContainsString(
                "return 'FIFA_' . uniqid()",
                $source,
                $file
            );
            $this->assertStringNotContainsString(
                "return 'COMP_' . uniqid()",
                $source,
                $file
            );
            $this->assertStringNotContainsString(
                "return 'HEALTH_' . uniqid()",
                $source,
                $file
            );
        }
    }

    public function test_account_creation_does_not_assign_synthetic_fifa_id(): void
    {
        $source = file_get_contents(
            $this->projectPath('app/Models/AccountRequest.php')
        );

        $this->assertStringContainsString(
            '$fifaConnectId = null;',
            $source
        );
    }
}
