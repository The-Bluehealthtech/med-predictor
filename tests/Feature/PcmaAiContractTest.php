<?php
namespace Tests\Feature;
use App\Http\Controllers\PCMAController;
use App\Services\MedicalAiResult;
use Illuminate\Http\{Request, UploadedFile};
use Illuminate\Support\Facades\{Http, Storage};
use Tests\TestCase;
final class PcmaAiContractTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp(); Storage::fake('local');
        config()->set('services.ai.base_url', 'https://ai.fixture.invalid');
        Http::preventStrayRequests();
    }
    public function test_text_fitness_contract_and_zero_values_are_preserved(): void
    {
        Http::fake(['https://ai.fixture.invalid/*'=>Http::response([
            'success'=>true,'analysis'=>['overall_score'=>0,'executive_summary'=>'Fixture only']],200)]);
        $response=app(PCMAController::class)->aiFitnessAssessment(
            Request::create('/','POST',['heart_rate'=>60]));
        self::assertSame(200,$response->getStatusCode());
        self::assertSame(0,$response->getData(true)['assessment']['overall_score']);
        Http::assertSent(fn ($r)=>$r['analysis_type']==='fitness_assessment'
            && !isset($r['file_content']));
    }
    public function test_fitness_without_clinical_input_never_calls_provider(): void
    {
        Http::fake();
        self::assertSame(422,app(PCMAController::class)->aiFitnessAssessment(
            Request::create('/','POST'))->getStatusCode());
        Http::assertNothingSent();
    }
    public function test_mock_and_unstructured_results_are_unavailable(): void
    {
        foreach ([
            ['success'=>true,'analysis'=>['success'=>true,'mockMode'=>true,'text'=>'{}']],
            ['success'=>true,'analysis'=>['success'=>true,'text'=>'plain unstructured response']],
            ['success'=>false,'analysis'=>['overall_score'=>100]],
        ] as $result) {
            Http::fake(['https://ai.fixture.invalid/*'=>Http::response($result,200)]);
            self::assertSame(503,app(PCMAController::class)->aiFitnessAssessment(
                Request::create('/','POST',['heart_rate'=>60]))->getStatusCode());
        }
    }
    public function test_ecg_failure_cleans_temporary_file_and_returns_503(): void
    {
        Http::fake(['https://ai.fixture.invalid/*'=>Http::response(['success'=>false],503)]);
        $request=Request::create('/','POST',[],[],[
            'ecg_file'=>UploadedFile::fake()->create('fixture.pdf',1,'application/pdf')]);
        self::assertSame(503,app(PCMAController::class)->aiAnalyzeEcg($request)->getStatusCode());
        self::assertSame([],Storage::disk('local')->allFiles('temp_analysis'));
    }
    public function test_legacy_json_wrapper_is_parsed_without_clinical_clearance(): void
    {
        $result=app(MedicalAiResult::class)->normalize(['success'=>true,
            'analysis'=>['success'=>true,'text'=>'{"abnormalities":"None","heart_rate":60}']]);
        self::assertSame(60,$result['analysis']['heart_rate']);
        $method=new \ReflectionMethod(app(PCMAController::class),'generateOverallAssessment');
        self::assertSame('Pending medical clearance',
            $method->invoke(app(PCMAController::class),['ecg'=>$result])['sports_eligibility']);
    }
}
