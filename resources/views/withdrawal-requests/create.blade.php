<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Request Payout') }} - {{ $group->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl px-4 mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <!-- Payout Summary -->
                    <div class="mb-8 p-6 bg-gradient-to-r from-green-50 to-blue-50 dark:from-green-900/20 dark:to-blue-900/20 border border-green-200 dark:border-green-800 rounded-lg">
                        <h3 class="text-xl font-bold text-green-900 dark:text-green-100 mb-4 text-center">💰 Payout Summary</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-center">
                            <div class="p-4 bg-white dark:bg-gray-700 rounded-lg">
                                <div class="text-2xl font-bold text-green-600 dark:text-green-400">₦{{ number_format($payoutAmount, 2) }}</div>
                                <div class="text-sm text-gray-600 dark:text-gray-400">Gross Amount</div>
                            </div>
                            <div class="p-4 bg-white dark:bg-gray-700 rounded-lg">
                                <div class="text-2xl font-bold text-red-600 dark:text-red-400">-₦{{ number_format($serviceCharge, 2) }}</div>
                                <div class="text-sm text-gray-600 dark:text-gray-400">Service Charge (5%)</div>
                            </div>
                            <div class="p-4 bg-white dark:bg-gray-700 rounded-lg border-2 border-green-500">
                                <div class="text-2xl font-bold text-green-700 dark:text-green-300">₦{{ number_format($netAmount, 2) }}</div>
                                <div class="text-sm text-gray-600 dark:text-gray-400">Net Amount</div>
                            </div>
                        </div>
                    </div>

                    <!-- Withdrawal Request Form -->
                    <form method="POST" action="{{ route('withdrawal-requests.store', $group) }}" class="space-y-6">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Bank Name -->
                            <div>
                                <label for="bank_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    {{ __('Bank Name') }}
                                </label>
                                <select id="bank_name" name="bank_name" required
                                    class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                    <option value="">{{ __('Select Bank') }}</option>
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
                                @error('bank_name')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Account Number -->
                            <div>
                                <label for="account_number" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    {{ __('Account Number') }}
                                </label>
                                <input type="text" id="account_number" name="account_number" 
                                    value="{{ old('account_number', $user->account_number) }}" required
                                    class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                    placeholder="Enter your 10-digit account number">
                                @error('account_number')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Account Name -->
                        <div>
                            <label for="account_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                {{ __('Account Name') }}
                            </label>
                            <input type="text" id="account_name" name="account_name" 
                                value="{{ old('account_name', $user->account_name) }}" required
                                class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                placeholder="Account name as it appears on your bank statement">
                            @error('account_name')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Notes -->
                        <div>
                            <label for="notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                {{ __('Additional Notes (Optional)') }}
                            </label>
                            <textarea id="notes" name="notes" rows="3"
                                class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                placeholder="Any additional information or special instructions">{{ old('notes') }}</textarea>
                            @error('notes')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Important Notice -->
                        <div class="p-4 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg">
                            <div class="flex items-start">
                                <svg class="w-5 h-5 text-yellow-600 dark:text-yellow-400 mt-0.5 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                </svg>
                                <div>
                                    <h4 class="text-sm font-medium text-yellow-800 dark:text-yellow-200">Important Notice</h4>
                                    <p class="mt-1 text-sm text-yellow-700 dark:text-yellow-300">
                                        • Withdrawal requests are processed within 1-3 business days<br>
                                        • A 5% service charge will be deducted from your payout<br>
                                        • Ensure your account details are correct to avoid delays<br>
                                        • You can only request withdrawal when it's your turn to receive payout
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex flex-col sm:flex-row gap-4 pt-6">
                            <button type="submit" 
                                class="flex-1 bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-6 rounded-lg transition duration-200 flex items-center justify-center">
                               
                                Submit Withdrawal Request
                            </button>
                            <a href="{{ route('groups.show', $group) }}" 
                                class="flex-1 bg-gray-600 hover:bg-gray-700 text-white font-bold py-3 px-6 rounded-lg transition duration-200 text-center">
                                Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>