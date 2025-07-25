<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\GroupMember;
use App\Models\WithdrawalRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class WithdrawalRequestController extends Controller
{
    public function create(Group $group)
    {
        $user = Auth::user();
        
        // Check if user is eligible for payout
        if (!$this->isEligibleForPayout($group, $user)) {
            return redirect()->back()->with('error', 'You are not eligible for payout at this time.');
        }

        // Check if user already has a pending withdrawal request for this group
        $existingRequest = WithdrawalRequest::where('user_uuid', $user->uuid)
            ->where('group_uuid', $group->uuid)
            ->where('status', 'pending')
            ->first();

        if ($existingRequest) {
            return redirect()->back()->with('error', 'You already have a pending withdrawal request for this group.');
        }

        $payoutAmount = $this->calculatePayoutAmount($group);
        $serviceCharge = WithdrawalRequest::calculateServiceCharge($payoutAmount);
        $netAmount = WithdrawalRequest::calculateNetAmount($payoutAmount);

        return view('withdrawal-requests.create', compact('group', 'user', 'payoutAmount', 'serviceCharge', 'netAmount'));
    }

    public function store(Request $request, Group $group)
    {
        $user = Auth::user();
        
        // Validate request
        $request->validate([
            'bank_name' => 'required|string|max:255',
            'account_number' => 'required|string|max:20',
            'account_name' => 'required|string|max:255',
            'notes' => 'nullable|string|max:500'
        ]);

        // Check eligibility again
        if (!$this->isEligibleForPayout($group, $user)) {
            return redirect()->back()->with('error', 'You are not eligible for payout at this time.');
        }

        // Check for existing pending request
        $existingRequest = WithdrawalRequest::where('user_uuid', $user->uuid)
            ->where('group_uuid', $group->uuid)
            ->where('status', 'pending')
            ->first();

        if ($existingRequest) {
            return redirect()->back()->with('error', 'You already have a pending withdrawal request for this group.');
        }

        $payoutAmount = $this->calculatePayoutAmount($group);
        $serviceCharge = WithdrawalRequest::calculateServiceCharge($payoutAmount);
        $netAmount = WithdrawalRequest::calculateNetAmount($payoutAmount);

        // Create withdrawal request
        WithdrawalRequest::create([
            'user_uuid' => $user->uuid,
            'group_uuid' => $group->uuid,
            'gross_amount' => $payoutAmount,
            'service_charge' => $serviceCharge,
            'net_amount' => $netAmount,
            'bank_name' => $request->bank_name,
            'account_number' => $request->account_number,
            'account_name' => $request->account_name,
            'notes' => $request->notes
        ]);

        return redirect()->route('groups.show', $group)
            ->with('success', 'Withdrawal request submitted successfully! You will receive ₦' . number_format($netAmount, 2) . ' after processing.');
    }

    private function isEligibleForPayout(Group $group, $user)
    {
        // Check if group is active
        if ($group->status !== 'active') {
            return false;
        }

        // Check if user is a member
        $member = $group->members()->where('user_uuid', $user->uuid)->first();
        if (!$member) {
            return false;
        }

        // Check if it's user's turn to receive payout
        return $group->current_turn_user_uuid === $user->uuid;
    }

    private function calculatePayoutAmount(Group $group)
    {
        // Calculate total payout (contribution amount * number of members)
        return $group->contribution_amount * $group->current_members;
    }
}