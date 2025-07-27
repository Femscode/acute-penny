@if($group->description)
<p class="text-gray-600 dark:text-gray-400 mb-6">{{ $group->description }}</p>
@endif

@if($group->isContributionStarted())
<div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4 mb-6">
    <h4 class="text-lg font-semibold text-blue-900 dark:text-blue-100 mb-3">{{ __('general.contribution_status') }}</h4>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
            <div class="text-sm text-blue-600 dark:text-blue-400">{{ __('general.current_turn') }}</div>
            <div class="font-semibold text-blue-900 dark:text-blue-100">
                {{ $group->currentTurnUser->name ?? 'N/A' }}
            </div>
        </div>
        <div>
            <div class="text-sm text-blue-600 dark:text-blue-400">{{ __('general.next_contribution_date') }}</div>
            <div class="font-semibold text-blue-900 dark:text-blue-100">
                {{ $group->getNextContributionDate()?->format('M d, Y') ?? 'N/A' }}
            </div>
        </div>
        <div>
            <div class="text-sm text-blue-600 dark:text-blue-400">{{ __('general.current_cycle') }}</div>
            <div class="font-semibold text-blue-900 dark:text-blue-100">
                {{ $group->current_cycle }} / {{ $group->current_members }}
            </div>
        </div>
    </div>

    @if($isAuthenticated)
    @if($group->current_turn_user_uuid === Auth::user()->uuid)
    <!-- Payout Request Section -->
    <div class="mt-6 p-4 bg-gradient-to-r from-green-50 to-yellow-50 dark:from-green-900/20 dark:to-yellow-900/20 border border-green-200 dark:border-green-800 rounded-lg">
        <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
            <div>
                <h5 class="text-lg font-bold text-green-900 dark:text-green-100">🎉 It's Your Turn to Receive Payout!</h5>
                <p class="text-sm text-green-700 dark:text-green-300 mt-1">
                    You can now request your payout of ₦{{ number_format(($group->contribution_amount * $group->current_members), 2) }}
                </p>
            </div>
            @php
            $existingRequest = \App\Models\WithdrawalRequest::where('user_uuid', Auth::user()->uuid)
            ->where('group_uuid', $group->uuid)
            ->where('status', 'pending')
            ->first();
            @endphp

            @if($existingRequest)
            <div class="text-center">
                <div class="inline-flex items-center px-4 py-2 bg-yellow-100 dark:bg-yellow-900/30 border border-yellow-300 dark:border-yellow-700 rounded-lg">
                    <svg class="w-5 h-5 text-yellow-600 dark:text-yellow-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                    </svg>
                    <span class="text-sm font-medium text-yellow-800 dark:text-yellow-200">Request Pending</span>
                </div>
                <p class="text-xs text-yellow-700 dark:text-yellow-300 mt-1">Your withdrawal request is being processed</p>
            </div>
            @else
            <a href="{{ route('withdrawal-requests.create', $group) }}"
                class="inline-flex items-center px-6 py-3 bg-green-600 hover:bg-green-700 text-white font-bold rounded-lg transition duration-200">
                Request Payout
            </a>
            @endif
        </div>
    </div>
    @endif
    @endif
</div>

@php
$userHasPaid = $group->hasUserPaidCurrentCycle(Auth::user()->uuid);
$userContribution = $group->getUserCurrentCycleContribution(Auth::user()->uuid);
@endphp

@if($userHasPaid)
<!-- Payment Completed Section -->
<div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-4 mb-6">
    <div class="flex items-center mb-3">
        <svg class="w-6 h-6 text-green-600 dark:text-green-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
        </svg>
        <h4 class="text-lg font-semibold text-green-900 dark:text-green-100">{{ __('general.payment_completed') }}</h4>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
            <div class="text-sm text-green-600 dark:text-green-400">{{ __('general.amount_paid') }}</div>
            <div class="font-semibold text-green-900 dark:text-green-100">
                ₦{{ number_format($userContribution->amount, 2) }}
            </div>
        </div>
        <div>
            <div class="text-sm text-green-600 dark:text-green-400">{{ __('general.payment_date') }}</div>
            <div class="font-semibold text-green-900 dark:text-green-100">
                {{ $userContribution->paid_at?->format('M d, Y H:i') ?? 'N/A' }}
            </div>
        </div>
        <div>
            <div class="text-sm text-green-600 dark:text-green-400">{{ __('general.status') }}</div>
            <div class="font-semibold text-green-900 dark:text-green-100">
                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-200">
                    {{ ucfirst($userContribution->status) }}
                </span>
            </div>
        </div>
    </div>
    <div class="mt-3 p-3 bg-green-100 dark:bg-green-900/30 rounded-lg">
        <p class="text-sm text-green-700 dark:text-green-300">
            <svg class="w-4 h-4 inline mr-1" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
            </svg>
            {{ __('general.payment_completed_message') }}
        </p>
    </div>
