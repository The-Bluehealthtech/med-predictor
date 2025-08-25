<?php

/**
 * Test script for ICD-11 and FHIR integrations
 * 
 * Usage: php scripts/test-integrations.php
 */

require_once __DIR__ . '/../vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class IntegrationTester
{
    private $baseUrl = 'http://localhost:8000';
    private $apiToken = null;

    public function __construct()
    {
        // Get API token (you may need to adjust this based on your auth setup)
        $this->getApiToken();
    }

    private function getApiToken()
    {
        // This is a simplified version - you may need to adjust based on your auth system
        try {
            $response = Http::post($this->baseUrl . '/api/v1/auth/login', [
                'email' => env('TEST_USER_EMAIL', 'admin@example.com'),
                'password' => env('TEST_USER_PASSWORD', 'password')
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $this->apiToken = $data['token'] ?? null;
            }
        } catch (\Exception $e) {
            echo "⚠️  Warning: Could not get API token: " . $e->getMessage() . "\n";
        }
    }

    public function testICD11Integration()
    {
        echo "\n🔍 Testing ICD-11 Integration...\n";
        echo str_repeat('-', 50) . "\n";

        // Test 1: Health check
        echo "1. Testing ICD-11 health endpoint...\n";
        $response = Http::get($this->baseUrl . '/api/v1/icd11/health');
        $this->printResponse($response, 'ICD-11 Health Check');

        // Test 2: Search functionality
        echo "\n2. Testing ICD-11 search...\n";
        $response = Http::get($this->baseUrl . '/api/v1/icd11/search', [
            'query' => 'diabetes',
            'language' => 'en',
            'limit' => 5
        ]);
        $this->printResponse($response, 'ICD-11 Search');

        // Test 3: Get specific code
        echo "\n3. Testing ICD-11 code details...\n";
        $response = Http::get($this->baseUrl . '/api/v1/icd11/code/S93.4');
        $this->printResponse($response, 'ICD-11 Code Details');

        // Test 4: Get chapters
        echo "\n4. Testing ICD-11 chapters...\n";
        $response = Http::get($this->baseUrl . '/api/v1/icd11/chapters', [
            'language' => 'en'
        ]);
        $this->printResponse($response, 'ICD-11 Chapters');
    }

    public function testFHIRIntegration()
    {
        echo "\n🏥 Testing FHIR Integration...\n";
        echo str_repeat('-', 50) . "\n";

        // Test 1: Connectivity
        echo "1. Testing FHIR connectivity...\n";
        $response = Http::get($this->baseUrl . '/api/v1/fhir/connectivity');
        $this->printResponse($response, 'FHIR Connectivity');

        // Test 2: Capabilities
        echo "\n2. Testing FHIR capabilities...\n";
        $response = Http::get($this->baseUrl . '/api/v1/fhir/capabilities');
        $this->printResponse($response, 'FHIR Capabilities');

        // Test 3: Sync statistics
        echo "\n3. Testing FHIR sync statistics...\n";
        $response = Http::get($this->baseUrl . '/api/v1/fhir/sync-statistics');
        $this->printResponse($response, 'FHIR Sync Statistics');

        // Test 4: Test specific resource
        echo "\n4. Testing FHIR resource access...\n";
        $response = Http::post($this->baseUrl . '/api/v1/fhir/test-resource', [
            'resource_type' => 'Patient'
        ]);
        $this->printResponse($response, 'FHIR Resource Test');
    }

    public function testEnvironmentConfiguration()
    {
        echo "\n⚙️  Testing Environment Configuration...\n";
        echo str_repeat('-', 50) . "\n";

        $requiredVars = [
            'ICD11_CLIENT_ID' => 'ICD-11 Client ID',
            'ICD11_CLIENT_SECRET' => 'ICD-11 Client Secret',
            'ICD11_BASE_URL' => 'ICD-11 Base URL',
            'FHIR_BASE_URL' => 'FHIR Base URL',
            'FHIR_TIMEOUT' => 'FHIR Timeout',
            'FHIR_RETRY_ATTEMPTS' => 'FHIR Retry Attempts'
        ];

        foreach ($requiredVars as $var => $description) {
            $value = env($var);
            if ($value) {
                echo "✅ {$description}: " . ($var === 'ICD11_CLIENT_SECRET' ? '***' : $value) . "\n";
            } else {
                echo "❌ {$description}: Not configured\n";
            }
        }
    }

    public function testServiceConfiguration()
    {
        echo "\n🔧 Testing Service Configuration...\n";
        echo str_repeat('-', 50) . "\n";

        // Test ICD-11 service configuration
        try {
            $icd11Config = [
                'client_id' => env('ICD11_CLIENT_ID'),
                'client_secret' => env('ICD11_CLIENT_SECRET'),
                'base_url' => env('ICD11_BASE_URL', 'https://icd.who.int/icdapi'),
                'timeout' => env('ICD11_TIMEOUT', 30)
            ];

            echo "ICD-11 Configuration:\n";
            foreach ($icd11Config as $key => $value) {
                $displayValue = $key === 'client_secret' ? '***' : $value;
                echo "  {$key}: {$displayValue}\n";
            }

            // Test FHIR service configuration
            $fhirConfig = [
                'base_url' => env('FHIR_BASE_URL'),
                'timeout' => env('FHIR_TIMEOUT', 30),
                'retry_attempts' => env('FHIR_RETRY_ATTEMPTS', 3),
                'version' => env('FHIR_VERSION', 'R4')
            ];

            echo "\nFHIR Configuration:\n";
            foreach ($fhirConfig as $key => $value) {
                echo "  {$key}: {$value}\n";
            }

        } catch (\Exception $e) {
            echo "❌ Error testing service configuration: " . $e->getMessage() . "\n";
        }
    }

    private function printResponse($response, $testName)
    {
        $status = $response->status();
        $success = $status >= 200 && $status < 300;
        
        echo "   Status: " . ($success ? "✅ {$status}" : "❌ {$status}") . "\n";
        
        if ($response->successful()) {
            $data = $response->json();
            if (isset($data['success'])) {
                echo "   Success: " . ($data['success'] ? 'Yes' : 'No') . "\n";
            }
            if (isset($data['message'])) {
                echo "   Message: {$data['message']}\n";
            }
            if (isset($data['data']) && is_array($data['data'])) {
                echo "   Data count: " . count($data['data']) . "\n";
            }
        } else {
            echo "   Error: " . $response->body() . "\n";
        }
    }

    public function runAllTests()
    {
        echo "🚀 Starting Integration Tests...\n";
        echo "Base URL: {$this->baseUrl}\n";
        echo "Timestamp: " . date('Y-m-d H:i:s') . "\n";

        $this->testEnvironmentConfiguration();
        $this->testServiceConfiguration();
        $this->testICD11Integration();
        $this->testFHIRIntegration();

        echo "\n🎉 Integration tests completed!\n";
        echo "Check the output above for any issues.\n";
    }
}

// Run the tests
if (php_sapi_name() === 'cli') {
    $tester = new IntegrationTester();
    $tester->runAllTests();
} else {
    echo "This script should be run from the command line.\n";
} 