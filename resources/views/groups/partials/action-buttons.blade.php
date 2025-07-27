@if(!$isAuthenticated)
<!-- Login/Signup buttons for unauthenticated users -->
<div class="mt-6 flex justify-center space-x-4">
    <a href="{{ route('login') }}" 
       class="inline-flex items-center px-6 py-3 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
        {{ __('general.login') }}
    </a>
    <a href="{{ route('register') }}" 
       class="inline-flex items-center px-6 py-3 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white focus:bg-gray-700 dark:focus:bg-white active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
        {{ __('general.signup_to_join_group') }}
    </a>
</div>
@elseif($canJoin)
<div class="mt-6 flex justify-center" x-data="{ groupId: {{ $group->id }} }">
    <form method="POST" action="{{ route('groups.join', $group) }}" id="join-group-{{ $group->id }}">
        @csrf
        <button
            type="button"
            class="inline-flex items-center px-8 py-3 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white focus:bg-gray-700 dark:focus:bg-white active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150"
            @click="$dispatch('open-modal', 'join-group-' + groupId)">
            {{ __('general.join_this_group') }}
        </button>
    </form>
</div>
@elseif($userIsMember)
<div class="mt-6 flex justify-center space-x-4">
    <span class="inline-flex items-center px-4 py-2 bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200 rounded-md text-sm font-medium">
        ✓ {{ __('general.you_are_member') }}
    </span>
    @if($group->created_by === Auth::user()->uuid && !$group->isContributionStarted())
    <a href="{{ route('groups.settings', $group->uuid) }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
        {{ __('general.group_settings') }}
    </a>
    @endif
    @if($group->created_by !== auth()->user()->uuid && $group->current_members > 1)
    <div x-data="{ groupId: {{ $group->id }} }">
        <form method="POST" action="{{ route('groups.leave', $group) }}" id="leave-group-{{ $group->id }}">
            @csrf
            @method('DELETE')
            <button
                type="button"
                class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-500 active:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150"
                @click="$dispatch('open-modal', 'leave-group-{{ $group->id }}')">
                {{ __('general.leave_group') }}
            </button>
        </form>
    </div>
    @endif
</div>
@endif