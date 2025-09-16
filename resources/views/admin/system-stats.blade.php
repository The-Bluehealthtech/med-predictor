@extends('layouts.app')

@section('title', 'Statistiques Système Techniques')

@section('content')
<div class="min-h-screen bg-gray-50 py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <nav class="flex" aria-label="Breadcrumb">
                <ol class="flex items-center space-x-4">
                    <li>
                        <a href="{{ route('dashboard') }}" class="text-gray-400 hover:text-gray-500">
                            Dashboard
                        </a>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <svg class="flex-shrink-0 h-5 w-5 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                            </svg>
                            <a href="{{ route('modules.administration.index') }}" class="ml-4 text-gray-400 hover:text-gray-500">
                                Administration
                            </a>
                        </div>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <svg class="flex-shrink-0 h-5 w-5 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                            </svg>
                            <span class="ml-4 text-gray-500">Statistiques Techniques</span>
                        </div>
                    </li>
                </ol>
            </nav>
            <h1 class="mt-4 text-3xl font-bold text-gray-900">🔧 Statistiques Système Techniques</h1>
            <p class="mt-2 text-gray-600">Métriques avancées et monitoring technique du système FIT</p>
        </div>

        <!-- Overview Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Total Users -->
            <div class="bg-white overflow-hidden shadow rounded-lg border-l-4 border-blue-500">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-blue-500 rounded-md flex items-center justify-center">
                                <span class="text-white text-sm">👥</span>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Total Utilisateurs</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ number_format($userStats['total_users']) }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Database Status -->
            <div class="bg-white overflow-hidden shadow rounded-lg border-l-4 border-green-500">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-green-500 rounded-md flex items-center justify-center">
                                <span class="text-white text-sm">💾</span>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Base de Données</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ $databaseStats['connection_status'] }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <!-- CI/CD Status -->
            <div class="bg-white overflow-hidden shadow rounded-lg border-l-4 border-purple-500">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-purple-500 rounded-md flex items-center justify-center">
                                <span class="text-white text-sm">🚀</span>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">GitHub Actions</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ $cicdStats['github_actions_status'] }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <!-- System Health -->
            <div class="bg-white overflow-hidden shadow rounded-lg border-l-4 border-orange-500">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-orange-500 rounded-md flex items-center justify-center">
                                <span class="text-white text-sm">⚡</span>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">CPU Usage</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ $systemStats['cpu_usage'] }}%</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Technical Details Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <!-- Users Statistics -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">👥 Statistiques Utilisateurs</h3>
                    <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                        <div class="sm:col-span-1">
                            <dt class="text-sm font-medium text-gray-500">Utilisateurs Actifs</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ number_format($userStats['active_users']) }}</dd>
                        </div>
                        <div class="sm:col-span-1">
                            <dt class="text-sm font-medium text-gray-500">Administrateurs</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ number_format($userStats['admin_users']) }}</dd>
                        </div>
                        <div class="sm:col-span-1">
                            <dt class="text-sm font-medium text-gray-500">Connexions (7j)</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ number_format($userStats['recent_logins']) }}</dd>
                        </div>
                        <div class="sm:col-span-1">
                            <dt class="text-sm font-medium text-gray-500">Total Rôles</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $userStats['total_roles'] ?? 0 }}</dd>
                        </div>
                    </dl>
                    <!-- Users by Role Chart -->
                    <div class="mt-4">
                        <h4 class="text-sm font-medium text-gray-700 mb-2">Répartition par Rôle</h4>
                        <div class="space-y-2">
                            @foreach($userStats['users_by_role'] as $role => $count)
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600">{{ ucfirst($role) }}</span>
                                <div class="flex items-center">
                                    <div class="w-20 bg-gray-200 rounded-full h-2 mr-2">
                                        <div class="bg-blue-500 h-2 rounded-full" style="width: {{ ($count / $userStats['total_users']) * 100 }}%"></div>
                                    </div>
                                    <span class="text-sm font-medium text-gray-900">{{ $count }}</span>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!-- Database Statistics -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">💾 Base de Données</h3>
                    <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                        <div class="sm:col-span-1">
                            <dt class="text-sm font-medium text-gray-500">Taille Total</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $databaseStats['database_size'] }} MB</dd>
                        </div>
                        <div class="sm:col-span-1">
                            <dt class="text-sm font-medium text-gray-500">Nombre Tables</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $databaseStats['total_tables'] }}</dd>
                        </div>
                        <div class="sm:col-span-1">
                            <dt class="text-sm font-medium text-gray-500">Requêtes Lentes</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $databaseStats['slow_queries'] }}</dd>
                        </div>
                        <div class="sm:col-span-1">
                            <dt class="text-sm font-medium text-gray-500">Statut</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $databaseStats['connection_status'] === 'Connected' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $databaseStats['connection_status'] }}
                                </span>
                            </dd>
                        </div>
                    </dl>
                    <!-- Top Tables by Size -->
                    @if(count($databaseStats['table_sizes']) > 0)
                    <div class="mt-4">
                        <h4 class="text-sm font-medium text-gray-700 mb-2">Tables les Plus Volumineuses</h4>
                        <div class="space-y-1">
                            @foreach(array_slice($databaseStats['table_sizes'], 0, 5) as $table)
                            <div class="flex justify-between items-center text-sm">
                                <span class="text-gray-600">{{ $table->table_name ?? 'N/A' }}</span>
                                <span class="font-medium text-gray-900">{{ $table->size_mb ?? 0 }} MB</span>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Docker Statistics -->
        <div class="bg-white shadow rounded-lg mb-8">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">🐳 Docker & Containers</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div class="text-center">
                        <div class="text-2xl font-bold text-blue-600">{{ $dockerStats['containers_running'] }}</div>
                        <div class="text-sm text-gray-500">Containers Running</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-gray-600">{{ $dockerStats['containers_total'] }}</div>
                        <div class="text-sm text-gray-500">Total Containers</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-green-600">{{ $dockerStats['images_count'] }}</div>
                        <div class="text-sm text-gray-500">Docker Images</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-purple-600">{{ $dockerStats['networks_count'] }}</div>
                        <div class="text-sm text-gray-500">Networks</div>
                    </div>
                </div>
                <div class="mt-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Docker Version</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $dockerStats['docker_version'] }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Disk Usage</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $dockerStats['disk_usage'] }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Memory Usage</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $dockerStats['memory_usage'] }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">CPU Usage</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $dockerStats['cpu_usage'] }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Volumes</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $dockerStats['volumes_count'] }}</dd>
                    </div>
                </div>
            </div>
        </div>

        <!-- CI/CD and System Information -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <!-- CI/CD Statistics -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">🚀 CI/CD & GitHub Actions</h3>
                    <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                        <div class="sm:col-span-1">
                            <dt class="text-sm font-medium text-gray-500">Dernier Déploiement</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $cicdStats['last_deployment'] }}</dd>
                        </div>
                        <div class="sm:col-span-1">
                            <dt class="text-sm font-medium text-gray-500">Taux de Réussite</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $cicdStats['pipeline_success_rate'] }}%</dd>
                        </div>
                        <div class="sm:col-span-1">
                            <dt class="text-sm font-medium text-gray-500">Durée Moyenne</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $cicdStats['build_duration'] }}</dd>
                        </div>
                        <div class="sm:col-span-1">
                            <dt class="text-sm font-medium text-gray-500">Builds Échoués</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $cicdStats['failed_builds'] }}</dd>
                        </div>
                    </dl>
                    <!-- Pipeline Success Rate -->
                    <div class="mt-4">
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-500">Taux de Réussite Pipeline</span>
                            <span class="text-green-600 font-medium">{{ $cicdStats['pipeline_success_rate'] }}%</span>
                        </div>
                        <div class="mt-2 bg-gray-200 rounded-full h-2">
                            <div class="bg-green-500 h-2 rounded-full" style="width: {{ $cicdStats['pipeline_success_rate'] }}%"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- System Information -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">⚙️ Informations Système</h3>
                    <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                        <div class="sm:col-span-1">
                            <dt class="text-sm font-medium text-gray-500">PHP Version</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $systemStats['php_version'] }}</dd>
                        </div>
                        <div class="sm:col-span-1">
                            <dt class="text-sm font-medium text-gray-500">Laravel Version</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $systemStats['laravel_version'] }}</dd>
                        </div>
                        <div class="sm:col-span-1">
                            <dt class="text-sm font-medium text-gray-500">Mémoire Utilisée</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $systemStats['memory_usage'] }} MB</dd>
                        </div>
                        <div class="sm:col-span-1">
                            <dt class="text-sm font-medium text-gray-500">Limite Mémoire</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $systemStats['memory_limit'] }}</dd>
                        </div>
                        <div class="sm:col-span-1">
                            <dt class="text-sm font-medium text-gray-500">OS</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $systemStats['operating_system'] }}</dd>
                        </div>
                        <div class="sm:col-span-1">
                            <dt class="text-sm font-medium text-gray-500">Serveur</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $systemStats['server_software'] }}</dd>
                        </div>
                    </dl>
                </div>
            </div>
        </div>

        <!-- Security and Logs -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <!-- Security Statistics -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">🔒 Sécurité</h3>
                    <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                        <div class="sm:col-span-1">
                            <dt class="text-sm font-medium text-gray-500">Connexions Échouées</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $securityStats['failed_logins'] }}</dd>
                        </div>
                        <div class="sm:col-span-1">
                            <dt class="text-sm font-medium text-gray-500">Activités Suspectes</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $securityStats['suspicious_activities'] }}</dd>
                        </div>
                        <div class="sm:col-span-1">
                            <dt class="text-sm font-medium text-gray-500">Certificat SSL</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $securityStats['ssl_certificate_status'] === 'Valid' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $securityStats['ssl_certificate_status'] }}
                                </span>
                            </dd>
                        </div>
                        <div class="sm:col-span-1">
                            <dt class="text-sm font-medium text-gray-500">Firewall</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $securityStats['firewall_status'] === 'Active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $securityStats['firewall_status'] }}
                                </span>
                            </dd>
                        </div>
                    </dl>
                </div>
            </div>

            <!-- Logs Statistics -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">📋 Logs Système</h3>
                    <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                        <div class="sm:col-span-1">
                            <dt class="text-sm font-medium text-gray-500">Erreurs (24h)</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $logStats['error_logs_24h'] }}</dd>
                        </div>
                        <div class="sm:col-span-1">
                            <dt class="text-sm font-medium text-gray-500">Erreurs (7j)</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $logStats['error_logs_7d'] }}</dd>
                        </div>
                        <div class="sm:col-span-1">
                            <dt class="text-sm font-medium text-gray-500">Avertissements (24h)</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $logStats['warning_logs_24h'] }}</dd>
                        </div>
                        <div class="sm:col-span-1">
                            <dt class="text-sm font-medium text-gray-500">Taille Fichier Log</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $logStats['log_file_size'] }} MB</dd>
                        </div>
                    </dl>
                    <!-- Log Levels Chart -->
                    <div class="mt-4">
                        <h4 class="text-sm font-medium text-gray-700 mb-2">Niveaux de Log (24h)</h4>
                        <div class="space-y-2">
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600">Erreurs</span>
                                <div class="flex items-center">
                                    <div class="w-20 bg-gray-200 rounded-full h-2 mr-2">
                                        <div class="bg-red-500 h-2 rounded-full" style="width: {{ min(100, ($logStats['error_logs_24h'] / max(1, $logStats['error_logs_24h'] + $logStats['warning_logs_24h'] + $logStats['info_logs_24h'])) * 100) }}%"></div>
                                    </div>
                                    <span class="text-sm font-medium text-gray-900">{{ $logStats['error_logs_24h'] }}</span>
                                </div>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600">Avertissements</span>
                                <div class="flex items-center">
                                    <div class="w-20 bg-gray-200 rounded-full h-2 mr-2">
                                        <div class="bg-yellow-500 h-2 rounded-full" style="width: {{ min(100, ($logStats['warning_logs_24h'] / max(1, $logStats['error_logs_24h'] + $logStats['warning_logs_24h'] + $logStats['info_logs_24h'])) * 100) }}%"></div>
                                    </div>
                                    <span class="text-sm font-medium text-gray-900">{{ $logStats['warning_logs_24h'] }}</span>
                                </div>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600">Informations</span>
                                <div class="flex items-center">
                                    <div class="w-20 bg-gray-200 rounded-full h-2 mr-2">
                                        <div class="bg-blue-500 h-2 rounded-full" style="width: {{ min(100, ($logStats['info_logs_24h'] / max(1, $logStats['error_logs_24h'] + $logStats['warning_logs_24h'] + $logStats['info_logs_24h'])) * 100) }}%"></div>
                                    </div>
                                    <span class="text-sm font-medium text-gray-900">{{ $logStats['info_logs_24h'] }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Performance Indicators -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">📊 Indicateurs de Performance</h3>
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                    <!-- Database Health -->
                    <div class="relative">
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-500">Santé Base de Données</span>
                            @php
                                $dbHealth = 100;
                                $dbStatus = 'Excellent';
                                $dbColor = 'green';
                                
                                // Calculate database health based on connection and performance
                                if ($databaseStats['connection_status'] !== 'Connected') {
                                    $dbHealth = 0;
                                    $dbStatus = 'Disconnected';
                                    $dbColor = 'red';
                                } elseif ($databaseStats['slow_queries'] > 10) {
                                    $dbHealth = 70;
                                    $dbStatus = 'Slow';
                                    $dbColor = 'yellow';
                                } elseif ($databaseStats['slow_queries'] > 5) {
                                    $dbHealth = 85;
                                    $dbStatus = 'Good';
                                    $dbColor = 'yellow';
                                }
                            @endphp
                            <span class="text-{{ $dbColor }}-600 font-medium">{{ $dbStatus }}</span>
                        </div>
                        <div class="mt-2 bg-gray-200 rounded-full h-2">
                            <div class="bg-{{ $dbColor }}-500 h-2 rounded-full" style="width: {{ $dbHealth }}%"></div>
                        </div>
                    </div>

                    <!-- System Load -->
                    <div class="relative">
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-500">Charge Système</span>
                            <span class="text-yellow-600 font-medium">{{ $systemStats['cpu_usage'] }}%</span>
                        </div>
                        <div class="mt-2 bg-gray-200 rounded-full h-2">
                            <div class="bg-yellow-500 h-2 rounded-full" style="width: {{ $systemStats['cpu_usage'] }}%"></div>
                        </div>
                    </div>

                    <!-- Memory Usage -->
                    <div class="relative">
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-500">Utilisation Mémoire</span>
                            @php
                                $memoryUsage = $systemStats['memory_usage'];
                                $memoryLimit = (int)str_replace(['M', 'G'], ['', '000'], $systemStats['memory_limit']);
                                $memoryPercent = min(100, round(($memoryUsage / $memoryLimit) * 100, 1));
                                $memoryColor = $memoryPercent > 80 ? 'red' : ($memoryPercent > 60 ? 'yellow' : 'green');
                            @endphp
                            <span class="text-{{ $memoryColor }}-600 font-medium">{{ $memoryUsage }} MB</span>
                        </div>
                        <div class="mt-2 bg-gray-200 rounded-full h-2">
                            <div class="bg-{{ $memoryColor }}-500 h-2 rounded-full" style="width: {{ $memoryPercent }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection