<?php

namespace App\Services\WhatsApp;

use App\Models\Marketing\WhatsappMessage;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    protected string $url;
    protected string $token;
    protected string $phoneNumberId;

    public function __construct()
    {
        // Using config fallback values for local development
        $this->token = config('qline.meta.token') ?? 'dummy_token';
        $this->phoneNumberId = config('qline.meta.phone_number_id') ?? 'dummy_id';
        $this->url = "https://graph.facebook.com/v19.0/{$this->phoneNumberId}/messages";
    }

    public function sendText(string $to, string $text, ?int $businessId = null, ?int $queueEntryId = null): WhatsappMessage
    {
        // If dummy local env, just mock success
        if ($this->token === 'dummy_token' || env('APP_ENV') === 'local') {
            Log::info("Mock WhatsApp to {$to}: {$text}");
            return WhatsappMessage::create([
                'business_id' => $businessId,
                'queue_entry_id' => $queueEntryId,
                'wa_id' => $to,
                'direction' => 'outbound',
                'body' => $text,
                'message_id' => 'mock_id_' . uniqid(),
                'status' => 'sent',
            ]);
        }

        $response = Http::withToken($this->token)
            ->timeout(15)
            ->post($this->url, [
            'messaging_product' => 'whatsapp',
            'to' => $to,
            'type' => 'text',
            'text' => ['body' => $text]
        ]);

        $status = $response->successful() ? 'sent' : 'failed';
        if (!$response->successful()) {
            Log::error("WhatsApp send failed: " . $response->body());
        }

        return WhatsappMessage::create([
            'business_id' => $businessId,
            'queue_entry_id' => $queueEntryId,
            'wa_id' => $to,
            'direction' => 'outbound',
            'body' => $text,
            'message_id' => $response->json('messages.0.id'),
            'status' => $status,
        ]);
    }
}
