<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Contribution;
use App\Models\Group;
use App\Services\AlatPayService;
use App\Services\ContributionService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    protected $alatPayService;

    public function __construct(AlatPayService $alatPayService)
    {
        $this->alatPayService = $alatPayService;
    }

    /**
     * Show payment options for a contribution
     */
    public function showPaymentOptions(Contribution $contribution)
    {
        $user = Auth::user();

        // Ensure user can pay this contribution
        if ($contribution->user_uuid !== $user->uuid) {
            abort(403, 'Unauthorized');
        }

        if ($contribution->status === 'paid') {
            return redirect()->back()->with('info', 'This contribution has already been paid.');
        }

        return view('payments.options', compact('contribution'));
    }

    /**
     * Initialize card payment
     */
    public function initializeCardPayment(Request $request, Contribution $contribution)
    {

        $request->validate([
            'card_number' => 'required|string|min:16'
        ]);

        try {
            $result = $this->alatPayService->initializeCard(
                $request->card_number,
                'NGN'
            );

            if (isset($result['gatewayRecommendation']) && $result['gatewayRecommendation'] === 'PROCEED') {
                return response()->json([
                    'success' => true,
                    'data' => $result,
                    'message' => 'Card validation successful. Please complete payment details.'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Card validation failed. Please try a different card.'
                ], 400);
            }
        } catch (Exception $e) {

            return response()->json([
                'success' => false,
                'message' => 'Payment initialization failed. Please try again.'
            ], 500);
        }
    }

    public function showCardForm(Group $group)
    {
        // Ensure the group has a current turn user
        if (!$group->currentTurnUser) {
            return redirect()->route('groups.show', $group)
                ->with('error', 'No active contribution found for this group.');
        }

        return view('payments.card-form', compact('group'));
    }
    /**
     * Process card payment
     */
    public function processCardPayment(Request $request, $contribution)
    {

        $request->validate([
            'card_number' => 'required|string',
            'card_month' => 'required|string|size:2',
            'card_year' => 'required|string|size:2',
            'security_code' => 'required|string|size:3',
            'transaction_id' => 'required|string'
        ]);

        $user = Auth::user();
        // $contribution = Contribution::where('uuid',$contribution)->where('user_uuid', $user->uuid)->first();
        // dd($contribution);
        $orderId = 'CONTRIB_' . $contribution->uuid . '_' . time();

        try {
            $cardData = [
                'cardNumber' => $request->card_number,
                'cardMonth' => $request->card_month,
                'cardYear' => $request->card_year,
                'securityCode' => $request->security_code,
                'amount' => $contribution->amount,
                'currency' => 'NGN',
                'customer' => [
                    'email' => $user->email,
                    'phone' => $user->phone ?? '',
                    'firstName' => explode(' ', $user->name)[0] ?? '',
                    'lastName' => explode(' ', $user->name)[1] ?? ''
                ]
            ];

            $result = $this->alatPayService->authenticateCard(
                $cardData,
                $request->transaction_id,
                $orderId
            );

            // Store payment reference
            $contribution->update([
                'payment_reference' => $orderId,
                'transactionId' => $result['transactionId'],
                'payment_method' => 'alatpay_card'
            ]);

            return response()->json([
                'success' => true,
                'data' => $result,
                'redirect_html' => $result['redirectHtml'] ?? null
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Payment processing failed. Please try again.'
            ], 500);
        }
    }

    /**
     * Generate virtual account for bank transfer
     */
    public function generateVirtualAccount($contribution)
    {

        $user = Auth::user();
        $contribution = Contribution::where('group_uuid', $contribution)->where('user_uuid', $user->uuid)->first();

        $orderId = 'CONTRIB_BANK_' . $contribution->uuid;


        try {
            $customer = [
                'email' => $user->email,
                'phone' => $user->phone ?? '09058744473',
                'firstName' => explode(' ', $user->name)[0] ?? '',
                'lastName' => explode(' ', $user->name)[1] ?? 'Fasanya',
                'metadata' => json_encode(['contribution_id' => $contribution->uuid])
            ];


            $result = $this->alatPayService->generateVirtualAccount(
                $contribution->amount,
                $orderId,
                $customer,
                'Contribution Payment for Group: ' . $contribution->group->name
            );




            if ($result['status']) {
                // Store payment reference and virtual account details
                $contribution->update([
                    'payment_reference' => $orderId,
                    'transactionId' => $result['data']['transactionId'],
                    'payment_method' => 'alatpay_transfer',
                    'virtual_account_data' => json_encode($result['data'])
                ]);

                return view('payments.virtual-account', [
                    'contribution' => $contribution,
                    'virtualAccount' => $result['data']
                ]);
            }


            throw new Exception('Failed to generate virtual account');
        } catch (Exception $e) {
            dd($e->getMessage());
            return redirect()->back()->with('error', 'Failed to generate virtual account. Please try again.');
        }
    }

    /**
     * Check payment status
     */
    public function checkPaymentStatus(Contribution $contribution)
    {
        if (!$contribution->transactionId) {
            return response()->json([
                'success' => false,
                'message' => 'No payment reference found'
            ]);
        }

        try {
            // $virtualAccountData = json_decode($contribution->virtual_account_data, true);
            $transactionId = $contribution['transactionId'] ?? null;

            if (!$transactionId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Transaction ID not found'
                ]);
            }

            $result = $this->alatPayService->checkTransactionStatus($transactionId);

            if ($result['status'] && isset($result['data']['status'])) {
                $paymentStatus = $result['data']['status'];

                if (strtolower($paymentStatus) === 'successful' || strtolower($paymentStatus) === 'completed') {
                    // Mark contribution as paid
                    $contribution->markAsPaid('alatpay_transfer');
                    $notificationService = app(\App\Services\NotificationService::class);
                    $notificationService->queuePaymentSuccessMail(
                        $contribution->user,
                        $contribution->group,
                        $contribution
                    );
                    return response()->json([
                        'success' => true,
                        'paid' => true,
                        'message' => 'Payment confirmed successfully!'
                    ]);
                }
            }

            return response()->json([
                'success' => true,
                'paid' => false,
                'message' => 'Payment is still pending'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to check payment status'
            ]);
        }
    }

        /**
     * Handle ALATPay webhook for secure payment verification
     */
    public function handleWebhook(Request $request)
    {
        

          file_put_contents(__DIR__ . '/alatlog.txt', json_encode($request->all(), JSON_PRETTY_PRINT), FILE_APPEND);


        try {
            // Validate webhook structure
            $webhookData = $request->all();
            
            if (!isset($webhookData['Value']['Data'])) {
                \Log::warning('Invalid webhook structure received', $webhookData);
                return response()->json(['message' => 'Invalid webhook structure'], 400);
            }

            $data = $webhookData['Value']['Data'];
            $status = $webhookData['Value']['Status'] ?? false;
            $message = $webhookData['Value']['Message'] ?? '';

            // Validate required fields
            if (!isset($data['OrderId']) || !isset($data['Status'])) {
                \Log::warning('Missing required fields in webhook', $data);
                return response()->json(['message' => 'Missing required fields'], 400);
            }

            $orderId = $data['OrderId'];
            $paymentStatus = strtolower($data['Status']);
            $transactionId = $data['Customer']['TransactionId'] ?? null;
            $amount = $data['Amount'] ?? 0;

            // Find contribution by order ID
            $contribution = Contribution::where('payment_reference', $orderId)->first();

            if (!$contribution) {
                \Log::warning('Contribution not found for webhook', [
                    'order_id' => $orderId,
                    'transaction_id' => $transactionId
                ]);
                return response()->json(['message' => 'Contribution not found'], 404);
            }

            // Verify amount matches (security check)
            if ($amount != $contribution->amount) {
                \Log::error('Amount mismatch in webhook', [
                    'webhook_amount' => $amount,
                    'contribution_amount' => $contribution->amount,
                    'contribution_id' => $contribution->id
                ]);
                return response()->json(['message' => 'Amount mismatch'], 400);
            }

            // Process payment based on status
            if ($status && ($paymentStatus === 'completed' || $paymentStatus === 'successful')) {
                // Check if already processed to prevent duplicate processing
                if ($contribution->status === 'paid') {
                    \Log::info('Payment already processed', [
                        'contribution_id' => $contribution->id,
                        'order_id' => $orderId
                    ]);
                    return response()->json(['message' => 'Payment already processed'], 200);
                }

                // Update contribution with transaction details
                $contribution->update([
                    'transactionId' => $transactionId,
                    'status' => 'paid',
                    'paid_at' => now()
                ]);

                // Queue payment success notification
                $notificationService = app(\App\Services\NotificationService::class);
                $notificationService->queuePaymentSuccessMail(
                    $contribution->user,
                    $contribution->group,
                    $contribution
                );

                \Log::info('Payment confirmed via webhook', [
                    'contribution_id' => $contribution->id,
                    'transaction_id' => $transactionId,
                    'amount' => $amount,
                    'order_id' => $orderId,
                    'user_email' => $contribution->user->email
                ]);

                return response()->json(['message' => 'Payment processed successfully'], 200);
            } 
            elseif ($paymentStatus === 'failed' || $paymentStatus === 'cancelled') {
                \Log::warning('Payment failed via webhook', [
                    'contribution_id' => $contribution->id,
                    'transaction_id' => $transactionId,
                    'status' => $paymentStatus,
                    'order_id' => $orderId
                ]);

                // Optionally update contribution status to failed
                // $contribution->update(['status' => 'failed']);

                return response()->json(['message' => 'Payment failed'], 200);
            }
            else {
                \Log::info('Payment pending via webhook', [
                    'contribution_id' => $contribution->id,
                    'status' => $paymentStatus,
                    'order_id' => $orderId
                ]);

                return response()->json(['message' => 'Payment status updated'], 200);
            }

        } catch (Exception $e) {
            \Log::error('ALATPay Webhook Error: ' . $e->getMessage(), [
                'exception' => $e,
                'request_data' => $request->all()
            ]);
            return response()->json(['message' => 'Webhook processing failed'], 500);
        }
    }

    /**
     * Handle ALATPay webhook/callback (Legacy method - kept for backward compatibility)
     */
    public function handleCallback(Request $request)
    {
        // Log the callback for debugging
        \Log::info('ALATPay Callback (Legacy):', $request->all());

        try {
            $data = $request->all();

            // Find contribution by order ID or transaction reference
            $orderId = $data['orderId'] ?? $data['reference'] ?? null;

            if (!$orderId) {
                return response()->json(['message' => 'Order ID not found'], 400);
            }

            $contribution = Contribution::where('payment_reference', $orderId)->first();

            if (!$contribution) {
                return response()->json(['message' => 'Contribution not found'], 404);
            }

            // Check if payment was successful
            $status = $data['status'] ?? '';
            if (strtolower($status) === 'successful' || strtolower($status) === 'completed') {
                $contribution->markAsPaid($contribution->payment_method);
                
                // Queue payment success notification
                $notificationService = app(\App\Services\NotificationService::class);
                $notificationService->queuePaymentSuccessMail(
                    $contribution->user,
                    $contribution->group,
                    $contribution
                );
            }

            return response()->json(['message' => 'Callback processed successfully']);
        } catch (Exception $e) {
            \Log::error('ALATPay Callback Error: ' . $e->getMessage());
            return response()->json(['message' => 'Callback processing failed'], 500);
        }
    }
    /**
     * Handle ALATPay webhook/callback
     */
    public function oldhandleCallback(Request $request)
    {
        // Log the callback for debugging
        \Log::info('ALATPay Callback:', $request->all());

        try {
            $data = $request->all();

            // Find contribution by order ID or transaction reference
            $orderId = $data['orderId'] ?? $data['reference'] ?? null;

            if (!$orderId) {
                return response()->json(['message' => 'Order ID not found'], 400);
            }

            $contribution = Contribution::where('payment_reference', $orderId)->first();

            if (!$contribution) {
                return response()->json(['message' => 'Contribution not found'], 404);
            }

            // Check if payment was successful
            $status = $data['status'] ?? '';
            if (strtolower($status) === 'successful' || strtolower($status) === 'completed') {
                $contribution->markAsPaid($contribution->payment_method);
            }

            return response()->json(['message' => 'Callback processed successfully']);
        } catch (Exception $e) {
            \Log::error('ALATPay Callback Error: ' . $e->getMessage());
            return response()->json(['message' => 'Callback processing failed'], 500);
        }
    }

    public function showPaymentOptionsForCycle(Group $group, int $cycle)
    {
        $user = Auth::user();

        // Check if user is a member
        if (!$group->members()->where('user_uuid', $user->uuid)->exists()) {
            return redirect()->route('groups.show', $group)
                ->with('error', 'You are not a member of this group.');
        }

        $contributionService = app(ContributionService::class);
        $contribution = $contributionService->createFutureCycleContribution($group, $user, $cycle);

        if (!$contribution) {
            return redirect()->route('groups.show', $group)
                ->with('error', 'Unable to create contribution for this cycle.');
        }

        return view('payments.options', compact('contribution', 'group'));
    }


    
}
