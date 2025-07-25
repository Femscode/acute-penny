<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            {{ __('general.update_password') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ __('general.password_security_message') }}
        </p>
    </header>

     <form method="post" action="{{ route('profile.password.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <!-- Current Password -->
        <div>
            <x-input-label for="update_password_current_password" :value="__('general.current_password')" />
            <x-text-input id="update_password_current_password" name="current_password" type="password" class="mt-1 block w-full" autocomplete="current-password" />
            <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2" />
        </div>

        <!-- New Password -->
        <div>
            <x-input-label for="update_password_password" :value="__('general.new_password')" />
            <x-text-input id="update_password_password" name="password" type="password" class="mt-1 block w-full" autocomplete="new-password" />
            <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div>
            <x-input-label for="update_password_password_confirmation" :value="__('general.confirm_password')" />
            <x-text-input id="update_password_password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full" autocomplete="new-password" />
            <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('general.save') }}</x-primary-button>

            @if (session('status') === 'password-updated')
                <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 3000)" class="text-sm text-green-600 dark:text-green-400">
                    {{ __('general.password_updated') }}
                </p>
            @endif
            
            @if ($errors->updatePassword->has('password_update'))
                <p class="text-sm text-red-600 dark:text-red-400">
                    {{ $errors->updatePassword->first('password_update') }}
                </p>
            @endif
        </div>
    </form>

     <script>
        document.getElementById('update_password_password').addEventListener('input', function(e) {
            const password = e.target.value;
            
            // Length check
            const lengthCheck = document.getElementById('length-check');
            if (password.length >= 8) {
                lengthCheck.className = 'text-green-500';
            } else {
                lengthCheck.className = 'text-red-500';
            }
            
            // Uppercase check
            const uppercaseCheck = document.getElementById('uppercase-check');
            if (/[A-Z]/.test(password)) {
                uppercaseCheck.className = 'text-green-500';
            } else {
                uppercaseCheck.className = 'text-red-500';
            }
            
            // Lowercase check
            const lowercaseCheck = document.getElementById('lowercase-check');
            if (/[a-z]/.test(password)) {
                lowercaseCheck.className = 'text-green-500';
            } else {
                lowercaseCheck.className = 'text-red-500';
            }
            
            // Number check
            const numberCheck = document.getElementById('number-check');
            if (/[0-9]/.test(password)) {
                numberCheck.className = 'text-green-500';
            } else {
                numberCheck.className = 'text-red-500';
            }
            
            // Special character check
            const specialCheck = document.getElementById('special-check');
            if (/[^A-Za-z0-9]/.test(password)) {
                specialCheck.className = 'text-green-500';
            } else {
                specialCheck.className = 'text-red-500';
            }
        });
    </script>
</section>

   