<?php

namespace App\Http\Controllers;

use App\Mail\ContributionStartedMail;
use App\Mail\GroupMembershipConfirmationMail;
use App\Mail\GroupMembershipMail;
use App\Mail\PaymentSuccessMail;
use App\Mail\WelcomeMail;
use App\Mail\WithdrawalRequestMail;
use App\Models\Contribution;
use App\Models\Group;
use App\Models\MailNotification;
use App\Models\User;
use App\Models\WithdrawalRequest;
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

  

    private function oldsendMail(MailNotification $mailNotification): void
    {
        $user = $mailNotification->user;
        $mailData = $mailNotification->mail_data;
        $language = $mailNotification->language;

        $mail = match ($mailNotification->mail_type) {
            'welcome' => new WelcomeMail($user, $language),
            'group_membership' => new GroupMembershipMail(
                $user,
                \App\Models\Group::where('uuid', $mailData['group_uuid'])->first(),
                $mailData['action'],
                $language
            ),
            'group_membership_confirmation' => new GroupMembershipConfirmationMail(
                $user,
                \App\Models\Group::where('uuid', $mailData['group_uuid'])->first(),
                $mailData['action'],
                $language,
                $mailData
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

    // ... existing code ...

    private function newsendMail(MailNotification $mailNotification): bool
    {
        try {
            $user = $mailNotification->user;
            $mailData = $mailNotification->mail_data;
            $language = $mailNotification->language;

            $mail = match($mailNotification->mail_type) {
                'welcome' => new WelcomeMail($user, $language),
                'group_membership' => new GroupMembershipMail(
                    User::where('uuid', $mailData['action_user_name'])->first() ?? $user,
                    Group::where('uuid', $mailData['group_uuid'])->first(),
                    $mailData['action'],
                    $language
                ),
                'group_membership_confirmation' => new GroupMembershipConfirmationMail(
                    $user,
                    Group::where('uuid', $mailData['group_uuid'])->first(),
                    $mailData['action'],
                    $language,
                    $mailData
                ),
                'contribution_started' => new ContributionStartedMail(
                    $user,
                    Group::where('uuid', $mailData['group_uuid'])->first(),
                    $mailData['user_turn_info'],
                    $language
                ),
                'payment_success' => new PaymentSuccessMail(
                    $user,
                    Group::where('uuid', $mailData['group_uuid'])->first(),
                    (object) $mailData, // Convert array to object for template compatibility
                    $language
                ),
                'withdrawal_request' => new WithdrawalRequestMail(
                    $user,
                    Group::where('uuid', $mailData['group_uuid'])->first(),
                    (object) $mailData, // Convert array to object for template compatibility
                    $language
                ),
                default => throw new \Exception("Unknown mail type: {$mailNotification->mail_type}")
            };

            Mail::to($user->email)->send($mail);
            return true;
        } catch (\Exception $e) {
            dd($e->getMessage());
            \Log::error('Failed to send mail: ' . $e->getMessage(), [
                'mail_notification_id' => $mailNotification->id,
                'mail_type' => $mailNotification->mail_type
            ]);
            return false;
        }
    }

    private function sendMail(MailNotification $mailNotification): bool
    {
        try {
            $user = $mailNotification->user;
            $mailData = $mailNotification->mail_data;
            $language = $mailNotification->language;

            $mail = match($mailNotification->mail_type) {
                'welcome' => new WelcomeMail($user, $language),
                'group_membership' => new GroupMembershipMail(
                    User::where('uuid', $mailData['action_user_name'])->first() ?? $user,
                    Group::where('uuid', $mailData['group_uuid'])->first(),
                    $mailData['action'],
                    $language
                ),
                'group_membership_confirmation' => new GroupMembershipConfirmationMail(
                    $user,
                    Group::where('uuid', $mailData['group_uuid'])->first(),
                    $mailData['action'],
                    $language,
                    $mailData
                ),
                'contribution_started' => new ContributionStartedMail(
                    $user,
                    Group::where('uuid', $mailData['group_uuid'])->first(),
                    $mailData['user_turn_info'],
                    $language
                ),
                'payment_success' => $this->createPaymentSuccessMail($user, $mailData, $language),
                'withdrawal_request' => $this->createWithdrawalRequestMail($user, $mailData, $language),
                default => throw new \Exception("Unknown mail type: {$mailNotification->mail_type}")
            };

            Mail::to($user->email)->send($mail);
            return true;
        } catch (\Exception $e) {
            \Log::error('Failed to send mail: ' . $e->getMessage(), [
                'mail_notification_id' => $mailNotification->id,
                'mail_type' => $mailNotification->mail_type,
                'error_trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }

    private function createPaymentSuccessMail(User $user, array $mailData, string $language): PaymentSuccessMail
    {
        $group = Group::where('uuid', $mailData['group_uuid'])->first();
        
        // Create a mock Contribution object with the data we have
        $contribution = new Contribution();
        $contribution->amount = $mailData['contribution_amount'];
        $contribution->payment_method = $mailData['payment_method'];
        $contribution->transactionId = $mailData['transaction_id'] ?? null;
        $contribution->updated_at = now(); // Use current time if not available
        $contribution->user = $user;
        $contribution->group = $group;
        
        return new PaymentSuccessMail($user, $group, $contribution, $language);
    }

    private function createWithdrawalRequestMail(User $user, array $mailData, string $language): WithdrawalRequestMail
    {
        $group = Group::where('uuid', $mailData['group_uuid'])->first();
        
        // Create a mock WithdrawalRequest object with the data we have
        $withdrawalRequest = new WithdrawalRequest();
        $withdrawalRequest->gross_amount = $mailData['gross_amount'];
        $withdrawalRequest->service_charge = $mailData['service_charge'];
        $withdrawalRequest->net_amount = $mailData['net_amount'];
        $withdrawalRequest->bank_name = $mailData['bank_name'];
        $withdrawalRequest->account_number = $mailData['account_number'];
        $withdrawalRequest->account_name = $mailData['account_name'];
        $withdrawalRequest->user = $user;
        $withdrawalRequest->group = $group;
        
        return new WithdrawalRequestMail($user, $group, $withdrawalRequest, $language);
    }


}
