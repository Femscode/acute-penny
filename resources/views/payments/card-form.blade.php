<x-app-layout>
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-md mx-auto bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-6">Card Payment</h2>
            
            <div class="mb-4 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                <h3 class="font-semibold text-blue-900 dark:text-blue-100">Payment Details</h3>
                <p class="text-sm text-blue-700 dark:text-blue-300">Group: {{ $group->name }}</p>
                <p class="text-sm text-blue-700 dark:text-blue-300">Amount: ₦{{ number_format($group->contribution_amount, 2) }}</p>
                <p class="text-sm text-blue-700 dark:text-blue-300">Recipient: {{ $group->currentTurnUser->name }}</p>
            </div>

            <form id="cardPaymentForm" action="{{ route('alat.card.initialize') }}" method="POST">
                @csrf
                <input type="hidden" name="group_id" value="{{ $group->id }}">
                <input type="hidden" name="recipient_id" value="{{ $group->currentTurnUser->id }}">
                <input type="hidden" name="amount" value="{{ $group->contribution_amount }}">
                
                <div class="mb-4">
                    <label for="card_number" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Card Number
                    </label>
                    <input type="text" 
                           id="card_number" 
                           name="card_number" 
                           placeholder="1234 5678 9012 3456"
                           maxlength="19"
                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-gray-100"
                           required>
                </div>
                
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label for="expiry_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Expiry Date
                        </label>
                        <input type="text" 
                               id="expiry_date" 
                               name="expiry_date" 
                               placeholder="MM/YY"
                               maxlength="5"
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-gray-100"
                               required>
                    </div>
                    <div>
                        <label for="cvv" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            CVV
                        </label>
                        <input type="text" 
                               id="cvv" 
                               name="cvv" 
                               placeholder="123"
                               maxlength="4"
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-gray-100"
                               required>
                    </div>
                </div>
                
                <div class="mb-6">
                    <label for="cardholder_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Cardholder Name
                    </label>
                    <input type="text" 
                           id="cardholder_name" 
                           name="cardholder_name" 
                           placeholder="John Doe"
                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-gray-100"
                           required>
                </div>
                
                <div class="flex space-x-4">
                    <button type="submit" 
                            class="flex-1 bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded-md font-medium transition">
                        Pay ₦{{ number_format($group->contribution_amount, 2) }}
                    </button>
                    <a href="{{ route('groups.show', $group) }}" 
                       class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-700 py-2 px-4 rounded-md font-medium transition text-center">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>

    <script>
    // Format card number input
    document.getElementById('card_number').addEventListener('input', function(e) {
        let value = e.target.value.replace(/\s/g, '').replace(/[^0-9]/gi, '');
        let formattedValue = value.match(/.{1,4}/g)?.join(' ') || value;
        e.target.value = formattedValue;
    });

    // Format expiry date input
    document.getElementById('expiry_date').addEventListener('input', function(e) {
        let value = e.target.value.replace(/\D/g, '');
        if (value.length >= 2) {
            value = value.substring(0, 2) + '/' + value.substring(2, 4);
        }
        e.target.value = value;
    });

    // Only allow numbers for CVV
    document.getElementById('cvv').addEventListener('input', function(e) {
        e.target.value = e.target.value.replace(/[^0-9]/g, '');
    });
    </script>
</x-app-layout>