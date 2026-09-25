<?php

namespace Tests\Unit\Components;

use PHPUnit\Framework\TestCase;

class FifaPortalContractTest extends TestCase
{
    private function projectPath(string $relative): string
    {
        return dirname(__DIR__, 3) . '/' . $relative;
    }

    public function test_active_fifa_portal_uses_clean_canonical_view(): void
    {
        $controller = file_get_contents(
            $this->projectPath('app/Http/Controllers/FIFATestController.php')
        );

        $this->assertStringContainsString(
            "return view('fifa.portal'",
            $controller
        );
        $this->assertStringNotContainsString(
            "view('fifa-portal-integrated'",
            $controller
        );
    }

    public function test_active_fifa_portal_contains_no_simulated_player_data(): void
    {
        $view = file_get_contents(
            $this->projectPath('resources/views/fifa/portal.blade.php')
        );

        foreach ([
            'Math.random',
            'Lionel',
            'Cristiano',
            'Mbapp',
            'Haaland',
            'De Bruyne',
            'mockResults',
        ] as $forbidden) {
            $this->assertStringNotContainsString($forbidden, $view);
        }
    }

    public function test_active_portal_exposes_unavailable_values_as_na(): void
    {
        $view = file_get_contents(
            $this->projectPath('resources/views/fifa/portal.blade.php')
        );

        $this->assertStringContainsString(
            "\$selectedPlayer->overall_rating ?? 'N/A'",
            $view
        );
        $this->assertStringContainsString(
            "\$selectedPlayer->potential_rating ?? 'N/A'",
            $view
        );
        $this->assertStringContainsString(
            'Aucun fallback numérique',
            $view
        );
    }
}
