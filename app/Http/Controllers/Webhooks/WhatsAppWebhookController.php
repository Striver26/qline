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
     * Handle webhook verification from Meta
     */
    public function verify(Request $request)
    {
        $mode = $request->query('hub_mode');
        $token = $request->query('hub_verify_token');
        $challenge = $request->query('hub_challenge');

        if ($mode && $token) {
            if ($mode === 'subscribe' && $token === env('META_WEBHOOK_VERIFY_TOKEN', 'qline_secret')) {
                return response($challenge, 200);
            }
        }
        return response('Forbidden', 403);
    }

    /**
     * Process incoming WhatsApp messages
     */
    public function process(Request $request, QueueService $queueService, WhatsAppService $waService)
    {
        $payload = $request->all();
        Log::info('Incoming Meta Webhook', ['payload' => $payload]);

        if (!isset($payload['entry'][0]['changes'][0]['value']['messages'][0])) {
            return response('OK', 200); // Probably a status update
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
}
