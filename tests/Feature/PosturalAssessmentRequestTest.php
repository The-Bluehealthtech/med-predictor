<?php

namespace Tests\Feature;

use App\Http\Requests\StorePosturalAssessmentRequest;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class PosturalAssessmentRequestTest extends TestCase
{
    public function test_valid_structured_finding_is_accepted(): void
    {
        $validator = $this->validatorFor([
            'assessment_type' => 'routine',
            'assessment_date' => '2026-10-01 10:00:00',
            'findings' => [[
                'view' => 'anterior',
                'region' => 'knee',
                'finding_key' => 'knee_valgus',
                'side' => 'right',
                'severity' => 'moderate',
                'source' => 'clinician',
            ]],
            'measurements' => [],
        ]);

        $this->assertFalse($validator->fails(), json_encode($validator->errors()->toArray()));
    }

    public function test_incompatible_view_is_rejected(): void
    {
        $validator = $this->validatorFor([
            'assessment_type' => 'routine',
            'assessment_date' => '2026-10-01 10:00:00',
            'findings' => [[
                'view' => 'right_lateral',
                'region' => 'knee',
                'finding_key' => 'knee_valgus',
                'side' => 'right',
            ]],
        ]);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('findings.0.view', $validator->errors()->toArray());
    }

    public function test_measurement_requires_expected_number_of_normalized_points(): void
    {
        $validator = $this->validatorFor([
            'assessment_type' => 'routine',
            'assessment_date' => '2026-10-01 10:00:00',
            'measurements' => [[
                'view' => 'anterior',
                'measurement_type' => 'angle',
                'measurement_key' => 'knee_frontal_angle',
                'unit' => 'deg',
                'points' => [
                    ['x' => 0.2, 'y' => 0.2],
                    ['x' => 0.4, 'y' => 0.4],
                ],
            ]],
        ]);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('measurements.0.points', $validator->errors()->toArray());
    }

    private function validatorFor(array $payload)
    {
        $request = StorePosturalAssessmentRequest::create('/', 'POST', $payload);
        $request->setContainer(app());
        $request->setRedirector(app('redirect'));

        $validator = Validator::make($payload, $request->rules());
        $request->withValidator($validator);

        return $validator;
    }
}
