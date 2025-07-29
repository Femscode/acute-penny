<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('general.group_settings') }}
            </h2>
            <a href="{{ route('groups.show', $group->uuid) }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                {{ __('general.back_to_group') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl px-4 mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    @if(session('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                            {{ session('error') }}
                        </div>
                    @endif

                    <!-- Pending Members Section -->
                    @if($group->requires_approval && $pendingMembers->count() > 0)
                        <div class="mb-8">
                            <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-4">{{ __('general.pending_members') }}</h2>
                            <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-4">
                                @foreach($pendingMembers as $member)
                                    <div class="flex items-center justify-between py-3 border-b border-yellow-200 dark:border-yellow-700 last:border-b-0">
                                        <div class="flex items-center">
                                            <img src="{{ $member->user->profile_image_url }}" alt="{{ $member->user->name }}" class="w-10 h-10 rounded-full mr-3">
                                            <div>
                                                <p class="font-medium text-gray-900 dark:text-gray-100">{{ $member->user->name }}</p>
                                                <p class="text-sm text-gray-600 dark:text-gray-400">{{ $member->user->email }}</p>
                                            </div>
                                        </div>
                                        <div class="flex space-x-2">
                                            <form action="{{ route('groups.approve-member', $group->uuid) }}" method="POST" class="inline">
                                                @csrf
                                                <input type="hidden" name="member_uuid" value="{{ $member->user->uuid }}">
                                                <button type="submit" class="bg-green-500 hover:bg-green-700 text-white font-bold py-1 px-3 rounded text-sm">
                                                    {{ __('general.approve') }}
                                                </button>
                                            </form>
                                            <form action="{{ route('groups.reject-member', $group->uuid) }}" method="POST" class="inline">
                                                @csrf
                                                <input type="hidden" name="member_uuid" value="{{ $member->user->uuid }}">
                                                <button type="submit" class="bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-3 rounded text-sm">
                                                    {{ __('general.reject') }}
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Group Settings Form -->
                    <div>
                        <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-4">{{ __('general.edit_group_details') }}</h2>
                        <form action="{{ route('groups.update', $group->uuid) }}" method="POST">
                            @csrf
                            @method('PATCH')

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('general.group_name') }}</label>
                                    <input type="text" id="name" name="name" value="{{ old('name', $group->name) }}" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-gray-100" required>
                                    @error('name')
                                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="contribution_amount" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('general.contribution_amount') }}</label>
                                    <input type="number" id="contribution_amount" name="contribution_amount" value="{{ old('contribution_amount', $group->contribution_amount) }}" min="100" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-gray-100" required>
                                    @error('contribution_amount')
                                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="frequency" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('general.frequency') }}</label>
                                    <select id="frequency" name="frequency" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-gray-100" required>
                                        <option value="daily" {{ old('frequency', $group->frequency) === 'daily' ? 'selected' : '' }}>{{ __('general.daily') }}</option>
                                        <option value="weekly" {{ old('frequency', $group->frequency) === 'weekly' ? 'selected' : '' }}>{{ __('general.weekly') }}</option>
                                        <option value="monthly" {{ old('frequency', $group->frequency) === 'monthly' ? 'selected' : '' }}>{{ __('general.monthly') }}</option>
                                    </select>
                                    @error('frequency')
                                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="turn_format" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('general.turn_format') }}</label>
                                    <select id="turn_format" name="turn_format" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-gray-100" required>
                                        <option value="random" {{ old('turn_format', $group->turn_format) === 'random' ? 'selected' : '' }}>{{ __('general.random') }}</option>
                                        <option value="linear" {{ old('turn_format', $group->turn_format) === 'linear' ? 'selected' : '' }}>{{ __('general.linear') }}</option>
                                        <option value="manual" {{ old('turn_format', $group->turn_format) === 'manual' ? 'selected' : '' }}>{{ __('general.manual') }}</option>
                                    </select>
                                    @error('turn_format')
                                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="privacy_type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('general.privacy_type') }}</label>
                                    <select id="privacy_type" name="privacy_type" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-gray-100" required>
                                        <option value="public" {{ old('privacy_type', $group->privacy_type) === 'public' ? 'selected' : '' }}>{{ __('general.public') }}</option>
                                        <option value="private" {{ old('privacy_type', $group->privacy_type) === 'private' ? 'selected' : '' }}>{{ __('general.private') }}</option>
                                    </select>
                                    @error('privacy_type')
                                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="max_members" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('general.max_members') }}</label>
                                    <input type="number" id="max_members" name="max_members" value="{{ old('max_members', $group->max_members) }}" min="2" max="50" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-gray-100" required>
                                    @error('max_members')
                                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="md:col-span-2">
                                    <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('general.description') }}</label>
                                    <textarea id="description" name="description" rows="4" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-gray-100" required>{{ old('description', $group->description) }}</textarea>
                                    @error('description')
                                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="md:col-span-2">
                                    <label for="start_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('general.start_date') }}</label>
                                    <input type="date" id="start_date" name="start_date" value="{{ old('start_date', $group->start_date?->format('Y-m-d')) }}" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-gray-100" required>
                                    @error('start_date')
                                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="md:col-span-2">
                                    <div class="flex items-center">
                                        <input type="checkbox" id="requires_approval" name="requires_approval" value="1" {{ old('requires_approval', $group->requires_approval) ? 'checked' : '' }} class="mr-2">
                                        <label for="requires_approval" class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('general.requires_approval') }}</label>
                                    </div>
                                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">{{ __('general.requires_approval_help') }}</p>
                                </div>
                            </div>

                            <div class="mt-6">
                                <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                    {{ __('general.update_group') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>