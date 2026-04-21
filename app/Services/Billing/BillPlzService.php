<?php

namespace App\Services\Billing;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Tenant\Business;

class BillPlzService
{
    protected ?string $secretKey;
    protected ?string $collectionId;
    protected ?string $xSignatureKey;
    protected string $baseUrl;

    public function __construct()
    {
        $this->secretKey = config('qline.billplz.secret');
        $this->collectionId = config('qline.billplz.collection_id');
        $this->xSignatureKey = config('qline.billplz.x_signature');
        $isSandbox = config('qline.billplz.sandbox', true);

        $this->baseUrl = $isSandbox 
            ? 'https://www.billplz-sandbox.com/api/v3'
            : 'https://www.billplz.com/api/v3';
    }

    public function createBill(Business $business, float $amount, string $description, string $callbackUrl, string $redirectUrl)
    {
        if (!$this->secretKey) {
            if (!app()->environment('local', 'testing')) {
                throw new \Exception('BillPlz secret key is not configured in this environment.');
            }
            Log::info("Mock BillPlz Create Bill for {$business->name} - RM {$amount}");
            return [
                'id' => 'mock_bill_' . uniqid(),
                'url' => route('business.dashboard') . '?mock_payment=success'
            ];
        }

        // BillPlz requires amounts in cents (e.g., 100 = RM 1.00)
        $amountInCents = intval($amount * 100);

        $response = Http::withBasicAuth($this->secretKey, '')
            ->post("{$this->baseUrl}/bills", [
                'collection_id' => $this->collectionId,
                'email' => 'admin@qline.local', // Requires valid email
                'name' => $business->name,
                'amount' => $amountInCents, 
                'callback_url' => $callbackUrl,
                'redirect_url' => $redirectUrl,
                'description' => $description
            ]);

        if (!$response->successful()) {
            throw new \Exception('Failed to create BillPlz bill: ' . $response->body());
        }

        return $response->json();
    }

    public function verifySignature(array $data): bool
    {
        if (!$this->xSignatureKey) {
            if (!app()->environment('local', 'testing')) {
                Log::error('BillPlz x_signature key missing in non-local environment.');
                return false;
            }
            return true;
        }

        $sourceString = "";
        
        // These keys must be appended in alphabetical order according to BillPlz docs
        $keys = [
            'amount', 'collection_id', 'due_at', 'email', 'id', 'mobile',
            'name', 'paid', 'paid_amount', 'paid_at', 'pay_instruction', 'state', 'url'
        ];

        foreach ($keys as $key) {
            if (isset($data[$key])) {
                $sourceString .= $key . $data[$key] . '|';
            }
        }
        
        $sourceString = rtrim($sourceString, '|');
        
        $signature = hash_hmac('sha256', $sourceString, $this->xSignatureKey);
        
        return hash_equals($signature, $data['x_signature'] ?? '');
    }
}
