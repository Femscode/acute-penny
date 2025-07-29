<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('general.create_group') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl px-4 mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form method="POST" action="{{ route('groups.store') }}" class="space-y-6">
                        @csrf

                        <!-- Group Name -->
                        <div>
                            <x-input-label for="name" :value="__('general.group_name')" />
                            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name')" required autofocus />
                            <x-input-error class="mt-2" :messages="$errors->get('name')" />
                        </div>

                        <!-- Description -->
                        <div>
                            <x-input-label for="description" :value="__('general.description')" />
                            <textarea id="description" name="description" rows="3" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">{{ old('description') }}</textarea>
                            <x-input-error class="mt-2" :messages="$errors->get('description')" />
                        </div>

                        <!-- Contribution Amount -->
                        <div>
                            <x-input-label for="contribution_amount" :value="__('general.contribution_amount')" />
                            <div class="mt-1 relative rounded-md shadow-sm">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500 sm:text-sm">₦</span>
                                </div>
                                <x-text-input id="contribution_amount" name="contribution_amount" type="number" step="0.01" min="1" class="pl-7 block w-full" :value="old('contribution_amount')" required />
                            </div>
                            <x-input-error class="mt-2" :messages="$errors->get('contribution_amount')" />
                        </div>

                        <!-- Frequency -->
                        <div>
                            <x-input-label for="frequency" :value="__('general.frequency')" />
                            <select id="frequency" name="frequency" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" required>
                                <option value="">{{ __('general.select_frequency') }}</option>
                                <option value="daily" {{ old('frequency') === 'daily' ? 'selected' : '' }}>{{ __('general.daily') }}</option>
                                <option value="weekly" {{ old('frequency') === 'weekly' ? 'selected' : '' }}>{{ __('general.weekly') }}</option>
                                <option value="monthly" {{ old('frequency') === 'monthly' ? 'selected' : '' }}>{{ __('general.monthly') }}</option>
                            </select>
                            <x-input-error class="mt-2" :messages="$errors->get('frequency')" />
                        </div>

                        <!-- Turn Format -->
                        <div>
                            <x-input-label for="turn_format" :value="__('general.turn_format')" />
                            <select id="turn_format" name="turn_format" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" required>
                                <option value="">{{ __('general.select_turn_format') }}</option>
                                <option value="linear" {{ old('turn_format') === 'linear' ? 'selected' : '' }}>{{ __('general.linear_turn') }}</option>
                                <option value="random" {{ old('turn_format') === 'random' ? 'selected' : '' }}>{{ __('general.random_turn') }}</option>
                                <option value="manual" {{ old('turn_format') === 'manual' ? 'selected' : '' }}>{{ __('general.manual_turn') }}</option>
                            </select>
                            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">{{ __('general.turn_format_help') }}</p>
                            <x-input-error class="mt-2" :messages="$errors->get('turn_format')" />
                        </div>

                        <!-- Privacy Settings -->
                        <div class="space-y-4">
                            <x-input-label :value="__('general.privacy_settings')" />
                            
                            <!-- Privacy Type -->
                            <div>
                                <label class="inline-flex items-center">
                                    <input type="radio" name="privacy_type" value="public" class="form-radio text-indigo-600" {{ old('privacy_type', 'public') === 'public' ? 'checked' : '' }} required>
                                    <span class="ml-2 text-gray-700 dark:text-gray-300">{{ __('general.public_group') }}</span>
                                </label>
                                <p class="ml-6 text-sm text-gray-600 dark:text-gray-400">{{ __('general.public_group_help') }}</p>
                            </div>
                            
                            <div>
                                <label class="inline-flex items-center">
                                    <input type="radio" name="privacy_type" value="private" class="form-radio text-indigo-600" {{ old('privacy_type') === 'private' ? 'checked' : '' }} required>
                                    <span class="ml-2 text-gray-700 dark:text-gray-300">{{ __('general.private_group') }}</span>
                                </label>
                                <p class="ml-6 text-sm text-gray-600 dark:text-gray-400">{{ __('general.private_group_help') }}</p>
                            </div>
                            
                            <!-- Approval Required (only for private groups) -->
                            <div id="approval-section" class="ml-6" style="display: none;">
                                <label class="inline-flex items-center">
                                    <input type="checkbox" name="requires_approval" value="1" class="form-checkbox text-indigo-600" {{ old('requires_approval') ? 'checked' : '' }}>
                                    <span class="ml-2 text-gray-700 dark:text-gray-300">{{ __('general.requires_approval') }}</span>
                                </label>
                                <p class="ml-6 text-sm text-gray-600 dark:text-gray-400">{{ __('general.requires_approval_help') }}</p>
                            </div>
                        </div>

                        <!-- Max Members -->
                        <div>
                            <x-input-label for="max_members" :value="__('general.max_members')" />
                            <x-text-input id="max_members" name="max_members" type="number" min="2" max="50" class="mt-1 block w-full" :value="old('max_members')" required />
                            <x-input-error class="mt-2" :messages="$errors->get('max_members')" />
                        </div>

                        <!-- Start Date -->
                        <div>
                            <x-input-label for="start_date" :value="__('general.start_date')" />
                            <x-text-input id="start_date" name="start_date" type="date" class="mt-1 block w-full" :value="old('start_date')" required />
                            <x-input-error class="mt-2" :messages="$errors->get('start_date')" />
                        </div>

                        <div class="flex items-center justify-end space-x-4">
                            <a href="{{ route('dashboard') }}" class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100">
                                {{ __('general.cancel') }}
                            </a>
                            <x-primary-button>
                                {{ __('general.create_group') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Show/hide approval section based on privacy type
        document.addEventListener('DOMContentLoaded', function() {
            const privacyRadios = document.querySelectorAll('input[name="privacy_type"]');
            const approvalSection = document.getElementById('approval-section');
            
            function toggleApprovalSection() {
                const selectedPrivacy = document.querySelector('input[name="privacy_type"]:checked')?.value;
                approvalSection.style.display = selectedPrivacy === 'private' ? 'block' : 'none';
            }
            
            privacyRadios.forEach(radio => {
                radio.addEventListener('change', toggleApprovalSection);
            });
            
            // Initial check
            toggleApprovalSection();
        });
    </script>
</x-app-layout>