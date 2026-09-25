<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AuditTrail;
use App\Models\User;
use Carbon\Carbon;

class AuditTrailSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing audit trails first
        AuditTrail::truncate();
        
        $users = User::take(5)->get();
        
        if ($users->isEmpty()) {
            $this->command->warn('Skipping AuditTrailSeeder: No users found.');
            return;
        }

        $actions = ['create', 'update', 'delete', 'login', 'logout', 'view', 'export', 'import', 'download', 'upload', 'approve', 'reject'];
        $eventTypes = ['user_action', 'system_event', 'security_event', 'data_access'];
        $severities = ['info', 'warning', 'error', 'critical'];
        $modelTypes = ['User', 'Player', 'HealthRecord', 'Competition', 'Club', 'PlayerLicense'];
        
        // Create 150 audit trail entries
        for ($i = 1; $i <= 150; $i++) {
            $user = $users->random();
            $action = $actions[array_rand($actions)];
            $eventType = $eventTypes[array_rand($eventTypes)];
            $severity = $severities[array_rand($severities)];
            $modelType = $modelTypes[array_rand($modelTypes)];
            
            AuditTrail::create([
                'user_id' => $user->id,
                'action' => $action,
                'model_type' => $modelType,
                'model_id' => rand(1, 100),
                'table_name' => strtolower($modelType) . 's',
                'event_type' => $eventType,
                'severity' => $severity,
                'description' => $this->getDescription($action, $modelType),
                'old_values' => $this->getOldValues($action),
                'new_values' => $this->getNewValues($action),
                'metadata' => [
                    'browser' => 'Chrome/120.0.0.0',
                    'os' => 'Windows 10',
                    'device' => 'desktop',
                    'location' => 'Office',
                ],
                'ip_address' => $this->getRandomIp(),
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                'session_id' => 'session_' . uniqid(),
                'request_method' => $this->getRequestMethod($action),
                'request_url' => $this->getRequestUrl($action, $modelType),
                'request_id' => uniqid(),
                'occurred_at' => Carbon::now()->subDays(rand(0, 30))->subHours(rand(0, 23))->subMinutes(rand(0, 59)),
                'created_at' => Carbon::now()->subDays(rand(0, 30)),
                'updated_at' => Carbon::now()->subDays(rand(0, 30)),
            ]);
        }

        $this->command->info('Audit trails seeded successfully!');
        $this->command->info('Created 150 audit trail entries');
    }
    
    private function getDescription($action, $modelType): string
    {
        $descriptions = [
            'create' => "Created new {$modelType} record",
            'update' => "Updated {$modelType} information",
            'delete' => "Deleted {$modelType} record",
            'login' => "User logged in successfully",
            'logout' => "User logged out",
            'view' => "Viewed {$modelType} details",
            'export' => "Exported {$modelType} data",
            'import' => "Imported {$modelType} data",
            'download' => "Downloaded {$modelType} file",
            'upload' => "Uploaded {$modelType} file",
            'approve' => "Approved {$modelType} request",
            'reject' => "Rejected {$modelType} request",
        ];
        
        return $descriptions[$action] ?? "Performed {$action} on {$modelType}";
    }
    
    private function getOldValues($action): ?array
    {
        if (in_array($action, ['update', 'delete'])) {
            return [
                'status' => 'pending',
                'updated_at' => Carbon::now()->subDays(1)->toISOString(),
            ];
        }
        return null;
    }
    
    private function getNewValues($action): ?array
    {
        if (in_array($action, ['create', 'update'])) {
            return [
                'status' => 'active',
                'updated_at' => Carbon::now()->toISOString(),
            ];
        }
        return null;
    }
    
    private function getRandomIp(): string
    {
        $ips = [
            '192.168.1.100',
            '10.0.0.50',
            '172.16.0.25',
            '203.0.113.10',
            '198.51.100.5',
        ];
        return $ips[array_rand($ips)];
    }
    
    private function getRequestMethod($action): string
    {
        $methods = [
            'create' => 'POST',
            'update' => 'PUT',
            'delete' => 'DELETE',
            'login' => 'POST',
            'logout' => 'POST',
            'view' => 'GET',
            'export' => 'GET',
            'import' => 'POST',
            'download' => 'GET',
            'upload' => 'POST',
            'approve' => 'POST',
            'reject' => 'POST',
        ];
        
        return $methods[$action] ?? 'GET';
    }
    
    private function getRequestUrl($action, $modelType): string
    {
        $baseUrl = 'http://localhost:8000';
        $modelPath = strtolower($modelType) . 's';
        
        $urls = [
            'create' => "{$baseUrl}/{$modelPath}/create",
            'update' => "{$baseUrl}/{$modelPath}/" . rand(1, 100) . "/edit",
            'delete' => "{$baseUrl}/{$modelPath}/" . rand(1, 100),
            'login' => "{$baseUrl}/login",
            'logout' => "{$baseUrl}/logout",
            'view' => "{$baseUrl}/{$modelPath}/" . rand(1, 100),
            'export' => "{$baseUrl}/{$modelPath}/export",
            'import' => "{$baseUrl}/{$modelPath}/import",
            'download' => "{$baseUrl}/{$modelPath}/" . rand(1, 100) . "/download",
            'upload' => "{$baseUrl}/{$modelPath}/upload",
            'approve' => "{$baseUrl}/{$modelPath}/" . rand(1, 100) . "/approve",
            'reject' => "{$baseUrl}/{$modelPath}/" . rand(1, 100) . "/reject",
        ];
        
        return $urls[$action] ?? "{$baseUrl}/{$modelPath}";
    }
} 