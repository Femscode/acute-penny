@if($isAuthenticated)
    <x-app-layout>
        <x-slot name="header">
            <div class="flex items-center justify-between">
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    {{ $group->name }}
                </h2>
                <span class="px-3 py-1 text-sm font-medium bg-{{ $group->status === 'open' ? 'green' : 'blue' }}-100 text-{{ $group->status === 'open' ? 'green' : 'blue' }}-800 dark:bg-{{ $group->status === 'open' ? 'green' : 'blue' }}-900 dark:text-{{ $group->status === 'open' ? 'green' : 'blue' }}-200 rounded-full">
                    {{ ucfirst($group->status) }}
                </span>
            </div>
        </x-slot>

        @include('groups.partials.confirmation-modals')

        <div class="py-12">
            <div class="max-w-7xl px-4 mx-auto sm:px-6 lg:px-8 space-y-6">
                <!-- Group Information -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center mb-4 gap-4">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ __('general.group_information') }}</h3>

                            @if($group->created_by === Auth::user()->uuid && $group->canStartContribution())
                                <!-- Linear Turn Order Section -->
                                <div class="mb-6 p-6 bg-gradient-to-br from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 border border-blue-200 dark:border-blue-800 rounded-lg">
                                    <h4 class="text-xl font-bold text-blue-900 dark:text-blue-100 mb-4 text-center">📋 {{ __('general.linear_turn_order') }}</h4>
                                    <p class="text-sm text-blue-700 dark:text-blue-300 mb-6 text-center">{{ __('general.linear_turn_description') }}</p>

                                    <!-- Turn Order Display -->
                                    <div class="space-y-3 mb-6">
                                        @foreach($group->members()->orderBy('joined_at')->get() as $index => $member)
                                            <div class="flex items-center justify-between p-3 bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg">
                                                <div class="flex items-center space-x-3">
                                                    <div class="w-8 h-8 bg-blue-500 text-white rounded-full flex items-center justify-center text-sm font-semibold">
                                                        {{ $index + 1 }}
                                                    </div>
                                                    <div>
                                                        <div class="font-medium text-gray-900 dark:text-gray-100">{{ $member->user->name }}</div>
                                                        <div class="text-sm text-gray-500 dark:text-gray-400">{{ __('general.joined') }} {{ $member->joined_at->format('M d, Y') }}</div>
                                                    </div>
                                                </div>
                                                @if($member->user->uuid === $group->created_by)
                                                    <span class="px-2 py-1 text-xs bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200 rounded">
                                                        {{ __('general.creator') }}
                                                    </span>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>

                                    <!-- Start Contribution Button -->
                                    <div class="text-center">
                                        <form method="POST" action="{{ route('groups.start-contribution', $group->uuid) }}">
                                            @csrf
                                            <button type="submit"
                                                class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-lg transition duration-200"
                                                onclick="return confirm('{{ __('general.confirm_start_contribution') }}')">
                                                {{ __('general.start_contribution') }}
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @endif
                        </div>

                        @include('groups.partials.common-info')
                        @include('groups.partials.action-buttons')
                    </div>
                </div>

                @include('groups.partials.members-list')
            </div>
        </div>
    </x-app-layout>
@else
    <x-guest-layout>
        <div class="mb-6 text-center">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100 mb-2">{{ $group->name }}</h1>
            <span class="inline-block px-3 py-1 text-sm font-medium bg-{{ $group->status === 'open' ? 'green' : 'blue' }}-100 text-{{ $group->status === 'open' ? 'green' : 'blue' }}-800 dark:bg-{{ $group->status === 'open' ? 'green' : 'blue' }}-900 dark:text-{{ $group->status === 'open' ? 'green' : 'blue' }}-200 rounded-full">
                {{ ucfirst($group->status) }}
            </span>
            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">{{ __('general.linear_turn_format') }}</p>
        </div>

        <div class="py-6">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
                <!-- Group Information -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        @include('groups.partials.common-info')
                        @include('groups.partials.action-buttons')
                    </div>
                </div>
            </div>
        </div>
    </x-guest-layout>
@endif
