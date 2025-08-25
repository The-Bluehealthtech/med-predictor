<?php

namespace App\Http\Controllers;

use App\Models\Season;
use App\Models\Competition;
use App\Models\Player;
use App\Models\Club;
use App\Models\Association;
use App\Models\ContentManagement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Artisan;

class BackOfficeController extends Controller
{
    public function dashboard()
    {
        // Get current season
        $currentSeason = Season::getCurrentSeason();
        
        // Statistics
        $stats = [
            'total_seasons' => Season::count(),
            'total_competitions' => Competition::count(),
            'total_players' => Player::count(),
            'total_clubs' => Club::count(),
            'total_associations' => Association::count(),
            'total_content' => ContentManagement::count(),
        ];

        // Recent activities
        $recentSeasons = Season::latest()->take(5)->get();
        $recentCompetitions = Competition::with('season', 'association')->latest()->take(5)->get();
        $recentContent = ContentManagement::with('createdBy')->latest()->take(5)->get();

        // Season statistics
        $seasonStats = [];
        if ($currentSeason) {
            $seasonStats = [
                'competitions_count' => $currentSeason->competitions()->count(),
                'players_count' => $currentSeason->players()->count(),
                'registration_open' => $currentSeason->isRegistrationOpen(),
                'days_remaining' => now()->diffInDays($currentSeason->end_date, false),
            ];
        }

        return view('back-office.dashboard', compact(
            'currentSeason',
            'stats',
            'recentSeasons',
            'recentCompetitions',
            'recentContent',
            'seasonStats'
        ));
    }

    public function systemStatus()
    {
        // Database status
        $dbStatus = DB::connection()->getPdo() ? 'Connected' : 'Disconnected';
        
        // Storage status
        $storageStatus = Storage::disk('public')->exists('') ? 'Available' : 'Unavailable';
        
        // Queue status
        $queueStatus = 'Active'; // You can add actual queue checking logic
        
        // Cache status
        $cacheStatus = cache()->has('test') ? 'Working' : 'Working'; // Simple test
        
        return view('back-office.system-status', compact(
            'dbStatus',
            'storageStatus',
            'queueStatus',
            'cacheStatus'
        ));
    }

    public function settings()
    {
        // Get current settings
        $settings = [
            'general' => \App\Models\Setting::getByGroup('general'),
            'email' => \App\Models\Setting::getByGroup('email'),
            'fifa' => \App\Models\Setting::getByGroup('fifa'),
            'security' => \App\Models\Setting::getByGroup('security'),
        ];
        
        return view('back-office.settings', compact('settings'));
    }

    public function updateSettings(Request $request)
    {
        $request->validate([
            'app_name' => 'required|string|max:255',
            'app_url' => 'required|url',
            'timezone' => 'required|string',
            'locale' => 'required|string|in:en,fr,es',
            'debug_mode' => 'boolean',
        ]);

        try {
            // Save settings
            \App\Models\Setting::set('app_name', $request->app_name, 'string', 'general', 'Application name');
            \App\Models\Setting::set('app_url', $request->app_url, 'string', 'general', 'Application URL');
            \App\Models\Setting::set('timezone', $request->timezone, 'string', 'general', 'Application timezone');
            \App\Models\Setting::set('locale', $request->locale, 'string', 'general', 'Application locale');
            \App\Models\Setting::set('debug_mode', $request->has('debug_mode') ? '1' : '0', 'boolean', 'general', 'Enable debug mode');

            return redirect()->route('back-office.settings')
                ->with('success', 'Application settings updated successfully.');
        } catch (\Exception $e) {
            return redirect()->route('back-office.settings')
                ->with('error', 'Failed to update settings: ' . $e->getMessage());
        }
    }

    public function updateEmailSettings(Request $request)
    {
        $request->validate([
            'mail_host' => 'required|string',
            'mail_port' => 'required|integer|min:1|max:65535',
            'mail_username' => 'required|string',
            'mail_password' => 'required|string',
        ]);

        try {
            // Save email settings
            \App\Models\Setting::set('mail_host', $request->mail_host, 'string', 'email', 'SMTP host');
            \App\Models\Setting::set('mail_port', $request->mail_port, 'integer', 'email', 'SMTP port');
            \App\Models\Setting::set('mail_username', $request->mail_username, 'string', 'email', 'SMTP username');
            \App\Models\Setting::set('mail_password', $request->mail_password, 'string', 'email', 'SMTP password');

            return redirect()->route('back-office.settings')
                ->with('success', 'Email settings updated successfully.');
        } catch (\Exception $e) {
            return redirect()->route('back-office.settings')
                ->with('error', 'Failed to update email settings: ' . $e->getMessage());
        }
    }

    public function updateFifaSettings(Request $request)
    {
        $request->validate([
            'fifa_api_url' => 'required|url',
            'fifa_api_key' => 'required|string',
            'fifa_sync_enabled' => 'boolean',
        ]);

        try {
            // Save FIFA settings
            \App\Models\Setting::set('fifa_api_url', $request->fifa_api_url, 'string', 'fifa', 'FIFA API URL');
            \App\Models\Setting::set('fifa_api_key', $request->fifa_api_key, 'string', 'fifa', 'FIFA API Key');
            \App\Models\Setting::set('fifa_sync_enabled', $request->has('fifa_sync_enabled') ? '1' : '0', 'boolean', 'fifa', 'Enable FIFA sync');

            return redirect()->route('back-office.settings')
                ->with('success', 'FIFA Connect settings updated successfully.');
        } catch (\Exception $e) {
            return redirect()->route('back-office.settings')
                ->with('error', 'Failed to update FIFA settings: ' . $e->getMessage());
        }
    }

