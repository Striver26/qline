<?php

use App\Models\Marketing\WhatsappMessage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WhatsAppWebhookStatusTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_updates_message_status_from_webhook()
    {
        // Disable signature check for test
        config(['qline.meta.app_secret' => null]);

        // 1. Create a message record
        $message = WhatsappMessage::create([
            'wa_id' => '60123456789',
            'direction' => 'outbound',
            'body' => 'Hello!',
            'message_id' => 'meta_msg_123',
            'status' => 'sent',
        ]);

        // 2. Mock 'delivered' status update
        $payload = [
            'entry' => [
                [
                    'changes' => [
                        [
                            'value' => [
                                'statuses' => [
                                    [
                                        'id' => 'meta_msg_123',
                                        'status' => 'delivered',
                                        'timestamp' => '1640000000',
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];

        // 3. Post to webhook
        $response = $this->postJson('/webhook/whatsapp', $payload);

        $response->assertStatus(200);

        // 4. Verify DB
        $message->refresh();
        $this->assertEquals('delivered', $message->status);
        $this->assertNotNull($message->delivered_at);
        $this->assertNull($message->read_at);

        // 5. Mock 'read' status update
        $readPayload = [
            'entry' => [
                [
                    'changes' => [
                        [
                            'value' => [
                                'statuses' => [
                                    [
                                        'id' => 'meta_msg_123',
                                        'status' => 'read',
                                        'timestamp' => '1640000100',
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];

        $this->postJson('/webhook/whatsapp', $readPayload)->assertStatus(200);

        $message->refresh();
        $this->assertEquals('read', $message->status);
        $this->assertNotNull($message->delivered_at);
        $this->assertNotNull($message->read_at);
    }
}
