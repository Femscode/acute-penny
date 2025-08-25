<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Contribution;
use App\Services\AlatPayService;
use App\Services\NotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Exception;

class PaymentProcessingController extends Controller
{
    protected $alatPayService;
    protected $notificationService;

    public function __construct(AlatPayService $alatPayService, NotificationService $notificationService)
    {
        $this->alatPayService = $alatPayService;
        $this->notificationService = $notificationService;
    }

    /**
     * Process pending payment transactions
     */
    public function processPendingPayments(): JsonResponse
    {
        // Get contributions with pending payments that have transaction IDs
        $pendingPayments = Contribution::where('status', 'pending')
            ->whereNotNull('transactionId')
            // ->whereNotNull('payment_reference')
            ->where('created_at', '>=', now()->subDays(7)) // Only check payments from last 7 days
            ->orderBy('created_at')
            ->limit(50) // Process 50 payments at a time
            ->get();

        $processed = 0;
        $confirmed = 0;
        $failed = 0;

        foreach ($pendingPayments as $contribution) {
            try {
                $result = $this->checkPaymentStatus($contribution);
                
                if ($result['status_checked']) {
                    $processed++;
                    
                    if ($result['payment_confirmed']) {
                        $confirmed++;
                    }
                } else {
                    $failed++;
                }
            } catch (Exception $e) {
                Log::error('Failed to check payment status', [
                    'contribution_id' => $contribution->id,
                    'transaction_id' => $contribution->transactionId,
                    'error' => $e->getMessage()
                ]);
                $failed++;
            }
        }

        return response()->json([
            'success' => true,
            'processed' => $processed,
            'confirmed' => $confirmed,
            'failed' => $failed,
            'total' => $pendingPayments->count()
        ]);
    }

    /**
     * Check individual payment status
     */
    private function checkPaymentStatus(Contribution $contribution): array
    {
        try {
            $result = $this->alatPayService->checkTransactionStatus($contribution->transactionId);

            if ($result['status'] && isset($result['data']['status'])) {
                $paymentStatus = $result['data']['status'];

                if (strtolower($paymentStatus) === 'successful' || strtolower($paymentStatus) === 'completed') {
                    // Mark contribution as paid
                    $contribution->markAsPaid($contribution->payment_method);
                    
                    // Queue payment success notification
                    $this->notificationService->queuePaymentSuccessMail(
                        $contribution->user,
                        $contribution->group,
                        $contribution
                    );

                    Log::info('Payment confirmed via cron job', [
                        'contribution_id' => $contribution->id,
                        'transaction_id' => $contribution->transactionId,
                        'amount' => $contribution->amount
                    ]);

                    return [
                        'status_checked' => true,
                        'payment_confirmed' => true
                    ];
                } elseif (strtolower($paymentStatus) === 'failed' || strtolower($paymentStatus) === 'cancelled') {
                    // Log failed payment but don't change status yet (might be temporary)
                    Log::warning('Payment failed or cancelled', [
                        'contribution_id' => $contribution->id,
                        'transaction_id' => $contribution->transactionId,
                        'status' => $paymentStatus
                    ]);
                }
            }

            return [
                'status_checked' => true,
                'payment_confirmed' => false
            ];
        } catch (Exception $e) {
            Log::error('Error checking payment status', [
                'contribution_id' => $contribution->id,
                'transaction_id' => $contribution->transactionId,
                'error' => $e->getMessage()
            ]);

            return [
                'status_checked' => false,
                'payment_confirmed' => false
            ];
        }
    }

    /**
     * Process expired virtual accounts
     */
    public function processExpiredVirtualAccounts(): JsonResponse
    {
        $expiredAccounts = Contribution::where('status', 'pending')
            ->whereNotNull('virtual_account_data')
            ->where('created_at', '<=', now()->subHours(24)) // Virtual accounts typically expire in 24 hours
            ->get();

        $processed = 0;

        foreach ($expiredAccounts as $contribution) {
            try {
                $virtualAccountData = json_decode($contribution->virtual_account_data, true);
                
                if (isset($virtualAccountData['expiredAt'])) {
                    $expiryDate = \Carbon\Carbon::parse($virtualAccountData['expiredAt']);
                    
                    if ($expiryDate->isPast()) {
                        // Check one final time before marking as expired
                        $statusResult = $this->checkPaymentStatus($contribution);
                        
                        if (!$statusResult['payment_confirmed']) {
                            Log::info('Virtual account expired', [
                                'contribution_id' => $contribution->id,
                                'expired_at' => $expiryDate->toDateTimeString()
                            ]);
                        }
                        
                        $processed++;
                    }
                }
            } catch (Exception $e) {
                Log::error('Error processing expired virtual account', [
                    'contribution_id' => $contribution->id,
                    'error' => $e->getMessage()
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'processed' => $processed
        ]);
    }
}