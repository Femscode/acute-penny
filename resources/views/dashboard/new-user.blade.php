<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('general.dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            <!-- Welcome Section -->
            <div class="bg-gradient-to-r from-brand-blue to-brand-orange overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-white">
                    <h3 class="text-2xl font-bold mb-2">{{ __('general.welcome_title') }}, {{ Auth::user()->name }}!</h3>
                    <p class="text-brand-off-white mb-4">{{ __('general.welcome_subtitle') }}</p>
                    <div class="flex flex-wrap gap-3">
                        <a href="{{ route('groups.create') }}" class="bg-brand-white text-brand-blue px-4 py-2 rounded-lg font-semibold hover:bg-brand-off-white transition">
                            {{ __('general.create_group') }}
                        </a>
                        <a href="{{ route('groups.browse') }}" class="bg-brand-blue/80 text-white px-4 py-2 rounded-lg font-semibold hover:bg-brand-blue/70 transition">
                            {{ __('general.join_group') }}
                        </a>
                    </div>
                </div>
            </div>

            <!-- Quick Stats (Empty State) -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-brand-blue/10 dark:bg-brand-blue/20">
                                <svg class="w-6 h-6 text-brand-blue" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">{{ __('general.groups_joined') }}</p>
                                <p class="text-2xl font-semibold text-gray-900 dark:text-gray-100">0</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-brand-orange/10 dark:bg-brand-orange/20">
                                <svg class="w-6 h-6 text-brand-orange" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">{{ __('general.total_saved') }}</p>
                                <p class="text-2xl font-semibold text-gray-900 dark:text-gray-100">₦0.00</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-gray-100 dark:bg-gray-700">
                                <svg class="w-6 h-6 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">{{ __('general.next_payout') }}</p>
                                <p class="text-2xl font-semibold text-gray-900 dark:text-gray-100">--</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Getting Started Section -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-6">{{ __('general.getting_started') }}</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="text-center p-6 border border-gray-200 dark:border-gray-700 rounded-lg hover:shadow-md transition">
                            <div class="mx-auto w-16 h-16 bg-brand-blue/10 dark:bg-brand-blue/20 rounded-full flex items-center justify-center mb-4">
                                <svg class="w-8 h-8 text-brand-blue" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                            </div>
                            <h4 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">{{ __('general.create_first_group_title') }}</h4>
                            <p class="text-gray-600 dark:text-gray-400 mb-4">{{ __('general.create_first_group_desc') }}</p>
                            <a href="{{ route('groups.create') }}" class="inline-flex items-center px-4 py-2 bg-brand-blue border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-brand-blue/90 transition">
                                {{ __('general.create_group') }}
                            </a>
                        </div>
                        <div class="text-center p-6 border border-gray-200 dark:border-gray-700 rounded-lg hover:shadow-md transition">
                            <div class="mx-auto w-16 h-16 bg-brand-orange/10 dark:bg-brand-orange/20 rounded-full flex items-center justify-center mb-4">
                                <svg class="w-8 h-8 text-brand-orange" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </div>
                            <h4 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">{{ __('general.browse_join_groups_title') }}</h4>
                            <p class="text-gray-600 dark:text-gray-400 mb-4">{{ __('general.browse_join_groups_desc') }}</p>
                            <a href="{{ route('groups.browse') }}" class="inline-flex items-center px-4 py-2 bg-brand-orange border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-brand-orange/90 transition">
                                {{ __('general.browse_groups') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Suggested Groups -->
            @if($suggestedGroups->count() > 0)
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ __('general.suggested_groups') }}</h3>
                        <a href="{{ route('groups.browse') }}" class="text-brand-blue hover:text-brand-blue/80 text-sm font-medium">{{ __('general.view_all') }}</a>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($suggestedGroups as $group)
                            <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4 hover:shadow-md transition">
                                <h4 class="font-semibold text-gray-900 dark:text-gray-100 mb-2">{{ $group->name }}</h4>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">{{ Str::limit($group->description, 60) }}</p>
                                <p class="text-sm font-medium text-brand-blue mb-2">₦{{ number_format($group->contribution_amount, 2) }} {{ $group->frequency }}</p>
                                <div class="flex justify-between items-center text-xs text-gray-500 mb-3">
                                    <span>{{ $group->current_members }}/{{ $group->max_members }} {{ __('general.members') }}</span>
                                    <span>{{ __('general.by') }} {{ $group->creator->name }}</span>
                                </div>
                                <a href="{{ route('groups.show', $group->uuid) }}" class="w-full inline-flex justify-center items-center px-3 py-2 bg-brand-blue border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-brand-blue/90 transition">
                                    {{ __('general.view_group') }}
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            <!-- How It Works -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-6">{{ __('general.how_it_works') }}</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="text-center">
                            <div class="mx-auto w-16 h-16 bg-brand-blue/10 dark:bg-brand-blue/20 rounded-full flex items-center justify-center mb-4">
                                <span class="text-2xl font-bold text-brand-blue">1</span>
                            </div>
                            <h4 class="font-semibold text-gray-900 dark:text-gray-100 mb-2">{{ __('general.step_1_title') }}</h4>
                            <p class="text-gray-600 dark:text-gray-400 text-sm">{{ __('general.step_1_desc') }}</p>
                        </div>
                        <div class="text-center">
                            <div class="mx-auto w-16 h-16 bg-brand-orange/10 dark:bg-brand-orange/20 rounded-full flex items-center justify-center mb-4">
                                <span class="text-2xl font-bold text-brand-orange">2</span>
                            </div>
                            <h4 class="font-semibold text-gray-900 dark:text-gray-100 mb-2">{{ __('general.step_2_title') }}</h4>
                            <p class="text-gray-600 dark:text-gray-400 text-sm">{{ __('general.step_2_desc') }}</p>
                        </div>
                        <div class="text-center">
                            <div class="mx-auto w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mb-4">
                                <span class="text-2xl font-bold text-gray-600 dark:text-gray-400">3</span>
                            </div>
                            <h4 class="font-semibold text-gray-900 dark:text-gray-100 mb-2">{{ __('general.step_3_title') }}</h4>
                            <p class="text-gray-600 dark:text-gray-400 text-sm">{{ __('general.step_3_desc') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>