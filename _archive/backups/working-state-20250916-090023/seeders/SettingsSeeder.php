<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Setting;

class SettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            // Application Settings
            [
                'key' => 'app_name',
                'value' => config('app.name'),
                'type' => 'string',
                'group' => 'general',
                'description' => 'Application name'
            ],
            [
                'key' => 'app_url',
                'value' => config('app.url'),
                'type' => 'string',
                'group' => 'general',
                'description' => 'Application URL'
            ],
            [
                'key' => 'timezone',
                'value' => config('app.timezone'),
                'type' => 'string',
                'group' => 'general',
                'description' => 'Application timezone'
            ],
            [
                'key' => 'locale',
                'value' => config('app.locale'),
                'type' => 'string',
                'group' => 'general',
                'description' => 'Application locale'
            ],
            [
                'key' => 'debug_mode',
                'value' => config('app.debug') ? '1' : '0',
                'type' => 'boolean',
                'group' => 'general',
                'description' => 'Enable debug mode'
            ],
            
            // Email Settings
            [
                'key' => 'mail_host',
                'value' => config('mail.mailers.smtp.host'),
                'type' => 'string',
                'group' => 'email',
                'description' => 'SMTP host'
            ],
            [
                'key' => 'mail_port',
                'value' => config('mail.mailers.smtp.port'),
                'type' => 'integer',
                'group' => 'email',
                'description' => 'SMTP port'
            ],
            [
                'key' => 'mail_username',
                'value' => config('mail.mailers.smtp.username'),
                'type' => 'string',
                'group' => 'email',
                'description' => 'SMTP username'
            ],
            [
                'key' => 'mail_password',
                'value' => config('mail.mailers.smtp.password'),
                'type' => 'string',
                'group' => 'email',
                'description' => 'SMTP password'
            ],
            
            // FIFA Connect Settings
            [
                'key' => 'fifa_api_url',
                'value' => config('services.fifa.api_url', 'https://api.fifa.com'),
                'type' => 'string',
                'group' => 'fifa',
                'description' => 'FIFA API URL'
            ],
            [
                'key' => 'fifa_api_key',
                'value' => config('services.fifa.api_key', ''),
                'type' => 'string',
                'group' => 'fifa',
                'description' => 'FIFA API Key'
            ],
            [
                'key' => 'fifa_sync_enabled',
                'value' => '1',
                'type' => 'boolean',
                'group' => 'fifa',
                'description' => 'Enable FIFA sync'
            ],
            
            // Security Settings
            [
                'key' => 'session_lifetime',
                'value' => config('session.lifetime'),
                'type' => 'integer',
                'group' => 'security',
                'description' => 'Session lifetime in minutes'
            ],
            [
                'key' => 'password_timeout',
                'value' => config('auth.password_timeout', 10800),
                'type' => 'integer',
                'group' => 'security',
                'description' => 'Password timeout in minutes'
            ],
            [
                'key' => 'force_https',
                'value' => config('app.env') === 'production' ? '1' : '0',
                'type' => 'boolean',
                'group' => 'security',
                'description' => 'Force HTTPS'
            ],
            [
                'key' => 'enable_csrf',
                'value' => '1',
                'type' => 'boolean',
                'group' => 'security',
                'description' => 'Enable CSRF protection'
            ],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }
}
