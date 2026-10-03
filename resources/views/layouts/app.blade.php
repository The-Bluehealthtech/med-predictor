<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name', 'FIT'))</title>
    <link rel="icon" type="image/png" href="{{ asset('images/fit-logo.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/fit-logo.png') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Compiled assets - Laravel Mix -->
    <link rel="stylesheet" href="{{ mix('/css/app.css') }}">
    <link rel="stylesheet" href="{{ mix('/css/fifa-design-system.css') }}">
    <script src="{{ mix('/js/app.js') }}" defer></script>

    @php $isModulesPage = request()->is('modules') || request()->is('modules/*'); @endphp
    @if($isModulesPage)
        <script src="https://cdn.tailwindcss.com"></script>
    @endif

    <!-- Alpine.js via CDN for Blade dropdowns -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <!-- Custom CSS for navigation spacing -->
    <style>
        {{-- Navigation CSS commented out since navigation is removed
        /* Force navigation bar height */
        .nav-bar {
            height: 5rem !important; /* 80px */
            min-height: 5rem !important;
            max-height: 5rem !important;
            position: fixed !important;
            top: 0 !important;
            left: 0 !important;
            right: 0 !important;
            z-index: 1000 !important;
        }
        
        /* Force content spacing */
        .nav-fixed-spacing {
            padding-top: 8rem !important; /* 128px - significantly increased */
            margin-top: 0 !important;
        }
        
        @media (min-width: 640px) {
            .nav-fixed-spacing {
                padding-top: 9rem !important; /* 144px - significantly increased */
            }
        }
        
        /* Additional safety margin */
        body {
            margin-top: 0 !important;
            padding-top: 0 !important;
        }
        
        /* Ensure main content starts below navigation */
        .main-content-wrapper {
            margin-top: 6rem !important; /* 96px minimum */
            padding-top: 2rem !important; /* Additional padding */
            position: relative !important;
            z-index: 1 !important;
        }
        
        /* Ensure all content is below navigation */
        main {
            position: relative !important;
            z-index: 1 !important;
        }
        --}}
        
        /* Reset spacing since navigation is removed */
        .nav-fixed-spacing {
            padding-top: 0 !important;
            margin-top: 0 !important;
        }
        
        .main-content-wrapper {
            margin-top: 0 !important;
            padding-top: 0 !important;
        }
    </style>
    
    
    <!-- Additional styles -->
    @stack('styles')
    

    
    <!-- Alpine.js Fallback Script (DISABLED, now using CDN) -->
    <!--
    <script>
        // Diagnostic script for Alpine.js
        console.log('App layout loaded, checking Alpine.js...');
        
        // Fallback for Alpine.js dropdown functionality
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Initializing dropdown fallback...');
            
            // Wait a bit for Alpine.js to load, then check
            setTimeout(function() {
                if (typeof window.Alpine === 'undefined') {
                    console.log('Alpine.js not found, initializing fallback...');
                    initializeDropdownFallback();
                } else {
                    console.log('Alpine.js found, dropdowns should work normally');
                }
            }, 100);
        });
        
        function initializeDropdownFallback() {
            console.log('Setting up dropdown fallback...');
            
            // Handle all dropdown elements
            document.querySelectorAll('[x-data*="open"]').forEach(function(element) {
                console.log('Found dropdown element:', element);
                let isOpen = false;
                
                // Find the trigger button
                const trigger = element.querySelector('button');
                if (trigger) {
                    console.log('Found trigger for dropdown:', trigger);
                    trigger.addEventListener('click', function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        isOpen = !isOpen;
                        console.log('Dropdown toggled, isOpen:', isOpen);
                        
                        // Find dropdown content
                        const content = element.querySelector('[x-show*="open"], [x-show]');
                        if (content) {
                            if (isOpen) {
                                content.style.display = 'block';
                                content.style.opacity = '1';
                                content.style.transform = 'scale(1)';
                                content.style.transition = 'all 0.2s ease-out';
                            } else {
                                content.style.opacity = '0';
                                content.style.transform = 'scale(0.95)';
                                setTimeout(() => {
                                    if (!isOpen) {
                                        content.style.display = 'none';
                                    }
                                }, 200);
                            }
                        }
                    });
                }
                
                // Close dropdown when clicking outside
                document.addEventListener('click', function(e) {
                    if (!element.contains(e.target) && isOpen) {
                        isOpen = false;
                        const content = element.querySelector('[x-show*="open"], [x-show]');
                        if (content) {
                            content.style.opacity = '0';
                            content.style.transform = 'scale(0.95)';
                            setTimeout(() => {
                                content.style.display = 'none';
                            }, 200);
                        }
                    }
                });
            });
            
            // Handle responsive navigation
            const mobileMenuButton = document.querySelector('button');
            const mobileMenu = document.querySelector('[x-show*="open"]');
            
            if (mobileMenuButton && mobileMenu) {
                let mobileOpen = false;
                
                mobileMenuButton.addEventListener('click', function(e) {
                    e.preventDefault();
                    mobileOpen = !mobileOpen;
                    
                    if (mobileOpen) {
                        mobileMenu.style.display = 'block';
                    } else {
                        mobileMenu.style.display = 'none';
                    }
                });
            }
        }
    </script>
    -->
    <!-- Simple JavaScript Dropdown Solution (DISABLED, now using CDN) -->
    <!--
    <script>
        // Wait for DOM to be ready
        document.addEventListener('DOMContentLoaded', function() {
            console.log('🚀 Initializing dropdown system...');
            
            // Initialize dropdowns after a short delay
            setTimeout(function() {
                setupDropdowns();
                setupMobileMenu();
            }, 100);
        });
        
        function setupDropdowns() {
            // Find all dropdown containers
            const dropdowns = document.querySelectorAll('[x-data*="open"]');
            console.log('📋 Found', dropdowns.length, 'dropdown elements');
            
            dropdowns.forEach((dropdown, index) => {
                console.log('🔽 Setting up dropdown', index + 1);
                
                // Find trigger and content
                const trigger = dropdown.querySelector('button');
                const content = dropdown.querySelector('[x-show*="open"], [x-show]');
                
                if (trigger && content) {
                    console.log('✅ Found trigger and content for dropdown', index + 1);
                    
                    // Remove Alpine.js attributes to prevent conflicts
                    // trigger.removeAttribute('@click'); // This line is not needed for native JS
                    content.removeAttribute('x-show');
                    
                    // Add our own click handler
                    trigger.addEventListener('click', function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        
                        const isVisible = content.style.display === 'block';
                        
                        if (isVisible) {
                            // Hide dropdown
                            content.style.display = 'none';
                            content.style.opacity = '0';
                            content.style.transform = 'scale(0.95)';
                            console.log('👁️ Hiding dropdown', index + 1);
                        } else {
                            // Show dropdown
                            content.style.display = 'block';
                            content.style.opacity = '1';
                            content.style.transform = 'scale(1)';
                            content.style.transition = 'all 0.2s ease-out';
                            console.log('👁️ Showing dropdown', index + 1);
                        }
                    });
                    
                    // Close when clicking outside
                    document.addEventListener('click', function(e) {
                        if (!dropdown.contains(e.target)) {
                            content.style.display = 'none';
                            content.style.opacity = '0';
                            content.style.transform = 'scale(0.95)';
                        }
                    });
                }
            });
        }
        
        function setupMobileMenu() {
            const mobileButton = document.querySelector('button');
            const mobileMenu = document.querySelector('[x-show*="open"]');
            
            if (mobileButton && mobileMenu) {
                console.log('📱 Setting up mobile menu');
                
                // Remove Alpine.js attributes
                // mobileButton.removeAttribute('@click'); // This line is not needed for native JS
                mobileMenu.removeAttribute('x-show');
                
                mobileButton.addEventListener('click', function(e) {
                    e.preventDefault();
                    
                    const isVisible = mobileMenu.style.display === 'block';
                    
                    if (isVisible) {
                        mobileMenu.style.display = 'none';
                        console.log('📱 Hiding mobile menu');
                    } else {
                        mobileMenu.style.display = 'block';
                        console.log('📱 Showing mobile menu');
                    }
                });
            }
        }
        
        // Test function
        function testDropdown() {
            console.log('🧪 Testing dropdown functionality...');
            console.log('📊 Alpine.js available:', typeof window.Alpine !== 'undefined');
            console.log('📊 Dropdowns found:', document.querySelectorAll('[x-data*="open"]').length);
            
            // Test each dropdown
            const dropdowns = document.querySelectorAll('[x-data*="open"]');
            dropdowns.forEach((dropdown, index) => {
                const trigger = dropdown.querySelector('button');
                const content = dropdown.querySelector('[x-show*="open"], [x-show]');
                console.log(`Dropdown ${index + 1}:`, {
                    hasTrigger: !!trigger,
                    hasContent: !!content,
                    triggerText: trigger ? trigger.textContent.trim() : 'N/A'
                });
            });
            
            alert('Dropdown test completed! Check console for details.');
        }
        
        // Debug function
        function debugDropdowns() {
            console.log('🔍 Debugging dropdowns...');
            
            // Check all elements with x-data
            const allXData = document.querySelectorAll('[x-data]');
            console.log('📋 All x-data elements:', allXData.length);
            
            allXData.forEach((el, index) => {
                console.log(`Element ${index + 1}:`, {
                    xData: el.getAttribute('x-data'),
                    tagName: el.tagName,
                    className: el.className,
                    hasClick: !!el.querySelector('button'),
                    hasShow: !!el.querySelector('[x-show]')
                });
            });
            
            // Check for Alpine.js
            console.log('Alpine.js status:', {
                available: typeof window.Alpine !== 'undefined',
                version: window.Alpine ? window.Alpine.version : 'N/A'
            });
            
            // Check for JavaScript errors
            console.log('🔧 JavaScript errors in console?');
            
            alert('Debug completed! Check console for detailed information.');
        }
    </script>
    -->
