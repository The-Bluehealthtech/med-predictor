<?php

namespace App\Console\Commands;

use App\Services\FifaConnectService;
use Illuminate\Console\Command;

class TestFifaConnectivity extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fifa:test-connectivity';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test FIFA Connect service connectivity';

    /**
     * Execute the console command.
     */
    public function handle(FifaConnectService $fifaService)
    {
        $this->info('Testing FIFA Connect service connectivity...');
        
        $result = $fifaService->checkConnectivity();
        
        $this->line('');
        $this->line('Connection Status: ' . ($result['connected'] ? '✅ Connected' : '❌ Disconnected'));
        $this->line('Status: ' . $result['status']);
        $this->line('Timestamp: ' . $result['timestamp']);
        
        if (isset($result['response_time'])) {
            $this->line('Response Time: ' . number_format($result['response_time'] * 1000, 2) . 'ms');
        }
        
        if (($result['status'] ?? null) === 'mock') {
            $this->line('Mode: 🔧 Mock explicite — aucune connexion live testée');
        } elseif (($result['status'] ?? null) === 'unconfigured') {
            $this->line('Mode: ⚠️ Non configuré');
        } else {
            $this->line('Mode: 🌐 Live');
        }
        
        if (isset($result['error'])) {
            $this->error('Error: ' . $result['error']);
        }
        
        $this->line('');
        
        if ($result['connected']) {
            $this->info('✅ FIFA Connect service is accessible!');
        } else {
            $this->error('❌ FIFA Connect service is not accessible.');
            $this->line('');
            $this->line('Troubleshooting tips:');
            $this->line('1. Check your FIFA_CONNECT_API_KEY environment variable');
            $this->line('2. Verify the FIFA_CONNECT_BASE_URL is correct');
            $this->line('3. Ensure your network can reach the FIFA API');
            $this->line('4. Mock mode is only used when FIFA_CONNECT_MOCK_MODE is explicitly enabled');
        }
        
        return $result['connected'] ? 0 : 1;
    }
}
