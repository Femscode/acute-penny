<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\GroupMember;
use App\Services\ContributionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class GroupController extends Controller
{
    protected $contributionService;
    public function __construct(ContributionService $contributionService)
    {
        $this->contributionService = $contributionService;
    }
    public function index(): View
    {
        $userGroups = Group::whereHas('members', function ($query) {
            $query->where('user_uuid', Auth::user()->uuid);
        })->with(['creator', 'members'])->latest()->get();

        $createdGroups = Group::where('created_by', Auth::user()->uuid)
            ->with(['members'])->latest()->get();

        return view('groups.index', compact('userGroups', 'createdGroups'));
    }

    public function create(): View
    {
        return view('groups.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'contribution_amount' => 'required|numeric|min:1',
            'frequency' => 'required|in:daily,weekly,monthly',
            'turn_format' => 'required|in:random,linear,manual',
            'privacy_type' => 'required|in:public,private',
            'requires_approval' => 'boolean',
            'max_members' => 'required|integer|min:2|max:50',
            'start_date' => 'required|date|after:today',
        ]);

        $group = Group::create([
            'name' => $request->name,
            'description' => $request->description,
            'contribution_amount' => $request->contribution_amount,
            'frequency' => $request->frequency,
            'turn_format' => $request->turn_format,
            'privacy_type' => $request->privacy_type,
            'requires_approval' => $request->boolean('requires_approval'),
            'max_members' => $request->max_members,
            'start_date' => $request->start_date,
            'created_by' => Auth::user()->uuid,
        ]);

        GroupMember::create([
            'group_uuid' => $group->uuid,
            'user_uuid' => Auth::user()->uuid,
            'joined_at' => now(),
            // 'payout_position' => 1,
        ]);

        $group->increment('current_members');

        return redirect()->route('groups.show', $group->uuid)
            ->with('success', __('general.group_created_success'));
    }

    public function startContribution(Group $group): RedirectResponse
    {
        $user = Auth::user();
        // Check if user is the group creator
        if ($group->created_by !== $user->uuid) {
            return back()->with('error', __('general.only_creator_can_start'));
        }

        // Check if contribution can be started
        if (!$group->canStartContribution()) {
            return back()->with('error', __('general.cannot_start_contribution'));
        }

        // Start the contribution
        if ($this->contributionService->startContribution($group)) {
            return back()->with('success', __('general.contribution_started_success'));
        }

        return back()->with('error', __('general.contribution_start_failed'));
    }

    public function spinWheel(Request $request, Group $group)
    {
        $request->validate([
            'selected_position' => 'required|integer|min:1'
        ]);

        try {
            $result = $this->contributionService->assignSelectedPosition(
                $group,
                Auth::user()->uuid,
                $request->selected_position
            );

            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function getAvailablePositions(Group $group)
    {
        try {
            $availablePositions = $this->contributionService->getAvailablePositions($group);

            return response()->json([
                'success' => true,
                'positions' => $availablePositions
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }
    public function browse(): View
    {
        $groups = Group::where('status', 'open')
            // ->where('current_members', '<', 'max_members')
            ->where('privacy_type', 'public')
            ->with(['creator', 'members'])
            ->latest()
            ->paginate(12);



        return view('groups.browse', compact('groups'));
    }

    public function show(Group $group): View
    {
        $group->load(['creator', 'members.user', 'contributions']);
        $userIsMember = $group->hasMember(Auth::user()->uuid);
        $canJoin = !$userIsMember && !$group->isFull() && $group->status === 'open';

        $viewName = match ($group->turn_format) {
            'random' => 'groups.random',
            'linear' => 'groups.linear',
            'manual' => 'groups.manual',
            default => 'groups.show'
        };

        return view($viewName, compact('group', 'userIsMember', 'canJoin'));
    }

    public function join(Group $group): RedirectResponse
    {

        if ($group->isFull()) {
            return back()->with('error', __('general.group_full_error'));
        }

        if ($group->hasMember(Auth::user()->uuid)) {
            return back()->with('error', __('general.already_member_error'));
        }

        if ($group->status !== 'open') {
            return back()->with('error', __('general.group_not_open_error'));
        }

        if ($group->requiresApproval()) {
            GroupMember::create([
                'group_uuid' => $group->uuid,
                'user_uuid' => Auth::user()->uuid,
                'status' => 'pending'
            ]);
            return redirect()->route('dashboard')->with('info', __('general.join_request_sent'));
        }

        GroupMember::create([
            'group_uuid' => $group->uuid,
            'user_uuid' => Auth::user()->uuid,
            'joined_at' => now(),
            // 'payout_position' => $group->current_members + 1,
        ]);

        $group->increment('current_members');

        return redirect()->route('groups.show', $group->uuid)
            ->with('success', __('general.joined_group_success'));
    }

    public function leave(Group $group): RedirectResponse
    {
        $membership = GroupMember::where('group_uuid', $group->uuid)
            ->where('user_uuid', Auth::user()->uuid)
            ->first();

        if (!$membership) {
            return back()->with('error', __('general.not_member_error'));
        }

        if ($group->created_by === Auth::user()->uuid && $group->current_members > 1) {
            return back()->with('error', __('general.creator_cannot_leave_error'));
        }

        $membership->delete();
        $group->decrement('current_members');

        return redirect()->route('dashboard')
            ->with('success', __('general.left_group_success'));
    }
    public function updateMemberPosition(Request $request, Group $group)
    {
        // Check if user is the group creator
        if ($group->created_by !== Auth::user()->uuid) {
            return back()->with('error', __('general.unauthorized_action'));
        }

        // Check if group uses manual turn format
        if ($group->turn_format !== 'manual') {
            return back()->with('error', __('general.group_not_manual_format'));
        }

        // Check if contribution has already started
        if ($group->isContributionStarted()) {
            return back()->with('error', __('general.cannot_change_positions_after_start'));
        }

        $request->validate([
            'member_uuid' => 'required|uuid',
            'position' => 'required|integer|min:1|max:' . $group->current_members,
        ]);

        if ($this->contributionService->updateMemberPosition($group, $request->member_uuid, $request->position)) {
            return back()->with('success', __('general.member_position_updated'));
        }

        return back()->with('error', __('general.failed_to_update_position'));
    }

    // ... existing code ...

    public function settings(Group $group): RedirectResponse|View      
    {
        // Check if user is the group creator
        if ($group->created_by !== Auth::user()->uuid) {
            abort(403, __('general.unauthorized_action'));
        }

        // Check if contribution has started
        if ($group->isContributionStarted()) {
            return redirect()->route('groups.show', $group->uuid)->with('error', __('general.cannot_edit_after_contribution_started'));
        }

        $pendingMembers = $group->members()->where('status', 'pending')->with('user')->get();

        return view('groups.settings', compact('group', 'pendingMembers'));
    }

    public function update(Request $request, Group $group): RedirectResponse
    {
        // Check if user is the group creator
        if ($group->created_by !== Auth::user()->uuid) {
            return back()->with('error', __('general.unauthorized_action'));
        }

        // Check if contribution has started
        if ($group->isContributionStarted()) {
            return back()->with('error', __('general.cannot_edit_after_contribution_started'));
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'contribution_amount' => 'required|numeric|min:100',
            'frequency' => 'required|in:daily,weekly,monthly',
            'turn_format' => 'required|in:random,linear,manual',
            'privacy_type' => 'required|in:public,private',
            'requires_approval' => 'boolean',
            'max_members' => 'required|integer|min:2|max:50',
            'start_date' => 'required|date|after:today',
        ]);

        $group->update([
            'name' => $request->name,
            'description' => $request->description,
            'contribution_amount' => $request->contribution_amount,
            'frequency' => $request->frequency,
            'turn_format' => $request->turn_format,
            'privacy_type' => $request->privacy_type,
            'requires_approval' => $request->boolean('requires_approval'),
            'max_members' => $request->max_members,
            'start_date' => $request->start_date,
        ]);

        return back()->with('success', __('general.group_updated_success'));
    }

    public function approveMember(Request $request, Group $group): RedirectResponse
    {
        // Check if user is the group creator
        if ($group->created_by !== Auth::user()->uuid) {
            return back()->with('error', __('general.unauthorized_action'));
        }

        $request->validate([
            'member_uuid' => 'required|uuid',
        ]);

        $membership = GroupMember::where('group_uuid', $group->uuid)
            ->where('user_uuid', $request->member_uuid)
            ->where('status', 'pending')
            ->first();

        if (!$membership) {
            return back()->with('error', __('general.member_not_found'));
        }

        if ($group->isFull()) {
            return back()->with('error', __('general.group_full_error'));
        }

        $membership->update([
            'status' => 'approved',
            'joined_at' => now(),
            'payout_position' => $group->current_members + 1,
        ]);

        $group->increment('current_members');

        return back()->with('success', __('general.member_approved_success'));
    }

    public function rejectMember(Request $request, Group $group): RedirectResponse
    {
        // Check if user is the group creator
        if ($group->created_by !== Auth::user()->uuid) {
            return back()->with('error', __('general.unauthorized_action'));
        }

        $request->validate([
            'member_uuid' => 'required|uuid',
        ]);

        $membership = GroupMember::where('group_uuid', $group->uuid)
            ->where('user_uuid', $request->member_uuid)
            ->where('status', 'pending')
            ->first();

        if (!$membership) {
            return back()->with('error', __('general.member_not_found'));
        }

         
        // $membership->update(['status' => 'rejected']);
        $membership->delete();

        return back()->with('success', __('general.member_rejected_success'));
    }
}
