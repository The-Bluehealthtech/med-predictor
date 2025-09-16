<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script>window.LARAVEL_LOCALE = '{{ app()->getLocale() }}';</script>

    <title>{{ __('app.title') }}</title>
    <meta name="description" content="Enterprise football management platform with AI analytics, real-time tracking, and FIFA Connect compliance.">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="fifa-body">
    <div id="language-selector-app" style="position:fixed;top:0;right:0;z-index:10001;"></div>
    <div id="app">
        <!-- Vue.js Application will be mounted here -->
        <div class="fifa-loading" id="loading-screen">
            <div class="fifa-loading__spinner"></div>
            <p class="fifa-loading__text">{{ __('app.loading') }}</p>
        </div>
    </div>

    <style>
        .fifa-body {
            margin: 0;
            padding: 0;
            font-family: var(--fifa-font-family);
            background: var(--fifa-gray-50);
            overflow-x: hidden;
        }

        .fifa-loading {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, var(--fifa-blue-primary) 0%, var(--fifa-blue-secondary) 100%);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            z-index: 9999;
        }

        .fifa-loading__spinner {
            width: 48px;
            height: 48px;
            border: 4px solid rgba(255, 255, 255, 0.3);
            border-top: 4px solid var(--fifa-white);
            border-radius: 50%;
            animation: fifa-spin 1s linear infinite;
            margin-bottom: var(--fifa-spacing-lg);
        }

        .fifa-loading__text {
            color: var(--fifa-white);
            font-size: var(--fifa-text-lg);
            font-weight: var(--fifa-font-weight-medium);
            margin: 0;
        }

        @keyframes fifa-spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Hide loading screen when Vue app is ready */
        .fifa-app-ready .fifa-loading {
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.3s ease-out;
        }
    </style>

    <script>
        // Add ready class when Vue app is mounted
        document.addEventListener('DOMContentLoaded', function() {
            // Wait for Vue to mount
            setTimeout(() => {
                document.body.classList.add('fifa-app-ready');
            }, 100);
        });

        // CSRF token setup for Vue
        window.csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        // Global error handling
        window.addEventListener('error', function(e) {
            console.error('Global error:', e.error);
        });

        // Global notification system
        window.showNotification = function(type, message, duration = 5000) {
            const notification = document.createElement('div');
            notification.className = `fifa-notification fifa-notification--${type}`;
            notification.innerHTML = `
                <div class="fifa-notification__content">
                    <span class="fifa-notification__message">${message}</span>
                    <button class="fifa-notification__close" onclick="this.parentElement.parentElement.remove()">
                        <svg viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                        </svg>
                    </button>
                </div>
            `;
            
            document.body.appendChild(notification);
            
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.remove();
                }
            }, duration);
        };

        // Global success notification
        window.showSuccess = function(message, duration = 5000) {
            window.showNotification('success', message, duration);
        };

        // Global error notification
        window.showError = function(message, duration = 5000) {
            window.showNotification('error', message, duration);
        };

        // Global warning notification
        window.showWarning = function(message, duration = 5000) {
            window.showNotification('warning', message, duration);
        };
    </script>

    <style>
        /* Notification styles */
        .fifa-notification {
            position: fixed;
            top: 20px;
            right: 20px;
            background: var(--fifa-white);
            border-left: 4px solid var(--fifa-blue-primary);
            border-radius: var(--fifa-border-radius);
            box-shadow: var(--fifa-shadow-lg);
            z-index: 10000;
            min-width: 300px;
            max-width: 400px;
        }

        .fifa-notification--success {
            border-left-color: var(--fifa-success);
        }

        .fifa-notification--error {
            border-left-color: var(--fifa-error);
        }

        .fifa-notification--warning {
            border-left-color: var(--fifa-warning);
        }

        .fifa-notification__content {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: var(--fifa-spacing-lg);
        }

        .fifa-notification__message {
            color: var(--fifa-gray-800);
            font-size: var(--fifa-text-sm);
            font-weight: var(--fifa-font-weight-medium);
        }

        .fifa-notification__close {
            background: none;
            border: none;
            color: var(--fifa-gray-400);
            cursor: pointer;
            padding: 4px;
            margin-left: var(--fifa-spacing-md);
        }

        .fifa-notification__close:hover {
            color: var(--fifa-gray-600);
        }

        .fifa-notification__close svg {
            width: 16px;
            height: 16px;
        }
    </style>
</body>
</html> 