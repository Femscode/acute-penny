<?php

namespace App\Services;

use App\Models\Group;
use App\Models\MailNotification;
use App\Models\User;
use Illuminate\Support\Facades\App;

class NotificationService
{
    public function queueWelcomeMail(User $user): void
    {
        $language = $user->preferred_language ?? 'en';
        
        MailNotification::create([
            'user_uuid' => $user->uuid,
            'mail_type' => 'welcome',
            'subject' => __('mail.welcome_subject', [], $language),
            'message_content' => __('mail.welcome_message', [], $language),
            'language' => $language,
            'mail_data' => [
                'user_name' => $user->name,
                'user_email' => $user->email
            ]
        ]);
    }

    public function queueGroupMembershipMail(User $user, Group $group, string $action): void
    {
        $language = $user->preferred_language ?? 'en';
        $subjectKey = $action === 'joined' ? 'mail.member_joined_subject' : 'mail.member_left_subject';
        
        // Notify all other group members
        $group->members()->where('user_uuid', '!=', $user->uuid)->each(function ($member) use ($user, $group, $action, $subjectKey) {
            $memberLanguage = $member->user->preferred_language ?? 'en';
            
            MailNotification::create([
                'user_uuid' => $member->user_uuid,
                'mail_type' => 'group_membership',
                'subject' => __($subjectKey, ['group_name' => $group->name], $memberLanguage),
                'message_content' => __('mail.member_' . $action . '_message', [], $memberLanguage),
                'language' => $memberLanguage,
                'mail_data' => [
                    'action_user_name' => $user->name,
                    'group_name' => $group->name,
                    'group_uuid' => $group->uuid,
                    'action' => $action
                ]
            ]);
        });
    }

    public function queueContributionStartedMails(Group $group): void
    {
        $group->members()->with('user')->each(function ($member) use ($group) {
            $user = $member->user;
            $language = $user->preferred_language ?? 'en';
            
            // Calculate user's turn information
            $userTurnInfo = $this->calculateUserTurnInfo($group, $member);
            
            MailNotification::create([
                'user_uuid' => $user->uuid,
                'mail_type' => 'contribution_started',
                'subject' => __('mail.contribution_started_subject', ['group_name' => $group->name], $language),
                'message_content' => __('mail.contribution_started_message', ['group_name' => $group->name], $language),
                'language' => $language,
                'mail_data' => [
                    'group_name' => $group->name,
                    'group_uuid' => $group->uuid,
                    'user_turn_info' => $userTurnInfo,
                    'contribution_amount' => $group->contribution_amount,
                    'frequency' => $group->frequency,
                    'start_date' => $group->start_date->format('M d, Y')
                ]
            ]);
        });
    }

    private function calculateUserTurnInfo(Group $group, $member): array
    {
        // This should match your existing logic for calculating turn dates
        $position = $member->payout_position ?? 1;
        $startDate = $group->start_date;
        
        // Calculate turn date based on frequency and position
        $turnDate = match($group->frequency) {
            'daily' => $startDate->addDays($position - 1),
            'weekly' => $startDate->addWeeks($position - 1),
            'monthly' => $startDate->addMonths($position - 1),
            default => $startDate
        };
        
        return [
            'position' => $position,
            'turn_date' => $turnDate->format('M d, Y'),
            'payout_amount' => $group->contribution_amount * $group->current_members
        ];
    }
}