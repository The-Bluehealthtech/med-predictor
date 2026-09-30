<?php
namespace Tests\Feature;
use App\Http\Controllers\PCMAController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;
final class PcmaAnalysisSafetyTest extends TestCase
{
    public function test_complete_without_files_returns_422_without_calling_ai(): void
    {
        Http::fake();
        $response = app(PCMAController::class)->aiAnalyzeComplete(Request::create('/', 'POST'));
        self::assertSame(422, $response->getStatusCode());
        self::assertFalse($response->getData(true)['success']);
        Http::assertNothingSent();
    }
    public function test_missing_ecg_is_validation_error_not_500(): void
    {
        Http::fake();
        $response = app(PCMAController::class)->aiAnalyzeEcg(Request::create('/', 'POST'));
        self::assertSame(422, $response->getStatusCode());
        self::assertArrayHasKey('ecg_file', $response->getData(true)['errors']);
        Http::assertNothingSent();
    }
    public function test_clinical_fields_preserve_null_zero_and_existing_json(): void
    {
        $method = new \ReflectionMethod(app(PCMAController::class), 'preserveClinicalFields');
        $result = $method->invoke(app(PCMAController::class), [
            'player_id' => 853, 'heart_rate' => 0, 'weight' => null,
            'medical_history' => 'Fixture history',
        ], ['vital_signs' => ['temperature' => 37], 'existing' => true]);
        self::assertSame(853, $result['player_id']);
        self::assertSame(0, $result['result_json']['vital_signs']['heart_rate']);
        self::assertNull($result['result_json']['vital_signs']['weight']);
        self::assertSame(37, $result['result_json']['vital_signs']['temperature']);
        self::assertTrue($result['result_json']['existing']);
        self::assertSame('Fixture history', $result['medical_history']['cardiovascular_history']);
        self::assertArrayNotHasKey('heart_rate', $result);
    }
    public function test_clinical_json_roundtrips_through_database_without_fifa_id(): void
    {
        config()->set('database.connections.pcma_roundtrip', [
            'driver' => 'sqlite', 'database' => ':memory:', 'prefix' => '']);
        \Illuminate\Support\Facades\Schema::connection('pcma_roundtrip')
            ->create('pcmas', function ($table) {
                $table->id(); $table->unsignedBigInteger('player_id');
                $table->text('result_json'); $table->timestamps();
            });
        try {
            $method = new \ReflectionMethod(app(PCMAController::class), 'preserveClinicalFields');
            $data = $method->invoke(app(PCMAController::class), [
                'player_id' => 853, 'heart_rate' => 60, 'weight' => null]);
            $pcma = new \App\Models\PCMA($data);
            $pcma->setConnection('pcma_roundtrip')->save();
            $saved = $pcma->fresh();
            self::assertSame(853, $saved->player_id);
            self::assertSame(60, $saved->result_json['vital_signs']['heart_rate']);
            self::assertNull($saved->result_json['vital_signs']['weight']);
        } finally {
            \Illuminate\Support\Facades\DB::purge('pcma_roundtrip');
        }
    }
    public function test_empty_failed_and_mock_responses_never_clear_sports(): void
    {
        $controller = app(PCMAController::class);
        $method = new \ReflectionMethod($controller, 'generateOverallAssessment');
        foreach ([[], ['ecg' => ['success' => false]],
            ['ecg' => ['success' => true, 'mockMode' => true]],
            ['ecg' => ['success' => true, 'analysis' => ['text' => 'unstructured']]]] as $analyses) {
            $result = $method->invoke($controller, $analyses);
            self::assertSame('Données insuffisantes', $result['medical_status']);
            self::assertSame('Pending medical clearance', $result['sports_eligibility']);
        }
    }
}
