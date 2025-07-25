<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('general.browse_groups') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if($groups->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($groups as $group)
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg hover:shadow-lg transition-shadow">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                                {{ $group->name }}
                            </h3>
                            <span class="px-2 py-1 text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200 rounded-full">
                                {{ ucfirst($group->status) }}
                            </span>
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
                                {{ __('general.view_details') }}
                            </a>
                            @if(!$group->isFull())
                            <div x-data="{ groupId: {{ $group->id }} }">
                                <form method="POST" action="{{ route('groups.join', $group) }}" class="inline" id="join-group-browse-{{ $group->id }}">
                                    @csrf
                                    <button
                                        type="button"
                                        class="inline-flex items-center px-3 py-1 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white focus:bg-gray-700 dark:focus:bg-white active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150"
                                        x-on:click="$dispatch('open-modal', 'join-group-browse-{{ $group->id }}')">
                                        {{ __('general.join_group') }}
                                    </button>
                                </form>
                            </div>
                            @else
                            <span class="text-xs text-gray-500 dark:text-gray-400">{{ __('general.group_full') }}</span>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <div class="mt-8">
                {{ $groups->links() }}
            </div>

            <!-- Confirmation Modals -->
            @foreach($groups as $group)
            @if(!$group->isFull())
            <x-confirmation-modal
                name="join-group-browse-{{ $group->id }}"
                :title="__('general.confirm_join_group')"
                :message="__('general.join_group_warning', ['amount' => number_format($group->contribution_amount, 2), 'frequency' => $group->frequency])"
                :confirm-text="__('general.join_group')"
                :cancel-text="__('general.cancel')"
                confirm-class="bg-green-600 hover:bg-green-700" />
            @endif
            @endforeach
            @else
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-center">
                    <svg class="mx-auto w-12 h-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">{{ __('general.no_groups_available') }}</h3>
                    <p class="text-gray-600 dark:text-gray-400 mb-4">{{ __('general.no_groups_available_desc') }}</p>
                    <a href="{{ route('groups.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 transition">
                        {{ __('general.create_first_group') }}
                    </a>
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- JavaScript for handling modal confirmations -->
    <script>
        document.addEventListener('alpine:init', () => {

            // Listen for confirm events globally
            window.addEventListener('confirm-action', (event) => {

                const modalName = event.detail;
                if (modalName && modalName.startsWith('join-group-browse-')) {
                    const groupId = modalName.replace('join-group-browse-', '');
                    const form = document.getElementById('join-form-browse-' + groupId);
                    if (form) {

                        form.submit();
                    } else {
                        console.error('Form not found for group ID:', groupId);
                    }
                }
            });
        });
    </script>
</x-app-layout>