<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ __('general.admin_panel') }} - {{ config('app.name', 'Synco Save') }}</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    
    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-100 dark:bg-gray-900">
    <div class="min-h-screen flex">
        <!-- Mobile menu overlay -->
        <div id="mobile-menu-overlay" class="fixed inset-0 z-40 bg-black bg-opacity-50 lg:hidden hidden"></div>
        
        <!-- Sidebar -->
        <div id="sidebar" class="fixed inset-y-0 left-0 z-50 w-64 bg-white dark:bg-gray-800 shadow-lg transform -translate-x-full transition-transform duration-300 ease-in-out lg:translate-x-0 lg:static lg:inset-0">
            <div class="p-6">
                <div class="flex items-center justify-between">
                    <h2 class="text-xl font-semibold text-gray-800 dark:text-white">{{ __('general.admin_panel') }}</h2>
                    <!-- Close button for mobile -->
                    <button id="close-sidebar" class="lg:hidden text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>
            
            <nav class="mt-6">
                <div class="px-6 py-3">
                    <a href="{{ route('admin.dashboard') }}" 
                       class="flex items-center px-4 py-2 text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg {{ request()->routeIs('admin.dashboard') ? 'bg-blue-100 dark:bg-blue-900 text-blue-700 dark:text-blue-200' : '' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
                        </svg>
                        {{ __('general.dashboard') }}
                    </a>
                </div>
                
                <div class="px-6 py-3">
                    <a href="{{ route('admin.groups.index') }}" 
                       class="flex items-center px-4 py-2 text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg {{ request()->routeIs('admin.groups.*') ? 'bg-blue-100 dark:bg-blue-900 text-blue-700 dark:text-blue-200' : '' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                        {{ __('general.groups_management') }}
                    </a>
                </div>
                
                <div class="px-6 py-3">
                    <a href="{{ route('admin.users.index') }}" 
                       class="flex items-center px-4 py-2 text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg {{ request()->routeIs('admin.users.*') ? 'bg-blue-100 dark:bg-blue-900 text-blue-700 dark:text-blue-200' : '' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m3 5.197H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        {{ __('general.users_management') }}
                    </a>
                </div>
                
                <div class="px-6 py-3">
                    <a href="{{ route('admin.transactions.index') }}" 
                       class="flex items-center px-4 py-2 text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg {{ request()->routeIs('admin.transactions.*') ? 'bg-blue-100 dark:bg-blue-900 text-blue-700 dark:text-blue-200' : '' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        {{ __('general.transactions_management') }}
                    </a>
                </div>
                
                <div class="px-6 py-3">
                    <a href="{{ route('admin.withdrawal-requests.index') }}" 
                       class="flex items-center px-4 py-2 text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg {{ request()->routeIs('admin.withdrawal-requests.*') ? 'bg-blue-100 dark:bg-blue-900 text-blue-700 dark:text-blue-200' : '' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                        {{ __('general.withdrawal_requests_management') }}
                    </a>
                </div>
                
                <div class="px-6 py-3 mt-8">
                    <a href="{{ route('dashboard') }}" 
                       class="flex items-center px-4 py-2 text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        {{ __('general.back_to_app') }}
                    </a>
                </div>
            </nav>
        </div>
        
        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden lg:ml-0">
            <!-- Top Navigation -->
            <header class="bg-white dark:bg-gray-800 shadow-sm border-b border-gray-200 dark:border-gray-700">
                <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between items-center">
                        <div class="flex items-center">
                            <!-- Mobile menu button -->
                            <button id="mobile-menu-button" class="lg:hidden mr-4 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                                </svg>
                            </button>
                            
                            @isset($header)
                                {{ $header }}
                            @endisset
                        </div>
                        
                        <div class="flex items-center space-x-4">
                            <!-- Language Selector -->
                            <div class="relative">
                                <select onchange="changeLanguage(this.value)" class="bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block p-2.5">
                                    <option value="en" {{ app()->getLocale() == 'en' ? 'selected' : '' }}>{{ __('general.english') }}</option>
                                    <option value="yoruba" {{ app()->getLocale() == 'yoruba' ? 'selected' : '' }}>{{ __('general.yoruba') }}</option>
                                    <option value="igbo" {{ app()->getLocale() == 'igbo' ? 'selected' : '' }}>{{ __('general.igbo') }}</option>
                                    <option value="hausa" {{ app()->getLocale() == 'hausa' ? 'selected' : '' }}>{{ __('general.hausa') }}</option>
                                </select>
                            </div>
                            
                            <!-- User Info -->
                            <div class="text-sm text-gray-700 dark:text-gray-200">
                                {{ auth()->user()->name }}
                            </div>
                        </div>
                    </div>
                </div>
            </header>
            
            <!-- Page Content -->
            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100 dark:bg-gray-900">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    {{ $slot }}
                </div>
            </main>
        </div>
    </div>
    
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script>
        function changeLanguage(locale) {
            window.location.href = '{{ url("/set-locale") }}/' + locale + '?redirect=' + encodeURIComponent(window.location.href);
        }
        
        // Mobile menu functionality
        $(document).ready(function() {
            const sidebar = $('#sidebar');
            const overlay = $('#mobile-menu-overlay');
            const menuButton = $('#mobile-menu-button');
            const closeButton = $('#close-sidebar');
            
            // Open mobile menu
            menuButton.on('click', function() {
                sidebar.removeClass('-translate-x-full');
                overlay.removeClass('hidden');
            });
            
            // Close mobile menu
            function closeMobileMenu() {
                sidebar.addClass('-translate-x-full');
                overlay.addClass('hidden');
            }
            
            closeButton.on('click', closeMobileMenu);
            overlay.on('click', closeMobileMenu);
            
            // Close menu when clicking on navigation links (mobile only)
            sidebar.find('a').on('click', function() {
                if (window.innerWidth < 1024) {
                    closeMobileMenu();
                }
            });
            
            @if(session('success'))
                Swal.fire({
                    icon: 'success',
                    title: '{{ session("success") }}'
                })
            @endif
            
            @if(session('error'))
                Swal.fire({
                    icon: 'error',
                    title: '{{ session("error") }}'
                })
            @endif
        });
    </script>
</body>
</html>