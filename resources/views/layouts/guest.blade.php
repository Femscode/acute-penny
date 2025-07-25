<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

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
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100 dark:bg-gray-900">
            <!-- Theme Toggle for Guest Pages -->
            <div class="absolute top-4 right-4">
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

            <div class="w-full sm:max-w-md md:max-w-lg lg:max-w-xl xl:max-w-4xl mt-6 px-6 py-4 bg-white dark:bg-gray-800 shadow-md overflow-hidden sm:rounded-lg">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>