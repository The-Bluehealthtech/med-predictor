<?php

namespace Tests\Unit\Components;

use PHPUnit\Framework\TestCase;

class PcmaAiFallbackContractTest extends TestCase
{
    private function projectPath(string $relative): string
    {
        return dirname(__DIR__, 3) . '/' . $relative;
    }

    public function test_web_pcma_active_ai_paths_do_not_return_mock_success_fallbacks(): void
    {
        $controller = file_get_contents(
            $this->projectPath('app/Http/Controllers/PCMAController.php')
        );

        $active = substr(
            $controller,
            strpos($controller, 'public function aiAnalyzeCt'),
            strpos($controller, 'private function getAnalysisPrompt')
                - strpos($controller, 'public function aiAnalyzeCt')
        );

        $this->assertStringNotContainsString(
            'using mock data',
            strtolower($active)
        );
        $this->assertStringNotContainsString(
            'getMockFitnessAssessment()',
            $active
        );
        $this->assertStringNotContainsString(
            'getMockAnalysis(',
            $active
        );
        $this->assertStringContainsString('], 503);', $active);
    }

    public function test_v1_pcma_ai_client_throws_instead_of_fabricating_analysis(): void
    {
        $controller = file_get_contents(
            $this->projectPath(
                'app/Http/Controllers/Api/V1/PCMAController.php'
            )
        );

        $start = strpos(
            $controller,
            'private function callMedGeminiAI'
        );
        $end = strpos(
            $controller,
            'private function getAnalysisPrompt',
            $start
        );

        $client = substr($controller, $start, $end - $start);

        $this->assertStringNotContainsString(
            'getMockAnalysis',
            $client
        );
        $this->assertStringContainsString(
            'throw new \RuntimeException',
            $client
        );
        $this->assertStringContainsString('throw $e;', $client);
    }

    public function test_transcript_prefill_never_falls_back_to_simulated_medical_data(): void
    {
        $controller = file_get_contents(
            $this->projectPath(
                'app/Http/Controllers/Api/V1/PCMAController.php'
            )
        );

        $start = strpos(
            $controller,
            'public function prefillFromTranscript'
        );
        $end = strpos(
            $controller,
            'public function whisperTranscribe',
            $start
        );

        $method = substr($controller, $start, $end - $start);

        $this->assertStringNotContainsString('generateMockData(', $method);
        $this->assertStringContainsString(
            'Service d’extraction IA indisponible.',
            $method
        );
        $this->assertStringContainsString('], 503);', $method);
    }
}
