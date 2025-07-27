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
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                <!-- Group Information -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center mb-4 gap-4">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ __('general.group_information') }}</h3>

                            @if($group->created_by === Auth::user()->uuid && $group->canStartContribution())
                                <!-- Manual Position Assignment Section -->
                                <div class="mb-6 p-6 bg-gradient-to-br from-yellow-50 to-orange-50 dark:from-yellow-900/20 dark:to-orange-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg">
                                    <h4 class="text-xl font-bold text-yellow-900 dark:text-yellow-100 mb-4 text-center">⚙️ {{ __('general.manual_position_assignment') }}</h4>
                                    <p class="text-sm text-yellow-700 dark:text-yellow-300 mb-6 text-center">{{ __('general.drag_to_reorder') }}</p>

                                    <div id="member-positions" class="space-y-2 mb-6">
                                        @foreach($group->members()->orderBy('payout_position')->get() as $member)
                                            <div class="flex items-center justify-between p-3 bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg cursor-move"
                                                data-member-uuid="{{ $member->user_uuid }}" data-position="{{ $member->payout_position ?? 0 }}">
                                                <div class="flex items-center space-x-3 flex-1 min-w-0">
                                                    <div class="w-8 h-8 bg-gray-200 dark:bg-gray-600 rounded-full flex items-center justify-center text-sm font-semibold flex-shrink-0">
                                                        {{ $member->payout_position ?? '?' }}
                                                    </div>
                                                    <div class="min-w-0 flex-1">
                                                        <div class="font-medium text-gray-900 dark:text-gray-100 truncate">{{ $member->user->name }}</div>
                                                        <div class="text-sm text-gray-500 dark:text-gray-400 truncate">{{ __('general.position_number', ['position' => $member->payout_position ?? 'Unassigned']) }}</div>
                                                    </div>
                                                </div>
                                                <div class="text-gray-400 flex-shrink-0 ml-2">
                                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                                        <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z"></path>
                                                    </svg>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>

                                    <div class="flex flex-col sm:flex-row gap-4">
                                        <button type="button" id="save-positions"
                                            class="flex-1 bg-yellow-600 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded-lg transition duration-200">
                                            {{ __('general.save_positions') }}
                                        </button>

                                        <form method="POST" action="{{ route('groups.start-contribution', $group->uuid) }}" class="flex-1">
                                            @csrf
                                            <button type="submit"
                                                class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-lg transition duration-200"
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

        @include('groups.partials.manual-scripts')
    </x-app-layout>
@else
    <x-guest-layout>
        <!-- Guest header -->
        <div class="mb-6 text-center">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100 mb-2">{{ $group->name }}</h1>
            <span class="inline-block px-3 py-1 text-sm font-medium bg-{{ $group->status === 'open' ? 'green' : 'blue' }}-100 text-{{ $group->status === 'open' ? 'green' : 'blue' }}-800 dark:bg-{{ $group->status === 'open' ? 'green' : 'blue' }}-900 dark:text-{{ $group->status === 'open' ? 'green' : 'blue' }}-200 rounded-full">
                {{ ucfirst($group->status) }}
            </span>
            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">{{ __('general.manual_turn_format') }}</p>
        </div>

        <div class="py-6">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
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
