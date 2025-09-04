<x-back-office-layout>
    <x-slot name="title">System Settings - Back Office</x-slot>
<div class="space-y-6">


    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif

    <!-- Header -->
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 text-gray-900">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">System Settings</h1>
                    <p class="mt-1 text-sm text-gray-600">Configure system parameters and preferences</p>
                </div>
                <div>
                    <a href="{{ route('dashboard') }}" 
                       class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-md transition-colors">
                        <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                        </svg>
                        Back to Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Application Settings -->
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Application Settings</h3>
            <form method="POST" action="{{ route('back-office.settings.update') }}" class="space-y-6">
                @csrf
                @method('PUT')
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="app_name" class="block text-sm font-medium text-gray-700">Application Name</label>
                        <input type="text" name="app_name" id="app_name" value="{{ isset($settings['general']['app_name']) ? $settings['general']['app_name']->value : config('app.name') }}" 
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    </div>

                    <div>
                        <label for="app_url" class="block text-sm font-medium text-gray-700">Application URL</label>
                        <input type="url" name="app_url" id="app_url" value="{{ isset($settings['general']['app_url']) ? $settings['general']['app_url']->value : config('app.url') }}" 
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    </div>

                    <div>
                        <label for="timezone" class="block text-sm font-medium text-gray-700">Timezone</label>
                        <select name="timezone" id="timezone" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                            @php $currentTimezone = isset($settings['general']['timezone']) ? $settings['general']['timezone']->value : config('app.timezone') @endphp
                            <option value="UTC" {{ $currentTimezone == 'UTC' ? 'selected' : '' }}>UTC</option>
                            <option value="Europe/Paris" {{ $currentTimezone == 'Europe/Paris' ? 'selected' : '' }}>Europe/Paris</option>
                            <option value="Europe/London" {{ $currentTimezone == 'Europe/London' ? 'selected' : '' }}>Europe/London</option>
                            <option value="America/New_York" {{ $currentTimezone == 'America/New_York' ? 'selected' : '' }}>America/New_York</option>
                        </select>
                    </div>

                    <div>
                        <label for="locale" class="block text-sm font-medium text-gray-700">Locale</label>
                        <select name="locale" id="locale" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                            @php $currentLocale = isset($settings['general']['locale']) ? $settings['general']['locale']->value : config('app.locale') @endphp
                            <option value="en" {{ $currentLocale == 'en' ? 'selected' : '' }}>English</option>
                            <option value="fr" {{ $currentLocale == 'fr' ? 'selected' : '' }}>Français</option>
                            <option value="es" {{ $currentLocale == 'es' ? 'selected' : '' }}>Español</option>
                        </select>
                    </div>
                </div>

                <div class="flex items-center">
                    <input type="checkbox" name="debug_mode" id="debug_mode" 
                           {{ (isset($settings['general']['debug_mode']) ? ($settings['general']['debug_mode']->value == '1') : config('app.debug')) ? 'checked' : '' }}
                           class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                    <label for="debug_mode" class="ml-2 block text-sm text-gray-900">Enable Debug Mode</label>
                </div>

                <div class="flex justify-end">
                    <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">
                        Save Settings
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Email Settings -->
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Email Settings</h3>
            <form method="POST" action="{{ route('back-office.settings.email') }}" class="space-y-6">
                @csrf
                @method('PUT')
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="mail_host" class="block text-sm font-medium text-gray-700">SMTP Host</label>
                        <input type="text" name="mail_host" id="mail_host" value="{{ isset($settings['email']['mail_host']) ? $settings['email']['mail_host']->value : config('mail.mailers.smtp.host') }}" 
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    </div>

                    <div>
                        <label for="mail_port" class="block text-sm font-medium text-gray-700">SMTP Port</label>
                        <input type="number" name="mail_port" id="mail_port" value="{{ isset($settings['email']['mail_port']) ? $settings['email']['mail_port']->value : config('mail.mailers.smtp.port') }}" 
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    </div>

                    <div>
                        <label for="mail_username" class="block text-sm font-medium text-gray-700">SMTP Username</label>
                        <input type="text" name="mail_username" id="mail_username" value="{{ isset($settings['email']['mail_username']) ? $settings['email']['mail_username']->value : config('mail.mailers.smtp.username') }}" 
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    </div>

                    <div>
                        <label for="mail_password" class="block text-sm font-medium text-gray-700">SMTP Password</label>
                        <input type="password" name="mail_password" id="mail_password" value="{{ isset($settings['email']['mail_password']) ? $settings['email']['mail_password']->value : config('mail.mailers.smtp.password') }}" 
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                </div>

                <div class="flex justify-end">
                    <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">
                        Save Email Settings
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- FIFA Connect Settings -->
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">FIFA Connect Settings</h3>
            <form method="POST" action="{{ route('back-office.settings.fifa') }}" class="space-y-6">
                @csrf
                @method('PUT')
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="fifa_api_url" class="block text-sm font-medium text-gray-700">FIFA API URL</label>
                        <input type="url" name="fifa_api_url" id="fifa_api_url" value="{{ isset($settings['fifa']['fifa_api_url']) ? $settings['fifa']['fifa_api_url']->value : config('services.fifa.api_url') }}" 
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    </div>

                    <div>
                        <label for="fifa_api_key" class="block text-sm font-medium text-gray-700">FIFA API Key</label>
                        <input type="password" name="fifa_api_key" id="fifa_api_key" value="{{ isset($settings['fifa']['fifa_api_key']) ? $settings['fifa']['fifa_api_key']->value : config('services.fifa.api_key') }}" 
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                </div>

                <div class="flex items-center">
                    <input type="checkbox" name="fifa_sync_enabled" id="fifa_sync_enabled" 
                           {{ (isset($settings['fifa']['fifa_sync_enabled']) ? ($settings['fifa']['fifa_sync_enabled']->value == '1') : config('services.fifa.sync_enabled', true)) ? 'checked' : '' }}
                           class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                    <label for="fifa_sync_enabled" class="ml-2 block text-sm text-gray-900">Enable FIFA Sync</label>
                </div>

                <div class="flex justify-end">
                    <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">
                        Save FIFA Settings
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Security Settings -->
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Security Settings</h3>
            <form method="POST" action="{{ route('back-office.settings.security') }}" class="space-y-6">
                @csrf
                @method('PUT')
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="session_lifetime" class="block text-sm font-medium text-gray-700">Session Lifetime (minutes)</label>
                        <input type="number" name="session_lifetime" id="session_lifetime" value="{{ isset($settings['security']['session_lifetime']) ? $settings['security']['session_lifetime']->value : config('session.lifetime') }}" 
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    </div>

                    <div>
                        <label for="password_timeout" class="block text-sm font-medium text-gray-700">Password Timeout (minutes)</label>
                        <input type="number" name="password_timeout" id="password_timeout" value="{{ isset($settings['security']['password_timeout']) ? $settings['security']['password_timeout']->value : config('auth.password_timeout') }}" 
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                </div>

                <div class="flex items-center">
                    <input type="checkbox" name="force_https" id="force_https" 
                           {{ (isset($settings['security']['force_https']) ? ($settings['security']['force_https']->value == '1') : config('app.env') === 'production') ? 'checked' : '' }}
                           class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                    <label for="force_https" class="ml-2 block text-sm text-gray-900">Force HTTPS</label>
                </div>

                <div class="flex items-center">
                    <input type="checkbox" name="enable_csrf" id="enable_csrf" 
                           {{ (isset($settings['security']['enable_csrf']) ? ($settings['security']['enable_csrf']->value == '1') : true) ? 'checked' : '' }}
                           class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                    <label for="enable_csrf" class="ml-2 block text-sm text-gray-900">Enable CSRF Protection</label>
                </div>

                <div class="flex justify-end">
                    <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">
                        Save Security Settings
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Maintenance Mode -->
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Maintenance Mode</h3>
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h4 class="text-sm font-medium text-gray-900">Maintenance Mode</h4>
                        <p class="text-sm text-gray-600">Enable maintenance mode to restrict access</p>
                    </div>
                    <button onclick="toggleMaintenanceMode()" class="bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700">
                        Enable Maintenance Mode
                    </button>
                </div>
                
                <div class="bg-yellow-50 border border-yellow-200 rounded-md p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-yellow-800">Warning</h3>
                            <div class="mt-2 text-sm text-yellow-700">
                                <p>Enabling maintenance mode will restrict access to all users except administrators. Make sure to disable it when maintenance is complete.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function toggleMaintenanceMode() {
    if (confirm('Are you sure you want to enable maintenance mode? This will restrict access to all users except administrators.')) {
        // Add AJAX call to toggle maintenance mode
        console.log('Toggling maintenance mode...');
    }
}
</script>
</x-back-office-layout> 