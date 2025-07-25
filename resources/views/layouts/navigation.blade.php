<nav x-data="{ 
    open: false,
    darkMode: localStorage.getItem('theme') === 'dark' || (!localStorage.getItem('theme') && window.matchMedia('(prefers-color-scheme: dark)').matches),
    toggleTheme() {
        this.darkMode = !this.darkMode;
        localStorage.setItem('theme', this.darkMode ? 'dark' : 'light');
        if (this.darkMode) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    }
}" 
x-init="
    if (darkMode) {
        document.documentElement.classList.add('dark');
    } else {
        document.documentElement.classList.remove('dark');
    }
" 
class="bg-white dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}">
                        <x-nav-logo class="block h-9 w-auto fill-current text-gray-800 dark:text-gray-200" />
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        {{ __('general.dashboard') }}
                    </x-nav-link>
                </div>
            </div>

            <!-- Right Side Navigation -->
            <div class="hidden sm:flex sm:items-center sm:space-x-4">
                <!-- Theme Toggle -->
                               <!-- Theme Toggle -->
                <div class="flex items-center">
                    <button
                        @click="toggleTheme()"
                        class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors focus:outline-none focus:ring-2 focus:ring-brand-blue focus:ring-offset-2 focus:ring-offset-white dark:focus:ring-offset-gray-800"
                        :class="darkMode ? 'bg-brand-blue' : 'bg-gray-200'"
                        :title="darkMode ? '{{ __('general.light_mode') }}' : '{{ __('general.dark_mode') }}'">
                        <span class="sr-only">Toggle dark mode</span>
                        <span
                            class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform duration-200 ease-in-out"
                            :class="darkMode ? 'translate-x-6' : 'translate-x-1'"></span>
                    </button>
                </div>

                <!-- Language Selection -->
                <div class="relative">
                    <x-dropdown align="right" width="40">
                        <x-slot name="trigger">
                            <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none transition ease-in-out duration-150">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129"></path>
                                </svg>
                                <span>
                                    @switch(app()->getLocale())
                                    @case('yoruba')
                                    YO
                                    @break
                                    @case('igbo')
                                    IG
                                    @break
                                    @case('hausa')
                                    HA
                                    @break
                                    @default
                                    EN
                                    @endswitch
                                </span>
                                <svg class="ml-1 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <x-dropdown-link :href="route('language.change', 'en')">
                                <div class="flex items-center">
                                    <span class="mr-2">🇬🇧</span>
                                    {{ __('general.english') }}
                                </div>
                            </x-dropdown-link>
                            <x-dropdown-link :href="route('language.change', 'yoruba')">
                                <div class="flex items-center">
                                    <span class="mr-2">🇳🇬</span>
                                    {{ __('general.yoruba') }}
                                </div>
                            </x-dropdown-link>
                            <x-dropdown-link :href="route('language.change', 'igbo')">
                                <div class="flex items-center">
                                    <span class="mr-2">🇳🇬</span>
                                    {{ __('general.igbo') }}
                                </div>
                            </x-dropdown-link>
                            <x-dropdown-link :href="route('language.change', 'hausa')">
                                <div class="flex items-center">
                                    <span class="mr-2">🇳🇬</span>
                                    {{ __('general.hausa') }}
                                </div>
                            </x-dropdown-link>
                        </x-slot>
                    </x-dropdown>
                </div>

                <!-- User Settings Dropdown -->
                <div class="relative">
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none transition ease-in-out duration-150">
                                <div>{{ Auth::user()->name }}</div>

                                <div class="ms-1">
                                    <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <x-dropdown-link :href="route('profile.edit')">
                                {{ __('general.profile') }}
                            </x-dropdown-link>

                            <!-- Authentication -->
                            <form method="POST" action="{{ route('logout') }}" class="border-t border-gray-100 dark:border-gray-600">
                                @csrf

                                <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                    this.closest('form').submit();">
                                    {{ __('general.logout') }}
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                </div>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 dark:text-gray-500 hover:text-gray-500 dark:hover:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-900 focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-900 focus:text-gray-500 dark:focus:text-gray-400 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('general.dashboard') }}
            </x-responsive-nav-link>
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200 dark:border-gray-600">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800 dark:text-gray-200">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('general.profile') }}
                </x-responsive-nav-link>

                <!-- Mobile Theme Toggle -->
                <div class="px-4 py-3 border-t border-gray-200 dark:border-gray-600">
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('general.theme') }}</span>

                        <button
                            @click="toggleTheme()"
                            class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                            :class="darkMode ? 'bg-indigo-600' : 'bg-gray-200'">
                            <span class="sr-only">Toggle dark mode</span>
                            <span
                                class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform duration-200 ease-in-out"
                                :class="darkMode ? 'translate-x-6' : 'translate-x-1'"></span>
                        </button>
                    </div>
                    <div class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                        <span x-show="!darkMode">{{ __('general.light_mode') }}</span>
                        <span x-show="darkMode">{{ __('general.dark_mode') }}</span>
                    </div>
                </div>

                <!-- Mobile Language Selection -->
                <div class="px-4 py-3 border-t border-gray-200 dark:border-gray-600">
                    <div class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('general.language') }}</div>
                    <div class="space-y-1">
                        <a href="{{ route('language.change', 'en') }}" class="flex items-center w-full px-2 py-1 text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded">
                            <span class="mr-2">🇬🇧</span>
                            {{ __('general.english') }}
                        </a>
                        <a href="{{ route('language.change', 'yoruba') }}" class="flex items-center w-full px-2 py-1 text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded">
                            <span class="mr-2">🇳🇬</span>
                            {{ __('general.yoruba') }}
                        </a>
                        <a href="{{ route('language.change', 'igbo') }}" class="flex items-center w-full px-2 py-1 text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded">
                            <span class="mr-2">🇳🇬</span>
                            {{ __('general.igbo') }}
                        </a>
                        <a href="{{ route('language.change', 'hausa') }}" class="flex items-center w-full px-2 py-1 text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded">
                            <span class="mr-2">🇳🇬</span>
                            {{ __('general.hausa') }}
                        </a>
                    </div>
                </div>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')"
                        onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('general.logout') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>