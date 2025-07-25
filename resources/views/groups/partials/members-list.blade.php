<!-- Members List -->
<div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
    <div class="p-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">{{ __('general.group_members') }}</h3>

        @if($group->members->count() > 0)
        <div class="space-y-3">
            @foreach($group->members as $member)
            <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
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
            @endforeach
        </div>
        @else
        <p class="text-gray-600 dark:text-gray-400 text-center py-4">
            {{ __('general.no_members_yet') }}
        </p>
        @endif
    </div>
</div>