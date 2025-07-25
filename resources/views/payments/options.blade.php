<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Payment Options') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <!-- Contribution Details -->
                    <div class="mb-8 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                        <h3 class="text-lg font-semibold text-blue-900 dark:text-blue-100 mb-2">
                            {{ __('Contribution Details') }}
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                            <div>
                                <span class="text-blue-600 dark:text-blue-400">{{ __('Group') }}:</span>
                                <span class="font-medium text-blue-900 dark:text-blue-100">{{ $contribution->group->name }}</span>
                            </div>
                            <div>
                                <span class="text-blue-600 dark:text-blue-400">{{ __('Amount') }}:</span>
                                <span class="font-medium text-blue-900 dark:text-blue-100">₦{{ number_format($contribution->amount, 2) }}</span>
                            </div>
                            <div>
                                <span class="text-blue-600 dark:text-blue-400">{{ __('Due Date') }}:</span>
                                <span class="font-medium text-blue-900 dark:text-blue-100">{{ $contribution->due_date->format('M d, Y') }}</span>
                            </div>
                            <div>
                                <span class="text-blue-600 dark:text-blue-400">{{ __('Status') }}:</span>
                                <span class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                                    {{ ucfirst($contribution->status) }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Methods -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Card Payment -->
                        <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-6 hover:shadow-lg transition-shadow">
                            <div class="flex items-center mb-4">
                                <div class="w-12 h-12 bg-green-100 dark:bg-green-900 rounded-lg flex items-center justify-center mr-4">
                                    <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ __('Pay with Card') }}</h4>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">{{ __('Instant payment with debit/credit card') }}</p>
                                </div>
                            </div>
                            <button onclick="showCardPayment()" 
                                class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-4 rounded-lg transition duration-200">
                                {{ __('Pay with Card') }}
                            </button>
                        </div>

                        <!-- Bank Transfer -->
                        <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-6 hover:shadow-lg transition-shadow">
                            <div class="flex items-center mb-4">
                                <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900 rounded-lg flex items-center justify-center mr-4">
                                    <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ __('Bank Transfer') }}</h4>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">{{ __('Pay via bank transfer to virtual account') }}</p>
                                </div>
                            </div>
                            <form method="POST" action="{{ route('payments.virtual-account', $contribution) }}">
                                @csrf
                                <button type="submit" 
                                    class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded-lg transition duration-200">
                                    {{ __('Generate Account Details') }}
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Card Payment Form (Hidden by default) -->
                    <div id="cardPaymentForm" class="hidden mt-8 p-6 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <h4 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">{{ __('Card Payment Details') }}</h4>
                        
                        <form id="cardForm" class="space-y-4">
                            @csrf
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('Card Number') }}</label>
                                    <input type="text" id="cardNumber" name="card_number" 
                                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 dark:bg-gray-800 dark:text-gray-100"
                                        placeholder="1234 5678 9012 3456" maxlength="19" required>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('Expiry Month') }}</label>
                                    <select id="cardMonth" name="card_month" 
                                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 dark:bg-gray-800 dark:text-gray-100" required>
                                        <option value="">{{ __('Month') }}</option>
                                        @for($i = 1; $i <= 12; $i++)
                                            <option value="{{ sprintf('%02d', $i) }}">{{ sprintf('%02d', $i) }}</option>
                                        @endfor
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('Expiry Year') }}</label>
                                    <select id="cardYear" name="card_year" 
                                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 dark:bg-gray-800 dark:text-gray-100" required>
                                        <option value="">{{ __('Year') }}</option>
                                        @for($i = 0; $i <= 10; $i++)
                                            <option value="{{ sprintf('%02d', $i) }}">{{ date('y', strtotime('+' . $i . ' years')) }}</option>
                                        @endfor
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('CVV') }}</label>
                                    <input type="text" id="securityCode" name="security_code" 
                                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 dark:bg-gray-800 dark:text-gray-100"
                                        placeholder="123" maxlength="3" required>
                                </div>
                            </div>
                            
                            <div class="flex space-x-4 mt-6">
                                <button type="button" onclick="hideCardPayment()" 
                                    class="flex-1 bg-gray-500 hover:bg-gray-600 text-white font-bold py-3 px-4 rounded-lg transition duration-200">
                                    {{ __('Cancel') }}
                                </button>
                                <button type="submit" id="payButton" 
                                    class="flex-1 bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-4 rounded-lg transition duration-200">
                                    {{ __('Pay Now') }}
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- 3D Secure iframe container -->
                    <div id="threeDSecureContainer" class="hidden mt-8">
                        <h4 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">{{ __('Complete Payment Authentication') }}</h4>
                        <div id="threeDSecureFrame" class="w-full h-96 border border-gray-300 rounded-lg"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        let transactionId = null;

        function showCardPayment() {
            document.getElementById('cardPaymentForm').classList.remove('hidden');
        }

        function hideCardPayment() {
            document.getElementById('cardPaymentForm').classList.add('hidden');
            document.getElementById('threeDSecureContainer').classList.add('hidden');
        }

        // Format card number input
        document.getElementById('cardNumber').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\s/g, '').replace(/[^0-9]/gi, '');
            let formattedValue = value.match(/.{1,4}/g)?.join(' ') || value;
            e.target.value = formattedValue;
        });

        // Handle card form submission
        document.getElementById('cardForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const payButton = document.getElementById('payButton');
            const originalText = payButton.textContent;
            payButton.textContent = 'Processing...';
            payButton.disabled = true;

            try {
                // First, initialize the card
                const cardNumber = document.getElementById('cardNumber').value.replace(/\s/g, '');
                
                const initResponse = await fetch('{{ route("payments.initialize-card", $contribution) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ card_number: cardNumber })
                });

                const initResult = await initResponse.json();
                
                if (!initResult.success) {
                    throw new Error(initResult.message);
                }

                transactionId = initResult.data.transactionId;

                // Now process the full payment
                const formData = new FormData(this);
                formData.append('transaction_id', transactionId);

                const paymentResponse = await fetch('{{ route("payments.process-card", $contribution) }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: formData
                });

                const paymentResult = await paymentResponse.json();
                
                if (paymentResult.success && paymentResult.redirect_html) {
                    // Show 3D Secure iframe
                    document.getElementById('threeDSecureContainer').classList.remove('hidden');
                    document.getElementById('threeDSecureFrame').innerHTML = paymentResult.redirect_html;
                } else {
                    throw new Error(paymentResult.message || 'Payment failed');
                }
            } catch (error) {
                alert('Payment Error: ' + error.message);
            } finally {
                payButton.textContent = originalText;
                payButton.disabled = false;
            }
        });
    </script>
</x-app-layout>