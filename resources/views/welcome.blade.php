<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Synco Save - Smart Savings Made Simple</title>
        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />
        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-brand-off-white dark:bg-[#0a0a0a] text-[#1b1b18] flex p-6 lg:p-8 items-center lg:justify-center min-h-screen flex-col"
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
        <header class="w-full lg:max-w-4xl max-w-[335px] text-sm mb-6">
            @if (Route::has('login'))
                <nav class="flex items-center justify-between">
                    <!-- Theme Toggle -->
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

                    <div class="flex items-center gap-4">
                        @auth
                            <a href="{{ url('/dashboard') }}"
                               class="inline-block px-5 py-1.5 dark:text-[#EDEDEC] border-[#19140035] hover:border-[#1915014a] border text-[#1b1b18] dark:border-[#3E3E3A] dark:hover:border-[#62605b] rounded-sm text-sm leading-normal">
                                Dashboard
                            </a>
                        @else
                            <a href="{{ route('login') }}"
                               class="inline-block px-5 py-1.5 dark:text-[#EDEDEC] text-[#1b1b18] border border-transparent hover:border-[#19140035] dark:hover:border-[#3E3E3A] rounded-sm text-sm leading-normal">
                                Log in
                            </a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}"
                                   class="inline-block px-5 py-1.5 dark:text-[#EDEDEC] border-[#19140035] hover:border-[#1915014a] border text-[#1b1b18] dark:border-[#3E3E3A] dark:hover:border-[#62605b] rounded-sm text-sm leading-normal">
                                    Register
                                </a>
                            @endif
                        @endauth
                    </div>
                </nav>
            @endif
        </header>
        
        <div class="flex items-center justify-center w-full transition-opacity opacity-100 duration-750 lg:grow starting:opacity-0">
            <main class="flex max-w-[335px] w-full flex-col-reverse lg:max-w-4xl lg:flex-row">
                <div class="text-[13px] leading-[20px] flex-1 p-6 pb-12 lg:p-20 bg-white dark:bg-[#161615] dark:text-[#EDEDEC] shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)] dark:shadow-[inset_0px_0px_0px_1px_#fffaed2d] rounded-bl-lg rounded-br-lg lg:rounded-tl-lg lg:rounded-br-none">
                    <h1 class="mb-1 font-medium text-2xl text-brand-blue dark:text-brand-off-white">Welcome to Synco Save</h1>
                    <p class="mb-2 text-[#706f6c] dark:text-[#A1A09A]">Smart savings made simple. <br>Start your financial journey with confidence.</p>
                    <ul class="flex flex-col mb-4 lg:mb-6">
                        <li class="flex items-center gap-4 py-2 relative before:border-l before:border-[#e3e3e0] dark:before:border-[#3E3E3A] before:top-1/2 before:bottom-0 before:left-[0.4rem] before:absolute">
                            <span class="relative py-1 bg-white dark:bg-[#161615]">
                                <span class="flex items-center justify-center rounded-full bg-[#FDFDFC] dark:bg-[#161615] shadow-[0px_0px_1px_0px_rgba(0,0,0,0.03),0px_1px_2px_0px_rgba(0,0,0,0.06)] w-3.5 h-3.5 border dark:border-[#3E3E3A] border-[#e3e3e0]">
                                    <span class="rounded-full bg-brand-blue w-1.5 h-1.5"></span>
                                </span>
                            </span>
                            <span>
                                Create your
                                <span class="font-medium text-brand-blue dark:text-brand-orange ml-1">
                                    savings group
                                </span>
                            </span>
                        </li>
                        <li class="flex items-center gap-4 py-2 relative before:border-l before:border-[#e3e3e0] dark:before:border-[#3E3E3A] before:bottom-1/2 before:top-0 before:left-[0.4rem] before:absolute">
                            <span class="relative py-1 bg-white dark:bg-[#161615]">
                                <span class="flex items-center justify-center rounded-full bg-[#FDFDFC] dark:bg-[#161615] shadow-[0px_0px_1px_0px_rgba(0,0,0,0.03),0px_1px_2px_0px_rgba(0,0,0,0.06)] w-3.5 h-3.5 border dark:border-[#3E3E3A] border-[#e3e3e0]">
                                    <span class="rounded-full bg-brand-blue w-1.5 h-1.5"></span>
                                </span>
                            </span>
                            <span>
                                Track your
                                <span class="font-medium text-brand-blue dark:text-brand-orange ml-1">
                                    contributions
                                </span>
                            </span>
                        </li>
                        <li class="flex items-center gap-4 py-2">
                            <span class="relative py-1 bg-white dark:bg-[#161615]">
                                <span class="flex items-center justify-center rounded-full bg-[#FDFDFC] dark:bg-[#161615] shadow-[0px_0px_1px_0px_rgba(0,0,0,0.03),0px_1px_2px_0px_rgba(0,0,0,0.06)] w-3.5 h-3.5 border dark:border-[#3E3E3A] border-[#e3e3e0]">
                                    <span class="rounded-full bg-brand-blue w-1.5 h-1.5"></span>
                                </span>
                            </span>
                            <span>
                                Achieve your
                                <span class="font-medium text-brand-blue dark:text-brand-orange ml-1">
                                    financial goals
                                </span>
                            </span>
                        </li>
                    </ul>
                    <ul class="flex gap-3 text-sm leading-normal">
                        <li>
                            @auth
                                <a href="{{ url('/dashboard') }}" class="inline-block bg-brand-blue hover:bg-brand-blue/90 dark:bg-brand-orange dark:hover:bg-brand-orange/90 px-5 py-1.5 rounded-sm border border-brand-blue dark:border-brand-orange text-white text-sm leading-normal transition-colors">
                                    Go to Dashboard
                                </a>
                            @else
                                <a href="{{ route('register') }}" class="inline-block bg-brand-blue hover:bg-brand-blue/90 dark:bg-brand-orange dark:hover:bg-brand-orange/90 px-5 py-1.5 rounded-sm border border-brand-blue dark:border-brand-orange text-white text-sm leading-normal transition-colors">
                                    Get Started
                                </a>
                            @endauth
                        </li>
                    </ul>
                </div>
                <div class="bg-gradient-to-br from-brand-blue/10 to-brand-orange/10 dark:from-brand-blue/20 dark:to-brand-orange/20 relative lg:-ml-px -mb-px lg:mb-0 rounded-t-lg lg:rounded-t-none lg:rounded-r-lg aspect-[335/376] lg:aspect-auto w-full lg:w-[438px] shrink-0 overflow-hidden flex items-center justify-center">
                    <!-- Synco Save Logo/Icon -->
                    <div class="text-center">
                        <div class="w-24 h-24 mx-auto mb-6 bg-brand-blue dark:bg-brand-orange rounded-2xl flex items-center justify-center shadow-lg">
                            <svg class="w-12 h-12 text-white" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1.41 15.09L7 13.5l1.41-1.41L10 13.67l6.59-6.59L18 8.5l-7.41 8.59z"/>
                            </svg>
                        </div>
                        <h2 class="text-3xl font-bold text-brand-blue dark:text-brand-off-white mb-2">Synco Save</h2>
                        <p class="text-brand-blue/70 dark:text-brand-off-white/70 text-sm">Smart Savings Made Simple</p>
                        
                        <!-- Feature highlights -->
                        <div class="mt-8 space-y-3 text-left">
                            <div class="flex items-center space-x-3 text-sm">
                                <div class="w-2 h-2 bg-brand-blue dark:bg-brand-orange rounded-full"></div>
                                <span class="text-brand-blue/80 dark:text-brand-off-white/80">Group savings made easy</span>
                            </div>
                            <div class="flex items-center space-x-3 text-sm">
                                <div class="w-2 h-2 bg-brand-blue dark:bg-brand-orange rounded-full"></div>
                                <span class="text-brand-blue/80 dark:text-brand-off-white/80">Automated contribution tracking</span>
                            </div>
                            <div class="flex items-center space-x-3 text-sm">
                                <div class="w-2 h-2 bg-brand-blue dark:bg-brand-orange rounded-full"></div>
                                <span class="text-brand-blue/80 dark:text-brand-off-white/80">Secure payment integration</span>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </body>
</html>