<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            {{ __('general.bank_details') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ __('general.update_banking_info') }}
        </p>
    </header>

    <form method="post" action="{{ route('profile.bank-details.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <!-- Bank Name -->
        <div>
            <x-input-label for="bank_name" :value="__('general.bank_name')" />
            <select id="bank_name" name="bank_name" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                <option value="">{{ __('general.select_bank') }}</option>
                <option value="Access Bank" {{ old('bank_name', $user->bank_name) === 'Access Bank' ? 'selected' : '' }}>Access Bank</option>
                <option value="Citibank" {{ old('bank_name', $user->bank_name) === 'Citibank' ? 'selected' : '' }}>Citibank</option>
                <option value="Ecobank" {{ old('bank_name', $user->bank_name) === 'Ecobank' ? 'selected' : '' }}>Ecobank</option>
                <option value="Fidelity Bank" {{ old('bank_name', $user->bank_name) === 'Fidelity Bank' ? 'selected' : '' }}>Fidelity Bank</option>
                <option value="First Bank" {{ old('bank_name', $user->bank_name) === 'First Bank' ? 'selected' : '' }}>First Bank</option>
                <option value="FCMB" {{ old('bank_name', $user->bank_name) === 'FCMB' ? 'selected' : '' }}>FCMB</option>
                <option value="GTBank" {{ old('bank_name', $user->bank_name) === 'GTBank' ? 'selected' : '' }}>GTBank</option>
                <option value="Heritage Bank" {{ old('bank_name', $user->bank_name) === 'Heritage Bank' ? 'selected' : '' }}>Heritage Bank</option>
                <option value="Keystone Bank" {{ old('bank_name', $user->bank_name) === 'Keystone Bank' ? 'selected' : '' }}>Keystone Bank</option>
                <option value="Polaris Bank" {{ old('bank_name', $user->bank_name) === 'Polaris Bank' ? 'selected' : '' }}>Polaris Bank</option>
                <option value="Providus Bank" {{ old('bank_name', $user->bank_name) === 'Providus Bank' ? 'selected' : '' }}>Providus Bank</option>
                <option value="Stanbic IBTC" {{ old('bank_name', $user->bank_name) === 'Stanbic IBTC' ? 'selected' : '' }}>Stanbic IBTC</option>
                <option value="Sterling Bank" {{ old('bank_name', $user->bank_name) === 'Sterling Bank' ? 'selected' : '' }}>Sterling Bank</option>
                <option value="UBA" {{ old('bank_name', $user->bank_name) === 'UBA' ? 'selected' : '' }}>UBA</option>
                <option value="Union Bank" {{ old('bank_name', $user->bank_name) === 'Union Bank' ? 'selected' : '' }}>Union Bank</option>
                <option value="Unity Bank" {{ old('bank_name', $user->bank_name) === 'Unity Bank' ? 'selected' : '' }}>Unity Bank</option>
                <option value="Wema Bank" {{ old('bank_name', $user->bank_name) === 'Wema Bank' ? 'selected' : '' }}>Wema Bank</option>
                <option value="Zenith Bank" {{ old('bank_name', $user->bank_name) === 'Zenith Bank' ? 'selected' : '' }}>Zenith Bank</option>
            </select>
            <x-input-error class="mt-2" :messages="$errors->get('bank_name')" />
        </div>

        <!-- Account Number -->
        <div>
            <x-input-label for="account_number" :value="__('general.account_number')" />
            <x-text-input id="account_number" name="account_number" type="text" class="mt-1 block w-full" :value="old('account_number', $user->account_number)" maxlength="10" pattern="[0-9]{10}" />
            <x-input-error class="mt-2" :messages="$errors->get('account_number')" />
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ __('general.account_number_hint') }}</p>
        </div>

        <!-- Account Name -->
        <div>
            <x-input-label for="account_name" :value="__('general.account_name')" />
            <x-text-input id="account_name" name="account_name" type="text" class="mt-1 block w-full" :value="old('account_name', $user->account_name)" />
            <x-input-error class="mt-2" :messages="$errors->get('account_name')" />
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ __('general.account_name_hint') }}</p>
        </div>

        <!-- BVN (Optional) -->
        <!-- <div>
            <x-input-label for="bvn" :value="__('general.bvn_optional')" />
            <x-text-input id="bvn" name="bvn" type="text" class="mt-1 block w-full" :value="old('bvn', $user->bvn)" maxlength="11" pattern="[0-9]{11}" />
            <x-input-error class="mt-2" :messages="$errors->get('bvn')" />
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ __('general.bvn_hint') }}</p>
        </div> -->

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('general.update_bank_details') }}</x-primary-button>

          
        </div>
    </form>

    
</section>