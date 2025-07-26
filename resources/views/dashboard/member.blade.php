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
                    <h3 class="text-2xl font-bold mb-2">{{ __('general.welcome_back') }}, {{ Auth::user()->name }}!</h3>
                    <p class="text-green-100 mb-4">{{ __('general.member_dashboard_subtitle') }}</p>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-center">
                        <div>
                            <div class="text-2xl font-bold">{{ $stats['total_groups'] }}</div>
                            <div class="text-sm opacity-90">{{ __('general.total_groups') }}</div>
                        </div>
                        <div>
                            <div class="text-2xl font-bold">₦{{ number_format($stats['total_paid'], 2) }}</div>
                            <div class="text-sm opacity-90">{{ __('general.total_paid') }}</div>
                        </div>
                        <div>
                            <div class="text-2xl font-bold">₦{{ number_format($stats['pending_amount'], 2) }}</div>
                            <div class="text-sm opacity-90">{{ __('general.pending_amount') }}</div>
                        </div>
                        <div>
                            <div class="text-2xl font-bold">{{ $stats['next_payout'] ? $stats['next_payout']->format('M d') : '--' }}</div>
                            <div class="text-sm opacity-90">{{ __('general.next_payout') }}</div>
                        </div>
                    </div>
                </div>
            </div>

            @if($pendingPayments->count() > 0)
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ __('general.pending_payments') }}</h3>
                        <span class="text-sm text-gray-500 dark:text-gray-400">{{ $pendingPayments->count() }} {{ __('general.payments_due') }}</span>
                    </div>

                    <div class="space-y-3">
                        @foreach($pendingPayments as $payment)
                        <div class="flex items-center justify-between p-4 border border-gray-200 dark:border-gray-700 rounded-lg hover:shadow-md transition-shadow
                            @if($payment->is_overdue) border-red-300 bg-red-50 dark:bg-red-900/20 dark:border-red-700
                            @elseif($payment->is_due_today) border-yellow-300 bg-yellow-50 dark:bg-yellow-900/20 dark:border-yellow-700
                            @else border-blue-300 bg-blue-50 dark:bg-blue-900/20 dark:border-blue-700 @endif">
                            
                            <div class="flex-1">
                                <div class="flex items-center space-x-3">
                                    <div class="p-2 rounded-full 
                                        @if($payment->is_overdue) bg-red-100 dark:bg-red-900
                                        @elseif($payment->is_due_today) bg-yellow-100 dark:bg-yellow-900
                                        @else bg-blue-100 dark:bg-blue-900 @endif">
                                        <svg class="w-4 h-4 
                                            @if($payment->is_overdue) text-red-600 dark:text-red-400
                                            @elseif($payment->is_due_today) text-yellow-600 dark:text-yellow-400
                                            @else text-blue-600 dark:text-blue-400 @endif" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <h4 class="font-medium text-gray-900 dark:text-gray-100">{{ $payment->group->name }}</h4>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">
                                            ₦{{ number_format($payment->amount, 2) }} • 
                                            @if($payment->is_overdue)
                                                <span class="text-red-600 dark:text-red-400 font-medium">{{ __('general.overdue') }} ({{ floor($payment->days_until_due) }} {{ __('general.days_ago') }})</span>
                                            @elseif($payment->is_due_today)
                                                <span class="text-yellow-600 dark:text-yellow-400 font-medium">{{ __('general.due_today') }}</span>
                                            @else
                                                <span class="text-blue-600 dark:text-blue-400">{{ __('general.due_in') }} {{ $payment->days_until_due }} {{ __('general.days') }}</span>
                                            @endif
                                        </p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('general.due_date') }}: {{ $payment->due_date->format('M d, Y') }}</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="flex items-center space-x-2">
                                @if($payment->is_overdue)
                                    <span class="px-2 py-1 text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200 rounded-full">
                                        {{ __('general.overdue') }}
                                    </span>
                                @elseif($payment->is_due_today)
                                    <span class="px-2 py-1 text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200 rounded-full">
                                        {{ __('general.due_today') }}
                                    </span>
                                @endif
                                
                                <button class="px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-md transition duration-150 ease-in-out" 
                                        onclick="window.location.href='{{ route('groups.show', $payment->group->uuid) }}'">
                                    {{ __('general.make_payment') }}
                                </button>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif
            <!-- Quick Stats -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-blue-100 dark:bg-blue-900">
                                <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">{{ __('general.groups_joined') }}</p>
                                <p class="text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ $stats['groups_joined'] }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-green-100 dark:bg-green-900">
                                <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">{{ __('general.groups_created') }}</p>
                                <p class="text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ $stats['groups_created'] }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                @if($stats['overdue_amount'] > 0)
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg border-l-4 border-red-500">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-red-100 dark:bg-red-900">
                                <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-red-600 dark:text-red-400">{{ __('general.overdue') }}</p>
                                <p class="text-2xl font-semibold text-red-900 dark:text-red-100">₦{{ number_format($stats['overdue_amount'], 2) }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                @else
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-purple-100 dark:bg-purple-900">
                                <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">{{ __('general.next_payout') }}</p>
                                <p class="text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ $stats['next_payout'] ? $stats['next_payout']->format('M d, Y') : '--' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            </div>

          

@if($payoutEligibleGroups->count() > 0)
<!-- Payout Eligible Groups -->
<div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
    <div class="p-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4 flex items-center">
           
            {{ __('Payout Available') }}
        </h3>
        
        <div class="space-y-4">
            @foreach($payoutEligibleGroups as $group)
            <div class="p-4 bg-gradient-to-r from-green-50 to-yellow-50 dark:from-green-900/20 dark:to-yellow-900/20 border border-green-200 dark:border-green-800 rounded-lg">
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                    <div class="flex-1">
                        <h4 class="font-semibold text-green-900 dark:text-green-100">{{ $group->name }}</h4>
                        <p class="text-sm text-green-700 dark:text-green-300 mt-1">
                            It's your turn to receive payout! 
                            <span class="font-semibold">₦{{ number_format($group->net_amount, 2) }}</span> 
                            (after 5% service charge)
                        </p>
                        <div class="flex items-center text-xs text-green-600 dark:text-green-400 mt-2">
                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"></path>
                            </svg>
                            {{ ucfirst($group->frequency) }} • {{ $group->current_members }} members
                        </div>
                    </div>
                    <div class="flex flex-col sm:flex-row gap-2">
                        <a href="{{ route('groups.show', $group) }}" 
                            class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white text-sm font-medium rounded-lg transition duration-200 text-center">
                            View Group
                        </a>
                        <a href="{{ route('withdrawal-requests.create', $group) }}" 
                            class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition duration-200 text-center">
                            Request Payout
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endif

<!-- Add this section before the "My Groups" section -->
@if($pendingGroups->count() > 0)
<div class="bg-white rounded-lg shadow-md p-6 mb-6">
    <h2 class="text-xl font-semibold text-gray-900 mb-4">{{ __('general.pending_group_requests') }}</h2>
    <div class="space-y-4">
        @foreach($pendingGroups as $membership)
            <div class="flex items-center justify-between p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                <div>
                    <h3 class="font-medium text-gray-900">{{ $membership->group->name }}</h3>
                    <p class="text-sm text-gray-600">{{ __('general.request_pending_approval') }}</p>
                </div>
                <span class="px-3 py-1 bg-yellow-100 text-yellow-800 text-sm font-medium rounded-full">
                    {{ __('general.pending') }}
                </span>
            </div>
        @endforeach
    </div>
</div>
@endif

            <!-- My Groups Section -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ __('general.my_groups') }}</h3>
                        <a href="{{ route('groups.index') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-md transition duration-150 ease-in-out">
                            {{ __('general.view_all') }}
                        </a>
                    </div>

                    @if($userGroups->count() > 0 || $createdGroups->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($userGroups->take(3) as $group)
                        <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4 hover:shadow-md transition relative">
                            <!-- Open button at top right -->
                  

                            <h4 class="font-semibold text-gray-900 dark:text-gray-100 mb-2 pr-16">{{ $group->name }}</h4>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">₦{{ number_format($group->contribution_amount, 2) }} {{ $group->frequency }}</p>
                            <div class="flex justify-between items-center text-xs text-gray-500">
                                <span>{{ $group->current_members }}/{{ $group->max_members }} {{ __('general.members') }}</span>
                                <!-- View button replaces status badge -->
                                <button class="px-2 py-1 bg-blue-600 hover:bg-blue-700 text-white rounded text-xs font-medium transition duration-150 ease-in-out" onclick="window.location.href='{{ route('groups.show', $group->uuid) }}'">
                                    {{ __('general.view_group') }}
                                </button>
                            </div>
                        </div>
                        @endforeach

                        @foreach($createdGroups->take(3 - $userGroups->count()) as $group)
                        <div class="border border-blue-200 dark:border-blue-700 rounded-lg p-4 hover:shadow-md transition bg-blue-50 dark:bg-blue-900/20">
                            <h4 class="font-semibold text-gray-900 dark:text-gray-100 mb-2">{{ $group->name }} <span class="text-xs text-blue-600">({{ __('general.creator') }})</span></h4>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">₦{{ number_format($group->contribution_amount, 2) }} {{ $group->frequency }}</p>
                            <div class="flex justify-between items-center text-xs text-gray-500">
                                <span>{{ $group->current_members }}/{{ $group->max_members }} {{ __('general.members') }}</span>
                                <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded-full">{{ ucfirst($group->status) }}</span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @endif
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">{{ __('general.recent_activity') }}</h3>

                    @if($recentActivity['contributions']->count() > 0 || $recentActivity['group_joins']->count() > 0)
                    <div class="space-y-4">
                        @foreach($recentActivity['contributions'] as $contribution)
                        <div class="flex items-center space-x-3 p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <div class="p-2 bg-green-100 dark:bg-green-900 rounded-full">
                                <svg class="w-4 h-4 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ __('general.contribution_made') }}</p>
                                <p class="text-xs text-gray-500">₦{{ number_format($contribution->amount, 2) }} to {{ $contribution->group->name }} • {{ $contribution->created_at->diffForHumans() }}</p>
                            </div>
                            <span class="px-2 py-1 text-xs rounded-full {{ $contribution->status === 'paid' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">{{ ucfirst($contribution->status) }}</span>
                        </div>
                        @endforeach

                        @foreach($recentActivity['group_joins'] as $join)
                        <div class="flex items-center space-x-3 p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <div class="p-2 bg-blue-100 dark:bg-blue-900 rounded-full">
                                <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ __('general.joined_group') }}</p>
                                <p class="text-xs text-gray-500">{{ $join->group->name }} • {{ $join->joined_at->diffForHumans() }}</p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div class="text-center py-8">
                        <svg class="mx-auto w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                        <p class="text-gray-500 dark:text-gray-400 mt-2">{{ __('general.no_activity_message') }}</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>