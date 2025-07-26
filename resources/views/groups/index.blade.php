<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('general.my_groups') }}
            </h2>
            <div class="flex space-x-3">
                <a href="{{ route('groups.browse') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition">
                    {{ __('general.browse_groups') }}
                </a>
                <a href="{{ route('groups.create') }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition">
                    {{ __('general.create_group') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

            <!-- Groups I've Joined -->
            <div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                    {{ __('general.groups_joined') }}
                </h3>

                @if($userGroups->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($userGroups as $group)
                    @php
                     $userContributions = $group->contributions->where('user_uuid', Auth::user()->uuid);

                    $totalPaid = $userContributions->where('status', 'paid')->sum('amount');
                    $pendingAmount = $userContributions->where('status', 'pending')->sum('amount');
                    $overdueCount = $userContributions->where('status', 'pending')->filter(function($c) { return $c->due_date->isPast(); })->count();
                    @endphp
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg hover:shadow-lg transition-shadow">
                        <div class="p-6">
                            <div class="flex items-center justify-between mb-4">
                                <h4 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                                    {{ $group->name }}
                                </h4>
                                <div class="flex items-center space-x-2">
                                    @if($overdueCount > 0)
                                    <span class="px-2 py-1 text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200 rounded-full">
                                        {{ $overdueCount }} {{ __('general.overdue') }}
                                    </span>
                                    @endif
                                    <span class="px-2 py-1 text-xs font-medium rounded-full
                                                @if($group->status === 'active') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                                @elseif($group->status === 'open') bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200
                                                @else bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200
                                                @endif">
                                        {{ ucfirst($group->status) }}
                                    </span>
                                </div>
                            </div>

                            @if($group->description)
                            <p class="text-gray-600 dark:text-gray-400 text-sm mb-4 line-clamp-2">
                                {{ $group->description }}
                            </p>
                            @endif

                            <div class="space-y-2 mb-4">
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600 dark:text-gray-400">{{ __('general.contribution_amount') }}:</span>
                                    <span class="font-medium text-gray-900 dark:text-gray-100">₦{{ number_format($group->contribution_amount, 2) }}</span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600 dark:text-gray-400">{{ __('general.my_total_paid') }}:</span>
                                    <span class="font-medium text-green-600 dark:text-green-400">₦{{ number_format($totalPaid, 2) }}</span>
                                </div>
                                @if($pendingAmount > 0)
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600 dark:text-gray-400">{{ __('general.pending_amount') }}:</span>
                                    <span class="font-medium text-yellow-600 dark:text-yellow-400">₦{{ number_format($pendingAmount, 2) }}</span>
                                </div>
                                @endif
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600 dark:text-gray-400">{{ __('general.frequency') }}:</span>
                                    <span class="font-medium text-gray-900 dark:text-gray-100">{{ ucfirst($group->frequency) }}</span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600 dark:text-gray-400">{{ __('general.members') }}:</span>
                                    <span class="font-medium text-gray-900 dark:text-gray-100">{{ $group->current_members }}/{{ $group->max_members }}</span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600 dark:text-gray-400">{{ __('general.created_by') }}:</span>
                                    <span class="font-medium text-gray-900 dark:text-gray-100">{{ $group->creator->name }}</span>
                                </div>
                            </div>

                            <div x-data="{ groupId: {{ $group->id }} }">
                                @if($group->created_by !== Auth::id() && $group->current_members > 1)
                                <x-confirmation-modal
                                    name="leave-group-{{ $group->id }}"
                                    :title="__('general.confirm_leave_group')"
                                    :message="__('general.leave_group_warning')"
                                    :confirm-text="__('general.leave_group')"
                                    :cancel-text="__('general.cancel')"
                                    confirm-class="bg-red-600 hover:bg-red-700" />

                                <div class="flex items-center justify-between">
                                    <a href="{{ route('groups.show', $group) }}" class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-200 text-sm font-medium">
                                        {{ __('general.view_details') }}
                                    </a>
                                    <form method="POST" action="{{ route('groups.leave', $group) }}" id="leave-group-{{ $group->id }}">
                                        @csrf
                                        @method('DELETE')
                                        <button
                                            type="button"
                                            class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-500 active:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150"
                                            @click="$dispatch('open-modal', 'leave-group-' + groupId)">
                                            {{ __('general.leave_group') }}
                                        </button>
                                    </form>
                                </div>
                                @else
                                <div class="flex items-center justify-between">
                                    <a href="{{ route('groups.show', $group) }}" class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-200 text-sm font-medium">
                                        {{ __('general.view_details') }}
                                    </a>
                                    <!-- <span class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ __('general.cannot_leave_as_creator_or_last_member') }}
                                    </span> -->
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-center">
                        <svg class="mx-auto w-12 h-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                        <h4 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">{{ __('general.no_groups_joined') }}</h4>
                        <p class="text-gray-600 dark:text-gray-400 mb-4">{{ __('general.no_groups_joined_desc') }}</p>
                        <a href="{{ route('groups.browse') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 transition">
                            {{ __('general.browse_groups') }}
                        </a>
                    </div>
                </div>
                @endif
            </div>

            <!-- Groups I've Created -->
            <div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                    {{ __('general.groups_created') }}
                </h3>

                @if($createdGroups->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($createdGroups as $group)
                    @php
                    $totalCollected = $group->total_contributions;
                    $pendingTotal = $group->pending_contributions_amount;
                    $overdueContributions = $group->contributions->filter(function($c) { return $c->status === 'pending' && $c->due_date->isPast(); });
                    @endphp
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg hover:shadow-lg transition-shadow border-l-4 border-green-500">
                        <div class="p-6">
                            <div class="flex items-center justify-between mb-4">
                                <h4 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                                    {{ $group->name }}
                                </h4>
                                <div class="flex items-center space-x-2">
                                    <span class="px-2 py-1 text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200 rounded-full">
                                        {{ __('general.creator') }}
                                    </span>
                                    @if($overdueContributions->count() > 0)
                                    <span class="px-2 py-1 text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200 rounded-full">
                                        {{ $overdueContributions->count() }} {{ __('general.overdue') }}
                                    </span>
                                    @endif
                                    <span class="px-2 py-1 text-xs font-medium rounded-full
                                                @if($group->status === 'active') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                                @elseif($group->status === 'open') bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200
                                                @else bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200
                                                @endif">
                                        {{ ucfirst($group->status) }}
                                    </span>
                                </div>
                            </div>

                            @if($group->description)
                            <p class="text-gray-600 dark:text-gray-400 text-sm mb-4 line-clamp-2">
                                {{ $group->description }}
                            </p>
                            @endif

                            <div class="space-y-2 mb-4">
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600 dark:text-gray-400">{{ __('general.contribution_amount') }}:</span>
                                    <span class="font-medium text-gray-900 dark:text-gray-100">₦{{ number_format($group->contribution_amount, 2) }}</span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600 dark:text-gray-400">{{ __('general.total_collected') }}:</span>
                                    <span class="font-medium text-green-600 dark:text-green-400">₦{{ number_format($totalCollected, 2) }}</span>
                                </div>
                                @if($pendingTotal > 0)
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600 dark:text-gray-400">{{ __('general.pending_total') }}:</span>
                                    <span class="font-medium text-yellow-600 dark:text-yellow-400">₦{{ number_format($pendingTotal, 2) }}</span>
                                </div>
                                @endif
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600 dark:text-gray-400">{{ __('general.frequency') }}:</span>
                                    <span class="font-medium text-gray-900 dark:text-gray-100">{{ ucfirst($group->frequency) }}</span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600 dark:text-gray-400">{{ __('general.members') }}:</span>
                                    <span class="font-medium text-gray-900 dark:text-gray-100">{{ $group->current_members }}/{{ $group->max_members }}</span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600 dark:text-gray-400">{{ __('general.start_date') }}:</span>
                                    <span class="font-medium text-gray-900 dark:text-gray-100">{{ $group->start_date->format('M d, Y') }}</span>
                                </div>
                            </div>

                            <div class="flex items-center justify-between">
                                <a href="{{ route('groups.show', $group) }}" class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-200 text-sm font-medium">
                                    {{ __('general.manage_group') }}
                                </a>
                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                    @if($group->current_members === 1)
                                    {{ __('general.no_members_yet') }}
                                    @else
                                    {{ $group->current_members - 1 }} {{ __('general.other_members') }}
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-center">
                        <svg class="mx-auto w-12 h-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        <h4 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">{{ __('general.no_groups_created') }}</h4>
                        <p class="text-gray-600 dark:text-gray-400 mb-4">{{ __('general.no_groups_created_desc') }}</p>
                        <a href="{{ route('groups.create') }}" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 transition">
                            {{ __('general.create_first_group') }}
                        </a>
                    </div>
                </div>
                @endif
            </div>

            <!-- Quick Actions -->
            @if($userGroups->count() === 0 && $createdGroups->count() === 0)
            <div class="bg-gradient-to-r from-blue-500 to-purple-600 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-white text-center">
                    <h3 class="text-xl font-bold mb-2">{{ __('general.welcome_to_groups') }}</h3>
                    <p class="text-blue-100 mb-4">{{ __('general.welcome_to_groups_desc') }}</p>
                    <div class="flex flex-wrap justify-center gap-3">
                        <a href="{{ route('groups.create') }}" class="bg-white text-blue-600 px-6 py-2 rounded-lg font-semibold hover:bg-blue-50 transition">
                            {{ __('general.create_your_first_group') }}
                        </a>
                        <a href="{{ route('groups.browse') }}" class="bg-blue-400 text-white px-6 py-2 rounded-lg font-semibold hover:bg-blue-300 transition">
                            {{ __('general.explore_existing_groups') }}
                        </a>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</x-app-layout>