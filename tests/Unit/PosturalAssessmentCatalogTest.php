<?php

namespace Tests\Unit;

use Tests\TestCase;

class PosturalAssessmentCatalogTest extends TestCase
{
    public function test_every_finding_references_valid_views_sides_and_measurements(): void
    {
        $catalog = config('postural_assessment');

        foreach ($catalog['findings'] as $key => $finding) {
            $this->assertNotEmpty($finding['region'], $key);
            $this->assertNotEmpty($finding['views'], $key);
            $this->assertNotEmpty($finding['sides'], $key);

            foreach ($finding['views'] as $view) {
                $this->assertContains($view, $catalog['views'], "$key uses unknown view $view");
            }

            foreach ($finding['sides'] as $side) {
                $this->assertContains($side, $catalog['sides'], "$key uses unknown side $side");
            }

            if ($finding['measurement'] !== null) {
                $this->assertArrayHasKey($finding['measurement'], $catalog['measurements'], "$key references an unknown measurement");
            }
        }
    }

    public function test_measurement_protocols_are_structurally_valid(): void
    {
        foreach (config('postural_assessment.measurements') as $key => $measurement) {
            $this->assertGreaterThan(0, $measurement['points'], $key);
            $this->assertCount($measurement['points'], $measurement['landmarks'], $key);
            $this->assertContains($measurement['unit'], ['deg', 'normalized'], $key);
        }
    }
}
