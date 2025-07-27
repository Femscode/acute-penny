<?php

namespace App\Http\Controllers;

use App\Mail\ContributionStartedMail;
use App\Mail\GroupMembershipMail;
use App\Mail\WelcomeMail;
use App\Models\MailNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class MailProcessingController extends Controller
{
    public function processPendingMails(): JsonResponse
    {
        $pendingMails = MailNotification::where('status', 'pending')
            ->orWhere(function ($query) {
                $query->where('status', 'failed')
                      ->where('retry_count', '<', 3);
            })
            ->orderBy('created_at')
            ->limit(50) // Process 50 emails at a time
            ->get();

        $processed = 0;
        $failed = 0;

        foreach ($pendingMails as $mailNotification) {
            try {
                $this->sendMail($mailNotification);
                $mailNotification->markAsSent();
                $processed++;
            } catch (\Exception $e) {
                dd($e->getMessage());
                Log::error('Failed to send mail notification', [
                    'mail_id' => $mailNotification->id,
                    'error' => $e->getMessage()
                ]);
                
                $mailNotification->markAsFailed($e->getMessage());
                $failed++;
            }
        }

        return response()->json([
            'success' => true,
            'processed' => $processed,
            'failed' => $failed,
            'total' => $pendingMails->count()
        ]);
    }

    private function sendMail(MailNotification $mailNotification): void
    {
        $user = $mailNotification->user;
        $mailData = $mailNotification->mail_data;
        $language = $mailNotification->language;

        $mail = match($mailNotification->mail_type) {
            'welcome' => new WelcomeMail($user, $language),
            'group_membership' => new GroupMembershipMail(
                $user,
                \App\Models\Group::where('uuid', $mailData['group_uuid'])->first(),
                $mailData['action'],
                $language
            ),
            'contribution_started' => new ContributionStartedMail(
                $user,
                \App\Models\Group::where('uuid', $mailData['group_uuid'])->first(),
                $mailData['user_turn_info'],
                $language
            ),
            default => throw new \Exception('Unknown mail type: ' . $mailNotification->mail_type)
        };

        Mail::to($user->email)->send($mail);
    }
}