</head>
<body class="font-sans antialiased">

    <div id="app"></div>
    <div class="min-h-screen bg-gray-100 nav-fixed-spacing main-content-wrapper" style="padding-top: 8rem;">
        @php
            $isBackOfficeRoute = request()->routeIs('back-office.*');
        @endphp
        
        @if(!$isBackOfficeRoute)
            {{-- @include('layouts.navigation') --}}
            @include('partials.top-bar')
        @endif

        <!-- Page Heading -->
        @if (isset($header))
            <header class="bg-white shadow">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
        @endif

        <!-- Page Content -->
        <main>
            @include('partials.back-links')
            
            {{-- Component layouts (for <x-app-layout>) pass their page body through $slot. --}}
            {{ $slot ?? '' }}
            @yield('content')
        </main>

        <!-- Footer -->
        <footer class="bg-gray-900 text-white mt-auto">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <!-- FIT Platform -->
                    <div>
                        <div class="flex items-center mb-4">
                            <div class="text-2xl font-bold">
                                <span class="text-white">F</span>
                                <span class="bg-blue-600 text-white px-1 relative">
                                    I
                                    <svg class="absolute inset-0 w-full h-full" viewBox="0 0 20 20" fill="none">
                                        <path d="M2 10 L6 6 L10 10 L14 6 L18 10" stroke="white" stroke-width="2" fill="none"/>
                                    </svg>
                                </span>
                                <span class="text-white">T</span>
                            </div>
                            <div class="ml-3 text-sm text-blue-300 font-semibold uppercase tracking-wide">
                                <div>FOOTBALL</div>
                                <div>INTELLIGENCE</div>
                                <div>& TRACKING</div>
                            </div>
                        </div>
                        <p class="text-gray-400 text-sm">
                            {{ __('dashboard.smarter_football') }}
                        </p>
                    </div>

                    <!-- FIFA Connect -->
                    <div>
                        <h3 class="text-lg font-semibold mb-4">{{ app()->getLocale() === 'en' ? 'FIFA Connect data model' : 'Modèle de données FIFA Connect' }}</h3>
                        <p class="text-gray-400 text-sm mb-4">
                            {{ app()->getLocale() === 'en' ? 'Demonstration records are not official FIFA identities or certifications.' : 'Les données de démonstration ne sont ni des identités ni des certifications officielles FIFA.' }}
                        </p>
                        <a href="https://www.fifa.com/what-we-do/fifa-connect" target="_blank" class="text-blue-400 hover:text-blue-300 text-sm">
                            Learn more →
                        </a>
                    </div>

                    <!-- Powered By -->
                    <div>
                        <h3 class="text-lg font-semibold mb-4">Powered By</h3>
                        <div class="bg-white bg-opacity-10 rounded-lg p-4">
                            <div class="text-center">
                                <h4 class="font-semibold text-white mb-2">The Blue Healthtech Ltd</h4>
                                <p class="text-gray-400 text-sm mb-3">
                                    © The Blue Healthtech Ltd. All rights reserved.
                                </p>
                                <a href="https://tbhc.uk" target="_blank" rel="noopener noreferrer" 
                                   class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-md transition-colors duration-200">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                    </svg>
                                    Visit TBHC
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="border-t border-gray-800 mt-8 pt-8 text-center">
                    <p class="text-gray-400 text-sm">
                        © 2024 The Blue Healthtech Ltd. All rights reserved. | FIT - Football Intelligence & Tracking
                    </p>
                </div>
            </div>
        </footer>
    </div>
    
    <!-- Scripts Stack -->
    @stack('scripts')
</body>
</html>
