<section>
    <header>
          <div id="alert-container" class="fixed top-4 right-4 z-50 max-w-sm transition-all duration-300 ease-in-out">
            <div id="alert-box" class="w-full bg-white dark:bg-gray-800 shadow-2xl rounded-lg pointer-events-auto ring-1 ring-gray-200 dark:ring-gray-700 overflow-hidden opacity-0 transform translate-x-full">
                <div class="p-4">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <svg id="alert-icon-success" class="h-6 w-6 text-green-500 hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <svg id="alert-icon-error" class="h-6 w-6 text-red-500 hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="ml-3 w-0 flex-1 pt-0.5">
                            <p id="alert-title" class="text-sm font-medium text-gray-900 dark:text-gray-100"></p>
                            <p id="alert-message" class="mt-1 text-sm text-gray-500 dark:text-gray-400"></p>
                        </div>
                        <div class="ml-4 flex-shrink-0 flex">
                            <button id="alert-close" class="bg-white dark:bg-gray-800 rounded-md inline-flex text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                                <span class="sr-only">Close</span>
                                <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="flex items-center mb-4">
            <div class="flex-1">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="relative">
                            <button type="button" class="bg-gray-800 rounded-full flex text-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-800 focus:ring-white">
                                <span class="sr-only">{{ __('general.profile') }}</span>
                                <img class="h-8 w-8 rounded-full" src="{{ $user->profile_image_url }}" alt="">
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            {{ __('general.profile_information') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ __('general.update_profile_info') }}
        </p>
    </header>

    <!-- Custom Alert Box -->
    <!-- Custom Alert Box -->


    <form id="profile-form" method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6" enctype="multipart/form-data">
        @csrf
        @method('patch')

        <!-- Profile Image Section -->
        <div class="flex items-center space-x-6">
            <div class="shrink-0">
                <img id="preview-image" class="h-20 w-20 object-cover rounded-full border-2 border-gray-300 dark:border-gray-600"
                    src="{{ $user->profile_image_url }}" alt="Current profile photo">
            </div>
            <div class="flex-1">
                <label for="profile_image" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                    {{ __('general.profile_photo') }}
                </label>
                <input type="file" id="profile_image" name="profile_image" accept="image/*"
                    class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 dark:file:bg-gray-700 dark:file:text-gray-300">
                <x-input-error class="mt-2" :messages="$errors->get('profile_image')" />

                @if($user->profile_image)
                <button type="button" id="remove-photo-btn" class="text-sm text-red-600 hover:text-red-800 dark:text-red-400 mt-2">
                    {{ __('general.remove_photo') }}
                </button>
                @endif
            </div>
        </div>

        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('general.name')" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name)" required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <!-- Email -->
        <div>
            <x-input-label for="email" :value="__('general.email')" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
            <div>
                <p class="text-sm mt-2 text-gray-800 dark:text-gray-200">
                    {{ __('general.email_unverified') }}

                    <button form="send-verification" class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">
                        {{ __('general.resend_verification') }}
                    </button>
                </p>

                @if (session('status') === 'verification-link-sent')
                <p class="mt-2 font-medium text-sm text-green-600 dark:text-green-400">
                    {{ __('general.verification_link_sent') }}
                </p>
                @endif
            </div>
            @endif
        </div>

        <!-- Phone -->
        <div>
            <x-input-label for="phone" :value="__('general.phone_number')" />
            <x-text-input id="phone" name="phone" type="tel" class="mt-1 block w-full" :value="old('phone', $user->phone)" autocomplete="tel" />
            <x-input-error class="mt-2" :messages="$errors->get('phone')" />
        </div>

        <!-- Address -->
        <div>
            <x-input-label for="address" :value="__('general.address')" />
            <textarea id="address" name="address" rows="3" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">{{ old('address', $user->address) }}</textarea>
            <x-input-error class="mt-2" :messages="$errors->get('address')" />
        </div>

        <!-- Date of Birth -->
        <div>
            <x-input-label for="date_of_birth" :value="__('general.date_of_birth')" />
            <x-text-input id="date_of_birth" name="date_of_birth" type="date" class="mt-1 block w-full" :value="old('date_of_birth', $user->date_of_birth?->format('Y-m-d'))" />
            <x-input-error class="mt-2" :messages="$errors->get('date_of_birth')" />
        </div>

        <!-- Gender -->
        <div>
            <x-input-label for="gender" :value="__('general.gender')" />
            <select id="gender" name="gender" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                <option value="">{{ __('general.select_gender') }}</option>
                <option value="male" {{ old('gender', $user->gender) === 'male' ? 'selected' : '' }}>{{ __('general.male') }}</option>
                <option value="female" {{ old('gender', $user->gender) === 'female' ? 'selected' : '' }}>{{ __('general.female') }}</option>
                <option value="other" {{ old('gender', $user->gender) === 'other' ? 'selected' : '' }}>{{ __('general.other') }}</option>
            </select>
            <x-input-error class="mt-2" :messages="$errors->get('gender')" />
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button id="save-btn">{{ __('general.save') }}</x-primary-button>
            <div id="loading-spinner" class="hidden">
                <svg class="animate-spin h-5 w-5 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </div>
        </div>
    </form>

    <!-- Separate form for email verification -->
    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <!-- Hidden form for removing profile image -->
    <form id="remove-image-form" method="post" action="{{ route('profile.image.remove') }}" class="hidden">
        @csrf
        @method('delete')
    </form>


    <x-confirmation-modal
        name="remove-profile-photo"
        :title="__('general.remove_profile_photo')"
        :message="__('general.remove_photo_confirm')"
        :confirmText="__('general.remove')"
        :cancelText="__('general.cancel')"
        confirmClass="bg-red-600 hover:bg-red-700" />


    <script>
        // Custom Alert System
        function showAlert(type, title, message) {
            const container = document.getElementById('alert-container');
            const alertBox = document.getElementById('alert-box');
            const successIcon = document.getElementById('alert-icon-success');
            const errorIcon = document.getElementById('alert-icon-error');
            const alertTitle = document.getElementById('alert-title');
            const alertMessage = document.getElementById('alert-message');

            // Reset previous state
            alertBox.classList.remove('opacity-100', 'translate-x-0');
            alertBox.classList.add('opacity-0', 'translate-x-full');

            // Hide all icons first
            successIcon.classList.add('hidden');
            errorIcon.classList.add('hidden');

            // Show appropriate icon
            if (type === 'success') {
                successIcon.classList.remove('hidden');
            } else {
                errorIcon.classList.remove('hidden');
            }

            alertTitle.textContent = title;
            alertMessage.textContent = message;

            // Force reflow to ensure animation plays
            void container.offsetWidth;

            // Show and animate the alert
            alertBox.classList.remove('opacity-0', 'translate-x-full');
            alertBox.classList.add('opacity-100', 'translate-x-0');

            // Auto hide after 5 seconds
            setTimeout(() => {
                hideAlert();
            }, 5000);
        }

        function hideAlert() {
            const alertBox = document.getElementById('alert-box');

            alertBox.classList.remove('opacity-100', 'translate-x-0');
            alertBox.classList.add('opacity-0', 'translate-x-full');
        } // Close button functionality
        document.getElementById('alert-close').addEventListener('click', hideAlert);

        // Profile image preview
        document.getElementById('profile_image').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('preview-image').src = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        });

        // Remove photo functionality
        @if($user->profile_image)
        document.getElementById('remove-photo-btn').addEventListener('click', function(e) {
            e.preventDefault();
            window.dispatchEvent(new CustomEvent('open-modal', {
                detail: 'remove-profile-photo'
            }));
        });
        @endif

        // Form submission with loading state
        document.getElementById('profile-form').addEventListener('submit', function(e) {
            const saveBtn = document.getElementById('save-btn');
            const spinner = document.getElementById('loading-spinner');

            saveBtn.disabled = true;
            saveBtn.textContent = '{{ __('general.saving') }}';
            spinner.classList.remove('hidden');
        });

        // Show alerts based on session status
        @if(session('success'))
        document.addEventListener('DOMContentLoaded', function() {
            showAlert("success", "{{ __('general.success') }}", "{{ __('general.profile_updated') }}");
        });
        @endif

        @if($errors->any() || $errors->updatePassword->any())
        document.addEventListener('DOMContentLoaded', function() {
            showAlert("error", "{{ __('general.error') }}", "{{ __('general.form_errors') }}");
        });
        @endif

        @if(session('error'))
        document.addEventListener('DOMContentLoaded', function() {
            showAlert("error", "{{ __('general.error') }}", "{{ session('error') }}");
        });
        @endif
    </script>
</section>