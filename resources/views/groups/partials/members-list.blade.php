<!-- Members List -->
<div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
    <div class="p-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">{{ __('general.group_members') }}</h3>

        @if($group->members->count() > 0)
        <div class="space-y-4">
            @foreach($group->members as $member)
            <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                <!-- Member Info Row -->
                <div class="flex items-center justify-between mb-3">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center text-white font-semibold">
                            {{ substr($member->user->name, 0, 1) }}
                        </div>
                        <div>
                            <div class="font-medium text-gray-900 dark:text-gray-100">
                                {{ $member->user->name }}
                                @if($member->user->uuid === $group->created_by)
                                <span class="ml-2 px-2 py-1 text-xs bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200 rounded">
                                    {{ __('general.creator') }}
                                </span>
                                @endif
                            </div>
                            <div class="text-sm text-gray-600 dark:text-gray-400">
                                {{ __('general.joined') }} {{ $member->joined_at->format('M d, Y') }}
                            </div>
                        </div>
                    </div>
                    @if($member->payout_position)
                    <div class="flex items-center space-x-2">
                        <div class="text-sm text-gray-600 dark:text-gray-400">
                            {{ __('general.position') }} #{{ $member->payout_position }}
                        </div>
                        @if($member->is_rolled)
                        <span class="text-green-500" title="{{ __('general.position_revealed') }}">
                            ✅
                        </span>
                        @else
                        <span class="text-gray-400" title="{{ __('general.position_not_revealed') }}">
                            ❓
                        </span>
                        @endif
                    </div>
                    @endif
                </div>

                <!-- Cycle Payment Indicators -->
                @if($group->isContributionStarted())
                <div class="border-t border-gray-200 dark:border-gray-600 pt-3">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-xs font-medium text-gray-600 dark:text-gray-400">Payment Status by Cycle:</span>
                        <span class="text-xs text-gray-500 dark:text-gray-400">{{ $group->current_cycle }}/{{ $group->current_members }} cycles</span>
                    </div>
                    <div class="flex flex-wrap gap-1">
                        @for($cycle = 1; $cycle <= $group->current_members; $cycle++)
                            @php
                                // Check if member has paid for this cycle
                                $contribution = $group->contributions()
                                    ->where('user_uuid', $member->user_uuid)
                                    ->where('cycle', $cycle)
                                    ->first();
                                
                                $isPaid = $contribution && $contribution->status === 'paid';
                                $isPending = $contribution && $contribution->status === 'pending';
                                $isOverdue = $contribution && $contribution->status === 'overdue';
                                $isFuture = $cycle > $group->current_cycle;
                                
                                // Determine circle color and content
                                if ($isPaid) {
                                    $circleClass = 'bg-green-500 text-white';
                                    $icon = '✓';
                                    $title = "Cycle {$cycle}: Paid";
                                } elseif ($isOverdue) {
                                    $circleClass = 'bg-red-500 text-white';
                                    $icon = '!';
                                    $title = "Cycle {$cycle}: Overdue";
                                } elseif ($isPending) {
                                    $circleClass = 'bg-yellow-500 text-white';
                                    $icon = '⏳';
                                    $title = "Cycle {$cycle}: Pending";
                                } elseif ($isFuture) {
                                    $circleClass = 'bg-gray-300 dark:bg-gray-600 text-gray-600 dark:text-gray-400';
                                    $icon = $cycle;
                                    $title = "Cycle {$cycle}: Future";
                                } else {
                                    $circleClass = 'bg-gray-400 text-white';
                                    $icon = $cycle;
                                    $title = "Cycle {$cycle}: Not paid";
                                }
                            @endphp
                            
                            <div class="w-6 h-6 rounded-full flex items-center justify-center text-xs font-medium {{ $circleClass }}" 
                                 title="{{ $title }}">
                                @if($isPaid)
                                    ✓
                                @elseif($isOverdue)
                                    !
                                @elseif($isPending)
                                    ⏳
                                @else
                                    {{ $cycle }}
                                @endif
                            </div>
                        @endfor
                    </div>
                    
                    <!-- Legend -->
                    <div class="flex flex-wrap gap-3 mt-2 text-xs text-gray-600 dark:text-gray-400">
                        <div class="flex items-center gap-1">
                            <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                            <span>Paid</span>
                        </div>
                        <div class="flex items-center gap-1">
                            <div class="w-3 h-3 bg-yellow-500 rounded-full"></div>
                            <span>Pending</span>
                        </div>
                        <div class="flex items-center gap-1">
                            <div class="w-3 h-3 bg-red-500 rounded-full"></div>
                            <span>Overdue</span>
                        </div>
                        <div class="flex items-center gap-1">
                            <div class="w-3 h-3 bg-gray-400 rounded-full"></div>
                            <span>Future</span>
                        </div>
                    </div>
                </div>
                @endif
            </div>
            @endforeach
        </div>
        @else
        <p class="text-gray-600 dark:text-gray-400 text-center py-4">
            {{ __('general.no_members_yet') }}
        </p>
        @endif
    </div>
</div>