<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class AlatPayService
{
    private $baseUrl;
    private $businessId;
    private $subscriptionKey;

    public function __construct()
    {
        $this->baseUrl = 'https://apibox.alatpay.ng';
        $this->businessId = config('services.alatpay.business_id');
        $this->subscriptionKey = config('services.alatpay.subscription_key');
    }

    /**
     * Initialize card payment
     */
    public function initializeCard($cardNumber, $currency = 'NGN')
    {
        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Ocp-Apim-Subscription-Key' => $this->subscriptionKey
            ])->post($this->baseUrl . '/paymentCard/api/v1/paymentCard/mc/initialize', [
                'cardNumber' => $cardNumber,
                'currency' => $currency,
                'businessId' => $this->businessId
            ]);

            if ($response->successful()) {
                return $response->json();
            }

            throw new Exception('Failed to initialize card: ' . $response->body());
        } catch (Exception $e) {
            Log::error('ALATPay Card Initialize Error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Authenticate card payment
     */
    public function authenticateCard($cardData, $transactionId, $orderId)
    {
        try {
            $payload = [
                'cardNumber' => $cardData['cardNumber'],
                'cardMonth' => $cardData['cardMonth'],
                'cardYear' => $cardData['cardYear'],
                'securityCode' => $cardData['securityCode'],
                'businessId' => $this->businessId,
                'businessName' => config('app.name'),
                'amount' => $cardData['amount'],
                'currency' => $cardData['currency'] ?? 'NGN',
                'orderId' => $orderId,
                'description' => 'Contribution Payment - ' . config('app.name'),
                'channel' => 'web',
                'customer' => [
                    'email' => $cardData['customer']['email'],
                    'phone' => $cardData['customer']['phone'],
                    'firstName' => $cardData['customer']['firstName'] ?? '',
                    'lastName' => $cardData['customer']['lastName'] ?? ''
                ]
            ];

            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Ocp-Apim-Subscription-Key' => $this->subscriptionKey
            ])->post($this->baseUrl . '/paymentcard/api/v1/paymentCard/mc/authenticate', $payload);

            if ($response->successful()) {
                return $response->json();
            }

            throw new Exception('Failed to authenticate card: ' . $response->body());
        } catch (Exception $e) {
            Log::error('ALATPay Card Authenticate Error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Generate virtual account for bank transfer
     */
    public function generateVirtualAccount($amount, $orderId, $customer, $description = null)
    {
        try {
            $payload = [
                'businessId' => $this->businessId,
                'amount' => $amount,
                'currency' => 'NGN',
                'orderId' => $orderId,
                'description' => $description ?? 'Contribution Payment',
                'customer' => [
                    'email' => $customer['email'],
                    'phone' => $customer['phone'],
                    'firstName' => $customer['firstName'],
                    'lastName' => $customer['lastName'],
                    'metadata' => $customer['metadata'] ?? ''
                ]
            ];

            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Ocp-Apim-Subscription-Key' => $this->subscriptionKey
            ])->post($this->baseUrl . '/bank-transfer/api/v1/bankTransfer/virtualAccount', $payload);

            if ($response->successful()) {
                return $response->json();
            }

            throw new Exception('Failed to generate virtual account: ' . $response->body());
        } catch (Exception $e) {
            Log::error('ALATPay Virtual Account Error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Check transaction status
     */
    public function checkTransactionStatus($transactionId)
    {
        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Ocp-Apim-Subscription-Key' => $this->subscriptionKey
            ])->get($this->baseUrl . '/bank-transfer/api/v1/bankTransfer/transactions/' . $transactionId);

            if ($response->successful()) {
                return $response->json();
            }

            throw new Exception('Failed to check transaction status: ' . $response->body());
        } catch (Exception $e) {
            Log::error('ALATPay Transaction Status Error: ' . $e->getMessage());
            throw $e;
        }
    }
}