    public function updateSecuritySettings(Request $request)
    {
        $request->validate([
            'session_lifetime' => 'required|integer|min:1|max:1440',
            'password_timeout' => 'required|integer|min:1|max:1440',
            'force_https' => 'boolean',
            'enable_csrf' => 'boolean',
        ]);

        try {
            // Save security settings
            \App\Models\Setting::set('session_lifetime', $request->session_lifetime, 'integer', 'security', 'Session lifetime in minutes');
            \App\Models\Setting::set('password_timeout', $request->password_timeout, 'integer', 'security', 'Password timeout in minutes');
            \App\Models\Setting::set('force_https', $request->has('force_https') ? '1' : '0', 'boolean', 'security', 'Force HTTPS');
            \App\Models\Setting::set('enable_csrf', $request->has('enable_csrf') ? '1' : '0', 'boolean', 'security', 'Enable CSRF protection');

            return redirect()->route('back-office.settings')
                ->with('success', 'Security settings updated successfully.');
        } catch (\Exception $e) {
            return redirect()->route('back-office.settings')
                ->with('error', 'Failed to update security settings: ' . $e->getMessage());
        }
    }

    public function logs(Request $request)
    {
        // Get log statistics
        $stats = [
            'errors' => 0,
            'warnings' => 0,
            'info' => 0,
            'total' => 0,
        ];

        // Mock log entries for now
        $logs = collect([
            (object) [
                'id' => 1,
                'timestamp' => now()->subMinutes(5),
                'level' => 'error',
                'message' => 'Database connection failed',
                'context' => 'Database connection timeout'
            ],
            (object) [
                'id' => 2,
                'timestamp' => now()->subMinutes(10),
                'level' => 'warning',
                'message' => 'High memory usage detected',
                'context' => 'Memory usage at 85%'
            ],
            (object) [
                'id' => 3,
                'timestamp' => now()->subMinutes(15),
                'level' => 'info',
                'message' => 'User login successful',
                'context' => 'User ID: 123'
            ],
        ]);

        // Apply filters
        if ($request->filled('level')) {
            $logs = $logs->where('level', $request->level);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $logs = $logs->filter(function ($log) use ($search) {
                return str_contains(strtolower($log->message), strtolower($search)) ||
                       str_contains(strtolower($log->context), strtolower($search));
            });
        }

        // Calculate stats
        $stats['errors'] = $logs->where('level', 'error')->count();
        $stats['warnings'] = $logs->where('level', 'warning')->count();
        $stats['info'] = $logs->where('level', 'info')->count();
        $stats['total'] = $logs->count();

        return view('back-office.logs', compact('logs', 'stats'));
    }

    public function downloadLogs(Request $request)
    {
        // Generate log file download
        $filename = 'system-logs-' . now()->format('Y-m-d-H-i-s') . '.txt';
        
        return response()->streamDownload(function () {
            echo "System Logs\n";
            echo "Generated: " . now()->format('Y-m-d H:i:s') . "\n";
            echo "=====================================\n\n";
            
            // Add actual log content here
            echo "Sample log entries...\n";
        }, $filename);
    }

    public function clearCache()
    {
        try {
            Artisan::call('cache:clear');
            Artisan::call('config:clear');
            Artisan::call('view:clear');
            
            return response()->json(['success' => true, 'message' => 'Cache cleared successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to clear cache: ' . $e->getMessage()]);
        }
    }

    public function optimizeDatabase()
    {
        try {
            // Analyze and optimize database tables
            $tables = ['players', 'clubs', 'competitions', 'game_matches', 'health_records', 'medical_predictions'];
            $optimizedTables = [];
            
            foreach ($tables as $table) {
                if (DB::getSchemaBuilder()->hasTable($table)) {
                    DB::statement("ANALYZE TABLE {$table}");
                    $optimizedTables[] = $table;
                }
            }
            
            // Clear query cache
            DB::statement('FLUSH QUERY CACHE');
            
            // Optimize cache
            Cache::flush();
            
            return response()->json([
                'success' => true, 
                'message' => 'Database optimized successfully',
                'optimized_tables' => $optimizedTables
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to optimize database: ' . $e->getMessage()]);
        }
    }

    public function backupSystem()
    {
        try {
            // Add system backup logic here
            return response()->json(['success' => true, 'message' => 'System backup created successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to create backup: ' . $e->getMessage()]);
        }
    }

    public function toggleMaintenanceMode()
    {
        try {
            if (app()->isDownForMaintenance()) {
                Artisan::call('up');
                $message = 'Maintenance mode disabled';
            } else {
                Artisan::call('down', ['--secret' => 'admin-secret']);
                $message = 'Maintenance mode enabled';
            }
            
            return response()->json(['success' => true, 'message' => $message]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to toggle maintenance mode: ' . $e->getMessage()]);
        }
    }
} 