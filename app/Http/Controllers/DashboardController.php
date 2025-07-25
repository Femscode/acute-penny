<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\GroupMember;
use App\Models\Contribution;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();

        // Check if user has joined any groups or created any groups
        $hasJoinedGroups = GroupMember::where('user_uuid', $user->uuid)->exists();
        $hasCreatedGroups = Group::where('created_by', $user->uuid)->exists();

        if ($hasJoinedGroups || $hasCreatedGroups) {
            return $this->memberDashboard();
        }

        return $this->newUserDashboard();
    }

    private function memberDashboard(): View
    {
        $user = Auth::user();

        // Get user's group memberships
        $userGroups = Group::whereHas('members', function ($query) use ($user) {
            $query->where('user_uuid', $user->uuid);
        })->with(['creator', 'members'])->get();

        // Get groups created by user
        $createdGroups = Group::where('created_by', $user->uuid)
            ->with(['members'])->get();

        // Calculate dashboard statistics
        $stats = $this->calculateUserStats($user, $userGroups, $createdGroups);

        // Get recent activity
        $recentActivity = $this->getRecentActivity($user);

        // Get pending payments
        $pendingPayments = $this->getPendingPayments($user);

        $pendingGroups = GroupMember::where('user_uuid', $user->uuid)
            ->where('status', 'pending')
            ->with('group')
            ->get();

        $payoutEligibleGroups = $this->getPayoutEligibleGroups($user);

        return view('dashboard.member', compact(
            'userGroups',
            'createdGroups',
            'stats',
            'recentActivity',
            'pendingGroups',
            'pendingPayments',
            'payoutEligibleGroups'
        ));
    }


    private function getPayoutEligibleGroups($user)
    {
        return Group::where('status', 'active')
            ->where('current_turn_user_uuid', $user->uuid)
            ->whereDoesntHave('withdrawalRequests', function ($query) use ($user) {
                $query->where('user_uuid', $user->uuid)
                    ->where('status', 'pending');
            })
            ->with(['members'])
            ->get()
            ->map(function ($group) {
                $group->payout_amount = $group->contribution_amount * $group->current_members;
                $group->service_charge = $group->payout_amount * 0.05;
                $group->net_amount = $group->payout_amount - $group->service_charge;
                return $group;
            });
    }
    private function getPendingPayments($user)
    {
        $now = now();
        $threeDaysFromNow = $now->copy()->addDays(3);

        return Contribution::where('user_uuid', $user->uuid)
            ->where('status', 'pending')
            ->where('due_date', '<=', $threeDaysFromNow)
            ->with(['group'])
            ->orderByRaw('CASE 
            WHEN due_date < ? THEN 1 
            WHEN due_date = ? THEN 2 
            ELSE 3 
        END', [$now->toDateString(), $now->toDateString()])
            ->orderBy('due_date', 'asc')
            ->get()
            ->map(function ($contribution) use ($now) {
                $contribution->is_overdue = $contribution->due_date->isPast();
                $contribution->is_due_today = $contribution->due_date->isToday();
                $contribution->days_until_due = $contribution->due_date->diffInDays($now, false);
                return $contribution;
            });
    }

    private function newUserDashboard(): View
    {
        // Get some public groups for browsing suggestions
        $suggestedGroups = Group::where('status', 'open')
            // ->where('current_members', '<', 'max_members')
            ->where('privacy_type', 'public')
            ->with(['creator'])
            ->latest()
            ->limit(6)
            ->get();

        return view('dashboard.new-user', compact('suggestedGroups'));
    }

    private function calculateUserStats($user, $userGroups, $createdGroups)
    {
        $totalContributions = Contribution::where('user_uuid', $user->uuid)
            ->where('status', 'paid')
            ->sum('amount');

        $pendingContributions = Contribution::where('user_uuid', $user->uuid)
            ->where('status', 'pending')
            ->sum('amount');

        $overdueContributions = Contribution::where('user_uuid', $user->uuid)
            ->where('status', 'overdue')
            ->sum('amount');

        $totalGroups = $userGroups->count() + $createdGroups->count();

        // Calculate next payout date
        $nextPayout = $this->getNextPayoutDate($user);

        return [
            'total_paid' => $totalContributions,
            'pending_amount' => $pendingContributions,
            'overdue_amount' => $overdueContributions,
            'total_groups' => $totalGroups,
            'groups_joined' => $userGroups->count(),
            'groups_created' => $createdGroups->count(),
            'next_payout' => $nextPayout,
        ];
    }

    private function getRecentActivity($user)
    {
        // Get recent contributions
        $recentContributions = Contribution::where('user_uuid', $user->uuid)
            ->with(['group'])
            ->latest()
            ->limit(5)
            ->get();

        // Get recent group joins
        $recentJoins = GroupMember::where('user_uuid', $user->uuid)
            ->with(['group'])
            ->latest('joined_at')
            ->limit(3)
            ->get();

        return [
            'contributions' => $recentContributions,
            'group_joins' => $recentJoins,
        ];
    }

    private function getNextPayoutDate($user)
    {
        $nextPayout = GroupMember::where('user_uuid', $user->uuid)
            ->join('groups', 'group_members.group_uuid', '=', 'groups.uuid')
            ->where('groups.status', 'active')
            ->orderBy('payout_position')
            ->first();

        if ($nextPayout) {
            // Calculate next payout based on group frequency and position
            $group = Group::where('uuid', $nextPayout->group_uuid)->first();
            if ($group) {
                return $this->calculateNextPayoutDate($group, $nextPayout->payout_position);
            }
        }

        return null;
    }

    private function calculateNextPayoutDate($group, $position)
    {
        $startDate = \Carbon\Carbon::parse($group->start_date);
        $frequency = $group->frequency;

        switch ($frequency) {
            case 'daily':
                return $startDate->addDays($position - 1);
            case 'weekly':
                return $startDate->addWeeks($position - 1);
            case 'monthly':
                return $startDate->addMonths($position - 1);
            default:
                return null;
        }
    }
}
