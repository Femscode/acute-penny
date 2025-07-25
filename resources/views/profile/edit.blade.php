<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Profile') }}
        </h2>
    </x-slot>

    <div class="py-6 sm:py-12">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4 sm:space-y-6">
            <!-- Profile Information -->
            <div class="p-4 sm:p-6 lg:p-8 bg-white dark:bg-gray-800 shadow-sm sm:shadow sm:rounded-lg border border-gray-200 dark:border-gray-700">
                <div class="max-w-2xl mx-auto">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <!-- Bank Details -->
            <div class="p-4 sm:p-6 lg:p-8 bg-white dark:bg-gray-800 shadow-sm sm:shadow sm:rounded-lg border border-gray-200 dark:border-gray-700">
                <div class="max-w-2xl mx-auto">
                    @include('profile.partials.update-bank-details-form')
                </div>
            </div>

            <!-- Update Password -->
            <div class="p-4 sm:p-6 lg:p-8 bg-white dark:bg-gray-800 shadow-sm sm:shadow sm:rounded-lg border border-gray-200 dark:border-gray-700">
                <div class="max-w-2xl mx-auto">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <!-- Delete Account -->
            <div class="p-4 sm:p-6 lg:p-8 bg-white dark:bg-gray-800 shadow-sm sm:shadow sm:rounded-lg border border-gray-200 dark:border-gray-700">
                <div class="max-w-2xl mx-auto">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
</x-app-layout>