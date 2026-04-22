<?php

namespace App\Http\Controllers\Webhooks;

use App\Http\Controllers\Controller;
use App\Models\Tenant\Business;
use App\Services\Queue\QueueService;
use App\Services\WhatsApp\WhatsAppService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WhatsAppWebhookController extends Controller
{
    /**
     * Handle webhook verification from Meta.
     * Uses config() instead of env() to work with config caching.
     */
    public function verify(Request $request)
    {
        $mode = $request->query('hub_mode');
        $token = $request->query('hub_verify_token');
        $challenge = $request->query('hub_challenge');

        if ($mode && $token) {
            if ($mode === 'subscribe' && $token === config('qline.meta.webhook_verify_token', 'qline_secret')) {
                return response($challenge, 200);
            }
        }
        return response('Forbidden', 403);
    }

    /**
     * Process incoming WhatsApp messages.
     * Verifies the X-Hub-Signature-256 header to prevent forged payloads.
     */
    public function process(Request $request, QueueService $queueService, WhatsAppService $waService)
    {
        // Verify webhook signature from Meta
        if (!$this->verifyWebhookSignature($request)) {
            Log::warning('WhatsApp webhook signature verification failed');
            return response('Invalid signature', 403);
        }

        $payload = $request->all();
        Log::info('Incoming Meta Webhook', ['payload' => $payload]);

        $value = $payload['entry'][0]['changes'][0]['value'] ?? [];

        // Handle status updates (sent, delivered, read, failed)
        if (isset($value['statuses'][0])) {
            $this->handleStatusUpdate($value['statuses'][0]);
            return response('OK', 200);
        }

        if (!isset($value['messages'][0])) {
            return response('OK', 200);
        }

        $message = $payload['entry'][0]['changes'][0]['value']['messages'][0];
        $waId = $message['from']; // Phone number
        
        $text = strtolower(trim($message['text']['body'] ?? ''));

        if (str_starts_with($text, 'join ')) {
            $code = strtoupper(trim(substr($text, 5)));
            
            $business = Business::where('join_code', $code)->first();

            if (!$business) {
                $waService->sendText($waId, "Sorry, we couldn't find a business with code {$code}.");
                return response('OK', 200);
            }

            try {
                $entry = $queueService->join($business, $waId);
                // Welcome text is now handled by TicketJoined event listener
            } catch (\Exception $e) {
                $waService->sendText($waId, "Queue entry failed: " . $e->getMessage(), $business->id);
            }
        } else {
            // General Help
            $waService->sendText($waId, "Welcome to QLine! To join a queue, please text 'JOIN [CODE]'.");
        }

        return response('OK', 200);
    }

    /**
     * Update the status of a sent message.
     */
    protected function handleStatusUpdate(array $statusUpdate): void
    {
        $messageId = $statusUpdate['id'];
        $status = $statusUpdate['status'];
        $timestamp = now();

        $message = \App\Models\Marketing\WhatsappMessage::where('message_id', $messageId)->first();

        if ($message) {
            $updateData = ['status' => $status];

            if ($status === 'delivered') {
                $updateData['delivered_at'] = $timestamp;
            } elseif ($status === 'read') {
                $updateData['read_at'] = $timestamp;
                if (!$message->delivered_at) {
                    $updateData['delivered_at'] = $timestamp;
                }
            }

            $message->update($updateData);
        }
    }

    /**
     * Verify the X-Hub-Signature-256 header from Meta's webhook payload.
     * Returns true if signature is valid, or if no app secret is configured (local dev).
     */
    protected function verifyWebhookSignature(Request $request): bool
    {
        $appSecret = config('qline.meta.app_secret');

        // Skip verification in local dev when no secret is configured
        if (!$appSecret) {
            return true;
        }

        $signature = $request->header('X-Hub-Signature-256');

        if (!$signature) {
            return false;
        }

        $expectedSignature = 'sha256=' . hash_hmac('sha256', $request->getContent(), $appSecret);

        return hash_equals($expectedSignature, $signature);
    }
}
