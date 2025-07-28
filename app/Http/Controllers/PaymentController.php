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
     * Handle ALATPay webhook/callback
     */
    public function handleCallback(Request $request)
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
