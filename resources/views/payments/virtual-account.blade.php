<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Bank Transfer Payment') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <!-- Payment Instructions -->
                    <div class="mb-8 p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg">
                        <h3 class="text-lg font-semibold text-green-900 dark:text-green-100 mb-2">
                            {{ __('Transfer Instructions') }}
                        </h3>
                        <p class="text-sm text-green-700 dark:text-green-300">
                            {{ __('Transfer the exact amount to the account details below. Payment will be confirmed automatically.') }}
                        </p>
                    </div>

                    <!-- Virtual Account Details -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                        <div class="space-y-4">
                            <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                <label class="block text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">{{ __('Account Number') }}</label>
                                <div class="flex items-center justify-between">
                                    <span class="text-lg font-bold text-gray-900 dark:text-gray-100">{{ $virtualAccount['virtualBankAccountNumber'] }}</span>
                                    <button onclick="copyToClipboard('{{ $virtualAccount['virtualBankAccountNumber'] }}', this)" 
                                        class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-200">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                            
                            <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                <label class="block text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">{{ __('Bank Name') }}</label>
                                <span class="text-lg font-bold text-gray-900 dark:text-gray-100">{{ $virtualAccount['virtualBankCode'] }}</span>
                            </div>
                        </div>
                        
                        <div class="space-y-4">
                            <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                <label class="block text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">{{ __('Amount') }}</label>
                                <span class="text-lg font-bold text-gray-900 dark:text-gray-100">₦{{ number_format($virtualAccount['amount'], 2) }}</span>
                            </div>
                            
                            <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                <label class="block text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">{{ __('Expires At') }}</label>
                                <span class="text-lg font-bold text-red-600 dark:text-red-400">{{ \Carbon\Carbon::parse($virtualAccount['expiredAt'])->format('M d, Y H:i A') }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Status -->
                    <div class="mb-8 p-4 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg">
                        <div class="flex items-center justify-between">
                            <div>
                                <h4 class="text-lg font-semibold text-yellow-900 dark:text-yellow-100">{{ __('Payment Status') }}</h4>
                                <p class="text-sm text-yellow-700 dark:text-yellow-300" id="statusMessage">
                                    {{ __('Waiting for payment...') }}
                                </p>
                            </div>
                            <button onclick="checkPaymentStatus()" id="checkStatusBtn"
                                class="bg-yellow-600 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded-lg transition duration-200">
                                {{ __('Check Status') }}
                            </button>
                        </div>
                    </div>

                    <!-- Important Notes -->
                    <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
                        <h4 class="text-lg font-semibold text-blue-900 dark:text-blue-100 mb-2">{{ __('Important Notes') }}</h4>
                        <ul class="text-sm text-blue-700 dark:text-blue-300 space-y-1">
                            <li>• {{ __('Transfer the exact amount shown above') }}</li>
                            <li>• {{ __('This account is valid for 30 minutes only') }}</li>
                            <li>• {{ __('Payment confirmation is automatic') }}</li>
                            <li>• {{ __('Contact support if payment is not confirmed within 1 hour') }}</li>
                        </ul>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex space-x-4 mt-8">
                        <a href="{{ route('groups.show', $contribution->group) }}" 
                            class="flex-1 bg-gray-500 hover:bg-gray-600 text-white font-bold py-3 px-4 rounded-lg text-center transition duration-200">
                            {{ __('Back to Group') }}
                        </a>
                        <button onclick="window.print()" 
                            class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded-lg transition duration-200">
                            {{ __('Print Details') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function copyToClipboard(text, button) {
            navigator.clipboard.writeText(text).then(function() {
                const originalContent = button.innerHTML;
                button.innerHTML = '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>';
                button.classList.add('text-green-600');
                
                setTimeout(() => {
                    button.innerHTML = originalContent;
                    button.classList.remove('text-green-600');
                }, 2000);
            });
        }

        async function checkPaymentStatus() {
            const button = document.getElementById('checkStatusBtn');
            const statusMessage = document.getElementById('statusMessage');
            
            button.textContent = 'Checking...';
            button.disabled = true;

            try {
                const response = await fetch('{{ route("payments.check-status", $contribution) }}', {
                    method: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });

                const result = await response.json();
                
                if (result.success && result.paid) {
                    statusMessage.textContent = result.message;
                    statusMessage.className = 'text-sm text-green-700 dark:text-green-300';
                    button.textContent = 'Payment Confirmed!';
                    button.className = 'bg-green-600 text-white font-bold py-2 px-4 rounded-lg';
                    
                    // Redirect to group page after 3 seconds
                    setTimeout(() => {
                        window.location.href = '{{ route("groups.show", $contribution->group) }}';
                    }, 3000);
                } else {
                    statusMessage.textContent = result.message || 'Payment still pending';
                }
            } catch (error) {
                statusMessage.textContent = 'Error checking payment status';
            } finally {
                if (!button.textContent.includes('Confirmed')) {
                    button.textContent = 'Check Status';
                    button.disabled = false;
                }
            }
        }

        // Auto-check payment status every 30 seconds
        setInterval(checkPaymentStatus, 30000);
    </script>
</x-app-layout>