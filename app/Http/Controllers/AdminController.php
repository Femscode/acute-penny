<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Group;
use App\Models\Contribution;
use App\Models\WithdrawalRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    /**
     * Display the admin dashboard.
     */
    public function dashboard()
    {
        $stats = [
            'total_users' => User::count(),
            'total_groups' => Group::count(),
            'total_transactions' => Contribution::count(),
            'pending_withdrawals' => WithdrawalRequest::where('status', 'pending')->count(),
            'total_amount_contributed' => Contribution::where('status', 'paid')->sum('amount'),
            'total_withdrawal_requests' => WithdrawalRequest::sum('net_amount'),
        ];


        return view('admin.dashboard', compact('stats'));
    }

    /**
     * Display all groups with management options.
     */
    public function groups()
    {
        $groups = Group::with(['creator', 'members'])
            ->withCount('members')
            ->paginate(15);

        return view('admin.groups.index', compact('groups'));
    }

    /**
     * Show group details with members and transactions.
     */
    public function showGroup(Group $group)
    {
        $group->load(['members', 'contributions.user']);
        
        $transactions = $group->contributions()
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.groups.show', compact('group', 'transactions'));
    }

    /**
     * Display all users.
     */
    public function users(Request $request)
    {
        $query = User::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $users = $query->withCount(['contributions', 'withdrawalRequests'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.users.index', compact('users'));
    }

    /**
     * Display all transactions.
     */
    public function transactions(Request $request)
    {
        $query = Contribution::with(['user', 'group']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('group_id')) {
            $query->where('group_id', $request->group_id);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $transactions = $query->orderBy('created_at', 'desc')
            ->paginate(25);

        $groups = Group::select('id', 'name')->get();

        return view('admin.transactions.index', compact('transactions', 'groups'));
    }

    /**
     * Display all withdrawal requests.
     */
    public function withdrawalRequests(Request $request)
    {
        $query = WithdrawalRequest::with(['user', 'group']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $withdrawalRequests = $query->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.withdrawal-requests.index', compact('withdrawalRequests'));
    }

    /**
     * Update withdrawal request status.
     */
    public function updateWithdrawalRequest(Request $request, WithdrawalRequest $withdrawalRequest)
    {
        $request->validate([
            'status' => 'required|in:pending,approved,rejected',
            'admin_notes' => 'nullable|string|max:500'
        ]);

        $withdrawalRequest->update([
            'status' => $request->status,
            'admin_notes' => $request->admin_notes,
            'processed_at' => now(),
            'processed_by' => auth()->id()
        ]);

        return redirect()->back()->with('success', 'Withdrawal request updated successfully.');
    }

    // ... existing code ...

    /**
     * Display transactions for a specific user.
     */
    public function userTransactions(Request $request, User $user)
    {
        $query = $user->contributions()->with(['group']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $transactions = $query->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.users.transactions', compact('user', 'transactions'));
    }

    /**
     * Display withdrawal requests for a specific user.
     */
    public function userWithdrawals(Request $request, User $user)
    {
        $query = $user->withdrawalRequests()->with(['group']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $withdrawals = $query->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.users.withdrawals', compact('user', 'withdrawals'));
    }

}