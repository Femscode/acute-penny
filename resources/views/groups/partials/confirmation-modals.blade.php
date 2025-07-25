@if($canJoin)
<x-confirmation-modal
    name="join-group-{{ $group->id }}"
    :title="__('general.confirm_join_group')"
    :message="__('general.join_group_warning', ['amount' => number_format($group->contribution_amount, 2), 'frequency' => $group->frequency])"
    :confirm-text="__('general.join_this_group')"
    :cancel-text="__('general.cancel')"
    confirm-class="bg-green-600 hover:bg-green-700"
    x-on:confirm-action="if ($event.detail === 'join-group-{{ $group->id }}') { document.getElementById('join-form-{{ $group->id }}').submit(); }" />
@endif

@if($userIsMember && $group->created_by !== auth()->user()->uuid && $group->current_members > 1)
<x-confirmation-modal
    name="leave-group-{{ $group->id }}"
    :title="__('general.confirm_leave_group')"
    :message="__('general.leave_group_warning')"
    :confirm-text="__('general.leave_group')"
    :cancel-text="__('general.cancel')"
    confirm-class="bg-red-600 hover:bg-red-700"
    x-on:confirm-action="if ($event.detail === 'leave-group-{{ $group->id }}') { document.getElementById('leave-form-{{ $group->id }}').submit(); }" />
@endif