</div>
@else
<!-- Payment Options Section -->
@if($group->currentTurnUser)
<div class="bg-orange-50 dark:bg-orange-900/20 border border-orange-200 dark:border-orange-800 rounded-lg p-4 mb-6">
    <div class="flex items-center mb-3">
        <svg class="w-6 h-6 text-orange-600 dark:text-orange-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
        </svg>
        <h4 class="text-lg font-semibold text-orange-900 dark:text-orange-100">{{ __('general.payment_required') }}</h4>
    </div>
    <p class="text-sm text-orange-700 dark:text-orange-300 mb-4">{{ __('general.choose_payment_method') }}</p>

    <!-- ALATPay Payment Options -->
    <div class="space-y-4">
        <!-- ALATPay Card Payment -->
        <div class="border border-orange-300 dark:border-orange-600 rounded-lg p-4">
            <h5 class="font-semibold text-orange-900 dark:text-orange-100 mb-2">ALATPay - {{ __('general.card_payment') }}</h5>
            <p class="text-sm text-orange-700 dark:text-orange-300 mb-3">{{ __('general.pay_securely_with_debit_card') }}</p>
         
            <!-- <a disabled href="{{ route('alat.card.form', $group) }}" -->
            <a disabled onclick="alert('{{ __('general.payment_option_not_available') }}')"
                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium transition inline-block">
                {{ __('general.pay_with_card') }}
            </a>
        </div>

        <!-- ALATPay Bank Transfer -->
        <div class="border border-orange-300 dark:border-orange-600 rounded-lg p-4">
            <h5 class="font-semibold text-orange-900 dark:text-orange-100 mb-2">ALATPay - {{ __('general.bank_transfer') }}</h5>
            <p class="text-sm text-orange-700 dark:text-orange-300 mb-3">{{ __('general.generate_a_virtual_account_for_bank_transfer') }}</p>
            <form action="{{ route('payments.virtual-account', $group->currentContribution ?? $group) }}" method="POST" class="inline">
                @csrf
                <input type="hidden" name="amount" value="{{ $group->contribution_amount }}">
                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md text-sm font-medium transition">
                    {{ __('general.generate_virtual_account') }}
                </button>
            </form>
        </div>
    </div>
</div>
@endif
@endif
@endif

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
    <div class="text-center p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
        <div class="text-2xl font-bold text-gray-900 dark:text-gray-100">₦{{ number_format($group->contribution_amount, 2) }}</div>
        <div class="text-sm text-gray-600 dark:text-gray-400">{{ __('general.contribution_amount') }}</div>
    </div>
    <div class="text-center p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
        <div class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ ucfirst($group->frequency) }}</div>
        <div class="text-sm text-gray-600 dark:text-gray-400">{{ __('general.frequency') }}</div>
    </div>
    <div class="text-center p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
        <div class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $group->current_members }}/{{ $group->max_members }}</div>
        <div class="text-sm text-gray-600 dark:text-gray-400">{{ __('general.members') }}</div>
    </div>
    <div class="text-center p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
        <div class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $group->start_date->format('M d, Y') }}</div>
        <div class="text-sm text-gray-600 dark:text-gray-400">{{ __('general.start_date') }}</div>
    </div>
</div>

<!-- Share Group (Compact Version) -->
<div class="mt-6 border-t border-gray-200 dark:border-gray-700 pt-4">
    <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
        <div class="text-sm font-medium text-gray-700 dark:text-gray-300">
            {{ __('general.share_group') }}:
        </div>

        <!-- Link Display with Copy Button -->
        <div class="flex-1 flex items-center w-full sm:w-auto">
            <div class="relative flex-1">
                <input type="text"
                    value="{{ route('groups.show', $group) }}"
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-l-md bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-gray-100 text-sm"
                    readonly>
            </div>
            <button onclick="copyToClipboard('{{ route('groups.show', $group) }}'); this.querySelector('.tooltip').classList.remove('hidden'); setTimeout(() => this.querySelector('.tooltip').classList.add('hidden'), 2000)"
                class="relative px-3 py-2 bg-gray-200 dark:bg-gray-600 text-gray-700 dark:text-gray-200 rounded-r-md hover:bg-gray-300 dark:hover:bg-gray-500 transition">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                    <path d="M8 3a1 1 0 011-1h2a1 1 0 110 2H9a1 1 0 01-1-1z"></path>
                    <path d="M6 3a2 2 0 00-2 2v11a2 2 0 002 2h8a2 2 0 002-2V5a2 2 0 00-2-2 3 3 0 01-3 3H9a3 3 0 01-3-3z"></path>
                </svg>
                <span class="tooltip hidden absolute -top-8 right-0 bg-gray-800 text-white text-xs px-2 py-1 rounded whitespace-nowrap">Copied!</span>
            </button>
        </div>

        <!-- Social Share Icons -->
        <div class="flex space-x-2">
            <a href="https://wa.me/?text={{ urlencode('Join our contribution group: ' . $group->name . ' - ' . route('groups.show', $group)) }}"
                target="_blank"
                class="p-2 bg-green-500 text-white rounded-full hover:bg-green-600 transition">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z" />
                </svg>
            </a>

            <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(route('groups.show', $group)) }}"
                target="_blank"
                class="p-2 bg-blue-600 text-white rounded-full hover:bg-blue-700 transition">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z" />
                </svg>
            </a>

            <a href="https://twitter.com/intent/tweet?text={{ urlencode('Join our contribution group: ' . $group->name) }}&url={{ urlencode(route('groups.show', $group)) }}"
                target="_blank"
                class="p-2 bg-black text-white rounded-full hover:bg-gray-800 transition">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z" />
                </svg>
            </a>

            <a href="mailto:?subject={{ urlencode('Join our contribution group: ' . $group->name) }}&body={{ urlencode('Hello! I would like to invite you to join our contribution group: ' . $group->name . '. Click the link to view details and join: ' . route('groups.show', $group)) }}"
                class="p-2 bg-gray-600 text-white rounded-full hover:bg-gray-700 transition">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z" />
                </svg>
            </a>
        </div>
    </div>
</div>

<script>
    function copyToClipboard(text) {
        const textarea = document.createElement('textarea');
        textarea.value = text;
        document.body.appendChild(textarea);
        textarea.select();
        document.execCommand('copy');
        document.body.removeChild(textarea);
    }
</script>