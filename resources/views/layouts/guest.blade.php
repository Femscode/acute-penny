<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <meta property="og:locale" content="en_US" />
	<meta property="og:type" content="article" />
    <title>{{ config('app.name', 'Synco Save') }}</title>
    @if(isset($show_info) && $show_info)
    <meta name="title" content="Become part of our savings circle">
    <meta name="description" content="Become part of our savings circle">
    <meta name="keywords" content="Join {{ $group->name }} to save together and get paid in turn.">
    <meta property="og:title" content="Join {{ $group->name }} to save together and get paid in turn." />
    @else
    <meta name="title" content="Discover Syncosave">
    <meta name="description" content="Discover Syncosave">
    <meta name="keywords" content="Join hands with the ones you love, friends, and family to grow financially.">
    <meta property="og:title" content="Join hands with the ones you love, friends, and family to grow financially.">
    @endif

    <meta property="og:image" content="https://syncosave.com/logo.png" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans text-gray-900 antialiased"
    x-data="{ 
              darkMode: localStorage.getItem('theme') === 'dark' || (!localStorage.getItem('theme') && window.matchMedia('(prefers-color-scheme: dark)').matches)
          }"
    x-init="
              if (darkMode) {
                  document.documentElement.classList.add('dark');
              } else {
                  document.documentElement.classList.remove('dark');
              }
          ">
    <div class="min-h-screen bg-gray-100 dark:bg-gray-900">
        <!-- Theme Toggle for Guest Pages -->
        <div class="absolute top-4 right-4 z-50">
            <button
                @click="
                        darkMode = !darkMode;
                        localStorage.setItem('theme', darkMode ? 'dark' : 'light');
                        if (darkMode) {
                            document.documentElement.classList.add('dark');
                        } else {
                            document.documentElement.classList.remove('dark');
                        }
                    "
                class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors focus:outline-none focus:ring-2 focus:ring-brand-blue focus:ring-offset-2"
                :class="darkMode ? 'bg-brand-blue' : 'bg-gray-200'"
                :title="darkMode ? 'Switch to light mode' : 'Switch to dark mode'">
                <span class="sr-only">Toggle dark mode</span>
                <span
                    class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform duration-200 ease-in-out"
                    :class="darkMode ? 'translate-x-6' : 'translate-x-1'"></span>
            </button>
        </div>

        <!-- Navigation for guests -->
        <nav class="bg-white dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex items-center">
                        <a href="{{ route('login') }}" class="text-xl font-bold text-gray-800 dark:text-gray-200">
                            {{ config('app.name', 'Laravel') }}
                        </a>
                    </div>
                    <div class="flex items-center space-x-4">
                        <a href="{{ route('login') }}" class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100">
                            {{ __('general.login') }}
                        </a>
                        <a href="{{ route('register') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium">
                            {{ __('general.register') }}
                        </a>
                    </div>
                </div>
            </div>
        </nav>

        <div class="w-full max-w-7xl mx-auto mt-6 px-6 py-4">
            {{ $slot }}
        </div>
    </div>
</body>

</html>