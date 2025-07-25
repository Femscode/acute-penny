<?php

namespace App\Services;

use App\Models\Group;
use App\Models\GroupMember;
use App\Models\Contribution;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ContributionService
{

    public function startContribution(Group $group): bool
    {
        if (!$group->canStartContribution()) {
            return false;
        }

        // For manual turn format, check if all positions are assigned
        if ($group->turn_format === 'manual' && !$this->allPositionsAssigned($group)) {
            throw new \Exception(__('general.manual_positions_not_assigned'));
        }

        return DB::transaction(function () use ($group) {
            // Assign turns based on turn format (skip for manual AND random)
            if ($group->turn_format === 'linear') {
                $this->assignTurns($group);
            }
            // For random groups, positions will be assigned when users spin

            // Update group status
            $group->update([
                'status' => 'active',
                'contribution_started_at' => $group->start_date,
                'current_cycle' => 1,
                // For random groups, current_turn_user_uuid will be set after first user spins
                'current_turn_user_uuid' => $group->turn_format !== 'random' ? $this->getFirstTurnUser($group)->user_uuid : null,
            ]);

            // Create initial contributions for all members
            $this->createInitialContributions($group);

            return true;
        });
    }

    private function assignTurns(Group $group): void
    {
        $members = $group->members()->orderBy('joined_at')->get();

        if ($group->turn_format === 'random') {
            $positions = range(1, $members->count());
            shuffle($positions);
        } else { // linear
            $positions = range(1, $members->count());
        }

        foreach ($members as $index => $member) {
            $member->update(['payout_position' => $positions[$index]]);
        }
    }

    // Add new method for wheel spinning
    public function spinWheelForUser(Group $group, $userUuid): array
    {
        // Check if group is active and uses random turn format
        if ($group->status !== 'active' || $group->turn_format !== 'random') {
            throw new \Exception(__('general.invalid_spin_conditions'));
        }

        // Get the member
        $member = $group->members()->where('user_uuid', $userUuid)->first();
        if (!$member) {
            throw new \Exception(__('general.member_not_found'));
        }

        // Check if user has already rolled
        if ($member->is_rolled) {
            throw new \Exception(__('general.already_rolled'));
        }

        // Check if user has a payout position assigned
        if (!$member->payout_position) {
            throw new \Exception(__('general.no_position_assigned'));
        }

        return DB::transaction(function () use ($member) {
            // Mark as rolled
            $member->update(['is_rolled' => true]);

            return [
                'success' => true,
                'position' => $member->payout_position,
                'message' => __('general.turn_position_assigned', ['position' => $member->payout_position])
            ];
        });
    }


    public function oldstartContribution(Group $group): bool
    {
        if (!$group->canStartContribution()) {
            return false;
        }

        // For manual turn format, check if all positions are assigned
        if ($group->turn_format === 'manual' && !$this->allPositionsAssigned($group)) {
            throw new \Exception(__('general.manual_positions_not_assigned'));
        }

        return DB::transaction(function () use ($group) {
            // Assign turns based on turn format (skip for manual as positions are pre-assigned)
            if ($group->turn_format !== 'manual') {
                $this->assignTurns($group);
            }

            // Update group status
            $group->update([
                'status' => 'active',
                'contribution_started_at' => $group->start_date,
                'current_cycle' => 1,
                'current_turn_user_uuid' => $this->getFirstTurnUser($group)->user_uuid,
            ]);

            // Create initial contributions for all members
            $this->createInitialContributions($group);

            return true;
        });
    }

    private function oldassignTurns(Group $group): void
    {
        $members = $group->members()->orderBy('joined_at')->get();

        if ($group->turn_format === 'random') {
            $positions = range(1, $members->count());
            shuffle($positions);
        } else { // linear
            $positions = range(1, $members->count());
        }

        foreach ($members as $index => $member) {
            $member->update(['payout_position' => $positions[$index]]);
        }
    }

    private function allPositionsAssigned(Group $group): bool
    {
        $memberCount = $group->members()->count();
        $assignedPositions = $group->members()->whereNotNull('payout_position')->count();

        // Check if all members have positions assigned
        if ($assignedPositions !== $memberCount) {
            return false;
        }

        // Check if positions are sequential from 1 to member count
        $positions = $group->members()->pluck('payout_position')->sort()->values()->toArray();
        $expectedPositions = range(1, $memberCount);

        return $positions === $expectedPositions;
    }

    public function updateMemberPosition(Group $group, $memberUuid, $position): bool
    {
        // Validate that the group uses manual turn format
        if ($group->turn_format !== 'manual') {
            return false;
        }

        // Validate position is within valid range
        if ($position < 1 || $position > $group->current_members) {
            return false;
        }

        return DB::transaction(function () use ($group, $memberUuid, $position) {
            // Get the member to update
            $member = $group->members()->where('user_uuid', $memberUuid)->first();
            if (!$member) {
                return false;
            }

            // Get current member at the target position
            $existingMember = $group->members()->where('payout_position', $position)->first();

            if ($existingMember && $existingMember->user_uuid !== $memberUuid) {
                // Swap positions
                $oldPosition = $member->payout_position;
                $member->update(['payout_position' => $position]);
                $existingMember->update(['payout_position' => $oldPosition]);
            } else {
                // Just update the position
                $member->update(['payout_position' => $position]);
            }

            return true;
        });
    }

    public function assignSelectedPosition(Group $group, $userUuid, $selectedPosition): array
    {
        // Check if group is active and uses random turn format
        if ($group->status !== 'active' || $group->turn_format !== 'random') {
            throw new \Exception(__('general.invalid_spin_conditions'));
        }

        // Get the member
        $member = $group->members()->where('user_uuid', $userUuid)->first();
        if (!$member) {
            throw new \Exception(__('general.member_not_found'));
        }

        // Check if user has already rolled
        if ($member->is_rolled) {
            throw new \Exception(__('general.already_rolled'));
        }

        // Verify the selected position is available
        $availablePositions = $this->getAvailablePositions($group);
        if (!in_array($selectedPosition, $availablePositions)) {
            throw new \Exception(__('general.position_not_available'));
        }

        return DB::transaction(function () use ($member, $selectedPosition) {
            // Assign the selected position and mark as rolled
            $member->update([
                'payout_position' => $selectedPosition,
                'is_rolled' => true
            ]);

            return [
                'success' => true,
                'position' => $selectedPosition,
                'message' => __('general.turn_position_assigned', ['position' => $selectedPosition])
            ];
        });
    }

    public function getAvailablePositions(Group $group): array
    {
        if ($group->status !== 'active' || $group->turn_format !== 'random') {
            throw new \Exception(__('general.invalid_spin_conditions'));
        }

        // Get all positions that haven't been assigned yet
        $assignedPositions = $group->members()
            ->whereNotNull('payout_position')
            ->pluck('payout_position')
            ->toArray();

        $totalPositions = range(1, $group->current_members);
        $availablePositions = array_diff($totalPositions, $assignedPositions);

        return array_values($availablePositions);
    }

    private function getFirstTurnUser(Group $group): GroupMember
    {
        return $group->members()->where('payout_position', 1)->first();
    }

    private function createInitialContributions(Group $group): void
    {
        $members = $group->members()->get();
        $dueDate = $this->calculateNextDueDate($group);

        foreach ($members as $member) {
            Contribution::create([
                'group_uuid' => $group->uuid,
                'user_uuid' => $member->user_uuid,
                'amount' => $group->contribution_amount,
                'due_date' => $dueDate,
                'status' => 'pending',
            ]);
        }
    }

    private function calculateNextDueDate(Group $group): Carbon
    {
        $startDate = Carbon::parse($group->contribution_started_at);

        switch ($group->frequency) {
            case 'daily':
                return $startDate->addDay();
            case 'weekly':
                return $startDate->addWeek();
            case 'monthly':
                return $startDate->addMonth();
            default:
                return $startDate->addWeek();
        }
    }

    public function advanceToNextTurn(Group $group): void
    {
        $nextPosition = ($group->current_cycle % $group->current_members) + 1;
        $nextUser = $group->members()->where('payout_position', $nextPosition)->first();

        if ($nextUser) {
            $group->update([
                'current_turn_user_uuid' => $nextUser->user_uuid,
                'current_cycle' => $group->current_cycle + 1,
            ]);

            // Create next round of contributions
            $this->createNextRoundContributions($group);
        }
    }

    private function createNextRoundContributions(Group $group): void
    {
        $members = $group->members()->get();
        $dueDate = $this->calculateNextDueDate($group);

        foreach ($members as $member) {
            Contribution::create([
                'group_uuid' => $group->uuid,
                'user_uuid' => $member->user_uuid,
                'amount' => $group->contribution_amount,
                'due_date' => $dueDate,
                'status' => 'pending',
            ]);
        }
    }
}
